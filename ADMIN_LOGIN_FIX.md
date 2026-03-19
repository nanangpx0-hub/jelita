# 🔐 ADMIN LOGIN TROUBLESHOOTING GUIDE
## SISE2026 BPS Kabupaten Jember

**Issue:** Cannot login with username `admin` and password `password`

---

## 🎯 ROOT CAUSE IDENTIFIED

The issue is caused by **conflicting password hashes** in the seed files:

### The Problem:

1. **`sql/schema.sql` (line 438-439)** creates admin with password hash for **"password"**
   ```sql
   INSERT INTO users (username, password_hash, ...)
   VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', ...);
   ```

2. **`sql/seed_dummy_data.sql` (line 98)** **OVERWRITES** admin with password hash for **"DemoSE2026!"**
   ```sql
   INSERT INTO users (...)
   VALUES (1, ..., 'admin', '$2y$10$s7IZyQCj25/V0rw8MM.6uOngY4eISUL2JqLjsbq5C19O3JoxN5.Mi', ...);
   ```

**Result:** After importing both files, admin password is **"DemoSE2026!"** not **"password"**

---

## ✅ SOLUTION - Choose One Method

### METHOD 1: Run Fix Script (RECOMMENDED - Easiest)

```bash
# Access via browser:
http://localhost/se2026-jember/fix_admin_password.php
```

This will automatically reset the admin password to "password".

**Steps:**
1. Open browser
2. Go to: `http://localhost/se2026-jember/fix_admin_password.php`
3. Wait for success message
4. Click "Go to Login Page"
5. Login with:
   - Username: `admin`
   - Password: `password`

---

### METHOD 2: Manual SQL Update

Run this SQL query in phpMyAdmin:

```sql
UPDATE users 
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';
```

**Steps:**
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database: `bps_jember_se2026`
3. Click SQL tab
4. Paste the query above
5. Click "Go"
6. Login with admin/password

---

### METHOD 3: Re-import Seed Data Correctly

If you want to use the original passwords from documentation:

**Option A: Use "password" for admin**
```sql
-- First import schema
source sql/schema.sql;

-- Then IMMEDIATELY fix admin password before seed overwrites it
UPDATE users SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';

-- Now import rest of seed data (excluding admin update)
-- Edit seed_dummy_data.sql and remove admin from line 98
```

**Option B: Use "DemoSE2026!" for all demo accounts**
```bash
# Just import as-is and use these credentials:
Username: admin
Password: DemoSE2026!
```

---

## 🧪 DIAGNOSE THE ISSUE

### Step 1: Check Current Admin User

Run diagnostic script:
```bash
# Access via browser:
http://localhost/se2026-jember/check_admin_user.php
```

This will show:
- ✓ If admin user exists
- ✓ Current password hash
- ✓ Which passwords match the hash
- ✓ All users in database

### Step 2: Manual Database Check

Open phpMyAdmin and run:

```sql
SELECT id, username, nama_lengkap, email, role, password_hash 
FROM users 
WHERE username = 'admin';
```

Compare the hash:
- Starts with `$2y$10$92IXUNpkjO0rOQ5byMi...` → Password is **"password"**
- Starts with `$2y$10$s7IZyQCj25/V0rw8MM...` → Password is **"DemoSE2026!"**

---

## 🔍 WHY THIS HAPPENED

### Timeline of the Issue:

