<?php
namespace App\Controllers;
use App\Services\PDFGenerator;
use App\Core\BaseController;
use App\Models\DAO\NoteFraisDAO;
use App\Models\DAO\DeplacementDAO;
use App\Models\DAO\HistoriqueStatutDAO;
use App\Models\DAO\DetailsFraisDAO;
use App\Models\Entities\NoteFrais;
use App\Models\Entities\User;
use App\Models\DAO\UserDAO;
use App\Models\Entities\HistoriqueStatut;
use App\Helpers\Auth;
use App\Models\DAO\NotificationDAO;

class NoteFraisController extends BaseController
 {
    private NotificationDAO $notifDAO;
    private NoteFraisDAO $noteDAO;
    private DetailsFraisDAO $detailsDAO;
    private DeplacementDAO $depDAO;
    private HistoriqueStatutDAO $histoDAO;
    private UserDAO $userDAO;

    public function __construct()
 {
        $this->notifDAO = new NotificationDAO();

        $this->userDAO = new UserDAO();

        $this->noteDAO = new NoteFraisDAO();
        $this->depDAO = new DeplacementDAO();
        $this->histoDAO = new HistoriqueStatutDAO();
        $this->detailsDAO = new DetailsFraisDAO();

    }

    public function index( $deplacement_id )
 {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = $user->getId();
        $role = $user->getRole();
        $user1 = $this->userDAO->findByIdForProfile( $user->getId() );
        // لتحديث بيانات المستخدم من قاعدة البيانات
        $coverUrl = $user1->getCoverUrl();
        $avatarUrl = $user1->getAvatarUrl();

        $dep = $this->depDAO->findById( $deplacement_id );
        if ( !$dep || !Auth::canManageDeplacement( $dep ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }

        // جلب الـ note ( واحدة لكل déplacement )
        $note = $this->noteDAO->findOneByDeplacementId( $deplacement_id );
        // Récupérer le commentaire manager le plus récent
        $commentaireManager = null;
        $commentaireAdmin = null;        $commentaire = null;
            $commentaireManager = $note->getCommentaireManager();
        $commentaire=$note->getCommentaire();
        // Récupérer le commentaire admin le plus récent
            $commentaireAdmin = $note->getCommentaireAdmin();
        
        // Si on a les deux, on peut arrêter
    
        // لو مفيش note → نعمل واحدة تلقائيًا
        if ( !$note ) {
            $newNote = new NoteFrais(
                null,
                $deplacement_id,
                Auth::user()->getId(),
                'brouillon',
                0.00
            );
            $newId = $this->noteDAO->insert( $newNote );
            $note = $this->noteDAO->findById( $newId );
        }

        // جلب كل الـ détails الخاصة بالـ note الحالية ( مهم جدًا! )
        $details = $this->detailsDAO->findByNoteId( $note->getId() );

        // جلب كل الفئات
        require_once __DIR__ . '/../Models/DAO/CategorieFraisDAO.php';
        $categorieDAO = new \App\Models\DAO\CategorieFraisDAO();
        $categories = $categorieDAO->findAll();

        // تمرير كل البيانات للـ view
        $this->view( 'notefrais/index', [
            'deplacement'    => $dep,
            'commentaireManager'=>  $commentaireManager,
            'commentaireAdmin'=>$commentaireAdmin,
            'deplacement_id' => $deplacement_id,
            'note'           => $note,
            'commentaire' => $commentaire,
            'details'        => $details,
            'userId'   => $userId,     // هذا هو الحل!
            'categories'     => $categories,
            'avatarUrl' => $avatarUrl,
            'coverUrl' => $coverUrl,
            'today'          => date( 'd/m/Y' ),
            'notifications'=>        $this->notifDAO->findByUser($userId)
        ] );
    }

    public function createPage( $deplacement_id )
 {
            $user = Auth::user();
        $userId = $user->getId();
        $dep = $this->depDAO->findById( $deplacement_id );
        if ( !$dep || $dep->getUserId() !== Auth::user()->getId() ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }

        // جلب أول note موجودة للـ déplacement ( لأنو بنعمل note تلقائي عند إنشاء الـ déplacement )
        $note = $this->noteDAO->findOneByDeplacementId( $deplacement_id );


        $this->view( 'detailsfrais/create', [
            'deplacement' => $dep,
            'note'        => $note,
            'userId'   => $userId,    
            'notifications'=>        $this->notifDAO->findByUser($userId) 
        ]);
    }
// Dans NoteFraisController.php - Ajouter ces méthodes


/**
 * Télécharger la note de frais en PDF
 */
public function downloadPDF($note_id)
{
    Auth::requireAuth();
    
    $note = $this->noteDAO->findById($note_id);
    if (!$note || !Auth::canAccessNote($note)) {
        $_SESSION['error'] = 'Accès refusé';
        return $this->redirect('/');
    }

    $deplacement = $this->depDAO->findById($note->getDeplacementId());
    $employe = $this->userDAO->findById($note->getUserId());
    $details = $this->detailsDAO->findByNoteId($note_id);
    $categorieDAO = new \App\Models\DAO\CategorieFraisDAO();
    $categories = $categorieDAO->findAll();

    $pdfGenerator = new PDFGenerator();
    
    try {
        $pdfGenerator->downloadNoteFraisPDF(
            $note,
            $deplacement,
            $employe,
            $details,
            $categories
        );
    } catch (\Exception $e) {
        $_SESSION['error'] = 'Erreur lors de la génération du PDF: ' . $e->getMessage();
        return $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}

/**
 * Prévisualiser le PDF dans le navigateur
 */
public function previewPDF($note_id)
{
    Auth::requireAuth();
    
    $note = $this->noteDAO->findById($note_id);
    if (!$note || !Auth::canAccessNote($note)) {
        $_SESSION['error'] = 'Accès refusé';
        return $this->redirect('/');
    }

    $deplacement = $this->depDAO->findById($note->getDeplacementId());
    $employe = $this->userDAO->findById($note->getUserId());
    $details = $this->detailsDAO->findByNoteId($note_id);
    $categorieDAO = new \App\Models\DAO\CategorieFraisDAO();
    $categories = $categorieDAO->findAll();

    $pdfGenerator = new PDFGenerator();
    
    try {
        $pdfContent = $pdfGenerator->generateNoteFraisPDF(
            $note,
            $deplacement,
            $employe,
            $details,
            $categories
        );
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="preview.pdf"');
        echo $pdfContent;
        exit;
        
    } catch (\Exception $e) {
        $_SESSION['error'] = 'Erreur lors de la génération du PDF';
        return $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}

    public function store()
 {
        Auth::requireAuth();
        $user = Auth::user();
        $deplacement_id = $_POST[ 'deplacement_id' ] ?? null;

        $dep = $this->depDAO->findById( $deplacement_id );
        if ( !$dep || $dep->getUserId() !== $user->getId() ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }

        $note = new NoteFrais( null, $deplacement_id, $user->getId(), 'brouillon', 0 );
        $this->noteDAO->insert( $note );

        $_SESSION[ 'success' ] = 'Note de frais créée';
        return $this->redirect( "/notes/$deplacement_id" );
    }

    public function updateStatut( $id )
 {
        $note = $this->noteDAO->findById( $id );
        $dep = $this->depDAO->findByNoteId( $id );

        $nouveau = $_POST[ 'statut' ] ?? null;
        if ( !$nouveau || !in_array( $nouveau, [ 'brouillon', 'soumis', 'valide_manager', 'rejetee_manager', 'en_cours_admin', 'approuve', 'rejetee_admin' ] ) ) {
            $_SESSION[ 'error' ] = 'Statut invalide';
            return $this->redirect( '/' );
        }

        $ancien = $note->getStatut();
        if ( $ancien !== $nouveau ) {
            $this->noteDAO->updateStatut( $id, $nouveau );
            $userId = Auth::user()->getId();
            $note = $this->noteDAO->findById( $id );

            if ( isset( $GLOBALS[ 'websocket_server' ] ) ) {
                $GLOBALS[ 'websocket_server' ]->sendNotification(
                    $userId,
                    'Note soumise',
                    'Votre note de frais a bien été envoyée pour validation.',
                    'info'
                );

                // Option : notifier aussi le manager ( si tu connais son ID )
                // $managerId = ...;
                // $GLOBALS[ 'websocket_server' ]->sendNotification( $managerId, 'Nouvelle note à valider', 'Un employé a soumis une note de frais.', 'info' );
            }
            $this->histoDAO->insert( new HistoriqueStatut(
                null,   $dep->getId(), $id, $ancien, $nouveau, Auth::user()->getId(), $_POST[ 'commentaire' ] ?? ''
            ) );
        }

        $_SESSION[ 'success' ] = 'Statut mis à jour';
        return $this->redirect( "/notes/{$note->getDeplacementId()}" );
    }

    public function updateStatut1( $id )
 {
        $note = $this->noteDAO->findById( $id );
        $dep = $this->depDAO->findByNoteId( $id );

        $nouveau = $_POST[ 'statut' ] ?? null;
        if ( !$nouveau || !in_array( $nouveau, [ 'brouillon', 'soumis', 'valide_manager', 'rejetee_manager', 'en_cours_admin', 'approuve', 'rejetee_admin' ] ) ) {
            $_SESSION[ 'error' ] = 'Statut invalide';
            return $this->redirect( '/' );
        }

        $ancien = $note->getStatut();
        if ( $ancien !== $nouveau ) {
            $this->noteDAO->updateStatut( $id, $nouveau );
            $userId = Auth::user()->getId();
            $note = $this->noteDAO->findById( $id );

            if ( isset( $GLOBALS[ 'websocket_server' ] ) ) {
                $GLOBALS[ 'websocket_server' ]->sendNotification(
                    $userId,
                    'Note soumise',
                    'Votre note de frais a bien été envoyée pour validation.',
                    'info'
                );

                // Option : notifier aussi le manager ( si tu connais son ID )
                // $managerId = ...;
                // $GLOBALS[ 'websocket_server' ]->sendNotification( $managerId, 'Nouvelle note à valider', 'Un employé a soumis une note de frais.', 'info' );
            }
            $this->histoDAO->insert( new HistoriqueStatut(
                null,   $dep->getId(), $id, $ancien, $nouveau, Auth::user()->getId(), $_POST[ 'commentaire' ] ?? ''
            ) );
        }

        $_SESSION[ 'success' ] = 'Statut mis à jour';
        return $this->redirect( "/manager" );
    }
    
    public function updateStatut2( $id )
 {
        $note = $this->noteDAO->findById( $id );
        $dep = $this->depDAO->findByNoteId( $id );

        $nouveau = $_POST[ 'statut' ] ?? null;
        if ( !$nouveau || !in_array( $nouveau, [ 'brouillon', 'soumis', 'valide_manager', 'rejetee_manager', 'en_cours_admin', 'approuve', 'rejetee_admin' ] ) ) {
            $_SESSION[ 'error' ] = 'Statut invalide';
            return $this->redirect( '/' );
        }

        $ancien = $note->getStatut();
        if ( $ancien !== $nouveau ) {
            $this->noteDAO->updateStatut( $id, $nouveau );
            $userId = Auth::user()->getId();
            $note = $this->noteDAO->findById( $id );

            if ( isset( $GLOBALS[ 'websocket_server' ] ) ) {
                $GLOBALS[ 'websocket_server' ]->sendNotification(
                    $userId,
                    'Note soumise',
                    'Votre note de frais a bien été envoyée pour validation.',
                    'info'
                );

                // Option : notifier aussi le manager ( si tu connais son ID )
                // $managerId = ...;
                // $GLOBALS[ 'websocket_server' ]->sendNotification( $managerId, 'Nouvelle note à valider', 'Un employé a soumis une note de frais.', 'info' );
            }
            $this->histoDAO->insert( new HistoriqueStatut(
                null,   $dep->getId(), $id, $ancien, $nouveau, Auth::user()->getId(), $_POST[ 'commentaire' ] ?? ''
            ) );
        }

        $_SESSION[ 'success' ] = 'Statut mis à jour';
        return $this->redirect( "/admin" );
    }


    public function delete( $id )
 {
        $note = $this->noteDAO->findById( $id );
        if ( !$note ) return $this->redirect( '/' );

        $dep = $this->depDAO->findById( $note->getDeplacementId() );
        if ( !$dep || !Auth::canManageDeplacement( $dep ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }

        $this->noteDAO->delete( $id );
        $_SESSION[ 'success' ] = 'Note supprimée';
        return $this->redirect( '/deplacements' );
    }
}