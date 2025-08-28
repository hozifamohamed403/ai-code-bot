# دليل الأمان - AI Code Bot

دليل شامل لأمان وحماية AI Code Bot.

## نظرة عامة

الأمان هو أولوية قصوى في AI Code Bot. نتبع أفضل الممارسات العالمية لحماية النظام والمستخدمين.

## ميزات الأمان

### 🔐 المصادقة والتفويض
- **تشفير كلمات المرور** باستخدام bcrypt
- **JWT tokens** للمصادقة
- **نظام صلاحيات متقدم** (user, moderator, admin)
- **تسجيل خروج تلقائي** بعد فترة من عدم النشاط

### 🛡️ حماية البيانات
- **تشفير البيانات الحساسة** في قاعدة البيانات
- **تطهير المدخلات** لمنع XSS
- **حماية CSRF** من الهجمات
- **تشفير الاتصالات** باستخدام HTTPS

### 🚫 منع الهجمات
- **تقييد معدل الطلبات** لمنع DDoS
- **حماية من SQL Injection**
- **حماية من File Upload** attacks
- **حماية من Directory Traversal**

## إعدادات الأمان

### 1. تشفير كلمات المرور

```php
// تشفير كلمة المرور
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// التحقق من كلمة المرور
if (password_verify($password, $passwordHash)) {
    // كلمة المرور صحيحة
}
```

### 2. JWT Tokens

```php
// إنشاء token
$token = JWT::encode([
    'user_id' => $user['id'],
    'username' => $user['username'],
    'role' => $user['role'],
    'exp' => time() + (60 * 60 * 24) // 24 ساعة
], SECRET_KEY);

// التحقق من token
try {
    $payload = JWT::decode($token, SECRET_KEY, ['HS256']);
    $userId = $payload->user_id;
} catch (Exception $e) {
    // token غير صالح
}
```

### 3. حماية CSRF

```php
// إنشاء token
$csrfToken = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;

// التحقق من token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token validation failed');
}
```

### 4. تطهير المدخلات

```php
// تطهير النص
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// تطهير المصفوفات
function sanitizeArray($array) {
    return array_map('sanitizeInput', $array);
}
```

### 5. حماية قاعدة البيانات

```php
// استخدام Prepared Statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);

// التحقق من نوع البيانات
if (!is_numeric($id)) {
    die('Invalid ID');
}
```

## إعدادات الخادم

### 1. Apache Security Headers

```apache
# .htaccess
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
```

### 2. PHP Security Settings

```ini
; php.ini
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
file_uploads = On
upload_max_filesize = 10M
max_file_uploads = 20
post_max_size = 10M
max_execution_time = 30
memory_limit = 128M
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Strict
session.gc_maxlifetime = 3600
```

### 3. MySQL Security

```sql
-- إنشاء مستخدم محدود الصلاحيات
CREATE USER 'aicodebot'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON ai_code_bot.* TO 'aicodebot'@'localhost';

-- حذف المستخدمين غير المستخدمين
DELETE FROM mysql.user WHERE User = '';

-- إزالة صلاحيات root من الشبكة
DELETE FROM mysql.user WHERE User = 'root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
```

## حماية الملفات

### 1. منع الوصول للملفات الحساسة

```apache
# .htaccess
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>

<Files "database.sql">
    Order allow,deny
    Deny from all
</Files>

<Files ".env">
    Order allow,deny
    Deny from all
</Files>
```

### 2. حماية المجلدات

```apache
# منع عرض محتويات المجلدات
Options -Indexes

# منع الوصول للمجلدات الحساسة
<IfModule mod_rewrite.c>
    RewriteRule ^(uploads|cache|logs|classes)/ - [F,L]
</IfModule>
```

### 3. صلاحيات الملفات

```bash
# إعداد الصلاحيات الصحيحة
chmod 644 *.php
chmod 644 *.css
chmod 644 *.js
chmod 755 uploads/ cache/ logs/
chmod 600 .env
chmod 600 config.php
```

## حماية قاعدة البيانات

### 1. تشفير البيانات الحساسة

```sql
-- تشفير البيانات الحساسة
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    api_key_encrypted TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- تشفير API keys
UPDATE users SET api_key_encrypted = AES_ENCRYPT(api_key, 'encryption_key');
```

### 2. فهارس الأمان

```sql
-- فهارس للأمان
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_sessions_user ON chat_sessions(user_id);
CREATE INDEX idx_messages_session ON chat_messages(session_id);

-- فهارس للأداء
CREATE INDEX idx_projects_user_status ON projects(user_id, status);
CREATE INDEX idx_code_snippets_user ON code_snippets(user_id);
```

### 3. Triggers للأمان

```sql
-- trigger لتحديث timestamp
DELIMITER //
CREATE TRIGGER before_user_update
    BEFORE UPDATE ON users
    FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- trigger لتسجيل التغييرات
CREATE TRIGGER after_user_update
    AFTER UPDATE ON users
    FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, action, old_values, new_values)
    VALUES ('users', NEW.id, 'UPDATE', JSON_OBJECT('username', OLD.username), JSON_OBJECT('username', NEW.username));
END //
```

