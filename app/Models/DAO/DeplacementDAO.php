<?php
namespace App\Models\DAO;
use App\Models\Entities\Deplacement;
use App\Config\Database;
use PDO;

require_once __DIR__ . '/../../config/Database.php';
// ← ده السطر الصحيح
require_once __DIR__ . '/../Entities/User.php';

class DeplacementDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function findByNoteId( int $noteId ) {
        $sql = "
        SELECT d.* 
        FROM deplacements d
        JOIN notes_frais nf ON nf.deplacement_id = d.id
        WHERE nf.id = ?
        LIMIT 1
    ";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $noteId ] );

        $r = $stmt->fetch( PDO::FETCH_ASSOC );

        if ( !$r ) return null;

        return new Deplacement(
            $r[ 'id' ],
            $r[ 'user_id' ],
            $r[ 'titre' ],
            $r[ 'lieu_depart' ],

            $r[ 'lieu' ],
            $r[ 'date_depart' ],
            $r[ 'date_retour' ],
            $r[ 'objet' ],
            $r[ 'created_at' ],
            $r[ 'updated_at' ]
        );
    }

    public function findById( $id ) {
        $sql = 'SELECT * FROM deplacements WHERE id = ?';
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $id ] );
        $r = $stmt->fetch( PDO::FETCH_ASSOC );
        if ( !$r ) return null;
        return new Deplacement(
            $r[ 'id' ], $r[ 'user_id' ], $r[ 'titre' ],  $r[ 'lieu_depart' ], $r[ 'lieu' ],
            $r[ 'date_depart' ], $r[ 'date_retour' ],
            $r[ 'objet' ],  $r[ 'created_at' ], $r[ 'updated_at' ]
        );
    }

    // Dans DeplacementDAO.php - Ajouter ces méthodes

/**
 * Récupérer les déplacements de l'équipe avec pagination
 */
public function findByTeamWithPagination($managerId, $limit = 10, $offset = 0)
{
    $sql = "
        SELECT 
            d.*,
            u.nom as employe_nom,
            u.email as employe_email,
            nf.statut as note_statut,
            nf.montant_total
        FROM deplacements d
        INNER JOIN users u ON d.user_id = u.id
        LEFT JOIN notes_frais nf ON d.id = nf.deplacement_id
        WHERE u.manager_id = :manager_id
        ORDER BY d.date_depart DESC, d.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':manager_id', $managerId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Compter le total des déplacements de l'équipe
 */
public function countByTeam($managerId)
{
    $sql = "
        SELECT COUNT(*) as total
        FROM deplacements d
        INNER JOIN users u ON d.user_id = u.id
        WHERE u.manager_id = :manager_id
    ";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':manager_id', $managerId, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$result['total'];
}

/**
 * Compter les déplacements de l'équipe par statut
 */
public function countByTeamAndStatus(int $managerId, string $status): int
{
    $sql = "
        SELECT COUNT(*) AS total
        FROM deplacements d
        INNER JOIN users u ON d.user_id = u.id
        INNER JOIN notes_frais nf ON d.id = nf.deplacement_id
        WHERE u.manager_id = :manager_id
        AND nf.statut = :status
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':manager_id', $managerId, PDO::PARAM_INT);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}


/**
 * Compter les déplacements de l'équipe ce mois
 */
public function countByTeamThisMonth($managerId)
{
    $sql = "
        SELECT COUNT(*) as total
        FROM deplacements d
        INNER JOIN users u ON d.user_id = u.id
        WHERE u.manager_id = :manager_id
        AND MONTH(d.date_depart) = MONTH(CURRENT_DATE())
        AND YEAR(d.date_depart) = YEAR(CURRENT_DATE())
    ";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':manager_id', $managerId, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$result['total'];
}

/**
 * Récupérer les déplacements récents de l'équipe (pour le dashboard)
 */
