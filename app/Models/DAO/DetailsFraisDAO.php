<?php
namespace App\Models\DAO;
use App\Models\Entities\DetailsFrais;
use App\Config\Database;
use PDO;

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Entities/User.php';
use App\WebSocket\NotificationClient;
class DetailsFraisDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function update( DetailsFrais $f ): bool {
        $sql = "UPDATE details_frais 
            SET 
                note_id = ?,
                categorie_id = ?,
                description = ?,
                date_frais = ?,
                montant_veloce = ?,
                montant_personnel = ?,
                montant_total = ?,
                justificatif_path = ?,
                justificatif_mime = ?,
                justificatif_size = ?,
                updated_at = NOW()
            WHERE id = ?";

        $stmt = $this->pdo->prepare( $sql );

        $success = $stmt->execute( [
            $f->getNoteId(),
            $f->getCategorieId(),
            $f->getDescription(),
            $f->getDateFrais(),
            $f->getMontantVeloce(),
            $f->getMontantPersonnel(),
            $f->getMontantTotal(),
            $f->getJustificatifPath(),
            $f->getJustificatifMime(),
            $f->getJustificatifSize(),
            $f->getId()
        ] );

        if ( $success ) {
            $this->forceNoteToBrouillon( $f->getNoteId() );
            // FORCE BROUILLON
            $this->updateNoteTotal( $f->getNoteId() );
        }

