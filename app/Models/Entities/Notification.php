<?php
namespace App\Models\Entities;

class Notification
{
    private ?int $id = null;
    private int $userId;
    private string $title;
    private string $message;
    private string $type;
    private bool $isRead = false;
    private ?string $createdAt = null;
    private ?int $entityId = null;
    private ?string $entityType = null;
    private ?array $metadata = null; // ✅ NOUVEAU : Stocke commentaires, etc.

    public function __construct(array $data = [])
    {
        if ($data) {
            $this->hydrate($data);
        }
    }

    public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            // Traitement spécial pour metadata (JSON)
            if ($key === 'metadata' && is_string($value)) {
                $this->metadata = json_decode($value, true);
                continue;
            }
            
            $method = 'set' . ucfirst($this->camelCase($key));
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    private function camelCase(string $str): string
    {
        return lcfirst(str_replace('_', '', ucwords($str, '_')));
    }

    // === GETTERS ===
    public function getId(): ?int           { return $this->id; }
    public function getUserId(): int        { return $this->userId; }
    public function getTitle(): string      { return $this->title; }
    public function getMessage(): string    { return $this->message; }
    public function getType(): string       { return $this->type; }
    public function isRead(): bool          { return $this->isRead; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getEntityId(): ?int     { return $this->entityId; }
    public function getEntityType(): ?string { return $this->entityType; }
    public function getMetadata(): ?array   { return $this->metadata; }

    // ✅ NOUVEAUX : Getters pour commentaires
    public function getCommentaireManager(): ?string
    {
        return $this->metadata['commentaire_manager'] ?? null;
    }

    public function getCommentaireAdmin(): ?string
    {
        return $this->metadata['commentaire_admin'] ?? null;
    }

    public function getNoteStatut(): ?string
    {
        return $this->metadata['note_statut'] ?? null;
    }

    public function getMontant(): ?float
    {
        return $this->metadata['montant'] ?? null;
    }

    // === SETTERS ===
    public function setId(?int $id): void           { $this->id = $id; }
    public function setUserId(int $userId): void    { $this->userId = $userId; }
    public function setTitle(string $title): void   { $this->title = $title; }
    public function setMessage(string $message): void { $this->message = $message; }
    public function setType(string $type): void     { $this->type = $type; }
    public function setIsRead(bool|int $isRead): void { $this->isRead = (bool)$isRead; }
    public function setCreatedAt(?string $createdAt): void { $this->createdAt = $createdAt; }
    public function setEntityId(?int $entityId): void { $this->entityId = $entityId; }
    public function setEntityType(?string $entityType): void { $this->entityType = $entityType; }
    public function setMetadata(?array $metadata): void { $this->metadata = $metadata; }

    public function toArray(): array
    {
        return [
            'id'          => $this->id ?? 0,
            'user_id'     => $this->userId ?? 0,
            'title'       => $this->title ?? '',
            'message'     => $this->message ?? '',
            'type'        => $this->type ?? 'info',
            'is_read'     => $this->isRead ?? false,
            'created_at'  => $this->createdAt ?? date('Y-m-d H:i:s'),
            'entity_id'   => $this->entityId,
            'entity_type' => $this->entityType,
            'metadata'    => $this->metadata,
        ];
    }

    public function getUrl(): ?string
    {
        if (!$this->entityId || !$this->entityType) {
            return null;
        }
        
        switch ($this->entityType) {
            case 'note_frais':
                return "/notes/{$this->entityId}";
            case 'deplacement':
                return "/deplacements/{$this->entityId}";
            case 'message':
                return "/messagerie/{$this->entityId}";
            default:
                return null;
        }
    }
}
