<?php
namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use App\Handler\DatabaseHandler;
use PDO;

class LoggerFactory
{
    private static $loggers = [];

    /**
     * Dosya tabanlı logger oluştur
     */
    public static function file(): Logger
    {
        if (!isset(self::$loggers['file'])) {
            $logger = new Logger('app');
            $logger->pushHandler(new RotatingFileHandler(__DIR__ . '/../../logs/app.log', 30, Level::Info));
            self::$loggers['file'] = $logger;
        }

        return self::$loggers['file'];
    }

    /**
     * Veritabanı ve dosya tabanlı logger oluştur
     */
    public static function database(PDO $db, $user_id = null, $user_name = 'System'): Logger
    {
        if (!isset(self::$loggers['database'])) {
            $logger = new Logger('database');
            
            // Dosya handler'ı ekle
            $logger->pushHandler(new RotatingFileHandler(__DIR__ . '/../../logs/database.log', 30, Level::Info));
            
            // Veritabanı handler'ını ekle
            $logger->pushHandler(new DatabaseHandler($db, $user_id, $user_name, Level::Info));
            
            self::$loggers['database'] = $logger;
        }

        return self::$loggers['database'];
    }

    /**
     * Kesif modülü için özel logger
     */
    public static function kesif(PDO $db, $user_id = null, $user_name = 'System'): Logger
    {
        if (!isset(self::$loggers['kesif'])) {
            $logger = new Logger('kesif');
            
            // Dosya handler'ı ekle
            $logger->pushHandler(new RotatingFileHandler(__DIR__ . '/../../logs/kesif.log', 30, Level::Info));
            
            // Veritabanı handler'ını ekle
            $logger->pushHandler(new DatabaseHandler($db, $user_id, $user_name, Level::Info));
            
            self::$loggers['kesif'] = $logger;
        }

        return self::$loggers['kesif'];
    }

    /**
     * Security logger
     */
    public static function security(PDO $db, $user_id = null, $user_name = 'System'): Logger
    {
        if (!isset(self::$loggers['security'])) {
            $logger = new Logger('security');
            
            // Dosya handler'ı ekle
            $logger->pushHandler(new RotatingFileHandler(__DIR__ . '/../../logs/security.log', 30, Level::Warning));
            
            // Veritabanı handler'ını ekle
            $logger->pushHandler(new DatabaseHandler($db, $user_id, $user_name, Level::Warning));
            
            self::$loggers['security'] = $logger;
        }

        return self::$loggers['security'];
    }
}
