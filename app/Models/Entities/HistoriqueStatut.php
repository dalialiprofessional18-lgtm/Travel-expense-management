<?php
namespace App\Models\Entities;

class HistoriqueStatut {
    private $id;
    private $deplacement_id;
    private $note_id;
    private $ancien_statut;
    private $nouveau_statut;
    private $changed_by;
    private $commentaire;
    private $created_at;
    
    // Nouvelles propriétés pour les infos utilisateur
    private $user_nom;
    private $user_role;

    public function __construct(
        $id = null,
        $deplacement_id = null,
        $note_id = null,
        $ancien_statut = null,
        $nouveau_statut = null,
        $changed_by = null,
        $commentaire = null,
        $created_at = null
    ) {
        $this->id = $id;
        $this->deplacement_id = $deplacement_id;
        $this->note_id = $note_id;
        $this->ancien_statut = $ancien_statut;
        $this->nouveau_statut = $nouveau_statut;
        $this->changed_by = $changed_by;
        $this->commentaire = $commentaire;
        $this->created_at = $created_at;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getDeplacementId() {
        return $this->deplacement_id;
    }

    public function getNoteId() {
        return $this->note_id;
    }

    public function getAncienStatut() {
        return $this->ancien_statut;
    }

    public function getNouveauStatut() {
        return $this->nouveau_statut;
    }

    public function getChangedBy() {
        return $this->changed_by;
    }

    public function getCommentaire() {
        return $this->commentaire;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
    
    public function getUserNom() {
        return $this->user_nom;
    }
    
    public function getUserRole() {
        return $this->user_role;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setDeplacementId($deplacement_id) {
        $this->deplacement_id = $deplacement_id;
    }

    public function setNoteId($note_id) {
        $this->note_id = $note_id;
    }

    public function setAncienStatut($ancien_statut) {
        $this->ancien_statut = $ancien_statut;
    }

    public function setNouveauStatut($nouveau_statut) {
        $this->nouveau_statut = $nouveau_statut;
    }

    public function setChangedBy($changed_by) {
        $this->changed_by = $changed_by;
    }

    public function setCommentaire($commentaire) {
        $this->commentaire = $commentaire;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
    
    public function setUserNom($user_nom) {
        $this->user_nom = $user_nom;
    }
    
    public function setUserRole($user_role) {
        $this->user_role = $user_role;
    }
    
    // Méthode helper pour obtenir le nom d'affichage
    public function getDisplayName() {
        if ($this->user_nom) {
            return $this->user_nom;
        }
        return 'Système';
    }
    
    // Méthode helper pour obtenir le rôle formaté
    public function getDisplayRole() {
        if (!$this->user_role) {
            return 'Automatique';
        }
        
        $roles = [
            'employee' => 'Employé',
            'manager' => 'Manager',
            'admin' => 'Administrateur'
        ];
        
        return $roles[$this->user_role] ?? ucfirst($this->user_role);
    }
}