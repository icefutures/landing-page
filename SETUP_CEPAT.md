# 🚀 SETUP CEPAT - 5 Menit!

## ✅ Upload Files

### 1. Upload tradecenter.idnads.pro
Upload **semua file** dari folder `tradecenter.idnads.pro/` ke hosting:
```
/home/idnafevn/tradecenter.idnads.pro/
```

### 2. Upload fx.idnads.pro  
Upload **semua file** dari folder `fx.idnads.pro/` ke hosting:
```
/home/idnafevn/fx.idnads.pro/
```

---

## 🔧 Auto Setup dengan Installer

### Step 1: Setup tradecenter (2 menit)
1. Buka browser: `https://tradecenter.idnads.pro/installer.php`
2. Klik **"Generate Shared Secret & Setup"**
3. Klik **"Download secret.txt"**
4. Save file secret.txt

### Step 2: Setup fx (2 menit)
1. Upload file `secret.txt` ke `/home/idnafevn/fx.idnads.pro/`
2. Buka browser: `https://fx.idnads.pro/installer.php`  
3. Klik **"Use Shared Secret dari File"**
4. Klik **"Selesai Setup"**

### Step 3: Testing (1 menit)
Test URL ini:
```
https://tradecenter.idnads.pro/go/invest?utm_source=test
```

**Expected:**
- Redirect ke fx.idnads.pro dengan token
- Redirect lagi tanpa token
- Landing page tampil ✓

### Step 4: Cleanup
**WAJIB** - Hapus file installer:
- Hapus `installer.php` di tradecenter
- Hapus `installer.php` di fx
- Hapus `secret.txt` di fx

---

## 🎯 URL untuk Iklan

Gunakan URL ini di platform iklan:
```
https://tradecenter.idnads.pro/go/invest
```

**Done! Sistem siap dipakai! 🎉**

---

## ⚠️ Troubleshooting

**Problem: Installer tidak bisa diakses (403/404)**
- Cek struktur folder: file harus langsung di root, bukan dalam subfolder

**Problem: Token invalid**  
- Pastikan secret.txt di-upload ke fx sebelum run installer fx

**Problem: Landing page tidak muncul**
- Cek folder `data/` exists di fx dengan permission 755

**Problem: CSS tidak load**
- Pastikan folder css/, js/, images/ ter-upload di fx

---

## 📞 Custom WhatsApp Number

Setelah setup selesai, edit file:
```
/home/idnafevn/fx.idnads.pro/invest/landing-page.php
```

Cari dan ganti:
```html
https://wa.me/6281234567890
```

Dengan nomor WhatsApp Anda (format: 62xxx tanpa +)

---

**Total waktu: ~5 menit**
**File yang perlu edit: 0 (sudah auto-setup)**
**Tinggal upload & run installer!** 🚀
