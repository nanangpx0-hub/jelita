# 🔄 ENVIRONMENT SETUP GUIDE - SISE2026

## Quick Start - Choose Your Scenario

---

## 🏠 SCENARIO 1: LOCAL DEVELOPMENT SETUP

**You want to:** Develop and test features locally on your computer

### Step-by-Step (5 minutes)

```bash
# 1. Navigate to project
cd c:\laragon\www\se2026-jember

# 2. The .env file is already configured for local development!
# Just verify these settings in .env:
```

**✅ Verify .env contains:**
```env
APP_ENV=development
APP_URL=http://localhost/se2026-jember
DB_NAME=bps_jember_se2026
DB_USER=root
DB_PASS=<your_mysql_password>
```

```bash
# 3. Create database (one-time setup)
# Open phpMyAdmin: http://localhost/phpmyadmin
# Run SQL:
CREATE DATABASE bps_jember_se2026;

# 4. Import schema and data
# In phpMyAdmin, select bps_jember_se2026 database
# Import: sql/schema.sql
# Import: sql/seed_dummy_data.sql

# 5. Access application
# Open browser: http://localhost/se2026-jember
```

**✅ Test Checklist:**
- [ ] Login page loads
- [ ] Can login with test credentials
- [ ] Dashboard displays
- [ ] File upload works

**🎉 Done! You're ready to develop locally.**

---

## 🚀 SCENARIO 2: DEPLOY TO PRODUCTION

**You want to:** Deploy the application to Jagoan Hosting server

### Step-by-Step (15 minutes)

#### PREPARATION (Local Computer)

```bash
# 1. Copy production template
cp .env.production .env.production.temp

# 2. Edit .env.production.temp with correct values:
```

**📝 Edit these values in `.env.production.temp`:**
```env
APP_ENV=production
APP_URL=https://se2026.bpsjember.my.id
DB_NAME=bpsjembe_se2026
DB_USER=bpsjembe_dbuser
DB_PASS=<GENERATE_STRONG_PASSWORD>
```

```bash
# 3. Upload files to server
# Option A: Via Git (Recommended)
ssh bpsjembe@brave
cd /home/bpsjembe/public_html
git clone <repository-url> .

# Option B: Via cPanel File Manager
# Login to cPanel > File Manager > Upload all files

# Option C: Via FTP (FileZilla)
# Host: ftp.bpsjember.com or 103.163.138.166
# Upload all files to /public_html/
```

#### ON SERVER (Via SSH or cPanel Terminal)

```bash
# 4. Setup environment file
cd /home/bpsjembe/public_html
mv .env.production.temp .env

# 5. Set file permissions
chmod -R 755 uploads/
chown -R bpsjembe:bpsjembe uploads/
```

#### VIA CPANEL (Web Interface)

```
# 6. Create Production Database
Login to cPanel: https://bpsjember.com/cpanel

1. Go to: MySQL Databases
2. Create database: bpsjembe_se2026
3. Create user: bpsjembe_dbuser
   - Use strong password from .env file
4. Add user to database with ALL PRIVILEGES
```

```bash
# 7. Import Database (Via phpMyAdmin in cPanel)
1. Open phpMyAdmin from cPanel
2. Select database: bpsjembe_se2026
3. Import sql/schema.sql
4. Import sql/seed_dummy_data.sql
```

```bash
# 8. Verify SSL Certificate
# In cPanel: SSL/TLS Status
# Ensure se2026.bpsjember.my.id has active SSL
```

**✅ Test Production Deployment:**
- [ ] Access: https://se2026.bpsjember.my.id
- [ ] HTTPS works (no security warnings)
- [ ] Login page loads
- [ ] Can authenticate
- [ ] File upload works
- [ ] All features functional

**🎉 Done! Application is live on production server.**

---

## 🔄 SCENARIO 3: SWITCH BETWEEN ENVIRONMENTS

**You want to:** Work on both local and production environments

### Working Locally Then Deploying

```bash
# LOCAL WORK (Your Computer)
# 1. Code and test features locally
# 2. Everything works? Great!

# BEFORE DEPLOYING TO PRODUCTION:
# 1. Document changes
# 2. Note any database migrations needed
# 3. Backup production database first!

# DEPLOYMENT:
# 1. Upload changed files to production
# 2. Don't overwrite .env on server!
# 3. Run any database updates
# 4. Test thoroughly
```

### Key Rule: NEVER MIX ENVIRONMENTS

❌ **Don't:**
- Use production database on local
- Use local database credentials on server
- Commit .env file to Git
- Skip testing before deployment

