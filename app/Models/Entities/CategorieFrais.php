<?php
namespace App\Models\Entities;

class CategorieFrais {
    private $id;
    private $type;
    private $description;

    public function __construct( $id, $type, $description = null ) {
        $this->id = $id;
        $this->type = $type;
        $this->description = $description;
    }

    public function getId() {
        return $this->id;
    }

    public function getType() {
        return $this->type;
    }

    public function getDescription() {
        return $this->description;
    }
}
