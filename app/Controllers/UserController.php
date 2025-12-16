<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DAO\UserDAO;
use App\Models\Entities\User;
use App\Helpers\Auth;
use App\Models\DAO\DetailsFraisDAO;
use App\Models\DAO\MessagerieDAO;
use App\Models\DAO\NoteFraisDAO;
use App\Models\DAO\CategorieFraisDAO;
use App\Models\DAO\DeplacementDAO;
use App\Services\EmailService; // ✅ Ajouter
use Exception;
use App\Models\Entities\DetailsFrais;
use App\Models\DAO\NotificationDAO;

class UserController extends BaseController {
    private UserDAO $userDAO;
    private NotificationDAO $notifDAO;
    private MessagerieDAO $messagerieDAO;
    private EmailService $emailService; // ✅ Ajouter

    private DetailsFraisDAO $detailsDAO;
    private DeplacementDAO $deplacementDAO;
    private NoteFraisDAO $noteDAO;
    private CategorieFraisDAO $categorieDAO;

    public function __construct() {
        $this->emailService = new EmailService(); // ✅ Ajouter
        $this->notifDAO = new NotificationDAO();
        $this->messagerieDAO = new MessagerieDAO();
        $this->userDAO = new UserDAO();
        $this->detailsDAO = new DetailsFraisDAO();
        $this->deplacementDAO = new DeplacementDAO();
        $this->noteDAO    = new NoteFraisDAO();
        $this->categorieDAO = new CategorieFraisDAO();
    }

    // === DASHBOARD INTELLIGENT : redirige selon le rôle ===

    public function dashboard()
    {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = $user->getId();
        $role = $user->getRole();
        $user1 = $this->userDAO->findByIdForProfile($userId);
        
        $coverUrl = $user1->getCoverUrl();
        $avatarUrl = $user1->getAvatarUrl();

        $data = [];

        // ========================================
        // DASHBOARD ADMIN
        // ========================================
        if ($role === 'admin') {
            // Récupérer TOUTES les notes avec détails complets
            $allNotes = $this->noteDAO->findAllWithDetails(100, 0);
            
            // Calculer les statistiques en temps réel
            $totalMontant = 0;
            $notesByStatus = [
                'soumis' => 0,
                'valide_manager' => 0,
                'rejetee_manager' => 0,
                'approuve' => 0,
                'rejetee_admin' => 0
            ];
            
            foreach ($allNotes as $note) {
                $totalMontant += $note['montant_total'];
                $status = $note['statut'];
                if (isset($notesByStatus[$status])) {
                    $notesByStatus[$status]++;
                }
            }
            
            $data = [
                'totalUsers'           => $this->userDAO->countAll(),
                'totalDeplacements'    => $this->deplacementDAO->countAll(),
                'pendingDeplacements'  => $notesByStatus['soumis'],
                'approvedDeplacements' => $notesByStatus['approuve'],
                'userId'               => $userId,
                'avatarUrl'            => $avatarUrl,
                'coverUrl'             => $coverUrl,
                'notifications'        => $this->notifDAO->findByUser($userId, 10),
                
                // Données pour le tableau
                'allNotes'             => $allNotes,
                'totalMontant'         => $totalMontant,
                'notesByStatus'        => $notesByStatus,
            ];
            
            return $this->view('dashboard/admin', $data);
        }

        // ========================================
        // DASHBOARD MANAGER
        // ========================================
        if ($role === 'manager') {
            $data = [
                'teamCount'            => $this->userDAO->countUnderManager($userId),
                'pendingTeam'          => $this->noteDAO->countByStatusForTeam($userId, 'soumis'),
                'approvedTeam'         => $this->noteDAO->countByStatusForTeam($userId, 'approuve'),
                'recentTeamRequests'   => $this->deplacementDAO->findRecentForTeam($userId, 20),
                'userId'               => $userId,
                'avatarUrl'            => $avatarUrl,
                'coverUrl'             => $coverUrl,
                'notifications'        => $this->notifDAO->findByUser($userId, 10)
            ];
            
            return $this->view('dashboard/manager', $data);
        }

        // ========================================
        // DASHBOARD EMPLOYÉ
        // ========================================
        $data = [
            'total'         => $this->noteDAO->countByStatusAndUser($userId),
            'pending'       => $this->noteDAO->countByStatusAndUser($userId, 'soumis'),
            'approved'      => $this->noteDAO->countByStatusAndUser($userId, 'approuve'),
            'rejecter'      => $this->noteDAO->countByStatusAndUser($userId, 'rejetee_manager'),
            'recent'        => $this->deplacementDAO->findByUserId($userId, 20),
            'userId'        => $userId,
            'avatarUrl'     => $avatarUrl,
            'coverUrl'      => $coverUrl,
            'notifications' => $this->notifDAO->findByUser($userId, 10)
        ];
        
        return $this->view('dashboard/employee', $data);
    }
 
public function demandes()
{
    Auth::requireAuth();
    Auth::requireRole('admin');

    $user = Auth::user();
    $userId = $user->getId();

    // Pagination
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = 15;
    $offset = ($page - 1) * $perPage;

    // Filtres
    $statusFilter = $_GET['status'] ?? '';
    $searchTerm = $_GET['search'] ?? '';

    // Récupérer les notes avec pagination et filtres
    $notes = $this->noteDAO->findAllWithDetailsAndFilters(
        $perPage, 
        $offset, 
        $statusFilter, 
        $searchTerm
    );

    // Compter le total pour la pagination
    $totalNotes = $this->noteDAO->countAllWithFilters($statusFilter, $searchTerm);
    $totalPages = ceil($totalNotes / $perPage);

    // Statistiques
    $notesByStatus = [
        'valide_manager' => $this->noteDAO->countByStatus('valide_manager'),
        'en_cours_admin' => $this->noteDAO->countByStatus('en_cours_admin'),
        'approuve' => $this->noteDAO->countByStatus('approuve'),
        'rejetee_admin' => $this->noteDAO->countByStatus('rejetee_admin'),
    ];

    $totalMontant = $this->noteDAO->getTotalMontant();

    // Données utilisateur
    $user1 = $this->userDAO->findByIdForProfile($userId);

    $data = [
        'userId' => $userId,
        'avatarUrl' => $user1->getAvatarUrl(),
        'coverUrl' => $user1->getCoverUrl(),
        'notifications' => $this->notifDAO->findByUser($userId, 10),
        
        // Données demandes
        'notes' => $notes,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalNotes' => $totalNotes,
        'perPage' => $perPage,
        'statusFilter' => $statusFilter,
        'searchTerm' => $searchTerm,
        'notesByStatus' => $notesByStatus,
        'totalMontant' => $totalMontant,
    ];

    return $this->view('deplacement/demandes', $data);
}
/**
 * Afficher le formulaire d'édition d'un utilisateur
 */
public function edit($id)
{
    Auth::requireAuth();
    Auth::requireRole('admin');

    $currentUser = Auth::user();
    $userId = $currentUser->getId();

    // Récupérer l'utilisateur à modifier
    $userToEdit = $this->userDAO->findByIdForProfile($id);
    
    if (!$userToEdit) {
        $_SESSION['error'] = "Utilisateur introuvable";
        return header('Location: /users');
    }

    // Récupérer tous les managers pour le select
    $managers = $this->userDAO->findByRole(['admin', 'manager']);
    
    // Récupérer les infos du user connecté pour le header
    $user1 = $this->userDAO->findByIdForProfile($userId);

    $data = [
        'userId' => $userId,
        'avatarUrl' => $user1->getAvatarUrl(),
        'coverUrl' => $user1->getCoverUrl(),
        'notifications' => $this->notifDAO->findByUser($userId, 10),
        'userToEdit' => $userToEdit,
        'managers' => $managers,
    ];

    return $this->view('user/edit', $data);
}

/**
 * Traiter la mise à jour d'un utilisateur
 */
public function updateUser($id)
{
    Auth::requireAuth();
    Auth::requireRole('admin');

    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token CSRF invalide";
        return header("Location: /users/$id/edit");
    }

