# Panduan Deploy Sistem Link Iklan Permanen

## Arsitektur Sistem

Sistem ini terdiri dari 2 domain:

1. **tradecenter.idnads.pro** - Generator token dan redirect
2. **fx.idnads.pro** - Landing page dengan validasi token dan session management

### Flow Sistem:

```
User klik iklan
    ↓
https://tradecenter.idnads.pro/go/invest?utm_source=fb&fbclid=xxx
    ↓
Generate token (timestamp.random.signature)
    ↓
Redirect 302 ke: https://fx.idnads.pro/invest?t=TOKEN&utm_source=fb&fbclid=xxx
    ↓
fx.idnads.pro validasi token:
  - Token valid & belum dipakai → Konsumsi token, buat session, set cookie, redirect tanpa 't'
  - Token sudah dipakai → Redirect ke expired.html
  - Token invalid/expired → Redirect ke expired.html
    ↓
User di landing page dengan cookie session
    ↓
Refresh page → Cookie masih valid, langsung tampilkan landing page
```

---

## Persiapan Sebelum Deploy

### 1. Requirements
- PHP 8.0 atau lebih tinggi
- SQLite support (biasanya sudah termasuk di PHP)
- mod_rewrite Apache (untuk clean URLs)
- HTTPS/SSL certificate (wajib untuk cookie secure)

### 2. Generate Shared Secret
Sebelum deploy, generate shared secret yang sama untuk kedua domain:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

Catat hasilnya, misalnya: `a1b2c3d4e5f6...` (64 karakter)

---

## Deploy ke cPanel - Domain: fx.idnads.pro

### Langkah 1: Upload Files via File Manager

1. Login ke cPanel fx.idnads.pro
2. Buka **File Manager**
3. Navigasi ke folder `public_html` (atau folder root domain Anda)
4. Upload semua file dari folder `fx.idnads.pro/`:
   ```
   public_html/
   ├── .htaccess
   ├── config.example.php
   ├── config.php (akan dibuat/edit manual)
   ├── db.php
   ├── expired.html
   ├── privacy-policy.html
   ├── terms.html
   ├── invest/
   │   ├── index.php
   │   └── landing-page.php
   ├── css/
   │   └── (semua file CSS)
   ├── js/
   │   └── (semua file JS)
   └── images/
       └── (semua file images jika ada)
   ```

### Langkah 2: Buat Folder Data

1. Di File Manager, buat folder baru bernama `data` di root (public_html)
2. Set permission folder `data` menjadi **0755**
3. File database `tokens.db` akan dibuat otomatis saat pertama kali diakses

### Langkah 3: Konfigurasi config.php

1. Edit file `config.php` di root folder
2. Ubah nilai berikut:

```php
<?php
return [
    // PENTING: Gunakan shared secret yang sama dengan tradecenter
    'shared_secret' => 'PASTE-SHARED-SECRET-YANG-ANDA-GENERATE',
    
    'db_path' => __DIR__ . '/data/tokens.db',
    'session_lifetime' => 86400, // 24 jam
    'session_cookie_name' => 'fx_session',
    'token_expiry' => 300, // 5 menit
    'expired_page' => '/expired.html',
    'environment' => 'production',
];
```

3. **Save** file

### Langkah 4: Set Permissions

Set permission untuk file dan folder:

```
chmod 644 *.php
chmod 644 *.html
chmod 755 invest/
chmod 644 invest/*.php
chmod 755 css/ js/ images/
chmod 755 data/
chmod 666 data/tokens.db (setelah file dibuat)
```

Atau via File Manager:
- Files (.php, .html): 644
- Folders: 755
- Database file (setelah dibuat): 666

### Langkah 5: Verifikasi .htaccess

Pastikan file `.htaccess` ada dan berisi:

```apache
# Protect config files
<Files "config.php">
    Require all denied
</Files>

<Files "db.php">
    Require all denied
</Files>

# Rewrite rules for clean URLs
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Rewrite /invest to /invest/index.php
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^invest/?$ /invest/index.php [QSA,L]
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

Options -Indexes

<FilesMatch "\.(db|sqlite|sqlite3)$">
    Require all denied
</FilesMatch>
```

### Langkah 6: Test SSL/HTTPS

1. Buka https://fx.idnads.pro (pastikan HTTPS aktif)
2. Jika belum ada SSL, install via cPanel:
   - **SSL/TLS Status** → Install SSL certificate
   - Atau gunakan **Let's Encrypt** (gratis)

---

## Deploy ke cPanel - Domain: tradecenter.idnads.pro

### Langkah 1: Upload Files via File Manager

1. Login ke cPanel tradecenter.idnads.pro
2. Buka **File Manager**
3. Navigasi ke folder `public_html`
4. Upload semua file dari folder `tradecenter.idnads.pro/`:
   ```
   public_html/
   ├── .htaccess
   ├── config.example.php
   ├── config.php (akan dibuat/edit manual)
   └── go/
       └── invest/
           └── index.php
   ```

