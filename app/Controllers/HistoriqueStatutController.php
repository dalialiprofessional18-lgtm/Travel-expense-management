<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DAO\HistoriqueStatutDAO;
use App\Models\DAO\DeplacementDAO;
use App\Models\DAO\NoteFraisDAO;
use App\Helpers\Auth;
use App\Models\DAO\NotificationDAO;

class HistoriqueStatutController extends BaseController
 {
    private HistoriqueStatutDAO $dao;
    private DeplacementDAO $depDAO;
    private NoteFraisDAO $noteDAO;
    private NotificationDAO $notifDAO;

    public function __construct()
 {
        $this->notifDAO = new NotificationDAO();

        $this->dao = new HistoriqueStatutDAO();
        $this->depDAO = new DeplacementDAO();
        $this->noteDAO = new NoteFraisDAO();
    }

    public function showDeplacement( $deplacement_id )
 {
        $user = Auth::user();
        $userId = $user->getId();
        $dep = $this->depDAO->findById( $deplacement_id );
        if ( !$dep || !Auth::canManageDeplacement( $dep ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }

        $historiques = $this->dao->findByDeplacement( $deplacement_id );
        $this->view( 'historique/show', [
            'deplacement' => $dep,
            'historiques' => $historiques,
            'userId'   => $userId,
            'notifications'=>        $this->notifDAO->findByUser($userId)
        ] );
    }

    public function showNote( $note_id )
 {
        $note = $this->noteDAO->findById( $note_id );
        if ( !$note || !Auth::canAccessNote( $note ) ) {
            $_SESSION[ 'error' ] = 'Accès refusé';
            return $this->redirect( '/' );
        }
        $user = Auth::user();
        $userId = $user->getId();
        $historiques = $this->dao->findByNote( $note_id );
        $this->view( 'historique/show', [
            'note' => $note,
            'historiques' => $historiques,
            'userId'   => $userId,
            'notifications'=>        $this->notifDAO->findByUser($userId)

        ] );
    }
}