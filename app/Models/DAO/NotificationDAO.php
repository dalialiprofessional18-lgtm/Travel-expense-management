<?php
// app/Models/DAO/NotificationDAO.php

namespace App\Models\DAO;

use App\Config\Database;
use App\Models\Entities\Notification;
use PDO;
use PDOException;

class NotificationDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /** Créer une notification */
    public function create(int $userId, string $title, string $message, string $type = 'info'): bool
    {
        $sql = "INSERT INTO notifications (user_id, title, message, type) 
                VALUES (:user_id, :title, :message, :type)";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':title'   => $title,
                ':message' => $message,
                ':type'    => $type
            ]);
        } catch (PDOException $e) {
            error_log('NotificationDAO::create → ' . $e->getMessage());
            return false;
        }
    }

    /** Retourne un tableau d'objets Notification */
   public function findByUser(int $userId, int $limit = 50): array
{
    $sql = "SELECT id, user_id, title, message, type, is_read, created_at
            FROM notifications
            WHERE user_id = :user_id
            ORDER BY is_read ASC, created_at DESC
            LIMIT :limit";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit',   $limit,   PDO::PARAM_INT);
    $stmt->execute();

    $notifications = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $notifications[] = new Notification($row);
    }

    return $notifications;
}
    public function countUnread(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function markAsRead(int $notificationId): bool
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $notificationId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function markAllAsRead(int $userId): bool
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $notificationId): bool
    {
        $sql = "DELETE FROM notifications WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $notificationId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function cleanupOld(int $days = 30): int
    {
        $sql = "DELETE FROM notifications WHERE created_at < NOW() - INTERVAL :days DAY";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}