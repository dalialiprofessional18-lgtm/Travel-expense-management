<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DAO\CategorieFraisDAO;
use App\Models\DAO\NotificationDAO;
use App\Helpers\Auth;
use App\Models\DAO\UserDAO;

class CategoryController extends BaseController {
    private CategorieFraisDAO $categorieDAO;
    private NotificationDAO $notifDAO;
    private UserDAO $userDAO;

    public function __construct() {
        $this->categorieDAO = new CategorieFraisDAO();
        $this->notifDAO = new NotificationDAO();
                $this->userDAO = new UserDAO();

    }

    /**
     * Afficher toutes les catégories
     */
    public function index() {
        Auth::requireAuth();
        Auth::requireRole('admin');
        
        $user = Auth::user();
        $userId = $user->getId();

        $user1 = $this->userDAO->findByIdForProfile($userId);
        
        $avatarUrl = $user1->getAvatarUrl();
        $categories = $this->categorieDAO->findAll();
        
        $this->view('categories/index', [
            'categories' => $categories,
            'userId' => $userId,
            'avatarUrl'     => $avatarUrl,
            'notifications' => $this->notifDAO->findByUser($userId)
        ]);
    }

    /**
     * Afficher le formulaire d'ajout
     */
    public function createPage() {
        Auth::requireRole('admin');
        
        $user = Auth::user();
        $userId = $user->getId();
                $user1 = $this->userDAO->findByIdForProfile($userId);
        
        $avatarUrl = $user1->getAvatarUrl();
        
        $this->view('categories/create', [
            'userId' => $userId,
                        'avatarUrl'     => $avatarUrl,

            'notifications' => $this->notifDAO->findByUser($userId)
        ]);
    }

    /**
     * Enregistrer une nouvelle catégorie
     */
    public function store() {
        Auth::requireRole('admin');
        
        $type = trim($_POST['type'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Validation
        if (empty($type)) {
            $_SESSION['error'] = 'Le type de catégorie est obligatoire';
            return $this->redirect('/admin/categories/create');
        }

        try {
            $this->categorieDAO->insert($type, $description);
            $_SESSION['success'] = 'Catégorie ajoutée avec succès';
            return $this->redirect('/admin/categories');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de l\'ajout: ' . $e->getMessage();
            return $this->redirect('/admin/categories/create');
        }
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function editPage($id) {
        Auth::requireRole('admin');
        
        $user = Auth::user();
        $userId = $user->getId();
        
        $category = $this->categorieDAO->findById($id);
        
        if (!$category) {
            $_SESSION['error'] = 'Catégorie introuvable';
            return $this->redirect('/admin/categories');
        }
        $user1 = $this->userDAO->findByIdForProfile($userId);
        
        $avatarUrl = $user1->getAvatarUrl();
        $this->view('categories/edit', [
            'category' => $category,
            'userId' => $userId,
                        'avatarUrl'     => $avatarUrl,
            'notifications' => $this->notifDAO->findByUser($userId)
        ]);
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update($id) {
        Auth::requireRole('admin');
        
        $type = trim($_POST['type'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($type)) {
            $_SESSION['error'] = 'Le type de catégorie est obligatoire';
            return $this->redirect('/admin/categories/edit/' . $id);
        }

        try {
            $this->categorieDAO->update($id, $type, $description);
            $_SESSION['success'] = 'Catégorie modifiée avec succès';
            return $this->redirect('/admin/categories');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la modification: ' . $e->getMessage();
            return $this->redirect('/admin/categories/edit/' . $id);
        }
    }

    /**
     * Supprimer une catégorie
     */
    public function delete($id) {
        Auth::requireRole('admin');
        
        try {
            $result = $this->categorieDAO->delete($id);
            
            if ($result === false) {
                $_SESSION['error'] = 'Impossible de supprimer cette catégorie car elle est utilisée dans des notes de frais';
            } else {
                $_SESSION['success'] = 'Catégorie supprimée avec succès';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la suppression: ' . $e->getMessage();
        }
        
        return $this->redirect('/admin/categories');
    }
}