<?php
namespace App\Models\DAO;
// dao/CalendrierDAO.php
use App\Config\Database;
use PDOException;
use PDO;
class CalendrierDAO {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    /**
     * Récupérer tous les déplacements pour le calendrier
     */

    public function getTousLesDeplacements($user_id = null, $role = 'employe')
    {
        try {
            if ($role === 'admin' || $role === 'manager') {
                // Admin et Manager voient tout
                $sql = "SELECT 
                            d.id,
                            d.user_id,
                            d.titre,
                            d.lieu_depart,
                            d.lieu as lieu_destination,
                            d.date_depart,
                            d.date_retour,
                            d.objet,
                            d.created_at,
                            d.updated_at,
                            
                            -- Informations utilisateur COMPLÈTES
                            u.nom as employe_nom,
                            u.email as employe_email,
                            u.role as employe_role,
                            u.job_title as employe_job_title,
                            
                            -- Avatar complet
                            u.avatar_path,
                            u.avatar_mime,
                            u.avatar_size,
                            
                            -- Cover complet
                            u.cover_path,
                            u.cover_mime,
                            u.cover_size,
                            
                            -- Note de frais
                            nf.id as note_id,
                            nf.statut as note_statut,
                            nf.montant_total,
                            
                            -- Statut formaté
                            CASE 
                                WHEN nf.statut = 'brouillon' THEN 'brouillon'
                                WHEN nf.statut = 'soumis' THEN 'en_attente'
                                WHEN nf.statut = 'valide_manager' THEN 'valide'
                                WHEN nf.statut = 'rejetee_manager' THEN 'refuse'
                                WHEN nf.statut = 'approuve' THEN 'termine'
                                WHEN nf.statut = 'rejetee_admin' THEN 'refuse'
                                ELSE 'brouillon'
                            END as statut_display
                        FROM deplacements d
                        INNER JOIN users u ON d.user_id = u.id
                        LEFT JOIN notes_frais nf ON d.id = nf.deplacement_id
                        ORDER BY d.date_depart ASC";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
            } else {
                // Employé voit uniquement ses déplacements
                $sql = "SELECT 
                            d.id,
                            d.user_id,
                            d.titre,
                            d.lieu_depart,
                            d.lieu as lieu_destination,
                            d.date_depart,
                            d.date_retour,
                            d.objet,
                            d.created_at,
                            d.updated_at,
                            
                            -- Informations utilisateur COMPLÈTES
                            u.nom as employe_nom,
                            u.email as employe_email,
                            u.role as employe_role,
                            u.job_title as employe_job_title,
                            
                            -- Avatar complet
                            u.avatar_path,
                            u.avatar_mime,
                            u.avatar_size,
                            
                            -- Cover complet
                            u.cover_path,
                            u.cover_mime,
                            u.cover_size,
                            
                            -- Note de frais
                            nf.id as note_id,
                            nf.statut as note_statut,
                            nf.montant_total,
                            
                            -- Statut formaté
                            CASE 
                                WHEN nf.statut = 'brouillon' THEN 'brouillon'
                                WHEN nf.statut = 'soumis' THEN 'en_attente'
                                WHEN nf.statut = 'valide_manager' THEN 'valide'
                                WHEN nf.statut = 'rejetee_manager' THEN 'refuse'
                                WHEN nf.statut = 'approuve' THEN 'termine'
                                WHEN nf.statut = 'rejetee_admin' THEN 'refuse'
                                ELSE 'brouillon'
                            END as statut_display
                        FROM deplacements d
                        INNER JOIN users u ON d.user_id = u.id
                        LEFT JOIN notes_frais nf ON d.id = nf.deplacement_id
                        WHERE d.user_id = ?
                        ORDER BY d.date_depart ASC";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$user_id]);
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur CalendrierDAO::getTousLesDeplacements: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les statistiques du mois
     */
    public function getStatistiquesMois($mois, $annee, $user_id = null, $role = 'employe') {
        try {
            $date_debut = "$annee-$mois-01";
            $date_fin = date("Y-m-t", strtotime($date_debut));
            
            $sql = "SELECT 
                        COUNT(DISTINCT d.id) as total_deplacements,
                        COUNT(CASE WHEN nf.statut = 'soumis' THEN 1 END) as en_attente_validation,
                        COUNT(CASE WHEN d.date_depart <= CURDATE() AND d.date_retour >= CURDATE() THEN 1 END) as en_cours,
                        COUNT(CASE WHEN nf.statut = 'approuve' THEN 1 END) as termines,
                        COALESCE(SUM(nf.montant_total), 0) as budget_total,
                        COUNT(DISTINCT d.user_id) as nombre_employes
                    FROM deplacements d
                    LEFT JOIN notes_frais nf ON d.id = nf.deplacement_id
                    WHERE d.date_depart BETWEEN ? AND ?";
            
            if ($role === 'employe') {
                $sql .= " AND d.user_id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$date_debut, $date_fin, $user_id]);
            } else {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$date_debut, $date_fin]);
            }
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur CalendrierDAO::getStatistiquesMois: " . $e->getMessage());
            return [
                'total_deplacements' => 0,
                'en_attente_validation' => 0,
                'en_cours' => 0,
                'termines' => 0,
                'budget_total' => 0,
                'nombre_employes' => 0
            ];
        }
    }
    
    /**
     * Récupérer les déplacements d'un jour spécifique
     */
    public function getDeplacementsParJour($date, $user_id = null, $role = 'employe') {
        try {
            $sql = "SELECT 
                        d.*,
                        u.nom as employe_nom,
                        u.email as employe_email,
                        u.avatar_path,
                        nf.statut as note_statut,
                        nf.montant_total
                    FROM deplacements d
                    INNER JOIN users u ON d.user_id = u.id
                    LEFT JOIN notes_frais nf ON d.id = nf.deplacement_id
                    WHERE (d.date_depart <= ? AND d.date_retour >= ?)";
            
            if ($role === 'employe') {
                $sql .= " AND d.user_id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$date, $date, $user_id]);
            } else {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$date, $date]);
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur CalendrierDAO::getDeplacementsParJour: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les prochains déplacements (7 jours)
     */
    public function getProchainsDeplacements($user_id = null, $role = 'employe', $limit = 5) {
        try {
            $sql = "SELECT 
                        d.*,
                        u.nom as employe_nom,
                        u.avatar_path,
                        nf.statut as note_statut,
                        DATEDIFF(d.date_depart, CURDATE()) as jours_restants
                    FROM deplacements d
                    INNER JOIN users u ON d.user_id = u.id
                    LEFT JOIN notes_frais nf ON d.id = nf.deplacement_id
                    WHERE d.date_depart >= CURDATE()";
            
            if ($role === 'employe') {
                $sql .= " AND d.user_id = ?";
            }
            
            $sql .= " ORDER BY d.date_depart ASC LIMIT ?";
            
            $stmt = $this->pdo->prepare($sql);
            
            if ($role === 'employe') {
                $stmt->execute([$user_id, $limit]);
            } else {
                $stmt->execute([$limit]);
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur CalendrierDAO::getProchainsDeplacements: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter les notifications en attente
     */
    public function getNotificationsEnAttente($user_id, $role) {
        try {
            if ($role === 'manager' || $role === 'admin') {
                // Manager/Admin : compter les notes en attente de validation
                $sql = "SELECT COUNT(*) as count
                        FROM notes_frais nf
                        INNER JOIN deplacements d ON nf.deplacement_id = d.id
                        WHERE nf.statut = 'soumis'";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
            } else {
                // Employé : compter ses propres notifications
                $sql = "SELECT COUNT(*) as count
                        FROM notifications
                        WHERE user_id = ? AND is_read = 0";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$user_id]);
            }
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Erreur CalendrierDAO::getNotificationsEnAttente: " . $e->getMessage());
            return 0;
        }
    }
}
?>