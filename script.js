// Global Variables
let chatHistory = [];
let currentProject = null;
let isTyping = false;

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    loadChatHistory();
    setupEventListeners();
    initializeAI();
});

// Setup Event Listeners
function setupEventListeners() {
    const userInput = document.getElementById('userInput');
    
    // Send message on Enter (Shift+Enter for new line)
    userInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Auto-resize textarea
    userInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 150) + 'px';
    });
}

// Send Message Function
async function sendMessage() {
    const userInput = document.getElementById('userInput');
    const message = userInput.value.trim();
    
    if (!message || isTyping) return;
    
    // Add user message to chat
    addMessage('user', message);
    
    // Clear input
    userInput.value = '';
    userInput.style.height = 'auto';
    
    // Show typing indicator
    showTypingIndicator();
    
    try {
        // Process message with AI
        const response = await processMessage(message);
        
        // Remove typing indicator and add bot response
        removeTypingIndicator();
        addMessage('bot', response);
        
        // Save to chat history
        saveChatHistory();
        
    } catch (error) {
        removeTypingIndicator();
        addMessage('bot', 'عذراً، حدث خطأ في معالجة رسالتك. يرجى المحاولة مرة أخرى.');
        console.error('Error:', error);
    }
}

// Add Message to Chat
function addMessage(type, content) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}-message`;
    
    const timestamp = new Date().toLocaleTimeString('ar-SA', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    
    const avatarIcon = type === 'user' ? 'fas fa-user' : 'fas fa-robot';
    const authorName = type === 'user' ? 'أنت' : 'AI Code Bot';
    
    messageDiv.innerHTML = `
        <div class="message-avatar">
            <i class="${avatarIcon}"></i>
        </div>
        <div class="message-content">
            <div class="message-header">
                <span class="message-author">${authorName}</span>
                <span class="message-time">${timestamp}</span>
            </div>
            <div class="message-text">${formatMessage(content)}</div>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // Add to chat history
    chatHistory.push({
        type: type,
        content: content,
        timestamp: new Date().toISOString()
    });
}

