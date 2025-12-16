<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DAO\UserDAO;
use App\Models\Entities\User;
use App\Helpers\Auth;
use App\Services\EmailService;
use App\Models\DAO\EmailVerificationDAO;


class AuthController extends BaseController {
    private UserDAO $userDAO;
    private EmailVerificationDAO $verificationDAO;
    private EmailService $emailService;

    public function __construct() {
        $this->userDAO = new UserDAO();
                $this->verificationDAO = new EmailVerificationDAO();
        $this->emailService = new EmailService();
    }

    public function landing() {
        if ( Auth::check() ) {
            return $this->redirect( '/employee' );
        }
        $this->view( 'auth/landing' );
    }

    public function loginPage() {
        if ( Auth::check() ) {
            return $this->redirect( '/employee' );
        }
        $this->view( 'auth/login' );
    }

  public function resendCode()
    {
        if (!isset($_SESSION['verification_user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Session expirée']);
            exit;
        }

        $userId = $_SESSION['verification_user_id'];
        $user = $this->userDAO->findById($userId);

        $code = $this->verificationDAO->create($userId, 'registration');
        $sent = $this->emailService->sendVerificationCode(
            $user->getEmail(),
            $user->getNom(),
            $code
        );

        echo json_encode([
            'success' => $sent,
            'message' => $sent ? 'Code renvoyé avec succès' : 'Erreur lors de l\'envoi'
        ]);
        exit;
    }

    /**
     * Login - Vérifier si l'email est vérifié
     */
    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';


        $user = $this->userDAO->findByEmail($email);


        // Vérifier si l'email est vérifié
        $sql = "SELECT is_verified FROM users WHERE id = ?";
        $pdo = \App\Config\Database::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user->getId()]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);



        // Connexion réussie
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_nom'] = $user->getNom();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_role'] = $user->getRole();
        $_SESSION['success'] = 'Bienvenue ' . htmlspecialchars($user->getNom());

        return match ($user->getRole()) {
            'admin' => $this->redirect('/admin'),
            'manager' => $this->redirect('/manager'),
            default => $this->redirect('/employee'),
        };
    }


    public function registerPage() {
        if ( Auth::check() ) return $this->redirect( '/employee' );
        $this->view( 'auth/register' );
    }

       public function register()
    {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        // Validations
        // if (!$nom || !$email || !$password || !$confirm) {
        //     $_SESSION['error'] = 'Tous les champs sont obligatoires';
        //     return $this->redirect('/register');
        // }

        // if ($password !== $confirm) {
        //     $_SESSION['error'] = 'Les mots de passe ne correspondent pas';
        //     return $this->redirect('/register');
        // }

        // if (strlen($password) < 8) {
        //     $_SESSION['error'] = 'Mot de passe trop court (minimum 8 caractères)';
        //     return $this->redirect('/register');
        // }

        // if ($this->userDAO->findByEmail($email)) {
        //     $_SESSION['error'] = 'Email déjà utilisé';
        //     return $this->redirect('/register');
        // }
    if ($this->userDAO->existsByEmail($email)) {
    header("Location: /register");
    exit;
}


        // Créer l'utilisateur (non vérifié)
        $user = new User(
            null,
            $nom,
            $email,
            'employe',
            'inactif', // Statut inactif jusqu'à vérification
            password_hash($password, PASSWORD_DEFAULT)
        );
        $this->userDAO->insert($user);
                $userId = $this->userDAO->findByEmail($email)->getId();

        $this->userDAO->updateAvatar($userId,"https://i.ibb.co/TDyL0h3Q/2-avatar-1765720835.png");

        // Générer et envoyer le code
        $code = $this->verificationDAO->create($userId, 'registration');
        $emailSent = $this->emailService->sendVerificationCode($email, $nom, $code);

      

        // Stocker l'email dans la session pour la page de vérification
        $_SESSION['verification_email'] = $email;
        $_SESSION['verification_user_id'] = $userId;
        $_SESSION['success'] = 'Un code de vérification a été envoyé à votre email';
        
        return $this->redirect('/verify-email');
    }

    /**
     * Page de vérification d'email
     */
    public function verifyEmailPage()
    {
        if (!isset($_SESSION['verification_email'])) {
            return $this->redirect('/register');
        }

        $this->view('auth/verify-email', [
            'email' => $_SESSION['verification_email']
        ]);
    }

    /**
     * Vérifier le code
     */
    public function verifyEmail()
    {
        if (!isset($_SESSION['verification_user_id'])) {
            $_SESSION['error'] = 'Session expirée';
            return $this->redirect('/register');
        }

        $code = trim($_POST['code'] ?? '');
        $userId = $_SESSION['verification_user_id'];

        if (!$code || strlen($code) !== 6) {
            $_SESSION['error'] = 'Code invalide';
            return $this->redirect('/verify-email');
        }

        // Vérifier le code
        $isValid = $this->verificationDAO->verify($userId, $code, 'registration');

        if (!$isValid) {
            $_SESSION['error'] = 'Code incorrect ou expiré';
            return $this->redirect('/verify-email');
        }

        // Activer le compte
        $sql = "UPDATE users SET is_verified = TRUE  WHERE id = ?";
        $pdo = \App\Config\Database::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);

        // Nettoyer la session
        unset($_SESSION['verification_email']);
        unset($_SESSION['verification_user_id']);

        $_SESSION['success'] = 'Compte vérifié avec succès ! Vous pouvez maintenant vous connecter.';
        return $this->redirect('/login');
    }

    public function logout() {
        session_destroy();
        $_SESSION[ 'success' ] = 'Déconnexion réussie';
        return $this->redirect( '/login' );
    }
}