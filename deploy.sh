#!/bin/bash

###############################################################################
# Laravel 12 Git Deployment Script for Hostinger Shared Hosting
# 
# Requirements:
# - Laravel 12.x
# - PHP 8.4+
# - MySQL Database
# - Git access (via cPanel or SSH)
# 
# Usage: ./deploy.sh [branch]
# Example: ./deploy.sh main
###############################################################################

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
BRANCH=${1:-main}
APP_PATH="$(pwd)"
BUILD_ASSETS=${BUILD_ASSETS:-false}  # Set to true if Node.js is available (usually false on shared hosting)

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

check_requirements() {
    log_info "Checking requirements..."
    
    # Check if we're in a Git repository
    if [ ! -d ".git" ]; then
        log_error "Not a Git repository. Please run this script from your project root."
        exit 1
    fi
    
    # Check PHP version
    if ! command -v php &> /dev/null; then
        log_error "PHP is not installed or not in PATH"
        exit 1
    fi
    
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    PHP_MAJOR=$(echo "$PHP_VERSION" | cut -d. -f1)
    PHP_MINOR=$(echo "$PHP_VERSION" | cut -d. -f2)
    
    if [ "$PHP_MAJOR" -lt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 4 ]); then
        log_error "PHP 8.4+ required for Laravel 12. Found: $PHP_VERSION"
        exit 1
    fi
    
    log_info "PHP Version: $PHP_VERSION ✓"
    
    # Check Composer
    if ! command -v composer &> /dev/null; then
        log_error "Composer is not installed"
        exit 1
    fi
    
    log_success "All requirements met"
}

backup_database() {
    log_info "Backing up MySQL database..."
    
    if [ -f ".env" ]; then
        DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f2 | tr -d ' ' | head -n1)
        DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2 | tr -d ' ' | head -n1)
        DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2 | tr -d ' ' | head -n1)
        DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2 | tr -d ' ' | head -n1)
        DB_HOST=$(grep "^DB_HOST=" .env | cut -d '=' -f2 | tr -d ' ' | head -n1)
        DB_HOST=${DB_HOST:-localhost}
        
        if [ "$DB_CONNECTION" = "mysql" ] && [ ! -z "$DB_DATABASE" ] && [ ! -z "$DB_USERNAME" ]; then
            BACKUP_DIR="database_backups"
            mkdir -p "$BACKUP_DIR"
            BACKUP_FILE="${BACKUP_DIR}/backup_$(date +%Y%m%d_%H%M%S).sql"
            
            if command -v mysqldump &> /dev/null; then
                mysqldump -h"${DB_HOST}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" > "${BACKUP_FILE}" 2>/dev/null && \
                    log_success "Database backed up to ${BACKUP_FILE}" || \
                    log_warning "Database backup failed (check credentials in .env)"
            else
                log_warning "mysqldump not found. Skipping database backup."
            fi
        else
            log_info "Database backup skipped (not MySQL or not configured)"
        fi
    fi
}

pull_latest_code() {
    log_info "Pulling latest code from ${BRANCH} branch..."
    
    # Fetch latest changes
    git fetch origin
    
    # Check if branch exists
    if ! git rev-parse --verify "origin/${BRANCH}" > /dev/null 2>&1; then
        log_error "Branch '${BRANCH}' not found on remote"
        exit 1
    fi
    
    # Pull latest changes
    git pull origin "${BRANCH}" || {
        log_error "Git pull failed. Please resolve conflicts manually."
        exit 1
    }
    
    log_success "Code updated successfully"
}

install_dependencies() {
    log_info "Installing PHP dependencies..."
    
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
    
    log_success "Dependencies installed"
}

build_assets() {
    log_info "Checking assets..."
    
    # On Hostinger shared hosting, assets should be pre-built
    if [ "$BUILD_ASSETS" = "true" ]; then
        log_info "Attempting to build assets..."
        
        if command -v npm &> /dev/null && command -v node &> /dev/null; then
            npm ci
            npm run build
            log_success "Assets built successfully"
        else
            log_warning "Node.js/NPM not available (common on shared hosting)"
            log_warning "Assets should be pre-built and committed to repository"
        fi
    else
        log_info "Skipping asset build (Hostinger shared hosting - build locally)"
        
        # Check if built assets exist
        if [ -d "public/build" ] && [ "$(ls -A public/build 2>/dev/null)" ]; then
            log_success "Pre-built assets found in public/build/"
        else
            log_warning "⚠️  WARNING: public/build/ directory is empty or missing!"
            log_warning "Build assets locally with: npm install && npm run build"
            log_warning "Then commit public/build/ to your repository"
        fi
    fi
}

run_migrations() {
    log_info "Running database migrations..."
    
    php artisan migrate --force || {
        log_warning "Migrations failed. Please check your database configuration."
    }
    
    log_success "Migrations completed"
}

optimize_application() {
    log_info "Optimizing application..."
    
    # Clear caches
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
    php artisan cache:clear || true
    
    # Cache for production
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan event:cache || true
    
    log_success "Application optimized"
}

set_permissions() {
    log_info "Setting file permissions for Hostinger..."
    
    # Hostinger shared hosting permissions
    # Storage and cache need to be writable by web server
    chmod -R 775 storage bootstrap/cache 2>/dev/null || {
        log_warning "Could not set permissions (may need to set via cPanel File Manager)"
        log_info "Required permissions:"
        log_info "  storage/ → 775"
        log_info "  bootstrap/cache/ → 775"
        log_info "  public/ → 755"
    }
    
    chmod -R 755 public 2>/dev/null || true
    
    # Ensure .htaccess exists
    if [ ! -f "public/.htaccess" ]; then
        log_warning "public/.htaccess not found. Laravel may not work correctly."
    fi
    
    log_success "Permissions configured"
}

restart_queue() {
    log_info "Restarting queue workers..."
    
    php artisan queue:restart || log_warning "Queue restart failed (this is okay if queue is not configured)"
    
    log_success "Queue workers restarted"
}

main() {
    log_info "========================================="
    log_info "Laravel 12 Deployment Script"
    log_info "Hostinger Shared Hosting"
    log_info "========================================="
    log_info "Branch: ${BRANCH}"
    log_info "Path: ${APP_PATH}"
    log_info "PHP: $(php -r 'echo PHP_VERSION;')"
    log_info "========================================="
    echo ""
    
    check_requirements
    backup_database
    pull_latest_code
    install_dependencies
    build_assets
    set_permissions
    run_migrations
    optimize_application
    restart_queue
    
    echo ""
    log_success "========================================="
    log_success "Deployment completed successfully!"
    log_success "========================================="
    log_info "Your Laravel 12 application is now updated."
    log_info ""
    log_info "Next steps:"
    log_info "  1. Verify your site is working"
    log_info "  2. Check storage/logs/laravel.log for any errors"
    log_info "  3. Test database connections"
    echo ""
}

# Run main function
main

