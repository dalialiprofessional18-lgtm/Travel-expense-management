<?php
namespace App\Models\Entities;

class Role {
    const EMPLOYE = 'employe';
    const MANAGER = 'manager';
    const ADMIN   = 'admin';

    public static function all() {
        return [
            self::EMPLOYE,
            self::MANAGER,
            self::ADMIN
        ];
    }
}
