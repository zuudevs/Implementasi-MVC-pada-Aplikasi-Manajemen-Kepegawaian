# SIMKEP (Sistem Manajemen Kepegawaian)

Aplikasi web manajemen data kepegawaian dengan implementasi pola **MVC (Model-View-Controller) murni** menggunakan PHP 8.x native tanpa framework.

---

## 📋 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Penggunaan](#penggunaan)
- [Struktur Proyek](#struktur-proyek)
- [API Routes](#api-routes)
- [Troubleshooting](#troubleshooting)
- [Tim Pengembang](#tim-pengembang)

---

## Tentang Proyek

**SIMKEP** adalah aplikasi web untuk mengelola data pegawai dengan fungsionalitas CRUD lengkap (Create, Read, Update, Delete). Dibangun sebagai studi implementasi pola desain MVC tanpa menggunakan framework external, sehingga developer dapat memahami konsep fundamental MVC.

**Fitur Unggulan:**

- Clean Code dengan separation of concerns (Model, View, Controller)
- RESTful-like routing system
- Input validation & error handling
- Soft delete untuk data integrity
- Search & pagination
- Security best practices (prepared statements, XSS prevention)

---

## Fitur Utama

✨ **Employee Management**

- 📊 Daftar pegawai dengan statistik per status & departemen
- 🔍 Search & filter berdasarkan NIK, nama, jabatan, departemen
- 📄 Pagination untuk dataset besar
- ➕ Tambah pegawai baru dengan validasi lengkap
- ✏️ Edit data pegawai existing
- 🗑️ Soft delete (data terarsip, tidak dihapus permanent)
- 👁️ Detail view untuk satu pegawai

✔️ **Data Integrity**

- NIK unique validation
- Date validation (tanggal_masuk)
- Status enumeration (Aktif/Non-Aktif/Cuti)
- Input sanitization & XSS prevention
- Server-side validation before database

🔐 **Security**

- PDO prepared statements (SQL Injection prevention)
- Input sanitization dengan `htmlspecialchars()`
- CSRF-lite: form delete menggunakan POST
- Soft delete: data tidak benar-benar dihapus

---

## Tech Stack

| Layer         | Technology                       |
| ------------- | -------------------------------- |
| **Backend**   | PHP 8.0+                         |
| **Database**  | MySQL 5.7+                       |
| **Server**    | Apache 2.4+ (dengan mod_rewrite) |
| **Pattern**   | MVC (Custom Implementation)      |
| **ORM/Query** | PDO (PHP Data Objects)           |

---

## Requirements

### Sistem

- **PHP**: 8.0 atau lebih tinggi
- **MySQL**: 5.7 atau lebih tinggi
- **Apache**: 2.4+ dengan `mod_rewrite` enabled
- **OS**: Windows, macOS, atau Linux

### PHP Extensions

- `pdo_mysql` — untuk koneksi MySQL
- `session` — untuk session management
- `filter` — untuk input validation

### Tools

- `git` — version control
- `mysql` command-line client — untuk import database

---

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/zuudevs/Implementasi-MVC-pada-Aplikasi-Manajemen-Kepegawaian.git
cd Implementasi-MVC-pada-Aplikasi-Manajemen-Kepegawaian
```

### 2. Setup Database

```bash
# Masuk ke MySQL
mysql -u root -p

# Atau langsung import (jika tidak ada password root)
mysql -u root < database/kepegawaian.sql
```

File SQL akan otomatis create database `kepegawaian` dan tabel `pegawai` dengan data sample.

### 3. Configure Apache

#### Option A: Windows (XAMPP/WampServer)

1. Copy folder proyek ke `htdocs` atau `www`
2. Buka `httpd.conf` (biasanya di `Apache/conf/httpd.conf`)
3. Uncomment line:
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Cari blok `<Directory "path/to/htdocs">` dan ubah:
   ```apache
   AllowOverride All  # dari None
   ```
5. Restart Apache

#### Option B: Linux/macOS

```bash
# Enable mod_rewrite
sudo a2enmod rewrite

# Restart Apache
sudo systemctl restart apache2  # Linux
sudo apachectl restart          # macOS
```

### 4. Konfigurasi Aplikasi

Edit `app/config/config.php`:

```php
// Database config
define('DB_HOST', 'localhost');
define('DB_NAME', 'kepegawaian');
define('DB_USER', 'root');
define('DB_PASS', 'your_password'); // Sesuaikan

// App config
define('URLROOT', 'http://localhost/mvc-kepegawaian');
define('APPROOT', __DIR__ . '/..'); // Jangan ubah
```

---

## Konfigurasi

### File Konfigurasi Utama

**`app/config/config.php`**

```php
// Database Credentials
DB_HOST    = 'localhost'
DB_NAME    = 'kepegawaian'
DB_USER    = 'root'
DB_PASS    = ''

// App Settings
URLROOT              = 'http://localhost/mvc-kepegawaian'
DEFAULT_CONTROLLER   = 'Employee'
DEFAULT_METHOD       = 'index'
ROWS_PER_PAGE        = 10

// Flash messages
FLASH_SUCCESS = 'flash_success'
FLASH_ERROR   = 'flash_error'
```

### URL Rewriting

**`public/.htaccess`** mengatur routing:

```apache
RewriteEngine On
RewriteBase /mvc-kepegawaian/public/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

---

## Penggunaan

### Starting the Application

```bash
# Di Apache document root
http://localhost/mvc-kepegawaian
```

Akan redirect otomatis ke `/employee` (list pegawai)

### Workflow Umum

**1. Lihat Daftar Pegawai**

```
GET /employee
GET /employee?q=john&page=2  # dengan search & pagination
```

**2. Lihat Detail Pegawai**

```
GET /employee/show/5
```

**3. Tambah Pegawai**

```
GET  /employee/add          # tampil form
POST /employee/add          # submit form
```

**4. Edit Pegawai**

```
GET  /employee/edit/5       # tampil form dengan data lama
POST /employee/edit/5       # submit perubahan
```

**5. Hapus Pegawai**

```
POST /employee/delete/5     # soft delete
```

---

## Struktur Proyek

```
mvc-kepegawaian/
├── app/
│   ├── config/
│   │   └── config.php              # Konfigurasi app & database
│   ├── controllers/
│   │   └── Employee.php            # Handle HTTP requests
│   ├── core/
│   │   ├── App.php                 # Router (URL parsing & dispatch)
│   │   ├── Controller.php          # Base controller (helper)
│   │   ├── Database.php            # PDO wrapper
│   │   └── helpers.php             # Utility functions
│   ├── models/
│   │   └── Employee_model.php      # Data access layer
│   └── views/
│       ├── employee/
│       │   ├── index.php           # List view + search
│       │   ├── add.php             # Add form
│       │   ├── edit.php            # Edit form
│       │   └── show.php            # Detail view
│       └── templates/
│           ├── header.php          # Layout header
│           ├── footer.php          # Layout footer
│           └── 404.php             # Error page
├── database/
│   └── kepegawaian.sql             # Database schema + sample data
├── docs/
│   └── ARCHITECTURE.md             # Architecture & UML diagrams
├── public/
│   ├── .htaccess                   # URL rewriting rules
│   ├── index.php                   # Entry point
│   └── assets/                     # CSS, JS, images (optional)
└── README.md                       # Dokumentasi ini
```

---

## API Routes

### Pegawai Resource

| Method   | Route                        | Handler             | Deskripsi                     |
| -------- | ---------------------------- | ------------------- | ----------------------------- |
| **GET**  | `/employee`                  | `Employee@index()`  | List semua pegawai            |
| **GET**  | `/employee?q=keyword&page=N` | `Employee@index()`  | List dengan search & paginasi |
| **GET**  | `/employee/show/{id}`        | `Employee@show()`   | Detail 1 pegawai              |
| **GET**  | `/employee/add`              | `Employee@add()`    | Form tambah                   |
| **POST** | `/employee/add`              | `Employee@add()`    | Proses tambah                 |
| **GET**  | `/employee/edit/{id}`        | `Employee@edit()`   | Form edit                     |
| **POST** | `/employee/edit/{id}`        | `Employee@edit()`   | Proses edit                   |
| **POST** | `/employee/delete/{id}`      | `Employee@delete()` | Soft delete                   |

### Request Parameters

**Search & Pagination:**

```
GET /employee?q=john&page=2
  q      = search keyword (optional)
  page   = halaman (default: 1)
```

**Form Data (POST):**

```
POST /employee/add
{
  "nik": "12345678",
  "nama": "John Doe",
  "jabatan": "Manager",
  "departemen": "Sales",
  "gaji": "5000000",
  "tanggal_masuk": "2024-01-15",
  "status": "Aktif"
}
```

### Validation Rules

| Field           | Rule                           |
| --------------- | ------------------------------ |
| `nik`           | Required, 8-18 digits, unique  |
| `nama`          | Required, min 3 chars          |
| `jabatan`       | Required                       |
| `departemen`    | Required                       |
| `gaji`          | Required, > 0                  |
| `tanggal_masuk` | Required, format Y-m-d         |
| `status`        | One of: Aktif, Non-Aktif, Cuti |

---

## Troubleshooting

### ❌ Muncul "Index of /" saat akses project

**Root Cause:** Apache mencari `index.php` di root folder, tapi seharusnya di `public/`

**Solusi:**

1. Buat `.htaccess` di root folder dengan isi:
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ public/$1 [L]
   </IfModule>
   ```
2. Atau config Apache DocumentRoot langsung ke `public/` folder

### ❌ Fatal Error: "Cannot assign Employee to property App::$controller of type string"

**Root Cause:** PHP 8.0+ strict typing — tidak bisa assign object ke typed string property

**Solusi:**
Ubah di `app/core/App.php` baris 10:

```php
// Dari:
protected string $controller = DEFAULT_CONTROLLER;

// Menjadi:
protected mixed $controller = DEFAULT_CONTROLLER;
```

### ❌ Error "Unknown database" saat import MySQL

**Root Cause:** Database belum pernah di-create manual

**Solusi:**
File `kepegawaian.sql` sudah include `CREATE DATABASE`, jalankan:

```bash
mysql -u root < database/kepegawaian.sql
# Jangan kasih nama database di command!
```

### ❌ Error "404 Not Found" saat akses `/employee/add`

**Root Cause:** `mod_rewrite` tidak enabled atau `AllowOverride` masih `None`

**Solusi:**

1. Edit `httpd.conf`:

   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so  # uncomment ini

   # Cari section <Directory "path/to/htdocs">
   <Directory "D:/program/Apache/Apache24/htdocs">
       AllowOverride All    # ubah dari None
   </Directory>
   ```

2. Restart Apache
3. Test: akses `http://localhost/mvc-kepegawaian/employee`

### ❌ Database connection error

**Konfigurasi DB tidak match dengan actual database**

**Solusi:**

1. Verifikasi MySQL user & password di `app/config/config.php`
2. Pastikan database `kepegawaian` sudah ada:
   ```bash
   mysql -u root -p
   SHOW DATABASES;
   ```
3. Jika belum, import ulang:
   ```bash
   mysql -u root < database/kepegawaian.sql
   ```

---

## Dokumentasi Teknis

Untuk detail lengkap tentang arsitektur, UML diagrams (use case, class, sequence), dan flowcharts, lihat:

📖 **[docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)**

---

## Tim Pengembang

**Kelompok 14 — Implementasi MVC pada Aplikasi Manajemen Kepegawaian**

| No  | Nama                         | NIM       | Peran                 |
| --- | ---------------------------- | --------- | --------------------- |
| 1   | Al Nizar Baihaqi             | 202331181 | Arsitektur & Core MVC |
| 2   | Aulia Ramadhana              | 202331061 | Model & Database      |
| 3   | Rafi Indra Pramudhito Zuhayr | 202331291 | Views & Frontend      |

---

## License

Proyek ini dibuat untuk keperluan pendidikan. Tidak ada lisensi komersial.

---

## Catatan

- Aplikasi ini adalah **pembelajaran implementasi MVC**, bukan production-ready system
- Untuk production, pertimbangkan menggunakan framework seperti Laravel, Symfony, atau Slim
- Semua best practices security sudah diterapkan (prepared statements, input validation, XSS prevention)
- Database soft delete memastikan data integrity & audit trail

---

**Last Updated:** 2026-05-20  
**PHP Version:** 8.0+  
**MySQL Version:** 5.7+  
**Apache:** 2.4+ with mod_rewrite
