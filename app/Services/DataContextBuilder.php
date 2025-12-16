<?php
// ========================================
// 1. SERVICE: DataContextBuilder.php
// Extrait les données de la BDD en temps réel
// ========================================
namespace App\Services;

use App\Models\DAO\UserDAO;
use App\Models\DAO\DeplacementDAO;
use App\Models\DAO\NoteFraisDAO;
use App\Models\DAO\DetailsFraisDAO;
use App\Config\Database;
use PDO;

class DataContextBuilder
{ 
    private PDO $pdo;

    private UserDAO $userDAO;
    private DeplacementDAO $deplacementDAO;
    private NoteFraisDAO $noteFraisDAO;
    private DetailsFraisDAO $detailsDAO;

    public function __construct()
    {
       $this->pdo = Database::getInstance();

        $this->userDAO = new UserDAO();
        $this->deplacementDAO = new DeplacementDAO();
        $this->noteFraisDAO = new NoteFraisDAO();
        $this->detailsDAO = new DetailsFraisDAO();
    }

    /**
     * Génère le contexte complet des données pour l'IA
     */
    public function buildContextForUser($user): array
    {
        $userId = $user->getId();
        $role = $user->getRole();

        $context = [
            'user' => [
                'id' => $userId,
                'nom' => $user->getNom(),
                'email' => $user->getEmail(),
                'role' => $role
            ],
            'timestamp' => date('Y-m-d H:i:s'),
            'statistics' => [],
            'data' => []
        ];

        if ($role === 'admin') {
            $context['statistics'] = $this->getAdminStatistics();
            $context['data'] = $this->getAdminData();
        } elseif ($role === 'manager') {
            $context['statistics'] = $this->getManagerStatistics($userId);
            $context['data'] = $this->getManagerData($userId);
        }

        return $context;
    }