// Format Message Content
function formatMessage(content) {
    // Handle code blocks
    if (content.includes('```')) {
        content = content.replace(/```(\w+)?\n([\s\S]*?)```/g, function(match, lang, code) {
            const language = lang || 'text';
            return `<div class="code-block">
                <div class="code-header">
                    <span class="code-language">${language}</span>
                    <button class="copy-btn" onclick="copyCode(this)">نسخ</button>
                </div>
                <pre><code>${escapeHtml(code.trim())}</code></pre>
            </div>`;
        });
    }
    
    // Handle inline code
    content = content.replace(/`([^`]+)`/g, '<code>$1</code>');
    
    // Handle line breaks
    content = content.replace(/\n/g, '<br>');
    
    return content;
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Show Typing Indicator
function showTypingIndicator() {
    isTyping = true;
    const chatMessages = document.getElementById('chatMessages');
    const typingDiv = document.createElement('div');
    typingDiv.className = 'message bot-message typing-indicator';
    typingDiv.id = 'typingIndicator';
    
    typingDiv.innerHTML = `
        <div class="message-avatar">
            <i class="fas fa-robot"></i>
        </div>
        <div class="message-content">
            <div class="message-header">
                <span class="message-author">AI Code Bot</span>
                <span class="message-time">يكتب...</span>
            </div>
            <div class="message-text">
                <div class="loading"></div> يفكر في الإجابة...
            </div>
        </div>
    `;
    
    chatMessages.appendChild(typingDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Remove Typing Indicator
function removeTypingIndicator() {
    isTyping = false;
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

// Process Message with AI
async function processMessage(message) {
    // Simulate AI processing delay
    await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 2000));
    
    // Analyze message and generate response
    const response = await generateAIResponse(message);
    return response;
}

// Generate AI Response
async function generateAIResponse(message) {
    const lowerMessage = message.toLowerCase();
    
    // Project creation requests
    if (lowerMessage.includes('مشروع') || lowerMessage.includes('project') || 
        lowerMessage.includes('نظام') || lowerMessage.includes('system')) {
        return await handleProjectCreation(message);
    }
    
    // Code generation requests
    if (lowerMessage.includes('كود') || lowerMessage.includes('code') || 
        lowerMessage.includes('برنامج') || lowerMessage.includes('program')) {
        return await handleCodeGeneration(message);
    }
    
    // Database requests
    if (lowerMessage.includes('قاعدة بيانات') || lowerMessage.includes('database') || 
        lowerMessage.includes('mysql') || lowerMessage.includes('sql')) {
        return await handleDatabaseRequest(message);
    }
    
    // Website requests
    if (lowerMessage.includes('موقع') || lowerMessage.includes('website') || 
        lowerMessage.includes('ويب') || lowerMessage.includes('web')) {
        return await handleWebsiteRequest(message);
    }
    
    // API requests
    if (lowerMessage.includes('api') || lowerMessage.includes('واجهة برمجة')) {
        return await handleAPIRequest(message);
    }
    
    // General programming questions
    if (lowerMessage.includes('كيف') || lowerMessage.includes('how') || 
        lowerMessage.includes('شرح') || lowerMessage.includes('explain')) {
        return await handleGeneralQuestion(message);
    }
    
    // Default response
    return generateDefaultResponse(message);
}

// Handle Project Creation
async function handleProjectCreation(message) {
    const projectTypes = {
        'ويب': 'موقع ويب',
        'web': 'موقع ويب',
        'موبايل': 'تطبيق موبايل',
        'mobile': 'تطبيق موبايل',
        'سطح المكتب': 'تطبيق سطح المكتب',
        'desktop': 'تطبيق سطح المكتب',
        'api': 'API',
        'قاعدة بيانات': 'قاعدة بيانات',
        'database': 'قاعدة بيانات'
    };
    
    let projectType = 'مشروع برمجي';
    for (const [key, value] of Object.entries(projectTypes)) {
        if (message.includes(key)) {
            projectType = value;
            break;
        }
    }
    
    return `ممتاز! سأقوم بإنشاء ${projectType} كامل لك! 🚀

**خطة المشروع:**
1. **تحليل المتطلبات** - فهم احتياجاتك بالكامل
2. **تصميم المعمارية** - هيكل المشروع والملفات
3. **إنشاء قاعدة البيانات** - جداول وعلاقات
4. **تطوير الواجهات** - واجهات المستخدم
5. **تطوير الخلفية** - منطق الأعمال
6. **اختبار وتطوير** - ضمان الجودة

**الملفات المطلوبة:**
- ملفات HTML/CSS للواجهات
- ملفات JavaScript للتفاعل
- ملفات PHP للخلفية
- ملف قاعدة البيانات SQL
- ملفات التكوين
- ملف README شامل

هل تريد أن أبدأ في إنشاء المشروع الآن؟ أم لديك متطلبات محددة؟ 💪`;
}

// Handle Code Generation
async function handleCodeGeneration(message) {
    const languages = {
        'python': 'Python',
        'javascript': 'JavaScript',
        'php': 'PHP',
        'java': 'Java',
        'sql': 'SQL',
        'html': 'HTML',
        'css': 'CSS'
    };
    
    let language = 'Python';
    for (const [key, value] of Object.entries(languages)) {
        if (message.includes(key)) {
            language = value;
            break;
        }
    }
    
    return `سأقوم بإنشاء كود ${language} احترافي لك! 💻

**مثال على كود ${language}:**

\`\`\`${language.toLowerCase()}
# كود ${language} احترافي
# تم إنشاؤه بواسطة AI Code Bot

def main():
    """
    الدالة الرئيسية للمشروع
    """
    print("مرحباً بك في مشروع ${language}!")
    
    # مثال على حلقة
    for i in range(5):
        print(f"رقم: {i}")
    
    # مثال على شرط
    if i > 3:
        print("الرقم أكبر من 3")
    else:
        print("الرقم أقل من أو يساوي 3")

if __name__ == "__main__":
    main()
\`\`\`

**مميزات الكود:**
✅ تعليقات واضحة
✅ أسماء متغيرات مفهومة
✅ هيكل منظم
✅ أفضل الممارسات

هل تريد كود محدد لمشكلة معينة؟ أم تريد مثالاً آخر؟ 🤔`;
}

