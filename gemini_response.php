<?php
// gemini_response.php
header('Content-Type: text/plain; charset=utf-8');

$GEMINI_KEY = 'AIzaSyAsd_1cxiA_udcGcqZ5mz7EoCTtXLuFEtA';
$question = $_POST['question'] ?? '';
$user_id  = $_POST['user_id'] ?? null;

if (!$question) { echo "Pas de question..."; exit; }

// Connexion MySQL
$pdo = new PDO('mysql:host=localhost;dbname=sgfd;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// === CONTEXTE TEMPS RÉEL COMPLET ===
$context = "=== SGFD - État au " . date('d/m/Y H:i') . " ===\n\n";

$stmt = $pdo->query("
    SELECT n.id, n.statut, n.montant_total, n.updated_at, d.titre, u.nom 
    FROM notes_frais n 
    JOIN deplacements d ON n.deplacement_id = d.id 
    JOIN users u ON n.user_id = u.id 
    ORDER BY n.updated_at DESC LIMIT 20
");
$context .= "Notes de frais récentes :\n";
foreach ($stmt as $r) {
    $context .= "• {$r['nom']} → {$r['titre']} | {$r['montant_total']}€ | {$r['statut']} (".date('d/m H:i', strtotime($r['updated_at'])).")\n";
}

$context .= "\nDéplacements à venir :\n";
$stmt = $pdo->query("SELECT titre, lieu, date_depart, date_retour, u.nom FROM deplacements d JOIN users u ON d.user_id=u.id WHERE date_depart >= CURDATE() ORDER BY date_depart");
foreach ($stmt as $r) {
    $context .= "• {$r['nom']} → {$r['titre']} à {$r['lieu']} (du ".date('d/m', strtotime($r['date_depart']))." au ".date('d/m/Y', strtotime($r['date_retour'])).")\n";
}

// Prompt final
$prompt = "Tu es l'assistant IA interne ultra-compétent de SGFD (gestion frais/déplacements).
Contexte complet et à jour :
$context

Question : $question

Réponds en français, pro, clair, avec émojis utiles.";

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$GEMINI_KEY";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode(['contents' => [['parts' => [['text' => $prompt]]]]]),
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
curl_close($ch);

$json = json_decode($response, true);
$reponse = $json['candidates'][0]['content']['parts'][0]['text'] ?? "Je réfléchis encore...";

echo $reponse;
?>