        return $success;
    }

    private function reopenNoteIfRefused( int $noteId ): void {
        $sql = 'SELECT statut FROM notes_frais WHERE id = ?';
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $noteId ] );
        $statut = $stmt->fetchColumn();

        if ( $statut === 'refuse' ) {
            $updateSql = "UPDATE notes_frais 
                          SET statut = 'brouillon', 
                              commentaire_manager = NULL,
                              updated_at = NOW() 
                          WHERE id = ?";
            $updateStmt = $this->pdo->prepare( $updateSql );
            $updateStmt->execute( [ $noteId ] );
        }
    }

    // === CORRIGÉ : on sélectionne updated_at et randomId ===

    public function findById( $id ) {
        $sql = 'SELECT *, randomId, updated_at FROM details_frais WHERE id = ?';
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $id ] );
        $r = $stmt->fetch( PDO::FETCH_ASSOC );
        if ( !$r ) return null;

        return new DetailsFrais(
            $r[ 'id' ],
            $r[ 'note_id' ],
            $r[ 'categorie_id' ],
            $r[ 'description' ],
            $r[ 'date_frais' ],
            $r[ 'montant_veloce' ],
            $r[ 'montant_personnel' ],
            $r[ 'montant_total' ],
            $r[ 'justificatif_path' ],
            $r[ 'justificatif_mime' ],
            $r[ 'justificatif_size' ],
            $r[ 'created_at' ],
            $r[ 'updated_at' ],     // maintenant passé !
            $r[ 'randomId' ]        // maintenant passé !
        );
    }

    public function updateNoteTotal( int $note_id ): void {
        $sql = "UPDATE notes_frais n
            SET n.montant_total = (
                SELECT COALESCE(SUM(df.montant_veloce + df.montant_personnel), 0)
                FROM details_frais df
                WHERE df.note_id = n.id
            )
            WHERE n.id = ?";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $note_id ] );
    }

    public function findByDeplacementId( int $deplacement_id ): array {
        $sql = "SELECT 
                df.*,
                df.randomId,
                df.updated_at,
                c.type AS categorie_type,
                n.id AS note_id
            FROM details_frais df
            JOIN notes_frais n ON df.note_id = n.id
            JOIN categories_frais c ON df.categorie_id = c.id
            WHERE n.deplacement_id = ?
            ORDER BY n.id DESC, df.date_frais DESC";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $deplacement_id ] );
        $results = $stmt->fetchAll( PDO::FETCH_ASSOC );

        $grouped = [];
        foreach ( $results as $row ) {
            $grouped[ $row[ 'note_id' ] ][] = $row;
        }
        return $grouped;
    }

    // === CORRIGÉ : on sélectionne updated_at et randomId ===

    public function findByNoteId( int $noteId ): array {
        $sql = 'SELECT df.*, df.randomId, df.updated_at, c.type as categorie_type
                FROM details_frais df
                LEFT JOIN categories_frais c ON df.categorie_id = c.id
                WHERE df.note_id = ?
                ORDER BY df.date_frais DESC';

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $noteId ] );

        $categorieDAO = new \App\Models\DAO\CategorieFraisDAO();
        $results = [];

        while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
            $detailFrais = new DetailsFrais(
                $row[ 'id' ],
                $row[ 'note_id' ],
                $row[ 'categorie_id' ],
                $row[ 'description' ],
                $row[ 'date_frais' ],
                ( float )$row[ 'montant_veloce' ],
                ( float )$row[ 'montant_personnel' ],
                ( float )( $row[ 'montant_veloce' ] + $row[ 'montant_personnel' ] ),
                $row[ 'justificatif_path' ],
                $row[ 'justificatif_mime' ],
                $row[ 'justificatif_size' ],
                $row[ 'created_at' ],
                $row[ 'updated_at' ],     // maintenant passé !
                $row[ 'randomId' ]        // maintenant passé !
            );

            $categorie = $categorieDAO->findById( $row[ 'categorie_id' ] );
            if ( method_exists( $detailFrais, 'setCategorie' ) ) {
                $detailFrais->setCategorie( $categorie );
            }

            $results[] = $detailFrais;
        }
        return $results;
    }

    public function insert( DetailsFrais $f ): bool {
        $sql = 'INSERT INTO details_frais
            (note_id, categorie_id, description, date_frais, montant_veloce, montant_personnel,
             justificatif_path, justificatif_mime, justificatif_size, randomId, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())';

        $stmt = $this->pdo->prepare( $sql );
        $success = $stmt->execute( [
            $f->getNoteId(), $f->getCategorieId(), $f->getDescription(), $f->getDateFrais(),
            $f->getMontantVeloce(), $f->getMontantPersonnel(),
            $f->getJustificatifPath(), $f->getJustificatifMime(), $f->getJustificatifSize(), $f->getRandomId()
        ] );

        if ( $success ) {
            $this->forceNoteToBrouillon( $f->getNoteId() );
            // FORCE BROUILLON
            $this->updateNoteTotal( $f->getNoteId() );
        }

        return $success;
    }
    /**
    * Après chaque ajout ou modification de détail,
    * on force la note à repasser en 'brouillon'**/

    private function forceNoteToBrouillon( int $noteId ): void {
        $sql = "UPDATE notes_frais SET statut = 'brouillon', commentaire_manager = NULL, updated_at = NOW() WHERE id = ?";
        $this->pdo->prepare( $sql )->execute( [ $noteId ] );

        $userStmt = $this->pdo->prepare( 'SELECT user_id FROM notes_frais WHERE id = ?' );
        $userStmt->execute( [ $noteId ] );
        $userId = $userStmt->fetchColumn();

        if ( $userId ) {
            // ✅ UTILISER NotificationClient
            NotificationClient::push(
                $userId,
                'Note réouverte',
                'Votre note de frais a été réouverte pour modification.',
                'warning'
            );
        }
    }

    public function delete( $id ) {
        // On récupère le note_id avant suppression pour pouvoir reopen si besoin
        $sqlGet = 'SELECT note_id FROM details_frais WHERE id = ?';
        $stmtGet = $this->pdo->prepare( $sqlGet );
        $stmtGet->execute( [ $id ] );
        $noteId = $stmtGet->fetchColumn();

        $sql = 'DELETE FROM details_frais WHERE id = ?';
        $stmt = $this->pdo->prepare( $sql );
        $success = $stmt->execute( [ $id ] );

        if ( $success && $noteId ) {
            $this->reopenNoteIfRefused( $noteId );
            $this->updateNoteTotal( $noteId );
        }
        return $success;
    }
}