    // Récupérer l'utilisateur
    $user = $this->userDAO->findByIdForProfile($id);
    
    if (!$user) {
        $_SESSION['error'] = "Utilisateur introuvable";
        return header('Location: /users');
    }

    // Validation
    $errors = [];
    
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'employe';
    $managerId = !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null;
    $jobTitle = trim($_POST['job_title'] ?? '');
    $experienceDetails = trim($_POST['experience_details'] ?? '');
    
    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide";
    }
    
    // Vérifier si l'email existe déjà (sauf pour cet utilisateur)
    $existingUser = $this->userDAO->findByEmail($email);
    if ($existingUser && $existingUser->getId() != $id) {
        $errors[] = "Cet email est déjà utilisé";
    }
    
    if (!in_array($role, ['admin', 'manager', 'employe'])) {
        $errors[] = "Rôle invalide";
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
        return header("Location: /users/$id/edit");
    }

    // Mettre à jour les informations
    $user->setNom($nom);
    $user->setEmail($email);
    $user->setRole($role);
    $user->setManagerId($managerId);
    $user->setJobTitle($jobTitle);
    $user->setExperienceDetails($experienceDetails);
    
    // Si un nouveau mot de passe est fourni
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if ($password !== $confirmPassword) {
            $_SESSION['error'] = "Les mots de passe ne correspondent pas";
            return header("Location: /users/$id/edit");
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error'] = "Le mot de passe doit contenir au moins 6 caractères";
            return header("Location: /users/$id/edit");
        }
        
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
    }

    // Sauvegarder
    if ($this->userDAO->update($user)) {
        $_SESSION['success'] = "Utilisateur mis à jour avec succès";
        return header('Location: /users');
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour";
        return header("Location: /users/$id/edit");
    }
}
// ========================================
// 1. ROUTES À AJOUTER DANS routes.php
// ========================================

// Paramètres utilisateur

// ========================================
// 2. MÉTHODES À AJOUTER DANS UserController.php
// ========================================

/**
 * Afficher la page des paramètres
 */


/**
 * Mettre à jour le profil
 */
