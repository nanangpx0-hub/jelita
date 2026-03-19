# 📋 DEPLOYMENT GUIDE - SISE2026 BPS Kabupaten Jember
## Multi-Environment Configuration Guide

**Version:** 1.0  
**Last Updated:** March 19, 2026  
**Application:** SE2026 Census System

---

## 🎯 OVERVIEW

This application supports **two distinct environments**:

1. **Local Development** (`development`)
   - URL: `http://localhost/se2026-jember`
   - Database: `bps_jember_se2026`
   - Used for: Local testing and development

2. **Production Hosting** (`production`)
   - URL: `https://se2026.bpsjember.my.id/`
   - Database: `bpsjembe_se2026`
   - Used for: Live production deployment on Jagoan Hosting

---

## 📁 ENVIRONMENT FILES STRUCTURE

```
se2026-jember/
├── .env.example          # Template for local development
├── .env.production       # Template for production (copy to .env on server)
├── .env                  # Active environment file (DO NOT COMMIT TO GIT)
└── config/
    └── config.php        # Main configuration (auto-detects environment)
```

---

## 🔧 LOCAL DEVELOPMENT SETUP

### Step 1: Clone Repository
```bash
cd c:\laragon\www
git clone <repository-url> se2026-jember
cd se2026-jember
```

### Step 2: Create Environment File
```bash
# Copy the example file
cp .env.example .env
```

### Step 3: Configure .env for Local Development

Edit `.env` file with your local settings:

```env
# Application Environment
APP_ENV=development
APP_URL=http://localhost/se2026-jember

# Database (Local - Laragon/MySQL)
DB_HOST=localhost
DB_PORT=3306
DB_NAME=bps_jember_se2026
DB_USER=root
DB_PASS=your_mysql_password

# Session Settings (Local - HTTP)
SESSION_LIFETIME=7200
SESSION_SECURE=false

# File Upload Settings
MAX_UPLOAD_SIZE=5242880  # 5MB in bytes
```

### Step 4: Create Database

**Option A: Automatic (via config.php)**
- The application will auto-create the database if it doesn't exist

**Option B: Manual**
```sql
CREATE DATABASE bps_jember_se2026;
```

### Step 5: Import Database Schema
```bash
# Using phpMyAdmin or MySQL CLI
mysql -u root -p bps_jember_se2026 < sql/schema.sql
mysql -u root -p bps_jember_se2026 < sql/seed_dummy_data.sql
```

### Step 6: Set Directory Permissions
```bash
# Ensure uploads directory is writable
chmod -R 755 uploads/
```

### Step 7: Install Dependencies (if using Composer)
```bash
composer install --no-dev --optimize-autoloader
```

### Step 8: Access Application
Open browser and navigate to:
```
http://localhost/se2026-jember
```

### Step 9: Verify Installation
✅ Check these features:
- [ ] Login page loads correctly
- [ ] Can authenticate with test credentials
- [ ] Dashboard displays properly
- [ ] File upload works (test with small file)
- [ ] Database queries execute without errors

---

## 🚀 PRODUCTION DEPLOYMENT (Jagoan Hosting)

### Prerequisites
- ✅ Jagoan Hosting cPanel account active
- ✅ Database created: `bpsjembe_se2026`
- ✅ Database user created with full privileges
- ✅ FTP/SFTP access configured
- ✅ Domain pointed to hosting: `se2026.bpsjember.my.id`

### Step 1: Prepare Production Environment File

On your local machine, copy production template:
```bash
cp .env.production .env.production.temp
```

Edit `.env.production.temp` with production values:

```env
# Application Environment
APP_ENV=production
APP_URL=https://se2026.bpsjember.my.id

# Database (Production - Jagoan Hosting)
DB_HOST=localhost
DB_PORT=3306
DB_NAME=bpsjembe_se2026
DB_USER=bpsjembe_dbuser
DB_PASS=YOUR_STRONG_PASSWORD_HERE

# Session Settings (Production - HTTPS)
SESSION_LIFETIME=7200
SESSION_SECURE=true

# File Upload Settings
MAX_UPLOAD_SIZE=5242880  # 5MB in bytes
```

### Step 2: Upload Files to Server

**Option A: Via cPanel File Manager**
1. Login to cPanel: `https://bpsjember.com/cpanel`
2. Navigate to **File Manager**
3. Go to `/public_html/`
4. Upload all project files
5. Rename `.env.production.temp` to `.env`

**Option B: Via FTP (FileZilla/WinSCP)**
```
Host: ftp.bpsjember.com or 103.163.138.166
Username: bpsjembe
Password: your_ftp_password
Port: 21 (FTP) or 22 (SFTP)
Remote Path: /public_html/
```

**Option C: Via Git (Recommended)**
```bash
# On production server via SSH/Terminal
cd /home/bpsjembe/public_html
git clone <repository-url> .
cp .env.production .env
```

