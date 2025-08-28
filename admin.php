<?php
/**
 * AI Code Bot Admin Panel
 * لوحة تحكم بوت البرمجة الذكي
 */

define('SECURE_ACCESS', true);
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

try {
    $db = Database::getInstance();
    
    // Get statistics
    $stats = getStatistics($db);
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - AI Code Bot</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .admin-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .admin-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .section-title {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #3498db;
        }
        
        .action-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            margin: 0.5rem 0;
            border: none;
            border-radius: 8px;
            background: #3498db;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .table th, .table td {
            padding: 0.8rem;
            text-align: right;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h1><i class="fas fa-cog"></i> لوحة التحكم - AI Code Bot</h1>
            <p>إدارة النظام والإعدادات</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_users'] ?? 0; ?></div>
                <div class="stat-label">إجمالي المستخدمين</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_projects'] ?? 0; ?></div>
                <div class="stat-label">إجمالي المشاريع</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_messages'] ?? 0; ?></div>
                <div class="stat-label">إجمالي الرسائل</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['active_sessions'] ?? 0; ?></div>
                <div class="stat-label">الجلسات النشطة</div>
            </div>
        </div>
        
        <!-- Admin Sections -->
        <div class="admin-sections">
            <!-- User Management -->
            <div class="admin-section">
                <h3 class="section-title"><i class="fas fa-users"></i> إدارة المستخدمين</h3>
                
                <button class="action-btn" onclick="showUsers()">
                    <i class="fas fa-list"></i> عرض جميع المستخدمين
                </button>
                
                <button class="action-btn" onclick="addUser()">
                    <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
                </button>
                
                <button class="action-btn" onclick="manageRoles()">
                    <i class="fas fa-user-shield"></i> إدارة الصلاحيات
                </button>
            </div>
            
            <!-- Project Management -->
            <div class="admin-section">
                <h3 class="section-title"><i class="fas fa-project-diagram"></i> إدارة المشاريع</h3>
                
                <button class="action-btn" onclick="showProjects()">
                    <i class="fas fa-list"></i> عرض جميع المشاريع
                </button>
                
                <button class="action-btn" onclick="projectStats()">
                    <i class="fas fa-chart-bar"></i> إحصائيات المشاريع
                </button>
                
                <button class="action-btn" onclick="backupProjects()">
                    <i class="fas fa-download"></i> نسخ احتياطية
                </button>
            </div>
            
            <!-- System Settings -->
            <div class="admin-section">
                <h3 class="section-title"><i class="fas fa-cogs"></i> إعدادات النظام</h3>
                
                <button class="action-btn" onclick="systemSettings()">
                    <i class="fas fa-sliders-h"></i> إعدادات عامة
                </button>
                
                <button class="action-btn" onclick="aiSettings()">
                    <i class="fas fa-robot"></i> إعدادات الذكاء الاصطناعي
                </button>
                
                <button class="action-btn" onclick="securitySettings()">
                    <i class="fas fa-shield-alt"></i> إعدادات الأمان
                </button>
            </div>
            
            <!-- Monitoring -->
            <div class="admin-section">
                <h3 class="section-title"><i class="fas fa-chart-line"></i> مراقبة النظام</h3>
                
                <button class="action-btn" onclick="systemLogs()">
                    <i class="fas fa-file-alt"></i> سجلات النظام
                </button>
                
                <button class="action-btn" onclick="performanceStats()">
                    <i class="fas fa-tachometer-alt"></i> إحصائيات الأداء
                </button>
                
                <button class="action-btn" onclick="errorLogs()">
                    <i class="fas fa-exclamation-triangle"></i> سجلات الأخطاء
                </button>
            </div>
        </div>
        
        <!-- Back to main site -->
        <div style="text-align: center; margin-top: 3rem;">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> العودة للموقع الرئيسي
            </a>
        </div>
    </div>
    
    <script>
        // Admin functions
        function showUsers() {
            alert('سيتم إضافة صفحة إدارة المستخدمين قريباً');
        }
        
        function addUser() {
            alert('سيتم إضافة صفحة إضافة المستخدمين قريباً');
        }
        
        function manageRoles() {
            alert('سيتم إضافة صفحة إدارة الصلاحيات قريباً');
        }
        
        function showProjects() {
            alert('سيتم إضافة صفحة إدارة المشاريع قريباً');
        }
        
        function projectStats() {
            alert('سيتم إضافة صفحة إحصائيات المشاريع قريباً');
        }
        
        function backupProjects() {
            alert('سيتم إضافة صفحة النسخ الاحتياطية قريباً');
        }
        
        function systemSettings() {
            alert('سيتم إضافة صفحة إعدادات النظام قريباً');
        }
        
        function aiSettings() {
            alert('سيتم إضافة صفحة إعدادات الذكاء الاصطناعي قريباً');
        }
        
        function securitySettings() {
            alert('سيتم إضافة صفحة إعدادات الأمان قريباً');
        }
        
        function systemLogs() {
            alert('سيتم إضافة صفحة سجلات النظام قريباً');
        }
        
        function performanceStats() {
            alert('سيتم إضافة صفحة إحصائيات الأداء قريباً');
        }
        
        function errorLogs() {
            alert('سيتم إضافة صفحة سجلات الأخطاء قريباً');
        }
    </script>
</body>
</html>

<?php
// Get system statistics
function getStatistics($db) {
    try {
        $stats = [];
        
        // Total users
        $result = $db->fetch("SELECT COUNT(*) as count FROM users");
        $stats['total_users'] = $result['count'];
        
        // Total projects
        $result = $db->fetch("SELECT COUNT(*) as count FROM projects");
        $stats['total_projects'] = $result['count'];
        
        // Total messages
        $result = $db->fetch("SELECT COUNT(*) as count FROM chat_messages");
        $stats['total_messages'] = $result['count'];
        
        // Active sessions
        $result = $db->fetch("SELECT COUNT(*) as count FROM chat_sessions WHERE is_active = 1");
        $stats['active_sessions'] = $result['count'];
        
        return $stats;
        
    } catch (Exception $e) {
        logMessage('ERROR', 'Failed to get statistics: ' . $e->getMessage());
        return [];
    }
}
?> 