public function findRecentByTeam($managerId, $limit = 5)
{
    $sql = "
        SELECT 
            d.*,
            u.nom as employe_nom,
            u.email as employe_email,
            nf.statut as note_statut,
            nf.montant_total
        FROM deplacements d
        INNER JOIN users u ON d.user_id = u.id
        LEFT JOIN notes_frais nf ON d.id = nf.deplacement_id
        WHERE u.manager_id = :manager_id
        AND nf.statut != 'brouillon'
        ORDER BY d.created_at DESC
        LIMIT :limit
    ";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':manager_id', $managerId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function findByUser( $user_id ) {
        $sql = 'SELECT * FROM deplacements 
            WHERE user_id = ? 
            ORDER BY created_at DESC';

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $user_id ] );

        $rows = $stmt->fetchAll( PDO::FETCH_ASSOC );
        $list = [];

        foreach ( $rows as $r ) {
            $list[] = new Deplacement(
                $r[ 'id' ], $r[ 'user_id' ], $r[ 'titre' ], $r[ 'lieu_depart' ],
                $r[ 'lieu' ], $r[ 'date_depart' ], $r[ 'date_retour' ],
                $r[ 'objet' ], $r[ 'created_at' ], $r[ 'updated_at' ]
            );
        }

        return $list;
    }

    public function insert( Deplacement $d ) {
        $sql = 'INSERT INTO deplacements (user_id, titre, lieu_depart, lieu, date_depart, date_retour, objet) 
            VALUES (?, ?, ?, ?, ?, ?, ?)';
        // ✅ Ajouter lieu_depart
        $stmt = $this->pdo->prepare( $sql );
        return $stmt->execute( [
            $d->getUserId(),
            $d->getTitre(),
            $d->getLieuDepart(),  // ✅ NOUVEAU
            $d->getLieu(),
            $d->getDateDepart(),
            $d->getDateRetour(),
            $d->getObjet()
        ] );
    }
    /**
 * Met à jour un déplacement existant en base de données
 */
public function update(Deplacement $d): bool
{
    $sql = "
        UPDATE deplacements 
        SET 
            user_id      = ?,
            titre        = ?,
            lieu_depart  = ?,
            lieu         = ?,
            date_depart  = ?,
            date_retour  = ?,
            objet        = ?,
            updated_at   = NOW()
        WHERE id = ?
    ";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        $d->getUserId(),
        $d->getTitre(),
        $d->getLieuDepart(),     // جديد
        $d->getLieu(),
        $d->getDateDepart(),
        $d->getDateRetour(),
        $d->getObjet(),
        $d->getId()              // WHERE id = ?
    ]);
}

    public function findRecentForTeam( int $managerId, int $limit = 5 ): array {
        // 1. Récupérer les déplacements récents de l'équipe
    $sql = "
        SELECT 
            d.id, d.titre, d.lieu, d.date_depart, d.date_retour,
            u.nom AS employe_nom
        FROM deplacements d
        JOIN users u ON d.user_id = u.id
        WHERE u.manager_id = ?
        ORDER BY d.created_at DESC
        LIMIT ?
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$managerId, $limit]);

    $deplacements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Pour chaque déplacement → récupérer le statut de la note de frais
    $results = [];
    foreach ($deplacements as $dep) {
        $noteStatut = 'Aucune note';

        // Chercher s'il existe une note pour ce déplacement
        $noteSql = 'SELECT statut FROM notes_frais WHERE deplacement_id = ? LIMIT 1';
        $noteStmt = $this->pdo->prepare( $noteSql );
        $noteStmt->execute( [ $dep[ 'id' ] ] );
        $note = $noteStmt->fetch( PDO::FETCH_ASSOC );

        if ( $note ) {
            $noteStatut = match ( $note[ 'statut' ] ) {
                'brouillon' => 'Brouillon',
                'soumis'    => 'Soumise',
                'valide_manager'   => 'Validée_manager',
                'rejetee_manager'   => 'Rejetee_manager',
                'en_cours_admin'     => 'En_cours_admin',
                'approuve'     => 'Approuve',
                'rejetee_admin'     => 'Rejetee_admin',
                default     => ucfirst( $note[ 'statut' ] )
            }
            ;
        }

        $results[] = [
            'id'            => $dep[ 'id' ],
            'titre'         => $dep[ 'titre' ],
            'lieu'          => $dep[ 'lieu' ],
            'date_depart'   => $dep[ 'date_depart' ],
            'date_retour'   => $dep[ 'date_retour' ],
            'employe_nom'   => $dep[ 'employe_nom' ],
            'note_statut'   => $noteStatut,        // Nouveau champ
        ];
    }

    return $results;
}

