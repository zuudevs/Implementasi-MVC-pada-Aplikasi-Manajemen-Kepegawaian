<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>404 — Halaman Tidak Ditemukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; }
        .container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="text-center">
        <div style="font-size:80px; font-weight:900; color:#1a56db; letter-spacing:-4px;">404</div>
        <h4 style="font-weight:700; margin-bottom:8px;">Halaman Tidak Ditemukan</h4>
        <p class="text-muted" style="font-size:14px;">Halaman yang Anda cari tidak tersedia atau telah dipindahkan.</p>
        <a href="<?= defined('URLROOT') ? URLROOT : '/' ?>/employee"
           class="btn btn-primary mt-2">
            <i class="bi bi-house me-1"></i>Kembali ke Beranda
        </a>
    </div>
</div>
</body>
</html>
