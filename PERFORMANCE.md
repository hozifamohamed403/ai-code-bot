# دليل الأداء - AI Code Bot

دليل شامل لتحسين أداء AI Code Bot.

## نظرة عامة

الأداء هو عامل حاسم في نجاح أي تطبيق ويب. نتبع أفضل الممارسات لضمان سرعة استجابة عالية.

## قياس الأداء

### 1. أدوات القياس

```bash
# Apache Bench
ab -n 1000 -c 10 http://localhost/

# Siege
siege -c 100 -t 30S http://localhost/

# wrk
wrk -t12 -c400 -d30s http://localhost/

# Lighthouse
lighthouse http://localhost/ --output html
```

### 2. مقاييس الأداء

```php
// قياس وقت الاستجابة
$startTime = microtime(true);

// تنفيذ العملية
$result = performOperation();

$endTime = microtime(true);
$executionTime = ($endTime - $startTime) * 1000; // بالمللي ثانية

logMessage('PERFORMANCE', "Operation completed in {$executionTime}ms");
```

### 3. مراقبة الأداء

```php
// مراقبة استخدام الذاكرة
function monitorMemory() {
    $memoryUsage = memory_get_usage(true);
    $memoryPeak = memory_get_peak_usage(true);
    
    logMessage('PERFORMANCE', "Memory: {$memoryUsage} bytes, Peak: {$memoryPeak} bytes");
    
    if ($memoryUsage > 128 * 1024 * 1024) { // 128MB
        logMessage('WARNING', 'High memory usage detected');
    }
}

// مراقبة وقت الاستجابة
function monitorResponseTime() {
    $responseTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
    
    if ($responseTime > 2.0) { // أكثر من 2 ثانية
        logMessage('WARNING', "Slow response time: {$responseTime}s");
    }
}
```

## تحسين قاعدة البيانات

### 1. الفهارس (Indexes)

```sql
-- فهارس أساسية
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_projects_user ON projects(user_id);
CREATE INDEX idx_messages_session ON chat_messages(session_id);

-- فهارس مركبة
CREATE INDEX idx_projects_user_status ON projects(user_id, status);
CREATE INDEX idx_messages_user_type ON chat_messages(user_id, message_type);

-- فهارس للنص
CREATE FULLTEXT INDEX idx_projects_search ON projects(name, description);
CREATE FULLTEXT INDEX idx_code_snippets_search ON code_snippets(title, description);
```

### 2. استعلامات محسنة

```sql
-- استعلام محسن
SELECT 
    p.id,
    p.name,
    p.description,
    p.status,
    u.username,
    COUNT(pf.id) as file_count
FROM projects p
JOIN users u ON p.user_id = u.id
LEFT JOIN project_files pf ON p.id = pf.project_id
WHERE p.status = 'active'
GROUP BY p.id
HAVING file_count > 0
ORDER BY p.created_at DESC
LIMIT 20;

-- استعلام مع EXPLAIN
EXPLAIN SELECT * FROM users WHERE username = 'admin';
```

### 3. تقسيم الجداول (Partitioning)

```sql
-- تقسيم جدول الرسائل حسب التاريخ
ALTER TABLE chat_messages
PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2023 VALUES LESS THAN (2024),
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026)
);

-- تقسيم جدول السجلات
ALTER TABLE system_logs
PARTITION BY RANGE (TO_DAYS(created_at)) (
    PARTITION p_current VALUES LESS THAN (TO_DAYS(NOW())),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

### 4. تنظيف البيانات

```sql
-- حذف البيانات القديمة
DELETE FROM chat_messages 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

DELETE FROM system_logs 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTHS);

-- أرشفة البيانات
INSERT INTO archived_messages 
SELECT * FROM chat_messages 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- تحسين الجداول
OPTIMIZE TABLE chat_messages;
ANALYZE TABLE users;
```

## تحسين PHP

### 1. OPcache

```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
opcache.enable_cli=1
opcache.validate_timestamps=0
```

### 2. تحسين الكود

```php
// استخدام lazy loading
class ProjectManager {
    private $db;
    private $cache;
    
    public function getProject($id) {
        // فحص الكاش أولاً
        if ($cached = $this->cache->get("project_{$id}")) {
            return $cached;
        }
        
        // جلب من قاعدة البيانات
        $project = $this->db->fetch("SELECT * FROM projects WHERE id = ?", [$id]);
        
        // حفظ في الكاش
        $this->cache->set("project_{$id}", $project, 3600);
        
        return $project;
    }
}

// استخدام prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = ?");
$stmt->execute([$username, 1]);