## حماية API

### 1. Rate Limiting

```php
// تقييد معدل الطلبات
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

// تطبيق Rate Limiting
if (isRateLimited($_SERVER['REMOTE_ADDR'], 100, 3600)) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded']);
    exit();
}
```

### 2. API Key Validation

```php
// التحقق من API Key
function validateAPIKey($apiKey) {
    if (empty($apiKey)) {
        return false;
    }
    
    // التحقق من قاعدة البيانات
    $stmt = $pdo->prepare("SELECT user_id, is_active FROM api_keys WHERE key_hash = ? AND is_active = 1");
    $stmt->execute([hash('sha256', $apiKey)]);
    $result = $stmt->fetch();
    
    return $result ? $result['user_id'] : false;
}

// تطبيق التحقق
$apiKey = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$apiKey = str_replace('Bearer ', '', $apiKey);

$userId = validateAPIKey($apiKey);
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid API key']);
    exit();
}
```

### 3. Input Validation

```php
// التحقق من المدخلات
function validateInput($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        if (!isset($data[$field]) || empty($data[$field])) {
            if (strpos($rule, 'required') !== false) {
                $errors[$field] = "Field {$field} is required";
            }
            continue;
        }
        
        $value = $data[$field];
        
        // التحقق من الطول
        if (strpos($rule, 'max:') !== false) {
            preg_match('/max:(\d+)/', $rule, $matches);
            $maxLength = $matches[1];
            if (strlen($value) > $maxLength) {
                $errors[$field] = "Field {$field} must be less than {$maxLength} characters";
            }
        }
        
        // التحقق من النوع
        if (strpos($rule, 'email') !== false) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Field {$field} must be a valid email";
            }
        }
        
        if (strpos($rule, 'numeric') !== false) {
            if (!is_numeric($value)) {
                $errors[$field] = "Field {$field} must be numeric";
            }
        }
    }
    
    return $errors;
}

// استخدام التحقق
$rules = [
    'username' => 'required|max:50',
    'email' => 'required|email|max:100',
    'age' => 'numeric'
];

$errors = validateInput($_POST, $rules);
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit();
}
```

## مراقبة الأمان

### 1. سجلات الأمان

```php
// تسجيل محاولات تسجيل الدخول
function logLoginAttempt($username, $ip, $success) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'username' => $username,
        'ip_address' => $ip,
        'success' => $success,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    file_put_contents(
        LOG_PATH . 'security.log',
        json_encode($logEntry) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

// تسجيل محاولات الوصول للملفات
function logFileAccess($file, $ip, $userAgent) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'file' => $file,
        'ip_address' => $ip,
        'user_agent' => $userAgent
    ];
    
    file_put_contents(
        LOG_PATH . 'file_access.log',
        json_encode($logEntry) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}
```

### 2. تنبيهات الأمان

```php
// إرسال تنبيه عند اكتشاف نشاط مشبوه
function sendSecurityAlert($type, $details) {
    $message = [
        'type' => $type,
        'timestamp' => date('Y-m-d H:i:s'),
        'details' => $details,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    // إرسال email
    mail(
        'security@aicodebot.com',
        'Security Alert: ' . $type,
        json_encode($message, JSON_PRETTY_PRINT),
        'Content-Type: application/json'
    );
    
    // تسجيل في قاعدة البيانات
    $stmt = $pdo->prepare("INSERT INTO security_alerts (type, details, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$type, json_encode($details), $_SERVER['REMOTE_ADDR']]);
}

// استخدام التنبيهات
if (isRateLimited($_SERVER['REMOTE_ADDR'], 100, 60)) {
    sendSecurityAlert('RATE_LIMIT_EXCEEDED', [
        'ip' => $_SERVER['REMOTE_ADDR'],
        'limit' => 100,
        'period' => 60
    ]);
}
```

### 3. فحص الأمان التلقائي

```php
// فحص ملفات النظام
function securityScan() {
    $vulnerableFiles = [];
    $suspiciousPatterns = [
        'eval\s*\(',
        'exec\s*\(',
        'system\s*\(',
        'shell_exec\s*\(',
        'passthru\s*\(',
        'base64_decode\s*\('
    ];
    
    $files = glob('*.php');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match("/{$pattern}/i", $content)) {
                $vulnerableFiles[] = [
                    'file' => $file,
                    'pattern' => $pattern,
                    'line' => $this->findLineNumber($content, $pattern)
                ];
            }
        }
    }
    
    return $vulnerableFiles;
}

// فحص قاعدة البيانات
function databaseSecurityScan() {
    $issues = [];
    
    // فحص المستخدمين بدون كلمات مرور قوية
    $stmt = $pdo->query("SELECT username FROM users WHERE LENGTH(password_hash) < 60");
    $weakPasswords = $stmt->fetchAll();
    
    if (!empty($weakPasswords)) {
        $issues[] = 'Users with weak passwords found';
    }
    
    // فحص الصلاحيات
    $stmt = $pdo->query("SELECT username FROM users WHERE role = 'admin' AND last_login < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $inactiveAdmins = $stmt->fetchAll();
    
    if (!empty($inactiveAdmins)) {
        $issues[] = 'Inactive admin accounts found';
    }
    
    return $issues;
}
```

