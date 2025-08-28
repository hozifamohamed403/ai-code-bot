<?php

namespace AICodeBot\Classes;

use PDO;
use PDOException;
use Exception;

/**
 * Database Management Class
 * فئة إدارة قاعدة البيانات
 */
class Database
{
    private static $instance = null;
    private $connection;
    private $config;
    
    /**
     * Private constructor to prevent direct instantiation
     * منع الإنشاء المباشر
     */
    private function __construct()
    {
        $this->config = [
            'host' => DB_HOST,
            'dbname' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASS,
            'charset' => DB_CHARSET
        ];
        
        $this->connect();
    }
    
    /**
     * Get singleton instance
     * الحصول على النسخة الوحيدة
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Establish database connection
     * إنشاء اتصال قاعدة البيانات
     */
    private function connect(): void
    {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
            
            $this->connection = new PDO($dsn, $this->config['username'], $this->config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->config['charset']}"
            ]);
            
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get database connection
     * الحصول على اتصال قاعدة البيانات
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
    
    /**
     * Execute a query
     * تنفيذ استعلام
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query execution failed: " . $e->getMessage());
        }
    }
    
    /**
     * Fetch all records
     * جلب جميع السجلات
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Fetch single record
     * جلب سجل واحد
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Insert record
     * إدراج سجل
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, $data);
        return $this->connection->lastInsertId();
    }
    
    /**
     * Update record
     * تحديث سجل
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $setClause) . " WHERE {$where}";
        
        $stmt = $this->query($sql, array_merge($data, $whereParams));
        return $stmt->rowCount();
    }
    
    /**
     * Delete record
     * حذف سجل
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Begin transaction
     * بدء المعاملة
     */
    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     * تأكيد المعاملة
     */
    public function commit(): void
    {
        $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     * التراجع عن المعاملة
     */
    public function rollback(): void
    {
        $this->connection->rollback();
    }
    
    /**
     * Check if table exists
     * التحقق من وجود الجدول
     */
    public function tableExists(string $table): bool
    {
        $sql = "SHOW TABLES LIKE :table";
        $result = $this->fetch($sql, ['table' => $table]);
        return $result !== null;
    }
    
    /**
     * Get table structure
     * الحصول على هيكل الجدول
     */
    public function getTableStructure(string $table): array
    {
        $sql = "DESCRIBE {$table}";
        return $this->fetchAll($sql);
    }
    
    /**
     * Execute raw SQL
     * تنفيذ SQL خام
     */
    public function executeRaw(string $sql): bool
    {
        try {
            $this->connection->exec($sql);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Raw SQL execution failed: " . $e->getMessage());
        }
    }
    
    /**
     * Close connection
     * إغلاق الاتصال
     */
    public function close(): void
    {
        $this->connection = null;
    }
    
    /**
     * Prevent cloning
     * منع النسخ
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     * منع إلغاء التسلسل
     */
    private function __wakeup() {}
} 