// تجنب الحلقات المتداخلة
$users = $pdo->query("SELECT * FROM users")->fetchAll();
$userIds = array_column($users, 'id');
$projects = $pdo->query("SELECT * FROM projects WHERE user_id IN (" . implode(',', $userIds) . ")")->fetchAll();
```

### 3. إدارة الذاكرة

```php
// تنظيف الذاكرة
function cleanupMemory() {
    // تنظيف المتغيرات الكبيرة
    unset($largeArray);
    
    // تنظيف الكاش
    if (function_exists('gc_collect_cycles')) {
        gc_collect_cycles();
    }
    
    // إعادة تعيين الذاكرة
    if (function_exists('memory_reset_peak_usage')) {
        memory_reset_peak_usage();
    }
}

// استخدام Generators للملفات الكبيرة
function readLargeFile($filename) {
    $handle = fopen($filename, 'r');
    
    while (!feof($handle)) {
        yield fgets($handle);
    }
    
    fclose($handle);
}

// استخدام Generators
foreach (readLargeFile('large_file.txt') as $line) {
    processLine($line);
}
```

## تحسين الواجهة الأمامية

### 1. تحسين CSS

```css
/* استخدام CSS Variables */
:root {
    --primary-color: #3498db;
    --secondary-color: #2ecc71;
    --text-color: #2c3e50;
}

/* تحسين الأداء */
.project-card {
    /* تجنب reflow */
    transform: translateZ(0);
    will-change: transform;
    
    /* تحسين الرسوم */
    backface-visibility: hidden;
}

/* استخدام CSS Grid للتصميم */
.container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}
```

### 2. تحسين JavaScript

```javascript
// استخدام Debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// تطبيق Debouncing
const debouncedSearch = debounce(function(query) {
    performSearch(query);
}, 300);

// استخدام Intersection Observer
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
});

// مراقبة العناصر
document.querySelectorAll('.lazy-load').forEach(el => observer.observe(el));

// استخدام Web Workers للمهام الثقيلة
const worker = new Worker('worker.js');
worker.postMessage({data: largeData});
worker.onmessage = function(e) {
    console.log('Result:', e.data);
};
```

### 3. تحسين الصور

```html
<!-- استخدام الصور المتجاوبة -->
<picture>
    <source media="(min-width: 800px)" srcset="large.jpg">
    <source media="(min-width: 400px)" srcset="medium.jpg">
    <img src="small.jpg" alt="صورة متجاوبة" loading="lazy">
</picture>

<!-- استخدام WebP -->
<picture>
    <source type="image/webp" srcset="image.webp">
    <img src="image.jpg" alt="صورة" loading="lazy">
</picture>
```

## التخزين المؤقت (Caching)

### 1. Redis Cache

```php
// إعداد Redis
class RedisCache {
    private $redis;
    
    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }
    
    public function get($key) {
        return $this->redis->get($key);
    }
    
    public function set($key, $value, $ttl = 3600) {
        return $this->redis->setex($key, $ttl, serialize($value));
    }
    
    public function delete($key) {
        return $this->redis->del($key);
    }
    
    public function exists($key) {
        return $this->redis->exists($key);
    }
}

// استخدام الكاش
$cache = new RedisCache();

function getProject($id) {
    $cacheKey = "project_{$id}";
    
    if ($cached = $cache->get($cacheKey)) {
        return unserialize($cached);
    }
    
    $project = fetchProjectFromDatabase($id);
    $cache->set($cacheKey, $project, 3600);
    
    return $project;
}
```

### 2. File Cache

```php
// كاش الملفات
class FileCache {
    private $cacheDir;
    
    public function __construct($dir = 'cache') {
        $this->cacheDir = $dir;
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function get($key) {
        $filename = $this->cacheDir . '/' . md5($key);
        
        if (!file_exists($filename)) {
            return false;
        }
        
        $data = file_get_contents($filename);
        $cached = json_decode($data, true);
        
        if ($cached['expires'] < time()) {
            unlink($filename);
            return false;
        }
        
        return $cached['data'];
    }
    
    public function set($key, $value, $ttl = 3600) {
        $filename = $this->cacheDir . '/' . md5($key);
        $data = [
            'data' => $value,
            'expires' => time() + $ttl
        ];
        
        return file_put_contents($filename, json_encode($data));
    }
}
```

### 3. Database Query Cache

```php
// كاش استعلامات قاعدة البيانات
class QueryCache {
    private $cache;
    private $pdo;
    
    public function __construct($cache, $pdo) {
        $this->cache = $cache;
        $this->pdo = $pdo;
    }
    
