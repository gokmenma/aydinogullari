<?php 
namespace App\Logging;

use PDO;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class MySQLHandler extends AbstractProcessingHandler
{
    protected PDO $db;
    protected string $table;

    public function __construct(PDO $db, string $table = 'security_logs', int|string|Level $level = Level::Warning, bool $bubble = true)
    {
        $this->db = $db;
        $this->table = $table;
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        $sql = "INSERT INTO {$this->table} (channel, level, message, context, datetime, ip, uri, user_id)
                VALUES (:channel, :level, :message, :context, :datetime, :ip, :uri, :user_id)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':channel'  => $record->channel,
            ':level'    => $record->level->name,
            ':message'  => $record->message,
            ':context'  => json_encode($record->context, JSON_UNESCAPED_UNICODE),
            ':datetime' => $record->datetime->format('Y-m-d H:i:s'),
            ':ip'       => $_SERVER['REMOTE_ADDR'] ?? null,
            ':uri'      => $_SERVER['REQUEST_URI'] ?? null,
            ':user_id'  => $_SESSION['user_id'] ?? null,
        ]);
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new JsonFormatter();
    }
}
