<?php
/**
 * app/views/employee/index.php
 * Variables injected: $employees, $keyword, $page, $totalPages, $totalRows,
 *                     $statusCount, $deptCount
 */
$totalAll = array_sum($statusCount);
?>

<!-- ── Stats Row ──────────────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-people"></i></div>
            <div>
                <div class="stat-label">Total Pegawai</div>
                <div class="stat-value"><?= $totalAll ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-person-check"></i></div>
            <div>
                <div class="stat-label">Aktif</div>
                <div class="stat-value"><?= $statusCount['Aktif'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="bi bi-person-dash"></i></div>
            <div>
                <div class="stat-label">Cuti</div>
                <div class="stat-value"><?= $statusCount['Cuti'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-person-x"></i></div>
            <div>
                <div class="stat-label">Non-Aktif</div>
                <div class="stat-value"><?= $statusCount['Non-Aktif'] ?></div>
            </div>
        </div>
    </div>
</div>

<!-- ── Table Card ─────────────────────────────────────────────────────────── -->
<div class="card">

    <!-- Header: title + search + add button -->
    <div class="card-header-custom">
        <div style="flex:1">
            <h5 style="font-weight:700; margin:0 0 2px; font-size:15px;">Daftar Pegawai</h5>
            <p style="margin:0; font-size:12px; color:var(--text-muted);">
                <?php if ($keyword): ?>
                    <?= $totalRows ?> hasil untuk pencarian "<strong><?= htmlspecialchars($keyword) ?></strong>"
                <?php else: ?>
                    Menampilkan <?= count($employees) ?> dari <?= $totalRows ?> pegawai
                <?php endif; ?>
            </p>
        </div>

        <!-- Search -->
        <form method="GET" action="<?= URLROOT ?>/employee" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    name="q"
                    class="form-control"
                    placeholder="Cari nama, NIK, jabatan…"
                    value="<?= htmlspecialchars($keyword) ?>"
                    style="width:230px;"
                >
            </div>
            <?php if ($keyword): ?>
                <a href="<?= URLROOT ?>/employee" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </form>

        <a href="<?= URLROOT ?>/employee/add" class="btn btn-primary d-flex align-items-center gap-2 ms-2">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline">Tambah Pegawai</span>
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>NIK</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan</th>
                        <th>Departemen</th>
                        <th>Gaji</th>
                        <th>Tgl Masuk</th>
                        <th>Status</th>
                        <th style="width:110px; text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <div style="font-weight:600; font-size:15px;">Tidak ada data pegawai</div>
                                    <div class="mt-1" style="font-size:13px;">
                                        <?= $keyword ? 'Tidak ada hasil untuk pencarian tersebut.' : 'Mulai dengan menambah pegawai baru.' ?>
                                    </div>
                                    <?php if (!$keyword): ?>
                                        <a href="<?= URLROOT ?>/employee/add" class="btn btn-primary mt-3">
                                            <i class="bi bi-plus-lg me-1"></i>Tambah Pegawai
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = ($page - 1) * ROWS_PER_PAGE + 1; ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td style="color:var(--text-muted); font-size:12px;"><?= $no++ ?></td>
                                <td><span class="nik-cell"><?= htmlspecialchars($emp->nik) ?></span></td>
                                <td>
                                    <div style="font-weight:600;"><?= htmlspecialchars($emp->nama) ?></div>
                                </td>
                                <td><?= htmlspecialchars($emp->jabatan) ?></td>
                                <td>
                                    <span style="background:#f1f5f9; padding:2px 8px; border-radius:20px; font-size:12px;">
                                        <?= htmlspecialchars($emp->departemen) ?>
                                    </span>
                                </td>
                                <td style="font-family:var(--font-mono); font-size:12.5px;">
                                    <?= formatRupiah($emp->gaji) ?>
                                </td>
                                <td style="font-size:12.5px; color:var(--text-muted);">
                                    <?= formatTanggal($emp->tanggal_masuk) ?>
                                </td>
                                <td><?= statusBadge($emp->status) ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Detail -->
                                        <a href="<?= URLROOT ?>/employee/show/<?= $emp->id ?>"
                                           class="btn-icon" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <!-- Edit -->
                                        <a href="<?= URLROOT ?>/employee/edit/<?= $emp->id ?>"
                                           class="btn-icon btn-edit" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <!-- Delete -->
                                        <button type="button"
                                                class="btn-icon btn-danger"
                                                title="Hapus"
                                                onclick="confirmDelete(<?= $emp->id ?>, '<?= htmlspecialchars(addslashes($emp->nama)) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="d-flex justify-content-between align-items-center px-4 py-3" style="border-top:1px solid var(--border);">
                <div style="font-size:12.5px; color:var(--text-muted);">
                    Halaman <?= $page ?> dari <?= $totalPages ?>
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?q=<?= urlencode($keyword) ?>&page=<?= $page - 1 ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?q=<?= urlencode($keyword) ?>&page=<?= $p ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?q=<?= urlencode($keyword) ?>&page=<?= $page + 1 ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

    </div><!-- /.card-body -->
</div><!-- /.card -->

<!-- Delete Confirm Modal -->
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
                    Data <strong id="deleteEmpName"></strong> akan dihapus secara permanen.
                    Tindakan ini tidak dapat dibatalkan.
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
