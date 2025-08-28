<?php

/**
 * AI Code Bot Authentication System
 * نظام المصادقة لبوت البرمجة الذكي
 */

define('SECURE_ACCESS', true);
require_once 'config.php';

/**
 * Authentication Class
 * فئة المصادقة
 */
class Auth
{
    private $db;
    private $session;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->session = new Session();
    }
    
    /**
     * User registration
     * تسجيل المستخدم
     */
    public function register(string $username, string $email, string $password, string $fullName = ''): array
    {
        try {
            // Validate input
            $this->validateRegistrationInput($username, $email, $password);
            
            // Check if user already exists
            if ($this->userExists($username, $email)) {
                return ['success' => false, 'message' => 'المستخدم موجود بالفعل'];
            }
            
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            
            // Create user
            $userId = $this->db->insert('users', [
                'username' => $username,
                'email' => $email,
                'password_hash' => $passwordHash,
                'full_name' => $fullName,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($userId) {
                return [
                    'success' => true,
                    'message' => 'تم التسجيل بنجاح',
                    'user_id' => $userId
                ];
            }
            
            return ['success' => false, 'message' => 'فشل في التسجيل'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * User login
     * تسجيل دخول المستخدم
     */
    public function login(string $username, string $password): array
    {
        try {
            // Get user by username or email
            $user = $this->getUserByUsernameOrEmail($username);
            
            if (!$user) {
                return ['success' => false, 'message' => 'اسم المستخدم أو كلمة المرور غير صحيحة'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'اسم المستخدم أو كلمة المرور غير صحيحة'];
            }
            
            // Check if user is active
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'الحساب معطل'];
            }
            
            // Create session
            $this->session->create($user['id'], $user['username'], $user['role']);
            
            // Update last login
            $this->db->update('users', 
                ['last_login' => date('Y-m-d H:i:s')], 
                'id = :id', 
                ['id' => $user['id']]
            );
            
            return [
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ]
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * User logout
     * تسجيل خروج المستخدم
     */
    public function logout(): array
    {
        try {
            $this->session->destroy();
            return ['success' => true, 'message' => 'تم تسجيل الخروج بنجاح'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Check if user is authenticated
     * التحقق من مصادقة المستخدم
     */
    public function isAuthenticated(): bool
    {
        return $this->session->isValid();
    }
    
    /**
     * Get current user
     * الحصول على المستخدم الحالي
     */
    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        $userId = $this->session->getUserId();
        return $this->getUserById($userId);
    }
    
    /**
     * Check user permissions
     * التحقق من صلاحيات المستخدم
     */
    public function hasPermission(string $permission): bool
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return false;
        }
        
        // Admin has all permissions
        if ($user['role'] === 'admin') {
            return true;
        }
        
        // Check specific permissions based on role
        $permissions = $this->getRolePermissions($user['role']);
        return in_array($permission, $permissions);
    }
    
    /**
     * Validate registration input
     * التحقق من صحة بيانات التسجيل
     */
    private function validateRegistrationInput(string $username, string $email, string $password): void
    {
        // Username validation
        if (strlen($username) < 3 || strlen($username) > 50) {
            throw new Exception('اسم المستخدم يجب أن يكون بين 3 و 50 حرف');
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new Exception('اسم المستخدم يجب أن يحتوي على أحرف وأرقام وشرطة سفلية فقط');
        }
        
        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('البريد الإلكتروني غير صحيح');
        }
        
        // Password validation
        if (strlen($password) < 8) {
            throw new Exception('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
        }
        
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            throw new Exception('كلمة المرور يجب أن تحتوي على حرف صغير وحرف كبير ورقم');
        }
    }
    
    /**
     * Check if user exists
     * التحقق من وجود المستخدم
     */
    private function userExists(string $username, string $email): bool
    {
        $sql = "SELECT id FROM users WHERE username = :username OR email = :email";
        $result = $this->db->fetch($sql, ['username' => $username, 'email' => $email]);
        return $result !== null;
    }
    
    /**
     * Get user by username or email
     * الحصول على المستخدم بالاسم أو البريد
     */
    private function getUserByUsernameOrEmail(string $username): ?array
    {
        $sql = "SELECT * FROM users WHERE username = :username OR email = :username";
        return $this->db->fetch($sql, ['username' => $username]);
    }
    
    /**
     * Get user by ID
     * الحصول على المستخدم بالمعرف
     */
    private function getUserById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get role permissions
     * الحصول على صلاحيات الدور
     */
    private function getRolePermissions(string $role): array
    {
        $permissions = [
            'user' => ['read', 'create', 'update_own'],
            'moderator' => ['read', 'create', 'update_own', 'moderate', 'delete_own'],
            'admin' => ['read', 'create', 'update', 'delete', 'moderate', 'manage_users']
        ];
        
        return $permissions[$role] ?? [];
    }
}

/**
 * Session Management Class
 * فئة إدارة الجلسة
 */
class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Create session
     * إنشاء جلسة
     */
    public function create(int $userId, string $username, string $role): void
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['created_at'] = time();
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Check if session is valid
     * التحقق من صحة الجلسة
     */
    public function isValid(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout (24 hours)
        if (time() - $_SESSION['last_activity'] > 86400) {
            $this->destroy();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Get user ID from session
     * الحصول على معرف المستخدم من الجلسة
     */
    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get username from session
     * الحصول على اسم المستخدم من الجلسة
     */
    public function getUsername(): ?string
    {
        return $_SESSION['username'] ?? null;
    }
    
    /**
     * Get user role from session
     * الحصول على دور المستخدم من الجلسة
     */
    public function getUserRole(): ?string
    {
        return $_SESSION['role'] ?? null;
    }
    
    /**
     * Destroy session
     * تدمير الجلسة
     */
    public function destroy(): void
    {
        session_unset();
        session_destroy();
    }
}

// Handle authentication requests
// معالجة طلبات المصادقة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new Auth();
    $response = [];
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'register':
            $response = $auth->register(
                $_POST['username'] ?? '',
                $_POST['email'] ?? '',
                $_POST['password'] ?? '',
                $_POST['full_name'] ?? ''
            );
            break;
            
        case 'login':
            $response = $auth->login(
                $_POST['username'] ?? '',
                $_POST['password'] ?? ''
            );
            break;
            
        case 'logout':
            $response = $auth->logout();
            break;
            
        default:
            $response = ['success' => false, 'message' => 'إجراء غير معروف'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} 