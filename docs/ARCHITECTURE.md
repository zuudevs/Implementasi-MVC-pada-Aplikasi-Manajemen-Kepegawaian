# Arsitektur Aplikasi SIMKEP

## Pola Desain: MVC (Model–View–Controller)

Aplikasi ini mengimplementasikan pola MVC secara **murni tanpa framework**, menggunakan PHP 8.x native dengan PDO untuk akses database.

---

## Diagram Arsitektur

```
HTTP Request
     │
     ▼
public/.htaccess  ─── URL Rewriting ──►  public/index.php
                                               │
                                               ▼
                                        app/core/App.php  (Router)
                                               │
                          ┌────────────────────┼──────────────────────┐
                          │                    │                       │
                          ▼                    ▼                       ▼
                  app/controllers/    app/models/             app/views/
                  Employee.php        Employee_model.php      employee/
                                                              templates/
                                               │
                                               ▼
                                      app/core/Database.php
                                               │
                                               ▼
                                           MySQL DB
```

---

## Struktur Direktori

```
mvc-kepegawaian/
├── app/
│   ├── config/
│   │   └── config.php           # Konstanta, konfigurasi DB & app
│   ├── controllers/
│   │   └── Employee.php         # Controller utama CRUD pegawai
│   ├── core/
│   │   ├── App.php              # Router / Front Controller
│   │   ├── Controller.php       # Base Controller (model + view loader)
│   │   ├── Database.php         # PDO wrapper
│   │   └── helpers.php          # Fungsi utilitas global
│   ├── models/
│   │   └── Employee_model.php   # Model akses data pegawai
│   └── views/
│       ├── employee/
│       │   ├── index.php        # Daftar pegawai + statistik
│       │   ├── add.php          # Form tambah pegawai
│       │   ├── edit.php         # Form edit pegawai
│       │   └── show.php         # Detail pegawai
│       └── templates/
│           ├── header.php       # Layout atas + sidebar + CSS
│           ├── footer.php       # Layout bawah + JS
│           └── 404.php          # Halaman not found
├── database/
│   └── kepegawaian.sql          # Skema + data awal
├── docs/
│   └── ARCHITECTURE.md          # Dokumen ini
├── public/
│   ├── .htaccess                # URL rewriting Apache
│   └── index.php                # Entry point aplikasi
└── README.md
```

---

## Alur Request (Request Lifecycle)

```
1. Browser  ──GET /employee/edit/5──►  Apache
2. Apache   ── mod_rewrite ──────────► public/index.php?url=employee/edit/5
3. index.php── bootstrap ────────────► App::__construct()
4. App      ── parse URL ─────────────► controller=Employee, method=edit, param=[5]
5. App      ── require + new ─────────► Employee controller
6. Employee ── $this->model() ────────► Employee_model::getById(5)
7. Model    ── PDO query ─────────────► MySQL → return stdObject
8. Employee ── $this->view() ─────────► header.php + edit.php + footer.php
9. PHP      ── render HTML ───────────► HTTP 200 response
```

---

## Komponen Utama

### `App.php` — Router
- Mem-parse `$_GET['url']` menjadi segmen: `[controller, method, ...params]`
- Me-require file controller yang sesuai dan memanggil method-nya
- Menangani 404 jika controller/method tidak ditemukan

### `Controller.php` — Base Controller
| Method | Fungsi |
|--------|--------|
| `model(string $name)` | Require dan instantiate model |
| `view(string $path, array $data)` | Require view, extract data sebagai variabel lokal |

### `Database.php` — PDO Wrapper
| Method | Fungsi |
|--------|--------|
| `connect()` | Lazy-init PDO singleton |
| `query(string $sql)` | Prepare statement |
| `bind(string $param, mixed $val)` | Bind parameter dengan type inference |
| `execute()` | Jalankan statement |
| `resultSet()` | Fetch semua baris sebagai array of stdObject |
| `single()` | Fetch satu baris |
| `rowCount()` | Jumlah baris terpengaruh |
| `lastInsertId()` | ID terakhir yang diinsert |

### `Employee_model.php` — Data Layer
| Method | Fungsi |
|--------|--------|
| `getAll($keyword, $limit, $offset)` | List pegawai + search + pagination |
| `countAll($keyword)` | Total baris untuk paginasi |
| `getById(int $id)` | Satu pegawai by PK |
| `nikExists(string $nik, int $excludeId)` | Cek duplikasi NIK |
| `create(array $data)` | INSERT pegawai baru |
| `update(int $id, array $data)` | UPDATE pegawai |
| `delete(int $id)` | Soft delete (set `deleted_at`) |
| `countByStatus()` | Statistik per status |
| `countByDepartemen()` | Statistik per departemen |

### `Employee.php` — Controller
| Method | HTTP | URL |
|--------|------|-----|
| `index()` | GET | `/employee` |
| `show(id)` | GET | `/employee/show/{id}` |
| `add()` | GET/POST | `/employee/add` |
| `edit(id)` | GET/POST | `/employee/edit/{id}` |
| `delete(id)` | POST | `/employee/delete/{id}` |

---

## Database

### Tabel: `pegawai`
| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | INT UNSIGNED PK | Auto increment |
| `nik` | VARCHAR(18) UNIQUE | Nomor Induk Karyawan |
| `nama` | VARCHAR(100) | Nama lengkap |
| `jabatan` | VARCHAR(100) | Posisi/jabatan |
| `departemen` | VARCHAR(50) | Unit kerja |
| `gaji` | DECIMAL(15,0) | Gaji bulanan |
| `tanggal_masuk` | DATE | Tanggal bergabung |
| `status` | ENUM | Aktif / Non-Aktif / Cuti |
| `created_at` | DATETIME | Timestamp buat |
| `updated_at` | DATETIME | Timestamp ubah |
| `deleted_at` | DATETIME | Soft delete |

---

## Fitur Keamanan

- **Prepared Statements** — Semua query menggunakan PDO prepared statements, mencegah SQL Injection
- **XSS Prevention** — Output di-escape dengan `htmlspecialchars()`
- **Input Sanitization** — Semua input POST di-sanitasi sebelum diproses
- **CSRF-lite** — Form delete menggunakan method POST
- **Soft Delete** — Data tidak benar-benar dihapus, hanya ditandai `deleted_at`
- **Input Validation** — Server-side validation di controller sebelum ke model

---

## Tim Pengembang

| Nama | NIM | Peran |
|------|-----|-------|
| Al Nizar Baihaqi | 202331181 | Arsitektur & Core MVC |
| Aulia Ramadhana | 202331061 | Model & Database |
| Rafi Indra Pramudhito Zuhayr | 202331291 | Views & Frontend |

*Kelompok 14 — Implementasi MVC pada Aplikasi Manajemen Kepegawaian*
