<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DAO\DetailsFraisDAO;
use App\Models\DAO\NoteFraisDAO;
use App\Models\DAO\UserDAO;
use App\Models\DAO\CategorieFraisDAO;
use App\Models\DAO\DeplacementDAO;

use App\Models\Entities\DetailsFrais;
use App\Helpers\Auth;
use App\Models\DAO\NotificationDAO;


class DetailsFraisController extends BaseController {
    private DetailsFraisDAO $detailsDAO;
    private DeplacementDAO $deplacementDAO;
    private UserDAO $userDAO;
    private NoteFraisDAO $noteDAO;
    private CategorieFraisDAO $categorieDAO;
        private NotificationDAO $notifDAO;

    public function __construct() {
                    $this->notifDAO = new NotificationDAO();

        $this->userDAO = new UserDAO();
        $this->detailsDAO = new DetailsFraisDAO();
        $this->deplacementDAO = new DeplacementDAO();
        $this->noteDAO    = new NoteFraisDAO();
        $this->categorieDAO = new CategorieFraisDAO();
    }

    public function index( $note_id ) {
        $note = $this->noteDAO->findById( $note_id );
        if ( !$note || !Auth::canAccessNote( $note ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }
        $user = Auth::user();
        $userId = $user->getId();
        $details = $this->detailsDAO->findByNoteId( $note_id );
        $categories = $this->categorieDAO->findAll();
        $deplacem = $this->deplacementDAO->findById( $note->getDeplacementId() );

        $this->view( 'notefrais/index', [
            'details'     => $details,
            'categories'  => $categories,
            'note'        => $note,
            'userId'   => $userId,
            'deplacementTitre' => $deplacem->getTitre(),
            'deplacement' => $note->getDeplacementId(),
            'today'       => date( 'd/m/Y' ),
            'notifications'=>        $this->notifDAO->findByUser($userId)
        ] );
    }

    public function createPage( $note_id ) {
        $note = $this->noteDAO->findById( $note_id );
        if ( !$note || $note->getUserId() !== Auth::user()->getId() ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }
        $user = Auth::user();
        $userId = $user->getId();
        $categories = $this->categorieDAO->findAll();
        $this->view( 'detailsfrais/create', [ 'userId'   => $userId,
        'note' => $note, 'categories' => $categories ,
            'notifications'=>        $this->notifDAO->findByUser($userId)] );
    }

    public function store() {
        Auth::requireAuth();
        $user = Auth::user();

        $note_id = $_POST[ 'note_id' ] ?? null;
        $note = $this->noteDAO->findById( $note_id );

        if ( !$note || $note->getUserId() !== $user->getId() ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }

        $categorie_id   = $_POST[ 'categorie_id' ] ?? null;
        $date_frais     = $_POST[ 'date_frais' ] ?? null;
        $montant_total  = ( float )( $_POST[ 'montant_total' ] ?? 0 );
        $subcategory    = trim( $_POST[ 'subcategory' ] ?? '' );
        $manual_desc    = trim( $_POST[ 'description' ] ?? '' );
        $description    = $manual_desc ?: $subcategory;

        $montant_veloce = 0;
        $montant_personnel = 0;
        if ( isset( $_POST[ 'via_veloce' ] ) ) {
            $montant_veloce = $montant_total;
        } elseif ( isset( $_POST[ 'frais_personnel' ] ) ) {
            $montant_personnel = $montant_total;
        } else {
            $montant_veloce = $montant_total;
        }

        $justificatif_path = $justificatif_mime = $justificatif_size = null;
        if ( isset( $_FILES[ 'justificatif' ] ) && $_FILES[ 'justificatif' ][ 'error' ] === UPLOAD_ERR_OK ) {
            $file = $_FILES[ 'justificatif' ];
            $allowed = [ 'jpg', 'jpeg', 'png', 'pdf' ];
            $ext = strtolower( pathinfo( $file[ 'name' ], PATHINFO_EXTENSION ) );

            if ( in_array( $ext, $allowed ) && $file[ 'size' ] <= 10_000_000 ) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if ( !is_dir( $uploadDir ) ) mkdir( $uploadDir, 0755, true );

                $filename = uniqid( 'justif_' ) . '.' . $ext;
                $path = $uploadDir . $filename;

                if ( move_uploaded_file( $file[ 'tmp_name' ], $path ) ) {
                    $justificatif_path = '/uploads/' . $filename;
                    $justificatif_mime = $file[ 'type' ];
                    $justificatif_size = $file[ 'size' ];
                }
            }
        }
        // 32 caractères hex

        $detail = new DetailsFrais(
            null,
            $note_id,
            $categorie_id,
            $description,
            $date_frais,
            $montant_veloce,
            $montant_personnel,
            null,
            $justificatif_path,
            $justificatif_mime,
            $justificatif_size
        );

        $this->detailsDAO->insert( $detail );
        $this->detailsDAO->updateNoteTotal( $note_id );

        $_SESSION[ 'success' ] = 'Détail ajouté avec succès !';
        return $this->redirect( "/notes/{$note->getDeplacementId()}" );
    }

    // جلب détail واحد ( للمودال - Ajax )

