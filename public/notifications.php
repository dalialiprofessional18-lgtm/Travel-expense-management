<?php
// Activer l'affichage des erreurs temporairement pour debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Vérifier les chemins
$daoPath = __DIR__ . '/../app/Models/DAO/NotificationDAO.php';
$authPath = __DIR__ . '/../app/Helpers/Auth.php';

if (!file_exists($daoPath)) {
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => 'NotificationDAO.php introuvable']));
}

if (!file_exists($authPath)) {
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => 'Auth.php introuvable']));
}

require_once $daoPath;
require_once $authPath;

use App\Models\DAO\NotificationDAO;
use App\Helpers\Auth;
use App\Config\Database;

header('Content-Type: application/json');

// Vérifier l'authentification
if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}
$pdo = Database::getInstance();


$userId = Auth::user()->getId();
$notifDAO = new NotificationDAO();

// ========================================
// GET: Récupérer les notifications
// ========================================
// GET: Récupérer les notifications
// GET: Récupérer les notifications
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Requête SQL directe pour éviter les problèmes d'objets
        $sql = "SELECT id, user_id, title, message, type, is_read, created_at
                FROM notifications
                WHERE user_id = :user_id
                ORDER BY is_read ASC, created_at DESC
                LIMIT 50";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $unreadCount = $notifDAO->countUnread($userId);
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Erreur serveur: ' . $e->getMessage()
        ]);
    }
    exit;
}

// ========================================
// POST: Actions sur les notifications
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['action'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Action manquante']);
        exit;
    }
    
    try {
        // Marquer UNE notification comme lue
        if ($data['action'] === 'mark_read' && isset($data['id'])) {
            $success = $notifDAO->markAsRead((int)$data['id']);
            echo json_encode(['success' => $success]);
            exit;
        }
        
        // Marquer TOUTES les notifications comme lues
        if ($data['action'] === 'mark_all_read') {
            $success = $notifDAO->markAllAsRead($userId);
            echo json_encode(['success' => $success]);
            exit;
        }
        
        // Supprimer une notification
        if ($data['action'] === 'delete' && isset($data['id'])) {
            $success = $notifDAO->delete((int)$data['id']);
            echo json_encode(['success' => $success]);
            exit;
        }
        
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Action invalide']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Erreur: ' . $e->getMessage()
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
