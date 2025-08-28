# AI Code Bot Makefile
# ملف Makefile لبوت البرمجة الذكي

.PHONY: help install start stop restart build deploy test clean backup rollback

# Default target
help:
	@echo "AI Code Bot - أقوى بوت برمجة في العالم! 🚀"
	@echo ""
	@echo "الأوامر المتاحة:"
	@echo "  install    - تثبيت النظام"
	@echo "  start      - تشغيل النظام"
	@echo "  stop       - إيقاف النظام"
	@echo "  restart    - إعادة تشغيل النظام"
	@echo "  build      - بناء النظام"
	@echo "  deploy     - نشر النظام"
	@echo "  test       - تشغيل الاختبارات"
	@echo "  clean      - تنظيف النظام"
	@echo "  backup     - إنشاء نسخة احتياطية"
	@echo "  rollback   - العودة لإصدار سابق"
	@echo "  logs       - عرض السجلات"
	@echo "  status     - حالة النظام"
	@echo "  update     - تحديث النظام"
	@echo "  help       - عرض هذه المساعدة"

# Install the system
install:
	@echo "🔧 تثبيت AI Code Bot..."
	@chmod +x deploy.sh
	@./deploy.sh development
	@echo "✅ تم التثبيت بنجاح!"

# Start the system
start:
	@echo "🚀 تشغيل AI Code Bot..."
	@if [ -f "docker-compose.yml" ]; then \
		docker-compose up -d; \
		echo "✅ تم تشغيل النظام بنجاح!"; \
	else \
		php -S localhost:8000 -t .; \
		echo "✅ تم تشغيل خادم التطوير على المنفذ 8000"; \
	fi

# Stop the system
stop:
	@echo "⏹️ إيقاف AI Code Bot..."
	@if [ -f "docker-compose.yml" ]; then \
		docker-compose down; \
		echo "✅ تم إيقاف النظام"; \
	else \
		pkill -f "php -S localhost:8000" || true; \
		echo "✅ تم إيقاف خادم التطوير"; \
	fi

# Restart the system
restart: stop start
	@echo "🔄 تم إعادة تشغيل النظام"

# Build the system
build:
	@echo "🔨 بناء AI Code Bot..."
	@if [ -f "docker-compose.yml" ]; then \
		docker-compose build --no-cache; \
		echo "✅ تم بناء النظام بنجاح!"; \
	else \
		echo "⚠️ ملف docker-compose.yml غير موجود"; \
	fi

# Deploy the system
deploy:
	@echo "🚀 نشر AI Code Bot..."
	@chmod +x deploy.sh
	@./deploy.sh production
	@echo "✅ تم النشر بنجاح!"

# Run tests
test:
	@echo "🧪 تشغيل الاختبارات..."
	@if [ -f "composer.json" ]; then \
		composer test || echo "⚠️ بعض الاختبارات فشلت"; \
	fi
	@if [ -f "package.json" ]; then \
		npm test || echo "⚠️ بعض الاختبارات فشلت"; \
	fi
	@echo "✅ تم تشغيل الاختبارات!"

