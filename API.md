# API Documentation - AI Code Bot

توثيق شامل لـ API الخاص بـ AI Code Bot.

## نظرة عامة

AI Code Bot يوفر API RESTful شامل للتفاعل مع النظام برمجياً.

### Base URL
```
Development: http://localhost:8000/api
Production: https://yourdomain.com/api
```

### Authentication
جميع الطلبات تتطلب API key في header:
```
Authorization: Bearer YOUR_API_KEY
```

### Response Format
جميع الاستجابات تأتي بصيغة JSON:
```json
{
  "success": true,
  "data": {},
  "message": "تم تنفيذ العملية بنجاح",
  "timestamp": "2024-01-01T12:00:00Z"
}
```

## Endpoints

### 1. Authentication

#### تسجيل الدخول
```http
POST /auth/login
```

**Request Body:**
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 1,
      "username": "admin",
      "email": "admin@aicodebot.com",
      "role": "admin"
    }
  },
  "message": "تم تسجيل الدخول بنجاح"
}
```

#### تسجيل مستخدم جديد
```http
POST /auth/register
```

**Request Body:**
```json
{
  "username": "newuser",
  "email": "user@example.com",
  "password": "password123",
  "full_name": "مستخدم جديد"
}
```

### 2. Chat

#### إرسال رسالة
```http
POST /chat/send
```

**Request Body:**
```json
{
  "message": "ابني لي موقع ويب للتجارة الإلكترونية",
  "session_id": null
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "response": "ممتاز! سأقوم بإنشاء موقع ويب للتجارة الإلكترونية...",
    "session_id": 123,
    "message_id": 456
  }
}
```

#### جلب تاريخ المحادثة
```http
GET /chat/history?session_id=123&limit=50
```

**Response:**
```json
{
  "success": true,
  "data": {
    "session": {
      "id": 123,
      "name": "محادثة جديدة",
      "created_at": "2024-01-01T12:00:00Z"
    },
    "messages": [
      {
        "id": 456,
        "type": "user",
        "content": "ابني لي موقع ويب",
        "created_at": "2024-01-01T12:00:00Z"
      },
      {
        "id": 457,
        "type": "bot",
        "content": "ممتاز! سأقوم بإنشاء...",
        "created_at": "2024-01-01T12:01:00Z"
      }
    ]
  }
}
```

### 3. Projects

#### إنشاء مشروع جديد
```http
POST /projects/create
```

**Request Body:**
```json
{
  "name": "موقع التجارة الإلكترونية",
  "description": "موقع متكامل للتجارة الإلكترونية",
  "type": "web",
  "technologies": ["PHP", "MySQL", "JavaScript"],
  "requirements": "نظام تسجيل دخول، إدارة المنتجات، نظام دفع"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "project": {
      "id": 1,
      "name": "موقع التجارة الإلكترونية",
      "status": "planning",
      "created_at": "2024-01-01T12:00:00Z"
    },
    "files": [
      {
        "name": "index.php",
        "path": "src/",
        "type": "php"
      },
      {
        "name": "database.sql",
        "path": "database/",
        "type": "sql"
      }
    ]
  }
}
```

#### جلب المشاريع
```http
GET /projects?user_id=1&status=active&limit=10
```

**Response:**
```json
{
  "success": true,
  "data": {
    "projects": [
      {
        "id": 1,
        "name": "موقع التجارة الإلكترونية",
        "type": "web",
        "status": "development",
        "created_at": "2024-01-01T12:00:00Z"
      }
    ],
    "total": 1,
    "page": 1,
    "per_page": 10
  }
}
```

#### تحديث حالة المشروع
```http
PUT /projects/{id}/status
```

**Request Body:**
```json
{
  "status": "completed"
}
```

### 4. Code Generation

#### إنشاء كود
```http
POST /code/generate
```

**Request Body:**
```json
{
  "language": "python",
  "description": "دالة لحل معادلة من الدرجة الثانية",
  "requirements": "استخدام معادلة المميز، معالجة الأخطاء"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "code": "def solve_quadratic(a, b, c):\n    # حل معادلة من الدرجة الثانية\n    discriminant = b**2 - 4*a*c\n    if discriminant < 0:\n        return None\n    x1 = (-b + math.sqrt(discriminant)) / (2*a)\n    x2 = (-b - math.sqrt(discriminant)) / (2*a)\n    return x1, x2",
    "language": "python",
    "explanation": "هذه الدالة تحل معادلة من الدرجة الثانية...",
    "complexity": "O(1)",
    "dependencies": ["math"]
  }
}
```

#### تحليل الكود
```http
POST /code/analyze
```

**Request Body:**
```json
{
  "code": "def hello():\n    print('Hello World')",
  "language": "python"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "analysis": {
      "complexity": "O(1)",
      "lines": 2,
      "functions": 1,
      "suggestions": [
        "أضف docstring للدالة",
        "أضف type hints"
      ]
    }
  }
}
```

### 5. Database

#### إنشاء قاعدة بيانات
```http
POST /database/create
```

**Request Body:**
```json
{
  "name": "مستشفى",
  "description": "قاعدة بيانات لإدارة المستشفى",
  "tables": [
    {
      "name": "patients",
      "fields": [
        {"name": "id", "type": "INT", "primary": true},
        {"name": "name", "type": "VARCHAR(100)"},
        {"name": "age", "type": "INT"}
      ]
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "sql": "CREATE TABLE patients (\n    id INT AUTO_INCREMENT PRIMARY KEY,\n    name VARCHAR(100) NOT NULL,\n    age INT\n);",
    "tables": 1,
    "fields": 3
  }
}
```

### 6. Files

#### رفع ملف
```http
POST /files/upload
Content-Type: multipart/form-data
```

**Form Data:**
- `file`: الملف المرفوع
- `type`: نوع الملف (code, image, document)
- `description`: وصف الملف

**Response:**
```json
{
  "success": true,
  "data": {
    "file": {
      "id": 1,
      "name": "app.py",
      "size": 1024,
      "type": "python",
      "url": "/uploads/app.py"
    }
  }
}
```

#### تحليل ملف
```http
POST /files/analyze
```

**Request Body:**
```json
{
  "file_id": 1
}
```

### 7. Users

#### جلب معلومات المستخدم
```http
GET /users/profile
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "email": "admin@aicodebot.com",
      "full_name": "مدير النظام",
      "role": "admin",
      "created_at": "2024-01-01T12:00:00Z"
    }
  }
}
```

#### تحديث الملف الشخصي
```http
PUT /users/profile
```

**Request Body:**
```json
{
  "full_name": "اسم جديد",
  "email": "newemail@example.com"
}
```

### 8. System

#### حالة النظام
```http
GET /system/status
```

**Response:**
```json
{
  "success": true,
  "data": {
    "status": "healthy",
    "version": "1.0.0",
    "uptime": "2 days, 5 hours",
    "memory_usage": "45%",
    "database_status": "connected",
    "active_users": 25
  }
}
```

#### إحصائيات النظام
```http
GET /system/stats
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_users": 100,
    "total_projects": 50,
    "total_messages": 1000,
    "active_sessions": 15,
    "system_load": "0.5"
  }
}
```

## Error Handling

### Error Response Format
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "بيانات غير صحيحة",
    "details": {
      "field": "username",
      "issue": "مطلوب"
    }
  },
  "timestamp": "2024-01-01T12:00:00Z"
}
```

