# 🚀 Sistem Link Iklan Permanen - Ready to Upload!

## 📦 Upload ke Hosting

### 1. Upload fx.idnads.pro
Upload **semua isi** folder `fx.idnads.pro/` ke:
```
/home/idnafevn/fx.idnads.pro/
```

### 2. Upload tradecenter.idnads.pro  
Upload **semua isi** folder `tradecenter.idnads.pro/` ke:
```
/home/idnafevn/tradecenter.idnads.pro/
```

---

## ⚙️ Setup Otomatis (3 Menit)

### Step 1: Setup tradecenter
1. Buka: `https://tradecenter.idnads.pro/installer.php`
2. Klik: **"Generate Shared Secret & Setup"**
3. Klik: **"Download secret.txt"**

### Step 2: Setup fx
1. Upload file `secret.txt` ke folder `/home/idnafevn/fx.idnads.pro/`
2. Buka: `https://fx.idnads.pro/installer.php`
3. Klik: **"Use Shared Secret dari File"**
4. Klik: **"Selesai Setup"**

### Step 3: Test
```
https://tradecenter.idnads.pro/go/invest?utm_source=test
```
Harus redirect ke fx → Landing page tampil ✓

### Step 4: Hapus Installer
- Hapus `installer.php` di tradecenter
- Hapus `installer.php` di fx
- Hapus `secret.txt` di fx

---

## 🎯 URL untuk Iklan

Gunakan URL ini di platform iklan:
```
https://tradecenter.idnads.pro/go/invest
```

---

## ⚙️ Custom WhatsApp (Opsional)

Edit file: `/home/idnafevn/fx.idnads.pro/invest/landing-page.php`

Cari & ganti:
```
https://wa.me/6281234567890
```
Dengan nomor Anda (format: 62xxx)

---

**Done! Sistem siap dipakai! 🎉**
