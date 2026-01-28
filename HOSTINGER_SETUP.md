# Hostinger Shared Hosting Setup Guide
## Laravel 12 + PHP 8.4 + MySQL

Complete setup guide for deploying Laravel 12 on Hostinger shared hosting.

---

## 📋 Prerequisites

- ✅ Hostinger shared hosting account
- ✅ PHP 8.4+ enabled
- ✅ MySQL database created
- ✅ Git access (via cPanel or SSH)
- ✅ Domain/subdomain configured

---

## 🚀 Step-by-Step Setup

### Step 1: Create MySQL Database

1. **Login to Hostinger cPanel**
2. **Go to**: Databases → MySQL Databases
3. **Create Database**:
   - Database name: `car_marketplace` (or your choice)
   - Click "Create Database"
4. **Create User**:
   - Username: `car_user` (or your choice)
   - Password: Generate strong password
   - Click "Create User"
5. **Add User to Database**:
   - Select user and database
   - Click "Add"
   - Grant ALL PRIVILEGES
   - Click "Make Changes"

**Note down**:
- Database name: `username_car_marketplace`
- Database user: `username_car_user`
- Database password: `your_password`
- Database host: `localhost` (usually)

---

### Step 2: Enable PHP 8.4

1. **In cPanel**, go to: **Select PHP Version**
2. **Choose PHP 8.4** (or latest available)
3. **Enable Extensions**:
   - ✅ mbstring
   - ✅ pdo_mysql
   - ✅ curl
   - ✅ gd
   - ✅ zip
   - ✅ bcmath
   - ✅ xml
   - ✅ dom

---

### Step 3: Set Up Git

#### Option A: Using cPanel Git Version Control

1. **In cPanel**, go to: **Git Version Control**
2. **Click**: "Create"
3. **Repository URL**: `https://github.com/yourusername/car-marketplace.git`
4. **Repository Path**: `/home/username/public_html/car-marketplace`
5. **Branch**: `main`
6. **Click**: "Create"

#### Option B: Using SSH (if available)

```bash
# Connect via SSH
ssh username@yourdomain.com

# Navigate to public_html
cd ~/public_html

# Clone repository
git clone https://github.com/yourusername/car-marketplace.git

# Navigate to project
cd car-marketplace
```

---

### Step 4: Configure Environment

1. **Create `.env` file**:
   ```bash
   cp .env.example .env
   ```

2. **Edit `.env`** with your Hostinger settings:
   ```env
   APP_NAME="Car Marketplace"
   APP_ENV=production
   APP_KEY=base64:your-generated-key
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   LOG_CHANNEL=stack
   LOG_LEVEL=error

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=username_car_marketplace
   DB_USERNAME=username_car_user
   DB_PASSWORD=your_database_password

   BROADCAST_DRIVER=log
   CACHE_STORE=file
   FILESYSTEM_DISK=local
   QUEUE_CONNECTION=database
   SESSION_DRIVER=file
   SESSION_LIFETIME=120

   MAIL_MAILER=smtp
   MAIL_HOST=smtp.hostinger.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@yourdomain.com
   MAIL_PASSWORD=your-email-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@yourdomain.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

3. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

---

### Step 5: Set File Permissions

**Via cPanel File Manager**:

1. Navigate to your project folder
2. Right-click on `storage` folder → **Change Permissions**
3. Set to **775** (or 755 if 775 doesn't work)
4. **Apply to subdirectories**: ✅ Yes
5. Repeat for `bootstrap/cache` folder

**Or via SSH** (if available):
```bash
chmod -R 775 storage bootstrap/cache
chmod -R 755 public
```

---

### Step 6: Install Dependencies

**Via SSH** (recommended):
```bash
cd ~/public_html/car-marketplace
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
```

**Or via cPanel Terminal**:
- Go to: **Terminal** in cPanel
- Run the same command

---

### Step 7: Build Assets (Locally)

**⚠️ Important**: Hostinger shared hosting doesn't have Node.js, so build assets locally:

```bash
# On your local machine
npm install
npm run build

