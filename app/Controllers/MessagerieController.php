<?php
namespace App\Controllers;
use App\Models\DAO\MessagerieDAO;
use App\Models\DAO\UserDAO;
use App\Models\DAO\NotificationDAO;
use App\Helpers\Auth;
use App\Core\BaseController;

class MessagerieController extends BaseController {
    private $messagerieDAO;
    private $userDAO;
    private $notifDAO;
    
    public function __construct() {
        $this->messagerieDAO = new MessagerieDAO();
        $this->userDAO = new UserDAO();
        $this->notifDAO = new NotificationDAO();
    }
    
    // Page principale de la messagerie
    public function index() {
        Auth::requireAuth();
        
        $user = Auth::user();
        $userId = $user->getId();
        $role = $user->getRole();
        
        // Récupérer les données utilisateur pour le header
        $user1 = $this->userDAO->findByIdForProfile($userId);
        $avatarUrl = $user1->getAvatarUrl();
        $coverUrl = $user1->getCoverUrl();
        
        // Récupérer les conversations
        $conversations = $this->messagerieDAO->getConversationsByUser($userId);
        
        // Récupérer les contacts disponibles
        $contacts = $this->messagerieDAO->getAvailableContacts($userId, $role);
        
        // Récupérer le nombre de messages non lus
        $unreadCount = $this->messagerieDAO->getUnreadCount($userId);
        
        // Récupérer les notifications
        $notifications = $this->notifDAO->findByUser($userId, 10);
        
        $data = [
            'conversations' => $conversations,
            'contacts' => $contacts,
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
            'userId' => $userId,
            'userRole' => $role,
            'avatarUrl' => $avatarUrl,
            'coverUrl' => $coverUrl,
            'currentConversation' => null,
            'messages' => []
        ];
        
        $this->view('messagerie/index', $data);
    }
    
    // Afficher une conversation spécifique
    public function conversation($conversationId) {
        Auth::requireAuth();
        
        $user = Auth::user();
        $userId = $user->getId();
        $role = $user->getRole();
        
        
        // Récupérer les données utilisateur pour le header
        $user1 = $this->userDAO->findByIdForProfile($userId);
        $avatarUrl = $user1->getAvatarUrl();
        $coverUrl = $user1->getCoverUrl();
        
        // Récupérer les conversations
        $conversations = $this->messagerieDAO->getConversationsByUser($userId);
        
        // Récupérer les contacts disponibles
        $contacts = $this->messagerieDAO->getAvailableContacts($userId, $role);
        
        // Récupérer les messages de la conversation
        $messages = $this->messagerieDAO->getMessagesByConversation($conversationId, $userId);
        
        // Récupérer les infos de la conversation courante
        $currentConversation = null;
        foreach ($conversations as $conv) {
            if ($conv['conversation_id'] == $conversationId) {
                $currentConversation = $conv;
                break;
            }
        }
        
        // Marquer les messages comme lus
        $this->messagerieDAO->markMessagesAsRead($conversationId, $userId);
        
        // Récupérer le nombre de messages non lus
        $unreadCount = $this->messagerieDAO->getUnreadCount($userId);
        
        // Récupérer les notifications
        $notifications = $this->notifDAO->findByUser($userId, 10);
        
        $data = [
            'conversations' => $conversations,
            'contacts' => $contacts,
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
            'userId' => $userId,
            'userRole' => $role,
            'avatarUrl' => $avatarUrl,
            'coverUrl' => $coverUrl,
            'currentConversation' => $currentConversation,
            'currentConversationId' => $conversationId,
            'messages' => $messages
        ];
        
        $this->view('messagerie/index', $data);
    }
    
   public function sendMessage() {
    Auth::requireAuth();
    
    $user = Auth::user();
    $userId = $user->getId();
    
    $conversationId = $_POST['conversation_id'] ?? null;
    $message = trim($_POST['message'] ?? '');
    
    // Détection AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if (!$conversationId || empty($message)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Données manquantes']);
            exit;
        }
        $_SESSION['error'] = 'Données manquantes';
        $this->redirect('/messagerie/conversation/' . $conversationId);
        return;
    }
    
    // Vérifier que l'utilisateur fait partie de la conversation
    if (!$this->messagerieDAO->isUserInConversation($userId, $conversationId)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Accès non autorisé']);
            exit;
        }
        $_SESSION['error'] = 'Accès non autorisé';
        $this->redirect('/messagerie');
        return;
    }
    
    // Envoyer le message
    $sentMessage = $this->messagerieDAO->sendMessage($conversationId, $userId, $message);
    
    if ($isAjax) {
        header('Content-Type: application/json');
        if ($sentMessage) {
            echo json_encode([
                'success' => true,
                'message' => $sentMessage
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de l\'envoi'
            ]);
        }
        exit;
    }
    
    // Fallback classique
    if ($sentMessage) {
        $_SESSION['success'] = 'Message envoyé';
    } else {
        $_SESSION['error'] = 'Erreur lors de l\'envoi du message';
    }
    
    $this->redirect('/messagerie/conversation/' . $conversationId);
}
    
    // Créer ou récupérer une conversation avec un utilisateur
    public function startConversation() {
        Auth::requireAuth();
        
        $user = Auth::user();
        $userId = $user->getId();
        
        $otherUserId = $_POST['other_user_id'] ?? null;
        
        if (!$otherUserId) {
            $_SESSION['error'] = 'Utilisateur non spécifié';
            $this->redirect('/messagerie');
            return;
        }
        
        // Créer ou récupérer la conversation
        $conversationId = $this->messagerieDAO->getOrCreateConversation($userId, $otherUserId);
        
        if ($conversationId) {
            $_SESSION['success'] = 'Conversation ouverte';
            $this->redirect('/messagerie/conversation/' . $conversationId);
        } else {
            $_SESSION['error'] = 'Erreur lors de la création de la conversation';
            $this->redirect('/messagerie');
        }
    }
    
    // Supprimer une conversation
// Supprimer une conversation
public function deleteConversation($conversationId) {
    Auth::requireAuth();
    
    $user = Auth::user();
    $userId = $user->getId();
    
    // Vérifier que l'utilisateur fait partie de la conversation
    if (!$this->messagerieDAO->isUserInConversation($userId, $conversationId)) {
        $_SESSION['error'] = 'Accès non autorisé';
        $this->redirect('/messagerie');
        return;
    }
    
    // Supprimer la conversation
    $result = $this->messagerieDAO->deleteConversation($conversationId, $userId);
    
    if ($result) {
        $_SESSION['success'] = 'Conversation supprimée avec succès';
    } else {
        $_SESSION['error'] = 'Erreur lors de la suppression de la conversation';
    }
    
    $this->redirect('/messagerie');
}
}