# دليل المساهمة - AI Code Bot

شكراً لك على اهتمامك بالمساهمة في تطوير AI Code Bot! 🚀

## كيفية البدء

### 1. Fork المشروع
1. اذهب إلى [GitHub Repository](https://github.com/aicodebot/ai-code-bot)
2. اضغط على زر "Fork" في الأعلى
3. انسخ المشروع إلى حسابك

### 2. Clone المشروع
```bash
git clone https://github.com/YOUR_USERNAME/ai-code-bot.git
cd ai-code-bot
```

### 3. إعداد البيئة
```bash
# تثبيت المتطلبات
composer install

# نسخ ملف البيئة
cp .env.example .env

# تعديل الإعدادات
# عدل ملف .env بالمعلومات الصحيحة
```

### 4. إنشاء Branch جديد
```bash
git checkout -b feature/your-feature-name
# أو
git checkout -b fix/your-bug-fix
```

## معايير الكود

### PHP
- استخدم **PHP 7.4+**
- اتبع **PSR-12** standards
- استخدم **namespaces** مناسبة
- أضف **type hints** حيثما أمكن
- اكتب **تعليقات واضحة** باللغة العربية

```php
<?php
namespace AICodeBot\Features;

/**
 * كلاس إدارة المشاريع
 * 
 * @package AICodeBot\Features
 * @author AI Code Bot
 */
class ProjectManager
{
    /**
     * إنشاء مشروع جديد
     * 
     * @param string $name اسم المشروع
     * @param string $description وصف المشروع
     * @return int معرف المشروع
     * @throws \Exception
     */
    public function createProject(string $name, string $description): int
    {
        // التحقق من المدخلات
        if (empty($name)) {
            throw new \Exception('اسم المشروع مطلوب');
        }
        
        // إنشاء المشروع
        return $this->projectRepository->create([
            'name' => $name,
            'description' => $description
        ]);
    }
}
```

### JavaScript
- استخدم **ES6+** features
- اتبع **Airbnb JavaScript Style Guide**
- استخدم **async/await** بدلاً من Promises
- أضف **JSDoc** comments

```javascript
/**
 * إنشاء مشروع جديد
 * @param {Object} projectData بيانات المشروع
 * @param {string} projectData.name اسم المشروع
 * @param {string} projectData.description وصف المشروع
 * @returns {Promise<Object>} المشروع المنشأ
 */
async function createProject(projectData) {
    try {
        // التحقق من البيانات
        if (!projectData.name) {
            throw new Error('اسم المشروع مطلوب');
        }
        
        // إرسال الطلب
        const response = await fetch('/api/projects', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(projectData)
        });
        
        return await response.json();
    } catch (error) {
        console.error('خطأ في إنشاء المشروع:', error);
        throw error;
    }
}
```

### CSS
- استخدم **CSS3** features
- اتبع **BEM** methodology
- استخدم **CSS variables** للألوان
- أضف **responsive design**

```css
/* متغيرات الألوان */
:root {
    --primary-color: #3498db;
    --secondary-color: #2ecc71;
    --accent-color: #e74c3c;
    --text-color: #2c3e50;
    --background-color: #f8f9fa;
}

/* مكونات */
.project-card {
    background: var(--background-color);
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.project-card:hover {
    transform: translateY(-5px);
}

.project-card__title {
    color: var(--text-color);
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.project-card__description {
    color: var(--text-color);
    opacity: 0.8;
    line-height: 1.6;
}

/* responsive design */
@media (max-width: 768px) {
    .project-card {
        padding: 1rem;
    }
    
    .project-card__title {
        font-size: 1.2rem;
    }
}
```

## إضافة ميزات جديدة

### 1. تخطيط الميزة
- اكتب **وصف مفصل** للميزة
- حدد **المتطلبات** والحدود
- ارسم **مخطط** للواجهة
- حدد **API endpoints** المطلوبة

### 2. تطوير الميزة
- ابدأ بـ **unit tests**
- اكتب **الكود** خطوة بخطوة
- أضف **validation** و error handling
- اكتب **documentation**

### 3. اختبار الميزة
```bash
# تشغيل الاختبارات
composer test

# فحص جودة الكود
composer stan
composer cs
```

## إصلاح الأخطاء

### 1. تحديد المشكلة
- اكتب **وصف واضح** للمشكلة
- أضف **خطوات التكرار**
- أرفق **screenshots** أو logs
- حدد **البيئة** (OS, PHP version, etc.)

### 2. إصلاح المشكلة
- ابحث عن **root cause**
- اكتب **test case** للمشكلة
- أصلح **الكود** خطوة بخطوة
- اختبر **الحل** جيداً

### 3. اختبار الإصلاح
```bash
# تشغيل الاختبارات
composer test

# اختبار يدوي
php -S localhost:8000
```

## إرسال Pull Request

### 1. تحديث Branch
```bash
git add .
git commit -m "feat: إضافة ميزة جديدة للمشاريع

- إضافة نموذج إنشاء المشاريع
- إضافة validation للبيانات
- إضافة unit tests
- تحديث documentation"
```

### 2. Push التغييرات
```bash
git push origin feature/your-feature-name
```

### 3. إنشاء Pull Request
1. اذهب إلى GitHub repository
2. اضغط على "Compare & pull request"
3. اكتب **title واضح** و **description مفصل**
4. أضف **labels** مناسبة
5. أضف **assignees** و **reviewers**

### 4. مراجعة الكود
- استجب **للمراجعات** بسرعة
- أضف **اختبارات إضافية** إذا طُلب
- حدث **documentation** إذا لزم الأمر
- احتفظ بـ **commit history** نظيف

## معايير الـ Commits

### تنسيق الـ Commit Message
```
type(scope): description

body

footer
```

### أنواع الـ Commits
- **feat**: ميزة جديدة
- **fix**: إصلاح خطأ
- **docs**: تحديث documentation
- **style**: تنسيق الكود
- **refactor**: إعادة هيكلة الكود
- **test**: إضافة أو تحديث الاختبارات
- **chore**: مهام صيانة

### أمثلة
```
feat(projects): إضافة نظام إدارة المشاريع

- إضافة نموذج إنشاء المشاريع
- إضافة validation للبيانات
- إضافة unit tests
- تحديث documentation

Closes #123
```

```
fix(auth): إصلاح مشكلة تسجيل الدخول

- إصلاح validation للبريد الإلكتروني
- إضافة error handling أفضل
- تحديث error messages

Fixes #456
```

## الاختبارات

### أنواع الاختبارات
- **Unit Tests**: اختبار الدوال الفردية
- **Integration Tests**: اختبار تكامل المكونات
- **Feature Tests**: اختبار الميزات كاملة
- **Browser Tests**: اختبار الواجهات

### كتابة الاختبارات
```php
<?php
namespace AICodeBot\Tests\Features;

use PHPUnit\Framework\TestCase;
use AICodeBot\Features\ProjectManager;

class ProjectManagerTest extends TestCase
{
    private ProjectManager $projectManager;
    
    protected function setUp(): void
    {
        $this->projectManager = new ProjectManager();
    }
    
    public function testCreateProjectWithValidData()
    {
        $name = 'مشروع تجريبي';
        $description = 'وصف المشروع التجريبي';
        
        $projectId = $this->projectManager->createProject($name, $description);
        
        $this->assertIsInt($projectId);
        $this->assertGreaterThan(0, $projectId);
    }
    
    public function testCreateProjectWithEmptyNameThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('اسم المشروع مطلوب');
        
        $this->projectManager->createProject('', 'وصف المشروع');
    }
}
```

## التوثيق

### تحديث README
- أضف **وصف** للميزة الجديدة
- أضف **أمثلة** للاستخدام
- حدث **قائمة الميزات**
- أضف **screenshots** إذا لزم الأمر

### تحديث API Documentation
- أضف **endpoints** الجديدة
- وثق **parameters** و **responses**
- أضف **أمثلة** للاستخدام
- حدث **error codes**

### تحديث CHANGELOG
- أضف **الميزات** الجديدة
- أضف **الإصلاحات** المهمة
- حدث **التواريخ** والإصدارات
- أضف **breaking changes** إذا وجدت

## التواصل

### قنوات التواصل
- **GitHub Issues**: للمشاكل والاقتراحات
- **GitHub Discussions**: للمناقشات العامة
- **Email**: admin@aicodebot.com
- **Website**: https://aicodebot.com

### اجتماعات المطورين
- **اجتماع أسبوعي**: كل يوم أحد
- **اجتماع شهري**: مراجعة التقدم
- **اجتماع ربع سنوي**: تخطيط الإصدارات

## المكافآت

### برنامج المساهمة
- **Contributor**: 5+ commits
- **Core Contributor**: 20+ commits
- **Maintainer**: 50+ commits
- **Project Lead**: 100+ commits

### المزايا
- **شهادة مساهمة** رسمية
- **وصول مبكر** للميزات الجديدة
- **دعم فني** متميز
- **فرص عمل** في المشروع

## شكر وتقدير

شكراً لك على مساهمتك في تطوير AI Code Bot! 🙏

كل مساهمة، مهما كانت صغيرة، تساعد في جعل هذا المشروع أفضل وأقوى! 💪

---

**AI Code Bot** - أقوى بوت برمجة في العالم! 🚀

*آخر تحديث: 2024-01-01* 