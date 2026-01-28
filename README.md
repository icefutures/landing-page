# 🎯 Sistem Link Iklan Permanen dengan Token Sekali Pakai

> Sistem token-based untuk link iklan yang permanen, dengan fitur token sekali pakai dan session persistent untuk user experience yang optimal.

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)](https://php.net)
[![SQLite](https://img.shields.io/badge/SQLite-3-003B57?logo=sqlite&logoColor=white)](https://sqlite.org)
[![License](https://img.shields.io/badge/License-Proprietary-red.svg)](LICENSE)

---

## 📖 Tentang Sistem

Sistem ini menyelesaikan masalah umum dalam advertising:

**❌ Masalah Lama:**
- Link iklan harus diganti setiap kali ada perubahan
- Token dibagikan dan digunakan berkali-kali
- Session hilang saat refresh

**✅ Solusi Kami:**
- ✅ Link iklan **permanen**, tidak pernah berubah
- ✅ Token **sekali pakai**, tidak bisa dibagikan
- ✅ Session **persistent**, user bisa refresh tanpa masalah
- ✅ Parameter tracking **ter-preserve** (utm_*, fbclid, gclid)
- ✅ Security **first** (HMAC, HttpOnly cookies, HTTPS)

---

## 🚀 Quick Start

### 1️⃣ Generate Shared Secret
```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```
Catat hasilnya (64 karakter)!

### 2️⃣ Deploy fx.idnads.pro
```bash
# Upload ke cPanel public_html:
# - Semua file dari fx.idnads.pro/
# - Buat folder data/ (chmod 755)
# - Copy config.example.php → config.php
# - Edit config.php (paste secret)
# - Install SSL certificate
```

### 3️⃣ Deploy tradecenter.idnads.pro
```bash
# Upload ke cPanel public_html:
# - Semua file dari tradecenter.idnads.pro/
# - Copy config.example.php → config.php
# - Edit config.php (paste secret SAMA)
# - Install SSL certificate
```

### 4️⃣ Test!
```
Buka: https://tradecenter.idnads.pro/go/invest?utm_source=test
✓ Redirect ke fx dengan token
✓ Landing page muncul
✓ Cookie ter-set
✓ Refresh works!
```

**Total waktu: ~15 menit**

---

## 📚 Dokumentasi

| File | Deskripsi |
|------|-----------|
| **[QUICK_SETUP.md](QUICK_SETUP.md)** | ⚡ Quick reference & checklist |
| **[DEPLOY_GUIDE.md](DEPLOY_GUIDE.md)** | 📘 Panduan deploy lengkap ke cPanel |
| **[README_SYSTEM.md](README_SYSTEM.md)** | 🏗️ Arsitektur & cara kerja sistem |
| **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)** | 📋 Summary implementasi lengkap |

**Mulai dari:** [QUICK_SETUP.md](QUICK_SETUP.md) untuk pemula

---

## 🎯 Cara Kerja

```
User klik iklan di Facebook/Google
    ↓
https://tradecenter.idnads.pro/go/invest?utm_*&fbclid=*
    ↓
[Generate token dengan HMAC signature]
    ↓ 302 Redirect
https://fx.idnads.pro/invest?t=TOKEN&utm_*&fbclid=*
    ↓
[Validasi token, konsumsi (sekali pakai), buat session]
    ↓ 302 Redirect (tanpa token)
https://fx.idnads.pro/invest?utm_*&fbclid=*
    ↓
[Landing page + cookie session HttpOnly Secure]
    ↓
User refresh → Landing page langsung muncul (session valid)
```

**Token Format:** `timestamp.random.hmac_signature`

**Token Lifetime:** 5 menit (configurable)

**Session Lifetime:** 24 jam (configurable)

---

## 📁 Struktur Project

```
landing-page/
│
├── 📘 Dokumentasi
│   ├── QUICK_SETUP.md
│   ├── DEPLOY_GUIDE.md
│   ├── README_SYSTEM.md
│   └── IMPLEMENTATION_SUMMARY.md
│
├── 🧪 Testing Tools
│   ├── test-local.sh              # Local testing dengan PHP built-in server
│   └── test-token.php             # Token generator untuk testing
│
├── 🌐 fx.idnads.pro/             # Landing Page (Main Domain)
│   ├── invest/
│   │   ├── index.php              # Entry point & token validation
│   │   └── landing-page.php       # Landing page HTML
│   ├── css/                       # All CSS files
│   ├── js/                        # All JS files
│   ├── images/                    # Images
│   ├── config.php                 # Config (shared secret, lifetime, etc)
│   ├── db.php                     # SQLite database class
│   ├── expired.html               # Expired token page
│   ├── db-inspector.php           # Debug tool (delete after use)
│   └── .htaccess                  # Security & rewrite rules
│
└── 🔗 tradecenter.idnads.pro/    # Token Generator
    ├── go/invest/
    │   └── index.php              # Token generator & redirect
    ├── config.php                 # Config (shared secret)
    └── .htaccess                  # Security rules
```

---

## 🔐 Security Features

| Feature | Implementation |
|---------|---------------|
| **Token Signature** | HMAC-SHA256 dengan shared secret |
| **One-time Use** | Token disimpan di SQLite setelah dipakai |
| **Token Expiry** | Valid hanya 5 menit |
| **Session Security** | HttpOnly, Secure, SameSite=Lax cookies |
| **Session Binding** | IP + User-Agent validation |
| **File Protection** | .htaccess protect config & database |
| **HTTPS Only** | Force HTTPS redirect |
| **Security Headers** | X-Frame-Options, CSP, XSS Protection |

---

## ⚙️ Konfigurasi

### fx.idnads.pro/config.php
```php
<?php
return [
    'shared_secret' => 'YOUR-64-CHAR-HEX-SECRET', // HARUS SAMA dengan tradecenter
    'db_path' => __DIR__ . '/data/tokens.db',
    'session_lifetime' => 86400,  // 24 jam
    'token_expiry' => 300,        // 5 menit
    'session_cookie_name' => 'fx_session',
    'expired_page' => '/expired.html',
    'environment' => 'production',
];
```

### tradecenter.idnads.pro/config.php
```php
<?php
return [
    'shared_secret' => 'YOUR-64-CHAR-HEX-SECRET', // HARUS SAMA dengan fx
    'redirect_url' => 'https://fx.idnads.pro/invest',
    'environment' => 'production',
];
```

⚠️ **PENTING:** `shared_secret` harus IDENTIK di kedua domain!

---

## 🧪 Testing

### Manual Testing
```bash
# 1. Generate token
curl -I "https://tradecenter.idnads.pro/go/invest?utm_source=test"

# 2. Open di browser (use URL dari step 1)
# → Harus tampil landing page

# 3. Refresh page
# → Landing page langsung muncul

# 4. Open same token URL di incognito
# → Harus redirect ke expired.html
```

### Local Testing
```bash
# Setup local environment
bash test-local.sh

# Terminal 1: Start fx server
cd fx.idnads.pro && php -S localhost:8001

# Terminal 2: Start tradecenter server
cd tradecenter.idnads.pro && php -S localhost:8002

# Browser: Test
http://localhost:8002/go/invest/index.php?utm_source=test
```

---

## 🎯 URL untuk Iklan

Gunakan URL ini di **semua** platform iklan:

```
https://tradecenter.idnads.pro/go/invest
```

✅ URL ini **tidak pernah berubah**  
✅ Semua parameter tracking **otomatis ter-preserve**  
✅ Setiap klik **generate token baru**  

**Contoh dengan tracking:**
```
Facebook Ads:
https://tradecenter.idnads.pro/go/invest?utm_source=facebook&utm_campaign=promo2026&fbclid=xxx

Google Ads:
https://tradecenter.idnads.pro/go/invest?utm_source=google&utm_campaign=promo2026&gclid=yyy
```

---

## 🛠️ Customization

### 1. Ganti WhatsApp Number
**File:** `fx.idnads.pro/invest/landing-page.php`

Cari dan ganti semua:
```html
https://wa.me/6281234567890
```
Dengan: `https://wa.me/62XXXXXXXXXXX` (format: 62xxx tanpa +)

### 2. Ganti Session Lifetime
**File:** `fx.idnads.pro/config.php`
```php
'session_lifetime' => 86400, // 24 jam

// Pilihan:
// 1 jam: 3600
// 12 jam: 43200
// 24 jam: 86400
// 7 hari: 604800
```

### 3. Ganti Token Expiry
**File:** `fx.idnads.pro/config.php`
```php
'token_expiry' => 300, // 5 menit

// Pilihan:
// 1 menit: 60
// 5 menit: 300
// 10 menit: 600
```

---

## 🔧 Troubleshooting

| Problem | Solution |
|---------|----------|
| Token invalid | Cek shared_secret di kedua config.php harus SAMA |
| Cookie tidak set | Pastikan HTTPS aktif di kedua domain |
| CSS/JS tidak load | Cek path di landing-page.php (gunakan `/css/` bukan `css/`) |
| 500 Error | Cek permission folder data/ (755) dan PHP version (8.0+) |
| Database error | chmod 755 data/, chmod 666 tokens.db |

**Detail lengkap:** [DEPLOY_GUIDE.md](DEPLOY_GUIDE.md#troubleshooting)

---

## 📊 Monitoring

### Database Inspector
Upload `fx.idnads.pro/db-inspector.php` ke server:

```
https://fx.idnads.pro/db-inspector.php
User: admin
Pass: change-this-password
```

Features:
- View tokens & sessions
- Statistics & analytics
- Manual cleanup
- Activity by hour

⚠️ **HAPUS setelah debugging!**

### Cleanup
Auto cleanup runs randomly (1% chance per request).

Manual: Buat file `cleanup.php`:
```php
<?php
require 'config.php';
require 'db.php';
$config = require 'config.php';
$db = new TokenDB($config);
$db->cleanup();
echo "Done!";
```

---

## 💻 Requirements

- PHP 8.0+
- SQLite support (built-in)
- Apache mod_rewrite
- SSL/HTTPS certificate (wajib)
- cPanel or similar hosting

---

## 📦 Deploy Checklist

**Persiapan:**
- [ ] Generate shared secret
- [ ] Akses cPanel kedua domain

**fx.idnads.pro:**
- [ ] Upload semua file
- [ ] Buat folder data/ (755)
- [ ] Configure config.php
- [ ] Install SSL
- [ ] Test protection (config.php → 403)

**tradecenter.idnads.pro:**
- [ ] Upload semua file
- [ ] Configure config.php (secret SAMA)
- [ ] Install SSL
- [ ] Test protection (config.php → 403)

**Testing:**
- [ ] Generate token
- [ ] Landing page shows
- [ ] Refresh works
- [ ] Token reuse → expired
- [ ] All parameters preserved

---

## 🤝 Support

Jika ada masalah:

1. Baca [QUICK_SETUP.md](QUICK_SETUP.md) untuk quick reference
2. Baca [DEPLOY_GUIDE.md](DEPLOY_GUIDE.md) untuk troubleshooting
3. Cek error log di cPanel
4. Gunakan db-inspector.php untuk debugging database

---

## 📝 License

Proprietary - All rights reserved

---

## 🌟 Features Summary

| Feature | Status |
|---------|--------|
| Link permanen | ✅ |
| Token sekali pakai | ✅ |
| Session persistent | ✅ |
| Parameter tracking | ✅ |
| HMAC security | ✅ |
| HttpOnly cookies | ✅ |
| HTTPS only | ✅ |
| SQLite database | ✅ |
| Auto cleanup | ✅ |
| Debug tools | ✅ |
| cPanel compatible | ✅ |
| No external DB | ✅ |

---

**Dibuat dengan ❤️ untuk solusi advertising yang aman dan efisien**

**Happy Marketing! 🚀**
