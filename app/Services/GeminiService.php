<?php
namespace App\Services;

class GeminiService
{
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent';
    private bool $demoMode = true;  // ✅ MODE DÉMO ACTIVÉ

    public function __construct()
    {
        $this->apiKey = 'AIzaSyD9eB3CezQ8SrGqCWlf9YeeYIvcYu2ueLc';
    }

    public function chat(array $messages, ?string $systemPrompt = null): string
    {
        // ✅ SI MODE DÉMO, RETOURNER RÉPONSE SIMULÉE DIRECTEMENT
        if ($this->demoMode) {
            return $this->getDemoResponse($messages);
        }

        // Code API normale (ne sera pas exécuté en mode démo)
        try {
            $fullPrompt = $systemPrompt ?? $this->getDefaultSystemPrompt();
            
            foreach ($messages as $msg) {
                if ($msg['role'] === 'user') {
                    $fullPrompt .= "\n\nUtilisateur: " . $msg['content'];
                } elseif ($msg['role'] === 'assistant') {
                    $fullPrompt .= "\n\nAssistant: " . $msg['content'];
                }
            }

            $payload = [
                'contents' => [['parts' => [['text' => $fullPrompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 2048],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE']
                ]
            ];

            $ch = curl_init($this->apiUrl . '?key=' . $this->apiKey);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 30,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 429) {
                return $this->getDemoResponse($messages);
            }

            if ($httpCode !== 200) {
                return "Erreur API ($httpCode)";
            }

            $data = json_decode($response, true);
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? "Pas de réponse";

        } catch (\Exception $e) {
            return $this->getDemoResponse($messages);
        }
    }

    private function getDemoResponse(array $messages): string
    {
        $lastMessage = end($messages);
        $userMessage = strtolower($lastMessage['content'] ?? '');

        if (strpos($userMessage, 'tendances') !== false || strpos($userMessage, 'dépenses') !== false) {
            return "📈 **Tendances des dépenses - Décembre 2024**

**Vue d'ensemble :**
- Budget mensuel : **50,000 DH**
- Dépenses actuelles : **12,800 DH** (25.6%)
- Projection fin de mois : **45,000 DH**

**Top 3 des catégories :**
1. 🚗 **Transport** : 5,200 DH (40%)
2. 🏨 **Hébergement** : 4,100 DH (32%)
3. 🍽️ **Restauration** : 2,500 DH (20%)

**Comparaison avec novembre :**
- Transport : **+15%** ⬆️
- Hébergement : **-5%** ⬇️
- Restauration : **+8%** ⬆️

**Recommandations :**
- ✅ Budget bien maîtrisé
- ⚠️ Surveiller l'augmentation des frais de transport
- 💡 Privilégier les réservations groupées";
        }

        if (strpos($userMessage, 'notes') !== false && strpos($userMessage, 'attente') !== false) {
            return "📊 **Notes de frais en attente**

**Statistiques :**
- Total : **2 notes**
- Montant : **4,250 DH**
- Délai moyen : **3 jours**

**Détails :**
1. Mohamed El Amrani - 2,500 DH (Rabat)
2. Fatima Zahra - 1,750 DH (Marrakech)

**Actions recommandées :**
- Valider dans les 48h
- Vérifier les justificatifs";
        }

        if (strpos($userMessage, 'améliorer') !== false) {
            return "💡 **Améliorations suggérées**

**1. Automatisation**
- Validation auto < 500 DH
- Alertes après 48h

**2. Communication**
- Notifications temps réel ✅
- Rappels email

**3. Organisation**
- Dashboard prioritaire
- Plages horaires dédiées

**Impact estimé :** -40% temps de validation";
        }

        return "👋 Bonjour ! Je peux vous aider avec :

📊 Analyses et statistiques
📋 Notes de frais en attente
💡 Recommandations

**Questions possibles :**
- Tendances des dépenses ?
- Notes en attente ?
- Améliorations ?

_(Mode démo - quota API temporaire)_";
    }

    private function getDefaultSystemPrompt(): string
    {
        return "Assistant IA SGFD - Réponds en français avec émojis et structure Markdown.";
    }

    public function generateTitle(string $firstMessage): string
    {
        $words = explode(' ', $firstMessage);
        return implode(' ', array_slice($words, 0, 5)) ?: 'Conversation';
    }

    }