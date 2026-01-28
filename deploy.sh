#!/bin/bash

###############################################################################
# Laravel Car Marketplace Deployment Script
# 
# This script automates the deployment process for Laravel applications
# Usage: ./deploy.sh [environment]
# Example: ./deploy.sh production
###############################################################################

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT=${1:-production}
APP_PATH="${DEPLOY_PATH:-/var/www/car-marketplace}"
BACKUP_PATH="${BACKUP_PATH:-/var/backups/car-marketplace}"
RELEASES_PATH="${APP_PATH}/releases"
CURRENT_PATH="${APP_PATH}/current"
SHARED_PATH="${APP_PATH}/shared"
RELEASE_NAME="release-$(date +%Y%m%d-%H%M%S)"
RELEASE_PATH="${RELEASES_PATH}/${RELEASE_NAME}"

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
    
    # Check PHP version
    PHP_VERSION=$(php -r "echo PHP_VERSION;" | cut -d. -f1,2)
    if [ "$(printf '%s\n' "8.4" "$PHP_VERSION" | sort -V | head -n1)" != "8.4" ]; then
        log_error "PHP 8.4 or higher is required. Found: $PHP_VERSION"
        exit 1
    fi
    
    # Check Composer
    if ! command -v composer &> /dev/null; then
        log_error "Composer is not installed"
        exit 1
    fi
    
    log_success "All requirements met"
}

create_directories() {
    log_info "Creating directory structure..."
    
    mkdir -p "${RELEASES_PATH}"
    mkdir -p "${SHARED_PATH}/storage/app/public"
    mkdir -p "${SHARED_PATH}/storage/framework/cache"
    mkdir -p "${SHARED_PATH}/storage/framework/sessions"
    mkdir -p "${SHARED_PATH}/storage/framework/views"
    mkdir -p "${SHARED_PATH}/storage/logs"
    mkdir -p "${BACKUP_PATH}"
    
    log_success "Directories created"
}

backup_database() {
    log_info "Backing up database..."
    
    if [ -f "${APP_PATH}/.env" ]; then
        DB_DATABASE=$(grep DB_DATABASE "${APP_PATH}/.env" | cut -d '=' -f2 | tr -d ' ')
        DB_USERNAME=$(grep DB_USERNAME "${APP_PATH}/.env" | cut -d '=' -f2 | tr -d ' ')
        DB_PASSWORD=$(grep DB_PASSWORD "${APP_PATH}/.env" | cut -d '=' -f2 | tr -d ' ')
        
        if [ ! -z "$DB_DATABASE" ]; then
            BACKUP_FILE="${BACKUP_PATH}/database-${RELEASE_NAME}.sql"
            mysqldump -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" > "${BACKUP_FILE}" 2>/dev/null || log_warning "Database backup failed"
            log_success "Database backed up to ${BACKUP_FILE}"
        fi
    fi
}

deploy_code() {
    log_info "Deploying code to ${RELEASE_PATH}..."
    
    # Create release directory
    mkdir -p "${RELEASE_PATH}"
    
    # Copy files (assuming you're running from project root)
    if [ -d ".git" ]; then
        log_info "Using Git to deploy..."
        git archive HEAD | tar -x -C "${RELEASE_PATH}"
    else
        log_info "Copying files..."
        rsync -av --exclude='.git' \
                  --exclude='node_modules' \
                  --exclude='vendor' \
                  --exclude='storage' \
                  --exclude='.env' \
                  --exclude='.env.backup' \
                  ./ "${RELEASE_PATH}/"
    fi
    
    log_success "Code deployed"
}

install_dependencies() {
    log_info "Installing dependencies..."
    
    cd "${RELEASE_PATH}"
    
    # Install PHP dependencies
    log_info "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
    
    log_success "Dependencies installed"
}

setup_environment() {
    log_info "Setting up environment..."
    
    cd "${RELEASE_PATH}"
    
    # Copy .env if it doesn't exist in release
    if [ ! -f "${RELEASE_PATH}/.env" ]; then
        if [ -f "${CURRENT_PATH}/.env" ]; then
            cp "${CURRENT_PATH}/.env" "${RELEASE_PATH}/.env"
        elif [ -f "${APP_PATH}/.env" ]; then
            cp "${APP_PATH}/.env" "${RELEASE_PATH}/.env"
        else
            log_warning ".env file not found. Please create one."
        fi
    fi
    
    # Link storage
    php artisan storage:link || true
    
    log_success "Environment setup complete"
}

run_migrations() {
    log_info "Running database migrations..."
    
    cd "${RELEASE_PATH}"
    
    php artisan migrate --force
    
    log_success "Migrations completed"
}

optimize_application() {
    log_info "Optimizing application..."
    
    cd "${RELEASE_PATH}"
    
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    
    log_success "Application optimized"
}

link_shared_resources() {
    log_info "Linking shared resources..."
    
    # Link storage
    rm -rf "${RELEASE_PATH}/storage"
    ln -sfn "${SHARED_PATH}/storage" "${RELEASE_PATH}/storage"
    
    log_success "Shared resources linked"
}

switch_release() {
    log_info "Switching to new release..."
    
    # Create symlink to new release
    ln -sfn "${RELEASE_PATH}" "${CURRENT_PATH}"
    
    # Restart services
    log_info "Restarting services..."
    
    # Restart queue workers
    php "${CURRENT_PATH}/artisan" queue:restart || true
    
    # Reload PHP-FPM
    sudo systemctl reload php8.4-fpm 2>/dev/null || \
    sudo systemctl reload php-fpm 2>/dev/null || \
    sudo service php8.4-fpm reload 2>/dev/null || \
    sudo service php-fpm reload 2>/dev/null || \
    log_warning "Could not reload PHP-FPM. Please restart manually."
    
    log_success "Switched to new release"
}

cleanup_old_releases() {
    log_info "Cleaning up old releases..."
    
    # Keep last 5 releases
    cd "${RELEASES_PATH}"
    ls -t | tail -n +6 | xargs rm -rf
    
    log_success "Old releases cleaned up"
}

main() {
    log_info "Starting deployment to ${ENVIRONMENT} environment..."
    
    check_requirements
    create_directories
    backup_database
    deploy_code
    install_dependencies
    setup_environment
    link_shared_resources
    run_migrations
    optimize_application
    switch_release
    cleanup_old_releases
    
    log_success "Deployment completed successfully!"
    log_info "Application is now live at: ${CURRENT_PATH}"
}

# Run main function
main

