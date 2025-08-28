# دليل التثبيت - AI Code Bot

دليل مفصل لتثبيت وتشغيل AI Code Bot على نظامك.

## المتطلبات الأساسية

### متطلبات النظام
- **نظام التشغيل**: Linux, macOS, أو Windows
- **المساحة**: 100MB على الأقل
- **الذاكرة**: 128MB RAM على الأقل
- **المعالج**: أي معالج حديث

### متطلبات البرامج
- **PHP**: 7.4 أو أحدث
- **MySQL**: 5.7 أو أحدث (أو MariaDB 10.2+)
- **خادم ويب**: Apache أو Nginx
- **Git**: لإدارة الإصدارات

### متطلبات PHP Extensions
```bash
# Extensions المطلوبة
- pdo
- pdo_mysql
- json
- mbstring
- openssl
- curl
- fileinfo
- gd
- zip
```

## طرق التثبيت

### الطريقة الأولى: التثبيت السريع (موصى به)

#### 1. تحميل المشروع
```bash
# Clone من GitHub
git clone https://github.com/aicodebot/ai-code-bot.git
cd ai-code-bot

# أو تحميل مباشر
wget https://github.com/aicodebot/ai-code-bot/archive/main.zip
unzip main.zip
cd ai-code-bot-main
```

#### 2. تشغيل التثبيت التلقائي
```bash
# إعطاء صلاحيات التنفيذ
chmod +x deploy.sh

# تشغيل التثبيت
./deploy.sh development
```

#### 3. الوصول للنظام
```
الموقع الرئيسي: http://localhost:8000
لوحة التحكم: http://localhost:8000/admin.php
```

### الطريقة الثانية: التثبيت اليدوي

#### 1. إعداد قاعدة البيانات
```sql
# تسجيل دخول MySQL
mysql -u root -p

# إنشاء قاعدة البيانات
CREATE DATABASE ai_code_bot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# إنشاء مستخدم (اختياري)
CREATE USER 'aicodebot'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON ai_code_bot.* TO 'aicodebot'@'localhost';
FLUSH PRIVILEGES;

# الخروج
EXIT;
```

#### 2. تشغيل قاعدة البيانات
```bash
# استيراد الجداول
mysql -u root -p ai_code_bot < database.sql
```

#### 3. تعديل الإعدادات
```bash
# نسخ ملف البيئة
cp .env.example .env

# تعديل الإعدادات
nano .env
```

```env
# تعديل هذه القيم
DB_HOST=localhost
DB_NAME=ai_code_bot
DB_USER=root
DB_PASS=your_password
```

#### 4. تثبيت المتطلبات
```bash
# تثبيت PHP dependencies
composer install

# إنشاء المجلدات المطلوبة
mkdir -p uploads cache logs ssl

# إعطاء الصلاحيات
chmod 755 uploads cache logs ssl
chmod 644 .env
```

#### 5. تشغيل النظام
```bash
# تشغيل خادم التطوير
php -S localhost:8000

# أو استخدام Apache/Nginx
```

### الطريقة الثالثة: التثبيت باستخدام Docker

#### 1. تثبيت Docker
```bash
# Ubuntu/Debian
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# macOS
brew install docker

# Windows
# تحميل Docker Desktop من الموقع الرسمي
```

#### 2. تشغيل النظام
```bash
# بناء وتشغيل الخدمات
docker-compose up -d

# عرض حالة الخدمات
docker-compose ps
```

#### 3. الوصول للنظام
```
الموقع الرئيسي: http://localhost
لوحة التحكم: http://localhost/admin.php
Adminer: http://localhost:8080
```

## التثبيت على استضافة مشتركة

### 1. رفع الملفات
```bash
# رفع الملفات عبر FTP/SFTP
# أو استخدام Git
git clone https://github.com/aicodebot/ai-code-bot.git
```

### 2. إنشاء قاعدة البيانات
- اذهب إلى لوحة تحكم الاستضافة
- أنشئ قاعدة بيانات MySQL جديدة
- سجل اسم قاعدة البيانات، المستخدم، وكلمة المرور

### 3. تعديل الإعدادات
```php
// عدل ملف config.php
define('DB_HOST', 'your_hosting_host');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
```

### 4. تشغيل التثبيت
```
افتح المتصفح واذهب إلى:
http://yourdomain.com/install.php
```

## التثبيت على VPS

### 1. إعداد الخادم
```bash
# تحديث النظام
sudo apt update && sudo apt upgrade -y

# تثبيت المتطلبات
sudo apt install -y apache2 mysql-server php php-mysql php-curl php-gd php-mbstring php-xml php-zip composer git

# تفعيل mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 2. إعداد قاعدة البيانات
```bash
# تأمين MySQL
sudo mysql_secure_installation

# إنشاء قاعدة البيانات
sudo mysql -u root -p
CREATE DATABASE ai_code_bot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'aicodebot'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON ai_code_bot.* TO 'aicodebot'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. إعداد Apache
```bash
# إنشاء Virtual Host
sudo nano /etc/apache2/sites-available/ai-code-bot.conf
```

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAdmin admin@yourdomain.com
    DocumentRoot /var/www/ai-code-bot
    
    <Directory /var/www/ai-code-bot>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/ai-code-bot_error.log
    CustomLog ${APACHE_LOG_DIR}/ai-code-bot_access.log combined
