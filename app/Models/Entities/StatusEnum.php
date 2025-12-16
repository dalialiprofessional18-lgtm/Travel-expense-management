<?php
namespace App\Models\Entities;
class Statut {
    const BROUILLON        = 'brouillon';
    const SOUMIS           = 'soumis';
    const VALIDE_MANAGER   = 'valide_manager';
    const REJETEE_MANAGER  = 'rejetee_manager';
    const EN_COURS_ADMIN   = 'en_cours_admin';
    const APPROUVE         = 'approuve';
    const REJETEE_ADMIN    = 'rejetee_admin';

    public static function all() {
        return [
            self::BROUILLON,
            self::SOUMIS,
            self::VALIDE_MANAGER,
            self::REJETEE_MANAGER,
            self::EN_COURS_ADMIN,
            self::APPROUVE,
            self::REJETEE_ADMIN,
        ];
    }
}