### Langkah 2: Konfigurasi config.php

1. Edit file `config.php` di root folder
2. Ubah nilai berikut:

```php
<?php
return [
    // PENTING: Harus SAMA dengan fx.idnads.pro
    'shared_secret' => 'PASTE-SHARED-SECRET-YANG-SAMA',
    
    'redirect_url' => 'https://fx.idnads.pro/invest',
    'environment' => 'production',
];
```

3. **PASTIKAN `shared_secret` SAMA** dengan fx.idnads.pro
4. **Save** file

### Langkah 3: Set Permissions

```
chmod 644 *.php
chmod 755 go/
chmod 755 go/invest/
chmod 644 go/invest/index.php
```

### Langkah 4: Verifikasi .htaccess

Pastikan file `.htaccess` ada dan berisi:

```apache
# Protect config files
<Files "config.php">
    Require all denied
</Files>

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

Options -Indexes
```

### Langkah 5: Test SSL/HTTPS

Pastikan HTTPS aktif untuk tradecenter.idnads.pro

---

## Testing Sistem

### Test 1: Generate Token dan Redirect

1. Buka URL: `https://tradecenter.idnads.pro/go/invest?utm_source=test`
2. Anda harus di-redirect ke: `https://fx.idnads.pro/invest?t=XXXX.YYYY.ZZZZ&utm_source=test`

### Test 2: Token Consumption dan Landing Page

1. Setelah redirect, Anda akan di-redirect lagi (tanpa parameter `t`)
2. URL final: `https://fx.idnads.pro/invest?utm_source=test`
3. Landing page harus muncul dengan semua CSS/JS berfungsi
4. Cookie `fx_session` harus ter-set (cek di Developer Tools → Application → Cookies)

### Test 3: Refresh Page (Cookie Valid)

1. Refresh halaman `https://fx.idnads.pro/invest?utm_source=test`
2. Landing page harus langsung muncul tanpa redirect
3. Cookie masih valid

### Test 4: Token Sekali Pakai

1. Copy URL dengan token dari Test 1: `https://fx.idnads.pro/invest?t=XXXX.YYYY.ZZZZ&utm_source=test`
2. Buka di browser baru atau incognito
3. Anda harus di-redirect ke `https://fx.idnads.pro/expired.html`
4. Karena token sudah dipakai

### Test 5: Expired Token

1. Generate token baru dari tradecenter
2. Tunggu 6 menit (token expired setelah 5 menit)
3. Buka URL dengan token tersebut
4. Anda harus di-redirect ke `https://fx.idnads.pro/expired.html`

### Test 6: No Token No Cookie

1. Buka `https://fx.idnads.pro/invest` tanpa parameter `t` di incognito/private mode
2. Anda harus langsung di-redirect ke `https://fx.idnads.pro/expired.html`

---

## Troubleshooting

### Problem: 500 Internal Server Error

**Solusi:**
1. Cek error log di cPanel → **Error Log**
2. Kemungkinan:
   - Permission salah (folder data harus 755)
   - SQLite tidak terinstall (jarang terjadi di PHP 8)
   - Syntax error di config.php

### Problem: Token Invalid / Selalu Redirect ke Expired

**Solusi:**
1. Pastikan `shared_secret` di fx.idnads.pro dan tradecenter.idnads.pro **SAMA PERSIS**
2. Cek error log di cPanel
3. Coba generate token baru

### Problem: Landing Page Tidak Muncul (CSS/JS Tidak Load)

**Solusi:**
1. Cek path di `landing-page.php`:
   - CSS: `/css/filename.css`
   - JS: `/js/filename.js`
2. Pastikan folder css/, js/, images/ ada di root public_html
3. Cek permission file CSS/JS (harus 644)

### Problem: Cookie Tidak Ter-set

**Solusi:**
1. Pastikan HTTPS aktif (cookie secure hanya bekerja di HTTPS)
2. Cek setting PHP session di cPanel → **Select PHP Version** → **Options**:
   - `session.cookie_httponly` = On
   - `session.cookie_secure` = On

### Problem: Database Permission Error

**Solusi:**
1. Folder `data/` permission 755
2. File `tokens.db` (setelah dibuat) permission 666
3. Atau coba 777 untuk testing (jangan di production!)

### Problem: mod_rewrite Tidak Bekerja

**Solusi:**
1. Pastikan mod_rewrite enabled di cPanel
2. Akses langsung: `https://fx.idnads.pro/invest/index.php?t=xxx` harus bekerja
3. Jika tidak bisa, contact hosting support untuk enable mod_rewrite

---

## Monitoring dan Maintenance

### 1. Cek Database Size

