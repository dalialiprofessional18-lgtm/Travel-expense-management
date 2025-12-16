<?php
// app/WebSocket/NotificationServer.php - VERSION ÉTENDUE
namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class NotificationServer implements MessageComponentInterface
{
    protected $clients;
    protected $users = [];
    protected $userConversations = []; // Nouveau: tracker les conversations actives

    public function __construct()
    {
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "✅ Connexion ouverte ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        
        // 1. Authentication du client
        if (isset($data['type']) && $data['type'] === 'auth' && !empty($data['user_id'])) {
            $this->users[$from->resourceId] = (int)$data['user_id'];
            echo "🔑 User {$data['user_id']} authentifié (conn: {$from->resourceId})\n";
            return;
        }

        // 2. Joindre une conversation
        if (isset($data['type']) && $data['type'] === 'join_conversation') {
            $userId = $this->users[$from->resourceId] ?? null;
            $conversationId = $data['conversation_id'] ?? null;
            
            if ($userId && $conversationId) {
                if (!isset($this->userConversations[$userId])) {
                    $this->userConversations[$userId] = [];
                }
                $this->userConversations[$userId][] = $conversationId;
                echo "👥 User {$userId} a rejoint conversation {$conversationId}\n";
            }
            return;
        }

        // 3. Quitter une conversation
        if (isset($data['type']) && $data['type'] === 'leave_conversation') {
            $userId = $this->users[$from->resourceId] ?? null;
            $conversationId = $data['conversation_id'] ?? null;
            
            if ($userId && $conversationId && isset($this->userConversations[$userId])) {
                $this->userConversations[$userId] = array_filter(
                    $this->userConversations[$userId],
                    fn($id) => $id != $conversationId
                );
                echo "👋 User {$userId} a quitté conversation {$conversationId}\n";
            }
            return;
        }

        // 4. Broadcast notification (existant)
        if (isset($data['type']) && $data['type'] === 'broadcast' && !empty($data['user_id'])) {
            $this->broadcastToUser($data['user_id'], $data['notification']);
        }

        // 5. NOUVEAU: Broadcast message dans une conversation
        if (isset($data['type']) && $data['type'] === 'new_message') {
            $this->broadcastMessageToConversation(
                $data['conversation_id'],
                $data['message'],
                $data['sender_id']
            );
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if (isset($this->users[$conn->resourceId])) {
            $userId = $this->users[$conn->resourceId];
            echo "❌ User {$userId} déconnecté\n";
            unset($this->users[$conn->resourceId]);
            unset($this->userConversations[$userId]);
        }
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "⚠️ Erreur: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Envoie une notification à un utilisateur spécifique
     */
    public function broadcastToUser(int $userId, array $notification): void
    {
        $payload = json_encode($notification);
        $sent = false;

        foreach ($this->clients as $client) {
            if (isset($this->users[$client->resourceId]) && 
                $this->users[$client->resourceId] === $userId) {
                $client->send($payload);
                echo "📨 Notification envoyée à user {$userId}\n";
                $sent = true;
            }
        }

        if (!$sent) {
            echo "⚠️ User {$userId} pas connecté (notification ignorée)\n";
        }
    }
    

    /**
     * NOUVEAU: Broadcast un message à tous les participants d'une conversation
     */
    public function broadcastMessageToConversation(int $conversationId, array $message, int $senderId): void
    {
        $payload = json_encode([
            'type' => 'new_message',
            'conversation_id' => $conversationId,
            'message' => $message
        ]);
        
        $sentCount = 0;

        foreach ($this->clients as $client) {
            $userId = $this->users[$client->resourceId] ?? null;
            
            // Envoyer à tous les users dans cette conversation SAUF l'expéditeur
            if ($userId && $userId != $senderId && 
                isset($this->userConversations[$userId]) &&
                in_array($conversationId, $this->userConversations[$userId])) {
                
                $client->send($payload);
                $sentCount++;
                echo "💬 Message envoyé à user {$userId} dans conversation {$conversationId}\n";
            }
        }

        echo "📊 Message broadcast à {$sentCount} participant(s)\n";
    }
}