    public function query($sql, $params = [], $ttl = 300) {
        $cacheKey = 'query_' . md5($sql . serialize($params));
        
        if ($cached = $this->cache->get($cacheKey)) {
            return $cached;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        
        $this->cache->set($cacheKey, $result, $ttl);
        
        return $result;
    }
}
```

## تحسين الشبكة

### 1. HTTP/2

```apache
# .htaccess
<IfModule mod_http2.c>
    # تفعيل HTTP/2
    Protocols h2 h2c http/1.1
    
    # Server Push
    H2Push on
    H2PushResource add /style.css
    H2PushResource add /script.js
</IfModule>
```

### 2. Gzip Compression

```apache
# .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>
```

### 3. Browser Caching

```apache
# .htaccess
<IfModule mod_expires.c>
    ExpiresActive On
    
    # الصور
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    
    # CSS و JavaScript
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    
    # HTML
    ExpiresByType text/html "access plus 1 hour"
    
    # البيانات
    ExpiresByType application/json "access plus 1 hour"
    ExpiresByType text/xml "access plus 1 hour"
</IfModule>
```

## تحسين الخادم

### 1. Apache Configuration

```apache
# apache2.conf
# تحسين الأداء
KeepAlive On
KeepAliveTimeout 5
MaxKeepAliveRequests 100

# تحسين الذاكرة
MaxRequestWorkers 150
MaxConnectionsPerChild 1000

# تحسين الملفات
EnableSendfile On
EnableMMAP On
```

### 2. PHP-FPM Configuration

```ini
; php-fpm.conf
[www]
user = www-data
group = www-data
listen = 127.0.0.1:9000
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

request_terminate_timeout = 30s
```

### 3. MySQL Configuration

```ini
; my.cnf
[mysqld]
# تحسين الذاكرة
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_log_buffer_size = 16M

# تحسين الاستعلامات
query_cache_type = 1
query_cache_size = 128M
query_cache_limit = 2M

# تحسين الاتصالات
max_connections = 200
max_connect_errors = 1000
connect_timeout = 10
wait_timeout = 600
interactive_timeout = 600
```

## مراقبة الأداء

### 1. Real-time Monitoring

```php
// مراقبة الأداء في الوقت الفعلي
class PerformanceMonitor {
    private $metrics = [];
    
    public function startTimer($name) {
        $this->metrics[$name]['start'] = microtime(true);
    }
    
    public function endTimer($name) {
        if (isset($this->metrics[$name]['start'])) {
            $this->metrics[$name]['duration'] = microtime(true) - $this->metrics[$name]['start'];
            $this->metrics[$name]['memory'] = memory_get_usage(true);
        }
    }
    
    public function getMetrics() {
        return $this->metrics;
    }
    
