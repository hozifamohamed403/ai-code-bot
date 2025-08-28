<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Code Bot - بوت البرمجة الذكي</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <i class="fas fa-robot"></i>
                <h1>AI Code Bot</h1>
                <span>بوت البرمجة الذكي</span>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="clearChat()">
                    <i class="fas fa-trash"></i> مسح المحادثة
                </button>
                <button class="btn btn-primary" onclick="exportChat()">
                    <i class="fas fa-download"></i> تصدير
                </button>
            </div>
        </header>

        <!-- Main Chat Area -->
        <main class="chat-container">
            <div class="chat-messages" id="chatMessages">
                <!-- Welcome Message -->
                <div class="message bot-message">
                    <div class="message-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-author">AI Code Bot</span>
                            <span class="message-time">الآن</span>
                        </div>
                        <div class="message-text">
                            أهلاً وسهلاً! أنا بوت البرمجة الذكي 🚀<br>
                            يمكنني مساعدتك في:<br>
                            • إنشاء مشاريع كاملة من الصفر<br>
                            • كتابة كود بلغات برمجة متعددة<br>
                            • تحليل وتصحيح الأخطاء<br>
                            • شرح المفاهيم البرمجية<br>
                            • تصميم قواعد البيانات<br>
                            • إنشاء واجهات مستخدم<br>
                            • حل المشاكل المعقدة<br><br>
                            اكتب لي ما تريد وسأقوم بمساعدتك! 💪
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="chat-input-container">
                <div class="input-wrapper">
                    <textarea 
                        id="userInput" 
                        placeholder="اكتب رسالتك هنا... يمكنك طلب إنشاء مشروع كامل، كتابة كود، أو شرح مفهوم معين..."
                        rows="3"
                    ></textarea>
                    <button class="send-btn" onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="input-actions">
                    <button class="action-btn" onclick="attachFile()">
                        <i class="fas fa-paperclip"></i> ملف
                    </button>
                    <button class="action-btn" onclick="generateProject()">
                        <i class="fas fa-project-diagram"></i> مشروع جديد
                    </button>
                    <button class="action-btn" onclick="analyzeCode()">
                        <i class="fas fa-code"></i> تحليل كود
                    </button>
                </div>
            </div>
        </main>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-section">
                <h3>المشاريع السابقة</h3>
                <div class="project-list" id="projectList">
                    <div class="project-item">
                        <i class="fas fa-folder"></i>
                        <span>مشروع جديد</span>
                    </div>
                </div>
            </div>
            
            <div class="sidebar-section">
                <h3>أدوات سريعة</h3>
                <button class="tool-btn" onclick="createDatabase()">
                    <i class="fas fa-database"></i> قاعدة بيانات
                </button>
                <button class="tool-btn" onclick="createAPI()">
                    <i class="fas fa-server"></i> API
                </button>
                <button class="tool-btn" onclick="createWebsite()">
                    <i class="fas fa-globe"></i> موقع ويب
                </button>
                <button class="tool-btn" onclick="createMobileApp()">
                    <i class="fas fa-mobile-alt"></i> تطبيق موبايل
                </button>
            </div>

            <div class="sidebar-section">
                <h3>اللغات المدعومة</h3>
                <div class="language-tags">
                    <span class="tag">Python</span>
                    <span class="tag">JavaScript</span>
                    <span class="tag">PHP</span>
                    <span class="tag">Java</span>
                    <span class="tag">C++</span>
                    <span class="tag">SQL</span>
                    <span class="tag">HTML/CSS</span>
                    <span class="tag">React</span>
                    <span class="tag">Node.js</span>
                    <span class="tag">Django</span>
                </div>
            </div>
        </aside>
    </div>

    <!-- Modals -->
    <div id="projectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>إنشاء مشروع جديد</h2>
                <span class="close" onclick="closeModal('projectModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>نوع المشروع:</label>
                    <select id="projectType">
                        <option value="web">موقع ويب</option>
                        <option value="mobile">تطبيق موبايل</option>
                        <option value="desktop">تطبيق سطح المكتب</option>
                        <option value="api">API</option>
                        <option value="database">قاعدة بيانات</option>
                        <option value="ai">ذكاء اصطناعي</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>اسم المشروع:</label>
                    <input type="text" id="projectName" placeholder="أدخل اسم المشروع">
                </div>
                <div class="form-group">
                    <label>الوصف:</label>
                    <textarea id="projectDescription" placeholder="صف مشروعك بالتفصيل..."></textarea>
                </div>
                <div class="form-group">
                    <label>اللغات المطلوبة:</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" value="python"> Python</label>
                        <label><input type="checkbox" value="javascript"> JavaScript</label>
                        <label><input type="checkbox" value="php"> PHP</label>
                        <label><input type="checkbox" value="java"> Java</label>
                        <label><input type="checkbox" value="sql"> SQL</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('projectModal')">إلغاء</button>
                <button class="btn btn-primary" onclick="createProject()">إنشاء المشروع</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html> 