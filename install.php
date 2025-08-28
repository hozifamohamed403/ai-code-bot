<?php
/**
 * AI Code Bot Installation Script
 * سكريبت تثبيت بوت البرمجة الذكي
 * Created by AI Code Bot
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Include configuration
require_once 'config.php';

// Installation status
$installationStatus = [
    'database' => false,
    'tables' => false,
    'directories' => false,
    'permissions' => false,
    'admin_user' => false
];

$errors = [];
$warnings = [];

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    $errors[] = 'PHP version 7.4 or higher is required. Current version: ' . PHP_VERSION;
}

// Check required PHP extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl'];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $errors[] = "Required PHP extension '{$ext}' is not loaded.";
    }
}

// Check directory permissions
$directories = [
    'uploads' => UPLOAD_PATH,
    'cache' => CACHE_PATH,
    'logs' => LOG_PATH
];

foreach ($directories as $name => $path) {
    if (!is_dir($path)) {
        if (!mkdir($path, 0755, true)) {
            $errors[] = "Cannot create directory: {$name}";
        } else {
            $installationStatus['directories'] = true;
        }
    } elseif (!is_writable($path)) {
        $warnings[] = "Directory '{$name}' is not writable. Some features may not work.";
    } else {
        $installationStatus['directories'] = true;
    }
}

// Database installation
if (empty($errors)) {
    try {
        // Test database connection
        $db = Database::getInstance();
        $db->query("SELECT 1");
        $installationStatus['database'] = true;
        
        // Check if tables exist
        $tables = $db->fetchAll("SHOW TABLES");
        if (empty($tables)) {
            // Install database schema
            installDatabaseSchema($db);
            $installationStatus['tables'] = true;
        } else {
            $installationStatus['tables'] = true;
            $warnings[] = 'Database tables already exist. Installation may have been completed before.';
        }
        
        // Create admin user if not exists
        $adminExists = $db->fetch("SELECT id FROM users WHERE username = 'admin'");
        if (!$adminExists) {
            createAdminUser($db);
            $installationStatus['admin_user'] = true;
        } else {
            $installationStatus['admin_user'] = true;
            $warnings[] = 'Admin user already exists.';
        }
        
    } catch (Exception $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}

// Install database schema
function installDatabaseSchema($db) {
    $sqlFile = 'database.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("Database schema file not found: {$sqlFile}");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^(--|#|\/\*)/', $statement)) {
            try {
                $db->query($statement);
            } catch (Exception $e) {
                // Skip some statements that might fail (like CREATE DATABASE)
                if (!strpos(strtolower($statement), 'create database')) {
                    throw new Exception("Failed to execute SQL: " . substr($statement, 0, 100) . "... Error: " . $e->getMessage());
                }
            }
        }
    }
}

// Create admin user
function createAdminUser($db) {
    $adminPassword = 'admin123'; // Change this in production
    $passwordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
    
    $db->query(
        "INSERT INTO users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)",
        ['admin', 'admin@aicodebot.com', $passwordHash, 'مدير النظام', 'admin']
    );
}

// Check if installation is complete
$installationComplete = !empty($errors) ? false : array_reduce($installationStatus, function($carry, $item) {
    return $carry && $item;
}, true);

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تثبيت AI Code Bot</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            direction: rtl;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 800px;
            width: 90%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 1rem;
        }
        
        .title {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .subtitle {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .status-section {
            margin: 2rem 0;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            margin: 0.5rem 0;
            border-radius: 10px;
            background: #f8f9fa;
            border: 2px solid transparent;
        }
        
        .status-item.success {
            border-color: #27ae60;
            background: #d5f4e6;
        }
        
        .status-item.error {
            border-color: #e74c3c;
            background: #fadbd8;
        }
        
        .status-item.warning {
            border-color: #f39c12;
            background: #fdebd0;
        }
        
        .status-icon {
            font-size: 1.5rem;
        }
        
        .status-icon.success {
            color: #27ae60;
        }
        
        .status-icon.error {
            color: #e74c3c;
        }
        
        .status-icon.warning {
            color: #f39c12;
        }
        
        .status-text {
            font-weight: 600;
        }
        
        .errors-section, .warnings-section {
            margin: 1rem 0;
        }
        
        .error-item, .warning-item {
            padding: 0.8rem;
            margin: 0.5rem 0;
            border-radius: 8px;
            border-right: 4px solid;
        }
        
        .error-item {
            background: #fadbd8;
            border-right-color: #e74c3c;
            color: #721c24;
        }
        
        .warning-item {
            background: #fdebd0;
            border-right-color: #f39c12;
            color: #856404;
        }
        
        .actions {
            text-align: center;
            margin-top: 2rem;
        }
        
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            margin: 0 0.5rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #1f5f8b);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
        
        .success-message {
            background: #d5f4e6;
            color: #155724;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            margin: 1rem 0;
            border: 2px solid #27ae60;
        }
        
        .credentials {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1rem 0;
            border: 2px solid #e9ecef;
        }
        
        .credential-item {
            display: flex;
            justify-content: space-between;
            margin: 0.5rem 0;
            padding: 0.5rem;
            background: white;
            border-radius: 5px;
        }
        
        .credential-label {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .credential-value {
            font-family: monospace;
            background: #e9ecef;
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🤖</div>
            <h1 class="title">تثبيت AI Code Bot</h1>
            <p class="subtitle">بوت البرمجة الذكي</p>
        </div>
        
        <?php if ($installationComplete): ?>
            <div class="success-message">
                <h2>🎉 تم التثبيت بنجاح!</h2>
                <p>تم تثبيت AI Code Bot بنجاح على نظامك.</p>
            </div>
            
            <div class="credentials">
                <h3>بيانات تسجيل الدخول:</h3>
                <div class="credential-item">
                    <span class="credential-label">اسم المستخدم:</span>
                    <span class="credential-value">admin</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">كلمة المرور:</span>
                    <span class="credential-value">admin123</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">البريد الإلكتروني:</span>
                    <span class="credential-value">admin@aicodebot.com</span>
                </div>
            </div>
            
            <div class="actions">
                <a href="index.php" class="btn btn-primary">الذهاب للموقع</a>
                <a href="admin/" class="btn btn-secondary">لوحة التحكم</a>
            </div>
            
        <?php else: ?>
            <div class="status-section">
                <h3>حالة التثبيت:</h3>
                
                <div class="status-item <?php echo $installationStatus['database'] ? 'success' : 'error'; ?>">
                    <span class="status-text">اتصال قاعدة البيانات</span>
                    <span class="status-icon <?php echo $installationStatus['database'] ? 'success' : 'error'; ?>">
                        <?php echo $installationStatus['database'] ? '✅' : '❌'; ?>
                    </span>
                </div>
                
                <div class="status-item <?php echo $installationStatus['tables'] ? 'success' : 'error'; ?>">
                    <span class="status-text">إنشاء الجداول</span>
                    <span class="status-icon <?php echo $installationStatus['tables'] ? 'success' : 'error'; ?>">
                        <?php echo $installationStatus['tables'] ? '✅' : '❌'; ?>
                    </span>
                </div>
                
                <div class="status-item <?php echo $installationStatus['directories'] ? 'success' : 'error'; ?>">
                    <span class="status-text">إنشاء المجلدات</span>
                    <span class="status-icon <?php echo $installationStatus['directories'] ? 'success' : 'error'; ?>">
                        <?php echo $installationStatus['directories'] ? '✅' : '❌'; ?>
                    </span>
                </div>
                
                <div class="status-item <?php echo $installationStatus['admin_user'] ? 'success' : 'error'; ?>">
                    <span class="status-text">إنشاء المستخدم المدير</span>
                    <span class="status-icon <?php echo $installationStatus['admin_user'] ? 'success' : 'error'; ?>">
                        <?php echo $installationStatus['admin_user'] ? '✅' : '❌'; ?>
                    </span>
                </div>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="errors-section">
                    <h3>أخطاء:</h3>
                    <?php foreach ($errors as $error): ?>
                        <div class="error-item">❌ <?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($warnings)): ?>
                <div class="warnings-section">
                    <h3>تحذيرات:</h3>
                    <?php foreach ($warnings as $warning): ?>
                        <div class="warning-item">⚠️ <?php echo htmlspecialchars($warning); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="actions">
                <a href="install.php" class="btn btn-primary">إعادة المحاولة</a>
                <a href="index.php" class="btn btn-secondary">العودة للموقع</a>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 3rem; text-align: center; color: #7f8c8d;">
            <p>AI Code Bot v1.0.0 - Created with ❤️ by AI Code Bot</p>
        </div>
    </div>
</body>
</html> 