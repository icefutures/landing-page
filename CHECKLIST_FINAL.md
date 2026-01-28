# ✅ CHECKLIST PROJECT - Ready to Deploy!

## 📦 Files yang Siap Upload

### tradecenter.idnads.pro (6 files)
```
✅ .gitignore
✅ .htaccess
✅ config.example.php
✅ config.php (placeholder, akan di-setup installer)
✅ installer.php (auto-setup tool)
✅ go/invest/index.php
```

### fx.idnads.pro (21+ files)
```
✅ .gitignore
✅ .htaccess
✅ config.example.php
✅ config.php (placeholder, akan di-setup installer)
✅ db.php (SQLite database class)
✅ db-inspector.php (debug tool)
✅ installer.php (auto-setup tool)
✅ expired.html
✅ privacy-policy.html
✅ terms.html
✅ invest/index.php (main entry point)
✅ invest/landing-page.php (landing page HTML)
✅ css/ (9 files)
✅ js/ (3 files)
✅ images/ (folder ready)
```

---

## 🎯 Upload Instructions

### Method 1: ZIP Upload (Recommended)

**tradecenter.idnads.pro:**
```bash
cd /workspaces/landing-page/tradecenter.idnads.pro/
zip -r tradecenter-upload.zip * .htaccess .gitignore
```
Upload `tradecenter-upload.zip` ke `/home/idnafevn/tradecenter.idnads.pro/` dan extract.

**fx.idnads.pro:**
```bash
cd /workspaces/landing-page/fx.idnads.pro/
zip -r fx-upload.zip * .htaccess .gitignore
```
Upload `fx-upload.zip` ke `/home/idnafevn/fx.idnads.pro/` dan extract.

### Method 2: FTP/File Manager Upload

Upload **ISI** folder (bukan foldernya sendiri):
- `tradecenter.idnads.pro/*` → `/home/idnafevn/tradecenter.idnads.pro/`
- `fx.idnads.pro/*` → `/home/idnafevn/fx.idnads.pro/`

---

## 🔧 Setup dengan Installer (5 Menit)

### 1. Setup tradecenter (2 menit)
```
https://tradecenter.idnads.pro/installer.php
→ Klik "Generate Shared Secret & Setup"
→ Download secret.txt
```

### 2. Setup fx (2 menit)
```
Upload secret.txt ke /home/idnafevn/fx.idnads.pro/
https://fx.idnads.pro/installer.php
→ Klik "Use Shared Secret dari File"
→ Klik "Selesai Setup"
```

### 3. Test (1 menit)
```
https://tradecenter.idnads.pro/go/invest?utm_source=test
→ Should redirect to fx
→ Landing page shows
→ Refresh works
```

### 4. Cleanup
```
Delete installer.php from both domains
Delete secret.txt from fx
```

---

## ✅ What Installer Does Automatically

**tradecenter:**
- ✅ Generate 64-char shared secret
- ✅ Create config.php with secret
- ✅ Save secret.txt for sharing
- ✅ Mark as installed

**fx:**
- ✅ Read shared secret from secret.txt
- ✅ Create config.php with same secret
- ✅ Create data/ folder (755 permission)
- ✅ Mark as installed

**You don't need to:**
- ❌ Edit any PHP files
- ❌ Generate secret manually
- ❌ Create folders manually
- ❌ Set permissions (mostly auto)

---

## 🎯 URL untuk Iklan

Setelah setup selesai, gunakan URL ini:
```
https://tradecenter.idnads.pro/go/invest
```

Parameter tracking (utm_*, fbclid, gclid) otomatis ter-preserve!

---

## 📝 Optional: Custom WhatsApp

Edit `/home/idnafevn/fx.idnads.pro/invest/landing-page.php`:
```html
<!-- Cari: -->
https://wa.me/6281234567890

<!-- Ganti dengan nomor Anda: -->
https://wa.me/62XXXXXXXXXXX
```

---

## 🔒 Security Checklist

**After setup:**
- [ ] Config.php cannot be accessed (403)
- [ ] Database file cannot be accessed (403)
- [ ] Installer.php deleted
- [ ] Secret.txt deleted
- [ ] HTTPS active on both domains
- [ ] Test token reuse → expired.html

---

## 📊 Features Summary

| Feature | Status |
|---------|--------|
| Auto installer | ✅ |
| Link permanen | ✅ |
| Token sekali pakai | ✅ |
| Session persistent | ✅ |
| Parameter tracking | ✅ |
| HMAC security | ✅ |
| HttpOnly cookies | ✅ |
| SQLite database | ✅ |
| Auto cleanup | ✅ |
| Debug tools | ✅ |
| Complete docs | ✅ |
| Ready to upload | ✅ |

---

## 📚 Documentation

- [SETUP_CEPAT.md](SETUP_CEPAT.md) - Quick 5-minute setup
- [DEPLOY_GUIDE.md](DEPLOY_GUIDE.md) - Detailed deployment guide
- [README.md](README.md) - Full system documentation
- [README_SYSTEM.md](README_SYSTEM.md) - Architecture & flow

---

**🎉 PROJECT 100% SIAP UPLOAD!**

**Tidak perlu edit apapun, tinggal:**
1. Upload files
2. Run installer
3. Done!

Total waktu: ~5-10 menit dari upload sampai jalan! 🚀
