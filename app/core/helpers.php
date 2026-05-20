<?php

/**
 * helpers.php — Global helper functions
 */

// ─── Redirect ────────────────────────────────────────────────────────────────

function redirect(string $url): never {
    header("Location: {$url}");
    exit;
}

// ─── Flash Messages ───────────────────────────────────────────────────────────

/**
 * Display and clear a flash message from session.
 * Returns the Bootstrap alert HTML or empty string.
 */
function flash(string $key): string {
    if (!empty($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);

        $type = match ($key) {
            FLASH_SUCCESS => 'success',
            FLASH_ERROR   => 'danger',
            FLASH_WARNING => 'warning',
            default       => 'info',
        };

        $icon = match ($type) {
            'success' => '<i class="bi bi-check-circle-fill me-2"></i>',
            'danger'  => '<i class="bi bi-x-circle-fill me-2"></i>',
            'warning' => '<i class="bi bi-exclamation-triangle-fill me-2"></i>',
            default   => '<i class="bi bi-info-circle-fill me-2"></i>',
        };

        return <<<HTML
        <div class="alert alert-{$type} alert-dismissible fade show d-flex align-items-center" role="alert">
            {$icon}
            <span>{$message}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        HTML;
    }
    return '';
}

// ─── Formatting ───────────────────────────────────────────────────────────────

function formatRupiah(int|float $amount): string {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatTanggal(string $date): string {
    if (empty($date) || $date === '0000-00-00') return '-';
    $months = [
        '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
    ];
    [$y, $m, $d] = explode('-', $date);
    return "{$d} {$months[$m]} {$y}";
}

function statusBadge(string $status): string {
    $class = match ($status) {
        'Aktif'     => 'badge-aktif',
        'Non-Aktif' => 'badge-nonaktif',
        'Cuti'      => 'badge-cuti',
        default     => 'bg-secondary',
    };
    return "<span class=\"status-badge {$class}\">{$status}</span>";
}

function errorClass(array $errors, string $field): string {
    return isset($errors[$field]) ? 'is-invalid' : '';
}

function errorMsg(array $errors, string $field): string {
    return isset($errors[$field])
        ? '<div class="invalid-feedback">' . htmlspecialchars($errors[$field]) . '</div>'
        : '';
}

function activeNav(string $segment): string {
    $url     = $_SERVER['REQUEST_URI'] ?? '';
    $current = strtolower(explode('/', trim(parse_url($url, PHP_URL_PATH), '/'))[1] ?? '');
    return strtolower($segment) === $current ? 'active' : '';
}

function oldValue(array $input, string $field, string $default = ''): string {
    return htmlspecialchars($input[$field] ?? $default);
}
