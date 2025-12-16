<?php
namespace App\Models\Entities;

class AssMessage
{
    private ?int $id;
    private int $conversationId;
    private string $role;
    private string $content;
    private ?array $metadata;
    private ?string $createdAt;

    public function __construct(
        ?int $id,
        int $conversationId,
        string $role,
        string $content,
        ?array $metadata = null,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->conversationId = $conversationId;
        $this->role = $role;
        $this->content = $content;
        $this->metadata = $metadata;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getConversationId(): int { return $this->conversationId; }
    public function getRole(): string { return $this->role; }
    public function getContent(): string { return $this->content; }
    public function getMetadata(): ?array { return $this->metadata; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
}