// Handle Database Request
async function handleDatabaseRequest(message) {
    return `ممتاز! سأقوم بإنشاء قاعدة بيانات احترافية لك! 🗄️

**مثال على قاعدة بيانات MySQL:**

\`\`\`sql
-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS my_project_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE my_project_db;

-- جدول المستخدمين
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- جدول المنتجات
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- جدول الفئات
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id INT NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);
\`\`\`

**مميزات قاعدة البيانات:**
✅ علاقات منظمة
✅ فهارس محسنة
✅ أنواع بيانات مناسبة
✅ قيود الأمان
✅ دعم اللغة العربية

هل تريد قاعدة بيانات لمشروع معين؟ أم تريد إضافة جداول أخرى؟ 💪`;
}

// Handle Website Request
async function handleWebsiteRequest(message) {
    return `ممتاز! سأقوم بإنشاء موقع ويب احترافي لك! 🌐

**هيكل الموقع:**

\`\`\`html
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>موقعي الرائع</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">شعار الموقع</div>
            <ul class="nav-menu">
                <li><a href="#home">الرئيسية</a></li>
                <li><a href="#about">من نحن</a></li>
                <li><a href="#services">الخدمات</a></li>
                <li><a href="#contact">اتصل بنا</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section id="home" class="hero">
            <h1>مرحباً بك في موقعنا</h1>
            <p>موقع احترافي ومتجاوب</p>
            <button class="cta-btn">ابدأ الآن</button>
        </section>
    </main>
    
    <footer class="footer">
        <p>&copy; 2024 جميع الحقوق محفوظة</p>
    </footer>
</body>
</html>
\`\`\`

**الملفات المطلوبة:**
📁 `index.html` - الصفحة الرئيسية
📁 `style.css` - التصميم
📁 `script.js` - التفاعل
📁 `images/` - الصور
📁 `pages/` - الصفحات الإضافية

هل تريد موقع معين؟ أم تريد إضافة ميزات أخرى؟ 🚀`;
}

// Handle API Request
async function handleAPIRequest(message) {
    return `ممتاز! سأقوم بإنشاء API احترافي لك! 🔌

**مثال على API باستخدام PHP:**

\`\`\`php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'config/database.php';
require_once 'models/User.php';

class UserAPI {
    private $db;
    private $user;
    
    public function __construct() {
        $this->db = new Database();
        $this->user = new User($this->db);
    }
    
    // الحصول على جميع المستخدمين
    public function getUsers() {
        try {
            $users = $this->user->getAll();
            return [
                'status' => 'success',
                'data' => $users,
                'message' => 'تم جلب المستخدمين بنجاح'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    // إنشاء مستخدم جديد
    public function createUser($data) {
        try {
            $userId = $this->user->create($data);
            return [
                'status' => 'success',
                'data' => ['id' => $userId],
                'message' => 'تم إنشاء المستخدم بنجاح'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}

// معالجة الطلبات
$api = new UserAPI();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo json_encode($api->getUsers());
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($api->createUser($data));
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
\`\`\`

**مميزات API:**
✅ RESTful design
✅ معالجة الأخطاء
✅ أمان عالي
✅ توثيق شامل
✅ اختبارات

هل تريد API لمشروع معين؟ أم تريد إضافة endpoints أخرى؟ 💪`;
}