public function updateProfile() {
    Auth::requireAuth();
    
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token CSRF invalide";
        return $this->redirect('/settings');
    }
    
    $user = $this->userDAO->findById(Auth::user()->getId());
    
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $jobTitle = trim($_POST['job_title'] ?? '');
    $experienceDetails = trim($_POST['experience_details'] ?? '');
    
    // Validation
    if (empty($nom) || empty($email)) {
        $_SESSION['error'] = 'Le nom et l\'email sont obligatoires';
        return $this->redirect('/settings');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Email invalide';
        return $this->redirect('/settings');
    }
    
    // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
    $existingUser = $this->userDAO->findByEmail($email);
    if ($existingUser && $existingUser->getId() != $user->getId()) {
        $_SESSION['error'] = 'Cet email est déjà utilisé';
        return $this->redirect('/settings');
    }
    
    // Mettre à jour
    $user->setNom($nom);
    $user->setEmail($email);
    $user->setJobTitle($jobTitle);
    $user->setExperienceDetails($experienceDetails);
    
    if ($this->userDAO->updateProfileOnly($user)) {
        $_SESSION['success'] = 'Profil mis à jour avec succès';
    } else {
        $_SESSION['error'] = 'Erreur lors de la mise à jour';
    }
    
    return $this->redirect('/settings');
}

/**
 * Changer le mot de passe
 */
public function updatePassword() {
    Auth::requireAuth();
    
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token CSRF invalide";
        return $this->redirect('/settings');
    }
    
    $user = $this->userDAO->findById(Auth::user()->getId());
    
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['error'] = 'Tous les champs sont obligatoires';
        return $this->redirect('/settings');
    }
    
    // Vérifier le mot de passe actuel
    if (!password_verify($currentPassword, $user->getPassword())) {
        $_SESSION['error'] = 'Le mot de passe actuel est incorrect';
        return $this->redirect('/settings');
    }
    
    // Vérifier la correspondance
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = 'Les mots de passe ne correspondent pas';
        return $this->redirect('/settings');
    }
    
    // Vérifier la longueur
    if (strlen($newPassword) < 6) {
        $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères';
        return $this->redirect('/settings');
    }
    
    // Mettre à jour
    $user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
    
    if ($this->userDAO->update($user)) {
        $_SESSION['success'] = 'Mot de passe mis à jour avec succès';
    } else {
        $_SESSION['error'] = 'Erreur lors de la mise à jour';
    }
    
    return $this->redirect('/settings');
}

/**
 * Mettre à jour les préférences d'apparence
 */
public function updateAppearance() {
    Auth::requireAuth();
    
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token CSRF invalide";
        return $this->redirect('/settings');
    }
    
    $theme = $_POST['theme'] ?? 'light';
    $language = $_POST['language'] ?? 'fr';
    
    // Validation
    if (!in_array($theme, ['light', 'dark', 'auto'])) {
        $theme = 'light';
    }
    
    if (!in_array($language, ['fr', 'en', 'ar'])) {
        $language = 'fr';
    }
    
    // Sauvegarder dans la session
    $_SESSION['theme'] = $theme;
    $_SESSION['language'] = $language;
    
    // Optionnel : sauvegarder en base de données
    // $this->userDAO->updatePreferences(Auth::user()->getId(), $theme, $language);
    
    $_SESSION['success'] = 'Préférences d\'apparence mises à jour';
    return $this->redirect('/settings');
}

/**
 * Mettre à jour les préférences de notifications
 */
public function updateNotifications() {
    Auth::requireAuth();
    
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token CSRF invalide";
        return $this->redirect('/settings');
    }
    
    $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
    $noteValidated = isset($_POST['note_validated']) ? 1 : 0;
    $noteRejected = isset($_POST['note_rejected']) ? 1 : 0;
    $newTrip = isset($_POST['new_trip']) ? 1 : 0;
    
    // Sauvegarder dans la session ou en BDD
    $_SESSION['notif_prefs'] = [
        'email_notifications' => $emailNotifications,
        'note_validated' => $noteValidated,
        'note_rejected' => $noteRejected,
        'new_trip' => $newTrip
    ];
    
    // Optionnel : sauvegarder en base de données
    // $this->userDAO->updateNotificationPreferences(Auth::user()->getId(), [...]);
    
    $_SESSION['success'] = 'Préférences de notifications mises à jour';
    return $this->redirect('/settings');
}

/**
 * Upload avatar
 */