✅ **Do:**
- Keep environments separate
- Test locally first
- Backup before deploying
- Use different database names

---

## 📊 ENVIRONMENT COMPARISON

| Feature | Local Dev | Production |
|---------|-----------|------------|
| **URL** | localhost | se2026.bpsjember.my.id |
| **Protocol** | HTTP | HTTPS 🔒 |
| **Database** | bps_jember_se2026 | bpsjembe_se2026 |
| **Location** | Your computer | Jagoan Hosting server |
| **Purpose** | Development/testing | Live users |
| **Data** | Dummy/test data | Real census data |

---

## 🛠️ TROUBLESHOOTING

### ❌ "Database connection failed"

**Local:**
```
✓ Check MySQL is running (Laragon tray icon)
✓ Verify DB_USER and DB_PASS in .env
✓ Try accessing phpMyAdmin
```

**Production:**
```
✓ Verify database exists in cPanel
✓ Check user has privileges
✓ Confirm password matches cPanel
```

### ❌ "Cannot upload files"

**Local:**
```
✓ Check uploads/ folder exists
✓ Windows: Usually no permission issues
```

**Production:**
```
✓ Set permissions: chmod 755 uploads/
✓ Check ownership: chown bpsjembe:bpsjembe uploads/
```

### ❌ "White screen / blank page"

**Check error logs:**
```bash
# Local
type c:\laragon\www\se2026-jember\error_log

# Production (SSH)
tail -f /home/bpsjembe/logs/error_log
```

### ❌ "Session issues / constant logout"

```
✓ Clear browser cookies
✓ Check APP_URL matches actual URL
✓ Verify SESSION_SECURE setting:
  - Local: false
  - Production: true
```

---

## 📁 FILE REFERENCE

### Environment Files

```
.env                  # Active configuration (create from template)
.env.example          # Local development template
.env.production       # Production template
.gitignore            # Excludes .env from Git (ALREADY CONFIGURED)
```

### Documentation Files

```
DEPLOYMENT_GUIDE.md           # Comprehensive guide
QUICK_DEPLOYMENT_REFERENCE.md # Quick reference card
CONFIGURATION_SUMMARY.md      # Technical summary
ENVIRONMENT_SETUP.md          # This file
HOSTING_CONFIGURATION_SUMMARY.md  # Server specs
```

---

## 🔐 SECURITY BEST PRACTICES

### ✅ DO:
- Use strong passwords in production
- Enable 2FA for cPanel
- Backup regularly
- Test locally before deploying
- Keep .env out of Git
- Use HTTPS in production
- Update documentation

### ❌ DON'T:
- Use root password in production
- Commit .env to Git
- Skip backups
- Deploy without testing
- Share credentials
- Ignore error logs
- Skip SSL certificate

---

## 📞 QUICK COMMANDS REFERENCE

### Local Development
```bash
cd c:\laragon\www\se2026-jember
# Access: http://localhost/se2026-jember
# phpMyAdmin: http://localhost/phpmyadmin
```

### Production (SSH)
```bash
ssh bpsjembe@brave
cd /home/bpsjembe/public_html
tail -f /home/bpsjembe/logs/error_log
chmod -R 755 uploads/
```

### Database Backup
```bash
# Local
mysqldump -u root -p bps_jember_se2026 > backup.sql

# Production
mysqldump -u bpsjembe_dbuser -p bpsjembe_se2026 > backup.sql
```

---

## 🎯 NEXT STEPS

### Just Starting?
→ Follow **Scenario 1** (Local Development)

### Ready to Deploy?
→ Follow **Scenario 2** (Production Deployment)

### Need Details?
→ Read **DEPLOYMENT_GUIDE.md** for comprehensive instructions

### Quick Reference?
→ Check **QUICK_DEPLOYMENT_REFERENCE.md**

### Server Specs?
→ See **HOSTING_CONFIGURATION_SUMMARY.md**

---

## ✅ SETUP VERIFICATION

### Local Development Ready When:
- ✅ .env file configured with local settings
- ✅ Database created and imported
- ✅ Application accessible at localhost
- ✅ Can login and use all features
- ✅ File upload working

### Production Ready When:
- ✅ .env file configured with production settings
- ✅ Database created via cPanel
- ✅ Files uploaded to server
- ✅ SSL certificate active
- ✅ Application accessible via HTTPS
- ✅ All features tested and working
- ✅ Backups configured

---

**Need Help?** Check the comprehensive guides or contact the development team.

**Status:** ✅ Environment configuration complete and ready for deployment!
