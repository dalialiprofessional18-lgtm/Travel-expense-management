<?php
namespace App\Models\DAO;
use App\WebSocket\MessageClient;
use App\config\Database;
use App\Models\Entities\Message;
use PDO;
use PDOException;
class MessagerieDAO {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    // Créer ou récupérer une conversation entre deux utilisateurs
    public function getOrCreateConversation($user1_id, $user2_id) {
        try {
            // Vérifier si une conversation existe déjà
            $sql = "SELECT c.id 
                    FROM conversations c
                    INNER JOIN conversation_participants cp1 ON c.id = cp1.conversation_id
                    INNER JOIN conversation_participants cp2 ON c.id = cp2.conversation_id
                    WHERE c.type = 'direct'
                    AND cp1.user_id = ?
                    AND cp2.user_id = ?
                    LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user1_id, $user2_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['id'];
            }
            
            // Créer une nouvelle conversation
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO conversations (type) VALUES ('direct')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $conversation_id = $this->pdo->lastInsertId();
            
            // Ajouter les participants
            $sql = "INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$conversation_id, $user1_id]);
            $stmt->execute([$conversation_id, $user2_id]);
            
            $this->pdo->commit();
            
            return $conversation_id;
            
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Erreur getOrCreateConversation: " . $e->getMessage());
            return false;
        }
    }
    
    // Récupérer les conversations d'un utilisateur
    public function getConversationsByUser($user_id) {
        try {
            $sql = "SELECT 
                        c.id as conversation_id,
                        c.type,
                        c.updated_at,
                        
                        -- Informations de l'autre participant
                        u.id as other_user_id,
                        u.nom as other_user_name,
                        u.email as other_user_email,
                        u.avatar_path as other_user_avatar,
                        u.role as other_user_role,
                        u.job_title as other_user_job_title,
                        
                        -- Dernier message
                        m.id as last_message_id,
                        m.message as last_message,
                        m.created_at as last_message_date,
                        m.sender_id as last_message_sender_id,
                        
                        -- Nombre de messages non lus
                        (SELECT COUNT(*) 
                         FROM messages m2 
                         WHERE m2.conversation_id = c.id 
                         AND m2.sender_id != ?
                         AND m2.id NOT IN (
                             SELECT message_id 
                             FROM message_reads 
                             WHERE user_id = ?
                         )) as unread_count
                        
                    FROM conversations c
                    INNER JOIN conversation_participants cp1 ON c.id = cp1.conversation_id
                    INNER JOIN conversation_participants cp2 ON c.id = cp2.conversation_id
                    INNER JOIN users u ON cp2.user_id = u.id
                    LEFT JOIN messages m ON c.id = m.conversation_id
                    LEFT JOIN messages m_later ON c.id = m_later.conversation_id AND m.created_at < m_later.created_at
                    
                    WHERE cp1.user_id = ?
                    AND cp2.user_id != ?
                    AND m_later.id IS NULL
                    
                    ORDER BY COALESCE(m.created_at, c.created_at) DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id, $user_id, $user_id, $user_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getConversationsByUser: " . $e->getMessage());
            return [];
        }
    }
    
    // Récupérer les messages d'une conversation
    public function getMessagesByConversation($conversation_id, $user_id, $limit = 50, $offset = 0) {
        try {
            $sql = "SELECT 
                        m.id,
                        m.conversation_id,
                        m.sender_id,
                        m.message,
                        m.type,
                        m.file_path,
                        m.file_name,
                        m.file_size,
                        m.created_at,
                        
                        -- Informations de l'expéditeur
                        u.nom as sender_name,
                        u.avatar_path as sender_avatar,
                        u.role as sender_role,
                        
                        -- Vérifier si lu par l'utilisateur
                        CASE WHEN mr.id IS NOT NULL THEN TRUE ELSE FALSE END as is_read
                        
                    FROM messages m
                    INNER JOIN users u ON m.sender_id = u.id
                    LEFT JOIN message_reads mr ON m.id = mr.message_id AND mr.user_id = ?
                    
                    WHERE m.conversation_id = ?
                    
                    ORDER BY m.created_at DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id, $conversation_id, $limit, $offset]);
            
            return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
            
        } catch (PDOException $e) {
            error_log("Erreur getMessagesByConversation: " . $e->getMessage());
            return [];
        }
    }
    