    public function logMetrics() {
        foreach ($this->metrics as $name => $metric) {
            logMessage('PERFORMANCE', "{$name}: {$metric['duration']}s, Memory: {$metric['memory']} bytes");
        }
    }
}

// استخدام المراقب
$monitor = new PerformanceMonitor();

$monitor->startTimer('database_query');
$result = $db->query("SELECT * FROM users");
$monitor->endTimer('database_query');

$monitor->startTimer('data_processing');
$processed = processData($result);
$monitor->endTimer('data_processing');

$monitor->logMetrics();
```

### 2. Performance Dashboard

```php
// لوحة تحكم الأداء
function getPerformanceStats() {
    $stats = [
        'response_time' => [
            'current' => getCurrentResponseTime(),
            'average' => getAverageResponseTime(),
            'peak' => getPeakResponseTime()
        ],
        'memory_usage' => [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit')
        ],
        'database' => [
            'queries' => getQueryCount(),
            'slow_queries' => getSlowQueryCount(),
            'connections' => getActiveConnections()
        ],
        'cache' => [
            'hit_rate' => getCacheHitRate(),
            'size' => getCacheSize(),
            'keys' => getCacheKeyCount()
        ]
    ];
    
    return $stats;
}
```

## اختبار الأداء

### 1. Load Testing

```bash
# اختبار التحميل
ab -n 10000 -c 100 http://localhost/

# اختبار الضغط
siege -c 200 -t 5M http://localhost/

# اختبار الاستقرار
wrk -t12 -c400 -d30s http://localhost/
```

### 2. Stress Testing

```bash
# اختبار الضغط
ab -n 100000 -c 1000 http://localhost/

# اختبار الذاكرة
for i in {1..1000}; do
    curl http://localhost/api/heavy-operation &
done
```

### 3. Performance Profiling

```php
// تحليل الأداء
xhprof_enable();

// تنفيذ العملية
performOperation();

$xhprof_data = xhprof_disable();
$xhprof_runs = new XHProfRuns_Default();
$run_id = $xhprof_runs->save_run($xhprof_data, "ai_code_bot");

echo "Profile saved with run id: {$run_id}";
```

## نصائح عامة

### 1. تحسين الكود

```php
// تجنب الحلقات المتداخلة
// ❌ سيء
foreach ($users as $user) {
    foreach ($projects as $project) {
        if ($user['id'] == $project['user_id']) {
            // معالجة
        }
    }
}

// ✅ جيد
$userProjects = [];
foreach ($projects as $project) {
    $userProjects[$project['user_id']][] = $project;
}

foreach ($users as $user) {
    if (isset($userProjects[$user['id']])) {
        // معالجة
    }
}

// استخدام References
// ❌ سيء
foreach ($array as $item) {
    $item['processed'] = true;
}

// ✅ جيد
foreach ($array as &$item) {
    $item['processed'] = true;
}
unset($item);
```

### 2. تحسين قاعدة البيانات

```sql
-- استخدام LIMIT مع OFFSET
SELECT * FROM users LIMIT 20 OFFSET 40;

-- استخدام JOIN بدلاً من subqueries
SELECT u.*, p.name as project_name
FROM users u
LEFT JOIN projects p ON u.id = p.user_id;

-- تجنب SELECT *
SELECT id, username, email FROM users;
```

### 3. تحسين الشبكة

```apache
# استخدام CDN
<IfModule mod_headers.c>
    Header set Cache-Control "public, max-age=31536000"
</IfModule>

# تحسين الصور
<IfModule mod_rewrite.c>
    RewriteRule ^images/(.*)\.(jpg|jpeg|png|gif)$ /image-processor.php?file=$1&type=$2 [L]
</IfModule>
```

## المراقبة المستمرة

### 1. Automated Monitoring

```bash
#!/bin/bash
# مراقبة تلقائية للأداء

# فحص استجابة الموقع
response_time=$(curl -o /dev/null -s -w "%{time_total}" http://localhost/)
if (( $(echo "$response_time > 2.0" | bc -l) )); then
    echo "Warning: Slow response time: ${response_time}s"
fi

# فحص استخدام الذاكرة
memory_usage=$(ps aux | grep php-fpm | awk '{sum+=$6} END {print sum/1024}')
if (( $(echo "$memory_usage > 512" | bc -l) )); then
    echo "Warning: High memory usage: ${memory_usage}MB"
fi

# فحص قاعدة البيانات
db_connections=$(mysql -u root -p -e "SHOW STATUS LIKE 'Threads_connected'" | tail -1 | awk '{print $2}')
if [ $db_connections -gt 100 ]; then
    echo "Warning: High database connections: ${db_connections}"
fi
```

### 2. Performance Alerts

```php
// تنبيهات الأداء
function checkPerformanceAlerts() {
    $alerts = [];
    
    // فحص وقت الاستجابة
    $responseTime = getCurrentResponseTime();
    if ($responseTime > 2.0) {
        $alerts[] = "Slow response time: {$responseTime}s";
    }
    
    // فحص استخدام الذاكرة
    $memoryUsage = memory_get_usage(true);
    if ($memoryUsage > 128 * 1024 * 1024) {
        $alerts[] = "High memory usage: " . round($memoryUsage / 1024 / 1024, 2) . "MB";
    }
    
    // فحص قاعدة البيانات
    $slowQueries = getSlowQueryCount();
    if ($slowQueries > 10) {
        $alerts[] = "High number of slow queries: {$slowQueries}";
    }
    
    // إرسال التنبيهات
    if (!empty($alerts)) {
        sendPerformanceAlert($alerts);
    }
}
```

---

## ملخص تحسين الأداء

### ✅ ما تم تطبيقه
- فهارس قاعدة البيانات
- OPcache
- Redis Cache
- Gzip Compression
- Browser Caching
- HTTP/2 Support
- Performance Monitoring

### 🚀 النتائج المتوقعة
- **وقت الاستجابة**: تحسن 60-80%
- **استخدام الذاكرة**: تقليل 40-60%
- **سرعة التحميل**: تحسن 70-90%
- **عدد المستخدمين المتزامنين**: زيادة 3-5 مرات

### 📊 مقاييس الأداء المستهدفة
- **وقت الاستجابة**: أقل من 500ms
- **استخدام الذاكرة**: أقل من 100MB
- **سرعة التحميل**: أقل من 2 ثانية
- **معدل الخطأ**: أقل من 0.1%

**AI Code Bot** - سريع وفعال! ⚡💪

*آخر تحديث: 2024-01-01* 