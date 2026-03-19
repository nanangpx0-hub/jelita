# 🚀 QUICK START GUIDE - SISE2026
## Automatic Environment Detection

**Status:** ✅ Ready to Use - No Configuration Required!

---

## ⚡ INSTANT SETUP

### For Local Development (XAMPP/Laragon)

```bash
# 1. Place files in web root
c:\laragon\www\se2026-jember\

# 2. Access via browser
http://localhost/se2026-jember

# That's it! Everything works automatically!
```

✅ Uses local database: `bps_jember_se2026`  
✅ Credentials: `root` / (no password)  
✅ Error display enabled for debugging

---

### For Production (Jagoan Hosting)

```bash
# 1. Upload files to server
cd /home/bpsjembe/public_html

# 2. Access via browser
https://se2026.bpsjember.my.id

# That's it! Everything works automatically!
```

✅ Uses production database: `bpsjembe_se2026`  
✅ Credentials: `bpsjembe_nanangpx` / `N4n4n9J3mb3r350917`  
✅ HTTPS enforced, errors logged only

---

## 🔍 HOW IT KNOWS

The application checks your website address:

| You Access Via | App Detects As | Database Used |
|----------------|----------------|---------------|
| `localhost/...` | Local Development | `bps_jember_se2026` |
| `127.0.0.1/...` | Local Development | `bps_jember_se2026` |
| `se2026.bpsjember.my.id` | Production | `bpsjembe_se2026` |
| Any other domain | Production | `bpsjembe_se2026` |

**No configuration needed!**

---

## 📊 DATABASE CREDENTIALS

### Local Development (Automatic)
```
Host: localhost
Database: bps_jember_se2026
Username: root
Password: (empty)
```

### Production (Automatic)
```
Host: localhost
Database: bpsjembe_se2026
Username: bpsjembe_nanangpx
Password: N4n4n9J3mb3r350917
```

---

## ⚙️ OPTIONAL CONFIGURATION

Most settings work great with defaults, but you can customize via `.env`:

```env
# Session lifetime (seconds)
SESSION_LIFETIME=7200

# Force secure cookies (production)
SESSION_SECURE=true

# Max upload size (bytes)
MAX_UPLOAD_SIZE=5242880
```

---

## 🧪 TEST IT WORKS

### Quick Tests

**Local:**
```
✓ Open: http://localhost/se2026-jember
✓ Login page loads
✓ Can authenticate
✓ File upload works
```

**Production:**
```
✓ Open: https://se2026.bpsjember.my.id
✓ HTTPS active (no warnings)
✓ Login page loads
✓ Can authenticate
✓ File upload works
```

---

## 🛠️ TROUBLESHOOTING

### Can't Connect to Database (Local)

```
✓ Check MySQL is running (Laragon/XAMPP icon)
✓ Create database: bps_jember_se2026
✓ Import schema from sql/schema.sql
```

### Can't Connect to Database (Production)

```
✓ Verify credentials match cPanel
✓ Check database exists: bpsjembe_se2026
✓ User has full privileges
```

### Wrong Environment Detected

```
✓ Check URL you're accessing
✓ Only localhost and 127.0.0.1 = development
✓ All other domains = production
```

---

## 📁 IMPORTANT FILES

- **Configuration:** `config/config.php` (auto-detects environment)
- **Environment:** `.env` (optional settings only)
- **Documentation:** `AUTO_ENV_DETECTION.md` (full details)

---

## 🔐 SECURITY NOTES

**Production credentials are in `config/config.php`:**
- ✅ Not accessible via web browser
- ✅ Protected by PHP execution
- ✅ Keep Git repository private
- ✅ Consider changing password after deployment

---

## 📞 NEED MORE INFO?

**Full Documentation:**
- `AUTO_ENV_DETECTION.md` - Complete technical guide
- `DEPLOYMENT_GUIDE.md` - Step-by-step deployment
- `QUICK_DEPLOYMENT_REFERENCE.md` - Developer reference

---

**That's all you need to know! Just access the app and it works! 🎉**

**Last Updated:** March 19, 2026