public function findAll(): array {
    $sql = "SELECT d.*, u.nom as user_nom 
            FROM deplacements d
            JOIN users u ON d.user_id = u.id
            ORDER BY d.date_depart DESC";

    $stmt = $this->pdo->query( $sql );

    $deplacements = [];
    while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
        $deplacements[] = new Deplacement(
            $row[ 'id' ],
            $row[ 'user_id' ],
            $row[ 'titre' ],
            $row[ 'lieu_depart' ],
            $row[ 'lieu' ],
            $row[ 'date_depart' ],
            $row[ 'date_retour' ],
            $row[ 'objet' ] ?? null,
            $row[ 'created_at' ] ?? null,
            $row[ 'updated_at' ] ?? null
        );
    }
    return $deplacements;
}

public function countAll(): int {
    return ( int )$this->pdo->query( 'SELECT COUNT(*) FROM deplacements' )->fetchColumn();
}

public function findByUserId( int $userId, int $limit = 10 ): array {
    $sql = 'SELECT * FROM deplacements WHERE user_id = ? ORDER BY created_at DESC LIMIT ?';

    $stmt = $this->pdo->prepare( $sql );
    $stmt->execute( [ $userId, $limit ] );

    $results = [];
    while ( $row = $stmt->fetch( \PDO::FETCH_ASSOC ) ) {
        $noteSql = 'SELECT statut FROM notes_frais WHERE deplacement_id = ? LIMIT 1';
        $noteStmt = $this->pdo->prepare( $noteSql );
        $noteStmt->execute( [ $row[ 'id' ] ] );
        $note = $noteStmt->fetch( PDO::FETCH_ASSOC );

        if ( $note ) {
            $noteStatut = match ( $note[ 'statut' ] ) {
                'brouillon' => 'Brouillon',
                'soumis'    => 'Soumise',
                'validee'   => 'Validée',
                'refusee'   => 'Refusée',
                'payee'     => 'Payée',
                default     => ucfirst( $note[ 'statut' ] )
            }
            ;
        }
        $results[] = [
            'id'            => $row[ 'id' ],
            'user_id'            => $row[ 'user_id' ],
            'titre'         => $row[ 'titre' ],
            'lieu_depart'   => $row[ 'lieu_depart' ],
            'lieu'          => $row[ 'lieu' ],
            'date_depart'   => $row[ 'date_depart' ],
            'date_retour'   => $row[ 'date_retour' ],
            'created_at'   => $row[ 'created_at' ],
            'updated_at'   => $row[ 'updated_at' ],
            'note_statut'   => $noteStatut, ];
        }

        return $results;
    }

    public function insertAndGetId( Deplacement $d ): int {
        $sql = "INSERT INTO deplacements 
            (user_id, titre, lieu_depart, lieu, date_depart, date_retour, objet) 
            VALUES (?, ?, ?, ?, ?, ?,?)";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [
            $d->getUserId(),
            $d->getTitre(),
            $d->getLieuDepart(),  // ✅ NOUVEAU
            $d->getLieu(),
            $d->getDateDepart(),
            $d->getDateRetour(),
            $d->getObjet()
        ] );

        return ( int )$this->pdo->lastInsertId();
        // هنا المهم
    }
    // عدد الديبلاسمون حسب الحالة وللمستخدم أو لفريقه

    // عدد الديبلاسمون لكل الموظفين تحت مدير معين

    public function updateStatut( $id, $statut ) {
        $sql = 'UPDATE deplacements SET statut=? WHERE id=?';
        $stmt = $this->pdo->prepare( $sql );
        return $stmt->execute( [ $statut, $id ] );
    }

    public function delete( $id ) {
        $sql = 'DELETE FROM deplacements WHERE id=?';
        $stmt = $this->pdo->prepare( $sql );
        return $stmt->execute( [ $id ] );
    }
}