    /**
     * Statistiques globales pour Admin
     */
    private function getAdminStatistics(): array
    {
        $now = date('Y-m-d');
        $thisMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));

        return [
            'users' => [
                'total' => $this->userDAO->countAll(),
                'admins' => $this->countUsersByRole('admin'),
                'managers' => $this->countUsersByRole('manager'),
                'employees' => $this->countUsersByRole('employee')
            ],
            'deplacements' => [
                'total' => $this->deplacementDAO->countAll(),
                'en_cours' => $this->noteFraisDAO->countByStatus('en_cours'),
                'termine' => $this->noteFraisDAO->countByStatus('termine'),
                'annule' => $this->noteFraisDAO->countByStatus('annule'),
                'this_month' => $this->countDeplacementsByMonth($thisMonth),
                'last_month' => $this->countDeplacementsByMonth($lastMonth)
            ],
            'notes_frais' => [
                'total' => $this->noteFraisDAO->countByStatus(),
                'brouillon' => $this->noteFraisDAO->countByStatus('brouillon'),
                'soumis' => $this->noteFraisDAO->countByStatus('soumis'),
                'approuve' => $this->noteFraisDAO->countByStatus('approuve'),
                'refuse' => $this->noteFraisDAO->countByStatus('refuse'),
            ],
            'depenses_par_categorie' => $this->getDepensesByCategory()
        ];
    }

    /**
     * Données détaillées pour Admin
     */
    private function getAdminData(): array
    {
        return [
            'recent_deplacements' => $this->getRecentDeplacements(10),
            'recent_notes' => $this->getRecentNotes(10),
            'users_actifs' => $this->getActiveUsers(),
            'alertes' => $this->getAlertes()
        ];
    }

    /**
     * Statistiques pour Manager
     */
    private function getManagerStatistics(int $managerId): array
    {
        return [
            'equipe' => [
                'total_membres' => $this->userDAO->countTeamMembers($managerId),
                'membres' => $this->getTeamMembersList($managerId)
            ],
            'deplacements_equipe' => [
                'en_cours' => $this->countTeamDeplacementsByStatus($managerId, 'en_cours'),
                'this_month' => $this->countTeamDeplacementsThisMonth($managerId)
            ],
            'notes_en_attente' => [
                'total' => $this->noteFraisDAO->countByStatusForTeam($managerId, 'soumis'),
                'montant_total' => $this->getTotalPendingAmount($managerId),
                'details' => $this->getPendingNotesDetails($managerId)
            ],
            'performance' => [
                'taux_validation' => $this->getValidationRate($managerId),
                'delai_moyen_validation' => $this->getAverageValidationDelay($managerId)
            ]
        ];
    }

    /**
     * Données détaillées pour Manager
     */
    private function getManagerData(int $managerId): array
    {
        return [
            'notes_a_valider' => $this->getNotesToValidate($managerId),
            'recent_validations' => $this->getRecentValidations($managerId),
            'equipe_performance' => $this->getTeamPerformance($managerId)
        ];
    }

    // ========================================
    // MÉTHODES UTILITAIRES
    // ========================================

    private function countUsersByRole(string $role): int
    {
        $sql = "SELECT COUNT(*) FROM users WHERE role = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$role]);
        return (int) $stmt->fetchColumn();
    }

    private function countDeplacementsByMonth(string $month): int
    {
        $sql = "SELECT COUNT(*) FROM deplacements WHERE DATE_FORMAT(date_depart, '%Y-%m') = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$month]);
        return (int) $stmt->fetchColumn();
    }

    private function getDepensesByCategory(): array
    {
        $sql = "SELECT c.type, SUM(df.montant_total) as total
                FROM details_frais df
                JOIN categories_frais c ON df.categorie_id = c.id
                GROUP BY c.type
                ORDER BY total DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getRecentDeplacements(int $limit): array
    {
        $sql = "SELECT d.*, u.nom as user_nom
                FROM deplacements d
                JOIN users u ON d.user_id = u.id
                ORDER BY d.created_at DESC
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getRecentNotes(int $limit): array
    {
        $sql = "SELECT nf.*, u.nom as user_nom, d.destination
                FROM notes_frais nf
                JOIN users u ON nf.user_id = u.id
                JOIN deplacements d ON nf.deplacement_id = d.id
                ORDER BY nf.created_at DESC
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getActiveUsers(): array
    {
        $sql = "SELECT u.*, COUNT(d.id) as nb_deplacements
                FROM users u
                LEFT JOIN deplacements d ON u.id = d.user_id
                WHERE d.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY u.id
                ORDER BY nb_deplacements DESC
                LIMIT 10";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getAlertes(): array
    {
        $alertes = [];

        // Notes en retard de validation (> 5 jours)
        $sql = "SELECT COUNT(*) FROM notes_frais 
                WHERE statut = 'soumis' 
                AND created_at < DATE_SUB(NOW(), INTERVAL 5 DAY)";
        $stmt = $this->pdo->query($sql);
        $retard = (int) $stmt->fetchColumn();

        if ($retard > 0) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "$retard note(s) en retard de validation (> 5 jours)"
            ];
        }

        // Budget dépassé
        $sql = "SELECT SUM(montant_total) FROM notes_frais 
                WHERE statut = 'approuve' 
                AND MONTH(created_at) = MONTH(NOW())";
        $stmt = $this->pdo->query($sql);
        $montantMois = (float) $stmt->fetchColumn();

        if ($montantMois > 50000) {
            $alertes[] = [
                'type' => 'danger',
                'message' => "Budget mensuel dépassé : " . number_format($montantMois, 2) . " DH"
            ];
        }

        return $alertes;
    }

    private function getTeamMembersList(int $managerId): array
    {
        $sql = "SELECT id, nom, email FROM users WHERE manager_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$managerId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function countTeamDeplacementsByStatus(int $managerId, string $status): int
    {
        $sql = "SELECT COUNT(*) FROM deplacements d
                JOIN users u ON d.user_id = u.id
                WHERE u.manager_id = ? AND d.statut = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$managerId, $status]);
        return (int) $stmt->fetchColumn();
    }

    private function countTeamDeplacementsThisMonth(int $managerId): int
    {
        $sql = "SELECT COUNT(*) FROM deplacements d
                JOIN users u ON d.user_id = u.id
                WHERE u.manager_id = ? 
                AND MONTH(d.date_depart) = MONTH(NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$managerId]);
        return (int) $stmt->fetchColumn();
    }

    private function getTotalPendingAmount(int $managerId): float
    {
        $sql = "SELECT SUM(nf.montant_total) FROM notes_frais nf
                JOIN users u ON nf.user_id = u.id
                WHERE u.manager_id = ? AND nf.statut = 'soumis'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$managerId]);
        return (float) $stmt->fetchColumn();
    }

    private function getPendingNotesDetails(int $managerId): array
    {
        $sql = "SELECT nf.*, u.nom as user_nom, d.destination
                FROM notes_frais nf
                JOIN users u ON nf.user_id = u.id
                JOIN deplacements d ON nf.deplacement_id = d.id
                WHERE u.manager_id = ? AND nf.statut = 'soumis'
                ORDER BY nf.created_at ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$managerId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getValidationRate(int $managerId): float
    {
        $sql = "SELECT 
                    COUNT(CASE WHEN statut = 'approuve' THEN 1 END) * 100.0 / COUNT(*) as taux
                FROM notes_frais nf
                JOIN users u ON nf.user_id = u.id
                WHERE u.manager_id = ? AND statut IN ('approuve', 'refuse')";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$managerId]);
        return (float) $stmt->fetchColumn();
    }

    private function getAverageValidationDelay(int $managerId): float
    {
        $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, nf.created_at, nf.updated_at)) / 24 as delai_jours
                FROM notes_frais nf
                JOIN users u ON nf.user_id = u.id
                WHERE u.manager_id = ? AND statut IN ('approuve', 'refuse')";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$managerId]);
        return round((float) $stmt->fetchColumn(), 1);
    }

    private function getNotesToValidate(int $managerId): array
    {
        return $this->getPendingNotesDetails($managerId);
    }

    private function getRecentValidations(int $managerId): array
    {
        $sql = "SELECT nf.*, u.nom as user_nom, d.destination
                FROM notes_frais nf
                JOIN users u ON nf.user_id = u.id
                JOIN deplacements d ON nf.deplacement_id = d.id
                WHERE u.manager_id = ? AND nf.statut IN ('approuve', 'refuse')
                ORDER BY nf.updated_at DESC
                LIMIT 10";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$managerId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getTeamPerformance(int $managerId): array
    {
        $sql = "SELECT 
                    u.nom,
                    COUNT(d.id) as nb_deplacements,
                    COUNT(nf.id) as nb_notes,
                    SUM(nf.montant_total) as total_depenses
                FROM users u
                LEFT JOIN deplacements d ON u.id = d.user_id
                LEFT JOIN notes_frais nf ON d.id = nf.deplacement_id
                WHERE u.manager_id = ?
                GROUP BY u.id
                ORDER BY total_depenses DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$managerId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Convertit le contexte en prompt formaté pour l'IA
     */
    public function contextToPrompt(array $context): string
    {
        $json = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        return "DONNÉES TEMPS RÉEL DE LA BASE DE DONNÉES (JSON): Tu as accès à TOUTES ces données en temps réel. Utilise-les pour répondre de manière précise et détaillée.Les chiffres que tu donnes DOIVENT correspondre exactement à ces données.Exemple: Si on te demande 'combien de notes en attente', regarde dans statistics.notes_frais.soumis";
    }
}