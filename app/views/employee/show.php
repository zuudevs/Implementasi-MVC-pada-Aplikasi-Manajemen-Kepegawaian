<?php
/**
 * app/views/employee/show.php
 * Variables: $employee (object)
 */
?>

<!-- Breadcrumb -->
<nav style="font-size:13px; margin-bottom:20px; color:var(--text-muted);">
    <a href="<?= URLROOT ?>/employee" style="color:var(--primary); text-decoration:none;">
        <i class="bi bi-people me-1"></i>Data Pegawai
    </a>
    <span class="mx-2">/</span>
    <span>Detail Pegawai</span>
</nav>

<div style="max-width:700px; margin:auto;">

    <!-- Profile Card -->
    <div class="card mb-3">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-4">
                <!-- Avatar -->
                <div style="width:72px; height:72px; border-radius:18px; background:var(--primary);
                            display:grid; place-items:center; color:#fff; font-size:28px; font-weight:700;
                            flex-shrink:0; letter-spacing:-1px;">
                    <?= mb_strtoupper(mb_substr($employee->nama, 0, 1)) ?>
                </div>

                <div style="flex:1; min-width:0;">
                    <div style="font-size:20px; font-weight:800; margin-bottom:4px;">
                        <?= htmlspecialchars($employee->nama) ?>
                    </div>
                    <div style="font-size:14px; color:var(--text-muted); margin-bottom:8px;">
                        <?= htmlspecialchars($employee->jabatan) ?>
                        &middot;
                        <span style="background:#f1f5f9; padding:2px 9px; border-radius:20px; font-size:12px;">
                            <?= htmlspecialchars($employee->departemen) ?>
                        </span>
                    </div>
                    <?= statusBadge($employee->status) ?>
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <a href="<?= URLROOT ?>/employee/edit/<?= $employee->id ?>" class="btn btn-warning btn-sm text-dark">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="<?= URLROOT ?>/employee" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Info Card -->
    <div class="card">
        <div class="card-body p-4">
            <h6 style="font-weight:700; font-size:13px; text-transform:uppercase; letter-spacing:.5px;
                       color:var(--text-muted); margin-bottom:20px;">Informasi Detail</h6>

            <div class="row g-4">
                <?php
                $fields = [
                    ['NIK',             $employee->nik,            'bi-fingerprint',   true],
                    ['Nama Lengkap',    $employee->nama,           'bi-person',        false],
                    ['Jabatan',         $employee->jabatan,        'bi-briefcase',     false],
                    ['Departemen',      $employee->departemen,     'bi-building',      false],
                    ['Gaji',            formatRupiah($employee->gaji), 'bi-cash-stack', false],
                    ['Tanggal Masuk',   formatTanggal($employee->tanggal_masuk), 'bi-calendar-check', false],
                    ['Status',          statusBadge($employee->status), 'bi-circle-fill', false],
                    ['ID Record',       '#' . $employee->id,       'bi-hash',          true],
                ];
                ?>

                <?php foreach ($fields as [$label, $value, $icon, $mono]): ?>
                    <div class="col-sm-6">
                        <div style="display:flex; align-items:flex-start; gap:12px;">
                            <div style="width:34px; height:34px; background:#f1f5f9; border-radius:8px;
                                        display:grid; place-items:center; color:var(--text-muted);
                                        font-size:15px; flex-shrink:0; margin-top:1px;">
                                <i class="bi <?= $icon ?>"></i>
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:11px; font-weight:600; text-transform:uppercase;
                                            letter-spacing:.5px; color:var(--text-muted); margin-bottom:3px;">
                                    <?= $label ?>
                                </div>
                                <div style="font-size:14px; font-weight:600; <?= $mono ? 'font-family:var(--font-mono);' : '' ?>">
                                    <?= $value ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

            <?php if (!empty($employee->created_at)): ?>
                <div style="margin-top:24px; padding-top:16px; border-top:1px solid var(--border);
                            font-size:11.5px; color:var(--text-muted); display:flex; gap:20px; flex-wrap:wrap;">
                    <span><i class="bi bi-plus-circle me-1"></i>Ditambahkan: <?= $employee->created_at ?></span>
                    <?php if (!empty($employee->updated_at)): ?>
                        <span><i class="bi bi-pencil me-1"></i>Diperbarui: <?= $employee->updated_at ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Danger Zone -->
    <div class="card mt-3" style="border-color:var(--danger);">
        <div class="card-body px-4 py-3 d-flex justify-content-between align-items-center">
            <div>
                <div style="font-weight:700; font-size:13.5px; color:var(--danger);">Hapus Pegawai</div>
                <div style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">
                    Tindakan ini tidak dapat dibatalkan.
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm"
                    onclick="confirmDelete(<?= $employee->id ?>, '<?= htmlspecialchars(addslashes($employee->nama)) ?>')">
                <i class="bi bi-trash me-1"></i>Hapus
            </button>
        </div>
    </div>

</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 60px rgba(0,0,0,.15)">
            <div class="modal-body p-4 text-center">
                <div style="width:60px; height:60px; background:var(--danger-light); border-radius:50%;
                            display:grid; place-items:center; margin:0 auto 16px; font-size:26px; color:var(--danger);">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <h6 style="font-weight:700; font-size:16px; margin-bottom:8px;">Hapus Pegawai?</h6>
                <p class="text-muted mb-0" style="font-size:13.5px;">
                    Data <strong id="deleteEmpName"></strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteEmpName').textContent = name;
    document.getElementById('deleteForm').action = '<?= URLROOT ?>/employee/delete/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
