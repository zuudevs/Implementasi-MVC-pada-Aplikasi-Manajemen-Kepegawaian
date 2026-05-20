<?php
/**
 * app/views/employee/edit.php
 * Variables: $employee (object), $input (array), $errors (array)
 */
?>

<!-- Breadcrumb -->
<nav style="font-size:13px; margin-bottom:20px; color:var(--text-muted);">
    <a href="<?= URLROOT ?>/employee" style="color:var(--primary); text-decoration:none;">
        <i class="bi bi-people me-1"></i>Data Pegawai
    </a>
    <span class="mx-2">/</span>
    <span>Edit Pegawai</span>
</nav>

<div class="card" style="max-width:780px; margin:auto;">
    <div class="card-header-custom pb-0 pt-4 px-4">
        <div style="width:40px; height:40px; background:var(--warn-light); border-radius:10px;
                    display:grid; place-items:center; color:var(--warn); font-size:18px; margin-right:4px;">
            <i class="bi bi-pencil-square"></i>
        </div>
        <div>
            <h5 style="font-weight:700; margin:0; font-size:15px;">Edit Data Pegawai</h5>
            <p style="margin:0; font-size:12px; color:var(--text-muted);">
                ID: <span style="font-family:var(--font-mono)"><?= $employee->id ?></span>
                &middot; NIK: <span style="font-family:var(--font-mono)"><?= htmlspecialchars($employee->nik) ?></span>
            </p>
        </div>
    </div>

    <div class="card-body px-4 pb-4">
        <form method="POST" action="<?= URLROOT ?>/employee/edit/<?= $employee->id ?>" novalidate>

            <div class="row g-3">

                <!-- NIK -->
                <div class="col-md-6">
                    <label class="form-label">NIK <span style="color:var(--danger)">*</span></label>
                    <input type="text"
                           name="nik"
                           class="form-control <?= errorClass($errors, 'nik') ?>"
                           value="<?= oldValue($input, 'nik') ?>"
                           maxlength="18">
                    <?= errorMsg($errors, 'nik') ?>
                </div>

                <!-- Nama -->
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap <span style="color:var(--danger)">*</span></label>
                    <input type="text"
                           name="nama"
                           class="form-control <?= errorClass($errors, 'nama') ?>"
                           value="<?= oldValue($input, 'nama') ?>">
                    <?= errorMsg($errors, 'nama') ?>
                </div>

                <!-- Jabatan -->
                <div class="col-md-6">
                    <label class="form-label">Jabatan <span style="color:var(--danger)">*</span></label>
                    <input type="text"
                           name="jabatan"
                           class="form-control <?= errorClass($errors, 'jabatan') ?>"
                           value="<?= oldValue($input, 'jabatan') ?>">
                    <?= errorMsg($errors, 'jabatan') ?>
                </div>

                <!-- Departemen -->
                <div class="col-md-6">
                    <label class="form-label">Departemen <span style="color:var(--danger)">*</span></label>
                    <select name="departemen" class="form-select <?= errorClass($errors, 'departemen') ?>">
                        <option value="">-- Pilih Departemen --</option>
                        <?php
                        $depts = ['IT', 'HRD', 'Keuangan', 'Marketing', 'Operasional', 'Legal', 'Produksi', 'Logistik'];
                        foreach ($depts as $d):
                            $sel = ($input['departemen'] ?? '') === $d ? 'selected' : '';
                        ?>
                            <option value="<?= $d ?>" <?= $sel ?>><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= errorMsg($errors, 'departemen') ?>
                </div>

                <!-- Gaji -->
                <div class="col-md-6">
                    <label class="form-label">Gaji <span style="color:var(--danger)">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text" style="font-size:13px; background:#f8fafc; border-color:var(--border);">Rp</span>
                        <input type="text"
                               id="gajiDisplay"
                               data-rupiah
                               class="form-control <?= errorClass($errors, 'gaji') ?>"
                               value="<?= number_format((int)($input['gaji'] ?? 0), 0, ',', '.') ?>">
                        <input type="hidden" name="gaji" id="gajiRaw" value="<?= (int)($input['gaji'] ?? 0) ?>">
                    </div>
                    <?= errorMsg($errors, 'gaji') ?>
                </div>

                <!-- Tanggal Masuk -->
                <div class="col-md-6">
                    <label class="form-label">Tanggal Masuk <span style="color:var(--danger)">*</span></label>
                    <input type="date"
                           name="tanggal_masuk"
                           class="form-control <?= errorClass($errors, 'tanggal_masuk') ?>"
                           value="<?= oldValue($input, 'tanggal_masuk') ?>"
                           max="<?= date('Y-m-d') ?>">
                    <?= errorMsg($errors, 'tanggal_masuk') ?>
                </div>

                <!-- Status -->
                <div class="col-12">
                    <label class="form-label">Status <span style="color:var(--danger)">*</span></label>
                    <div class="d-flex gap-3 flex-wrap">
                        <?php
                        $statuses = [
                            'Aktif'     => ['green',  'bi-person-check'],
                            'Cuti'      => ['yellow', 'bi-person-dash'],
                            'Non-Aktif' => ['red',    'bi-person-x'],
                        ];
                        foreach ($statuses as $s => [$color, $icon]):
                            $checked = ($input['status'] ?? 'Aktif') === $s ? 'checked' : '';
                        ?>
                            <label class="status-radio-label" style="cursor:pointer">
                                <input type="radio" name="status" value="<?= $s ?>" <?= $checked ?> hidden>
                                <div class="status-radio-btn d-flex align-items-center gap-2 px-4 py-2"
                                     style="border:2px solid var(--border); border-radius:10px; background:var(--card-bg); transition:.15s; font-weight:600; font-size:13.5px;">
                                    <i class="bi <?= $icon ?>"></i>
                                    <?= $s ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?= errorMsg($errors, 'status') ?>
                </div>

            </div><!-- /.row -->

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top:1px solid var(--border);">
                <a href="<?= URLROOT ?>/employee/show/<?= $employee->id ?>"
                   style="font-size:13px; color:var(--text-muted); text-decoration:none;">
                    <i class="bi bi-eye me-1"></i>Lihat Detail
                </a>
                <div class="d-flex gap-2">
                    <a href="<?= URLROOT ?>/employee" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-warning text-dark">
                        <i class="bi bi-floppy me-1"></i>Perbarui Data
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
function updateStatusRadios() {
    document.querySelectorAll('.status-radio-label').forEach(label => {
        const radio = label.querySelector('input[type=radio]');
        const btn   = label.querySelector('.status-radio-btn');
        const colors = { 'Aktif': '#0e9f6e', 'Non-Aktif': '#e02424', 'Cuti': '#e3a008' };
        const bgs    = { 'Aktif': '#d5f5e6', 'Non-Aktif': '#fde8e8', 'Cuti': '#fef3c7' };
        if (radio.checked) {
            btn.style.borderColor = colors[radio.value] || '#1a56db';
            btn.style.background  = bgs[radio.value]    || '#e8f0fe';
            btn.querySelector('i').style.color = colors[radio.value] || '#1a56db';
        } else {
            btn.style.borderColor = 'var(--border)';
            btn.style.background  = 'var(--card-bg)';
            btn.querySelector('i').style.color = 'var(--text-muted)';
        }
    });
}

document.querySelectorAll('input[name=status]').forEach(r => {
    r.addEventListener('change', updateStatusRadios);
});
updateStatusRadios();

document.querySelector('form').addEventListener('submit', function() {
    const display = document.getElementById('gajiDisplay');
    const raw     = document.getElementById('gajiRaw');
    if (display && raw) {
        raw.value = display.value.replace(/\./g, '');
        display.removeAttribute('name');
    }
});
</script>