public function uploadAvatar() {
    Auth::requireAuth();
    $user = Auth::user();
    
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Erreur lors du téléchargement de l\'image';
        return $this->redirect('/settings');
    }
    
    $file = $_FILES['avatar'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        $_SESSION['error'] = 'Format de fichier non autorisé';
        return $this->redirect('/settings');
    }
    
    if ($file['size'] > 2 * 1024 * 1024) { // 2MB max
        $_SESSION['error'] = 'Le fichier est trop volumineux (max 2MB)';
        return $this->redirect('/settings');
    }
    
    // Upload sur ImgBB
    $apiKey = '9ebc3d3ad5519c20a70e04f221d04c53';
    $url = 'https://api.imgbb.com/1/upload';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'key' => $apiKey,
        'image' => base64_encode(file_get_contents($file['tmp_name'])),
        'name' => $user->getId() . '_avatar_' . time()
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (!$result || !$result['success']) {
        $_SESSION['error'] = 'Erreur lors de l\'upload sur ImgBB';
        return $this->redirect('/settings');
    }
    
    $imageUrl = $result['data']['url'];
    
    // Sauvegarder en base de données
    if ($this->userDAO->updateAvatar($user->getId(), $imageUrl)) {
        $_SESSION['success'] = 'Photo de profil mise à jour';
    } else {
        $_SESSION['error'] = 'Erreur lors de la sauvegarde';
    }
    
    return $this->redirect('/settings');
}

/**
 * Supprimer l'avatar
 */
public function deleteAvatar() {
    Auth::requireAuth();
    $user = Auth::user();
    
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token CSRF invalide";
        return $this->redirect('/settings');
    }
    
    if ($this->userDAO->updateAvatar($user->getId(), null)) {
        $_SESSION['success'] = 'Photo de profil supprimée';
    } else {
        $_SESSION['error'] = 'Erreur lors de la suppression';
    }
    
    return $this->redirect('/settings');
}

/**
 * Upload cover
 */
public function uploadCover() {
    Auth::requireAuth();
    $user = Auth::user();
    
    if (!isset($_FILES['cover']) || $_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Erreur lors du téléchargement de l\'image';
        return $this->redirect('/settings');
    }
    
    $file = $_FILES['cover'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        $_SESSION['error'] = 'Format de fichier non autorisé';
        return $this->redirect('/settings');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
        $_SESSION['error'] = 'Le fichier est trop volumineux (max 5MB)';
        return $this->redirect('/settings');
    }
    
    // Upload sur ImgBB
    $apiKey = '9ebc3d3ad5519c20a70e04f221d04c53';
    $url = 'https://api.imgbb.com/1/upload';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'key' => $apiKey,
        'image' => base64_encode(file_get_contents($file['tmp_name'])),
        'name' => $user->getId() . '_cover_' . time()
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (!$result || !$result['success']) {
        $_SESSION['error'] = 'Erreur lors de l\'upload sur ImgBB';
        return $this->redirect('/settings');
    }
    
    $imageUrl = $result['data']['url'];
    
    // Sauvegarder en base de données
    if ($this->userDAO->updateCover($user->getId(), $imageUrl)) {
        $_SESSION['success'] = 'Photo de couverture mise à jour';
    } else {
        $_SESSION['error'] = 'Erreur lors de la sauvegarde';
    }
    
    return $this->redirect('/settings');
}

/**
 * Supprimer la cover
 */
