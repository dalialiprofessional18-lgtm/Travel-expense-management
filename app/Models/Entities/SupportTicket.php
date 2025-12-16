<?php
// app/Models/Entities/SupportTicket.php

namespace App\Models\Entities;

class SupportTicket
{
    private ?int $id;
    private int $userId;
    private string $subject;
    private string $category;
    private string $message;
    private string $priority;
    private string $status;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id,
        int $userId,
        string $subject,
        string $category,
        string $message,
        string $priority = 'normal',
        string $status = 'open',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->subject = $subject;
        $this->category = $category;
        $this->message = $message;
        $this->priority = $priority;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getSubject(): string { return $this->subject; }
    public function getCategory(): string { return $this->category; }
    public function getMessage(): string { return $this->message; }
    public function getPriority(): string { return $this->priority; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // Setters
    public function setStatus(string $status): void { $this->status = $status; }
    public function setPriority(string $priority): void { $this->priority = $priority; }
}