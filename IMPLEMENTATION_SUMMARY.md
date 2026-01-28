# 🎯 SISTEM LINK IKLAN PERMANEN - IMPLEMENTASI LENGKAP

## ✅ Yang Telah Dibuat

Sistem token sekali pakai dengan session persistent untuk link iklan yang tidak pernah berubah.

### 📁 Struktur File Lengkap

```
landing-page/
│
├── 📘 DOKUMENTASI
│   ├── README_SYSTEM.md          # Overview lengkap sistem
│   ├── DEPLOY_GUIDE.md           # Panduan deploy detail ke cPanel
│   ├── QUICK_SETUP.md            # Quick reference setup
│   └── IMPLEMENTATION_SUMMARY.md # File ini
│
├── 🧪 TESTING & TOOLS
│   ├── test-local.sh             # Script testing lokal
│   ├── test-token.php            # Token generator untuk testing
│   └── .gitignore                # Git ignore rules
│
├── 🌐 FX.IDNADS.PRO (Landing Page Domain)
│   ├── .htaccess                 # Rewrite & security rules
│   ├── .gitignore                # Git ignore
│   ├── config.example.php        # Template config
│   ├── config.php                # Config (secret, db path, lifetime)
│   ├── db.php                    # Database class (SQLite operations)
│   ├── expired.html              # Halaman expired/invalid token
│   ├── privacy-policy.html       # Kebijakan privasi
│   ├── terms.html                # Syarat & ketentuan
│   ├── db-inspector.php          # Tool debugging database (hapus setelah use!)
│   │
│   ├── invest/
│   │   ├── index.php             # Entry point utama (token validation)
│   │   └── landing-page.php      # Landing page HTML (dari index.html)
│   │
│   ├── css/                      # Semua file CSS dari landing-page-1/
│   │   ├── variables.css
│   │   ├── base.css
│   │   ├── banner.css
│   │   ├── contact.css
│   │   ├── package.css
│   │   ├── slideshow.css
│   │   ├── modal.css
│   │   ├── footer.css
│   │   └── responsive.css
│   │
│   ├── js/                       # Semua file JS dari landing-page-1/
│   │   ├── app.js
│   │   ├── modal.js
│   │   └── slideshow.js
│   │
│   └── images/                   # Folder images (copy dari landing-page-1/)
│
└── 🔗 TRADECENTER.IDNADS.PRO (Token Generator)
    ├── .htaccess                 # Security rules
    ├── .gitignore                # Git ignore
    ├── config.example.php        # Template config
    ├── config.php                # Config (shared secret)
    └── go/invest/
        └── index.php             # Token generator & redirect
```

---

## 🔄 Flow Sistem

```
┌─────────────────────────────────────────────────────────────────┐
│                    USER KLIK IKLAN                              │
│    URL: https://tradecenter.idnads.pro/go/invest?utm_*&fbclid=* │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│              TRADECENTER: GENERATE TOKEN                        │
│  - Buat timestamp                                               │
│  - Random 16 bytes                                              │
│  - HMAC signature dengan shared secret                          │
│  - Format: timestamp.random.signature                           │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼ (302 REDIRECT)
┌─────────────────────────────────────────────────────────────────┐
│              FX: VALIDASI TOKEN                                 │
│  URL: https://fx.idnads.pro/invest?t=TOKEN&utm_*&fbclid=*      │
│                                                                 │
│  1. Parse token → timestamp, random, signature                  │
│  2. Verify HMAC signature                                       │
│  3. Check timestamp < 5 menit                                   │
│  4. Check token belum dipakai (SQLite lookup)                   │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                ┌──────────┴──────────┐
                │                     │
            VALID                 INVALID
                │                     │
                ▼                     ▼
┌───────────────────────┐   ┌──────────────────────┐
│   KONSUMSI TOKEN      │   │  REDIRECT EXPIRED    │
│  - Mark token used    │   │  expired.html        │
│  - Generate session   │   └──────────────────────┘
│  - Bind IP+UA         │
│  - Set cookie         │
└──────┬────────────────┘
       │
       ▼ (302 REDIRECT TANPA 't')
┌───────────────────────────────────────────────────────────────┐
│              LANDING PAGE                                     │
│  URL: https://fx.idnads.pro/invest?utm_*&fbclid=*           │
│  Cookie: fx_session=xxx (HttpOnly, Secure, SameSite=Lax)    │
└──────┬────────────────────────────────────────────────────────┘
       │
       ▼ USER REFRESH PAGE
┌───────────────────────────────────────────────────────────────┐
│              VALIDATE SESSION                                 │
│  - Check cookie fx_session                                    │
│  - Lookup in sessions table                                   │
│  - Verify IP + User-Agent match                               │
│  - Check last_activity < 24 jam                               │
│  - Update last_activity                                       │
└──────┬────────────────────────────────────────────────────────┘
       │
       ▼
┌───────────────────────────────────────────────────────────────┐
│              SHOW LANDING PAGE                                │
│  (tanpa redirect, langsung tampil)                           │
└───────────────────────────────────────────────────────────────┘
```

