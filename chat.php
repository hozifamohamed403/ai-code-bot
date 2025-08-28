<?php
/**
 * AI Code Bot Chat Handler
 * معالج المحادثة لبوت البرمجة الذكي
 */

define('SECURE_ACCESS', true);
require_once 'config.php';

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check rate limiting
if (isRateLimited($_SERVER['REMOTE_ADDR'], RATE_LIMIT_PER_MINUTE, 60)) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded. Please try again later.']);
    exit();
}

try {
    $db = Database::getInstance();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['message'])) {
            throw new Exception('Invalid input');
        }
        
        $message = sanitizeInput($input['message']);
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default user
        
        // Process message with AI
        $response = processAIMessage($message);
        
        // Save to database
        $sessionId = getOrCreateSession($db, $userId);
        saveMessage($db, $sessionId, $userId, 'user', $message);
        saveMessage($db, $sessionId, $userId, 'bot', $response);
        
        // Return response
        echo json_encode([
            'success' => true,
            'response' => $response,
            'session_id' => $sessionId
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get chat history
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        $sessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : null;
        
        $history = getChatHistory($db, $userId, $sessionId);
        
        echo json_encode([
            'success' => true,
            'history' => $history
        ]);
        
    } else {
        throw new Exception('Method not allowed');
    }
    
} catch (Exception $e) {
    logMessage('ERROR', 'Chat error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => APP_DEBUG ? $e->getMessage() : 'Internal server error'
    ]);
}

// Process AI message
function processAIMessage($message) {
    $lowerMessage = strtolower($message);
    
    // Project creation
    if (strpos($lowerMessage, 'مشروع') !== false || strpos($lowerMessage, 'project') !== false) {
        return generateProjectResponse($message);
    }
    
    // Code generation
    if (strpos($lowerMessage, 'كود') !== false || strpos($lowerMessage, 'code') !== false) {
        return generateCodeResponse($message);
    }
    
    // Database
    if (strpos($lowerMessage, 'قاعدة بيانات') !== false || strpos($lowerMessage, 'database') !== false) {
        return generateDatabaseResponse($message);
    }
    
    // Website
    if (strpos($lowerMessage, 'موقع') !== false || strpos($lowerMessage, 'website') !== false) {
        return generateWebsiteResponse($message);
    }
    
    // API
    if (strpos($lowerMessage, 'api') !== false) {
        return generateAPIResponse($message);
    }
    
    // Default response
    return generateDefaultResponse($message);
}

// Generate project response
function generateProjectResponse($message) {
    return "ممتاز! سأقوم بإنشاء مشروع كامل لك! 🚀\n\n" .
           "**خطة المشروع:**\n" .
           "1. تحليل المتطلبات\n" .
           "2. تصميم المعمارية\n" .
           "3. إنشاء قاعدة البيانات\n" .
           "4. تطوير الواجهات\n" .
           "5. تطوير الخلفية\n" .
           "6. اختبار وتطوير\n\n" .
           "أخبرني بالتفاصيل وسأبدأ العمل! 💪";
}

// Generate code response
function generateCodeResponse($message) {
    return "سأقوم بإنشاء كود احترافي لك! 💻\n\n" .
           "**مثال على الكود:**\n" .
           "```python\n" .
           "# كود Python احترافي\n" .
           "def main():\n" .
           "    print('مرحباً بك!')\n" .
           "    return True\n" .
           "```\n\n" .
           "هل تريد كود محدد؟ أم تريد مثالاً آخر؟ 🤔";
}

// Generate database response
function generateDatabaseResponse($message) {
    return "ممتاز! سأقوم بإنشاء قاعدة بيانات احترافية لك! 🗄️\n\n" .
           "**مثال على قاعدة البيانات:**\n" .
           "```sql\n" .
           "CREATE TABLE users (\n" .
           "    id INT AUTO_INCREMENT PRIMARY KEY,\n" .
           "    username VARCHAR(50) UNIQUE NOT NULL,\n" .
           "    email VARCHAR(100) UNIQUE NOT NULL\n" .
           ");\n" .
           "```\n\n" .
           "هل تريد قاعدة بيانات لمشروع معين؟ 💪";
}

// Generate website response
function generateWebsiteResponse($message) {
    return "ممتاز! سأقوم بإنشاء موقع ويب احترافي لك! 🌐\n\n" .
           "**هيكل الموقع:**\n" .
           "```html\n" .
           "<!DOCTYPE html>\n" .
           "<html lang='ar'>\n" .
           "<head>\n" .
           "    <title>موقعي الرائع</title>\n" .
           "</head>\n" .
           "<body>\n" .
           "    <h1>مرحباً بك!</h1>\n" .
           "</body>\n" .
           "</html>\n" .
           "```\n\n" .
           "هل تريد موقع معين؟ 🚀";
}

// Generate API response
function generateAPIResponse($message) {
    return "ممتاز! سأقوم بإنشاء API احترافي لك! 🔌\n\n" .
           "**مثال على API:**\n" .
           "```php\n" .
           "<?php\n" .
           "header('Content-Type: application/json');\n" .
           "echo json_encode(['status' => 'success']);\n" .
           "?>\n" .
           "```\n\n" .
           "هل تريد API لمشروع معين؟ 💪";
}

// Generate default response
function generateDefaultResponse($message) {
    $responses = [
        "أهلاً! كيف يمكنني مساعدتك في البرمجة اليوم? 💻",
        "ممتاز! أخبرني ماذا تريد أن أعمل لك? 🚀",
        "أنا هنا لمساعدتك! ما هو مشروعك التالي? 💪",
        "دعني أساعدك في تحقيق أفكارك البرمجية! 🌟"
    ];
    
    return $responses[array_rand($responses)];
}

// Get or create chat session
function getOrCreateSession($db, $userId) {
    $existingSession = $db->fetch(
        "SELECT id FROM chat_sessions WHERE user_id = ? AND is_active = 1 ORDER BY updated_at DESC LIMIT 1",
        [$userId]
    );
    
    if ($existingSession) {
        return $existingSession['id'];
    }
    
    return $db->insert(
        "INSERT INTO chat_sessions (user_id, session_name) VALUES (?, ?)",
        [$userId, 'محادثة جديدة']
    );
}

// Save message to database
function saveMessage($db, $sessionId, $userId, $type, $content) {
    return $db->insert(
        "INSERT INTO chat_messages (session_id, user_id, message_type, content) VALUES (?, ?, ?, ?)",
        [$sessionId, $userId, $type, $content]
    );
}

// Get chat history
function getChatHistory($db, $userId, $sessionId = null) {
    if ($sessionId) {
        return $db->fetchAll(
            "SELECT * FROM chat_messages WHERE session_id = ? ORDER BY created_at ASC",
            [$sessionId]
        );
    }
    
    return $db->fetchAll(
        "SELECT cs.session_name, cm.* FROM chat_messages cm 
         JOIN chat_sessions cs ON cm.session_id = cs.id 
         WHERE cs.user_id = ? ORDER BY cs.updated_at DESC, cm.created_at ASC 
         LIMIT 100",
        [$userId]
    );
}
?> 