Database SQLite ada di: `public_html/data/tokens.db`

Cek size via File Manager atau:
```bash
ls -lh data/tokens.db
```

### 2. Manual Cleanup (Opsional)

Cleanup otomatis berjalan secara random (1% chance setiap request).

Untuk manual cleanup, buat file `cleanup.php`:

```php
<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$config = require __DIR__ . '/config.php';
$db = new TokenDB($config);

$db->cleanup();

echo "Cleanup completed!";
```

Jalankan via browser: `https://fx.idnads.pro/cleanup.php` (lalu hapus file ini)

### 3. Monitoring Error Log

Secara berkala cek error log di cPanel untuk detect masalah:
- **cPanel → Error Log**
- Atau file: `public_html/error_log`

---

## Security Best Practices

### 1. Protect config.php

File `.htaccess` sudah mengamankan `config.php`, pastikan tidak bisa diakses:
- Test: https://fx.idnads.pro/config.php (harus 403 Forbidden)

### 2. Protect Database

File `.htaccess` sudah block akses ke file `.db`:
- Test: https://fx.idnads.pro/data/tokens.db (harus 403 Forbidden)

### 3. Regular Backup

Backup via cPanel → **Backup**:
- Full backup (weekly)
- Database backup (daily)

### 4. Update Shared Secret

Jika terjadi security breach, generate shared secret baru:
1. Generate new secret
2. Update di fx.idnads.pro/config.php
3. Update di tradecenter.idnads.pro/config.php
4. Restart (clear old sessions akan invalid)

### 5. Monitor Unusual Activity

Check database untuk pattern aneh:
- Terlalu banyak token dari 1 IP
- Session yang tidak normal

---

## Customization

### 1. Ganti WhatsApp Number

Edit file `fx.idnads.pro/invest/landing-page.php`:

Cari dan ganti semua:
```html
https://wa.me/6281234567890
```

Menjadi nomor WhatsApp Anda (format: 62xxxx tanpa +)

### 2. Ganti Session Lifetime

Edit `fx.idnads.pro/config.php`:
```php
'session_lifetime' => 86400, // 24 jam (ubah sesuai kebutuhan)
```

Contoh:
- 1 jam: 3600
- 12 jam: 43200
- 24 jam: 86400
- 7 hari: 604800

### 3. Ganti Token Expiry

Edit `fx.idnads.pro/config.php`:
```php
'token_expiry' => 300, // 5 menit (ubah sesuai kebutuhan)
```

Contoh:
- 1 menit: 60
- 5 menit: 300
- 10 menit: 600

---

## Support dan Contact

Jika ada masalah saat deploy:

1. Cek dokumentasi ini terlebih dahulu
2. Cek error log di cPanel
3. Test step-by-step sesuai Testing Sistem
4. Screenshot error untuk troubleshooting

---

## Checklist Deploy

**fx.idnads.pro:**
- [ ] Upload semua file (PHP, HTML, CSS, JS)
- [ ] Buat folder `data/` dengan permission 755
- [ ] Edit `config.php` dengan shared secret
- [ ] Verifikasi .htaccess
- [ ] Install SSL certificate (HTTPS)
- [ ] Test akses expired.html
- [ ] Test protection config.php (harus 403)

**tradecenter.idnads.pro:**
- [ ] Upload semua file PHP
- [ ] Edit `config.php` dengan shared secret SAMA
- [ ] Verifikasi .htaccess
- [ ] Install SSL certificate (HTTPS)
- [ ] Test protection config.php (harus 403)

**Testing:**
- [ ] Test generate token dan redirect
- [ ] Test landing page muncul
- [ ] Test cookie session
- [ ] Test refresh page
- [ ] Test token sekali pakai
- [ ] Test expired token
- [ ] Test no token no cookie

---

## File Structure Summary

### fx.idnads.pro/
```
public_html/
├── .htaccess                    # Rewrite rules & security
├── .gitignore                   # Git ignore
├── config.example.php           # Config template
├── config.php                   # Config (jangan commit)
├── db.php                       # Database functions
├── expired.html                 # Expired page
├── privacy-policy.html          # Privacy policy
├── terms.html                   # Terms & conditions
├── data/                        # Database folder (755)
│   └── tokens.db               # SQLite database (auto-created)
├── invest/
│   ├── index.php               # Main entry point
│   └── landing-page.php        # Landing page HTML
├── css/                        # All CSS files
├── js/                         # All JS files
└── images/                     # All image files
```

### tradecenter.idnads.pro/
```
public_html/
├── .htaccess                    # Security
├── .gitignore                   # Git ignore
├── config.example.php           # Config template
├── config.php                   # Config (jangan commit)
└── go/
    └── invest/
        └── index.php           # Token generator
```

---

**Selamat! Sistem link iklan permanen Anda sudah siap digunakan! 🚀**