---

## 🔐 Security Features

### 1. Token Security
- **HMAC Signature**: Token di-sign dengan HMAC-SHA256
- **Shared Secret**: 64-char hex secret shared between domains
- **Expiry**: Token valid hanya 5 menit setelah dibuat
- **One-time Use**: Setiap token hanya bisa dipakai 1x
- **Race Condition Protection**: Database transaction untuk atomic consumption

### 2. Session Security
- **HttpOnly Cookie**: Tidak bisa diakses JavaScript (XSS protection)
- **Secure Cookie**: Hanya via HTTPS
- **SameSite=Lax**: CSRF protection
- **IP + User-Agent Binding**: Session terikat dengan IP dan UA
- **Lifetime**: 24 jam (configurable)

### 3. File Protection
- **config.php**: Protected via .htaccess (403 Forbidden)
- **tokens.db**: Protected via .htaccess (403 Forbidden)
- **db.php**: Protected via .htaccess (403 Forbidden)

### 4. Headers Security
- **X-Frame-Options**: SAMEORIGIN (clickjacking protection)
- **X-Content-Type-Options**: nosniff
- **X-XSS-Protection**: 1; mode=block
- **Referrer-Policy**: strict-origin-when-cross-origin

---

## 💾 Database Schema (SQLite)

### Table: tokens
```sql
CREATE TABLE tokens (
    token TEXT PRIMARY KEY,           -- Token string
    used INTEGER DEFAULT 0,           -- 0=unused, 1=used
    session_id TEXT,                  -- Generated session ID
    created_at INTEGER,               -- Unix timestamp
    used_at INTEGER,                  -- Unix timestamp when consumed
    ip_address TEXT,                  -- Client IP
    user_agent TEXT                   -- Client User-Agent
);

CREATE INDEX idx_token_used ON tokens(used);
```

### Table: sessions
```sql
CREATE TABLE sessions (
    session_id TEXT PRIMARY KEY,      -- 64-char random hex
    token TEXT,                       -- Original token
    created_at INTEGER,               -- Unix timestamp
    last_activity INTEGER,            -- Unix timestamp of last request
    ip_address TEXT,                  -- Client IP
    user_agent TEXT                   -- Client User-Agent
);

CREATE INDEX idx_session_activity ON sessions(last_activity);
```

---

## 📝 Konfigurasi

### fx.idnads.pro/config.php
```php
<?php
return [
    // HARUS SAMA dengan tradecenter
    'shared_secret' => 'a1b2c3...64chars',
    
    // Path database
    'db_path' => __DIR__ . '/data/tokens.db',
    
    // Session lifetime (seconds)
    'session_lifetime' => 86400, // 24 jam
    
    // Cookie name
    'session_cookie_name' => 'fx_session',
    
    // Token expiry (seconds)
    'token_expiry' => 300, // 5 menit
    
    // Expired page path
    'expired_page' => '/expired.html',
    
    // Environment
    'environment' => 'production',
];
```

### tradecenter.idnads.pro/config.php
```php
<?php
return [
    // HARUS SAMA dengan fx
    'shared_secret' => 'a1b2c3...64chars',
    
    // Redirect target
    'redirect_url' => 'https://fx.idnads.pro/invest',
    
    // Environment
    'environment' => 'production',
];
```