### Common Error Codes
- `AUTHENTICATION_ERROR`: خطأ في المصادقة
- `AUTHORIZATION_ERROR`: خطأ في الصلاحيات
- `VALIDATION_ERROR`: خطأ في البيانات
- `NOT_FOUND`: المورد غير موجود
- `RATE_LIMIT_EXCEEDED`: تجاوز حد الطلبات
- `INTERNAL_ERROR`: خطأ داخلي في النظام

## Rate Limiting

### Limits
- **Free Tier**: 100 requests/hour
- **Premium Tier**: 1000 requests/hour
- **Enterprise**: Unlimited

### Headers
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1640995200
```

## Pagination

### Query Parameters
```
GET /projects?page=1&per_page=20&sort=created_at&order=desc
```

### Response Headers
```
X-Total-Count: 150
X-Page-Count: 8
X-Current-Page: 1
X-Per-Page: 20
```

## Webhooks

### إنشاء Webhook
```http
POST /webhooks/create
```

**Request Body:**
```json
{
  "url": "https://yourdomain.com/webhook",
  "events": ["project.created", "message.sent"],
  "secret": "your_secret_key"
}
```

### Webhook Payload
```json
{
  "event": "project.created",
  "timestamp": "2024-01-01T12:00:00Z",
  "data": {
    "project_id": 1,
    "project_name": "موقع جديد"
  },
  "signature": "sha256=..."
}
```

## SDKs

### PHP SDK
```bash
composer require aicodebot/php-sdk
```

```php
use AICodeBot\Client;

