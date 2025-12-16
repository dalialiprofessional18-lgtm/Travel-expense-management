<?php
namespace App\Models\DAO;
use App\Models\Entities\HistoriqueStatut;
use App\Config\Database;
use PDO;

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Entities/User.php';

class HistoriqueStatutDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function insert( HistoriqueStatut $h ) {
        $sql = 'INSERT INTO historique_statuts (deplacement_id, note_id, ancien_statut, nouveau_statut, changed_by, commentaire)
                VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->pdo->prepare( $sql );
        return $stmt->execute( [
            $h->getDeplacementId(), $h->getNoteId(), $h->getAncienStatut(),
            $h->getNouveauStatut(), $h->getChangedBy(), $h->getCommentaire()
        ] );
    }

    public function findByDeplacement( $deplacement_id ) {
        $sql = 'SELECT 
                    h.*,
                    u.nom as user_nom,
                    u.role as user_role
                FROM historique_statuts h
                LEFT JOIN users u ON h.changed_by = u.id
                WHERE h.deplacement_id = ?
                ORDER BY h.created_at DESC';
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $deplacement_id ] );
        $rows = $stmt->fetchAll( PDO::FETCH_ASSOC );
        $list = [];
        
        foreach ( $rows as $r ) {
            $hist = new HistoriqueStatut(
                $r[ 'id' ], 
                $r[ 'deplacement_id' ], 
                $r[ 'note_id' ], 
                $r[ 'ancien_statut' ],
                $r[ 'nouveau_statut' ], 
                $r[ 'changed_by' ], 
                $r[ 'commentaire' ], 
                $r[ 'created_at' ]
            );
            
            // Ajouter les infos utilisateur
            $hist->setUserNom( $r['user_nom'] ?? null );
            $hist->setUserRole( $r['user_role'] ?? null );
            
            $list[] = $hist;
        }
        
        return $list;
    }

    public function findByNote( $note_id ) {
        $sql = 'SELECT 
                    h.*,
                    u.nom as user_nom,
                    u.role as user_role
                FROM historique_statuts h
                LEFT JOIN users u ON h.changed_by = u.id
                WHERE h.note_id = ?
                ORDER BY h.created_at DESC';
        
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $note_id ] );
        $rows = $stmt->fetchAll( PDO::FETCH_ASSOC );
        $list = [];
        
        foreach ( $rows as $r ) {
            $hist = new HistoriqueStatut(
                $r[ 'id' ], 
                $r[ 'deplacement_id' ], 
                $r[ 'note_id' ], 
                $r[ 'ancien_statut' ],
                $r[ 'nouveau_statut' ], 
                $r[ 'changed_by' ], 
                $r[ 'commentaire' ], 
                $r[ 'created_at' ]
            );
            
            // Ajouter les infos utilisateur
            $hist->setUserNom( $r['user_nom'] ?? null );
            $hist->setUserRole( $r['user_role'] ?? null );
            
            $list[] = $hist;
        }
        
        return $list;
    }
}