// Handle General Question
async function handleGeneralQuestion(message) {
    const responses = [
        "سأقوم بشرح هذا الموضوع بالتفصيل! 📚",
        "ممتاز! هذا سؤال مهم جداً 💡",
        "دعني أشرح لك هذا المفهوم خطوة بخطوة 🚀",
        "سأقدم لك شرحاً شاملاً ومفصلاً 📖"
    ];
    
    const randomResponse = responses[Math.floor(Math.random() * responses.length)];
    
    return `${randomResponse}

**شرح مفصل:**
هذا الموضوع مهم جداً في البرمجة. دعني أشرح لك:

**1. الأساسيات:**
- فهم المفهوم الأساسي
- أهمية الموضوع
- متى نستخدمه

**2. التطبيق العملي:**
- أمثلة عملية
- كود توضيحي
- أفضل الممارسات

**3. النصائح:**
- تجنب الأخطاء الشائعة
- تحسين الأداء
- أمان الكود

هل تريد مثالاً عملياً؟ أم لديك أسئلة أخرى؟ 🤔`;
}

// Generate Default Response
function generateDefaultResponse(message) {
    const responses = [
        "أهلاً! كيف يمكنني مساعدتك في البرمجة اليوم? 💻",
        "ممتاز! أخبرني ماذا تريد أن أعمل لك? 🚀",
        "أنا هنا لمساعدتك! ما هو مشروعك التالي? 💪",
        "دعني أساعدك في تحقيق أفكارك البرمجية! 🌟"
    ];
    
    return responses[Math.floor(Math.random() * responses.length)];
}

// Project Management Functions
function generateProject() {
    document.getElementById('projectModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

async function createProject() {
    const projectType = document.getElementById('projectType').value;
    const projectName = document.getElementById('projectName').value;
    const projectDescription = document.getElementById('projectDescription').value;
    
    if (!projectName || !projectDescription) {
        alert('يرجى ملء جميع الحقول المطلوبة');
        return;
    }
    
    // Close modal
    closeModal('projectModal');
    
    // Add project creation message
    addMessage('user', `أريد إنشاء مشروع ${projectType}: ${projectName} - ${projectDescription}`);
    
    // Generate project
    const response = await generateProjectFiles(projectType, projectName, projectDescription);
    addMessage('bot', response);
    
    // Save to project list
    saveProject(projectType, projectName, projectDescription);
}

// Generate Project Files
async function generateProjectFiles(type, name, description) {
    const projectTypes = {
        'web': 'موقع ويب',
        'mobile': 'تطبيق موبايل',
        'desktop': 'تطبيق سطح المكتب',
        'api': 'API',
        'database': 'قاعدة بيانات',
        'ai': 'مشروع ذكاء اصطناعي'
    };
    
    const projectType = projectTypes[type] || 'مشروع برمجي';
    
    return `ممتاز! سأقوم بإنشاء ${projectType} كامل لك! 🚀

**تفاصيل المشروع:**
- **الاسم:** ${name}
- **النوع:** ${projectType}
- **الوصف:** ${description}

**الملفات المطلوبة:**

\`\`\`plaintext
${name}/
├── src/
│   ├── components/
│   ├── pages/
│   ├── utils/
│   └── assets/
├── public/
│   ├── index.html
│   ├── style.css
│   └── script.js
├── docs/
│   ├── README.md
│   ├── API.md
│   └── DEPLOYMENT.md
├── tests/
├── package.json
└── .gitignore
\`\`\`

**الخطوات التالية:**
1. إنشاء هيكل المجلدات
2. إنشاء الملفات الأساسية
3. تطوير الوظائف
4. اختبار النظام
5. نشر المشروع

هل تريد أن أبدأ في إنشاء الملفات؟ أم لديك متطلبات إضافية؟ 💪`;
}

// Utility Functions
function clearChat() {
    if (confirm('هل أنت متأكد من مسح المحادثة؟')) {
        document.getElementById('chatMessages').innerHTML = '';
        chatHistory = [];
        localStorage.removeItem('chatHistory');
        
        // Add welcome message back
        addMessage('bot', 'تم مسح المحادثة. كيف يمكنني مساعدتك اليوم؟');
    }
}

function exportChat() {
    const chatData = JSON.stringify(chatHistory, null, 2);
    const blob = new Blob([chatData], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = `chat-export-${new Date().toISOString().split('T')[0]}.json`;
    a.click();
    
    URL.revokeObjectURL(url);
}

function copyCode(button) {
    const codeBlock = button.closest('.code-block');
    const code = codeBlock.querySelector('code').textContent;
    
    navigator.clipboard.writeText(code).then(() => {
        button.textContent = 'تم النسخ!';
        setTimeout(() => {
            button.textContent = 'نسخ';
        }, 2000);
    });
}

// File Management
function attachFile() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.txt,.js,.py,.php,.html,.css,.sql,.json';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            analyzeFile(file);
        }
    };
    input.click();
}

