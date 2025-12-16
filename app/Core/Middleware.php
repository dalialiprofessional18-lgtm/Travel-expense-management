<?php
namespace App\Core;

use App\Helpers\Auth;
use App\Models\DAO\DeplacementDAO;
use App\Models\DAO\NoteFraisDAO;

class Middleware
 {
    public static function requireAuth()
    {
        if ( !Auth::check() ) {
            $_SESSION[ 'error' ] = 'Connexion requise';
            header( 'Location: /login' );
            exit;
        }
    }

    public static function requireRole( $role )
 {
        self::requireAuth();
        if ( !Auth::hasRole( is_array( $role ) ? $role : [ $role ] ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            header( 'Location: /' );
            exit;
        }
    }

    public static function ownerOrManager( $userId )
 {
        self::requireAuth();
        $current = Auth::user();
        if ( $current->getId() != $userId && !in_array( $current->getRole(), [ 'manager', 'admin' ] ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            header( 'Location: /' );
            exit;
        }
    }

    public static function checkDeplacementAccess( $deplacementId )
 {
        self::requireAuth();
        $dao = new DeplacementDAO();
        $dep = $dao->findById( $deplacementId );
        if ( !$dep || !Auth::canManageDeplacement( $dep ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            header( 'Location: /' );
            exit;
        }
    }

    public static function checkDeplacementOwner( $deplacementId )
 {
        self::requireAuth();
        $dao = new DeplacementDAO();
        $dep = $dao->findById( $deplacementId );
        if ( !$dep || $dep->getUserId() !== Auth::id() ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            header( 'Location: /' );
            exit;
        }
    }

    public static function checkNoteAccess( $noteId )
 {
        self::requireAuth();
        $dao = new NoteFraisDAO();
        $note = $dao->findById( $noteId );
        if ( !$note || !Auth::canAccessNote( $note ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            header( 'Location: /' );
            exit;
        }
    }

    public static function checkNoteOwner( $noteId )
 {
        self::requireAuth();
        $dao = new NoteFraisDAO();
        $note = $dao->findById( $noteId );
        if ( !$note || $note->getUserId() !== Auth::id() ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            header( 'Location: /' );
            exit;
        }
    }
    public static function checkDetailOwner( $detailId )
 {
        $detailDAO = new \App\Models\DAO\DetailsFraisDAO();
        $detail = $detailDAO->findById( $detailId );

        if ( !$detail || $detail->getNoteId()->getUserId() !== Auth::user()?->getId() ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            header( 'Location: /' );
            exit;
        }
    }
}