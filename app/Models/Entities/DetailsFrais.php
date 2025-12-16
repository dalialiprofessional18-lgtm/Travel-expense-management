<?php
namespace App\Models\Entities;

class DetailsFrais {
    private $id;
    private $note_id;
    private $categorie_id;
    private $description;
    private $date_frais;
    private $montant_veloce;
    private $montant_personnel;
    private $montant_total;
    private $justificatif_path;
    private $justificatif_mime;
    private $justificatif_size;
    private $created_at;
    private $updated_at;
    // AJOUTÉ : pour détecter les modifications
    private $randomId;
    // Corrigé : plus de $

    private $categorie = null;

    public function __construct(
        $id = null,
        $note_id = null,
        $categorie_id = null,
        $description = null,
        $date_frais = null,
        $montant_veloce = 0,
        $montant_personnel = 0,
        $montant_total = null,
        $justificatif_path = null,
        $justificatif_mime = null,
        $justificatif_size = null,
        $created_at = null,
        $updated_at = null,      // Nouveau paramètre
        $randomId = null
    ) {
        $this->id = $id;
        $this->note_id = $note_id;
        $this->categorie_id = $categorie_id;
        $this->description = $description;
        $this->date_frais = $date_frais;
        $this->montant_veloce = $montant_veloce;
        $this->montant_personnel = $montant_personnel;
        $this->montant_total = $montant_total ?? ( $montant_veloce + $montant_personnel );
        $this->justificatif_path = $justificatif_path;
        $this->justificatif_mime = $justificatif_mime;
        $this->justificatif_size = $justificatif_size;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        // Initialisé
        $this->randomId = $randomId;
    }

    // ===  ===  ===  ===  ===  ===  == GETTERS ===  ===  ===  ===  ===  ===  ==

    public function getId() {
        return $this->id;
    }

    public function getNoteId() {
        return $this->note_id;
    }

    public function getCategorieId() {
        return $this->categorie_id;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getDateFrais() {
        return $this->date_frais;
    }

    public function getMontantVeloce() {
        return $this->montant_veloce;
    }

    public function getMontantPersonnel() {
        return $this->montant_personnel;
    }

    public function getMontantTotal() {
        return $this->montant_total;
    }

    public function getJustificatifPath() {
        return $this->justificatif_path;
    }

    public function getJustificatifMime() {
        return $this->justificatif_mime;
    }

    public function getJustificatifSize() {
        return $this->justificatif_size;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }
    // NOUVEAU

    public function getRandomId() {
        // Si pas déjà généré → on le crée une seule fois
        if ( $this->randomId === null ) {
            $this->randomId = bin2hex( random_bytes( 16 ) );
            // 32 caractères uniques
        }
        return $this->randomId;
    }

    public function getCategorie() {
        if ( $this->categorie === null && $this->categorie_id !== null ) {
            $dao = new \App\Models\DAO\CategorieFraisDAO();
            $this->categorie = $dao->findById( $this->categorie_id );
        }
        return $this->categorie;
    }

    // ===  ===  ===  ===  ===  ===  == SETTERS ===  ===  ===  ===  ===  ===  ==

    public function setId( $id ) {
        $this->id = $id;
    }

    public function setNoteId( $note_id ) {
        $this->note_id = $note_id;
    }

    public function setCategorieId( $categorie_id ) {
        $this->categorie_id = $categorie_id;
        $this->categorie = null;
        // invalidate cache
    }

    public function setDescription( $description ) {
        $this->description = $description;
    }

    public function setDateFrais( $date_frais ) {
        $this->date_frais = $date_frais;
    }

    public function setMontantVeloce( $montant_veloce ) {
        $this->montant_veloce = $montant_veloce;
        $this->montant_total = $this->montant_veloce + $this->montant_personnel;
    }

    public function setMontantPersonnel( $montant_personnel ) {
        $this->montant_personnel = $montant_personnel;
        $this->montant_total = $this->montant_veloce + $this->montant_personnel;
    }

    public function setMontantTotal( $montant_total ) {
        $this->montant_total = $montant_total;
    }

    public function setJustificatifPath( $path ) {
        $this->justificatif_path = $path;
    }

    public function setJustificatifMime( $mime ) {
        $this->justificatif_mime = $mime;
    }

    public function setJustificatifSize( $size ) {
        $this->justificatif_size = $size;
    }

    public function setCreatedAt( $created_at ) {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt( $updated_at ) {
        $this->updated_at = $updated_at;
    }
    // NOUVEAU

    public function setRandomId( $randomId ) {
        $this->randomId = $randomId;
    }

    public function setCategorie( $categorie ) {
        $this->categorie = $categorie;
        $this->categorie_id = $categorie ? $categorie->getId() : null;
    }
}