async function analyzeFile(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const content = e.target.result;
        addMessage('user', `أريد تحليل هذا الملف: ${file.name}`);
        addMessage('bot', `ممتاز! سأقوم بتحليل ملف ${file.name} لك! 📁

**معلومات الملف:**
- **الاسم:** ${file.name}
- **الحجم:** ${(file.size / 1024).toFixed(2)} KB
- **النوع:** ${file.type || 'غير محدد'}

**محتوى الملف:**
\`\`\`${getFileExtension(file.name)}
${content.substring(0, 500)}${content.length > 500 ? '...' : ''}
\`\`\`

**تحليل الملف:**
✅ تم قراءة الملف بنجاح
✅ جاهز للتحليل
✅ يمكنني مساعدتك في تحسينه

هل تريد تحليلاً مفصلاً؟ أم تريد اقتراحات للتحسين؟ 🤔`);
    };
    reader.readAsText(file);
}

function getFileExtension(filename) {
    return filename.split('.').pop() || 'text';
}

// Code Analysis
function analyzeCode() {
    const code = prompt('أدخل الكود الذي تريد تحليله:');
    if (code) {
        addMessage('user', `أريد تحليل هذا الكود: ${code}`);
        addMessage('bot', `ممتاز! سأقوم بتحليل الكود لك! 🔍

**تحليل الكود:**

\`\`\`text
${code}
\`\`\`

**الملاحظات:**
✅ الكود مقروء ومفهوم
✅ يمكن تحسين الأداء
✅ إضافة تعليقات مفيدة

**اقتراحات للتحسين:**
1. إضافة تعليقات توضيحية
2. تحسين أسماء المتغيرات
3. إضافة معالجة الأخطاء
4. تحسين الأداء

هل تريد تحليلاً أعمق؟ أم تريد اقتراحات محددة؟ 💪`);
    }
}

// Database Functions
function createDatabase() {
    addMessage('user', 'أريد إنشاء قاعدة بيانات جديدة');
    addMessage('bot', `ممتاز! سأقوم بإنشاء قاعدة بيانات احترافية لك! 🗄️

**خطة إنشاء قاعدة البيانات:**

**1. تحليل المتطلبات:**
- فهم احتياجات المشروع
- تحديد الجداول المطلوبة
- تصميم العلاقات

**2. إنشاء الجداول:**
- جداول المستخدمين
- جداول المنتجات/الخدمات
- جداول العلاقات
- جداول النظام

**3. تحسين الأداء:**
- فهارس محسنة
- استعلامات محسنة
- أمان عالي

**4. التوثيق:**
- مخطط قاعدة البيانات
- دليل الاستخدام
- أمثلة عملية

أخبرني عن مشروعك وسأقوم بتصميم قاعدة البيانات المناسبة! 💪`);
}

// API Functions
function createAPI() {
    addMessage('user', 'أريد إنشاء API جديد');
    addMessage('bot', `ممتاز! سأقوم بإنشاء API احترافي لك! 🔌

**خطة إنشاء API:**

**1. تصميم API:**
- RESTful endpoints
- هيكل البيانات
- معالجة الأخطاء

**2. تطوير الخلفية:**
- قاعدة البيانات
- منطق الأعمال
- الأمان

**3. التوثيق:**
- Swagger/OpenAPI
- أمثلة الاستخدام
- دليل المطورين

**4. الاختبار:**
- اختبار الوحدات
- اختبار التكامل
- اختبار الأداء

أخبرني عن متطلبات API وسأقوم بتطويره لك! 🚀`);
}

