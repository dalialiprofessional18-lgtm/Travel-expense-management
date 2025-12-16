<?php
namespace App\Models\Entities;

class User {

    private $id;
    private $nom;
    private $email;
    private $role;
    private $job_title;
    private $experience_details;
    private $mot_de_passe;
    private $manager_id;

    private $avatar_path;
    private $avatar_mime;
    private $avatar_size;

    private $cover_path;
    private $cover_mime;
    private $cover_size;
    private  $statut;
    private  $is_verified;
    private $created_at;
    private $updated_at;

    

    // === Constructeur principal ( login / registration ) ===

    public function __construct(
        $id,
        $nom,
        $email,
        $role,
        $statut ='actif',
        $mot_de_passe,
        $is_verified=false,
        $manager_id = null,
        $job_title = null,
        $experience_details = null,
        $created_at = null,
        $updated_at = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->role = $role;
        $this->statut =  $statut;
   $this->is_verified =$is_verified;
        ;
        $this->mot_de_passe = $mot_de_passe;
        $this->manager_id = $manager_id;

        // 🆕 ajout des 2 propriétés manquantes
        $this->job_title = $job_title;
        $this->experience_details = $experience_details;

        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // === Second constructeur ( full DB object ) ===
    public static function constructFull(
        $id,
        $nom,
        $email,
        $role,
        $mot_de_passe,
        $manager_id,
        $avatar_path,
        $avatar_mime,
        $avatar_size,
        $cover_path,
        $cover_mime,
        $cover_size,
        $created_at,
        $updated_at
    ) {
        $user = new self(
            $id,
            $nom,
            $email,
            $role,
            $mot_de_passe,
            $manager_id,
            $created_at,
            $updated_at
        );

        $user->avatar_path = $avatar_path;
        $user->avatar_mime = $avatar_mime;
        $user->avatar_size = $avatar_size;

        $user->cover_path = $cover_path;
        $user->cover_mime = $cover_mime;
        $user->cover_size = $cover_size;

        return $user;
    }

    // === GETTERS ===

    public function getId() {
        return $this->id;
    }

    public function getNom() {
        return $this->nom;
    } 
    public function isVerified() {
        return $this->is_verified;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }

    public function getPassword() {
        return $this->mot_de_passe;
    }

    public function getManagerId() {
        return $this->manager_id;
    }

    public function getJobTitle() {
        return $this->job_title;
    }

    public function getExperienceDetails() {
        return $this->experience_details;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    // Avatar getters

    public function getAvatarPath() {
        return $this->avatar_path;
    }

    public function getAvatarMime() {
        return $this->avatar_mime;
    }

    public function getAvatarSize() {
        return $this->avatar_size;
    }

    public function getAvatarUrl() {
            return $this->avatar_path;
        


    }

    // Cover getters

    public function getCoverPath() {
        return $this->cover_path;
    }

    public function getCoverMime() {
        return $this->cover_mime;
    }

    public function getCoverSize() {
        return $this->cover_size;
    }

    public function getCoverUrl() {
        if ( $this->cover_path ) {
            return $this->cover_path;
        }

        // sinon, on teste les chemins
        if ( file_exists( './assets/icons/cover-01.png' ) ) {
            return './assets/icons/cover-01.png';
        }

        if ( file_exists( '../../assets/icons/cover-01.png' ) ) {
            return '../../assets/icons/cover-01.png';
        }

        return '../assets/icons/cover-01.png';
    }

    // === SETTERS ===

    public function setId( $id ) {
        $this->id = $id;
    }

    public function setNom( $nom ) {
        $this->nom = $nom;
    }

    public function setEmail( $email ) {
        $this->email = $email;
    }

    public function setRole( $role ) {
        $this->role = $role;
    }

    public function setPassword( $password ) {
        $this->mot_de_passe = $password;
    }

    public function setManagerId( $manager_id ) {
        $this->manager_id = $manager_id;
    }

    public function setJobTitle( $job_title ) {
        $this->job_title = $job_title;
    }

    public function setExperienceDetails( $experience_details ) {
        $this->experience_details = $experience_details;
    }

    public function setCreatedAt( $created_at ) {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt( $updated_at ) {
        $this->updated_at = $updated_at;
    }

    // Avatar setter

    public function setAvatar( $path, $mime = null, $size = null ) {
        $this->avatar_path = $path;
        $this->avatar_mime = $mime;
        $this->avatar_size = $size;
    }

    // Cover setter

    public function setCover( $path, $mime = null, $size = null ) {
        $this->cover_path = $path;
        $this->cover_mime = $mime;
        $this->cover_size = $size;
    }
}
