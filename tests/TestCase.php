<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base Test Case for AI Code Bot
 * فئة الاختبار الأساسية لبوت البرمجة الذكي
 */
class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment
        // إعداد بيئة الاختبار
        $this->setupTestEnvironment();
    }
    
    protected function tearDown(): void
    {
        // Clean up test environment
        // تنظيف بيئة الاختبار
        $this->cleanupTestEnvironment();
        
        parent::tearDown();
    }
    
    /**
     * Set up test environment
     * إعداد بيئة الاختبار
     */
    protected function setupTestEnvironment(): void
    {
        // Set test environment variables
        // تعيين متغيرات بيئة الاختبار
        putenv('APP_ENV=testing');
        putenv('DB_DATABASE=ai_code_bot_test');
        
        // Create test database if needed
        // إنشاء قاعدة بيانات الاختبار إذا لزم الأمر
        $this->createTestDatabase();
    }
    
    /**
     * Clean up test environment
     * تنظيف بيئة الاختبار
     */
    protected function cleanupTestEnvironment(): void
    {
        // Clean up test data
        // تنظيف بيانات الاختبار
        $this->cleanupTestData();
        
        // Reset environment variables
        // إعادة تعيين متغيرات البيئة
        putenv('APP_ENV');
        putenv('DB_DATABASE');
    }
    
    /**
     * Create test database
     * إنشاء قاعدة بيانات الاختبار
     */
    protected function createTestDatabase(): void
    {
        // Implementation for creating test database
        // تنفيذ إنشاء قاعدة بيانات الاختبار
    }
    
    /**
     * Clean up test data
     * تنظيف بيانات الاختبار
     */
    protected function cleanupTestData(): void
    {
        // Implementation for cleaning up test data
        // تنفيذ تنظيف بيانات الاختبار
    }
    
    /**
     * Assert response is JSON
     * التأكد من أن الاستجابة JSON
     */
    protected function assertJsonResponse($response): void
    {
        $this->assertIsString($response);
        $this->assertJson($response);
    }
    
    /**
     * Assert response has status code
     * التأكد من أن الاستجابة لها رمز الحالة
     */
    protected function assertResponseStatus($response, int $statusCode): void
    {
        $this->assertEquals($statusCode, $response->getStatusCode());
    }
    
    /**
     * Assert response contains key
     * التأكد من أن الاستجابة تحتوي على المفتاح
     */
    protected function assertResponseContains($response, string $key): void
    {
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey($key, $data);
    }
} 