# Clean the system
clean:
	@echo "🧹 تنظيف النظام..."
	@rm -rf vendor/ node_modules/ cache/* logs/* uploads/*
	@if [ -f "docker-compose.yml" ]; then \
		docker-compose down -v --remove-orphans; \
		docker system prune -f; \
	fi
	@echo "✅ تم التنظيف!"

# Create backup
backup:
	@echo "💾 إنشاء نسخة احتياطية..."
	@mkdir -p backups
	@tar -czf "backups/backup-$(date +%Y%m%d-%H%M%S).tar.gz" \
		--exclude='./backups' \
		--exclude='./node_modules' \
		--exclude='./vendor' \
		--exclude='./.git' \
		.
	@echo "✅ تم إنشاء النسخة الاحتياطية!"

# Rollback to previous version
rollback:
	@echo "⏪ العودة لإصدار سابق..."
	@chmod +x deploy.sh
	@./deploy.sh rollback
	@echo "✅ تم العودة للإصدار السابق!"

# Show logs
logs:
	@echo "📋 عرض السجلات..."
	@if [ -f "docker-compose.yml" ]; then \
		docker-compose logs -f; \
	else \
		tail -f logs/*.log || echo "⚠️ لا توجد سجلات"; \
	fi

# Show system status
status:
	@echo "📊 حالة النظام..."
	@if [ -f "docker-compose.yml" ]; then \
		docker-compose ps; \
		echo ""; \
		echo "استخدام الموارد:"; \
		docker stats --no-stream; \
	else \
		echo "⚠️ النظام يعمل على خادم التطوير"; \
		ps aux | grep "php -S" | grep -v grep || echo "❌ النظام متوقف"; \
	fi

# Update the system
update:
	@echo "🔄 تحديث النظام..."
	@git pull origin main || echo "⚠️ لا يمكن تحديث النظام"
	@if [ -f "composer.json" ]; then \
		composer update; \
	fi
	@if [ -f "package.json" ]; then \
		npm update; \
	fi
	@echo "✅ تم التحديث!"

# Development commands
dev-install:
	@echo "🔧 تثبيت بيئة التطوير..."
	@composer install
	@npm install
	@echo "✅ تم تثبيت بيئة التطوير!"

dev-server:
	@echo "🚀 تشغيل خادم التطوير..."
	@php -S localhost:8000 -t . &
	@echo "✅ خادم التطوير يعمل على http://localhost:8000"

# Database commands
db-create:
	@echo "🗄️ إنشاء قاعدة البيانات..."
	@mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS ai_code_bot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
	@echo "✅ تم إنشاء قاعدة البيانات!"

db-migrate:
	@echo "🔄 تشغيل الترحيلات..."
	@mysql -u root -p ai_code_bot < database.sql
	@echo "✅ تم تشغيل الترحيلات!"

db-reset:
	@echo "🔄 إعادة تعيين قاعدة البيانات..."
	@mysql -u root -p -e "DROP DATABASE IF EXISTS ai_code_bot;"
	@make db-create
	@make db-migrate
	@echo "✅ تم إعادة تعيين قاعدة البيانات!"

# Security commands
security-check:
	@echo "🔒 فحص الأمان..."
	@composer audit || echo "⚠️ تم العثور على مشاكل أمنية"
	@npm audit || echo "⚠️ تم العثور على مشاكل أمنية"
	@echo "✅ تم فحص الأمان!"

# Performance commands
performance-test:
	@echo "⚡ اختبار الأداء..."
	@if command -v ab >/dev/null 2>&1; then \
		ab -n 1000 -c 10 http://localhost/; \
	else \
		echo "⚠️ أداة Apache Bench غير مثبتة"; \
	fi

# Monitoring commands
monitor:
	@echo "📊 مراقبة النظام..."
	@watch -n 1 'make status'

# Quick start for development
quick-start: dev-install db-create db-migrate dev-server
	@echo "🚀 AI Code Bot جاهز للتطوير!"
	@echo "🌐 افتح المتصفح على: http://localhost:8000"
	@echo "🔧 لوحة التحكم: http://localhost:8000/admin.php"

# Production setup
prod-setup: install deploy
	@echo "🚀 AI Code Bot جاهز للإنتاج!"
	@echo "🌐 افتح المتصفح على: http://localhost"
	@echo "🔧 لوحة التحكم: http://localhost/admin.php"

# Show system info
info:
	@echo "ℹ️ معلومات النظام:"
	@echo "  PHP Version: $(shell php -v | head -n1)"
	@echo "  Node Version: $(shell node -v 2>/dev/null || echo 'غير مثبت')"
	@echo "  Composer Version: $(shell composer -V | head -n1)"
	@echo "  Docker Version: $(shell docker -v 2>/dev/null || echo 'غير مثبت')"
	@echo "  Git Version: $(shell git --version 2>/dev/null || echo 'غير مثبت')"
	@echo "  OS: $(shell uname -s) $(shell uname -r)"
	@echo "  Architecture: $(shell uname -m)"

# Show help in Arabic
help-ar:
	@echo "AI Code Bot - أقوى بوت برمجة في العالم! 🚀"
	@echo ""
	@echo "الأوامر المتاحة:"
	@echo "  install    - تثبيت النظام"
	@echo "  start      - تشغيل النظام"
	@echo "  stop       - إيقاف النظام"
	@echo "  restart    - إعادة تشغيل النظام"
	@echo "  build      - بناء النظام"
	@echo "  deploy     - نشر النظام"
	@echo "  test       - تشغيل الاختبارات"
	@echo "  clean      - تنظيف النظام"
	@echo "  backup     - إنشاء نسخة احتياطية"
	@echo "  rollback   - العودة لإصدار سابق"
	@echo "  logs       - عرض السجلات"
	@echo "  status     - حالة النظام"
	@echo "  update     - تحديث النظام"
	@echo "  help       - عرض هذه المساعدة"
	@echo ""
	@echo "أمثلة:"
	@echo "  make quick-start    # بدء سريع للتطوير"
	@echo "  make prod-setup     # إعداد الإنتاج"
	@echo "  make dev-server     # خادم التطوير فقط"
	@echo "  make db-reset       # إعادة تعيين قاعدة البيانات" 