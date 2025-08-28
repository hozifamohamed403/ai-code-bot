<?php
/**
 * AI Code Bot Configuration
 * إعدادات بوت البرمجة الذكي
 * Created by AI Code Bot
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ai_code_bot');
define('DB_USER', 'root'); // Change this to your database username
define('DB_PASS', ''); // Change this to your database password
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'AI Code Bot');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost'); // Change this to your domain
define('APP_PATH', __DIR__);
define('APP_DEBUG', true); // Set to false in production

// Security Configuration
define('SECRET_KEY', 'your-secret-key-here-change-this-in-production');
define('SESSION_NAME', 'ai_code_bot_session');
define('SESSION_LIFETIME', 3600); // 1 hour
define('CSRF_TOKEN_NAME', 'csrf_token');

// File Upload Configuration
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', [
    'txt', 'js', 'py', 'php', 'html', 'css', 'sql', 'json', 'md',
    'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'
]);
define('UPLOAD_PATH', APP_PATH . '/uploads/');

// AI API Configuration
define('OPENAI_API_KEY', ''); // Add your OpenAI API key
define('CLAUDE_API_KEY', ''); // Add your Claude API key
define('GEMINI_API_KEY', ''); // Add your Gemini API key

// Rate Limiting
define('RATE_LIMIT_PER_MINUTE', 60);
define('RATE_LIMIT_PER_HOUR', 1000);

// Cache Configuration
define('CACHE_ENABLED', true);
define('CACHE_PATH', APP_PATH . '/cache/');
define('CACHE_LIFETIME', 3600); // 1 hour

// Logging Configuration
define('LOG_ENABLED', true);
define('LOG_PATH', APP_PATH . '/logs/');
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR

// Email Configuration (for notifications)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_ENCRYPTION', 'tls');

// Error Reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Riyadh');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Database Connection Class
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ]);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die("Database connection failed: " . $e->getMessage());
            } else {
                die("Database connection failed. Please try again later.");
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                throw new Exception("Query failed: " . $e->getMessage());
            } else {
                throw new Exception("Database query failed.");
            }
        }
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function insert($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }
    
    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function delete($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
}

// Utility Functions
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function logMessage($level, $message, $context = []) {
    if (!LOG_ENABLED) return;
    
    $logFile = LOG_PATH . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
    
    $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;
    
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

function isRateLimited($identifier, $limit, $period = 60) {
    $cacheKey = "rate_limit_{$identifier}";
    $current = time();
    
    if (isset($_SESSION[$cacheKey])) {
        $data = $_SESSION[$cacheKey];
        if ($current - $data['timestamp'] < $period) {
            if ($data['count'] >= $limit) {
                return true;
            }
            $_SESSION[$cacheKey]['count']++;
        } else {
            $_SESSION[$cacheKey] = ['timestamp' => $current, 'count' => 1];
        }
    } else {
        $_SESSION[$cacheKey] = ['timestamp' => $current, 'count' => 1];
    }
    
    return false;
}

function getSetting($key, $default = null) {
    try {
        $db = Database::getInstance();
        $result = $db->fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        return $result ? $result['setting_value'] : $default;
    } catch (Exception $e) {
        logMessage('ERROR', "Failed to get setting: {$key}", ['error' => $e->getMessage()]);
        return $default;
    }
}

function setSetting($key, $value) {
    try {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
             ON DUPLICATE KEY UPDATE setting_value = ?",
            [$key, $value, $value]
        );
        return true;
    } catch (Exception $e) {
        logMessage('ERROR', "Failed to set setting: {$key}", ['error' => $e->getMessage()]);
        return false;
    }
}

// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Set CSRF token
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    generateCSRFToken();
}

// Security headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Auto-load classes (if using autoloader)
spl_autoload_register(function ($class) {
    $file = APP_PATH . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Error handler
if (!APP_DEBUG) {
    set_error_handler(function($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return;
        }
        logMessage('ERROR', $message, ['file' => $file, 'line' => $line]);
        return true;
    });
}

// Exception handler
if (!APP_DEBUG) {
    set_exception_handler(function($exception) {
        logMessage('ERROR', $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        if (headers_sent()) {
            echo "An error occurred. Please try again later.";
        } else {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Internal server error']);
        }
    });
}

// Check if required directories exist
$requiredDirs = [UPLOAD_PATH, CACHE_PATH, LOG_PATH];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Load environment-specific configuration
$envFile = APP_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            if (!defined($key)) {
                define($key, $value);
            }
        }
    }
}

// Initialize application
try {
    // Test database connection
    $db = Database::getInstance();
    $db->query("SELECT 1");
    
    if (APP_DEBUG) {
        logMessage('INFO', 'Application initialized successfully');
    }
} catch (Exception $e) {
    if (APP_DEBUG) {
        logMessage('ERROR', 'Failed to initialize application', ['error' => $e->getMessage()]);
    }
}
?> 