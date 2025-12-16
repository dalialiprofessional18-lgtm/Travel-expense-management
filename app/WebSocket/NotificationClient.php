<?php
namespace App\WebSocket;

use App\Models\DAO\NotificationDAO;

class NotificationClient
{
    private static $host = '127.0.0.1';
    private static $port = 8081;

    /**
     * Envoie une notification (sauvegarde ET broadcast temps réel)
     */
    public static function push(int $userId, string $title, string $message, string $type = 'info'): void
    {
        // 1. SAUVEGARDER EN BDD (toujours)
        try {
            $notifDAO = new NotificationDAO();
            $notifDAO->create($userId, $title, $message, $type);
            error_log("💾 Notification sauvegardée en BDD pour user {$userId}");
        } catch (\Exception $e) {
            error_log("❌ Erreur sauvegarde notif: " . $e->getMessage());
        }

        // 2. ENVOYER EN TEMPS RÉEL (si connecté)
        try {
            $socket = @stream_socket_client(
                "tcp://" . self::$host . ":" . self::$port,
                $errno,
                $errstr,
                2,
                STREAM_CLIENT_CONNECT
            );

            if ($socket) {
                $payload = json_encode([
                    'type' => 'broadcast',
                    'user_id' => $userId,
                    'notification' => [
                        'type' => 'notification',
                        'title' => $title,
                        'message' => $message,
                        'icon' => $type,
                        'time' => date('H:i')
                    ]
                ]) . "\n";

                fwrite($socket, $payload);
                fclose($socket);
                error_log("📡 Notification broadcast à user {$userId}");
            }
        } catch (\Exception $e) {
            error_log("⚠️ Broadcast impossible (user peut-être déconnecté)");
        }
    }
}