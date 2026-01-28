# Deployment Guide

This guide covers CI/CD deployment setup for the Laravel 12 Car Marketplace application.

**Framework**: Laravel 12.x  
**PHP Version**: 8.4+

## Table of Contents

- [Prerequisites](#prerequisites)
- [GitHub Actions Setup](#github-actions-setup)
- [GitLab CI Setup](#gitlab-ci-setup)
- [Manual Deployment](#manual-deployment)
- [Environment Configuration](#environment-configuration)
- [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Server Requirements

- **PHP**: 8.4 or higher (Laravel 12 requires PHP 8.4+)
- **Composer**: Latest version
- **Database**: MySQL 8.0+ / PostgreSQL / SQLite
- **Web Server**: Nginx / Apache
- **PHP Extensions**: mbstring, dom, curl, libxml, mysql, sqlite, pgsql, bcmath, gd, zip

> **Note**: This deployment setup does not include Node.js/NPM. Assets should be pre-built locally and committed to the repository before deployment. If you need to build assets on the server, you'll need to install Node.js separately.

### Required PHP Extensions

```bash
php -m | grep -E "mbstring|dom|curl|libxml|pdo_mysql|pdo_sqlite|pdo_pgsql|bcmath|gd|zip"
```

---

## GitHub Actions Setup

### 1. Create GitHub Secrets

Go to your repository → Settings → Secrets and variables → Actions, and add:

#### Required Secrets:

- `SSH_HOST`: Your server IP address or hostname
- `SSH_USERNAME`: SSH username (e.g., `deploy`, `ubuntu`)
- `SSH_PRIVATE_KEY`: Private SSH key for server access
- `SSH_PORT`: SSH port (default: 22)
- `DEPLOY_PATH`: Server deployment path (e.g., `/var/www/car-marketplace`)
- `DEPLOY_URL`: Production URL (e.g., `https://yourdomain.com`)

#### Optional Secrets:

- `DB_PASSWORD`: Database password (if needed for migrations)
- `APP_KEY`: Laravel application key

### 2. SSH Key Setup

```bash
# On your local machine
ssh-keygen -t rsa -b 4096 -C "github-actions" -f ~/.ssh/github_actions

# Copy public key to server
ssh-copy-id -i ~/.ssh/github_actions.pub user@your-server.com

# Add private key to GitHub Secrets
cat ~/.ssh/github_actions
# Copy output and paste into SSH_PRIVATE_KEY secret
```

### 3. Server Permissions

```bash
# On your server
sudo mkdir -p /var/www/car-marketplace
sudo chown -R $USER:www-data /var/www/car-marketplace
chmod -R 755 /var/www/car-marketplace
```

### 4. Workflow Triggers

The workflow triggers on:
- Push to `main`, `master`, or `production` branches
- Manual trigger via GitHub Actions UI
- Pull requests (tests only)

---

## GitLab CI Setup

### 1. Configure GitLab Variables

Go to your project → Settings → CI/CD → Variables, and add:

#### Required Variables:

- `SSH_HOST`: Server hostname or IP
- `SSH_USER`: SSH username
- `SSH_PRIVATE_KEY`: Private SSH key
- `DEPLOY_PATH_STAGING`: Staging deployment path
- `DEPLOY_PATH_PRODUCTION`: Production deployment path
- `STAGING_URL`: Staging environment URL
- `PRODUCTION_URL`: Production environment URL

### 2. SSH Key Setup

```bash
# Generate SSH key
ssh-keygen -t rsa -b 4096 -C "gitlab-ci" -f ~/.ssh/gitlab_ci

# Add public key to server
ssh-copy-id -i ~/.ssh/gitlab_ci.pub user@your-server.com

# Add private key to GitLab CI/CD variables
cat ~/.ssh/gitlab_ci
```

### 3. Pipeline Stages

- **test**: Runs PHPUnit tests and code style checks
- **build**: Builds production assets
- **deploy**: Deploys to staging/production (manual trigger)

---

## Manual Deployment

### Using the Deployment Script

```bash
# Make script executable
chmod +x deploy.sh

# Deploy to production
./deploy.sh production

# Deploy to staging
./deploy.sh staging
```

### Manual Steps

```bash
# 1. Pull latest code
git pull origin main

# 2. Install PHP dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 3. Run migrations
php artisan migrate --force

# 6. Clear and cache config
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Restart queue workers
php artisan queue:restart

# 8. Reload PHP-FPM
sudo systemctl reload php8.4-fpm
# or
sudo service php8.4-fpm reload
```

---

## Environment Configuration

### Production `.env` File

```env
APP_NAME="Car Marketplace"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=car_marketplace
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=null
```

### Generate Application Key

```bash
php artisan key:generate
```

### Storage Link

```bash
php artisan storage:link
```

---

## Server Configuration

### Nginx Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/car-marketplace/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/car-marketplace/public

    <Directory /var/www/car-marketplace/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### Queue Worker (Supervisor)

```ini
[program:car-marketplace-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/car-marketplace/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/car-marketplace/storage/logs/worker.log
stopwaitsecs=3600
```

---

## Troubleshooting

### Common Issues

#### 1. Permission Errors

```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

#### 2. Composer Memory Limit

```bash
# Increase PHP memory limit
php -d memory_limit=512M /usr/local/bin/composer install
```

#### 3. Asset Build Issues

> **Note**: Assets should be pre-built locally before deployment. If you need to build assets on the server, install Node.js and run `npm install && npm run build` manually.

#### 4. Migration Errors

```bash
# Check migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# Fresh migration (WARNING: Deletes all data)
php artisan migrate:fresh
```

#### 5. Cache Issues

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Debugging Deployment

```bash
# Check PHP version
php -v

# Check Composer
composer --version

# Check Laravel installation
php artisan --version

# Check environment
php artisan env

# View logs
tail -f storage/logs/laravel.log
```

---

## Post-Deployment Checklist

- [ ] Verify application is accessible
- [ ] Check database migrations ran successfully
- [ ] Verify assets are loading correctly
- [ ] Test authentication flow
- [ ] Check queue workers are running
- [ ] Verify file uploads work
- [ ] Check email sending (if configured)
- [ ] Monitor error logs
- [ ] Test critical user flows
- [ ] Verify SSL certificate (if using HTTPS)

---

## Rollback Procedure

### Quick Rollback

```bash
# If using deployment script
cd /var/www/car-marketplace/releases
# List releases
ls -lt
# Switch to previous release
ln -sfn /var/www/car-marketplace/releases/release-YYYYMMDD-HHMMSS /var/www/car-marketplace/current
sudo systemctl reload php8.4-fpm
```

### Database Rollback

```bash
# Rollback last migration
php artisan migrate:rollback

# Rollback specific number of migrations
php artisan migrate:rollback --step=3
```

---

## Security Best Practices

1. **Never commit `.env` file** to version control
2. **Use strong passwords** for database and services
3. **Enable HTTPS** with SSL certificates
4. **Keep dependencies updated**: `composer update` (assets should be pre-built locally)
5. **Set proper file permissions**: 755 for directories, 644 for files
6. **Use environment-specific configurations**
7. **Enable firewall** and restrict SSH access
8. **Regular backups** of database and files
9. **Monitor logs** for suspicious activity
10. **Keep PHP and server software updated**

---

## Support

For issues or questions:
- Check Laravel documentation: https://laravel.com/docs
- Review GitHub Issues
- Contact development team

---

**Last Updated**: 2025-01-XX  
**Laravel Version**: 12.x  
**PHP Version**: 8.4+

