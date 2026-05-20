<?php

/**
 * Employee.php — Employee Controller
 * 
 * Handles all HTTP actions for the Employee resource:
 *   GET  /employee            → index()   – list + search + pagination
 *   GET  /employee/show/{id}  → show()    – detail
 *   GET  /employee/add        → add()     – blank form
 *   POST /employee/add        → add()     – process create
 *   GET  /employee/edit/{id}  → edit()    – pre-filled form
 *   POST /employee/edit/{id}  → edit()    – process update
 *   POST /employee/delete/{id}→ delete()  – soft delete
 */
class Employee extends Controller {

    private object $employeeModel;

    public function __construct() {
        // Ensure session is started once
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->employeeModel = $this->model('Employee_model');
    }

    // ─── Index (list) ─────────────────────────────────────────────────────────

    public function index(): void {
        $keyword  = trim($_GET['q'] ?? '');
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $limit    = ROWS_PER_PAGE;
        $offset   = ($page - 1) * $limit;

        $totalRows  = $this->employeeModel->countAll($keyword);
        $totalPages = (int) ceil($totalRows / $limit);
        $employees  = $this->employeeModel->getAll($keyword, $limit, $offset);

        // Stats for summary cards
        $statusCount    = $this->employeeModel->countByStatus();
        $deptCount      = $this->employeeModel->countByDepartemen();

        $data = [
            'title'       => 'Data Pegawai',
            'employees'   => $employees,
            'keyword'     => $keyword,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'totalRows'   => $totalRows,
            'statusCount' => $statusCount,
            'deptCount'   => $deptCount,
        ];

        $this->view('templates/header', $data);
        $this->view('employee/index',   $data);
        $this->view('templates/footer', $data);
    }

    // ─── Show (detail) ────────────────────────────────────────────────────────

    public function show(int $id = 0): void {
        $employee = $this->employeeModel->getById($id);

        if (!$employee) {
            $this->setFlash(FLASH_ERROR, 'Data pegawai tidak ditemukan.');
            redirect(URLROOT . '/employee');
        }

        $data = [
            'title'    => 'Detail Pegawai',
            'employee' => $employee,
        ];

        $this->view('templates/header', $data);
        $this->view('employee/show',    $data);
        $this->view('templates/footer', $data);
    }

    // ─── Add ─────────────────────────────────────────────────────────────────

    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input  = $this->sanitizeInput($_POST);
            $errors = $this->validate($input);

            // NIK uniqueness check
            if (empty($errors['nik']) && $this->employeeModel->nikExists($input['nik'])) {
                $errors['nik'] = 'NIK sudah terdaftar.';
            }

            if (empty($errors)) {
                if ($this->employeeModel->create($input)) {
                    $this->setFlash(FLASH_SUCCESS, 'Pegawai berhasil ditambahkan.');
                    redirect(URLROOT . '/employee');
                } else {
                    $this->setFlash(FLASH_ERROR, 'Gagal menyimpan data. Silakan coba lagi.');
                }
            }

