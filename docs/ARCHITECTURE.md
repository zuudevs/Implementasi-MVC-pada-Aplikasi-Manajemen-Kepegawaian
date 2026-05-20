# Arsitektur Aplikasi SIMKEP

## Pola Desain: MVC (Model–View–Controller)

Aplikasi ini mengimplementasikan pola MVC secara **murni tanpa framework**, menggunakan PHP 8.x native dengan PDO untuk akses database.

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

## Database

### Tabel: `pegawai`

| Kolom           | Tipe               | Keterangan               |
| --------------- | ------------------ | ------------------------ |
| `id`            | INT UNSIGNED PK    | Auto increment           |
| `nik`           | VARCHAR(18) UNIQUE | Nomor Induk Karyawan     |
| `nama`          | VARCHAR(100)       | Nama lengkap             |
| `jabatan`       | VARCHAR(100)       | Posisi/jabatan           |
| `departemen`    | VARCHAR(50)        | Unit kerja               |
| `gaji`          | DECIMAL(15,0)      | Gaji bulanan             |
| `tanggal_masuk` | DATE               | Tanggal bergabung        |
| `status`        | ENUM               | Aktif / Non-Aktif / Cuti |
| `created_at`    | DATETIME           | Timestamp buat           |
| `updated_at`    | DATETIME           | Timestamp ubah           |
| `deleted_at`    | DATETIME           | Soft delete              |

---

## Fitur Keamanan

- **Prepared Statements** — Semua query menggunakan PDO prepared statements, mencegah SQL Injection
- **XSS Prevention** — Output di-escape dengan `htmlspecialchars()`
- **Input Sanitization** — Semua input POST di-sanitasi sebelum diproses
- **CSRF-lite** — Form delete menggunakan method POST
- **Soft Delete** — Data tidak benar-benar dihapus, hanya ditandai `deleted_at`
- **Input Validation** — Server-side validation di controller sebelum ke model

---

## UML Diagrams

### 1. Use Case Diagram

```mermaid
graph TB
    subgraph SIMKEP ["Sistem Manajemen Kepegawaian (SIMKEP)"]
        direction LR
        UC1["View Employee List<br/>(with search & pagination)"]
        UC2["View Employee Detail"]
        UC3["Add New Employee"]
        UC4["Edit Employee Data"]
        UC5["Delete Employee"]
        UC6["View Statistics<br/>(by status & dept)"]
    end

    Admin["👤 Admin/User"]

    Admin -->|uses| UC1
    Admin -->|uses| UC2
    Admin -->|uses| UC3
    Admin -->|uses| UC4
    Admin -->|uses| UC5
    Admin -->|views| UC6

    UC1 -.->|includes| UC6
    UC3 -->|triggered by| UC6
    UC4 -->|triggered by| UC6
    UC5 -->|triggered by| UC6
```

### 2. Class Diagram

```mermaid
classDiagram
    direction TB

    class App {
        #string controller
        #string method
        #array params
        __construct()
        -parseUrl() array
        -notFound() void
    }

    class Controller {
        +model(string) object
        +view(string, array) void
    }

    class Database {
        -string host
        -string dbname
        -string username
        -string password
        -PDO conn
        -mixed stmt
        +connect() PDO
        +query(string) Database
        +bind(string, mixed) Database
        +execute() bool
        +resultSet() array
        +single() mixed
        +rowCount() int
        +lastInsertId() string|false
    }

    class Employee {
        -object employeeModel
        +__construct()
        +index() void
        +show(int) void
        +add() void
        +edit(int) void
        +delete(int) void
        -sanitizeInput(array) array
        -validate(array) array
        -emptyInput() array
        -setFlash(string, string) void
    }

    class Employee_model {
        -Database db
        +__construct()
        +getAll(string, int, int) array
        +countAll(string) int
        +getById(int) object|false
        +nikExists(string, int) bool
        +create(array) bool
        +update(int, array) bool
        +delete(int) bool
        +countByStatus() array
        +countByDepartemen() array
        -bindEmployeeData(array) void
    }

    class Pegawai {
        +int id
        +string nik
        +string nama
        +string jabatan
        +string departemen
        +decimal gaji
        +date tanggal_masuk
        +enum status
        +datetime created_at
        +datetime updated_at
        +datetime deleted_at
    }

    App --> Controller: extends
    Employee --> Controller: extends
    Employee --> Employee_model: uses
    Employee_model --> Database: uses
    Employee_model --> Pegawai: accesses
    Controller --> Database: (via model)
```

### 3. Activity Flowchart

#### 3.1 List & Search Employee Flow

```mermaid
flowchart TD
    Start([User Access /employee]) --> GetRequest{GET Request?}
    GetRequest -->|Yes| GetParams["Get keyword, page<br/>from query string"]
    GetParams --> ValidatePage["Validate page number<br/>min=1"]
    ValidatePage --> CalcPagination["Calculate offset<br/>offset = (page-1) * limit"]
    CalcPagination --> QueryCount["Query: COUNT employees<br/>with keyword filter"]
    QueryCount --> GetEmployees["Query: SELECT employees<br/>with LIMIT + OFFSET"]
    GetEmployees --> GetStats["Query: Statistics<br/>by status & department"]
    GetStats --> RenderView["Render header.php<br/>+ index.php + footer.php"]
    RenderView --> End([Display to Browser])

    style Start fill:#90EE90
    style End fill:#FFB6C6
```

