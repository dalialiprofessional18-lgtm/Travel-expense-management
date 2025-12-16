<?php
namespace App\Helpers;

use App\Models\DAO\UserDAO;
use App\Models\Entities\User;

class Auth {
 private static ?array $userData = null;

public static function check(): bool
{
    return isset($_SESSION['user_id']);
}

public static function user(): ?User
{
    if (!self::check()) {
        return null;
    }

    if (self::$userData === null) {
        // احفظ البيانات في session من الـ login مرة واحدة بس
        self::$userData = [
            'id' => $_SESSION['user_id'],
            'nom' => $_SESSION['user_nom'] ?? 'Inconnu',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'employe',
        ];
    }

    return new User(
        self::$userData['id'],
        self::$userData['nom'],
        self::$userData['email'],
        self::$userData['role'],
        null, // password
        null  // manager_id
    );
}
    /**
     * Retourne l'ID de l'utilisateur connecté
     */
    public static function id(): ?int
    {
        return  $_SESSION['user_id'] ?? null;
    }

    /**
     * Vérifie si l'utilisateur a un rôle précis
    */
    public static function isRole( string $role ): bool {
        $user = self::user();
        return $user && $user->getRole() === $role;
    }

    /**
    * Vérifie si l'utilisateur a l'un des rôles donnés
    */
    public static function hasRole( array $roles ): bool {
        $user = self::user();
        return $user && in_array( $user->getRole(), $roles );
    }

    /**
    * Exige une connexion ( sinon redirige )
    */
    public static function requireAuth(): void {
        if ( !self::check() ) {
            $_SESSION[ 'error' ] = 'Vous devez être connecté';
            header( 'Location: /login' );
            exit;
        }
    }

    /**
    * Exige un rôle précis ( sinon redirige )
    */
    public static function requireRole( string $role ): void {
        self::requireAuth();
        if ( !self::isRole( $role ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            header( 'Location: /' );
            exit;
        }
    }

    /**
    * Exige un des rôles ( admin, manager, etc. )
    */
    public static function requireRoles( array $roles ): void {
        self::requireAuth();
        if ( !self::hasRole( $roles ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            header( 'Location: /' );
            exit;
        }
    }

    /**
    * Vérifie si l'utilisateur peut accéder à un déplacement
     * (propriétaire OU manager OU admin)
     */
    public static function canManageDeplacement($deplacement): bool
    {
        if (!$deplacement) return false;
        $user = self::user();
        if (!$user) return false;

        return $user->getId() == $deplacement->getUserId() ||
               in_array($user->getRole(), ['manager', 'admin']);
    }

    /**
     * Vérifie si l'utilisateur peut accéder à une note de frais
    */
    public static function canAccessNote( $note ): bool {
        if ( !$note ) return false;
        $user = self::user();
        if ( !$user ) return false;

        return $user->getId() == $note->getUserId() ||
        in_array( $user->getRole(), [ 'manager', 'admin' ] );
    }

    /**
    * Vérifie si l'utilisateur est le propriétaire d'une ressource
    */
    public static function isOwner( int $ownerId ): bool {
        $user = self::user();
        return $user && $user->getId() == $ownerId;
    }

    /**
    * Raccourci : admin ?
    */
    public static function isAdmin(): bool {
        return self::isRole( 'admin' );
    }

    /**
    * Raccourci : manager ?
    */
    public static function isManager(): bool {
        return self::isRole( 'manager' );
    }

    /**
    * Raccourci : employé ?
    */
    public static function isEmploye(): bool {
        return self::isRole( 'employe' );
    }
}