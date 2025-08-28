#!/bin/bash

# AI Code Bot Deployment Script
# سكريبت نشر بوت البرمجة الذكي

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="ai-code-bot"
PROJECT_VERSION="1.0.0"
DEPLOY_ENV="${1:-production}"
BACKUP_DIR="./backups"
LOG_FILE="./deploy.log"

# Log function
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}" | tee -a "$LOG_FILE"
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}" | tee -a "$LOG_FILE"
    exit 1
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   error "This script should not be run as root"
fi

# Check dependencies
check_dependencies() {
    log "Checking dependencies..."
    
    command -v git >/dev/null 2>&1 || error "git is required but not installed"
    command -v docker >/dev/null 2>&1 || error "docker is required but not installed"
    command -v docker-compose >/dev/null 2>&1 || error "docker-compose is required but not installed"
    command -v php >/dev/null 2>&1 || error "php is required but not installed"
    command -v composer >/dev/null 2>&1 || error "composer is required but not installed"
    
    log "All dependencies are satisfied"
}

# Create backup
create_backup() {
    log "Creating backup..."
    
    if [ ! -d "$BACKUP_DIR" ]; then
        mkdir -p "$BACKUP_DIR"
    fi
    
    BACKUP_FILE="$BACKUP_DIR/backup-$(date +'%Y%m%d-%H%M%S').tar.gz"
    
    tar -czf "$BACKUP_FILE" \
        --exclude='./backups' \
        --exclude='./node_modules' \
        --exclude='./vendor' \
        --exclude='./.git' \
        .
    
    log "Backup created: $BACKUP_FILE"
}

# Update code
update_code() {
    log "Updating code..."
    
    if [ -d ".git" ]; then
        git fetch origin
        git reset --hard origin/main
        log "Code updated from git"
    else
        warn "No git repository found, skipping code update"
    fi
}

# Install dependencies
install_dependencies() {
    log "Installing dependencies..."
    
    if [ -f "composer.json" ]; then
        composer install --no-dev --optimize-autoloader
        log "PHP dependencies installed"
    fi
    
    if [ -f "package.json" ]; then
        npm ci --only=production
        log "Node.js dependencies installed"
    fi
}

# Setup environment
setup_environment() {
    log "Setting up environment..."
    
    if [ ! -f ".env" ] && [ -f ".env.example" ]; then
        cp .env.example .env
        warn "Created .env file from .env.example - please configure it"
    fi
    
    # Create necessary directories
    mkdir -p uploads cache logs ssl
    
    # Set permissions
    chmod 755 uploads cache logs ssl
    chmod 644 .env
    
    log "Environment setup completed"
}

# Build Docker images
build_docker() {
    log "Building Docker images..."
    
    if [ -f "docker-compose.yml" ]; then
        docker-compose build --no-cache
        log "Docker images built successfully"
    else
        warn "No docker-compose.yml found, skipping Docker build"
    fi
}

# Start services
start_services() {
    log "Starting services..."
    
    if [ -f "docker-compose.yml" ]; then
        docker-compose up -d
        log "Services started successfully"
    else
        # Start PHP built-in server for development
        if [ "$DEPLOY_ENV" = "development" ]; then
            php -S localhost:8000 &
            log "PHP development server started on port 8000"
        fi
    fi
}

# Run database migrations
run_migrations() {
    log "Running database migrations..."
    
    if [ -f "database.sql" ]; then
        # Check if database exists
        if command -v mysql >/dev/null 2>&1; then
            mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS ai_code_bot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
            mysql -u root -p ai_code_bot < database.sql
            log "Database migrations completed"
        else
            warn "MySQL client not found, skipping database setup"
        fi
    fi
}

# Run tests
run_tests() {
    log "Running tests..."
    
    if [ -f "composer.json" ]; then
        composer test || warn "Some tests failed"
    fi
    
    if [ -f "package.json" ]; then
        npm test || warn "Some tests failed"
    fi
    
    log "Tests completed"
}

# Health check
health_check() {
    log "Performing health check..."
    
    # Wait for services to start
    sleep 10
    
    # Check if application is responding
    if command -v curl >/dev/null 2>&1; then
        if curl -f http://localhost/ >/dev/null 2>&1; then
            log "Application is responding"
        else
            warn "Application is not responding"
        fi
    fi
    
    log "Health check completed"
}

# Cleanup
cleanup() {
    log "Cleaning up..."
    
    # Remove old backups (keep last 5)
    if [ -d "$BACKUP_DIR" ]; then
        cd "$BACKUP_DIR"
        ls -t | tail -n +6 | xargs -r rm
        cd - > /dev/null
    fi
    
    # Clean Docker images
    if command -v docker >/dev/null 2>&1; then
        docker image prune -f
    fi
    
    log "Cleanup completed"
}

# Main deployment function
deploy() {
    log "Starting deployment of $PROJECT_NAME v$PROJECT_VERSION to $DEPLOY_ENV environment"
    
    check_dependencies
    create_backup
    update_code
    install_dependencies
    setup_environment
    build_docker
    start_services
    run_migrations
    run_tests
    health_check
    cleanup
    
    log "Deployment completed successfully! 🚀"
    log "Application should be available at: http://localhost"
    
    if [ -f "docker-compose.yml" ]; then
        log "Docker services status:"
        docker-compose ps
    fi
}

# Rollback function
rollback() {
    log "Rolling back to previous version..."
    
    if [ -d "$BACKUP_DIR" ]; then
        LATEST_BACKUP=$(ls -t "$BACKUP_DIR" | head -1)
        if [ -n "$LATEST_BACKUP" ]; then
            log "Restoring from backup: $LATEST_BACKUP"
            tar -xzf "$BACKUP_DIR/$LATEST_BACKUP"
            log "Rollback completed"
        else
            error "No backup found for rollback"
        fi
    else
        error "Backup directory not found"
    fi
}

# Show help
show_help() {
    echo "AI Code Bot Deployment Script"
    echo ""
    echo "Usage: $0 [OPTION]"
    echo ""
    echo "Options:"
    echo "  production   Deploy to production environment (default)"
    echo "  development  Deploy to development environment"
    echo "  rollback     Rollback to previous version"
    echo "  help         Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0                    # Deploy to production"
    echo "  $0 development        # Deploy to development"
    echo "  $0 rollback          # Rollback to previous version"
}

# Main script logic
case "${1:-production}" in
    "production"|"development")
        deploy
        ;;
    "rollback")
        rollback
        ;;
    "help"|"-h"|"--help")
        show_help
        ;;
    *)
        error "Invalid option: $1. Use 'help' for usage information."
        ;;
esac 