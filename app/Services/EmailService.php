<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;
    
    // ⚠️ À CONFIGURER AVEC VOS INFORMATIONS
    private string $smtpHost = 'smtp.gmail.com';
    private int $smtpPort = 587;
    private string $smtpUsername = 'bokarrobak@gmail.com'; // CHANGEZ ICI
    private string $smtpPassword = 'vqqn ehyo zpit ykbp'; // CHANGEZ ICI (mot de passe d'application)
    private string $fromEmail = 'bokarrobak@gmail.com'; // CHANGEZ ICI
    private string $fromName = 'SGFD - Gestion de Frais';

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure(): void
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->smtpHost;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->smtpUsername;
            $this->mailer->Password = $this->smtpPassword;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $this->smtpPort;
            $this->mailer->setFrom($this->fromEmail, $this->fromName);
            $this->mailer->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("❌ Erreur config email: " . $e->getMessage());
        }
    }
  public function sendNoteApprovedEmail(
        string $toEmail,
        string $employeName,
        int $noteId,
        string $destination,
        float $montant,
        string $commentaire
    ): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '✅ Note de frais approuvée - #' . $noteId;
            
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>✅ Note de frais approuvée</h1>
                    </div>
                    
                    <div style='padding: 30px; background-color: #f9f9f9;'>
                        <p style='font-size: 16px;'>Bonjour <strong>{$employeName}</strong>,</p>
                        
                        <p>Bonne nouvelle ! Votre note de frais a été <strong style='color: #10b981;'>approuvée par l'administration</strong>.</p>
                        
                        <div style='background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                            <h3 style='color: #667eea; margin-top: 0;'>📋 Détails de la note</h3>
                            <p><strong>Numéro :</strong> #{$noteId}</p>
                            <p><strong>Destination :</strong> {$destination}</p>
                            <p><strong>Montant total :</strong> <span style='font-size: 18px; color: #10b981; font-weight: bold;'>" . number_format($montant, 2) . " DH</span></p>
                            <p><strong>Commentaire admin :</strong><br><em>{$commentaire}</em></p>
                        </div>
                        
                        <p style='color: #10b981; font-weight: bold;'>Le remboursement sera traité prochainement.</p>
                        
                        <div style='text-align: center; margin-top: 30px;'>
                            <a href='http://votre-site.com/employee' style='background-color: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                                Voir mes notes de frais
                            </a>
                        </div>
                    </div>
                    
                    <div style='text-align: center; padding: 20px; color: #666; font-size: 12px;'>
                        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                    </div>
                </div>
            ";
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erreur email approbation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Note rejetée par l'admin
     */
    public function sendNoteRejectedEmail(
        string $toEmail,
        string $employeName,
        int $noteId,
        string $destination,
        float $montant,
        string $motif
    ): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '❌ Note de frais rejetée - #' . $noteId;
            
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>❌ Note de frais rejetée</h1>
                    </div>
                    
                    <div style='padding: 30px; background-color: #f9f9f9;'>
                        <p style='font-size: 16px;'>Bonjour <strong>{$employeName}</strong>,</p>
                        
                        <p>Votre note de frais a été <strong style='color: #ef4444;'>rejetée par l'administration</strong>.</p>
                        
                        <div style='background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                            <h3 style='color: #f43f5e; margin-top: 0;'>📋 Détails de la note</h3>
                            <p><strong>Numéro :</strong> #{$noteId}</p>
                            <p><strong>Destination :</strong> {$destination}</p>
                            <p><strong>Montant :</strong> " . number_format($montant, 2) . " DH</p>
                            
                            <div style='background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin-top: 15px;'>
                                <p style='margin: 0;'><strong>Motif du rejet :</strong></p>
                                <p style='margin: 5px 0 0 0;'>{$motif}</p>
                            </div>
                        </div>
                        
                        <p>Si vous pensez qu'il s'agit d'une erreur, veuillez contacter votre responsable ou l'administration.</p>
                        
                        <div style='text-align: center; margin-top: 30px;'>
                            <a href='http://votre-site.com/employee' style='background-color: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                                Voir mes notes de frais
                            </a>
                        </div>
                    </div>
                    
                    <div style='text-align: center; padding: 20px; color: #666; font-size: 12px;'>
                        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                    </div>
                </div>
            ";
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erreur email rejet: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Décision révoquée par l'admin
     */
    public function sendDecisionRevokedEmail(
        string $toEmail,
        string $employeName,
        int $noteId,
        string $destination,
        string $motif
    ): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '🔄 Décision révoquée - Note #' . $noteId;
            
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>🔄 Décision révoquée</h1>
                    </div>
                    
                    <div style='padding: 30px; background-color: #f9f9f9;'>
                        <p style='font-size: 16px;'>Bonjour <strong>{$employeName}</strong>,</p>
                        
                        <p>La décision concernant votre note de frais a été <strong style='color: #f59e0b;'>révoquée par l'administration</strong>.</p>
                        
                        <div style='background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                            <h3 style='color: #f59e0b; margin-top: 0;'>📋 Détails</h3>
                            <p><strong>Numéro :</strong> #{$noteId}</p>
                            <p><strong>Destination :</strong> {$destination}</p>
                            
                            <div style='background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin-top: 15px;'>
                                <p style='margin: 0;'><strong>Motif de la révocation :</strong></p>
                                <p style='margin: 5px 0 0 0;'>{$motif}</p>
                            </div>
                        </div>
                        
                        <p>Votre note est de nouveau en attente de validation.</p>
                        
                        <div style='text-align: center; margin-top: 30px;'>
                            <a href='http://votre-site.com/employee' style='background-color: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                                Voir mes notes de frais
                            </a>
                        </div>
                    </div>
                    
                    <div style='text-align: center; padding: 20px; color: #666; font-size: 12px;'>
                        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                    </div>
                </div>
            ";
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erreur email révocation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Note validée par le manager
     */
    public function sendNoteValidatedByManagerEmail(
        string $toEmail,
        string $employeName,
        string $managerName,
        int $noteId,
        string $destination,
        float $montant,
        string $commentaire
    ): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '✅ Note validée par votre manager - #' . $noteId;
            
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>✅ Note validée par votre manager</h1>
                    </div>
                    
                    <div style='padding: 30px; background-color: #f9f9f9;'>
                        <p style='font-size: 16px;'>Bonjour <strong>{$employeName}</strong>,</p>
                        
                        <p>Votre note de frais a été <strong style='color: #3b82f6;'>validée par {$managerName}</strong> et est maintenant en attente d'approbation administrative.</p>
                        
                        <div style='background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                            <h3 style='color: #3b82f6; margin-top: 0;'>📋 Détails</h3>
                            <p><strong>Numéro :</strong> #{$noteId}</p>
                            <p><strong>Destination :</strong> {$destination}</p>
                            <p><strong>Montant :</strong> " . number_format($montant, 2) . " DH</p>
                            <p><strong>Commentaire :</strong><br><em>{$commentaire}</em></p>
                        </div>
                        
                        <p style='color: #3b82f6;'>Votre demande sera traitée prochainement par l'administration.</p>
                    </div>
                </div>
            ";
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erreur email validation manager: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Note rejetée par le manager
     */
    public function sendNoteRejectedByManagerEmail(
        string $toEmail,
        string $employeName,
        string $managerName,
        int $noteId,
        string $destination,
        float $montant,
        string $commentaire
    ): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '❌ Note rejetée par votre manager - #' . $noteId;
            
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>❌ Note rejetée par votre manager</h1>
                    </div>
                    
                    <div style='padding: 30px; background-color: #f9f9f9;'>
                        <p style='font-size: 16px;'>Bonjour <strong>{$employeName}</strong>,</p>
                        
                        <p>Votre note de frais a été <strong style='color: #ef4444;'>rejetée par {$managerName}</strong>.</p>
                        
                        <div style='background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                            <h3 style='color: #ef4444; margin-top: 0;'>📋 Détails</h3>
                            <p><strong>Numéro :</strong> #{$noteId}</p>
                            <p><strong>Destination :</strong> {$destination}</p>
                            <p><strong>Montant :</strong> " . number_format($montant, 2) . " DH</p>
                            
                            <div style='background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin-top: 15px;'>
                                <p style='margin: 0;'><strong>Commentaire :</strong></p>
                                <p style='margin: 5px 0 0 0;'>{$commentaire}</p>
                            </div>
                        </div>
                        
                        <p>Veuillez contacter votre manager pour plus d'informations.</p>
                    </div>
                </div>
            ";
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erreur email rejet manager: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Envoyer un code de vérification
     */
    public function sendVerificationCode(string $toEmail, string $toName, string $code): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '🔐 Vérification de votre compte SGFD';
            $this->mailer->Body = $this->getVerificationTemplate($toName, $code);
            $this->mailer->AltBody = "Votre code de vérification est : $code (valide 15 min)";
            
            $result = $this->mailer->send();
            error_log($result ? "✅ Email envoyé à $toEmail" : "❌ Échec envoi à $toEmail");
            return $result;
            
        } catch (Exception $e) {
            error_log("❌ Erreur envoi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer un code de réinitialisation
     */
    public function sendPasswordResetCode(string $toEmail, string $toName, string $code): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '🔑 Réinitialisation de mot de passe SGFD';
            $this->mailer->Body = $this->getPasswordResetTemplate($toName, $code);
            $this->mailer->AltBody = "Votre code de réinitialisation est : $code (valide 15 min)";
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("❌ Erreur envoi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Template HTML pour vérification
     */
    private function getVerificationTemplate(string $name, string $code): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background: #f3f4f6; }
                .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center; }
                .header h1 { color: white; font-size: 28px; margin-bottom: 10px; }
                .header p { color: rgba(255,255,255,0.9); font-size: 14px; }
                .content { padding: 40px 30px; }
                .greeting { font-size: 18px; color: #111827; margin-bottom: 20px; }
                .message { color: #6b7280; line-height: 1.6; margin-bottom: 30px; }
                .code-container { background: #f9fafb; border: 2px dashed #667eea; border-radius: 12px; padding: 30px; text-align: center; margin: 30px 0; }
                .code { font-size: 42px; font-weight: 800; color: #667eea; letter-spacing: 8px; font-family: 'Courier New', monospace; }
                .expiry { color: #ef4444; font-weight: 600; margin-top: 15px; font-size: 14px; }
                .warning { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 8px; margin: 20px 0; color: #92400e; font-size: 14px; }
                .footer { background: #f9fafb; padding: 20px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; }
                .footer a { color: #667eea; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Vérification de compte</h1>
                    <p>Système de Gestion de Frais de Déplacement</p>
                </div>
                <div class='content'>
                    <div class='greeting'>Bonjour <strong>$name</strong>,</div>
                    <div class='message'>
                        Merci de vous être inscrit sur SGFD ! Pour activer votre compte, veuillez entrer le code de vérification ci-dessous dans l'application.
                    </div>
                    <div class='code-container'>
                        <div class='code'>$code</div>
                        <div class='expiry'>⏰ Expire dans 15 minutes</div>
                    </div>
                    <div class='warning'>
                        <strong>⚠️ Important :</strong> Si vous n'avez pas créé de compte, vous pouvez ignorer cet email en toute sécurité.
                    </div>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " SGFD - Tous droits réservés</p>
                    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Template HTML pour réinitialisation
     */
    private function getPasswordResetTemplate(string $name, string $code): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background: #f3f4f6; }
                .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 40px 20px; text-align: center; }
                .header h1 { color: white; font-size: 28px; margin-bottom: 10px; }
                .header p { color: rgba(255,255,255,0.9); font-size: 14px; }
                .content { padding: 40px 30px; }
                .greeting { font-size: 18px; color: #111827; margin-bottom: 20px; }
                .message { color: #6b7280; line-height: 1.6; margin-bottom: 30px; }
                .code-container { background: #fef2f2; border: 2px dashed #f5576c; border-radius: 12px; padding: 30px; text-align: center; margin: 30px 0; }
                .code { font-size: 42px; font-weight: 800; color: #f5576c; letter-spacing: 8px; font-family: 'Courier New', monospace; }
                .expiry { color: #dc2626; font-weight: 600; margin-top: 15px; font-size: 14px; }
                .warning { background: #fee2e2; border-left: 4px solid #dc2626; padding: 15px; border-radius: 8px; margin: 20px 0; color: #991b1b; font-size: 14px; }
                .footer { background: #f9fafb; padding: 20px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔑 Réinitialisation de mot de passe</h1>
                    <p>Système de Gestion de Frais de Déplacement</p>
                </div>
                <div class='content'>
                    <div class='greeting'>Bonjour <strong>$name</strong>,</div>
                    <div class='message'>
                        Vous avez demandé la réinitialisation de votre mot de passe. Voici votre code de vérification :
                    </div>
                    <div class='code-container'>
                        <div class='code'>$code</div>
                        <div class='expiry'>⏰ Expire dans 15 minutes</div>
                    </div>
                    <div class='warning'>
                        <strong>⚠️ Attention :</strong> Si vous n'avez PAS demandé cette réinitialisation, quelqu'un essaie peut-être d'accéder à votre compte. Veuillez changer immédiatement votre mot de passe et nous contacter.
                    </div>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " SGFD - Tous droits réservés</p>
                    <p>Pour votre sécurité, ne partagez jamais ce code avec personne.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
