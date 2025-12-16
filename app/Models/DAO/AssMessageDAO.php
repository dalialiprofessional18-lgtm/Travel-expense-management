<?php
namespace App\Models\DAO;

use App\Config\Database;
use App\Models\Entities\AssMessage;
use PDO;

class AssMessageDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function create(int $conversationId, string $role, string $content, ?array $metadata = null): int
    {
        $sql = "INSERT INTO assistant_messages (conversation_id, role, content, metadata) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $conversationId,
            $role,
            $content,
            $metadata ? json_encode($metadata) : null
        ]);
        
        return (int) $this->pdo->lastInsertId();
    }

    public function findByConversation(int $conversationId): array
    {
        $sql = "SELECT * FROM assistant_messages 
                WHERE conversation_id = ? 
                ORDER BY created_at ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$conversationId]);
        
        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new AssMessage(
                $row['id'],
                $row['conversation_id'],
                $row['role'],
                $row['content'],
                $row['metadata'] ? json_decode($row['metadata'], true) : null,
                $row['created_at']
            );
        }
        return $messages;
    }
}
