# Sistem Link Iklan Permanen dengan Token Sekali Pakai

Sistem ini memungkinkan Anda memiliki URL iklan yang permanen tanpa perlu edit link iklan, dengan fitur:

✅ **Token sekali pakai** - Setiap klik iklan generate token baru  
✅ **Session persistent** - User bisa refresh halaman tanpa masalah  
✅ **Parameter tracking preserved** - utm_*, fbclid, gclid tetap tersimpan  
✅ **Secure by design** - HMAC signature, HttpOnly cookies, SameSite protection  
✅ **No external database** - Menggunakan SQLite (file-based)  

---

## Arsitektur

```
User klik iklan
    ↓
tradecenter.idnads.pro/go/invest
    ↓
Generate token (timestamp.random.signature)
    ↓
Redirect 302 → fx.idnads.pro/invest?t=TOKEN&utm_*&fbclid=xxx
    ↓
Validasi token:
  ✓ Valid & belum dipakai → Konsumsi, buat session, redirect tanpa 't'
  ✗ Sudah dipakai → expired.html
  ✗ Invalid/expired → expired.html
    ↓
Landing page dengan session cookie
    ↓
Refresh → Session valid, langsung tampil (tanpa token)
```

---

## Struktur Folder

```
landing-page/
├── DEPLOY_GUIDE.md              # 📘 Panduan deploy lengkap
├── README.md                     # 📄 Dokumentasi ini
│
├── fx.idnads.pro/               # Landing page utama
│   ├── .htaccess                # Rewrite rules & security
│   ├── config.example.php       # Template konfigurasi
│   ├── config.php               # Konfigurasi (jangan commit!)
│   ├── db.php                   # Database functions (SQLite)
│   ├── expired.html             # Halaman expired
│   ├── privacy-policy.html      # Kebijakan privasi
│   ├── terms.html               # Syarat & ketentuan
│   ├── invest/
│   │   ├── index.php            # Entry point utama
│   │   └── landing-page.php     # Landing page HTML
│   ├── css/                     # All CSS files
│   ├── js/                      # All JS files
│   └── images/                  # All images
│
└── tradecenter.idnads.pro/      # Token generator
    ├── .htaccess                # Security
    ├── config.example.php       # Template konfigurasi
    ├── config.php               # Konfigurasi (jangan commit!)
    └── go/invest/
        └── index.php            # Token generator & redirect
```

---

## Quick Start

### 1. Persiapan

Generate shared secret (akan digunakan di kedua domain):

```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

Catat hasilnya!

### 2. Deploy fx.idnads.pro

1. Upload semua file dari folder `fx.idnads.pro/` ke public_html
2. Buat folder `data/` dengan permission 755
3. Copy `config.example.php` → `config.php`
4. Edit `config.php` dan masukkan shared secret
5. Install SSL certificate
6. Test: https://fx.idnads.pro/expired.html

### 3. Deploy tradecenter.idnads.pro

1. Upload semua file dari folder `tradecenter.idnads.pro/` ke public_html
2. Copy `config.example.php` → `config.php`
3. Edit `config.php` dengan shared secret **YANG SAMA**
4. Install SSL certificate
5. Test: https://tradecenter.idnads.pro/go/invest

### 4. Test Sistem

```
1. Buka: https://tradecenter.idnads.pro/go/invest?utm_source=test
2. Akan redirect ke: https://fx.idnads.pro/invest?t=TOKEN&utm_source=test
3. Akan redirect lagi ke: https://fx.idnads.pro/invest?utm_source=test
4. Landing page muncul dengan cookie session
5. Refresh → Landing page langsung muncul
6. Buka link dengan token yang sama di browser lain → Expired
```

---

## Konfigurasi

### fx.idnads.pro/config.php

```php
<?php
return [
    // Shared secret (HARUS SAMA dengan tradecenter)
    'shared_secret' => 'YOUR-64-CHAR-HEX-SECRET',
    
    // Path database SQLite
    'db_path' => __DIR__ . '/data/tokens.db',
    
    // Session lifetime (detik)
    'session_lifetime' => 86400, // 24 jam
    
    // Nama cookie session
    'session_cookie_name' => 'fx_session',
    
    // Token expiry (detik)
    'token_expiry' => 300, // 5 menit
    
    // Path halaman expired
    'expired_page' => '/expired.html',
    
    // Environment
    'environment' => 'production',
];
```

### tradecenter.idnads.pro/config.php

```php
<?php
return [
    // Shared secret (HARUS SAMA dengan fx)
    'shared_secret' => 'YOUR-64-CHAR-HEX-SECRET',
    
    // URL redirect target
    'redirect_url' => 'https://fx.idnads.pro/invest',
    
    // Environment
    'environment' => 'production',
];
```

**⚠️ PENTING:** `shared_secret` di kedua domain HARUS SAMA!

---

## Requirements

- PHP 8.0+
- SQLite support (biasanya built-in)
- Apache mod_rewrite
- SSL/HTTPS certificate (wajib!)

---

## Cara Kerja

### 1. Token Generation (tradecenter)

```php
$timestamp = time();
$random = bin2hex(random_bytes(16));
$signature = hash_hmac('sha256', $timestamp . '.' . $random, $secret);
$token = $timestamp . '.' . $random . '.' . $signature;
```

Format token: `1738012345.a1b2c3d4e5f6.signature`

### 2. Token Validation (fx)

1. Parse token → timestamp, random, signature
2. Verify signature dengan shared secret
3. Cek apakah timestamp < 5 menit
4. Cek apakah token belum dipakai di database
5. Jika valid → Konsumsi token, buat session

### 3. Session Management

- Session ID: 64 char random hex
- Cookie: HttpOnly, Secure, SameSite=Lax
- Binding: token → session_id → IP + User-Agent
- Lifetime: 24 jam (configurable)

### 4. Database Schema (SQLite)

```sql
-- Table: tokens
CREATE TABLE tokens (
    token TEXT PRIMARY KEY,
    used INTEGER DEFAULT 0,
    session_id TEXT,
    created_at INTEGER,
    used_at INTEGER,
    ip_address TEXT,
    user_agent TEXT
);