</VirtualHost>
```

```bash
# تفعيل الموقع
sudo a2ensite ai-code-bot.conf
sudo systemctl reload apache2
```

### 4. نشر التطبيق
```bash
# نسخ الملفات
sudo cp -r ai-code-bot /var/www/
sudo chown -R www-data:www-data /var/www/ai-code-bot
sudo chmod -R 755 /var/www/ai-code-bot

# تشغيل قاعدة البيانات
sudo mysql -u root -p ai_code_bot < /var/www/ai-code-bot/database.sql
```

## التثبيت على Windows

### 1. تثبيت XAMPP
- حمل XAMPP من الموقع الرسمي
- ثبت XAMPP مع Apache, MySQL, PHP
- شغل Apache و MySQL

### 2. إعداد المشروع
```bash
# انسخ المشروع إلى htdocs
xcopy ai-code-bot C:\xampp\htdocs\ai-code-bot /E /I

# اذهب للمجلد
cd C:\xampp\htdocs\ai-code-bot
```

### 3. إعداد قاعدة البيانات
- افتح phpMyAdmin: http://localhost/phpmyadmin
- أنشئ قاعدة بيانات جديدة: `ai_code_bot`
- استورد ملف `database.sql`

### 4. تعديل الإعدادات
```php
// عدل ملف config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ai_code_bot');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## التثبيت على macOS

### 1. تثبيت Homebrew
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### 2. تثبيت المتطلبات
```bash
# تثبيت PHP و MySQL
brew install php mysql

# تثبيت Composer
brew install composer

# تشغيل MySQL
brew services start mysql
```

### 3. إعداد المشروع
```bash
# نسخ المشروع
cp -r ai-code-bot ~/Sites/
cd ~/Sites/ai-code-bot

# إنشاء قاعدة البيانات
mysql -u root -e "CREATE DATABASE ai_code_bot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root ai_code_bot < database.sql
```

## استكشاف الأخطاء

### مشاكل شائعة

#### 1. خطأ في قاعدة البيانات
```
Error: Database connection failed
```
**الحل:**
- تأكد من تشغيل MySQL
- تحقق من بيانات الاتصال
- تأكد من وجود قاعدة البيانات

#### 2. خطأ في الصلاحيات
```
Error: Permission denied
```
**الحل:**
```bash
sudo chown -R www-data:www-data /var/www/ai-code-bot
sudo chmod -R 755 /var/www/ai-code-bot
```

#### 3. خطأ في PHP Extensions
```
Error: Required PHP extension not loaded
```
**الحل:**
```bash
# Ubuntu/Debian
sudo apt install php-pdo php-mysql php-curl php-gd php-mbstring php-xml php-zip

# CentOS/RHEL
sudo yum install php-pdo php-mysql php-curl php-gd php-mbstring php-xml php-zip
```

#### 4. خطأ في mod_rewrite
```
Error: .htaccess not working
```
**الحل:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### فحص النظام
```bash
# فحص PHP
php -v
php -m | grep -E "(pdo|mysql|json|mbstring|openssl)"

# فحص MySQL
mysql --version

# فحص Apache
apache2 -v
```

## التحديث

### تحديث تلقائي
```bash
# تحديث الكود
git pull origin main

# تحديث المتطلبات
composer update
npm update

# إعادة تشغيل الخدمات
sudo systemctl restart apache2
sudo systemctl restart mysql
```

### تحديث قاعدة البيانات
```bash
# نسخ احتياطية
mysqldump -u root -p ai_code_bot > backup.sql

# تشغيل التحديثات
mysql -u root -p ai_code_bot < update.sql
```

## النسخ الاحتياطية

### نسخ احتياطية تلقائية
```bash
# إنشاء نسخة احتياطية
make backup

# أو يدوياً
tar -czf backup-$(date +%Y%m%d).tar.gz --exclude='./backups' .
```

### استعادة النسخة الاحتياطية
```bash
# استعادة من نسخة احتياطية
tar -xzf backup-20240101.tar.gz

# أو استخدام الأمر
make rollback
```

## الأمان

### تأمين النظام
```bash
# تحديث النظام
sudo apt update && sudo apt upgrade

# تأمين MySQL
sudo mysql_secure_installation

# تأمين Apache
sudo a2enmod ssl
sudo a2enmod headers
```

### شهادة SSL
```bash
# تثبيت Certbot
sudo apt install certbot python3-certbot-apache

# الحصول على شهادة مجانية
sudo certbot --apache -d yourdomain.com
```

## الدعم

### الحصول على المساعدة
- **GitHub Issues**: للمشاكل التقنية
- **GitHub Discussions**: للمناقشات العامة
- **Email**: admin@aicodebot.com
- **Website**: https://aicodebot.com

### الموارد الإضافية
- [دليل المستخدم](README.md)
- [دليل المساهمة](CONTRIBUTING.md)
- [سجل التغييرات](CHANGELOG.md)
- [ملف الترخيص](LICENSE)

---

## ملخص سريع

```bash
# التثبيت السريع
git clone https://github.com/aicodebot/ai-code-bot.git
cd ai-code-bot
chmod +x deploy.sh
./deploy.sh development

# الوصول للنظام
# http://localhost:8000
```

**AI Code Bot** - أقوى بوت برمجة في العالم! 🚀💪

*آخر تحديث: 2024-01-01* 