// Dans MessagerieDAO::sendMessage() - AJOUTER CES LIGNES



public function sendMessage($conversation_id, $sender_id, $message, $type = 'text', $file_data = null) {
    try {
        $this->pdo->beginTransaction();
        
        $sql = "INSERT INTO messages (conversation_id, sender_id, message, type, file_path, file_name, file_size) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $conversation_id,
            $sender_id,
            $message,
            $type,
            $file_data['path'] ?? null,
            $file_data['name'] ?? null,
            $file_data['size'] ?? null
        ]);
        
        $message_id = $this->pdo->lastInsertId();
        
        // Mettre à jour la date de la conversation
        $sql = "UPDATE conversations SET updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$conversation_id]);
        
        // Marquer comme lu pour l'expéditeur
        $sql = "INSERT INTO message_reads (message_id, user_id) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$message_id, $sender_id]);
        
        $this->pdo->commit();
        
        // Récupérer le message complet
        $fullMessage = $this->getMessageById($message_id);
        
        // ✅ NOUVEAU: Broadcaster le message en temps réel
        if ($fullMessage) {
            MessageClient::broadcastMessage(
                $conversation_id,
                $fullMessage,
                $sender_id
            );
        }
        
        return $fullMessage;
        
    } catch (PDOException $e) {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        error_log("Erreur sendMessage: " . $e->getMessage());
        return false;
    }
}
    // Supprimer une conversation pour un utilisateur
public function deleteConversation($conversation_id, $user_id) {
    try {
        $this->pdo->beginTransaction();
        
        // Vérifier que l'utilisateur fait partie de la conversation
        if (!$this->isUserInConversation($user_id, $conversation_id)) {
            $this->pdo->rollBack();
            return false;
        }
        
        // Option 1: Suppression complète (si les deux utilisateurs suppriment)
        // Compter le nombre de participants
        $sql = "SELECT COUNT(*) as count FROM conversation_participants WHERE conversation_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$conversation_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $participant_count = $result['count'];
        
        // Retirer l'utilisateur des participants
        $sql = "DELETE FROM conversation_participants WHERE conversation_id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$conversation_id, $user_id]);
        
        // Si c'était le dernier participant, supprimer toute la conversation
        if ($participant_count <= 1) {
            // Supprimer les lectures de messages
            $sql = "DELETE mr FROM message_reads mr
                    INNER JOIN messages m ON mr.message_id = m.id
                    WHERE m.conversation_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$conversation_id]);
            
            // Supprimer les messages
            $sql = "DELETE FROM messages WHERE conversation_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$conversation_id]);
            
            // Supprimer la conversation
            $sql = "DELETE FROM conversations WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$conversation_id]);
        }
        
        $this->pdo->commit();
        return true;
        
    } catch (PDOException $e) {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        error_log("Erreur deleteConversation: " . $e->getMessage());
        return false;
    }
}

// Alternative: Suppression douce (soft delete) - masquer la conversation
public function hideConversation($conversation_id, $user_id) {
    try {
        // Ajouter un champ 'hidden_for' dans conversation_participants si nécessaire
        $sql = "UPDATE conversation_participants 
                SET hidden = 1, hidden_at = NOW() 
                WHERE conversation_id = ? AND user_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$conversation_id, $user_id]);
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        error_log("Erreur hideConversation: " . $e->getMessage());
        return false;
    }
}