## أفضل الممارسات

### 1. تحديث النظام

```bash
# تحديث دوري
git pull origin main
composer update
npm update

# فحص الأمان
composer audit
npm audit

# تحديث النظام
sudo apt update && sudo apt upgrade
```

### 2. نسخ احتياطية

```bash
# نسخ احتياطية تلقائية
0 2 * * * /usr/bin/mysqldump -u root -p ai_code_bot > /backups/db_$(date +\%Y\%m\%d).sql
0 3 * * * tar -czf /backups/files_$(date +\%Y\%m\%d).tar.gz /var/www/ai-code-bot
```

### 3. مراقبة السجلات

```bash
# مراقبة سجلات الأمان
tail -f /var/log/apache2/access.log | grep -E "(404|403|500)"
tail -f /var/log/apache2/error.log
tail -f /var/www/ai-code-bot/logs/security.log
```

### 4. فحص الثغرات

```bash
# فحص الثغرات
nmap -sV localhost
nikto -h localhost
sqlmap -u "http://localhost/login.php" --forms

# فحص SSL
openssl s_client -connect localhost:443 -servername localhost
```

## استجابة الحوادث

### 1. خطة الاستجابة

```php
// خطة الاستجابة للحوادث الأمنية
function incidentResponse($incident) {
    switch ($incident['type']) {
        case 'BRUTE_FORCE':
            // حظر IP
            banIP($incident['ip']);
            // إرسال تنبيه
            sendSecurityAlert('BRUTE_FORCE_ATTACK', $incident);
            break;
            
        case 'SQL_INJECTION':
            // تسجيل المحاولة
            logSecurityIncident($incident);
            // إرسال تنبيه فوري
            sendImmediateAlert($incident);
            break;
            
        case 'FILE_UPLOAD_ATTACK':
            // حذف الملف
            deleteFile($incident['file']);
            // حظر المستخدم
            banUser($incident['user_id']);
            break;
    }
}
```

### 2. استعادة النظام

```bash
# استعادة من نسخة احتياطية
mysql -u root -p ai_code_bot < /backups/db_20240101.sql
tar -xzf /backups/files_20240101.tar.gz

# فحص الملفات
find /var/www/ai-code-bot -name "*.php" -exec grep -l "eval\|exec\|system" {} \;

# إعادة تشغيل الخدمات
sudo systemctl restart apache2
sudo systemctl restart mysql
```

## اختبار الأمان

### 1. اختبار الاختراق

```bash
# اختبار الاختراق
# 1. فحص الثغرات
nmap -sV -sC localhost

# 2. اختبار SQL Injection
sqlmap -u "http://localhost/search.php?q=test" --dbs

# 3. اختبار XSS
# أدخل <script>alert('XSS')</script> في حقول البحث

# 4. اختبار CSRF
# حاول إرسال طلب POST بدون token
```

### 2. اختبار التحميل

```bash
# اختبار تحميل الملفات
# 1. حاول تحميل ملف PHP
# 2. حاول تحميل ملف كبير جداً
# 3. حاول تحميل ملف بامتداد مزدوج (.php.jpg)
```

### 3. اختبار المصادقة

```bash
# اختبار كلمات المرور
# 1. جرب كلمات مرور ضعيفة
# 2. جرب تسجيل دخول متكرر
# 3. جرب تسجيل دخول بدون بيانات
```

## الموارد الإضافية

### أدوات الأمان
- **OWASP ZAP**: فحص الثغرات
- **Nmap**: فحص الشبكات
- **Nikto**: فحص خوادم الويب
- **SQLMap**: فحص SQL Injection
- **Burp Suite**: فحص التطبيقات

### مراجع الأمان
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [OWASP Cheat Sheet Series](https://cheatsheetseries.owasp.org/)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [MySQL Security](https://dev.mysql.com/doc/refman/8.0/en/security.html)

### تدريب الأمان
- [OWASP WebGoat](https://owasp.org/www-project-webgoat/)
- [DVWA](http://www.dvwa.co.uk/)
- [Juice Shop](https://owasp.org/www-project-juice-shop/)

---

## ملخص الأمان

### ✅ ما تم تطبيقه
- تشفير كلمات المرور بـ bcrypt
- حماية CSRF
- تطهير المدخلات
- Rate Limiting
- JWT Authentication
- HTTPS Enforcement
- Security Headers
- Input Validation
- SQL Injection Protection
- File Upload Security

### 🔒 ما يجب مراقبته
- سجلات الأمان
- محاولات تسجيل الدخول
- تحميل الملفات
- استهلاك الموارد
- نشاط المستخدمين

### 🚨 ما يجب فعله عند الحوادث
1. **تحديد** نوع الحادث
2. **عزل** النظام المتأثر
3. **تقييم** حجم الضرر
4. **إصلاح** الثغرة
5. **استعادة** النظام
6. **توثيق** الحادث
7. **منع** تكرار الحادث

**AI Code Bot** - آمن ومحمي! 🛡️💪

*آخر تحديث: 2024-01-01* 