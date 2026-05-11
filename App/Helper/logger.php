<?php
use App\Logging\LoggerFactory;
use Monolog\Logger;

/**
 * Global logger getter - basit ve hızlı
 * Kullanım: $logger = \getLogger("kesif");
 *           $logger->info("Mesaj", ['user_id' => $_SESSION['lid']]);
 */
function getLogger($module = 'app'): Logger
{
    global $ac;
    
    try {
        $user_id = $_SESSION['lid'] ?? 0;
        $username = $_SESSION['username'] ?? 'Guest';
        
        // Module'e göre logger seç
        if ($module === 'kesif') {
            return LoggerFactory::kesif($ac, $user_id, $username);
        } elseif ($module === 'security') {
            return LoggerFactory::security($ac, $user_id, $username);
        } elseif ($module === 'database') {
            return LoggerFactory::database($ac, $user_id, $username);
        } else {
            return LoggerFactory::file();
        }
    } catch (\Exception $e) {
        error_log("Logger hatası: " . $e->getMessage());
        return LoggerFactory::file();
    }
}

/**
 * Kısayol fonksiyon - hızlı loglama
 * Kullanım: log_info("Mesaj", "kesif");
 */
function log_info($message, $module = 'app', $context = [])
{
    \getLogger($module)->info($message, $context);
}

function log_error($message, $module = 'app', $context = [])
{
    \getLogger($module)->error($message, $context);
}

function log_warning($message, $module = 'app', $context = [])
{
    \getLogger($module)->warning($message, $context);
}

function log_debug($message, $module = 'app', $context = [])
{
    \getLogger($module)->debug($message, $context);
}