public function deleteCover() {
    Auth::requireAuth();
    $user = Auth::user();
    
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token CSRF invalide";
        return $this->redirect('/settings');
    }
    
    if ($this->userDAO->updateCover($user->getId())) {
        $_SESSION['success'] = 'Photo de couverture supprimée';
    } else {
        $_SESSION['error'] = 'Erreur lors de la suppression';
    }
    
    return $this->redirect('/settings');
}
    public function settings() {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = $user->getId();

        $userById = $this->userDAO->findById( $user->getId() );
        $user1 = $this->userDAO->findByIdForProfile( $user->getId() );
        $coverUrl = $user1->getCoverUrl();
        $avatarUrl = $user1->getAvatarUrl();
        $this->view( 'user/settings', [ 'userId'   => $userId, 'user' =>    $userById, 'avatarUrl' => $avatarUrl, 'coverUrl' => $coverUrl ,
        'notifications'=>        $this->notifDAO->findByUser( $userId ) ] );
    }

    public function settingsUpdate() {
        Auth::requireAuth();
        $user = $this->userDAO->findById( Auth::user()->getId() );

        $nom = trim( $_POST[ 'nom' ] ?? '' );
        $experience_details = trim( $_POST[ 'experience_details' ] ?? '' );
        $job_title = $_POST[ 'job_title' ] ?? '';

        if ( !$nom || !$experience_details ) {
            $_SESSION[ 'profile_error' ] = 'Nom et email obligatoires';
            return $this->redirect( '/settings' );
        }

        if ( $job_title !== '' ) {
            $_SESSION[ 'profile_error' ] = 'Les mots de passe ne correspondent pas';
            return $this->redirect( '/settings' );
        }

        $user->setNom( $nom );
        $user->setExperienceDetails( $experience_details );
        if ( $job_title !== '' ) {
            $user->setJobTitle( $job_title );
        }

        $this->userDAO->update( $user );

        $_SESSION[ 'profile_success' ] = 'Profil mis à jour avec succès !';
        return $this->redirect( '/settings' );
    }

    public function profile() {
        Auth::requireAuth();
        $user1 = Auth::user();
        $userId = $user1->getId();

        $user = $this->userDAO->findByIdForProfile( $user1->getId() );

        $userById = $this->userDAO->findById( $user1->getId() );

        // لتحديث بيانات المستخدم من قاعدة البيانات
        $coverUrl = $user->getCoverUrl();
        $avatarUrl = $user->getAvatarUrl();
        $email = $user->getEmail();
        $role = $user->getRole();
        $nom = $user->getNom();
        $experienceDetails = $userById->getExperienceDetails();
        $jobTitle = $userById->getJobTitle();
        $this->view( 'user/profile', [ 'userId'   => $userId, 'experienceDetails' => $experienceDetails, 'jobTitle' => $jobTitle, 'avatarUrl' => $avatarUrl, 'coverUrl' => $coverUrl,  'email' => $email, 'role' => $role,  'nom' => $nom ,
        'notifications'=>        $this->notifDAO->findByUser( $userId ) ] );
    }

    /**
    * Voir les détails d'une note de frais (Admin)
     */
     public function viewNote($noteId)
{
    Auth::requireAuth();
    $user1 = Auth::user();
    $userId = $user1->getId();
    
    $note = $this->noteDAO->findById($noteId);
    if (!$note) {
        $_SESSION['error'] = 'Note de frais introuvable';
        return $this->redirect('/admin');
    }

    $deplacement = $this->deplacementDAO->findById($note->getDeplacementId());
    $employe = $this->userDAO->findUserByDeplacementId($note->getDeplacementId());
    $lignes = $this->detailsDAO->findByNoteId($note->getId());

    // Statistiques de l'employé
    $stats = [
        'mois' => $this->noteDAO->countByUserThisMonth($employe->getId()),
        'approuvees' => $this->noteDAO->countApprovedByUser($employe->getId()),
        'total_montant' => $this->noteDAO->calculateTotalMontant($employe->getId())
    ];
    
    $user = Auth::user();
    $userId = $user->getId();
    $user1 = $this->userDAO->findByIdForProfile($user->getId());
    $coverUrl = $user1->getCoverUrl();
    $avatarUrl = $user1->getAvatarUrl();
    
    // ✅ Récupérer ou créer la conversation entre l'admin et l'employé
    $conversationId = $this->messagerieDAO->getOrCreateConversation($userId, $employe->getId());
    
    $this->view('admin/voir', [
        'deplacement' => $deplacement,
        'note' => $note,
        'userId' => $userId,
        'avatarUrl' => $avatarUrl,
        'coverUrl' => $coverUrl,
        'lignes' => $lignes,
        'employe' => $employe,
        'stats' => $stats,
        'conversationId' => $conversationId,  // ✅ ID de la conversation
        'notifications' => $this->notifDAO->findByUser($userId)
    ]);
}

/**
 * Approuver une note directement (admin)
 */
public function approveNote(int $id)
{
    Auth::requireRole('admin');
    
    $commentaire = $_POST['commentaire'] ?? 'Approuvé par l\'administrateur';
    $montantRembourser = floatval($_POST['montant_rembourser'] ?? 0); // ✅ Récupérer le montant
    
    try {
        // ✅ Récupérer la note et l'employé
        $note = $this->noteDAO->findById($id);
        if (!$note) {
            $_SESSION['error'] = 'Note de frais introuvable';
            return $this->redirect('/admin');
        }
        
        $deplacement = $this->deplacementDAO->findById($note->getDeplacementId());
        if (!$deplacement) {
            $_SESSION['error'] = 'Déplacement introuvable';
            return $this->redirect('/admin');
        }
        
        $employe = $this->userDAO->findUserByDeplacementId($note->getDeplacementId());
        if (!$employe) {
            $_SESSION['error'] = 'Employé introuvable';
            return $this->redirect('/admin');
        }
        
        // ✅ Approuver la note AVEC le montant à rembourser
        $success = $this->noteDAO->adminApprove($id, $commentaire, $montantRembourser);
        
        if ($success) {
            // ✅ Envoyer l'email de notification
            $destination = method_exists($deplacement, 'getLieu') 
                ? $deplacement->getLieu() 
                : "jjjj";
            
            $emailSent = $this->emailService->sendNoteApprovedEmail(
                $employe->getEmail(),
                $employe->getNom(),
                $note->getId(),
                $destination,
                $montantRembourser, // ✅ Utiliser le montant à rembourser au lieu du total
                $commentaire
            );
            
            if ($emailSent) {
                error_log("✅ Email d'approbation envoyé à " . $employe->getEmail());
            } else {
                error_log("❌ Échec envoi email à " . $employe->getEmail());
            }
            
            $_SESSION['success'] = "Note de frais approuvée avec succès. Montant à rembourser : " . 
                                   number_format($montantRembourser, 2, ',', ' ') . " €";
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'approbation';
        }
        
    } catch (Exception $e) {
        error_log("❌ Erreur approveNote: " . $e->getMessage());
        $_SESSION['error'] = 'Erreur lors de l\'approbation: ' . $e->getMessage();
    }
    
    return $this->redirect('/admin');
}

/**
 * Rejeter une note (admin)
 */