$client = new Client('YOUR_API_KEY');
$response = $client->chat()->send('ابني لي موقع ويب');
```

### JavaScript SDK
```bash
npm install aicodebot-js-sdk
```

```javascript
import { AICodeBot } from 'aicodebot-js-sdk';

const client = new AICodeBot('YOUR_API_KEY');
const response = await client.chat.send('ابني لي موقع ويب');
```

### Python SDK
```bash
pip install aicodebot-python-sdk
```

```python
from aicodebot import Client

client = Client('YOUR_API_KEY')
response = client.chat.send('ابني لي موقع ويب')
```

## Examples

### إنشاء مشروع كامل
```javascript
// 1. إنشاء المشروع
const project = await client.projects.create({
  name: 'تطبيق إدارة المهام',
  type: 'mobile',
  description: 'تطبيق React Native لإدارة المهام'
});

// 2. إنشاء قاعدة البيانات
const database = await client.database.create({
  name: 'tasks_db',
  tables: [
    {
      name: 'tasks',
      fields: [
        { name: 'id', type: 'INT', primary: true },
        { name: 'title', type: 'VARCHAR(200)' },
        { name: 'completed', type: 'BOOLEAN' }
      ]
    }
  ]
});

// 3. إنشاء الكود
const code = await client.code.generate({
  language: 'javascript',
  description: 'مكون React Native لإضافة مهمة جديدة'
});
```

### Chat Bot Integration
```php
<?php
use AICodeBot\Client;

$client = new Client('YOUR_API_KEY');

// إرسال رسالة
$response = $client->chat()->send('كيف يمكنني إنشاء API؟');

// حفظ المحادثة
$sessionId = $response['data']['session_id'];

// جلب التاريخ
$history = $client->chat()->history($sessionId);
?>
```

## Testing

### Test Environment
```
Base URL: https://staging.aicodebot.com/api
API Key: test_key_123
```

### Test Data
```json
{
  "test_user": {
    "username": "testuser",
    "password": "testpass123"
  },
  "test_project": {
    "name": "مشروع تجريبي",
    "type": "web"
  }
}
```

## Support

### Documentation
- **API Reference**: https://docs.aicodebot.com/api
- **Examples**: https://docs.aicodebot.com/examples
- **SDKs**: https://docs.aicodebot.com/sdks

### Contact
- **Email**: api@aicodebot.com
- **GitHub**: https://github.com/aicodebot/api-issues
- **Discord**: https://discord.gg/aicodebot

---

**AI Code Bot API** - أقوى API للبرمجة الذكية! 🚀💻

*آخر تحديث: 2024-01-01* 