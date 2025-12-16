<?php
namespace App\Models\Entities;

class Message {
    private $id;
    private $conversation_id;
    private $sender_id;
    private $message;
    private $type;
    private $file_path;
    private $file_name;
    private $file_size;
    private $is_read;
    private $created_at;
    
    // Informations additionnelles
    private $sender_name;
    private $sender_avatar;
    
    // Getters et Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getConversationId() { return $this->conversation_id; }
    public function setConversationId($conversation_id) { $this->conversation_id = $conversation_id; }
    
    public function getSenderId() { return $this->sender_id; }
    public function setSenderId($sender_id) { $this->sender_id = $sender_id; }
    
    public function getMessage() { return $this->message; }
    public function setMessage($message) { $this->message = $message; }
    
    public function getType() { return $this->type; }
    public function setType($type) { $this->type = $type; }
    
    public function getFilePath() { return $this->file_path; }
    public function setFilePath($file_path) { $this->file_path = $file_path; }
    
    public function getFileName() { return $this->file_name; }
    public function setFileName($file_name) { $this->file_name = $file_name; }
    
    public function getFileSize() { return $this->file_size; }
    public function setFileSize($file_size) { $this->file_size = $file_size; }
    
    public function getIsRead() { return $this->is_read; }
    public function setIsRead($is_read) { $this->is_read = $is_read; }
    
    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    
    public function getSenderName() { return $this->sender_name; }
    public function setSenderName($sender_name) { $this->sender_name = $sender_name; }
    
    public function getSenderAvatar() { return $this->sender_avatar; }
    public function setSenderAvatar($sender_avatar) { $this->sender_avatar = $sender_avatar; }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->sender_id,
            'message' => $this->message,
            'type' => $this->type,
            'file_path' => $this->file_path,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
            'sender_name' => $this->sender_name,
            'sender_avatar' => $this->sender_avatar
        ];
    }
}