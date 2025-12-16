<?php
namespace App\Models\Entities;

class Conversation
{
    private ?int $id;
    private int $userId;
    private string $title;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id,
        int $userId,
        string $title = 'Nouvelle conversation',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getTitle(): string { return $this->title; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
}
