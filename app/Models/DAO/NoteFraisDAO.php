<?php

namespace App\Models\DAO;

use App\Models\Entities\NoteFrais;
use App\Config\Database;
use PDO;

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Entities/NoteFrais.php'; // Ajouté pour éviter les erreurs
use App\WebSocket\NotificationClient;
class NoteFraisDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function findById($id)
    {
        $sql = 'SELECT * FROM notes_frais WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$r) return null;

        return new NoteFrais(
            $r['id'],
            $r['deplacement_id'],
            $r['user_id'],
            $r['statut'] ?? 'brouillon',
            $r['montant_total'] ?? 0,
            $r['totale_rembosement'] ?? null,        // ← ajouté
            $r['created_at'] ?? null,
            $r['updated_at'] ?? null
        );
    }
  
/**
 * Récupérer les notes avec filtres et pagination
 * CORRIGÉ : Colonnes alignées avec votre schéma de base de données
 */
public function findAllWithDetailsAndFilters(int $limit, int $offset, string $statusFilter = '', string $searchTerm = ''): array
{
    $sql = "SELECT 
                n.id as note_id,
                n.deplacement_id,
                n.statut,
                n.montant_total,
                n.created_at,
                n.updated_at,
                n.commentaire_admin,
                n.commentaire_manager,
                
                u.id as employe_id,
                u.nom as employe_nom,
                u.email as employe_email,
                u.avatar_path as employe_avatar,  -- ⚠️ CHANGÉ: avatar_url → avatar_path
                
                d.titre as deplacement_titre,
                d.lieu as deplacement_lieu,
                d.date_depart as deplacement_date_debut,  -- ⚠️ CHANGÉ: date_debut → date_depart
                d.date_retour as deplacement_date_fin,    -- ⚠️ CHANGÉ: date_fin → date_retour
                
                m.nom as manager_nom,
                m.email as manager_email
                
            FROM notes_frais n
            INNER JOIN users u ON n.user_id = u.id
            INNER JOIN deplacements d ON n.deplacement_id = d.id
            LEFT JOIN users m ON u.manager_id = m.id
            WHERE n.statut IN ('valide_manager', 'en_cours_admin', 'approuve', 'rejetee_admin')";
    
    $params = [];
    
    // Filtre par statut
    if (!empty($statusFilter)) {
        $sql .= " AND n.statut = :status";
        $params[':status'] = $statusFilter;
    }
    
    // Filtre par recherche
    if (!empty($searchTerm)) {
        $sql .= " AND (
            u.nom LIKE :search 
            OR u.email LIKE :search 
            OR d.titre LIKE :search 
            OR d.lieu LIKE :search
        )";
        $params[':search'] = '%' . $searchTerm . '%';
    }
    
    $sql .= " ORDER BY n.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $this->pdo->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
/**
 * Compter les notes avec filtres
 */
public function countAllWithFilters(string $statusFilter = '', string $searchTerm = ''): int
{
    $sql = "SELECT COUNT(*) as total
            FROM notes_frais n
            INNER JOIN users u ON n.user_id = u.id
            INNER JOIN deplacements d ON n.deplacement_id = d.id
            WHERE n.statut IN ('valide_manager', 'en_cours_admin', 'approuve', 'rejetee_admin')";
    
    $params = [];
    
    if (!empty($statusFilter)) {
        $sql .= " AND n.statut = :status";
        $params[':status'] = $statusFilter;
    }
    
    if (!empty($searchTerm)) {
        $sql .= " AND (
            u.nom LIKE :search 
            OR u.email LIKE :search 
            OR d.titre LIKE :search 
            OR d.lieu LIKE :search
        )";
        $params[':search'] = '%' . $searchTerm . '%';
    }
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    
    return (int)$stmt->fetch(\PDO::FETCH_ASSOC)['total'];
}

/**
 * Compter par statut
 */
public function countByStatus(string $status): int
{
    $sql = "SELECT COUNT(*) as total FROM notes_frais WHERE statut = :status";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':status' => $status]);
    return (int)$stmt->fetch(\PDO::FETCH_ASSOC)['total'];
}
public function countByStatus1(): int
{
    $sql = "SELECT COUNT(*) as total FROM notes_frais ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([]);
    return (int)$stmt->fetch(\PDO::FETCH_ASSOC)['total'];
}

