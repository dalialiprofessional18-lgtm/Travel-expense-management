<?php
// app/Controllers/SupportController.php

namespace App\Controllers;

use App\Core\BaseController;
use App\Helpers\Auth;
use App\Models\DAO\NotificationDAO;
use App\Models\DAO\SupportTicketDAO;
use App\Models\Entities\SupportTicket;

class SupportController extends BaseController
{
    private NotificationDAO $notifDAO;
    private SupportTicketDAO $ticketDAO;

    public function __construct()
    {
        $this->notifDAO = new NotificationDAO();
        $this->ticketDAO = new SupportTicketDAO();
    }

    // Page principale Support & Help
    public function index()
    {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = $user->getId();

        // Récupérer les tickets de l'utilisateur
        $tickets = $this->ticketDAO->findByUser($userId);
        $notifications = $this->notifDAO->findByUser($userId);

        $this->view('support/index', [
            'userId' => $userId,
            'tickets' => $tickets,
            'notifications' => $notifications,
            'pageTitle' => 'Support & Help'
        ]);
    }

    // Créer un nouveau ticket
    public function createTicket()
    {
        Auth::requireAuth();
        $user = Auth::user();

        $subject = trim($_POST['subject'] ?? '');
        $category = trim($_POST['category'] ?? 'general');
        $message = trim($_POST['message'] ?? '');
        $priority = $_POST['priority'] ?? 'normal';

        if (!$subject || !$message) {
            $_SESSION['error'] = 'Le sujet et le message sont requis';
            return $this->redirect('/support');
        }

        $ticket = new SupportTicket(
            null,
            $user->getId(),
            $subject,
            $category,
            $message,
            $priority,
            'open'
        );

        if ($this->ticketDAO->insert($ticket)) {
            $_SESSION['success'] = 'Votre ticket a été créé avec succès !';
        } else {
            $_SESSION['error'] = 'Erreur lors de la création du ticket';
        }

        return $this->redirect('/support');
    }

    // Voir mes tickets
    public function myTickets()
    {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = $user->getId();

        $tickets = $this->ticketDAO->findByUser($userId);
        $notifications = $this->notifDAO->findByUser($userId);

        $this->view('support/tickets', [
            'userId' => $userId,
            'tickets' => $tickets,
            'notifications' => $notifications,
            'pageTitle' => 'Mes Tickets'
        ]);
    }

    // Voir un ticket spécifique
    public function viewTicket($id)
    {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = $user->getId();

        $ticket = $this->ticketDAO->findById($id);
        
        if (!$ticket || ($ticket->getUserId() !== $userId && $user->getRole() !== 'admin')) {
            $_SESSION['error'] = 'Accès refusé';
            return $this->redirect('/support');
        }

        $replies = $this->ticketDAO->getReplies($id);
        $notifications = $this->notifDAO->findByUser($userId);

        $this->view('support/view', [
            'userId' => $userId,
            'ticket' => $ticket,
            'replies' => $replies,
            'notifications' => $notifications,
            'pageTitle' => 'Ticket #' . $id
        ]);
    }

    // Répondre à un ticket
    public function replyTicket($id)
    {
        Auth::requireAuth();
        $user = Auth::user();

        $message = trim($_POST['message'] ?? '');

        if (!$message) {
            $_SESSION['error'] = 'Le message ne peut pas être vide';
            return $this->redirect("/support/ticket/{$id}");
        }

        $ticket = $this->ticketDAO->findById($id);
        
        if (!$ticket || ($ticket->getUserId() !== $user->getId() && $user->getRole() !== 'admin')) {
            $_SESSION['error'] = 'Accès refusé';
            return $this->redirect('/support');
        }

        if ($this->ticketDAO->addReply($id, $user->getId(), $message)) {
            $_SESSION['success'] = 'Votre réponse a été ajoutée';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'envoi de la réponse';
        }

        return $this->redirect("/support/ticket/{$id}");
    }

    // Page admin pour gérer tous les tickets
    public function adminIndex()
    {
        Auth::requireAuth();
        $user = Auth::user();
        
        if ($user->getRole() !== 'admin') {
            $_SESSION['error'] = 'Accès refusé';
            return $this->redirect('/');
        }

        $tickets = $this->ticketDAO->findAll();
        $notifications = $this->notifDAO->findByUser($user->getId());

        $this->view('support/admin', [
            'userId' => $user->getId(),
            'tickets' => $tickets,
            'notifications' => $notifications,
            'pageTitle' => 'Gestion des Tickets'
        ]);
    }

    // Fermer un ticket (Admin)
    public function closeTicket($id)
    {
        Auth::requireAuth();
        $user = Auth::user();
        
        if ($user->getRole() !== 'admin') {
            $_SESSION['error'] = 'Accès refusé';
            return $this->redirect('/');
        }

        if ($this->ticketDAO->updateStatus($id, 'closed')) {
            $_SESSION['success'] = 'Ticket fermé avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la fermeture du ticket';
        }

        return $this->redirect('/admin/support');
    }
}