// Récupérer une conversation spécifique
public function getConversationById($conversation_id, $user_id) {
    try {
        $sql = "SELECT 
                    c.id as conversation_id,
                    c.type,
                    c.created_at,
                    c.updated_at,
                    
                    -- Informations de l'autre participant
                    u.id as other_user_id,
                    u.nom as other_user_name,
                    u.email as other_user_email,
                    u.avatar_path as other_user_avatar,
                    u.role as other_user_role,
                    u.job_title as other_user_job_title
                    
                FROM conversations c
                INNER JOIN conversation_participants cp1 ON c.id = cp1.conversation_id
                INNER JOIN conversation_participants cp2 ON c.id = cp2.conversation_id
                INNER JOIN users u ON cp2.user_id = u.id
                
                WHERE c.id = ?
                AND cp1.user_id = ?
                AND cp2.user_id != ?
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$conversation_id, $user_id, $user_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur getConversationById: " . $e->getMessage());
        return false;
    }
}
    // Récupérer un message par ID
    public function getMessageById($message_id) {
        try {
            $sql = "SELECT 
                        m.*,
                        u.nom as sender_name,
                        u.avatar_path as sender_avatar,
                        u.role as sender_role
                    FROM messages m
                    INNER JOIN users u ON m.sender_id = u.id
                    WHERE m.id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$message_id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getMessageById: " . $e->getMessage());
            return false;
        }
    }
    
    // Marquer les messages comme lus
    public function markMessagesAsRead($conversation_id, $user_id) {
        try {
            $sql = "INSERT IGNORE INTO message_reads (message_id, user_id)
                    SELECT m.id, ?
                    FROM messages m
                    WHERE m.conversation_id = ?
                    AND m.sender_id != ?
                    AND m.id NOT IN (
                        SELECT message_id 
                        FROM message_reads 
                        WHERE user_id = ?
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id, $conversation_id, $user_id, $user_id]);
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Erreur markMessagesAsRead: " . $e->getMessage());
            return false;
        }
    }
    
    // Récupérer le nombre total de messages non lus
    public function getUnreadCount($user_id) {
        try {
            $sql = "SELECT COUNT(*) as count
                    FROM messages m
                    INNER JOIN conversation_participants cp ON m.conversation_id = cp.conversation_id
                    WHERE cp.user_id = ?
                    AND m.sender_id != ?
                    AND m.id NOT IN (
                        SELECT message_id 
                        FROM message_reads 
                        WHERE user_id = ?
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id, $user_id, $user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'];
            
        } catch (PDOException $e) {
            error_log("Erreur getUnreadCount: " . $e->getMessage());
            return 0;
        }
    }
    
    // Récupérer les utilisateurs avec qui un utilisateur peut parler (selon son rôle)
    public function getAvailableContacts($user_id, $user_role) {
        try {
            $sql = "SELECT 
                        u.id,
                        u.nom,
                        u.email,
                        u.role,
                        u.job_title,
                        u.avatar_path,
                        
                        -- Vérifier si une conversation existe déjà
                        CASE WHEN c.id IS NOT NULL THEN c.id ELSE NULL END as conversation_id
                        
                    FROM users u
                    LEFT JOIN conversation_participants cp1 ON u.id = cp1.user_id
                    LEFT JOIN conversation_participants cp2 ON cp1.conversation_id = cp2.conversation_id AND cp2.user_id = ?
                    LEFT JOIN conversations c ON cp1.conversation_id = c.id AND c.type = 'direct'
                    
                    WHERE u.id != ?
                    AND (
                        -- Admin peut parler avec tout le monde
                        ? = 'admin'
                        OR
                        -- Manager peut parler avec admin et ses employés
                        (? = 'manager' AND u.role IN ('admin', 'employe'))
                        OR
                        -- Employé peut parler avec admin et manager
                        (? = 'employe' AND u.role IN ('admin', 'manager'))
                    )
                    
                    GROUP BY u.id
                    ORDER BY u.nom ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id, $user_id, $user_role, $user_role, $user_role]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getAvailableContacts: " . $e->getMessage());
            return [];
        }
    }
    
    // Vérifier si un utilisateur fait partie d'une conversation
    public function isUserInConversation($user_id, $conversation_id) {
        try {
            $sql = "SELECT COUNT(*) as count
                    FROM conversation_participants
                    WHERE user_id = ? AND conversation_id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id, $conversation_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Erreur isUserInConversation: " . $e->getMessage());
            return false;
        }
    }
}