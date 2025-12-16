<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/WebSocket/NotificationServer.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\NotificationServer;

// Instance partagée du serveur de notifications
$notificationServer = new NotificationServer();

// Serveur WebSocket pour les clients (navigateurs)
$wsServer = IoServer::factory(
    new HttpServer(new WsServer($notificationServer)),
    8080,
    '0.0.0.0'
);

echo "═══════════════════════════════════════\n";
echo "   🚀 WebSocket Server STARTED\n";
echo "   📡 WebSocket: ws://localhost:8080\n";
echo "   📡 Command Port: tcp://localhost:8081\n";
echo "   ⚡ Notifications temps réel actives\n";
echo "═══════════════════════════════════════\n\n";

// Serveur TCP pour recevoir les commandes PHP
$commandServer = stream_socket_server("tcp://127.0.0.1:8081", $errno, $errstr);
if (!$commandServer) {
    die("❌ Impossible de démarrer le serveur de commandes: $errstr ($errno)\n");
}

stream_set_blocking($commandServer, false);
echo "✅ Serveur de commandes démarré sur le port 8081\n\n";

// Boucle d'événements personnalisée
$wsServer->loop->addPeriodicTimer(0.1, function() use ($commandServer, $notificationServer) {
    $client = @stream_socket_accept($commandServer, 0);
    
    if ($client) {
        $data = fread($client, 8192);
        fclose($client);
        
        if ($data) {
            $command = json_decode(trim($data), true);
            
            if (isset($command['type'])) {
                switch($command['type']) {
                    case 'broadcast':
                        echo "📥 Commande: broadcast notification\n";
                        $notificationServer->broadcastToUser(
                            $command['user_id'],
                            $command['notification']
                        );
                        break;
                        
                    case 'new_message':
                        echo "📥 Commande: nouveau message\n";
                        $notificationServer->broadcastMessageToConversation(
                            $command['conversation_id'],
                            $command['message'],
                            $command['sender_id']
                        );
                        break;
                }
            }
        }
    }
});

$wsServer->run();
