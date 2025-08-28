-- AI Code Bot Database
-- قاعدة بيانات بوت البرمجة الذكي
-- Created by AI Code Bot

-- Create database
CREATE DATABASE IF NOT EXISTS ai_code_bot
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE ai_code_bot;

-- Users table - جدول المستخدمين
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    avatar_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Chat sessions table - جدول جلسات المحادثة
CREATE TABLE chat_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_active (is_active)
);

-- Chat messages table - جدول رسائل المحادثة
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT,
    user_id INT,
    message_type ENUM('user', 'bot') NOT NULL,
    content TEXT NOT NULL,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES chat_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id),
    INDEX idx_user_id (user_id),
    INDEX idx_message_type (message_type),
    INDEX idx_created_at (created_at)
);

-- Projects table - جدول المشاريع
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    project_type ENUM('web', 'mobile', 'desktop', 'api', 'database', 'ai', 'other') NOT NULL,
    status ENUM('planning', 'development', 'testing', 'completed', 'maintenance') DEFAULT 'planning',
    technologies JSON,
    github_url VARCHAR(255),
    live_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_project_type (project_type),
    INDEX idx_status (status)
);

-- Project files table - جدول ملفات المشاريع
CREATE TABLE project_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    content_hash VARCHAR(64),
    is_generated BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_file_type (file_type)
);

-- Code snippets table - جدول أجزاء الكود
CREATE TABLE code_snippets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    code_content TEXT NOT NULL,
    language VARCHAR(50),
    tags JSON,
    is_public BOOLEAN DEFAULT FALSE,
    views_count INT DEFAULT 0,
    likes_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_language (language),
    INDEX idx_is_public (is_public)
);

-- AI models table - جدول نماذج الذكاء الاصطناعي
CREATE TABLE ai_models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    provider VARCHAR(100),
    model_type VARCHAR(100),
    api_key VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    rate_limit INT,
    cost_per_token DECIMAL(10,6),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider (provider),
    INDEX idx_is_active (is_active)
);

-- API usage logs table - جدول سجلات استخدام API
CREATE TABLE api_usage_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    model_id INT,
    tokens_used INT,
    cost DECIMAL(10,6),
    request_data JSON,
    response_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (model_id) REFERENCES ai_models(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_model_id (model_id),
    INDEX idx_created_at (created_at)
);

-- Settings table - جدول الإعدادات
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('site_name', 'AI Code Bot', 'string', 'اسم الموقع', TRUE),
('site_description', 'بوت البرمجة الذكي', 'string', 'وصف الموقع', TRUE),
('max_tokens_per_request', '4000', 'number', 'الحد الأقصى للرموز في الطلب', FALSE),
('default_language', 'ar', 'string', 'اللغة الافتراضية', TRUE),
('maintenance_mode', 'false', 'boolean', 'وضع الصيانة', TRUE),
('max_file_size', '10485760', 'number', 'الحد الأقصى لحجم الملف (10MB)', FALSE),
('allowed_file_types', '["txt","js","py","php","html","css","sql","json","md"]', 'json', 'أنواع الملفات المسموحة', FALSE);

-- Insert default AI models
INSERT INTO ai_models (name, provider, model_type, is_active, rate_limit, cost_per_token) VALUES
('GPT-4', 'OpenAI', 'text-generation', TRUE, 1000, 0.00003),
('Claude-3', 'Anthropic', 'text-generation', TRUE, 1000, 0.000015),
('Gemini Pro', 'Google', 'text-generation', TRUE, 1000, 0.00001),
('Local Model', 'Local', 'text-generation', TRUE, 10000, 0.000001);

-- Create admin user (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@aicodebot.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير النظام', 'admin');

-- Create indexes for better performance
CREATE INDEX idx_chat_messages_session_user ON chat_messages(session_id, user_id);
CREATE INDEX idx_projects_user_status ON projects(user_id, status);
CREATE INDEX idx_code_snippets_user_language ON code_snippets(user_id, language);
CREATE INDEX idx_api_usage_user_date ON api_usage_logs(user_id, created_at);

-- Create views for common queries
CREATE VIEW active_chat_sessions AS
SELECT cs.*, u.username, u.full_name
FROM chat_sessions cs
JOIN users u ON cs.user_id = u.id
WHERE cs.is_active = TRUE;

CREATE VIEW user_projects_summary AS
SELECT 
    u.username,
    u.full_name,
    COUNT(p.id) as total_projects,
    COUNT(CASE WHEN p.status = 'completed' THEN 1 END) as completed_projects,
    COUNT(CASE WHEN p.status = 'development' THEN 1 END) as active_projects
FROM users u
LEFT JOIN projects p ON u.id = p.user_id
GROUP BY u.id, u.username, u.full_name;

CREATE VIEW popular_code_snippets AS
SELECT 
    cs.*,
    u.username,
    u.full_name
FROM code_snippets cs
JOIN users u ON cs.user_id = u.id
WHERE cs.is_public = TRUE
ORDER BY cs.views_count DESC, cs.likes_count DESC
LIMIT 100;

-- Create stored procedures
DELIMITER //

CREATE PROCEDURE CreateNewChatSession(
    IN p_user_id INT,
    IN p_session_name VARCHAR(100)
)
BEGIN
    INSERT INTO chat_sessions (user_id, session_name)
    VALUES (p_user_id, p_session_name);
    
    SELECT LAST_INSERT_ID() as session_id;
END //

CREATE PROCEDURE GetUserChatHistory(
    IN p_user_id INT,
    IN p_limit INT DEFAULT 50
)
BEGIN
    SELECT 
        cs.session_name,
        cs.created_at as session_created,
        cm.message_type,
        cm.content,
        cm.created_at as message_created
    FROM chat_sessions cs
    JOIN chat_messages cm ON cs.id = cm.session_id
    WHERE cs.user_id = p_user_id
    ORDER BY cs.created_at DESC, cm.created_at ASC
    LIMIT p_limit;
END //

CREATE PROCEDURE GetProjectFiles(
    IN p_project_id INT
)
BEGIN
    SELECT 
        pf.*,
        p.name as project_name
    FROM project_files pf
    JOIN projects p ON pf.project_id = p.id
    WHERE pf.project_id = p_project_id
    ORDER BY pf.file_path, pf.file_name;
END //

CREATE PROCEDURE UpdateProjectStatus(
    IN p_project_id INT,
    IN p_new_status VARCHAR(50)
)
BEGIN
    UPDATE projects 
    SET status = p_new_status, updated_at = CURRENT_TIMESTAMP
    WHERE id = p_project_id;
    
    SELECT ROW_COUNT() as affected_rows;
END //

DELIMITER ;

-- Create triggers
DELIMITER //

CREATE TRIGGER before_user_update
    BEFORE UPDATE ON users
    FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //

CREATE TRIGGER before_project_update
    BEFORE UPDATE ON projects
    FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //

CREATE TRIGGER after_message_insert
    AFTER INSERT ON chat_messages
    FOR EACH ROW
BEGIN
    UPDATE chat_sessions 
    SET updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.session_id;
END //

DELIMITER ;

-- Grant permissions (adjust as needed)
-- GRANT ALL PRIVILEGES ON ai_code_bot.* TO 'your_username'@'localhost';
-- FLUSH PRIVILEGES;

-- Show table structure
SHOW TABLES;

-- Show table information
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    DATA_LENGTH,
    INDEX_LENGTH
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'ai_code_bot'; 