#### 3.2 Add/Edit Employee Flow

```mermaid
flowchart TD
    Start([User Access Form]) --> IsPost{POST Request?}

    IsPost -->|No| ShowForm["Render blank form<br/>(for add)<br/>or pre-filled<br/>(for edit)"]
    ShowForm --> End([Display to Browser])

    IsPost -->|Yes| Sanitize["Sanitize input<br/>using htmlspecialchars()<br/>trim(), etc"]
    Sanitize --> Validate["Run validation:<br/>NIK, nama, jabatan,<br/>departemen, gaji,<br/>tanggal_masuk, status"]
    Validate --> HasErrors{Errors?}

    HasErrors -->|Yes| SetError["Set error messages<br/>in data array"]
    SetError --> ShowFormWithError["Re-render form<br/>with input + errors"]
    ShowFormWithError --> End

    HasErrors -->|No| CheckNIK{For Add:<br/>Check NIK<br/>Duplicate?}
    CheckNIK -->|Duplicate| SetNIKError["Set NIK error"]
    SetNIKError --> ShowFormWithError

    CheckNIK -->|Unique| ExecuteDB["Execute INSERT or UPDATE<br/>to database"]
    ExecuteDB --> DBSuccess{DB Operation<br/>Success?}

    DBSuccess -->|Yes| SetFlash["Set success flash<br/>message in session"]
    SetFlash --> Redirect["Redirect to /employee"]
    Redirect --> End

    DBSuccess -->|No| SetDBError["Set database error<br/>flash message"]
    SetDBError --> Redirect

    style Start fill:#90EE90
    style End fill:#FFB6C6
    style HasErrors fill:#FFE4B5
    style CheckNIK fill:#FFE4B5
```

#### 3.3 Delete Employee Flow

```mermaid
flowchart TD
    Start([User Click Delete]) --> IsPost{POST Request?}
    IsPost -->|No| Redirect1["Redirect to /employee"]
    Redirect1 --> End([End])

    IsPost -->|Yes| GetID["Get employee ID<br/>from URL param"]
    GetID --> QueryEmp["Query employee<br/>by ID"]
    QueryEmp --> EmpExists{Employee<br/>Found?}

    EmpExists -->|No| SetError["Set error flash:<br/>Data not found"]
    SetError --> Redirect2["Redirect to /employee"]

    EmpExists -->|Yes| SoftDelete["Soft Delete:<br/>UPDATE pegawai<br/>SET deleted_at = NOW()"]
    SoftDelete --> DelSuccess{Update<br/>Success?}

    DelSuccess -->|Yes| SetSuccess["Set success flash:<br/>Employee deleted"]
    DelSuccess -->|No| SetDelError["Set error flash:<br/>Delete failed"]

    SetSuccess --> Redirect2
    SetDelError --> Redirect2
    Redirect2 --> End

    style Start fill:#90EE90
    style End fill:#FFB6C6
    style EmpExists fill:#FFE4B5
    style DelSuccess fill:#FFE4B5
```

### 4. Sequence Diagram

#### 4.1 Sequence: View Employee List

```mermaid
sequenceDiagram
    participant Browser as 🌐 Browser
    participant Apache as 🖥️ Apache
    participant Index as 📄 public/index.php
    participant App as 🔀 App (Router)
    participant Controller as 📋 Employee Controller
    participant Model as 💾 Employee_model
    participant DB as 🗄️ Database
    participant MySQL as 🔲 MySQL
    participant View as 👁️ View (Blade)

    Browser->>Apache: GET /employee?q=john&page=1
    Apache->>Index: URL Rewrite to ?url=employee
    Index->>App: new App()
    App->>App: parseUrl() → [employee]
    App->>App: Resolve controller: Employee
    App->>Controller: new Employee()
    Controller->>Model: __construct()
    Model->>DB: new Database()

    Controller->>Model: index() with keyword, page
    Model->>DB: query("SELECT COUNT...WHERE deleted_at IS NULL")
    DB->>MySQL: Execute prepared statement
    MySQL-->>DB: Result count
    DB-->>Model: Single row count

    Model->>DB: query("SELECT * FROM pegawai...LIMIT/OFFSET")
    DB->>MySQL: Execute with keyword filter
    MySQL-->>DB: Rows array
    DB-->>Model: Array of objects

    Model->>DB: query("SELECT status, COUNT...")
    DB->>MySQL: Execute aggregate
    MySQL-->>DB: Status statistics
    DB-->>Model: Counts by status

    Model->>DB: query("SELECT departemen, COUNT...")
    DB->>MySQL: Execute aggregate
    MySQL-->>DB: Department statistics
    DB-->>Model: Counts by department

    Model-->>Controller: employees[], totalRows, stats
    Controller->>View: view('templates/header', data)
    View-->>Browser: Render header HTML
    Controller->>View: view('employee/index', data)
    View-->>Browser: Render table with pagination
    Controller->>View: view('templates/footer', data)
    View-->>Browser: Render footer HTML

    Browser->>Browser: Display complete page
```