# Commit built assets
git add public/build/
git commit -m "Build assets for production"
git push origin main
```

Then pull on server:
```bash
git pull origin main
```

---

### Step 8: Run Migrations

```bash
php artisan migrate --force
```

---

### Step 9: Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

### Step 10: Configure Document Root

**Important**: Laravel's `public` folder should be your document root.

#### Option A: Move Files (Recommended)

1. Move contents of `public/` to `public_html/`
2. Move all other files one level up
3. Update `.env` paths if needed

#### Option B: Use .htaccess Redirect

Create `.htaccess` in `public_html/`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ car-marketplace/public/$1 [L]
</IfModule>
```

---

## 🔄 Deployment Workflow

### Using the Deployment Script

1. **Make script executable** (via SSH):
   ```bash
   chmod +x deploy.sh
   ```

2. **Run deployment**:
   ```bash
   ./deploy.sh main
   ```

### Manual Deployment

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 🛠️ Troubleshooting

### Issue: 500 Internal Server Error

**Solutions**:
1. Check `storage/logs/laravel.log`
2. Verify `.env` file exists and is configured
3. Check file permissions (storage: 775)
4. Verify PHP version is 8.4+

### Issue: Database Connection Failed

**Solutions**:
1. Verify database credentials in `.env`
2. Check database host (usually `localhost`)
3. Ensure database user has privileges
4. Test connection:
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

### Issue: Assets Not Loading (404)

**Solutions**:
1. Ensure `public/build/` exists with files
2. Build assets locally and commit:
   ```bash
   npm run build
   git add public/build/
   git commit -m "Build assets"
   git push
   ```
3. Check `.htaccess` in `public/` folder
4. Clear cache: `php artisan cache:clear`

### Issue: Permission Denied

**Solutions**:
1. Set permissions via cPanel File Manager:
   - `storage/` → 775
   - `bootstrap/cache/` → 775
2. Or via SSH:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

### Issue: Composer Not Found

**Solutions**:
1. Use full path: `/usr/local/bin/composer`
2. Or install Composer:
   ```bash
   curl -sS https://getcomposer.org/installer | php
   mv composer.phar /usr/local/bin/composer
   ```

---

## 📁 File Structure on Hostinger

```
public_html/
├── car-marketplace/          ← Your Laravel project
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/               ← Document root
│   │   ├── index.php
│   │   ├── .htaccess
│   │   └── build/            ← Built assets (from local build)
│   ├── resources/
│   ├── routes/
│   ├── storage/              ← Must be writable (775)
│   ├── vendor/
│   ├── .env                  ← Your configuration
│   └── artisan
```

---

## 🔐 Security Checklist

- [ ] `.env` file is not in public directory
- [ ] `APP_DEBUG=false` in production
- [ ] Strong database passwords
- [ ] File permissions set correctly
- [ ] `.htaccess` configured properly
- [ ] HTTPS enabled (SSL certificate)
- [ ] Regular backups configured

---

## 📞 Hostinger-Specific Notes

1. **PHP Version**: Select PHP 8.4 in cPanel → Select PHP Version
2. **MySQL**: Usually `localhost` as host
3. **File Upload Limit**: Check PHP settings (usually 64MB)
4. **Memory Limit**: Increase if needed (256MB recommended)
5. **Execution Time**: Increase for migrations (300 seconds)

---

## 🎯 Quick Reference

### Essential Commands

```bash
# Deploy
./deploy.sh main

# Check Laravel version
php artisan --version

# View logs
tail -f storage/logs/laravel.log

# Clear all caches
php artisan optimize:clear

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

---

**Last Updated**: 2025-01-XX  
**Laravel Version**: 12.x  
**PHP Version**: 8.4+  
**Hosting**: Hostinger Shared Hosting

