# 🚀 دليل التثبيت الشامل - AI Code Bot

## 🌟 نظرة عامة

هذا الدليل يوضح كيفية تثبيت **AI Code Bot** - أقوى بوت برمجة في العالم - على جميع المنصات! 💻✨

---

## 📋 المتطلبات الأساسية

### 🖥️ متطلبات النظام
- **Windows 10/11** أو **macOS 10.15+** أو **Linux Ubuntu 18.04+**
- **4GB RAM** على الأقل
- **2GB مساحة فارغة** على القرص
- **اتصال بالإنترنت** مستقر

### 🔧 متطلبات البرمجيات
- **PHP 7.4+** (يفضل PHP 8.0+)
- **MySQL 5.7+** أو **MariaDB 10.2+**
- **Apache 2.4+** أو **Nginx 1.18+**
- **Git 2.20+**
- **Composer 2.0+**
- **Node.js 14+** (اختياري)

---

## 🚀 التثبيت السريع (5 دقائق)

### ⚡ الطريقة الأولى: Docker (الأسهل)

```bash
# 1. تحميل المشروع
git clone https://github.com/aicodebot/ai-code-bot.git
cd ai-code-bot

# 2. تشغيل النظام
docker-compose up -d

# 3. فتح المتصفح
# http://localhost
```

### ⚡ الطريقة الثانية: Make (أسهل)

```bash
# 1. تحميل المشروع
git clone https://github.com/aicodebot/ai-code-bot.git
cd ai-code-bot

# 2. تشغيل التثبيت التلقائي
make quick-start

# 3. فتح المتصفح
# http://localhost:8000
```

---

## 🐳 التثبيت باستخدام Docker

### 📥 تثبيت Docker

#### Windows
```bash
# 1. حمل Docker Desktop من
# https://www.docker.com/products/docker-desktop

# 2. ثبت Docker Desktop
# 3. أعد تشغيل الكمبيوتر
# 4. تأكد من تشغيل Docker
docker --version
```

#### macOS
```bash
# 1. حمل Docker Desktop من
# https://www.docker.com/products/docker-desktop

# 2. ثبت Docker Desktop
# 3. تأكد من تشغيل Docker
docker --version
```

#### Linux (Ubuntu)
```bash
# 1. تحديث النظام
sudo apt update && sudo apt upgrade -y

# 2. تثبيت Docker
sudo apt install docker.io docker-compose -y

# 3. إضافة المستخدم لمجموعة Docker
sudo usermod -aG docker $USER

# 4. تشغيل Docker
sudo systemctl start docker
sudo systemctl enable docker

# 5. تأكد من التثبيت
docker --version
```

### 🚀 تشغيل النظام
```bash
# 1. انتقل لمجلد المشروع
cd ai-code-bot

# 2. تشغيل النظام
docker-compose up -d

# 3. انتظر حتى يكتمل التحميل
docker-compose logs -f

# 4. فتح المتصفح
# 🌐 الموقع: http://localhost
# 🗄️ قاعدة البيانات: localhost:3306
# 📊 Adminer: http://localhost:8080
```

---

## 🛠️ التثبيت باستخدام Make

### 📥 تثبيت Make

#### Windows
```bash
# 1. تثبيت Chocolatey
# https://chocolatey.org/install

# 2. تثبيت Make
choco install make
```

#### macOS
```bash
# 1. تثبيت Homebrew
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 2. تثبيت Make
brew install make
```

#### Linux
```bash
# Make مثبت افتراضياً في معظم توزيعات Linux
make --version
```

### 🚀 تشغيل النظام
```bash
# 1. انتقل لمجلد المشروع
cd ai-code-bot

# 2. عرض الأوامر المتاحة
make help

# 3. التثبيت السريع
make quick-start

# 4. أو التثبيت الكامل
make install
```

---

## 🔧 التثبيت اليدوي

### 📥 تثبيت PHP

#### Windows
```bash
# 1. حمل XAMPP من
# https://www.apachefriends.org/

# 2. ثبت XAMPP
# 3. تأكد من تشغيل Apache و MySQL
# 4. أضف PHP للمسار
# C:\xampp\php
```

#### macOS
```bash
# 1. تثبيت Homebrew
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 2. تثبيت PHP
brew install php

# 3. تشغيل PHP
brew services start php
```

#### Linux (Ubuntu)
```bash
# 1. تحديث النظام
sudo apt update && sudo apt upgrade -y

# 2. تثبيت PHP
sudo apt install php php-mysql php-mbstring php-xml php-curl php-zip -y

# 3. تأكد من التثبيت
php --version
```