#### 4.2 Sequence: Add New Employee

```mermaid
sequenceDiagram
    participant Browser as 🌐 Browser
    participant Controller as 📋 Employee Controller
    participant Model as 💾 Employee_model
    participant DB as 🗄️ Database
    participant MySQL as 🔲 MySQL
    participant Session as 💾 $_SESSION

    Browser->>Controller: POST /employee/add
    activate Controller

    Controller->>Controller: sanitizeInput($_POST)
    Controller->>Controller: validate(input)

    alt Validation Errors
        Controller->>Controller: setFlash(ERROR, msg)
        Controller->>Browser: Re-render form with errors
    else Valid Input
        Controller->>Model: nikExists(nik)
        Model->>DB: query("SELECT id FROM pegawai WHERE nik=? AND deleted_at IS NULL")
        DB->>MySQL: Execute
        MySQL-->>DB: rowCount
        DB-->>Model: bool exists
        Model-->>Controller: nikExists result

        alt NIK Already Exists
            Controller->>Controller: Set error for NIK
            Controller->>Browser: Re-render form
        else NIK Unique
            Controller->>Model: create(data)
            Model->>DB: query("INSERT INTO pegawai VALUES...")
            Model->>DB: bind all fields
            DB->>MySQL: Execute INSERT
            MySQL-->>DB: lastInsertId
            DB-->>Model: bool success
            Model-->>Controller: bool result

            Controller->>Session: $_SESSION[FLASH_SUCCESS] = 'Added'
            Controller->>Browser: Redirect /employee
        end
    end

    deactivate Controller
```

#### 4.3 Sequence: Edit Employee

```mermaid
sequenceDiagram
    participant Browser as 🌐 Browser
    participant Controller as 📋 Employee Controller
    participant Model as 💾 Employee_model
    participant DB as 🗄️ Database
    participant MySQL as 🔲 MySQL
    participant View as 👁️ View

    Browser->>Controller: GET /employee/edit/5
    Controller->>Model: getById(5)
    Model->>DB: query("SELECT * FROM pegawai WHERE id=? AND deleted_at IS NULL")
    DB->>MySQL: Execute
    MySQL-->>DB: Employee object
    DB-->>Model: stdObject
    Model-->>Controller: Employee data

    Controller->>View: view with pre-filled form
    View-->>Browser: Display form with current data

    Browser->>Controller: POST /employee/edit/5 with updated data
    Controller->>Controller: sanitizeInput($_POST)
    Controller->>Controller: validate(input)

    alt Validation Errors
        Controller->>View: Re-render with errors
    else Valid
        Controller->>Model: nikExists(nik, excludeId=5)
        Model->>DB: query("SELECT id WHERE nik=? AND id!=5")
        DB->>MySQL: Execute
        MySQL-->>DB: rowCount (0 if unique)

        alt NIK Conflict
            Controller->>View: Re-render with error
        else NIK OK
            Controller->>Model: update(5, data)
            Model->>DB: query("UPDATE pegawai SET ... WHERE id=5")
            DB->>MySQL: Execute UPDATE
            MySQL-->>DB: rowCount
            DB-->>Model: bool success
            Model-->>Controller: result

            Controller->>Browser: Redirect /employee
        end
    end
```

#### 4.4 Sequence: Delete Employee

```mermaid
sequenceDiagram
    participant Browser as 🌐 Browser
    participant Controller as 📋 Employee Controller
    participant Model as 💾 Employee_model
    participant DB as 🗄️ Database
    participant MySQL as 🔲 MySQL

    Browser->>Controller: POST /employee/delete/5
    Controller->>Model: getById(5)
    Model->>DB: SELECT * FROM pegawai WHERE id=5
    DB->>MySQL: Execute
    MySQL-->>DB: Employee object / false

    alt Employee Not Found
        Controller->>Browser: Flash error + redirect
    else Employee Found
        Controller->>Model: delete(5)
        activate Model
        Model->>DB: query("UPDATE pegawai SET deleted_at=NOW() WHERE id=5")
        Model->>DB: bind(:id, 5)
        DB->>MySQL: Execute UPDATE (soft delete)
        MySQL-->>DB: rowCount
        DB-->>Model: bool success
        deactivate Model

        alt Delete Success
            Controller->>Browser: Flash success + redirect
        else Delete Failed
            Controller->>Browser: Flash error + redirect
        end
    end
```

---

## Tim Pengembang

| Nama                         | NIM       | Peran                 |
| ---------------------------- | --------- | --------------------- |
| Al Nizar Baihaqi             | 202331181 | Arsitektur & Core MVC |
| Aulia Ramadhana              | 202331061 | Model & Database      |
| Rafi Indra Pramudhito Zuhayr | 202331291 | Views & Frontend      |

_Kelompok 14 — Implementasi MVC pada Aplikasi Manajemen Kepegawaian_
