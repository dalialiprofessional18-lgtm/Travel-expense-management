<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DAO\ConversationDAO;
use App\Models\DAO\AssMessageDAO;
use App\Models\DAO\UserDAO;
use App\Models\DAO\DeplacementDAO;
use App\Models\DAO\NoteFraisDAO;
use App\Services\GeminiService;
use App\Helpers\Auth;
use App\Services\DataContextBuilder;

class AssistantController extends BaseController
 {
    private ConversationDAO $conversationDAO;
    private AssMessageDAO $messageDAO;
    private GeminiService $gemini;
    private UserDAO $userDAO;
    private DeplacementDAO $deplacementDAO;
    private DataContextBuilder $contextBuilder;

    private NoteFraisDAO $noteFraisDAO;

    public function __construct()
 {
        $this->contextBuilder = new DataContextBuilder();
        $this->conversationDAO = new ConversationDAO();
        $this->messageDAO = new AssMessageDAO();
        $this->gemini = new GeminiService();
        $this->userDAO = new UserDAO();
        $this->deplacementDAO = new DeplacementDAO();
        $this->noteFraisDAO = new NoteFraisDAO();
    }

    /**
    * GET /assistant - Page principale de l'assistant
     */
    public function index()
    {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = $user->getId();

        // Vérifier que l'utilisateur est admin ou manager
    if ( !Auth::hasRole( [ 'admin', 'manager' ] ) ) {
        $_SESSION[ 'error' ] = 'Accès refusé';
        return $this->redirect( '/' );
    }

    // Récupérer les conversations existantes
    $conversations = $this->conversationDAO->findByUser( $userId );

    // Récupérer des statistiques pour le contexte
    $stats = $this->getContextData( $user );

    $this->view( 'assistant/index', [
        'userId' => $userId,
        'conversations' => $conversations,
        'stats' => $stats,
        'avatarUrl' => $user->getAvatarUrl(),
        'coverUrl' => $user->getCoverUrl()
    ] );
}
 public function chat()
    {
        Auth::requireAuth();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $message = $data['message'] ?? '';
        $conversationId = $data['conversation_id'] ?? null;

        if (empty($message)) {
            echo json_encode(['success' => false, 'error' => 'Message vide']);
            exit;
        }

        $userId = Auth::user()->getId();

        try {
            // Créer une nouvelle conversation si besoin
            if (!$conversationId) {
                $conversationId = $this->conversationDAO->create($userId);
                
                // Générer un titre intelligent
                $title = $this->gemini->generateTitle($message);
                $this->conversationDAO->updateTitle($conversationId, $title);
            }

            // Sauvegarder le message de l'utilisateur
            $this->messageDAO->create($conversationId, 'user', $message);

            // Récupérer l'historique complet
            $messages = $this->messageDAO->findByConversation($conversationId);
            $history = array_map(function($msg) {
                return [
                    'role' => $msg->getRole(),
                    'content' => $msg->getContent()
                ];
            }, $messages);

            // Construire le contexte avec les données de l'entreprise
            $context = $this->buildContextPrompt(Auth::user());

            // Appeler Gemini
            $response = $this->gemini->chat($history, $context);

            // Sauvegarder la réponse de l'IA
            $this->messageDAO->create($conversationId, 'assistant', $response);

            echo json_encode([
                'success' => true,
                'conversation_id' => $conversationId,
                'response' => $response
            ]);

        } catch (\Exception $e) {
            error_log("Assistant Error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de la communication avec l\'IA'
            ]);
        }
        exit;
    }public function debugContext()
{
    Auth::requireAuth();
    $user = Auth::user();
    
    $contextBuilder = new DataContextBuilder();
    $context = $contextBuilder->buildContextForUser($user);
    
    header('Content-Type: application/json;
        charset = utf-8');
    echo json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
    /**
     * POST /assistant/chat - Envoyer un message à l'IA
        */

        /**
        * GET /assistant/conversations - Liste des conversations
        */

        public function conversations()
 {
            Auth::requireAuth();
            header( 'Content-Type: application/json' );

            $userId = Auth::user()->getId();
            $conversations = $this->conversationDAO->findByUser( $userId );

            $result = array_map( function( $conv ) {
                return [
                    'id' => $conv->getId(),
                    'title' => $conv->getTitle(),
                    'updated_at' => $conv->getUpdatedAt()
                ];
            }
            , $conversations );

            echo json_encode( [ 'success' => true, 'conversations' => $result ] );
            exit;
        }

        /**
        * GET /assistant/conversation/ {
            id}
            - Charger une conversation
            */

            public function loadConversation( $id )
 {
                Auth::requireAuth();
                header( 'Content-Type: application/json' );

                $conversation = $this->conversationDAO->findById( $id );

                if ( !$conversation || $conversation->getUserId() !== Auth::user()->getId() ) {
                    echo json_encode( [ 'success' => false, 'error' => 'Conversation introuvable' ] );
                    exit;
                }

                $messages = $this->messageDAO->findByConversation( $id );

                $result = [
                    'success' => true,
                    'conversation' => [
                        'id' => $conversation->getId(),
                        'title' => $conversation->getTitle()
                    ],
                    'messages' => array_map( function( $msg ) {
                        return [
                            'role' => $msg->getRole(),
                            'content' => $msg->getContent(),
                            'created_at' => $msg->getCreatedAt()
                        ];
                    }
                    , $messages )
                ];

                echo json_encode( $result );
                exit;
            }

            /**
            * GET /assistant/delete/ {
                id}
                - Supprimer une conversation
                */

                public function delete( $id )
 {
                    Auth::requireAuth();

                    $conversation = $this->conversationDAO->findById( $id );

                    if ( !$conversation || $conversation->getUserId() !== Auth::user()->getId() ) {
                        $_SESSION[ 'error' ] = 'Conversation introuvable';
                        return $this->redirect( '/assistant' );
                    }

                    $this->conversationDAO->delete( $id );

                    $_SESSION[ 'success' ] = 'Conversation supprimée';
                    return $this->redirect( '/assistant' );
                }

                /**
                * Construire le contexte pour l'IA avec les données de l'entreprise
                */

                private function buildContextPrompt( $user ): string
 {
                    $userId = $user->getId();
                    $role = $user->getRole();

                    $context = "Tu es un assistant IA pour le système de gestion de déplacements et notes de frais de l'entreprise.

**Informations sur l'utilisateur actuel :**
- Nom : {$user->getNom()}
- Rôle : {$role}
- ID : {$userId}

";

                    // Ajouter des statistiques selon le rôle
                    if ( $role === 'admin' ) {
                        $context .= $this->getAdminContext();
                    } elseif ( $role === 'manager' ) {
                        $context .= $this->getManagerContext( $userId );
                    }

                    $context .= "

**Tes capacités :**
- Analyser les données de déplacements et notes de frais
- Générer des rapports et statistiques
- Répondre aux questions sur les processus de validation
- Donner des recommandations basées sur les données
- Aider à identifier les tendances et anomalies

Réponds toujours en français, de manière professionnelle et structurée.
Utilise des listes à puces et des tableaux Markdown quand c'est pertinent.
Si tu ne peux pas répondre avec certitude, dis-le clairement.";

                    return $context;
                }

                /**
                * Contexte spécifique pour les admins
                */

                private function getAdminContext(): string
 {
                    // Statistiques globales
                    $totalUsers = $this->userDAO->countAll();
                    $totalDeplacements = count( $this->deplacementDAO->findAll() );
                    $totalNotes = $this->noteFraisDAO->countByStatus1();

                    return "**Statistiques globales (Admin) :**
- Nombre total d'utilisateurs : {$totalUsers}
- Nombre total de déplacements : {$totalDeplacements}
- Nombre total de notes de frais : {$totalNotes}
- Tu as accès à toutes les données de l'entreprise
";
                }

                /**
                * Contexte spécifique pour les managers
                */

                private function getManagerContext( $managerId ): string
 {
                    // Statistiques de l'équipe
        $teamSize = $this->userDAO->countTeamMembers($managerId);
        $pendingNotes = $this->noteFraisDAO->countByStatusForTeam($managerId, 'soumis');

        return "**Statistiques de votre équipe (Manager) :**
- Taille de l'équipe : {
                    $teamSize}
                    personnes
                    - Notes en attente de validation : {
                        $pendingNotes}
                        - Tu as accès aux données de ton équipe uniquement
                        ";
                    }

                    /**
                    * Récupérer les données de contexte pour affichage
                    */

                    private function getContextData( $user ): array
 {
                        $userId = $user->getId();
                        $role = $user->getRole();

                        $stats = [
                            'role' => $role,
                            'userName' => $user->getNom()
                        ];

                        if ( $role === 'admin' ) {
                            $stats[ 'totalUsers' ] = $this->userDAO->countAll();
                            $stats[ 'totalDeplacements' ] = count( $this->deplacementDAO->findAll() );
                            $stats[ 'totalNotes' ] = $this->noteFraisDAO->countByStatus1();
                        } elseif ( $role === 'manager' ) {
                            $stats[ 'teamSize' ] = $this->userDAO->countTeamMembers( $userId );
                            $stats[ 'pendingNotes' ] = $this->noteFraisDAO->countByStatusForTeam( $userId, 'soumis' );
                        }

                        return $stats;
                    }
                }