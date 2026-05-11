<?php

namespace App\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use PDO;
use PDOException;

class DatabaseHandler extends AbstractProcessingHandler
{
    private $pdo;
    private $user_id;
    private $user_name;

    public function __construct(PDO $pdo, $user_id = null, $user_name = 'System', $level = \Monolog\Level::Debug)
    {
        parent::__construct($level);
        $this->pdo = $pdo;
        $this->user_id = $user_id;
        $this->user_name = $user_name;
    }

    protected function write(LogRecord $record): void
    {
        try {
            $sql = "
                INSERT INTO logs (user_id, action, details, level, ip_address, url, method, created_at)
                VALUES (:user_id, :action, :details, :level, :ip_address, :url, :method, NOW())
            ";

            $stmt = $this->pdo->prepare($sql);
            
            $details = json_encode([
                'channel' => $record->channel,
                'message' => $record->formatted,
                'context' => $record->context,
                'extra' => $record->extra
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $stmt->execute([
                ':user_id' => $this->user_id,
                ':action' => $record->channel . ' - ' . $this->user_name,
                ':details' => $details,
                ':level' => strtoupper($record->level->getName()),
                ':ip_address' => $this->getClientIP(),
                ':url' => $_SERVER['REQUEST_URI'] ?? '',
                ':method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI'
            ]);
        } catch (PDOException $e) {
            error_log("Database Log Error: " . $e->getMessage());
            // Hata olsa bile exception throw etme, loglama başarısız olmuş demek
        }
    }

    private function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }
}