public function rejectNote(int $id)
{
    Auth::requireRole('admin');
    
    $motif = $_POST['motif'] ?? null;
    
    if (!$motif || trim($motif) === '') {
        $_SESSION['error'] = 'Le motif du rejet est obligatoire';
        return $this->redirect('/admin');
    }
    
    try {
        // ✅ Récupérer la note et l'employé
        $note = $this->noteDAO->findById($id);
        if (!$note) {
            $_SESSION['error'] = 'Note de frais introuvable';
            return $this->redirect('/admin');
        }
        
        $deplacement = $this->deplacementDAO->findById($note->getDeplacementId());
        if (!$deplacement) {
            $_SESSION['error'] = 'Déplacement introuvable';
            return $this->redirect('/admin');
        }
        
        $employe = $this->userDAO->findUserByDeplacementId($note->getDeplacementId());
        if (!$employe) {
            $_SESSION['error'] = 'Employé introuvable';
            return $this->redirect('/admin');
        }
        
        // Rejeter la note
        $success = $this->noteDAO->adminReject($id, $motif);
        
        if ($success) {
            // ✅ Envoyer l'email de notification
            $destination = method_exists($deplacement, 'getLieu') 
                ? $deplacement->getLieu() 
                : "hjjd";
            
            $emailSent = $this->emailService->sendNoteRejectedEmail(
                $employe->getEmail(),
                $employe->getNom(),
                $note->getId(),
                $destination,
                $note->getMontantTotal(),
                $motif
            );
            
            if ($emailSent) {
                error_log("✅ Email de rejet envoyé à " . $employe->getEmail());
            } else {
                error_log("❌ Échec envoi email à " . $employe->getEmail());
            }
            
            $_SESSION['success'] = 'Note de frais rejetée';
        } else {
            $_SESSION['error'] = 'Erreur lors du rejet';
        }
        
    } catch (Exception $e) {
        error_log("❌ Erreur rejectNote: " . $e->getMessage());
        $_SESSION['error'] = 'Erreur lors du rejet: ' . $e->getMessage();
    }
    
    return $this->redirect('/admin');
}

/**
 * Révoquer une décision de manager
 */
public function revokeDecision(int $id)
{
    Auth::requireRole('admin');
    
    $motif = $_POST['motif'] ?? null;
    
    if (!$motif || trim($motif) === '') {
        $_SESSION['error'] = 'Le motif de la révocation est obligatoire';
        return $this->redirect('/admin');
    }
    
    try {
        // ✅ Récupérer la note et l'employé
        $note = $this->noteDAO->findById($id);
        if (!$note) {
            $_SESSION['error'] = 'Note de frais introuvable';
            return $this->redirect('/admin');
        }
        
        $deplacement = $this->deplacementDAO->findById($note->getDeplacementId());
        if (!$deplacement) {
            $_SESSION['error'] = 'Déplacement introuvable';
            return $this->redirect('/admin');
        }
        
        $employe = $this->userDAO->findUserByDeplacementId($note->getDeplacementId());
        if (!$employe) {
            $_SESSION['error'] = 'Employé introuvable';
            return $this->redirect('/admin');
        }
        
        // Révoquer la décision
        $success = $this->noteDAO->revokeDecision($id, $motif);
        
        if ($success) {
            // ✅ Envoyer l'email de notification
            $destination = method_exists($deplacement, 'getLieu') 
                ? $deplacement->getLieu() 
                : "jjjj";
        
            $emailSent = $this->emailService->sendDecisionRevokedEmail(
                $employe->getEmail(),
                $employe->getNom(),
                $note->getId(),
                $destination,
                $motif
            );
            
            if ($emailSent) {
                error_log("✅ Email de révocation envoyé à " . $employe->getEmail());
            } else {
                error_log("❌ Échec envoi email à " . $employe->getEmail());
            }
            
            $_SESSION['success'] = 'Décision révoquée. La note est de nouveau en attente.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la révocation ou note non éligible';
        }
        
    } catch (Exception $e) {
        error_log("❌ Erreur revokeDecision: " . $e->getMessage());
        $_SESSION['error'] = 'Erreur: ' . $e->getMessage();
    }
    
    return $this->redirect('/admin');
}

/**
 * Action du manager (approuver/rejeter)
 */