/**
 * Montant total
 */
public function getTotalMontant(): float
{
    $sql = "SELECT COALESCE(SUM(montant_total), 0) as total 
            FROM notes_frais 
            WHERE statut IN ('valide_manager', 'en_cours_admin', 'approuve', 'rejetee_admin')";
    $stmt = $this->pdo->query($sql);
    return (float)$stmt->fetch(\PDO::FETCH_ASSOC)['total'];
}


// ==========================================
// 3. ROUTE DANS routes.php
// ==========================================

// Routes Admin - Demandes

// ==========================================
// 4. MODIFIER dashboard() POUR N'AFFICHER QUE 5 DEMANDES
//
public function findAllWithDetails(int $limit = 100, int $offset = 0): array
{
    $sql = "
        SELECT 
            nf.id AS note_id,
            nf.deplacement_id,
            nf.user_id,
            nf.statut,
            nf.montant_total,
            nf.commentaire_manager,
            nf.commentaire_admin,
            nf.created_at,
            nf.updated_at,
            
            -- Employé
            u.nom AS employe_nom,
            u.email AS employe_email,
            u.avatar_path AS employe_avatar,
            
            -- Manager
            m.nom AS manager_nom,
            m.email AS manager_email,
            m.id AS manager_id,
            
            -- Déplacement
            d.titre AS deplacement_titre,
            d.lieu AS deplacement_lieu,
            d.date_depart,
            d.date_retour,
            d.objet AS deplacement_objet
            
        FROM notes_frais nf
        
        INNER JOIN users u ON nf.user_id = u.id
        LEFT JOIN users m ON u.manager_id = m.id
        INNER JOIN deplacements d ON nf.deplacement_id = d.id
        
        ORDER BY 
            CASE nf.statut
                WHEN 'soumis' THEN 1
                WHEN 'valide_manager' THEN 2
                WHEN 'rejetee_manager' THEN 3
                WHEN 'approuve' THEN 4
                WHEN 'rejetee_admin' THEN 5
                ELSE 6
            END,
            nf.created_at DESC
            
        LIMIT ? OFFSET ?
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$limit, $offset]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
        public function calculateTotalMontant($userId)
    {
        $sql = "SELECT SUM(montant_total) 
                FROM notes_frais 
                WHERE user_id = ? AND statut = 'approuve'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        return (float) $stmt->fetchColumn();
    }


    public function findOneByDeplacementId(int $deplacement_id): ?NoteFrais
    {
        $sql = 'SELECT * FROM notes_frais WHERE deplacement_id = ? LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$deplacement_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new NoteFrais(
            $data['id'],
            $data['deplacement_id'],
            $data['user_id'],
            $data['statut'] ?? 'brouillon',
            $data['montant_total'] ?? 0,
            $data['totale_rembosement'] ?? null,     // ← ajouté
            $data['commentaire_manager'] ?? null,     // ← ajouté
            $data['commentaire_admin'] ?? null,     // ← ajouté
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    public function findByDeplacement($deplacement_id)
    {
        $sql = 'SELECT * FROM notes_frais WHERE deplacement_id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$deplacement_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $list = [];

        foreach ($rows as $r) {
            $list[] = new NoteFrais(
                $r['id'],
                $r['deplacement_id'],
                $r['user_id'],
                $r['statut'] ?? 'brouillon',
                $r['montant_total'] ?? 0,
                $r['commentaire'] ?? null,    // ← ajouté
                $r['created_at'] ?? null,
                $r['updated_at'] ?? null
            );
        }
        return $list;
    }

    public function insert(NoteFrais $n): bool
    {
        $sql = 'INSERT INTO notes_frais 
                (deplacement_id, user_id, statut, montant_total, commentaire_manager) 
                VALUES (?, ?, ?, ?, ?)';
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            $n->getDeplacementId(),
            $n->getUserId(),
            $n->getStatut(),
            $n->getMontantTotal(),
            $n->getCommentaire()              // ← ajouté
        ]);
    }



    public function countByStatusForTeam(int $managerId, ?string $status = null): int
    {
        $sql = "SELECT COUNT(d.id) 
                FROM notes_frais d 
                JOIN users u ON d.user_id = u.id 
                WHERE u.manager_id = ?";

        $params = [$managerId];

        if ($status !== null) {
            $sql .= ' AND d.statut = ?';
            $params[] = $status;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }    public function updateStatut(int $noteId, string $statut, ?string $commentaire = null): bool
    {
        $sql = 'UPDATE notes_frais 
                SET statut = ?, 
                    commentaire_manager = ?, 
                    updated_at = NOW() 
                WHERE id = ?';

        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([$statut, $commentaire, $noteId]);

        if ($success) {
            // Récupérer les informations complètes
            $infoSql = "
                SELECT 
                    nf.user_id,
                    nf.deplacement_id,
                    u.manager_id,
                    u.nom AS employe_nom,
                    m.nom AS manager_nom,
                    d.titre AS deplacement_titre,
                    nf.montant_total
                FROM notes_frais nf
                JOIN users u ON nf.user_id = u.id
                LEFT JOIN users m ON u.manager_id = m.id
                JOIN deplacements d ON nf.deplacement_id = d.id
                WHERE nf.id = ?
            ";
            
            $infoStmt = $this->pdo->prepare($infoSql);
            $infoStmt->execute([$noteId]);
            $info = $infoStmt->fetch(PDO::FETCH_ASSOC);

            if (!$info) return $success;

            // ========================================
            // CAS 1: EMPLOYÉ SOUMET → Notifier MANAGER
            // ========================================
            if ($statut === 'soumis' && $info['manager_id']) {
                NotificationClient::push(
                    $info['manager_id'],
                    "📋 Nouvelle demande de {$info['employe_nom']}",
                    "Demande pour « {$info['deplacement_titre']} » - Montant: " . 
                    number_format($info['montant_total'], 2) . " €",
                    'info'
                );
            }

            // ========================================
            // CAS 2: MANAGER VALIDE → Notifier EMPLOYÉ + ADMIN
            // ========================================
            elseif ($statut === 'valide_manager') {
                // Notifier l'employé
                NotificationClient::push(
                    $info['user_id'],
                    "✅ Note validée par votre manager",
                    "Votre note « {$info['deplacement_titre']} » a été approuvée par {$info['manager_nom']}",
                    'success'
                );

                // Notifier tous les admins
                $this->notifyAllAdmins(
                    "✅ Note validée par {$info['manager_nom']}",
                    "Note de {$info['employe_nom']} - « {$info['deplacement_titre']} » (" . 
                    number_format($info['montant_total'], 2) . " €) en attente d'approbation finale",
                    'info'
                );
            }

            // ========================================
            // CAS 3: MANAGER REJETTE → Notifier EMPLOYÉ + ADMIN
            // ========================================
            elseif ($statut === 'rejetee_manager') {
                $motifText = $commentaire ? " Motif: " . strip_tags($commentaire) : "";
                
                // Notifier l'employé
                NotificationClient::push(
                    $info['user_id'],
                    "❌ Note rejetée par votre manager",
                    "Votre note « {$info['deplacement_titre']} » a été refusée.{$motifText}",
                    'danger'
                );

                // Notifier tous les admins
                $this->notifyAllAdmins(
                    "❌ Note rejetée par {$info['manager_nom']}",
                    "Note de {$info['employe_nom']} - « {$info['deplacement_titre']} » rejetée.{$motifText}",
                    'warning'
                );
            }

            // ========================================
            // CAS 4: ADMIN APPROUVE → Notifier EMPLOYÉ + MANAGER
            // ========================================
            elseif ($statut === 'approuve') {
                // Notifier l'employé
                NotificationClient::push(
                    $info['user_id'],
                    "🎉 Note approuvée définitivement",
                    "Votre note « {$info['deplacement_titre']} » (" . 
                    number_format($info['montant_total'], 2) . " €) a été approuvée par l'admin",
                    'success'
                );

                // Notifier le manager
                if ($info['manager_id']) {
                    NotificationClient::push(
                        $info['manager_id'],
                        "✅ Note approuvée par l'admin",
                        "La note de {$info['employe_nom']} - « {$info['deplacement_titre']} » est approuvée",
                        'success'
                    );
                }
            }

            // ========================================
            // CAS 5: ADMIN REJETTE → Notifier EMPLOYÉ + MANAGER
            // ========================================
            elseif ($statut === 'rejetee_admin') {
                $motifText = $commentaire ? " Motif: " . strip_tags($commentaire) : "";
                
                // Notifier l'employé
                NotificationClient::push(
                    $info['user_id'],
                    "❌ Note rejetée par l'admin",
                    "Votre note « {$info['deplacement_titre']} » a été refusée.{$motifText}",
                    'danger'
                );

                // Notifier le manager
                if ($info['manager_id']) {
                    NotificationClient::push(
                        $info['manager_id'],
                        "❌ Note rejetée par l'admin",
                        "La note de {$info['employe_nom']} - « {$info['deplacement_titre']} » a été refusée par l'admin",
                        'warning'
                    );
                }
            }
        }

        return $success;
    }

    /**
     * ========================================
     * ACTIONS ADMIN DIRECTES
     * ========================================
     */

    /**
     * Admin approuve directement (bypass manager)
     *//**
 * Admin approuve directement avec montant de remboursement
 */
public function adminApprove(int $noteId, string $commentaire = null, float $montantRembourser = 0): bool
{
    $sql = 'UPDATE notes_frais 
            SET statut = "approuve", 
                commentaire_admin = ?,
                totale_rembosement = ?, 
                updated_at = NOW() 
            WHERE id = ?';

    $stmt = $this->pdo->prepare($sql);
    $success = $stmt->execute([$commentaire, $montantRembourser, $noteId]);

    if ($success) {
        // Récupérer les infos
        $infoSql = "
            SELECT 
                nf.user_id,
                u.manager_id,
                u.nom AS employe_nom,
                d.titre AS deplacement_titre,
                nf.montant_total
            FROM notes_frais nf
            JOIN users u ON nf.user_id = u.id
            JOIN deplacements d ON nf.deplacement_id = d.id
            WHERE nf.id = ?
        ";
        
        $infoStmt = $this->pdo->prepare($infoSql);
        $infoStmt->execute([$noteId]);
        $info = $infoStmt->fetch(PDO::FETCH_ASSOC);

        if ($info) {
            // Notifier l'employé
            NotificationClient::push(
                $info['user_id'],
                "🎉 Note approuvée par l'admin",
                "Votre note « {$info['deplacement_titre']} » a été approuvée. Montant à rembourser : " . 
                number_format($montantRembourser, 2, ',', ' ') . " €",
                'success'
            );

            // Notifier le manager
            if ($info['manager_id']) {
                NotificationClient::push(
                    $info['manager_id'],
                    "✅ Approbation directe",
                    "La note de {$info['employe_nom']} - « {$info['deplacement_titre']} » approuvée directement par l'admin. Remboursement : " . 
                    number_format($montantRembourser, 2, ',', ' ') . " €",
                    'info'
                );
            }
        }
    }

    return $success;
}

    /**
     * Admin rejette directement
     */
    public function adminReject(int $noteId, string $motif): bool
    {
        $sql = 'UPDATE notes_frais 
                SET statut = "rejetee_admin", 
                    commentaire_admin = ?,
                    updated_at = NOW() 
                WHERE id = ?';

        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([$motif, $noteId]);

        if ($success) {
            // Récupérer les infos
            $infoSql = "
                SELECT 
                    nf.user_id,
                    u.manager_id,
                    u.nom AS employe_nom,
                    d.titre AS deplacement_titre
                FROM notes_frais nf
                JOIN users u ON nf.user_id = u.id
                JOIN deplacements d ON nf.deplacement_id = d.id
                WHERE nf.id = ?
            ";
            
            $infoStmt = $this->pdo->prepare($infoSql);
            $infoStmt->execute([$noteId]);
            $info = $infoStmt->fetch(PDO::FETCH_ASSOC);

            if ($info) {
                // Notifier l'employé
                NotificationClient::push(
                    $info['user_id'],
                    "❌ Note rejetée par l'admin",
                    "Votre note « {$info['deplacement_titre']} » a été refusée. Motif: " . 
                    strip_tags($motif),
                    'danger'
                );

                // Notifier le manager
                if ($info['manager_id']) {
                    NotificationClient::push(
                        $info['manager_id'],
                        "❌ Rejet par l'admin",
                        "La note de {$info['employe_nom']} - « {$info['deplacement_titre']} » rejetée par l'admin",
                        'warning'
                    );
                }
            }
        }

        return $success;
    }

    /**
     * Révoquer une décision de manager (Admin revient en arrière)
     */
    public function revokeDecision(int $noteId, string $motifRevocation): bool
    {
        $sql = 'UPDATE notes_frais 
                SET statut = "soumis", 
                    commentaire_admin = ?,
                    updated_at = NOW() 
                WHERE id = ?
                AND statut IN ("valide_manager", "rejetee_manager")';

        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([$motifRevocation, $noteId]);

        if ($success && $stmt->rowCount() > 0) {
            // Récupérer les infos
            $infoSql = "
                SELECT 
                    nf.user_id,
                    u.manager_id,
                    u.nom AS employe_nom,
                    m.nom AS manager_nom,
                    d.titre AS deplacement_titre
                FROM notes_frais nf
                JOIN users u ON nf.user_id = u.id
                LEFT JOIN users m ON u.manager_id = m.id
                JOIN deplacements d ON nf.deplacement_id = d.id
                WHERE nf.id = ?
            ";
            
            $infoStmt = $this->pdo->prepare($infoSql);
            $infoStmt->execute([$noteId]);
            $info = $infoStmt->fetch(PDO::FETCH_ASSOC);

            if ($info) {
                // Notifier l'employé
                NotificationClient::push(
                    $info['user_id'],
                    "🔄 Décision révoquée",
                    "La décision sur votre note « {$info['deplacement_titre']} » a été révoquée. " .
                    "Motif: " . strip_tags($motifRevocation),
                    'warning'
                );

                // Notifier le manager
                if ($info['manager_id']) {
                    NotificationClient::push(
                        $info['manager_id'],
                        "⚠️ Révocation par l'admin",
                        "Votre décision sur la note de {$info['employe_nom']} - « {$info['deplacement_titre']} » " .
                        "a été révoquée. Une nouvelle validation est requise.",
                        'warning'
                    );
                }
            }
        }

        return $success;
    }

    /**
     * ========================================
     * FONCTION HELPER : Notifier tous les admins
     * ========================================
     */
    private function notifyAllAdmins(string $title, string $message, string $type = 'info'): void
    {
        $sql = "SELECT id FROM users WHERE role = 'admin'";
        $stmt = $this->pdo->query($sql);
        $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($admins as $adminId) {
            NotificationClient::push($adminId, $title, $message, $type);
        }
    }
    public function countApprovedByUser(int $userId): int
    {
        $sql = "SELECT COUNT(*) 
                FROM notes_frais nf
                JOIN deplacements d ON nf.deplacement_id = d.id
                WHERE nf.user_id = ? AND nf.statut = 'approuve'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countByUserThisMonth(int $userId): int
    {
        $sql = "SELECT COUNT(*) 
                FROM notes_frais nf
                JOIN deplacements d ON nf.deplacement_id = d.id
                WHERE nf.user_id = ?
                  AND MONTH(d.date_depart) = MONTH(CURRENT_DATE)
                  AND YEAR(d.date_depart) = YEAR(CURRENT_DATE)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function findByDeplacementId(int $deplacementId): ?NoteFrais
    {
        $sql = 'SELECT nf.*, u.nom as employe_nom 
                FROM notes_frais nf
                JOIN deplacements d ON nf.deplacement_id = d.id
                JOIN users u ON d.user_id = u.id
                WHERE nf.deplacement_id = ? 
                LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$deplacementId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new NoteFrais(
            $row['id'],
            $row['deplacement_id'],
            $row['user_id'],
            $row['statut'] ?? 'brouillon',
            (float)$row['montant_total'],
            $row['commentaire'] ?? null,      // ← ajouté
            $row['created_at'] ?? null,
            $row['updated_at'] ?? null
        );
    }

    public function countByStatusAndUser(int $userId, string $status = null): int
    {
        $sql = 'SELECT COUNT(*) FROM notes_frais WHERE user_id = ?';
        $params = [$userId];

        if ($status) {
            $sql .= ' AND statut = ?';
            $params[] = $status;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM notes_frais WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}