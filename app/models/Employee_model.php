<?php

/**
 * Employee_model.php
 * 
 * Handles all database interactions for the Pegawai (Employee) entity.
 */
class Employee_model {

    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    // ─── Read ─────────────────────────────────────────────────────────────────

    /**
     * Get all employees, with optional keyword search and pagination.
     */
    public function getAll(string $keyword = '', int $limit = 0, int $offset = 0): array {
        $sql = "SELECT * FROM pegawai WHERE deleted_at IS NULL";

        if ($keyword !== '') {
            $sql .= " AND (nik LIKE :kw 
                          OR nama LIKE :kw 
                          OR jabatan LIKE :kw 
                          OR departemen LIKE :kw)";
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $this->db->query($sql);

        if ($keyword !== '') {
            $this->db->bind(':kw', "%{$keyword}%");
        }

        if ($limit > 0) {
            $this->db->bind(':limit',  $limit,  PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }

        return $this->db->resultSet();
    }

    /**
     * Count all employees (for pagination), with optional keyword filter.
     */
    public function countAll(string $keyword = ''): int {
        $sql = "SELECT COUNT(*) AS total FROM pegawai WHERE deleted_at IS NULL";

        if ($keyword !== '') {
            $sql .= " AND (nik LIKE :kw 
                          OR nama LIKE :kw 
                          OR jabatan LIKE :kw 
                          OR departemen LIKE :kw)";
        }

        $this->db->query($sql);

        if ($keyword !== '') {
            $this->db->bind(':kw', "%{$keyword}%");
        }

        $row = $this->db->single();
        return (int) $row->total;
    }

    /**
     * Get a single employee by primary key.
     */
    public function getById(int $id): object|false {
        $this->db->query(
            "SELECT * FROM pegawai WHERE id = :id AND deleted_at IS NULL"
        );
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * Check whether a NIK already exists (optionally exclude a given id — for edit).
     */
    public function nikExists(string $nik, int $excludeId = 0): bool {
        $this->db->query(
            "SELECT id FROM pegawai 
             WHERE nik = :nik AND deleted_at IS NULL AND id != :excludeId"
        );
        $this->db->bind(':nik',       $nik);
        $this->db->bind(':excludeId', $excludeId, PDO::PARAM_INT);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    /**
     * Insert a new employee record.
     */
    public function create(array $data): bool {
        $this->db->query(
            "INSERT INTO pegawai 
                (nik, nama, jabatan, departemen, gaji, tanggal_masuk, status, created_at)
             VALUES 
                (:nik, :nama, :jabatan, :departemen, :gaji, :tanggal_masuk, :status, NOW())"
        );
        $this->bindEmployeeData($data);
        return $this->db->execute();
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    /**
     * Update an existing employee record.
     */
    public function update(int $id, array $data): bool {
        $this->db->query(
            "UPDATE pegawai SET
                nik           = :nik,
                nama          = :nama,
                jabatan       = :jabatan,
                departemen    = :departemen,
                gaji          = :gaji,
                tanggal_masuk = :tanggal_masuk,
                status        = :status,
                updated_at    = NOW()
             WHERE id = :id AND deleted_at IS NULL"
        );
        $this->bindEmployeeData($data);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    /**
     * Soft-delete an employee (sets deleted_at timestamp).
     */
    public function delete(int $id): bool {
        $this->db->query(
            "UPDATE pegawai SET deleted_at = NOW() WHERE id = :id"
        );
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    // ─── Stats (for dashboard) ────────────────────────────────────────────────

    public function countByStatus(): array {
        $this->db->query(
            "SELECT status, COUNT(*) AS total 
             FROM pegawai 
             WHERE deleted_at IS NULL 
             GROUP BY status"
        );
        $rows   = $this->db->resultSet();
        $counts = ['Aktif' => 0, 'Non-Aktif' => 0, 'Cuti' => 0];
        foreach ($rows as $row) {
            $counts[$row->status] = (int) $row->total;
        }
        return $counts;
    }

    public function countByDepartemen(): array {
        $this->db->query(
            "SELECT departemen, COUNT(*) AS total 
             FROM pegawai 
             WHERE deleted_at IS NULL 
             GROUP BY departemen 
             ORDER BY total DESC"
        );
        return $this->db->resultSet();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function bindEmployeeData(array $d): void {
        $this->db->bind(':nik',           $d['nik']);
        $this->db->bind(':nama',          $d['nama']);
        $this->db->bind(':jabatan',       $d['jabatan']);
        $this->db->bind(':departemen',    $d['departemen']);
        $this->db->bind(':gaji',          $d['gaji']);
        $this->db->bind(':tanggal_masuk', $d['tanggal_masuk']);
        $this->db->bind(':status',        $d['status']);
    }
}