### Step 3: Import Production Database

**Via phpMyAdmin:**
1. Login to phpMyAdmin from cPanel
2. Select database: `bpsjembe_se2026`
3. Click **Import** tab
4. Choose file: `sql/schema.sql`
5. Click **Go**
6. Repeat for `sql/seed_dummy_data.sql`

**Via SSH/Terminal:**
```bash
# Login via SSH
ssh bpsjembe@brave

# Import schema
mysql -u bpsjembe_dbuser -p bpsjembe_se2026 < /home/bpsjembe/public_html/sql/schema.sql

# Import seed data
mysql -u bpsjembe_dbuser -p bpsjembe_se2026 < /home/bpsjembe/public_html/sql/seed_dummy_data.sql
```

### Step 4: Set File Permissions (Critical!)

**Via cPanel File Manager:**
1. Right-click `uploads/` folder
2. Select **Change Permissions**
3. Set to: **755** (or **775** if 755 doesn't work)
4. Check "Apply to files and folders recursively"

**Via SSH:**
```bash
cd /home/bpsjembe/public_html
chmod -R 755 uploads/
chown -R bpsjembe:bpsjembe uploads/
```

### Step 5: Configure PHP Settings

From cPanel > **PHP Selector**:
- ✅ PHP Version: **8.2** (already set)
- ✅ Extensions: Verify all required extensions are checked
- ✅ Options: Review limits (already configured per hosting documentation)

### Step 6: Enable SSL Certificate

From cPanel > **SSL/TLS**:
1. Verify SSL certificate is active for `se2026.bpsjember.my.id`
2. Force HTTPS redirect (add to `.htaccess`):

```apache
# Add to .htaccess in public_html
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Step 7: Test Production Deployment

Access: `https://se2026.bpsjember.my.id`

**Test Checklist:**
- [ ] HTTPS works (no security warnings)
- [ ] Login page loads
- [ ] Can authenticate with credentials
- [ ] Dashboard displays
- [ ] File upload works (test with photo/document)
- [ ] File download works
- [ ] All CRUD operations functional
- [ ] No PHP errors in logs

### Step 8: Setup Automated Backups

**Via JetBackup5 (cPanel):**
1. Navigate to **JetBackup5** in cPanel
2. Enable daily backups
3. Schedule: Daily at 2:00 AM
4. Retention: Keep last 7 days
5. Include: Full home directory + databases

---

## 🔄 SWITCHING BETWEEN ENVIRONMENTS

### For Developers Working on Both Environments

**Local Development:**
```bash
# Ensure .env contains local settings
APP_ENV=development
APP_URL=http://localhost/se2026-jember
DB_NAME=bps_jember_se2026
```

**Before Deploying to Production:**
```bash
# On production server, ensure .env contains production settings
APP_ENV=production
APP_URL=https://se2026.bpsjember.my.id
DB_NAME=bpsjembe_se2026
```

### Best Practices
1. ✅ **Never commit `.env` to Git** (already in `.gitignore`)
2. ✅ Keep `.env.example` and `.env.production` templates updated
3. ✅ Use different database names to avoid accidental cross-environment data mixing
4. ✅ Test all changes locally before deploying to production
5. ✅ Backup production database before major updates

---

## 🛠️ TROUBLESHOOTING

### Issue: Database Connection Failed

**Local:**
```
Error: SQLSTATE[HY000] [1045] Access denied
```
**Solution:**
- Check `.env` DB_USER and DB_PASS values
- Verify MySQL is running (check Laragon tray icon)
- Try accessing phpMyAdmin: `http://localhost/phpmyadmin`

**Production:**
```
Error: SQLSTATE[HY000] [1045] Access denied
```
**Solution:**
- Verify database user exists in cPanel > MySQL Databases
- Check user has privileges for `bpsjembe_se2026` database
- Confirm password in `.env` matches cPanel database password

---

### Issue: File Upload Fails

**Error: "Failed to save uploaded file"**

**Solution:**
1. Check `uploads/` folder permissions:
   ```bash
   chmod -R 755 uploads/
   ```
2. Verify `MAX_FILE_SIZE` in `.env` doesn't exceed hosting limits
3. Check PHP `upload_max_filesize` in cPanel > PHP Options

---

### Issue: URLs Not Working (404 Errors)

**Local:**
- Ensure APP_URL in `.env` matches actual URL
- Check Apache mod_rewrite is enabled

**Production:**
- Verify APP_URL is exactly: `https://se2026.bpsjember.my.id`
- Check `.htaccess` file was uploaded correctly
- Ensure SSL certificate is active

---

### Issue: Session/Cookie Problems

**Symptoms:** Cannot stay logged in, constant redirects to login

**Solution:**
1. Clear browser cookies
2. Check SESSION_SECURE setting:
   - Local: `SESSION_SECURE=false`
   - Production: `SESSION_SECURE=true`
3. Verify domain name matches APP_URL exactly

---

### Issue: White Screen/Blank Page

**Solution:**
1. Check error logs:
   - Local: `error_log` in project root
   - Production: cPanel > **Errors** or `/home/bpsjembe/logs/error_log`
2. Enable error display temporarily (local only!):
   ```php
   // In config.php, add temporarily
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
3. Common causes:
   - Missing PHP extensions
   - Database connection failed
   - File permission issues

---

## 📊 ENVIRONMENT COMPARISON TABLE

| Setting | Local Development | Production Hosting |
|---------|------------------|-------------------|
| **APP_ENV** | `development` | `production` |
| **APP_URL** | `http://localhost/se2026-jember` | `https://se2026.bpsjember.my.id` |
| **Protocol** | HTTP | HTTPS (forced) |
| **DB_HOST** | `localhost` | `localhost` |
| **DB_NAME** | `bps_jember_se2026` | `bpsjembe_se2026` |
| **DB_USER** | `root` | `bpsjembe_dbuser` |
| **DB_PASS** | Your local MySQL password | Strong unique password |
| **SESSION_SECURE** | `false` | `true` |
| **Error Display** | Show errors (for debugging) | Hide errors (log only) |
| **Debug Level** | High | Minimal |
| **File Upload Limit** | 5MB (configurable) | 2GB (hosting limit) |
| **Memory Limit** | As per local php.ini | 256MB |
| **Max Execution** | As per local php.ini | 300 seconds |

---

## 🔐 SECURITY CHECKLIST FOR PRODUCTION

### Before Going Live:

- [ ] Change all default passwords
- [ ] Remove/disable test user accounts
- [ ] Enable Two-Factor Authentication for cPanel
- [ ] Set up regular automated backups
- [ ] Configure ModSecurity rules
- [ ] Enable SSL certificate (AutoSSL)
- [ ] Set proper file permissions (755 directories, 644 files)
- [ ] Restrict access to sensitive directories via `.htaccess`
- [ ] Disable directory browsing
- [ ] Set up IP whitelisting for admin areas (optional)
- [ ] Configure email notifications for critical actions
- [ ] Review and update CORS policies
- [ ] Enable HTTP security headers (HSTS, X-Frame-Options, etc.)

### `.htaccess` Security Additions:

```apache
# Prevent directory browsing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.env|^\.git|composer\.(json|lock)">
    Order allow,deny
    Deny from all
</FilesMatch>

# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security Headers
Header always set Strict-Transport-Security "max-age=31536000"
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
```

---

## 📞 SUPPORT & CONTACTS

### Technical Support

**Local Development Issues:**
- Check Laravel/PHP documentation
- Review application logs
- Contact development team

**Production Hosting Issues:**
- **Jagoan Hosting Support:** Available via cPanel ticket system
- **Server Admin:** Contact through cPanel > Support
- **Emergency:** Check Jagoan Hosting phone support

### Useful Links

- **cPanel Login:** `https://bpsjember.com/cpanel`
- **phpMyAdmin:** Available in cPanel
- **File Manager:** Available in cPanel
- **Jagoan Hosting Support:** `https://www.jagoanhosting.com/support`

---

## 📝 DEPLOYMENT CHECKLIST

### Pre-Deployment (Local)
- [ ] Code reviewed and tested locally
- [ ] All features working in local environment
- [ ] Database migrations/seeds ready
- [ ] `.env` file configured for production template
- [ ] Backup of current production data (if updating)

### Deployment Day
- [ ] Files uploaded to server
- [ ] `.env` configured with production values
- [ ] Database imported
- [ ] File permissions set correctly
- [ ] SSL certificate active
- [ ] Basic functionality tested
- [ ] Critical paths tested (login, upload, download, CRUD)

### Post-Deployment
- [ ] Monitor error logs for 24 hours
- [ ] Verify backups are running
- [ ] Check resource usage (CPU, RAM, storage)
- [ ] User acceptance testing completed
- [ ] Documentation updated
- [ ] Team notified of deployment success

---

## 🎯 QUICK REFERENCE COMMANDS

### Local Development
```bash
# Start Laragon
# (Click Laragon tray icon > Start All)

# Access via browser
http://localhost/se2026-jember

# View local error log
tail -f c:/laragon/www/se2026-jember/error_log
```

### Production (SSH)
```bash
# Connect via SSH
ssh bpsjembe@brave

# Navigate to web root
cd /home/bpsjembe/public_html

# View error log
tail -f /home/bpsjembe/logs/error_log

# Check disk usage
df -h

# Check file permissions
ls -la uploads/

# Restart PHP (if needed)
# Contact Jagoan Hosting support
```

---

**Document End**  
For questions or updates to this guide, contact the development team.
