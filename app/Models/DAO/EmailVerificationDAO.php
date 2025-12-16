<?php
namespace App\Models\DAO;

use App\Config\Database;
use PDO;

class EmailVerificationDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Créer un nouveau code de vérification
     */
    public function create(int $userId, string $type = 'registration'): string
    {
        // Générer un code à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Expire dans 15 minutes
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Invalider les anciens codes non utilisés
        $this->invalidateOldCodes($userId, $type);
        
        // Insérer le nouveau code
        $sql = "INSERT INTO email_verifications (user_id, code, type, expires_at) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $code, $type, $expiresAt]);
        
        return $code;
    }

    /**
     * Vérifier un code
     */
    public function verify(int $userId, string $code, string $type = 'registration'): bool
    {
        $sql = "SELECT id FROM email_verifications 
                WHERE user_id = ? 
                AND code = ? 
                AND type = ?
                AND is_used = FALSE 
                AND expires_at > NOW()
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $code, $type]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Marquer le code comme utilisé
            $this->markAsUsed($result['id']);
            return true;
        }
        
        return false;
    }

    /**
     * Marquer un code comme utilisé
     */
    private function markAsUsed(int $verificationId): void
    {
        $sql = "UPDATE email_verifications SET is_used = TRUE WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$verificationId]);
    }

    /**
     * Invalider les anciens codes non utilisés
     */
    private function invalidateOldCodes(int $userId, string $type): void
    {
        $sql = "UPDATE email_verifications 
                SET is_used = TRUE 
                WHERE user_id = ? 
                AND type = ? 
                AND is_used = FALSE";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $type]);
    }

    /**
     * Nettoyer les codes expirés (à exécuter via cron)
     */
    public function cleanExpiredCodes(): int
    {
        $sql = "DELETE FROM email_verifications WHERE expires_at < NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
