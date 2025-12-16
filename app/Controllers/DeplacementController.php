<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DAO\DeplacementDAO;
use App\Models\DAO\HistoriqueStatutDAO;
use App\Models\Entities\Deplacement;
use App\Models\Entities\HistoriqueStatut;
use App\Helpers\Auth;
use App\Models\DAO\NoteFraisDAO;
use App\Models\DAO\UserDAO;
use App\Models\Entities\NoteFrais;
use App\Models\DAO\NotificationDAO;
use Exception;
class DeplacementController extends BaseController
 {
    private UserDAO $userDAO;
    private DeplacementDAO $dao;
    private NoteFraisDAO $noteDAO;
    private NotificationDAO $notifDAO;

    private HistoriqueStatutDAO $histoDAO;

    public function __construct()
 {
            $this->notifDAO = new NotificationDAO();

        $this->userDAO = new UserDAO();
        $this->dao = new DeplacementDAO();
        $this->histoDAO = new HistoriqueStatutDAO();
        $this->noteDAO = new NoteFraisDAO();
    }

    public function index( $user_id )
 {
        $currentUser = Auth::user();
        $user1 = $this->userDAO->findByIdForProfile( $currentUser->getId() );
        //
        $user = Auth::user();
        $userId = $user->getId();
        $avatarUrl = $user1->getAvatarUrl();
        $isOwner = $currentUser->getId() == $user_id;
        $isManagerOrAdmin = in_array( $currentUser->getRole(), [ 'manager', 'admin' ] );

        if ( !$isOwner && !$isManagerOrAdmin ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }

        $deplacements = $this->dao->findByUser( $user_id );
        $this->view( 'deplacement/index', [
            'deplacements' => $deplacements,
            'owner_id' => $user_id,
            'userId' => $userId,
            'avatarUrl' => $avatarUrl,
            'notifications'=>        $this->notifDAO->findByUser($userId)
        ] );
    }
// في ملف: app/Controllers/DeplacementController.php

public function get($id)
{
    Auth::requireAuth();

    $currentUser = Auth::user();
    $userId      = $currentUser->getId();

    // جلب الـ Déplacement من الـ DAO
    $deplacement = $this->dao->findById($id);

    if (!$deplacement) {
        $_SESSION['error'] = 'Déplacement introuvable.';
        return $this->redirect("/deplacements/{$userId}");
    }

    // تحقق الصلاحيات
    if ($deplacement->getUserId() !== $userId && !in_array($currentUser->getRole(), ['manager', 'admin'])) {
        $_SESSION['error'] = 'Accès refusé.';
        return $this->redirect("/deplacements/{$userId}");
    }

    // جلب الإشعارات (اختياري حسب الـ layout بتاعك)
    $notifications = $this->notifDAO->findByUser($userId);

    // عرض صفحة التعديل (GET فقط)
    return $this->view('deplacement/edit', [
        'deplacement'   => $deplacement,
        'userId'        => $userId,
        'notifications' => $notifications ?? [],
        'pageTitle'     => 'Modifier – ' . htmlspecialchars($deplacement->getTitre())
    ]);
}
    public function createPage()
 {        Auth::requireAuth();
        Auth::user()->getId();
            $user = Auth::user();
        $userId = $user->getId();


        $this->view( 'deplacement/create', [
            'userId'    =>   Auth::user()->getId(),
            'notifications'=>        $this->notifDAO->findByUser($userId)

        ] );
    }
public function edit($id)
{Auth::requireAuth();
    $user = Auth::user();

    $deplacement = $this->dao->findById($id);
    if (!$deplacement || !Auth::canManageDeplacement($deplacement)) {
        $_SESSION['error'] = 'Accès refusé';
        return $this->redirect('/deplacements/' . $user->getId());
    }

    $titre       = trim($_POST['titre'] ?? '');
    $lieu_depart = trim($_POST['lieu_depart'] ?? '');
    $lieu        = trim($_POST['lieu'] ?? '');
    $date_depart  = $_POST['date_depart'] ?? '';
    $date_retour  = $_POST['date_retour'] ?? '';
    $objet       = trim($_POST['objet'] ?? '');

    if (!$titre || !$lieu_depart || !$lieu || !$date_depart || !$date_retour) {
        $_SESSION['error'] = 'Tous les champs obligatoires doivent être remplis.';
        return $this->redirect("/deplacements/edit/{$id}");
    }

    // Mise à jour
    $deplacement->setTitre($titre);
    $deplacement->setLieuDepart($lieu_depart);
    $deplacement->setLieu($lieu);
    $deplacement->setDateDepart($date_depart);
    $deplacement->setDateRetour($date_retour);
    $deplacement->setObjet($objet);

    if ($this->dao->update($deplacement)) {
        $_SESSION['success'] = 'Déplacement mis à jour avec succès !';
    return $this->redirect('/deplacements/' . $user->getId());
    } else {
        $_SESSION['error'] = 'Erreur lors de la mise à jour.';
        return $this->redirect("/deplacements/edit/{$id}");
    }
}
    public function indexPage()
 {
        Auth::requireAuth();
            $user = Auth::user();
        $userId = $user->getId();
        $this->view( 'deplacement/index' ,[
            'userId'    =>   Auth::user()->getId(),
            'notifications'=>        $this->notifDAO->findByUser($userId)

        ]);
    }

    public function store()
 {
     
    Auth::requireAuth();
    $user = Auth::user();
    
    $titre        = trim($_POST['titre'] ?? '');
    $lieu_depart  = trim($_POST['lieu_depart'] );  // ✅ NOUVEAU
    $lieu         = trim($_POST['lieu'] ?? '');
    $date_depart  = $_POST['date_depart'] ?? '';
    $date_retour  = $_POST['date_retour'] ?? '';
    $objet        = $_POST['objet'] ?? '';

    if (!$titre || !$lieu || !$date_depart || !$date_retour) {
        $_SESSION['error'] = 'Tous les champs sont requis';
        return $this->redirect('/deplacements/create');
    }

    // 1. Créer le déplacement
    $dep = new Deplacement(
        null,
        $user->getId(),
        $titre,
        $lieu_depart,  // ✅ NOUVEAU paramètre
        $lieu,
        $date_depart,
        $date_retour,
        $objet,
        'brouillon'
    );

        // لازم نعمل insert ونرجع الـ ID الجديد
        $deplacementId = $this->dao->insertAndGetId( $dep );
        // مهم: لازم ترجع الـ ID

        if ( !$deplacementId ) {
            $_SESSION[ 'error' ] = 'Erreur lors de la création du déplacement';
            return $this->redirect( '/deplacements/create' );
        }

        // 2. إنشاء Note de frais فارغة تلقائيًا
        $note = new NoteFrais(
            null,
            $deplacementId,
            $user->getId(),
            'brouillon',
            0.00
        );

        // تأكد أن عندك NoteFraisDAO محقون أو معرّف
        $this->noteDAO->insert( $note );

        $_SESSION[ 'success' ] = 'Déplacement et note de frais créés avec succès !';
        return $this->redirect( "/deplacements/{$user->getId()}" );
    }
// Dans /app/Controllers/DeplacementController.php

public function attribuerPage()
{
    Auth::requireRole('manager');
    
    $currentUser = Auth::user();
    $userId = $currentUser->getId();
    $user1 = $this->userDAO->findByIdForProfile($userId);
    
    // ✅ Récupérer UNIQUEMENT les employés (pas les managers)
    $employees = $this->userDAO->findByRole('employe');
    
    // Formater les utilisateurs avec photos
    $usersData = [];
    foreach ($employees as $user) {
        $userWithPhoto = $this->userDAO->findByIdForProfile($user->getId());
        $usersData[] = [
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'avatar_url' => $userWithPhoto ? $userWithPhoto->getAvatarUrl() : null
        ];
    }
    
    return $this->view('deplacement/attribuer', [
        'userId' => $userId,
        'avatarUrl' => $user1->getAvatarUrl(),
        'coverUrl' => $user1->getCoverUrl(),
        'notifications' => $this->notifDAO->findByUser($userId, 10),
        'users' => $usersData
    ]);
}

public function deplacementsEquipe()
{
    Auth::requireRole('manager');
    
    $currentUser = Auth::user();
    $managerId = $currentUser->getId();
    
    // Pagination
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = 10;
    $offset = ($page - 1) * $perPage;
    
    // Récupérer TOUS les déplacements de l'équipe avec pagination
    $deplacements = $this->dao->findByTeamWithPagination($managerId, $perPage, $offset);
    
    // Compter le total pour la pagination
    $totalCount = $this->dao->countByTeam($managerId);
    $totalPages = ceil($totalCount / $perPage);
    
    // Calculer les statistiques
    $stats = [
        'total' => $totalCount,
        'en_attente' => $this->dao->countByTeamAndStatus($managerId, 'en_attente'),
        'approuve' => $this->dao->countByTeamAndStatus($managerId, 'approuve'),
        'rejetee' => $this->dao->countByTeamAndStatus($managerId, 'rejetee'),
        'brouillon' => $this->dao->countByTeamAndStatus($managerId, 'brouillon'),
        'ce_mois' => $this->dao->countByTeamThisMonth($managerId)
    ];
    
    // Enrichir les déplacements avec les infos employés
    foreach ($deplacements as &$deplacement) {
        $employe = $this->userDAO->findByIdForProfile($deplacement['user_id']);
        if ($employe) {
            $deplacement['employe_nom'] = $employe->getNom();
            $deplacement['employe_avatar'] = $employe->getAvatarUrl();
        } else {
            $deplacement['employe_nom'] = 'Utilisateur inconnu';
            $deplacement['employe_avatar'] = '/assets/images/default-avatar.png';
        }
    }
    
    return $this->view('manager/deplacements-equipe', [
        'deplacements' => $deplacements,
        'stats' => $stats,
        'page' => $page,
        'perPage' => $perPage,
        'totalCount' => $totalCount,
        'totalPages' => $totalPages,
        'userId' => $managerId,
        'notifications' => $this->notifDAO->findByUser($managerId)
    ]);
}

/**
 * Approuver un déplacement
 */
public function approuverDeplacement($id)
{
    Auth::requireRole('manager');
    
    $deplacement = $this->dao->findById($id);
    $currentUser = Auth::user();
    
    if (!$deplacement) {
        $_SESSION['error'] = 'Déplacement introuvable';
        return $this->redirect('/manager/deplacements-equipe');
    }
    
    // Vérifier que le déplacement appartient à un membre de l'équipe
    $employe = $this->userDAO->findById($deplacement->getUserId());
    if (!$employe || $employe->getManagerId() != $currentUser->getId()) {
        $_SESSION['error'] = 'Accès refusé';
        return $this->redirect('/manager/deplacements-equipe');
    }
    
    // Mettre à jour le statut
    $this->dao->update($deplacement);
    
    // Mettre à jour la note de frais associée
    $note = $this->noteDAO->findByDeplacementId($id);
    if ($note) {
        $note->setStatut('validee_manager');
        $this->noteDAO->updateStatut($note->getId(), 'validee_manager');
    }
    
    // Créer une notification pour l'employé
    $this->notifDAO->create(
        $deplacement->getUserId(),
        'deplacement_approuve',
        sprintf('Votre déplacement "%s" a été approuvé', $deplacement->getTitre()),
        "/deplacements/{$deplacement->getUserId()}"
    );
    
    $_SESSION['success'] = 'Déplacement approuvé avec succès';
    return $this->redirect('/manager/deplacements-equipe');
}

/**
 * Rejeter un déplacement
 */
public function rejeterDeplacement($id)
{
    Auth::requireRole('manager');
    
    $deplacement = $this->dao->findById($id);
    $currentUser = Auth::user();
    
    if (!$deplacement) {
        $_SESSION['error'] = 'Déplacement introuvable';
        return $this->redirect('/manager/deplacements-equipe');
    }
    
    // Vérifier que le déplacement appartient à un membre de l'équipe
    $employe = $this->userDAO->findById($deplacement->getUserId());
    if (!$employe || $employe->getManagerId() != $currentUser->getId()) {
        $_SESSION['error'] = 'Accès refusé';
        return $this->redirect('/manager/deplacements-equipe');
    }
    
    // Mettre à jour le statut
    $this->dao->update($deplacement);
    
    // Mettre à jour la note de frais associée
    $note = $this->noteDAO->findByDeplacementId($id);
    if ($note) {
        $note->setStatut('rejetee_manager');
        $this->noteDAO->updateStatut($note->getId(), 'rejetee_manager');
    }
    
    // Créer une notification pour l'employé
    $this->notifDAO->create(
        $deplacement->getUserId(),
        'deplacement_rejete',
        sprintf('Votre déplacement "%s" a été rejeté', $deplacement->getTitre()),
        "/deplacements/{$deplacement->getUserId()}"
    );
    
    $_SESSION['warning'] = 'Déplacement rejeté';
    return $this->redirect('/manager/deplacements-equipe');
}
/**
 * Traiter l'attribution d'un déplacement
 */
public function attribuer()
{
    Auth::requireRole('manager');
    
    $currentUser = Auth::user();
    $assignedByUserId = $currentUser->getId();
    
    // Récupérer les données
    $titre = trim($_POST['titre'] ?? '');
    $lieu_depart = trim($_POST['lieu_depart'] ?? '');
    $lieu = trim($_POST['lieu'] ?? '');
    $date_depart = $_POST['date_depart'] ?? '';
    $date_retour = $_POST['date_retour'] ?? '';
    $objet = trim($_POST['objet'] ?? '');
    $userId = (int)($_POST['user_id'] ?? 0);
    
    // Validation
    if (!$titre || !$lieu_depart || !$lieu || !$date_depart || !$date_retour || !$userId) {
        $_SESSION['error'] = 'Tous les champs obligatoires doivent être remplis';
        return $this->redirect('/deplacements/attribuer');
    }
    
    // Vérifier que l'utilisateur existe
    $targetUser = $this->userDAO->findById($userId);
    if (!$targetUser) {
        $_SESSION['error'] = 'Utilisateur introuvable';
        return $this->redirect('/deplacements/attribuer');
    }
    
    // Vérifier les permissions
    $canAssign = false;
    if ($currentUser->getRole() === 'admin') {
        $canAssign = true;
    } elseif ($currentUser->getRole() === 'manager') {
        // Manager peut attribuer à ses employés ou à d'autres managers
        if ($targetUser->getManagerId() == $currentUser->getId() || 
            $targetUser->getRole() === 'manager') {
            $canAssign = true;
        }
    }
    
    if (!$canAssign) {
        $_SESSION['error'] = 'Vous n\'avez pas les permissions pour attribuer à cet utilisateur';
        return $this->redirect('/deplacements/attribuer');
    }
    
    try {
        // 1. Créer le déplacement
        $dep = new Deplacement(
            null,
            $userId,  // Attribué à l'utilisateur sélectionné
            $titre,
            $lieu_depart,
            $lieu,
            $date_depart,
            $date_retour,
            $objet,
            'brouillon'
        );
        
        $deplacementId = $this->dao->insertAndGetId($dep);
        
        if (!$deplacementId) {
            throw new Exception('Erreur lors de la création du déplacement');
        }
        
        // 2. Créer la note de frais associée
        $note = new NoteFrais(
            null,
            $deplacementId,
            $userId,  // Même user_id que le déplacement
            'brouillon',
            0.00
        );
        
        $this->noteDAO->insert($note);
        
        // 3. Créer une notification pour l'utilisateur
        $message = sprintf(
            '%s vous a attribué un nouveau déplacement : %s',
            $currentUser->getNom(),
            $titre
        );
        
        $this->notifDAO->create(
            $userId,
            'deplacement_attribue',
            $message,
            "/deplacements/{$userId}"
        );
        
        // 4. Log l'attribution
        error_log(sprintf(
            "✅ Déplacement #%d attribué par %s (ID:%d) à %s (ID:%d)",
            $deplacementId,
            $currentUser->getNom(),
            $assignedByUserId,
            $targetUser->getNom(),
            $userId
        ));
        
        $_SESSION['success'] = sprintf(
            'Déplacement créé et attribué à %s avec succès !',
            $targetUser->getNom()
        );
        
        return $this->redirect('/manager');
        
    } catch (Exception $e) {
        error_log("❌ Erreur attribuer: " . $e->getMessage());
        $_SESSION['error'] = 'Erreur lors de l\'attribution: ' . $e->getMessage();
        return $this->redirect('/deplacements/attribuer');
    }
}
public function showMap($id) {
    Auth::requireAuth();
    $user = Auth::user();
    $userId = $user->getId();
    
    $deplacement = $this->dao->findById($id);
    
    $notifications = $this->notifDAO->findByUser($userId);

    
    $this->view('deplacement/map', [
        'deplacement' => $deplacement,
        'userId' => $userId,
                'notifications' => $notifications ?? [],

    ]);
}
    public function delete( $id )
 {
        $dep = $this->dao->findById( $id );
        if ( !$dep || !Auth::canManageDeplacement( $dep ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }

        $this->dao->delete( $id );
        $_SESSION[ 'success' ] = 'Déplacement supprimé';
        return $this->redirect( '/employee' );
    }
}