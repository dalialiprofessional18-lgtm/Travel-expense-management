<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DAO\CalendrierDAO;
use App\Models\DAO\UserDAO;
use App\Models\DAO\NotificationDAO;
use App\Helpers\Auth;

class CalendrierController extends BaseController
{
    private CalendrierDAO $calendrierDAO;
    private UserDAO $userDAO;
    private NotificationDAO $notifDAO;

    public function __construct()
    {
        $this->calendrierDAO = new CalendrierDAO();
        $this->userDAO = new UserDAO();
        $this->notifDAO = new NotificationDAO();
    }

    /**
     * Afficher la page du calendrier
     */
    public function index()
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        $userId = $user->getId();
        $role = $user->getRole();
        
        // Récupérer les données utilisateur pour le header
        $user1 = $this->userDAO->findByIdForProfile($userId);
        $avatarUrl = $user1->getAvatarUrl();
        $coverUrl = $user1->getCoverUrl();
        
        // Récupérer le mois et l'année (par défaut : mois actuel)
        $mois = isset($_GET['mois']) ? intval($_GET['mois']) : date('m');
        $annee = isset($_GET['annee']) ? intval($_GET['annee']) : date('Y');
        
        // Récupérer tous les déplacements
        $deplacements = $this->calendrierDAO->getTousLesDeplacements($userId, $role);
        
        // Récupérer les statistiques du mois
        $statistiques = $this->calendrierDAO->getStatistiquesMois($mois, $annee, $userId, $role);
        
        // Récupérer les prochains déplacements
        $prochains_deplacements = $this->calendrierDAO->getProchainsDeplacements($userId, $role, 5);
        
        // Récupérer les notifications
        $notifications = $this->notifDAO->findByUser($userId, 10);
        
        // Formater les données pour le JavaScript
        $data = [
            'deplacements' => $this->formaterDeplacements($deplacements),
            'statistiques' => $statistiques,
            'prochains_deplacements' => $prochains_deplacements,
            'notifications' => $notifications,
            'mois_actuel' => $mois,
            'annee_actuelle' => $annee,
            'user_role' => $role,
            'userId' => $userId,
            'avatarUrl' => $avatarUrl,
            'coverUrl' => $coverUrl
        ];
        
        // Charger la vue
        $this->view('calendrier/index', $data);
    }

    /**
     * API : Récupérer les déplacements en JSON
     */
    public function getDeplacementsJSON()
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        $userId = $user->getId();
        $role = $user->getRole();
        
        $deplacements = $this->calendrierDAO->getTousLesDeplacements($userId, $role);
        $deplacements_formated = $this->formaterDeplacements($deplacements);
        
        header('Content-Type: application/json');
        echo json_encode($deplacements_formated, JSON_HEX_TAG | JSON_HEX_AMP);
        exit;
    }

    /**
     * API : Récupérer les déplacements d'un jour
     */
    public function getDeplacementsJour()
    {
        Auth::requireAuth();
        
        $date = $_GET['date'] ?? null;
        
        if (!$date) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Date manquante']);
            exit;
        }
        
        $user = Auth::user();
        $userId = $user->getId();
        $role = $user->getRole();
        
        $deplacements = $this->calendrierDAO->getDeplacementsParJour($date, $userId, $role);
        
        header('Content-Type: application/json');
        echo json_encode($deplacements, JSON_HEX_TAG | JSON_HEX_AMP);
        exit;
    }

    /**
     * Formater les déplacements pour le calendrier
     */
    private function formaterDeplacements($deplacements)
    {
        $formatted = [];
        
        foreach ($deplacements as $dep) {
            // Déterminer le type basé sur le titre ou créer une logique
            $type = $this->determinerType($dep['titre'], $dep['lieu_destination']);
            
            // Déterminer la couleur selon le statut
            $couleur = $this->getCouleurStatut($dep['note_statut'] ?? 'brouillon');
            
            // Calculer la durée en jours
            $date_depart = new \DateTime($dep['date_depart']);
            $date_retour = new \DateTime($dep['date_retour']);
            $duree = $date_retour->diff($date_depart)->days + 1;
            
            $formatted[] = [
                'id' => $dep['id'],
                'title' => $dep['titre'],
                'employe' => $dep['employe_nom'],
                'employe_email' => $dep['employe_email'],
                'avatar' => $dep['avatar_path'] ?? null,
                'type' => $type,
                'lieu_depart' => $dep['lieu_depart'] ?? '',
                'lieu_destination' => $dep['lieu_destination'],
                'date_depart' => $dep['date_depart'],
                'date_retour' => $dep['date_retour'],
                'duree_jours' => $duree,
                'objet' => $dep['objet'] ?? '',
                'statut' => $dep['statut_display'] ?? 'brouillon',
                'note_statut' => $dep['note_statut'] ?? 'brouillon',
                'montant' => floatval($dep['montant_total'] ?? 0),
                'couleur' => $couleur,
                'note_id' => $dep['note_id'] ?? null,
                // Formatage des dates pour affichage
                'date_depart_format' => date('d/m/Y', strtotime($dep['date_depart'])),
                'date_retour_format' => date('d/m/Y', strtotime($dep['date_retour'])),
            ];
        }
        
        return $formatted;
    }

    /**
     * Déterminer le type de déplacement basé sur les données
     */
    private function determinerType($titre, $destination)
    {
        $titre_lower = strtolower($titre);
        $destination_lower = strtolower($destination ?? '');
        
        // Logique pour déterminer le type
        if (strpos($titre_lower, 'formation') !== false) {
            return 'formation';
        } elseif (strpos($titre_lower, 'réunion') !== false || strpos($titre_lower, 'reunion') !== false) {
            return 'reunion';
        } elseif (strpos($titre_lower, 'visite') !== false) {
            return 'visite';
        } elseif (
            strpos($destination_lower, 'france') === false && 
            strpos($destination_lower, 'paris') === false &&
            !empty($destination_lower)
        ) {
            return 'international';
        } else {
            return 'mission';
        }
    }

    /**
     * Obtenir la couleur selon le statut
     */
    private function getCouleurStatut($statut)
    {
        $couleurs = [
            'brouillon' => '#6b7280',      // Gris
            'soumis' => '#f59e0b',         // Orange
            'en_attente' => '#f59e0b',     // Orange
            'valide_manager' => '#3b82f6', // Bleu
            'valide' => '#10b981',         // Vert
            'rejetee_manager' => '#ef4444', // Rouge
            'refuse' => '#ef4444',         // Rouge
            'approuve' => '#10b981',       // Vert
            'termine' => '#10b981',        // Vert
            'rejetee_admin' => '#dc2626',  // Rouge foncé
        ];
        
        return $couleurs[$statut] ?? '#6b7280';
    }

    /**
     * Exporter le calendrier en PDF
     */
    public function exporterPDF()
    {
        Auth::requireAuth();
        
        // TODO: Implémenter l'export PDF avec TCPDF ou FPDF
        $_SESSION['info'] = "Fonctionnalité d'export PDF en développement";
        return $this->redirect('/calendrier');
    }
}