    public function get( $id ) {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = $user->getId();
        $role = $user->getRole();
        $user1 = $this->userDAO->findByIdForProfile( $user->getId() );
        // لتحديث بيانات المستخدم من قاعدة البيانات
        $coverUrl = $user1->getCoverUrl();
        $avatarUrl = $user1->getAvatarUrl();
        $detail = $this->detailsDAO->findById( $id );
        if ( !$detail ) {
            http_response_code( 404 );
            echo json_encode( [ 'error' => 'Détail non trouvé' ] );
            exit;
        }

        $note = $this->noteDAO->findById( $detail->getNoteId() );
        if ( !$note || $note->getUserId() !== Auth::user()->getId() ) {
            http_response_code( 403 );
            echo json_encode( [ 'error' => 'Accès refusé' ] );
            exit;
        }
        $d = $this->deplacementDAO->findById( $note->getDeplacementId() );
        $details = $this->detailsDAO->findByNoteId( $note->getId() );
        $categories = $this->categorieDAO->findAll();

        $total = $detail->getMontantVeloce() + $detail->getMontantPersonnel();
        $deplacem = $this->deplacementDAO->findById( $note->getDeplacementId() );

        $this->view( 'notefrais/index', [
            'iddd'                => $detail->getId(),
            'date_frais'        => $detail->getDateFrais(),
            'montant_total'     => $total,
            'userId'   => $userId,
            'description'       => $detail->getDescription(),
            'montant_veloce'    => $detail->getMontantVeloce(),
            'montant_personnel' => $detail->getMontantPersonnel(),
            'categorie_id'      => $detail->getCategorieId(),
            'details'     => $details,
            'deplacementTitre' => $deplacem->getTitre(), // لو محتاجه في الـ view
            'categories'  => $categories,
            'avatarUrl' => $avatarUrl,
            'note'        => $note,
            'deplacement' => $d, // لو محتاجه في الـ view
            'today'       => date( 'd/m/Y' ),
            'randomId'       => $detail->getRandomId(),
            'justificatif_path' => $detail->getJustificatifPath() ,
            'notifications'=>        $this->notifDAO->findByUser($userId)] );
        }

        // تعديل détail

        public function update() {
            Auth::requireAuth();
            $user = Auth::user();

            $detail_id     = $_POST[ 'detail_id' ] ?? null;
            $note_id       = $_POST[ 'note_id' ] ?? null;
            $categorie_id   = $_POST[ 'categorie_id' ] ?? null;
            $date_frais    = $_POST[ 'date_frais' ] ?? null;
            $montant_total = ( float )( $_POST[ 'montant_total' ] ?? 0 );

            $description   = trim( $_POST[ 'subcategory' ] ?? '' );
            $detail = $this->detailsDAO->findById( $detail_id );

            $note = $this->noteDAO->findById( $detail->getNoteId() );
            if ( !$note || $note->getUserId() !== $user->getId() ) {
                $_SESSION[ 'error' ] = 'Accès refusé';
                return $this->redirect( $_SERVER[ 'HTTP_REFERER' ] ?? '/' );
            }

            $montant_veloce = 0;
            $montant_personnel = 0;
            if ( isset( $_POST[ 'via_veloce' ] ) ) {
                $montant_veloce = $montant_total;
            } elseif ( isset( $_POST[ 'frais_personnel' ] ) ) {
                $montant_personnel = $montant_total;
            } else {
                $montant_veloce = $montant_total;
            }

            $detail->setDateFrais( $date_frais );
            $detail->setDescription( $description );
            $detail->setMontantVeloce( $montant_veloce );
            $detail->setMontantPersonnel( $montant_personnel );
            $detail->setCategorieId( $categorie_id );

            $this->detailsDAO->update( $detail );
            $this->detailsDAO->updateNoteTotal( $note_id );

            $_SESSION[ 'success' ] = 'Détail modifié avec succès !';

            return $this->redirect( "/notes/{$note->getDeplacementId()}" );

        }

        // حذف détail - الدالة الصحيحة ( بدون deleted )

        public function delete( $id ) {
            Auth::requireAuth();
            $user = Auth::user();

            $detail = $this->detailsDAO->findById( $id );
            if ( !$detail ) {
                $_SESSION[ 'error' ] = 'Détail non trouvé';
                return $this->redirect( '/' );
            }

            $note = $this->noteDAO->findById( $detail->getNoteId() );
            if ( !$note || $note->getUserId() !== $user->getId() ) {
                $_SESSION[ 'error' ] = 'Accès refusé';
                return $this->redirect( '/' );
            }

            $note_id = $detail->getNoteId();

            // حذف الملف
            $path = $detail->getJustificatifPath();
            if ( $path && file_exists( __DIR__ . '/../../public' . $path ) ) {
                unlink( __DIR__ . '/../../public' . $path );
            }

            $this->detailsDAO->delete( $id );
            $this->detailsDAO->updateNoteTotal( $note_id );

            // دعم Ajax
            if ( !empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' ) {
                header( 'Content-Type: application/json' );
                echo json_encode( [ 'success' => true ] );
                exit;
            }

            $_SESSION[ 'success' ] = 'Détail supprimé avec succès !';
            return $this->redirect( "/notes/{$note->getDeplacementId()}" );
        }
    }