⚠️ **CRITICAL**: `shared_secret` HARUS identik di kedua domain!

---

## 🚀 Deployment Steps

### Persiapan
1. Generate shared secret:
   ```bash
   php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
   ```
2. Catat secret (64 karakter hex)

### Deploy fx.idnads.pro
1. Upload semua file ke `public_html/`
2. Buat folder `data/` (permission 755)
3. Copy `config.example.php` → `config.php`
4. Edit `config.php`, paste shared secret
5. Install SSL certificate (HTTPS wajib!)
6. Test: https://fx.idnads.pro/expired.html

### Deploy tradecenter.idnads.pro
1. Upload semua file ke `public_html/`
2. Copy `config.example.php` → `config.php`
3. Edit `config.php`, paste shared secret SAMA
4. Install SSL certificate
5. Test: https://tradecenter.idnads.pro/go/invest

### Testing
```bash
# Test 1: Generate token
curl -I "https://tradecenter.idnads.pro/go/invest?utm_source=test"
# → 302 redirect dengan token

# Test 2: Open in browser
# Buka URL dari Test 1 di browser
# → Harus tampil landing page + cookie ter-set

# Test 3: Refresh
# Refresh page
# → Landing page langsung muncul

# Test 4: Token reuse
# Buka URL token yang sama di incognito
# → Redirect ke expired.html
```

---

## 🎯 URL untuk Iklan

Gunakan URL ini di semua platform iklan:

```
https://tradecenter.idnads.pro/go/invest
```

**Keuntungan:**
- ✅ URL tidak pernah berubah
- ✅ Semua parameter tracking ter-preserve (utm_*, fbclid, gclid)
- ✅ Setiap klik generate token baru
- ✅ User bisa refresh tanpa masalah
- ✅ Token sekali pakai (tidak bisa dishare)

**Contoh dengan tracking:**
```
https://tradecenter.idnads.pro/go/invest?utm_source=facebook&utm_campaign=jan2026&fbclid=xxx
```

Semua parameter akan diteruskan ke landing page.

---

## 🛠️ Customization

### 1. Ganti WhatsApp Number
File: `fx.idnads.pro/invest/landing-page.php`

Cari dan ganti:
```html
https://wa.me/6281234567890
```

### 2. Ganti Session Lifetime
File: `fx.idnads.pro/config.php`
```php
'session_lifetime' => 86400, // 24 jam
```

Pilihan:
- 1 jam: 3600
- 12 jam: 43200
- 24 jam: 86400 (default)
- 7 hari: 604800

### 3. Ganti Token Expiry
File: `fx.idnads.pro/config.php`
```php
'token_expiry' => 300, // 5 menit
```

Pilihan:
- 1 menit: 60
- 5 menit: 300 (default)
- 10 menit: 600
- 30 menit: 1800

---

## 🧪 Testing Tools

### 1. test-token.php (Local Testing)
```bash
# Generate test token
php test-token.php

# Generate new secret
php test-token.php --secret
```

### 2. db-inspector.php (Database Debugging)
Upload ke fx.idnads.pro dan akses:
```
https://fx.idnads.pro/db-inspector.php
User: admin
Pass: change-this-password (ubah di file!)
```

Features:
- View tokens & sessions
- Statistics (total, used, active)
- Activity by hour
- Manual cleanup

⚠️ **HAPUS FILE INI SETELAH DEBUGGING!**

### 3. test-local.sh (Local Development)
```bash
# Setup & test locally
bash test-local.sh

# Start servers
cd fx.idnads.pro && php -S localhost:8001
cd tradecenter.idnads.pro && php -S localhost:8002

# Test
http://localhost:8002/go/invest/index.php?utm_source=test
```

---

## ⚠️ Troubleshooting

### Token Invalid / Always Expired
**Problem**: Token selalu redirect ke expired.html

**Solution**:
1. Cek `shared_secret` di kedua config.php HARUS SAMA
2. Cek error log: `cPanel → Error Log`
3. Generate token baru dan test

