# Quick Setup Guide

## 📋 Checklist Setup

### Persiapan
- [ ] Akses cPanel fx.idnads.pro
- [ ] Akses cPanel tradecenter.idnads.pro
- [ ] Generate shared secret: `php -r "echo bin2hex(random_bytes(32));"`

### Deploy fx.idnads.pro
- [ ] Upload semua file ke public_html
- [ ] Buat folder `data/` (permission 755)
- [ ] Copy `config.example.php` → `config.php`
- [ ] Edit `config.php` (paste shared secret)
- [ ] Install SSL certificate
- [ ] Test: https://fx.idnads.pro/expired.html

### Deploy tradecenter.idnads.pro
- [ ] Upload semua file ke public_html
- [ ] Copy `config.example.php` → `config.php`
- [ ] Edit `config.php` (paste shared secret SAMA)
- [ ] Install SSL certificate
- [ ] Test: https://tradecenter.idnads.pro/go/invest

### Testing
- [ ] Test normal flow (generate token → landing page)
- [ ] Test refresh page (cookie session)
- [ ] Test token reuse (harus expired)
- [ ] Test CSS/JS load dengan benar

---

## 🔑 Konfigurasi Penting

### Shared Secret
```bash
# Generate di terminal:
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"

# Hasil contoh:
# a1b2c3d4e5f6789abcdef0123456789abcdef0123456789abcdef0123456789
```

**⚠️ WAJIB:** Gunakan shared secret yang SAMA di kedua config.php!

### fx.idnads.pro/config.php
```php
'shared_secret' => 'PASTE-64-CHAR-HEX-HERE',
'session_lifetime' => 86400, // 24 jam
'token_expiry' => 300, // 5 menit
```

### tradecenter.idnads.pro/config.php
```php
'shared_secret' => 'PASTE-64-CHAR-HEX-YANG-SAMA',
'redirect_url' => 'https://fx.idnads.pro/invest',
```

---

## 🧪 Testing Commands

### Test 1: Generate Token
```bash
curl -I "https://tradecenter.idnads.pro/go/invest?utm_source=test"
# Harus return: 302 redirect dengan token
```

### Test 2: Validate Token
```bash
# Buka URL dari redirect di browser
# Harus tampil landing page + cookie ter-set
```

### Test 3: Refresh
```bash
# Refresh page di browser
# Landing page harus langsung muncul
```

### Test 4: Token Reuse
```bash
# Copy URL dengan token, buka di incognito
# Harus redirect ke expired.html
```

---

## 🔒 Security Checklist

- [ ] HTTPS aktif di kedua domain
- [ ] `config.php` tidak bisa diakses via browser (403)
- [ ] `tokens.db` tidak bisa diakses via browser (403)
- [ ] Cookie `fx_session` flag: HttpOnly, Secure, SameSite
- [ ] Folder `data/` permission 755
- [ ] File PHP permission 644

Test protection:
```
https://fx.idnads.pro/config.php → 403 Forbidden
https://fx.idnads.pro/data/tokens.db → 403 Forbidden
```

---

## 🚀 URL untuk Iklan

Gunakan URL ini di platform iklan:

```
https://tradecenter.idnads.pro/go/invest
```

Parameter tracking otomatis ter-preserve (fbclid, gclid, utm_*).

---

## ⚠️ Common Errors

| Error | Penyebab | Solusi |
|-------|----------|--------|
| 500 Error | Permission folder data/ salah | chmod 755 data/ |
| Token invalid | Shared secret beda | Cek config.php di kedua domain |
| CSS tidak load | Path salah | Gunakan `/css/` bukan `css/` |
| Cookie tidak set | HTTPS tidak aktif | Install SSL certificate |

---

## 📞 Customization

### Ganti WhatsApp Number
File: `fx.idnads.pro/invest/landing-page.php`
```html
<!-- Cari dan ganti: -->
https://wa.me/6281234567890
<!-- Dengan nomor Anda (format: 62xxx tanpa +) -->
```

### Ganti Lifetime
File: `fx.idnads.pro/config.php`
```php
'session_lifetime' => 86400, // 24 jam (ubah sesuai kebutuhan)
'token_expiry' => 300, // 5 menit (ubah sesuai kebutuhan)
```

---

## 📚 Dokumentasi Lengkap

- **[README_SYSTEM.md](README_SYSTEM.md)** - Overview sistem & arsitektur
- **[DEPLOY_GUIDE.md](DEPLOY_GUIDE.md)** - Panduan deploy detail step-by-step

---

**Setup memakan waktu ~15 menit. Good luck! 🎯**
