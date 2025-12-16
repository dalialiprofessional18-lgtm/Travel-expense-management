<?php
namespace App\Models\DAO;

use App\Config\Database;
use App\Models\Entities\Conversation;
use PDO;

class ConversationDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function create(int $userId, string $title = 'Nouvelle conversation'): int
    {
        $sql = "INSERT INTO assistant_conversations (user_id, title) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $title]);
        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $id): ?Conversation
    {
        $sql = "SELECT * FROM assistant_conversations WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Conversation(
            $row['id'],
            $row['user_id'],
            $row['title'],
            $row['created_at'],
            $row['updated_at']
        );
    }

    public function findByUser(int $userId, int $limit = 20): array
    {
        $sql = "SELECT * FROM assistant_conversations 
                WHERE user_id = ? 
                ORDER BY updated_at DESC 
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $limit]);
        
        $conversations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $conversations[] = new Conversation(
                $row['id'],
                $row['user_id'],
                $row['title'],
                $row['created_at'],
                $row['updated_at']
            );
        }
        return $conversations;
    }

    public function updateTitle(int $id, string $title): bool
    {
        $sql = "UPDATE assistant_conversations SET title = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $id]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM assistant_conversations WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}