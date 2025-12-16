<?php

namespace App\Core;

class BaseController {
    /**
    * Charger une vue
    * Exemple : $this->view( 'employee/dashboard', [ 'user' => $user ] );
    */

    public function view( $view, $data = [] ) {
        extract( $data );

        $viewFile = "../app/views/$view.php";
        if ( !file_exists( $viewFile ) ) {
            die( "Vue introuvable : $viewFile" );
        }
        if ( $view == 'auth/login' || $view == 'auth/register'|| $view == 'auth/landing'|| $view == 'auth/verify-email'|| $view == 'assistant/debug' ) {

            require $viewFile;
        } else {
            require '../app/views/_layouts/header.php';
            require $viewFile;
            require '../app/views/_layouts/footer.php';

        }

    }

    /**
    * Redirection simple
    */

    public function redirect( $path ) {
        $base = 'http://localhost';
        // فقط localhost كما طلبت

        // إزالة أي duplicate للسلاش
        $path = '/' . ltrim( $path, '/' );

        header( 'Location: ' . $base . $path );
        exit;
    }

    /**
    * Définir un message flash
    */

    public function flash( $key, $message ) {
        $_SESSION[ 'flash' ][ $key ] = $message;
    }

    /**
    * Récupérer un message flash
    */

    public function getFlash( $key ) {
        if ( isset( $_SESSION[ 'flash' ][ $key ] ) ) {
            $msg = $_SESSION[ 'flash' ][ $key ];
            unset( $_SESSION[ 'flash' ][ $key ] );
            return $msg;
        }
        return null;
    }
}