### 📥 تثبيت MySQL

#### Windows
```bash
# MySQL مثبت مع XAMPP
# أو حمل MySQL من https://dev.mysql.com/downloads/
```

#### macOS
```bash
# 1. تثبيت MySQL
brew install mysql

# 2. تشغيل MySQL
brew services start mysql

# 3. تعيين كلمة مرور
mysql_secure_installation
```

#### Linux (Ubuntu)
```bash
# 1. تثبيت MySQL
sudo apt install mysql-server -y

# 2. تشغيل MySQL
sudo systemctl start mysql
sudo systemctl enable mysql

# 3. تعيين كلمة مرور
sudo mysql_secure_installation
```

### 📥 تثبيت Composer

#### Windows
```bash
# 1. حمل Composer من
# https://getcomposer.org/download/

# 2. ثبت Composer
# 3. تأكد من التثبيت
composer --version
```

#### macOS/Linux
```bash
# 1. تحميل Composer
curl -sS https://getcomposer.org/installer | php

# 2. نقل Composer للمسار
sudo mv composer.phar /usr/local/bin/composer

# 3. تأكد من التثبيت
composer --version
```

### 🚀 تشغيل النظام
```bash
# 1. انتقل لمجلد المشروع
cd ai-code-bot

# 2. تثبيت التبعيات
composer install

# 3. إنشاء قاعدة البيانات
mysql -u root -p -e "CREATE DATABASE ai_code_bot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. استيراد البيانات
mysql -u root -p ai_code_bot < database.sql

# 5. تعديل الإعدادات
# افتح config.php وعدّل بيانات قاعدة البيانات

# 6. تشغيل النظام
php -S localhost:8000

# 7. فتح المتصفح
# http://localhost:8000
```

---

## 🌐 التثبيت على Hostinger

### 📥 رفع الملفات

#### الطريقة الأولى: File Manager
1. **افتح لوحة التحكم** في Hostinger
2. **اذهب لـ File Manager**
3. **انتقل لمجلد `public_html`**
4. **ارفع جميع ملفات المشروع**

#### الطريقة الثانية: FTP
```bash
# 1. استخدم FileZilla أو أي عميل FTP
# 2. اربط بالخادم
# 3. ارفع الملفات لمجلد `public_html`
```

### 🗄️ إنشاء قاعدة البيانات
1. **اذهب لـ MySQL Databases**
2. **أنشئ قاعدة بيانات جديدة**
3. **أنشئ مستخدم جديد** مع صلاحيات كاملة
4. **استورد ملف `database.sql`**

### ⚙️ تعديل الإعدادات
```php
// افتح config.php وعدّل
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_hostinger_db_name');
define('DB_USER', 'your_hostinger_username');
define('DB_PASS', 'your_hostinger_password');
define('APP_URL', 'https://yourdomain.com');
```

---

## 🔒 إعدادات الأمان

### 🛡️ حماية الملفات
```bash
# 1. حماية ملفات الإعدادات
chmod 600 config.php
chmod 600 .env

# 2. حماية المجلدات
chmod 755 uploads/
chmod 755 cache/
chmod 755 logs/
chmod 755 sessions/
```

### 🔐 إعدادات قاعدة البيانات
```sql
-- 1. إنشاء مستخدم محدود الصلاحيات
CREATE USER 'aicodebot'@'localhost' IDENTIFIED BY 'strong_password';

-- 2. منح الصلاحيات المطلوبة فقط
GRANT SELECT, INSERT, UPDATE, DELETE ON ai_code_bot.* TO 'aicodebot'@'localhost';

-- 3. تطبيق التغييرات
FLUSH PRIVILEGES;
```

### 🌐 إعدادات HTTPS
```apache
# في ملف .htaccess
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 🧪 اختبار النظام

### ✅ اختبار الواجهة
1. **افتح المتصفح** على `http://localhost:8000`
2. **تأكد من ظهور** واجهة AI Code Bot
3. **تحقق من التصميم** واللغة العربية

### ✅ اختبار قاعدة البيانات
```bash
# 1. فتح phpMyAdmin
# 2. التحقق من وجود الجداول
# 3. اختبار الاتصال
mysql -u username -p ai_code_bot -e "SHOW TABLES;"
```

### ✅ اختبار المحادثة
1. **اكتب رسالة** للبوت
2. **تأكد من الرد** من البوت
3. **اختبر إنشاء مشروع** بسيط

---

## 🚨 حل المشاكل

