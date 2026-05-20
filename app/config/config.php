<?php

// ─── Environment ─────────────────────────────────────────────────────────────
define('APP_NAME',    'SIMKEP');
define('APP_VERSION', '1.0.0');
define('APP_ENV',     'development'); // 'production' | 'development'

// ─── URL ─────────────────────────────────────────────────────────────────────
// Set URLROOT to the base URL of the application.
// Examples:
//   Local  → 'http://localhost/mvc-kepegawaian/public'
//   Docker → 'http://localhost:8080'
define('URLROOT', 'http://localhost/mvc-kepegawaian/public');

// ─── Paths ───────────────────────────────────────────────────────────────────
define('APPROOT',  dirname(__DIR__));            // …/app
define('PUBROOT',  dirname(dirname(__DIR__)) . '/public');  // …/public

// ─── Database ────────────────────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_kepegawaian');
define('DB_USER', 'root');
define('DB_PASS', '');

// ─── Routing Defaults ────────────────────────────────────────────────────────
define('DEFAULT_CONTROLLER', 'Employee');
define('DEFAULT_METHOD',     'index');

// ─── Pagination ──────────────────────────────────────────────────────────────
define('ROWS_PER_PAGE', 10);

// ─── Flash Message Session Keys ──────────────────────────────────────────────
define('FLASH_SUCCESS', 'flash_success');
define('FLASH_ERROR',   'flash_error');
define('FLASH_WARNING', 'flash_warning');

// ─── Error Reporting ─────────────────────────────────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
