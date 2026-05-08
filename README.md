<p align="center">
  <img src="public/icons/KasirKita.svg" width="120" alt="KasirKita Logo">
</p>

<h1 align="center">KasirKita - Modern POS System</h1>

<p align="center">
  <strong>Sistem Point of Sale (POS) modern berbasis web dengan fitur lengkap untuk mengelola kasir, inventory, dan laporan penjualan secara realtime.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue.js">
  <img src="https://img.shields.io/badge/Inertia.js-2.x-9553E9?style=for-the-badge&logo=inertia&logoColor=white" alt="Inertia.js">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="TailwindCSS">
</p>

---

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [User Roles](#-user-roles)
- [Fitur Detail](#-fitur-detail)
- [Screenshot](#-screenshot)
- [Troubleshooting](#-troubleshooting)
- [License](#-license)

---

## ✨ Fitur Utama

### 🎯 **3 Role Pengguna**
- **Owner** - Dashboard analytics, laporan lengkap, audit trail
- **Inventory** - Manajemen produk, kategori, dan stok
- **Kasir** - Point of Sale dengan product grid & barcode scanner

### 🚀 **Fitur Unggulan**
- ✅ **Realtime Dashboard** - Data update otomatis tanpa refresh (polling 5 detik)
- ✅ **Product Grid POS** - Seperti coffee shop, klik langsung tanpa scan
- ✅ **Barcode Scanner Support** - Auto-add produk saat scan barcode
- ✅ **Multi Payment Method** - Cash, QRIS, Midtrans Payment Gateway
- ✅ **Void Transaction** - Batalkan transaksi dengan audit trail lengkap
- ✅ **Export CSV** - Export laporan penjualan ke CSV/Excel
- ✅ **Low Stock Alert** - Notifikasi stok menipis realtime
- ✅ **Stock Management** - Stock In, Stock Out, Adjustment dengan log lengkap
- ✅ **Thermal Printer Ready** - Struk 80mm siap cetak
- ✅ **Responsive Design** - Mobile-friendly interface

---

## 🛠 Tech Stack

### Backend
- **Laravel 13.x** - PHP Framework
- **Inertia.js 2.x** - Modern monolith architecture
- **Spatie Permission** - Role & permission management
- **Midtrans PHP** - Payment gateway integration
- **SQLite/MySQL** - Database

### Frontend
- **Vue.js 3.x** - Progressive JavaScript framework
- **TailwindCSS 3.x** - Utility-first CSS framework
- **Heroicons** - Beautiful hand-crafted SVG icons
- **Vite** - Next generation frontend tooling

---

## 📦 Persyaratan Sistem

### Minimum Requirements
- **PHP** >= 8.3
- **Composer** >= 2.x
- **Node.js** >= 18.x
- **NPM** >= 9.x
- **Database** SQLite (default) atau MySQL/PostgreSQL

### Recommended
- **PHP** 8.3+
- **Composer** 2.7+
- **Node.js** 20.x LTS
- **NPM** 10.x
- **RAM** 2GB minimum
- **Storage** 500MB free space

---

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/M4SToriqq/pos-system.git
cd pos-system
```

### 2. Install Dependencies

#### Install PHP Dependencies
```bash
composer install
```

#### Install JavaScript Dependencies
```bash
npm install
```

### 3. Setup Environment
```bash
# Copy file .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Setup Database

#### Opsi A: SQLite (Default - Recommended untuk Development)
```bash
# Buat file database SQLite
touch database/database.sqlite

# Update .env
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite (opsional)
```

#### Opsi B: MySQL
```bash
# Update .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_system
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi & Seeder
```bash
# Jalankan migrasi database
php artisan migrate

# Jalankan seeder (data dummy)
php artisan db:seed
```

### 6. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

---

## ⚙️ Konfigurasi

### Konfigurasi Midtrans (Opsional)
Jika ingin menggunakan payment gateway Midtrans:

1. Daftar di [Midtrans](https://midtrans.com)
2. Dapatkan Server Key dan Client Key
3. Update `.env`:
```env
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false
```

4. Tambahkan Midtrans Snap script di `resources/views/app.blade.php`:
```html
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
```

### Konfigurasi Email (Opsional)
Untuk fitur reset password:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🎮 Menjalankan Aplikasi

### Development Mode

#### Opsi 1: Manual (2 Terminal)
```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite Dev Server
npm run dev
```

#### Opsi 2: Concurrently (1 Terminal)
```bash
composer run dev
```

Aplikasi akan berjalan di: **http://localhost:8000**

### Production Mode
```bash
# Build assets
npm run build

# Jalankan dengan web server (Apache/Nginx)
# atau
php artisan serve --host=0.0.0.0 --port=8000
```

---

## 👥 User Roles

### Default Users (Setelah Seeder)

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| **Owner** | owner@kasirkita.com | Password | Dashboard, Reports, Audit Trail |
| **Inventory** | inventory@kasirkita.com | Password | Products, Categories, Stock Management |
| **Kasir** | kasir@kasirkita.com | Password | POS, Transactions |

> ⚠️ **PENTING**: Ganti password default setelah login pertama kali!

---

## 📱 Fitur Detail

### 🏪 Owner Dashboard
- **Analytics Realtime**
  - Total penjualan, transaksi, profit
  - Penjualan hari ini
  - Item terjual
  - Stok menipis alert
- **Laporan Lengkap**
  - Sales Report (per periode)
  - Product Report (best seller, profit per produk)
  - Cashier Report (performa kasir)
  - Audit Trail (void transactions, stock adjustments)
- **Export Data**
  - Export laporan ke CSV
  - Compatible dengan Excel/Google Sheets

### 📦 Inventory Management
- **Product Management**
  - CRUD produk dengan kategori
  - SKU & Barcode support
  - Harga beli & jual
  - Min stock alert
  - Soft delete
- **Stock Management**
  - Stock In (pembelian)
  - Stock Out (adjustment)
  - Stock Log (history lengkap)
  - Low Stock Alert realtime
- **Category Management**
  - CRUD kategori
  - Product count per kategori

### 💰 Point of Sale (Kasir)
- **Product Grid**
  - Tampilan visual seperti coffee shop
  - Filter by kategori
  - Klik langsung untuk add to cart
  - Stock indicator
- **Barcode Scanner**
  - Auto-detect barcode input
  - Auto-add produk ke cart
  - Support USB/Bluetooth scanner
- **Payment Methods**
  - Cash (dengan kembalian otomatis)
  - QRIS
  - Midtrans (Credit Card, E-Wallet, dll)
- **Receipt**
  - Thermal printer ready (80mm)
  - Print preview
  - Auto-format untuk struk
- **Transaction Management**
  - Riwayat transaksi
  - Void transaction (dengan alasan)
  - Filter & pagination

### 🔒 Security Features
- **Authentication**
  - Laravel Breeze
  - Email verification
  - Password reset
- **Authorization**
  - Role-based access control (Spatie Permission)
  - Route protection
  - Middleware validation
- **Audit Trail**
  - Log semua void transactions
  - Log stock adjustments
  - User activity tracking

---

## 🐛 Troubleshooting

### Error: "Class not found"
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Error: "Vite manifest not found"
```bash
npm run build
```

### Error: "Database locked" (SQLite)
```bash
# Tutup semua koneksi database
php artisan cache:clear
php artisan config:clear
```

### Error: "Permission denied"
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows
# Pastikan folder storage dan bootstrap/cache memiliki write permission
```

### Midtrans Payment Tidak Muncul
1. Pastikan Midtrans credentials sudah benar di `.env`
2. Pastikan Snap script sudah ditambahkan di `app.blade.php`
3. Clear cache: `php artisan config:clear`

---

## 📚 Dokumentasi Tambahan

### Database Schema
Lihat file migrations di `database/migrations/` untuk struktur database lengkap.

### API Routes
```bash
php artisan route:list
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=TransactionTest
```

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Developer

Developed with ❤️ by **Toriq Habil Fadhila**

- GitHub: [@M4SToriqq](https://github.com/M4SToriqq)
- Email: toriqqhabilfadhila21@gmail.com

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Vue.js](https://vuejs.org) - The Progressive JavaScript Framework
- [Inertia.js](https://inertiajs.com) - The Modern Monolith
- [TailwindCSS](https://tailwindcss.com) - A utility-first CSS framework
- [Heroicons](https://heroicons.com) - Beautiful hand-crafted SVG icons
- [Midtrans](https://midtrans.com) - Payment Gateway Indonesia

---

<p align="center">Made with ❤️ for Indonesian SMEs</p>
