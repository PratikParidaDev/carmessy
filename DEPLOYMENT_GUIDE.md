# Git-Based Deployment Guide

This guide covers Git-based deployment for Laravel applications on shared hosting (Hostinger, etc.).

## 📋 Table of Contents

- [Quick Start](#quick-start)
- [Deployment Methods](#deployment-methods)
- [Configuration](#configuration)
- [Troubleshooting](#troubleshooting)

---

## 🚀 Quick Start

### Method 1: Manual Deployment Script

#### On Linux/Mac:

```bash
# Make script executable
chmod +x deploy.sh

# Deploy from main branch
./deploy.sh main

# Deploy from specific branch
./deploy.sh develop
```

#### On Windows:

```bash
# Run batch script
deploy.bat main
```

### Method 2: Git Pull (Simple)

```bash
# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📦 Deployment Methods

### 1. SSH Deployment (If Available)

If your hosting provider supports SSH:

```bash
# Connect to server
ssh user@yourdomain.com

# Navigate to project directory
cd /path/to/your/project

# Run deployment script
./deploy.sh main
```

### 2. Git Hooks (Automatic Deployment)

Set up automatic deployment when you push to Git:

#### On Your Server:

```bash
# 1. Create bare repository
mkdir -p /var/repos/car-marketplace.git
cd /var/repos/car-marketplace.git
git init --bare

# 2. Create post-receive hook
cat > hooks/post-receive << 'EOF'
#!/bin/bash
APP_PATH="/var/www/car-marketplace"
cd "$APP_PATH" || exit
git --git-dir="$APP_PATH/.git" --work-tree="$APP_PATH" checkout -f main
cd "$APP_PATH" && ./deploy.sh main
EOF

# 3. Make hook executable
chmod +x hooks/post-receive
```

#### On Your Local Machine:

```bash
# Add server as remote
git remote add production user@yourdomain.com:/var/repos/car-marketplace.git

# Deploy by pushing
git push production main
```

### 3. cPanel Git Version Control

If your hosting provider has Git in cPanel:

1. **Enable Git** in cPanel
2. **Clone Repository**:
   ```bash
   git clone https://github.com/yourusername/car-marketplace.git
   ```
3. **Set up deployment**:
   - Go to cPanel → Git Version Control
   - Create repository
   - Set deployment path
   - Enable auto-deployment

### 4. FTP + Git (Manual)

1. **Clone locally**:
   ```bash
   git clone https://github.com/yourusername/car-marketplace.git
   cd car-marketplace
   ```

2. **Build assets** (if needed):
   ```bash
   npm install
   npm run build
   ```

3. **Upload via FTP**:
   - Upload all files except:
     - `.git/`
     - `node_modules/`
     - `.env.example`
     - `tests/`

---

## ⚙️ Configuration

### Environment Variables

The deployment script uses these environment variables:

```bash
# Set branch to deploy
BRANCH=main

# Enable asset building (if Node.js available)
BUILD_ASSETS=true

# Application path (auto-detected)
APP_PATH=$(pwd)
```

### Pre-Deployment Checklist

- [ ] `.env` file configured with production settings
- [ ] Database credentials are correct
- [ ] `APP_ENV=production` in `.env`
- [ ] `APP_DEBUG=false` in `.env`
- [ ] Assets built (`public/build/` exists) OR `BUILD_ASSETS=true`
- [ ] File permissions set (storage: 775, bootstrap/cache: 775)

---

## 🔧 Script Features

### `deploy.sh` Features:

✅ **Git Integration**
- Pulls latest code from specified branch
- Handles merge conflicts gracefully

✅ **Dependency Management**
- Installs Composer dependencies
- Optional NPM asset building

✅ **Database Management**
- Automatic database backup before migration
- Runs migrations safely

✅ **Optimization**
- Clears all caches
- Rebuilds optimized caches
- Caches config, routes, views, events

✅ **Queue Management**
- Restarts queue workers
- Handles queue gracefully if not configured

✅ **Error Handling**
- Exits on critical errors
- Warns on non-critical issues
- Continues deployment when possible

---

## 📝 Usage Examples

### Basic Deployment

```bash
# Deploy from main branch
./deploy.sh main
```

### Deploy with Asset Building

```bash
# Set environment variable
export BUILD_ASSETS=true

# Deploy
./deploy.sh main
```

### Deploy Specific Branch

```bash
# Deploy from develop branch
./deploy.sh develop
```

### Windows Deployment

```bash
# Run batch script
deploy.bat main

# Or with PowerShell
.\deploy.bat develop
```

---

## 🛠️ Troubleshooting

### Git Pull Fails

**Problem**: `git pull` fails with conflicts

**Solution**:
```bash
# Check status
git status

# Resolve conflicts manually
git merge --abort  # Cancel merge
# Fix conflicts in files
git add .
git commit -m "Resolve conflicts"
```

### Composer Install Fails

**Problem**: Composer dependencies fail to install

**Solution**:
```bash
# Clear Composer cache
composer clear-cache

# Try again
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
```

### Migrations Fail

**Problem**: Database migrations fail

**Solution**:
```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check migration status
php artisan migrate:status

# Run migrations manually
php artisan migrate --force
```

### Assets Not Loading

**Problem**: CSS/JS files return 404

**Solution**:
```bash
# Ensure assets are built
npm run build

# Or check if public/build/ exists
ls -la public/build/

# Clear Laravel cache
php artisan cache:clear
php artisan view:clear
```

### Permission Errors

**Problem**: Permission denied errors

**Solution**:
```bash
# Set correct permissions
chmod -R 775 storage bootstrap/cache
chmod -R 755 public

# Set ownership (if needed)
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🔐 Security Best Practices

1. **Never commit `.env` file**
   ```bash
   # Ensure .env is in .gitignore
   echo ".env" >> .gitignore
   ```

2. **Use strong database passwords**
   - Generate secure passwords
   - Use different passwords for dev/production

3. **Set correct file permissions**
   ```bash
   # Storage and cache should be writable
   chmod -R 775 storage bootstrap/cache
   
   # Other files should be read-only
   find . -type f -exec chmod 644 {} \;
   find . -type d -exec chmod 755 {} \;
   ```

4. **Enable HTTPS**
   - Use SSL certificates
   - Force HTTPS in `.env`: `APP_URL=https://yourdomain.com`

5. **Keep dependencies updated**
   ```bash
   composer update
   # Review changes before deploying
   ```

---

## 📊 Deployment Workflow

```
┌─────────────────┐
│  Local Changes  │
└────────┬────────┘
         │
         │ git push
         ▼
┌─────────────────┐
│  Git Repository │
└────────┬────────┘
         │
         │ git pull
         ▼
┌─────────────────┐
│  Server Script   │
│   (deploy.sh)   │
└────────┬────────┘
         │
         ├─► Install Dependencies
         ├─► Build Assets (optional)
         ├─► Run Migrations
         ├─► Optimize Application
         └─► Restart Services
         │
         ▼
┌─────────────────┐
│  Live Site      │
└─────────────────┘
```

---

## 🎯 Quick Reference

### Common Commands

```bash
# Deploy
./deploy.sh main

# Check Git status
git status

# View recent commits
git log --oneline -10

# Rollback to previous commit
git reset --hard HEAD~1
./deploy.sh main

# Check Laravel version
php artisan --version

# Clear all caches
php artisan optimize:clear

# View logs
tail -f storage/logs/laravel.log
```

---

## 📞 Support

For issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check PHP error logs
3. Verify `.env` configuration
4. Test database connection
5. Check file permissions

---

**Last Updated**: 2025-01-XX  
**Laravel Version**: 12.x  
**PHP Version**: 8.4+

