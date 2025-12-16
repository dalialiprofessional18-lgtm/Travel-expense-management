<?php
namespace App\Models\Entities;

class Deplacement {
    private $id;
    private $user_id;
    private $titre;
    private $lieu_depart;  // NOUVEAU
    private $lieu;
    private $date_depart;
    private $date_retour;
    private $objet;
    private $created_at;
    private $updated_at;

    public function __construct( $id, $user_id, $titre, $lieu_depart='lieu', $lieu, $date_depart, $date_retour, $objet, $created_at = null, $updated_at = null ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->titre = $titre;
        $this->lieu_depart = $lieu_depart;
        $this->lieu = $lieu;
        $this->date_depart = $date_depart;
        $this->date_retour = $date_retour;
        $this->objet = $objet;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // ====================== GETTERS ======================
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getLieuDepart() {
        return $this->lieu_depart;
    }

    public function getLieu() {
        return $this->lieu;
    }

    public function getDateDepart() {
        return $this->date_depart;
    }

    public function getDateRetour() {
        return $this->date_retour;
    }

    public function getObjet() {
        return $this->objet;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    // ====================== SETTERS (اللي طلبتهم) ======================
    public function setTitre($titre) {
        $this->titre = $titre;
    }

    public function setLieuDepart($lieu_depart) {
        $this->lieu_depart = $lieu_depart;
    }

    public function setLieu($lieu) {
        $this->lieu = $lieu;
    }

    public function setDateDepart($date_depart) {
        $this->date_depart = $date_depart;
    }

    public function setDateRetour($date_retour) {
        $this->date_retour = $date_retour;
    }

    public function setObjet($objet) {
        $this->objet = $objet;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt($updated_at) {
        $this->updated_at = $updated_at;
    }

    // لو حابب تضيف setId و setUserId (عادةً منعملش setId برا الـ DAO)
    // بس لو عايز:
    // public function setId($id) { $this->id = $id; }
    // public function setUserId($user_id) { $this->user_id = $user_id; }
}