1. **Initial Schema Creation** (`schema.sql`)
   - Created admin with password "password"
   - Hash: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`

2. **Later Seed Data Addition** (`seed_dummy_data.sql`)
   - Added multiple demo accounts (admin, operator, pml, pcl)
   - All with same password "DemoSE2026!"
   - Used `ON DUPLICATE KEY UPDATE` which **overwrites** existing admin
   - Hash: `$2y$10$s7IZyQCj25/V0rw8MM.6uOngY4eISUL2JqLjsbq5C19O3JoxN5.Mi`

3. **Result**
   - Documentation says use "password"
   - Actual password is "DemoSE2026!"
   - Login fails with "password"

---

## 📋 ALL DEMO ACCOUNT CREDENTIALS

After running the fix script:

| Username | Password | Role | Status |
|----------|----------|------|--------|
| **admin** | **password** | admin | ✓ Fixed |
| operator.jember | DemoSE2026! | operator | As-is |
| pml.kaliwates | DemoSE2026! | pml | As-is |
| pcl.sumbersari | DemoSE2026! | pcl | As-is |

**Note:** Only admin password is changed by fix script. Other accounts keep "DemoSE2026!".

---

## 🛠️ PREVENT FUTURE ISSUES

### For Development Team:

**1. Document Password Changes**
If changing seed data, update documentation immediately.

**2. Use Consistent Passwords**
Consider using same password for all demo accounts:
```sql
-- All use 'password' for simplicity in development
UPDATE users SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
```

**3. Add Comments in Seed Files**
```sql
-- IMPORTANT: This updates admin password to 'DemoSE2026!'
-- Change back to 'password' for development if needed
```

**4. Create Migration Scripts**
For production, use proper migrations instead of overwriting seed data.

---

## 🚀 QUICK FIX COMMANDS

### For Developers (Command Line):

```bash
# MySQL CLI - Reset admin password to 'password'
mysql -u root -p bps_jember_se2026 -e "UPDATE users SET password_hash = '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'admin';"

# Verify change
mysql -u root -p bps_jember_se2026 -e "SELECT username, LEFT(password_hash, 20) as hash_prefix FROM users WHERE username = 'admin';"
```

### For Production (Jagoan Hosting):

Use phpMyAdmin in cPanel:
1. Login to cPanel
2. Open phpMyAdmin
3. Select database `bpsjembe_se2026`
4. Run SQL:
   ```sql
   UPDATE users 
   SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
   WHERE username = 'admin';
   ```

---

## ✅ VERIFICATION STEPS

After applying fix:

### 1. Test Login
```
URL: http://localhost/se2026-jember
Username: admin
Password: password
Expected: Redirects to dashboard
```

### 2. Check Session
After login, verify in browser console:
```javascript
// Should show logged-in state
document.querySelector('.user-menu'); // Should exist
```

### 3. Verify Database
```sql
-- Should show recent login timestamp
SELECT username, last_login 
FROM users 
WHERE username = 'admin';
```

### 4. Check Activity Logs
```sql
-- Should show login activity
SELECT * FROM activity_logs 
WHERE action = 'login' 
ORDER BY created_at DESC 
LIMIT 5;
```

---

## 🆘 STILL HAVING ISSUES?

### If login still fails after fix:

**Checklist:**
- [ ] Database connection working (check config.php)
- [ ] Correct database selected (`bps_jember_se2026`)
- [ ] Users table exists and has data
- [ ] Admin user `is_active = TRUE`
- [ ] PHP session working properly
- [ ] Browser cookies enabled
- [ ] No PHP errors in error_log

### Debug Mode:

Temporarily enable error display in `config/config.php`:
```php
// Add temporarily for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

Check error logs:
```bash
# Local (Laragon/XAMPP)
type c:\laragon\www\se2026-jember\error_log

# Production (Jagoan Hosting via SSH)
tail -f /home/bpsjembe/logs/error_log
```

---

## 📞 CONTACT SUPPORT

If none of the above solutions work:

1. **Gather Information:**
   - Run `check_admin_user.php` and save output
   - Check error logs
   - Note exact error messages
   - Verify database credentials in config.php

2. **Contact Development Team:**
   - Provide diagnostic output
   - Include steps already tried
   - Mention environment (local/production)

---

## 📝 SUMMARY

**Problem:** Admin login fails with "password"  
**Cause:** Seed data overwrites admin password with different hash  
**Solution:** Run `fix_admin_password.php` or manually update SQL  
**Prevention:** Better documentation and seed file management  

**Quick Fix:**
```bash
# 1. Open browser
http://localhost/se2026-jember/fix_admin_password.php

# 2. Wait for success message

# 3. Login with:
Username: admin
Password: password
```

---

**Last Updated:** March 19, 2026  
**Status:** ✅ Fix available and tested