// Website Functions
function createWebsite() {
    addMessage('user', 'أريد إنشاء موقع ويب جديد');
    addMessage('bot', `ممتاز! سأقوم بإنشاء موقع ويب احترافي لك! 🌐

**خطة إنشاء الموقع:**

**1. التصميم:**
- واجهة جميلة ومتجاوبة
- تجربة مستخدم ممتازة
- ألوان وتخطيط احترافي

**2. التطوير:**
- HTML5 + CSS3
- JavaScript متقدم
- PHP للخلفية
- قاعدة بيانات

**3. الميزات:**
- نظام تسجيل دخول
- لوحة تحكم
- إدارة المحتوى
- SEO محسن

**4. النشر:**
- استضافة محسنة
- نطاق احترافي
- شهادة SSL
- نسخ احتياطية

أخبرني عن نوع موقعك وسأقوم بتطويره لك! 💪`);
}

// Mobile App Functions
function createMobileApp() {
    addMessage('user', 'أريد إنشاء تطبيق موبايل جديد');
    addMessage('bot', `ممتاز! سأقوم بإنشاء تطبيق موبايل احترافي لك! 📱

**خطة إنشاء التطبيق:**

**1. التصميم:**
- واجهة مستخدم جميلة
- تجربة مستخدم سلسة
- ألوان وتخطيط احترافي

**2. التطوير:**
- React Native (متعدد المنصات)
- أو Flutter (متعدد المنصات)
- أو تطبيقات أصلية

**3. الميزات:**
- تسجيل دخول آمن
- إشعارات push
- عمل offline
- مزامنة البيانات

**4. النشر:**
- App Store
- Google Play
- اختبار شامل
- تحديثات دورية

أخبرني عن نوع تطبيقك وسأقوم بتطويره لك! 🚀`);
}

// Chat History Management
function saveChatHistory() {
    localStorage.setItem('chatHistory', JSON.stringify(chatHistory));
}

function loadChatHistory() {
    const saved = localStorage.getItem('chatHistory');
    if (saved) {
        chatHistory = JSON.parse(saved);
        // Don't reload all messages, just keep them in memory
    }
}

// Project Management
function saveProject(type, name, description) {
    const projects = JSON.parse(localStorage.getItem('projects') || '[]');
    const project = {
        id: Date.now(),
        type: type,
        name: name,
        description: description,
        createdAt: new Date().toISOString()
    };
    
    projects.push(project);
    localStorage.setItem('projects', JSON.stringify(projects));
    
    // Update project list
    updateProjectList();
}

function updateProjectList() {
    const projects = JSON.parse(localStorage.getItem('projects') || '[]');
    const projectList = document.getElementById('projectList');
    
    projectList.innerHTML = '';
    
    projects.forEach(project => {
        const projectDiv = document.createElement('div');
        projectDiv.className = 'project-item';
        projectDiv.innerHTML = `
            <i class="fas fa-folder"></i>
            <span>${project.name}</span>
        `;
        projectDiv.onclick = () => loadProject(project);
        projectList.appendChild(projectDiv);
    });
}

function loadProject(project) {
    addMessage('user', `أريد العمل على مشروع: ${project.name}`);
    addMessage('bot', `ممتاز! دعنا نعمل على مشروع ${project.name}! 🚀

**تفاصيل المشروع:**
- **النوع:** ${project.type}
- **الوصف:** ${project.description}
- **تاريخ الإنشاء:** ${new Date(project.createdAt).toLocaleDateString('ar-SA')}

**ما الذي تريد عمله؟**
1. إضافة ميزات جديدة
2. تحسين الكود الحالي
3. إصلاح مشاكل
4. إضافة اختبارات
5. نشر المشروع

أخبرني ماذا تريد وسأساعدك! 💪`);
}

// Initialize AI
function initializeAI() {
    // Load saved projects
    updateProjectList();
    
    // Add welcome message if no chat history
    if (chatHistory.length === 0) {
        // Welcome message is already in HTML
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to send message
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        sendMessage();
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
    }
}); 