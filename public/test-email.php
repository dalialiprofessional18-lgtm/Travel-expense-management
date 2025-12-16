<?php

use App\Services\EmailService;

$emailService = new EmailService();

$result = $emailService->sendNoteApprovedEmail(
    'nofarokaz@gmail.com',  // ✅ Mettez VOTRE email ici
    'Test User',
    123,
    'Paris',
    1500.00,
    'Test d\'envoi'
);

if ($result) {
    echo "✅ Email envoyé avec succès !";
} else {
    echo "❌ Échec de l'envoi. Vérifiez les logs PHP.";
}