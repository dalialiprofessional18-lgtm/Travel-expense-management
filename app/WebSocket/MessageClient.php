<?php
// app/WebSocket/MessageClient.php
namespace App\WebSocket;

class MessageClient
{
    private static $host = '127.0.0.1';
    private static $port = 8081;

    /**
     * Broadcast un nouveau message à tous les participants de la conversation
     */
    public static function broadcastMessage(int $conversationId, array $messageData, int $senderId): void
    {
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
                    'type' => 'new_message',
                    'conversation_id' => $conversationId,
                    'sender_id' => $senderId,
                    'message' => $messageData
                ]) . "\n";

                fwrite($socket, $payload);
                fclose($socket);
                error_log("📡 Message broadcast à conversation {$conversationId}");
            }
        } catch (\Exception $e) {
            error_log("⚠️ Broadcast message impossible: " . $e->getMessage());
        }
    }
}