### ❌ مشكلة: لا يمكن الاتصال بقاعدة البيانات
```bash
# 1. تأكد من تشغيل MySQL
sudo systemctl status mysql

# 2. تحقق من بيانات الاتصال
# 3. تأكد من صلاحيات المستخدم
mysql -u username -p -e "SHOW DATABASES;"
```

### ❌ مشكلة: صفحة فارغة
```bash
# 1. تحقق من سجلات الأخطاء
tail -f logs/php_errors.log

# 2. فعّل عرض الأخطاء في PHP
# 3. تحقق من إعدادات .htaccess
```

### ❌ مشكلة: لا يمكن رفع الملفات
```bash
# 1. تحقق من أذونات المجلدات
ls -la uploads/
ls -la cache/
ls -la logs/

# 2. أعد تعيين الأذونات
chmod 755 uploads/ cache/ logs/
```

### ❌ مشكلة: خطأ في Composer
```bash
# 1. تحديث Composer
composer self-update

# 2. مسح التخزين المؤقت
composer clear-cache

# 3. إعادة تثبيت التبعيات
composer install --no-cache
```

---

## 📚 أوامر مفيدة

### 🐳 أوامر Docker
```bash
# عرض الحاويات
docker ps

# عرض السجلات
docker-compose logs -f

# إيقاف النظام
docker-compose down

# إعادة تشغيل النظام
docker-compose restart

# حذف النظام
docker-compose down -v
```

### 🛠️ أوامر Make
```bash
# عرض المساعدة
make help

# تشغيل النظام
make start

# إيقاف النظام
make stop

# إعادة تشغيل النظام
make restart

# عرض السجلات
make logs

# عرض الحالة
make status
```

### 🔧 أوامر النظام
```bash
# فحص إصدار PHP
php --version

# فحص إصدار MySQL
mysql --version

# فحص إصدار Composer
composer --version

# فحص إصدار Git
git --version
```

---

## 🌟 نصائح للتثبيت

### 💡 نصائح عامة
- **ابدأ بالتثبيت السريع** أولاً
- **استخدم Docker** إذا كنت مبتدئ
- **احتفظ بنسخة احتياطية** من قاعدة البيانات
- **اختبر النظام** قبل النشر للإنتاج

### 🛡️ نصائح الأمان
- **غيّر كلمات المرور** الافتراضية
- **فعّل HTTPS** في الإنتاج
- **احمِ ملفات الإعدادات** من الوصول العام
- **استخدم مستخدم قاعدة بيانات** محدود الصلاحيات

### ⚡ نصائح الأداء
- **فعّل OPcache** في PHP
- **استخدم Redis** للتخزين المؤقت
- **حسّن قاعدة البيانات** بالفهارس
- **ضغط الملفات** باستخدام Gzip

---

## 🎊 بعد التثبيت

### 🚀 الخطوات التالية
1. **جرب النظام** - تحدث مع البوت
2. **أنشئ مشروعك الأول** - موقع ويب أو تطبيق
3. **شارك تجربتك** - مع المجتمع
4. **ساهم في التطوير** - أضف ميزات جديدة

### 📚 الموارد المفيدة
- **`README.md`** - نظرة عامة
- **`QUICKSTART.md`** - البدء السريع
- **`DEMO.md`** - أمثلة عملية
- **`FEATURES.md`** - جميع المميزات

### 🌍 الانضمام للمجتمع
- **GitHub**: https://github.com/aicodebot/ai-code-bot
- **Discord**: https://discord.gg/aicodebot
- **Telegram**: https://t.me/aicodebot

---

## 🎯 الخلاصة

**AI Code Bot** سهل التثبيت ويعمل في **أقل من 5 دقائق**!

### ✅ طرق التثبيت
1. **Docker** - أسهل طريقة (5 دقائق)
2. **Make** - أوامر بسيطة (5 دقائق)
3. **يدوي** - تحكم كامل (15 دقيقة)
4. **Hostinger** - استضافة مشتركة (10 دقائق)

### 🌟 ابدأ الآن!
**اتبع هذا الدليل وستحصل على أقوى بوت برمجة في العالم!** 🚀💻✨

---

## 📞 الدعم الفني

### 🆘 في حالات الطوارئ
- **البريد الإلكتروني**: emergency@aicodebot.com
- **Discord**: تواصل مع المشرفين مباشرة
- **GitHub Issues**: أنشئ issue جديد

### 💬 للاستفسارات العامة
- **البريد الإلكتروني**: support@aicodebot.com
- **GitHub Discussions**: ناقش مع المجتمع
- **Telegram**: انضم للمجموعة

---

*آخر تحديث: 2024-01-01*
*الإصدار: 1.0.0*
*المطور: AI Code Bot* 