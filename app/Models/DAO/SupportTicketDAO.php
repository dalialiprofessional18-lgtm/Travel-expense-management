<?php
// app/Models/DAO/SupportTicketDAO.php

namespace App\Models\DAO;

use App\Models\Entities\SupportTicket;
use PDO;
use App\Config\Database;
use App\Models\Entities\Notification;
use PDOException;
class SupportTicketDAO 
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }
    public function insert(SupportTicket $ticket): bool
    {
        $sql = "INSERT INTO support_tickets (user_id, subject, category, message, priority, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $ticket->getUserId(),
            $ticket->getSubject(),
            $ticket->getCategory(),
            $ticket->getMessage(),
            $ticket->getPriority(),
            $ticket->getStatus()
        ]);
    }

    public function findById(int $id): ?SupportTicket
    {
        $sql = "SELECT * FROM support_tickets WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    public function findByUser(int $userId): array
    {
        $sql = "SELECT * FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        $tickets = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tickets[] = $this->hydrate($row);
        }
        return $tickets;
    }

    public function findAll(): array
    {
        $sql = "SELECT st.*, u.nom, u.prenom 
                FROM support_tickets st
                JOIN users u ON st.user_id = u.id
                ORDER BY st.created_at DESC";
        
        $stmt = $this->pdo->query($sql);
        
        $tickets = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tickets[] = $this->hydrate($row);
        }
        return $tickets;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE support_tickets SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function addReply(int $ticketId, int $userId, string $message): bool
    {
        $sql = "INSERT INTO support_replies (ticket_id, user_id, message, created_at) 
                VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$ticketId, $userId, $message]);
    }

    public function getReplies(int $ticketId): array
    {
        $sql = "SELECT sr.*, u.nom, u.prenom, u.role 
                FROM support_replies sr
                JOIN users u ON sr.user_id = u.id
                WHERE sr.ticket_id = ?
                ORDER BY sr.created_at ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ticketId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function hydrate(array $row): SupportTicket
    {
        return new SupportTicket(
            $row['id'],
            $row['user_id'],
            $row['subject'],
            $row['category'],
            $row['message'],
            $row['priority'],
            $row['status'],
            $row['created_at'] ?? null,
            $row['updated_at'] ?? null
        );
    }
}