public function actionManager($noteId)
{
    Auth::requireRole('manager');
    
    $action = $_POST['action'] ?? '';
    $commentaire = trim($_POST['commentaire'] ?? '');

    if (!in_array($action, ['approuver', 'rejeter'])) {
        return $this->redirect('/dashboard');
    }

    try {
        // ✅ Récupérer la note et l'employé
        $note = $this->noteDAO->findById($noteId);
        
        if (!$note) {
            $_SESSION['error'] = 'Note de frais introuvable';
            return $this->redirect('/manager');
        }
        
        if ($note->getStatut() !== 'soumis') {
            $_SESSION['error'] = 'Cette demande a déjà été traitée';
            return $this->redirect('/manager');
        }

        $deplacement = $this->deplacementDAO->findById($note->getDeplacementId());
        if (!$deplacement) {
            $_SESSION['error'] = 'Déplacement introuvable';
            return $this->redirect('/manager');
        }
        
        $employe = $this->userDAO->findUserByDeplacementId($note->getDeplacementId());
        if (!$employe) {
            $_SESSION['error'] = 'Employé introuvable';
            return $this->redirect('/manager');
        }
        
        $manager = Auth::user();

        // Mettre à jour le statut
        $nouveauStatut = $action === 'approuver' ? 'valide_manager' : 'rejetee_manager';
        $this->noteDAO->updateStatut($noteId, $nouveauStatut, $commentaire);

        // ✅ Envoyer l'email selon l'action
        $destination = method_exists($deplacement, 'getLieu') 
            ? $deplacement->getLieu() 
            : "jjjj";
        
        if ($action === 'approuver') {
            $emailSent = $this->emailService->sendNoteValidatedByManagerEmail(
                $employe->getEmail(),
                $employe->getNom(),
                $manager->getNom(),
                $note->getId(),
                $destination,
                $note->getMontantTotal(),
                $commentaire
            );
        } else {
            $emailSent = $this->emailService->sendNoteRejectedByManagerEmail(
                $employe->getEmail(),
                $employe->getNom(),
                $manager->getNom(),
                $note->getId(),
                $destination,
                $note->getMontantTotal(),
                $commentaire
            );
        }
        
        if ($emailSent) {
            error_log("✅ Email manager envoyé à " . $employe->getEmail());
        } else {
            error_log("❌ Échec envoi email à " . $employe->getEmail());
        }

        $_SESSION['success'] = 'Demande ' . ($action === 'approuver' ? 'approuvée' : 'rejetée') . ' avec succès';
        
    } catch (Exception $e) {
        error_log("❌ Erreur actionManager: " . $e->getMessage());
        $_SESSION['error'] = 'Erreur: ' . $e->getMessage();
    }
    
    return $this->redirect('/manager');
}    public function profileUpdate() {
        Auth::requireAuth();
        $user = Auth::user();

        $nom = trim( $_POST[ 'nom' ] ?? '' );
        $email = trim( $_POST[ 'email' ] ?? '' );

        if ( $nom && $email ) {
            $user->setNom( $nom );
            $user->setEmail( $email );

            if ( !empty( $_POST[ 'password' ] ) ) {
                $user->setPassword( password_hash( $_POST[ 'password' ], PASSWORD_DEFAULT ) );
            }

            $this->userDAO->update( $user );
            // لازم تضيف ميثود update في UserDAO
        }

        $_SESSION[ 'success' ] = 'Profil mis à jour';
        return $this->redirect( '/profile' );
    }

    public function ai() {
         Auth::requireAuth();
        $user = Auth::user();
        $userId = $user->getId();
        $role = $user->getRole();
        $user1 = $this->userDAO->findByIdForProfile($userId);
        
        $coverUrl = $user1->getCoverUrl();
        $avatarUrl = $user1->getAvatarUrl();

        $data = [];
            $this->view( 'ai/chat', [
            'total'         => $this->noteDAO->countByStatusAndUser($userId),
            'pending'       => $this->noteDAO->countByStatusAndUser($userId, 'soumis'),
            'approved'      => $this->noteDAO->countByStatusAndUser($userId, 'approuve'),
            'rejecter'      => $this->noteDAO->countByStatusAndUser($userId, 'rejetee_manager'),
            'recent'        => $this->deplacementDAO->findByUserId($userId, 20),
            'userId'        => $userId,
            'avatarUrl'     => $avatarUrl,
            'coverUrl'      => $coverUrl,
            'notifications' => $this->notifDAO->findByUser($userId, 10)
        ]);
    }
    public function profileUpload() {
        Auth::requireAuth();
        $user = Auth::user();
        $type = $_POST[ 'type' ] ?? '';

        if ( !in_array( $type, [ 'avatar', 'cover' ] ) ) {
            echo json_encode( [ 'success' => false ] );
            exit;
        }

        if ( empty( $_FILES[ $type ][ 'name' ] ) ) {
            echo json_encode( [ 'success' => false ] );
            exit;
        }

        $file = $_FILES[ $type ];
        $allowed = [ 'jpg', 'jpeg', 'png', 'gif', 'webp' ];
        $ext = strtolower( pathinfo( $file[ 'name' ], PATHINFO_EXTENSION ) );

        if ( !in_array( $ext, $allowed ) || $file[ 'size' ] > 10_000_000 ) {
            // 10MB max on ImgBB
            echo json_encode( [ 'success' => false, 'error' => 'Fichier invalide ou trop lourd' ] );
            exit;
        }

        // رفع الصورة على ImgBB
        $apiKey = '9ebc3d3ad5519c20a70e04f221d04c53';
        // مفتاحك
        $url = 'https://api.imgbb.com/1/upload';

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true );

        $data = [
            'key' => $apiKey,
            'image' => base64_encode( file_get_contents( $file[ 'tmp_name' ] ) ),
            'name' => $user->getId() . '_' . $type . '_' . time()
        ];

        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );

        $response = curl_exec( $ch );
        curl_close( $ch );

        $result = json_decode( $response, true );

        if ( !$result || !$result[ 'success' ] ) {
            echo json_encode( [ 'success' => false, 'error' => 'Erreur ImgBB' ] );
            exit;
        }

        $imageUrl = $result[ 'data' ][ 'url' ];
        // الرابط المباشر

        // حفظ الرابط في قاعدة البيانات
        if ( $type === 'avatar' ) {
            $user->setAvatar( $imageUrl );
            $this->userDAO->updateAvatar( $user->getId(), $imageUrl );
        } else {
            $user->setCover( $imageUrl );
            $this->userDAO->updateCover( $user->getId(), $imageUrl );
        }

        // ريفريش الصفحة عشان الصورة تظهر فورًا
        return $this->redirect( '/profile' );
    }
    // === ADMIN : Liste des utilisateurs ===

    public function index() {
        Auth::requireRole( 'admin' );
        $user = Auth::user();

        $userId = $user->getId();

        $users = $this->userDAO->findAll();
        $usersPhoto = $this->userDAO->findAllP();
        $this->view( 'user/index', [ 'userId'   => $userId, 'users' => $users, 'usersPhoto' => $usersPhoto ,
            'notifications'=>        $this->notifDAO->findByUser($userId) ] );
    }

    public function createPage() {
        Auth::requireRole( 'admin' );
        $user1 = Auth::user();
        $userId = $user1->getId();
        $this->view( 'user/create', [ 'userId'   => $userId,
            'notifications'=>        $this->notifDAO->findByUser($userId)] );
    }

    public function store() {
        Auth::requireRole( 'admin' );

        $nom       = trim( $_POST[ 'nom' ] ?? '' );
        $email     = trim( $_POST[ 'email' ] ?? '' );
        $password  = $_POST[ 'password' ] ?? '';
        $role      = $_POST[ 'role' ] ?? 'employe';
        $manager_id = !empty( $_POST[ 'manager_id' ] ) ? ( int )$_POST[ 'manager_id' ] : null;

        if ( !$nom || !$email || !$password ) {
            $_SESSION[ 'error' ] = 'Tous les champs obligatoires sont requis';
            return $this->redirect( '/users/create' );
        }
        if ( $password !== ( $_POST[ 'password_confirm' ] ?? '' ) ) {
            $_SESSION[ 'error' ] = 'كلمتا المرور غير متطابقتين';
            return $this->redirect( '/users/create' );
        }
        if ( $this->userDAO->findByEmail( $email ) ) {
            $_SESSION[ 'error' ] = 'Cet email est déjà utilisé';
            return $this->redirect( '/users/create' );
        }

        $user = new User(
            id: null,
            nom: $nom,
            email: $email,
            role: $role,
            mot_de_passe: password_hash( $password, PASSWORD_DEFAULT ),
            manager_id: $manager_id
        );

        $this->userDAO->insert( $user );
        $_SESSION[ 'success' ] = 'Utilisateur créé avec succès';
        return $this->redirect( '/users' );
    }

    public function updateRole( $id ) {
        Auth::requireRole( 'admin' );

        $role = $_POST[ 'role' ] ?? null;
        if ( !in_array( $role, [ 'employe', 'manager', 'admin' ] ) ) {
            $_SESSION[ 'error' ] = 'Rôle invalide';
            return $this->redirect( '/users' );
        }

        $this->userDAO->updateRole( $id, $role );
        $_SESSION[ 'success' ] = 'Rôle mis à jour';
        return $this->redirect( '/users' );
    }
    // ManagerController.php

    public function voirManager( $deplacementId ) {
        Auth::requireRole( 'manager' );
        $managerId = Auth::user()->getId();

        $deplacement = $this->deplacementDAO->findById( $deplacementId );
     

        $note = $this->noteDAO->findByDeplacementId( $deplacementId );
        $lignes = $this->detailsDAO->findByNoteId( $note->getId() );
        $employe = $this->userDAO->findUserByDeplacementId( $deplacementId );

        // إحصائيات الموظف
        $stats = [
            'mois' => $this->noteDAO->countByUserThisMonth( $employe->getId() ),
            'approuvees' => $this->noteDAO->countApprovedByUser( $employe->getId() )
        ];
        $user = Auth::user();
        $userId = $user->getId();
        $user1 = $this->userDAO->findByIdForProfile( $user->getId() );
        $coverUrl = $user1->getCoverUrl();
        $avatarUrl = $user1->getAvatarUrl();
            $conversationId = $this->messagerieDAO->getOrCreateConversation($userId, $employe->getId());

        $this->view( 'manager/voir', [
            'deplacement' => $deplacement,
            'note' => $note,
            'userId'   => $userId, 'avatarUrl' => $avatarUrl, 'coverUrl' => $coverUrl,
            'lignes' => $lignes,
            'employe' => $employe,
                    'conversationId' => $conversationId,  // ✅ ID de la conversation

            'stats' => $stats,
            'notifications'=>        $this->notifDAO->findByUser($userId)
        ] );
    }

   

    public function delete( $id ) {
        Auth::requireRole( 'admin' );


        $this->userDAO->delete( $id );
        $_SESSION[ 'success' ] = 'Utilisateur supprimé';
        return $this->redirect( '/users' );
        
    }
}