            $data = [
                'title'  => 'Tambah Pegawai',
                'input'  => $input,
                'errors' => $errors,
            ];
        } else {
            $data = [
                'title'  => 'Tambah Pegawai',
                'input'  => $this->emptyInput(),
                'errors' => [],
            ];
        }

        $this->view('templates/header', $data);
        $this->view('employee/add',     $data);
        $this->view('templates/footer', $data);
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(int $id = 0): void {
        $employee = $this->employeeModel->getById($id);

        if (!$employee) {
            $this->setFlash(FLASH_ERROR, 'Data pegawai tidak ditemukan.');
            redirect(URLROOT . '/employee');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input  = $this->sanitizeInput($_POST);
            $errors = $this->validate($input);

            if (empty($errors['nik']) && $this->employeeModel->nikExists($input['nik'], $id)) {
                $errors['nik'] = 'NIK sudah digunakan pegawai lain.';
            }

            if (empty($errors)) {
                if ($this->employeeModel->update($id, $input)) {
                    $this->setFlash(FLASH_SUCCESS, 'Data pegawai berhasil diperbarui.');
                    redirect(URLROOT . '/employee');
                } else {
                    $this->setFlash(FLASH_ERROR, 'Gagal memperbarui data. Silakan coba lagi.');
                }
            }

            $data = [
                'title'    => 'Edit Pegawai',
                'employee' => $employee,
                'input'    => $input,
                'errors'   => $errors,
            ];
        } else {
            $data = [
                'title'    => 'Edit Pegawai',
                'employee' => $employee,
                'input'    => (array) $employee,
                'errors'   => [],
            ];
        }

        $this->view('templates/header', $data);
        $this->view('employee/edit',    $data);
        $this->view('templates/footer', $data);
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function delete(int $id = 0): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(URLROOT . '/employee');
        }

        $employee = $this->employeeModel->getById($id);

        if (!$employee) {
            $this->setFlash(FLASH_ERROR, 'Data pegawai tidak ditemukan.');
        } elseif ($this->employeeModel->delete($id)) {
            $this->setFlash(FLASH_SUCCESS, "Pegawai {$employee->nama} berhasil dihapus.");
        } else {
            $this->setFlash(FLASH_ERROR, 'Gagal menghapus data. Silakan coba lagi.');
        }

        redirect(URLROOT . '/employee');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function sanitizeInput(array $post): array {
        return [
            'nik'           => trim(htmlspecialchars($post['nik']          ?? '')),
            'nama'          => trim(htmlspecialchars($post['nama']         ?? '')),
            'jabatan'       => trim(htmlspecialchars($post['jabatan']      ?? '')),
            'departemen'    => trim(htmlspecialchars($post['departemen']   ?? '')),
            'gaji'          => (int) preg_replace('/[^0-9]/', '', $post['gaji'] ?? '0'),
            'tanggal_masuk' => trim($post['tanggal_masuk'] ?? ''),
            'status'        => trim($post['status']        ?? 'Aktif'),
        ];
    }

    private function validate(array $d): array {
        $errors = [];

        if (empty($d['nik']))           $errors['nik']           = 'NIK wajib diisi.';
        elseif (!preg_match('/^\d{8,18}$/', $d['nik']))
                                        $errors['nik']           = 'NIK harus berupa 8–18 digit angka.';

        if (empty($d['nama']))          $errors['nama']          = 'Nama wajib diisi.';
        elseif (strlen($d['nama']) < 3) $errors['nama']          = 'Nama minimal 3 karakter.';

        if (empty($d['jabatan']))       $errors['jabatan']       = 'Jabatan wajib diisi.';
        if (empty($d['departemen']))    $errors['departemen']    = 'Departemen wajib diisi.';

        if ($d['gaji'] <= 0)            $errors['gaji']          = 'Gaji harus lebih dari 0.';

        if (empty($d['tanggal_masuk'])) {
            $errors['tanggal_masuk'] = 'Tanggal masuk wajib diisi.';
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $d['tanggal_masuk']);
            if (!$date || $date->format('Y-m-d') !== $d['tanggal_masuk']) {
                $errors['tanggal_masuk'] = 'Format tanggal tidak valid.';
            }
        }

        if (!in_array($d['status'], ['Aktif', 'Non-Aktif', 'Cuti'])) {
            $errors['status'] = 'Status tidak valid.';
        }

        return $errors;
    }

    private function emptyInput(): array {
        return [
            'nik'           => '',
            'nama'          => '',
            'jabatan'       => '',
            'departemen'    => '',
            'gaji'          => '',
            'tanggal_masuk' => '',
            'status'        => 'Aktif',
        ];
    }

    private function setFlash(string $key, string $message): void {
        $_SESSION[$key] = $message;
    }
}
