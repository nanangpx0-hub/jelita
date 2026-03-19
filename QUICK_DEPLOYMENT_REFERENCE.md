# 🚀 QUICK DEPLOYMENT REFERENCE - SISE2026

## Environment Comparison at a Glance

---

## LOCAL DEVELOPMENT 💻

### Configuration (.env)
```env
APP_ENV=development
APP_URL=http://localhost/se2026-jember
DB_HOST=localhost
DB_NAME=bps_jember_se2026
DB_USER=root
DB_PASS=<your_local_mysql_password>
SESSION_SECURE=false
MAX_UPLOAD_SIZE=5242880
```

### Access URLs
- **Application:** http://localhost/se2026-jember
- **phpMyAdmin:** http://localhost/phpmyadmin

### Quick Commands
```bash
cd c:\laragon\www\se2026-jember
cp .env.example .env
# Edit .env with local settings
# Start Laragon, access via browser
```

### Database Setup
```sql
CREATE DATABASE bps_jember_se2026;
-- Import: sql/schema.sql
-- Import: sql/seed_dummy_data.sql
```

---

## PRODUCTION HOSTING 🌐

### Configuration (.env)
```env
APP_ENV=production
APP_URL=https://se2026.bpsjember.my.id
DB_HOST=localhost
DB_NAME=bpsjembe_se2026
DB_USER=bpsjembe_dbuser
DB_PASS=<strong_unique_password>
SESSION_SECURE=true
MAX_UPLOAD_SIZE=5242880
```

### Access URLs
- **Application:** https://se2026.bpsjember.my.id
- **cPanel:** https://bpsjember.com/cpanel
- **File Manager:** cPanel > File Manager
- **phpMyAdmin:** cPanel > phpMyAdmin

### Server Details (Jagoan Hosting)
```
Server: brave
IP: 103.163.138.166
Username: bpsjembe
Home: /home/bpsjembe
Web Root: /home/bpsjembe/public_html
PHP Version: 8.2
Database: MariaDB 10.6.24
```

### Deployment Steps
1. Upload files to `/public_html/`
2. Copy `.env.production` to `.env`
3. Edit `.env` with production credentials
4. Import database via phpMyAdmin
5. Set permissions: `chmod -R 755 uploads/`
6. Verify SSL active
7. Test application

---

## KEY DIFFERENCES

| Aspect | Local | Production |
|--------|-------|------------|
| **URL** | localhost | se2026.bpsjember.my.id |
| **Protocol** | HTTP | HTTPS (forced) |
| **Database** | bps_jember_se2026 | bpsjembe_se2026 |
| **DB User** | root | bpsjembe_dbuser |
| **Session Secure** | false | true |
| **Error Display** | Yes (debug) | No (log only) |
| **File Upload** | 5MB default | 2GB max (hosting) |
| **Memory** | As per local | 256MB |
| **Execution Time** | As per local | 300s |

---

## COMMON TASKS

### Switch Environments
```bash
# LOCAL: Use .env.example as template
cp .env.example .env

# PRODUCTION: Use .env.production as template
cp .env.production .env
```

### Fix File Permissions
```bash
# Local (Windows)
# Usually no permission issues in Laragon

# Production (Linux via SSH)
chmod -R 755 uploads/
chown -R bpsjembe:bpsjembe uploads/
```

### View Error Logs
```bash
# Local
type c:\laragon\www\se2026-jember\error_log

# Production (SSH)
tail -f /home/bpsjembe/logs/error_log
```

### Backup Database
```bash
# Local (via phpMyAdmin or CLI)
mysqldump -u root -p bps_jember_se2026 > backup.sql

# Production (via SSH)
mysqldump -u bpsjembe_dbuser -p bpsjembe_se2026 > backup.sql
```

### Import Database
```bash
# Local
mysql -u root -p bps_jember_se2026 < backup.sql

# Production
mysql -u bpsjembe_dbuser -p bpsjembe_se2026 < backup.sql
```

---

## TROUBLESHOOTING QUICK FIXES

### ❌ Cannot Connect to Database
```
✓ Check DB credentials in .env
✓ Verify database exists
✓ Check user has privileges
✓ Ensure MySQL/MariaDB is running
```

### ❌ File Upload Fails
```
✓ Check uploads/ folder permissions (755)
✓ Verify MAX_UPLOAD_SIZE in .env
✓ Check PHP upload limits in cPanel
```

### ❌ 404 Errors
```
✓ Verify APP_URL matches actual URL
✓ Check .htaccess file exists
✓ Enable mod_rewrite (Apache)
```

### ❌ Session/Cookie Issues
```
✓ Clear browser cookies
✓ Check SESSION_SECURE setting
✓ Verify domain matches APP_URL
```

### ❌ White Screen
```
✓ Check error logs
✓ Verify PHP extensions enabled
✓ Check file permissions
✓ Review database connection
```

---

## SECURITY REMINDERS

### ✅ Before Production Launch
- [ ] Change all default passwords
- [ ] Remove test accounts
- [ ] Enable 2FA for cPanel
- [ ] Setup automated backups
- [ ] Verify SSL certificate active
- [ ] Configure proper file permissions
- [ ] Hide error messages (display_errors = Off)

### ⚠️ Never Do This
- ❌ Don't commit .env to Git
- ❌ Don't use root password in production
- ❌ Don't skip SSL/HTTPS
- ❌ Don't ignore error logs
- ❌ Don't upload without testing first

---

## SUPPORT CONTACTS

### Local Development
- Documentation: See DEPLOYMENT_GUIDE.md
- Team: Contact development team

### Production Hosting
- **Jagoan Hosting Support:** Via cPanel ticket
- **Server:** brave (103.163.138.166)
- **Emergency:** Check Jagoan Hosting website

---

## MONITORING CHECKLIST

### Daily (Production)
- [ ] Check application uptime
- [ ] Review error logs
- [ ] Monitor disk usage

### Weekly
- [ ] Verify backups completed
- [ ] Check resource usage (CPU/RAM)
- [ ] Review user activity logs

### Monthly
- [ ] Update documentation
- [ ] Security review
- [ ] Performance optimization
- [ ] Clean up old files/logs

---

**Quick Reference v1.0** | March 2026  
Keep this handy for quick deployment tasks!