### Cookie Not Set
**Problem**: Session cookie tidak ter-set

**Solution**:
1. Pastikan HTTPS aktif (cookie secure hanya di HTTPS)
2. Cek PHP session settings di cPanel
3. Clear browser cookies dan coba lagi

### CSS/JS Not Loading
**Problem**: Landing page tampil tanpa styling

**Solution**:
1. Cek path di `landing-page.php`: harus `/css/` bukan `css/`
2. Verify file exists: `public_html/css/base.css`
3. Cek permission: 644 untuk files, 755 untuk folders

### Database Permission Error
**Problem**: SQLite error atau can't write

**Solution**:
```bash
chmod 755 data/
chmod 666 data/tokens.db  # after created
```

### 500 Internal Server Error
**Problem**: White screen atau 500 error

**Solution**:
1. Cek error log di cPanel
2. Verify PHP version (8.0+)
3. Check file permissions
4. Verify SQLite support: `php -m | grep sqlite`

---

## 📊 Monitoring

### Database Size
```bash
# Via terminal
ls -lh public_html/data/tokens.db

# Via cPanel File Manager
# Navigate to data/ folder
```

### Cleanup
Automatic cleanup runs randomly (1% chance per request).

Manual cleanup:
```php
<?php
// Create cleanup.php
require 'config.php';
require 'db.php';
$config = require 'config.php';
$db = new TokenDB($config);
$db->cleanup();
echo "Done!";
```

Access: https://fx.idnads.pro/cleanup.php  
Then delete the file!

### Error Logs
- cPanel → Error Log
- File: `public_html/error_log`

---

## 📋 Checklist Pre-Launch

**fx.idnads.pro:**
- [ ] Semua file uploaded
- [ ] Folder `data/` created (755)
- [ ] `config.php` configured dengan shared secret
- [ ] SSL certificate installed
- [ ] HTTPS redirect working
- [ ] `config.php` protected (test: 403 Forbidden)
- [ ] `tokens.db` protected (test: 403 Forbidden)
- [ ] expired.html accessible
- [ ] CSS/JS loading correctly

**tradecenter.idnads.pro:**
- [ ] Semua file uploaded
- [ ] `config.php` configured dengan shared secret SAMA
- [ ] SSL certificate installed
- [ ] HTTPS redirect working
- [ ] `config.php` protected (test: 403 Forbidden)

**Testing:**
- [ ] Generate token → redirect to fx
- [ ] Landing page shows with cookie
- [ ] Refresh works (session valid)
- [ ] Token reuse → expired.html
- [ ] Old token → expired.html
- [ ] No token → expired.html
- [ ] All tracking parameters preserved

**Security:**
- [ ] Both domains use HTTPS
- [ ] Config files return 403
- [ ] Database file returns 403
- [ ] db-inspector.php deleted
- [ ] Cookie flags: HttpOnly, Secure, SameSite

---

## 📞 Support Resources

1. **[README_SYSTEM.md](README_SYSTEM.md)** - Arsitektur & overview
2. **[DEPLOY_GUIDE.md](DEPLOY_GUIDE.md)** - Step-by-step deployment
3. **[QUICK_SETUP.md](QUICK_SETUP.md)** - Quick reference
4. **Error Logs** - cPanel → Error Log
5. **Database Inspector** - db-inspector.php (temporary use only)

---

## 🎉 Summary

Sistem ini memberikan:

✅ **Link Iklan Permanen** - URL tidak pernah berubah  
✅ **Token Sekali Pakai** - Setiap klik generate token baru  
✅ **Session Persistent** - User bisa refresh tanpa masalah  
✅ **Security First** - HMAC, HttpOnly, Secure cookies  
✅ **Tracking Preserved** - utm_*, fbclid, gclid tetap ada  
✅ **No External DB** - SQLite file-based, simple deploy  
✅ **Easy Monitoring** - db-inspector untuk debugging  

**Total waktu deploy: ~15-20 menit**  
**Maintenance: Minimal (auto cleanup)**

---

**Selamat menggunakan! Semoga sukses dengan kampanye iklan Anda! 🚀**
