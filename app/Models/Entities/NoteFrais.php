<?php

namespace App\Models\Entities;

class NoteFrais
{
    private $id;
    private $deplacement_id;
    private $user_id;
    private $statut;
    private $montant_total;

    private $commentaire;           // commentaire utilisateur
    private $commentaire_manager;   // commentaire manager
    private $commentaire_admin;     // commentaire admin

    private $created_at;
    private $updated_at;

    public function __construct(
        $id,
        $deplacement_id,
        $user_id,
        $statut = 'brouillon',
        $montant_total = 0,
        $commentaire = 0,
        $commentaire_manager = null,
        $commentaire_admin = null,
        $created_at = null,
        $updated_at = null
    ) {
        $this->id = $id;
        $this->deplacement_id = $deplacement_id;
        $this->user_id = $user_id;
        $this->statut = $statut;
        $this->montant_total = $montant_total;

        $this->commentaire = $commentaire;
        $this->commentaire_manager = $commentaire_manager;
        $this->commentaire_admin = $commentaire_admin;

        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // ---------------- GETTERS ----------------

    public function getId()
    {
        return $this->id;
    }

    public function getDeplacementId()
    {
        return $this->deplacement_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function getMontantTotal()
    {
        return $this->montant_total;
    }

    public function getCommentaire()
    {
        return $this->commentaire;
    }

    public function getCommentaireManager()
    {
        return $this->commentaire_manager;
    }

    public function getCommentaireAdmin()
    {
        return $this->commentaire_admin;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    // ---------------- SETTERS ----------------

    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function setCommentaireManager($commentaire_manager)
    {
        $this->commentaire_manager = $commentaire_manager;
        return $this;
    }

    public function setCommentaireAdmin($commentaire_admin)
    {
        $this->commentaire_admin = $commentaire_admin;
        return $this;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
        return $this;
    }

    public function setMontantTotal($montant_total)
    {
        $this->montant_total = $montant_total;
        return $this;
    }
}