-- Table: sessions
CREATE TABLE sessions (
    session_id TEXT PRIMARY KEY,
    token TEXT,
    created_at INTEGER,
    last_activity INTEGER,
    ip_address TEXT,
    user_agent TEXT
);
```

---

## Security Features

### 1. Token Sekali Pakai
- Setiap token hanya bisa digunakan 1 kali
- Race condition protection dengan transaction
- Token expired setelah 5 menit

### 2. Session Security
- Cookie HttpOnly (tidak bisa diakses JavaScript)
- Cookie Secure (hanya via HTTPS)
- Cookie SameSite=Lax (CSRF protection)
- Session binding dengan IP + User-Agent

### 3. HMAC Signature
- Token di-sign dengan HMAC-SHA256
- Shared secret tidak pernah terexpose
- Tidak bisa forge token tanpa secret

### 4. File Protection
- `.htaccess` protect config.php dan db files
- Database tidak bisa diakses via HTTP
- Security headers (X-Frame-Options, etc.)

---

## Testing

### Test 1: Normal Flow
```bash
curl -I "https://tradecenter.idnads.pro/go/invest?utm_source=test"
# → 302 redirect ke fx dengan token

curl -I "https://fx.idnads.pro/invest?t=TOKEN&utm_source=test"
# → 302 redirect tanpa token + set cookie

curl -H "Cookie: fx_session=XXX" "https://fx.idnads.pro/invest?utm_source=test"
# → 200 OK landing page
```

### Test 2: Token Reuse
```bash
# Klik link dengan token yang sama 2x
# Klik ke-2 harus redirect ke expired.html
```

### Test 3: Expired Token
```bash
# Generate token, tunggu 6 menit, buka link
# Harus redirect ke expired.html
```

### Test 4: No Token No Cookie
```bash
curl -I "https://fx.idnads.pro/invest"
# → 302 redirect ke expired.html
```

---

## Monitoring

### Check Database Size
```bash
ls -lh /path/to/public_html/data/tokens.db
```

### Manual Cleanup
```php
<?php
require 'config.php';
require 'db.php';
$config = require 'config.php';
$db = new TokenDB($config);
$db->cleanup();
echo "Cleanup done!";
```

### Error Logs
- cPanel → Error Log
- File: `public_html/error_log`

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| 500 Error | Cek permission folder data/ (755) |
| Token invalid | Pastikan shared_secret SAMA di kedua domain |
| CSS/JS tidak load | Cek path di landing-page.php (harus `/css/` bukan `css/`) |
| Cookie tidak set | Pastikan HTTPS aktif |
| Database error | Cek permission tokens.db (666) |
| mod_rewrite error | Contact hosting untuk enable mod_rewrite |

Lihat [DEPLOY_GUIDE.md](DEPLOY_GUIDE.md) untuk detail lengkap!

---

## Customization

### 1. Ganti WhatsApp Number

Edit `fx.idnads.pro/invest/landing-page.php`:
```html
https://wa.me/6281234567890
```

### 2. Ganti Session Lifetime

Edit `fx.idnads.pro/config.php`:
```php
'session_lifetime' => 86400, // 24 jam (ubah sesuai kebutuhan)
```

### 3. Ganti Token Expiry

Edit `fx.idnads.pro/config.php`:
```php
'token_expiry' => 300, // 5 menit (ubah sesuai kebutuhan)
```

---

## URL untuk Iklan

Gunakan URL ini di platform iklan (Facebook Ads, Google Ads, dll):

```
https://tradecenter.idnads.pro/go/invest
```

Semua parameter tracking akan otomatis ter-preserve:
- Facebook: `fbclid`, `utm_source=facebook`
- Google: `gclid`, `utm_source=google`
- Custom: `utm_campaign`, `utm_medium`, dll.

Contoh:
```
https://tradecenter.idnads.pro/go/invest?utm_source=facebook&utm_campaign=promo2026&fbclid=xxx
    ↓
https://fx.idnads.pro/invest?utm_source=facebook&utm_campaign=promo2026&fbclid=xxx
```

---

## License

Proprietary - All rights reserved

---

## Support

Untuk pertanyaan atau masalah, silakan baca:
1. [DEPLOY_GUIDE.md](DEPLOY_GUIDE.md) - Panduan deploy lengkap
2. Troubleshooting section di atas
3. Error logs di cPanel

---

**Happy Marketing! 🚀**
