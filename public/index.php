<?php

/**
 * public/index.php — Application Entry Point
 *
 * Every HTTP request is routed here via .htaccess.
 * This file bootstraps the application: loads config,
 * requires core classes, and instantiates the router.
 */

// ── 1. Define root paths ──────────────────────────────────────────────────────
define('ROOT',   dirname(__DIR__));            // project root
define('APPDIR', ROOT . '/app');               // app directory

// ── 2. Load configuration ─────────────────────────────────────────────────────
require_once APPDIR . '/config/config.php';

// ── 3. Start session ──────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── 4. Autoload core classes ──────────────────────────────────────────────────
require_once APPDIR . '/core/Database.php';
require_once APPDIR . '/core/Controller.php';
require_once APPDIR . '/core/helpers.php';
require_once APPDIR . '/core/App.php';

// ── 5. Launch the application ─────────────────────────────────────────────────
$app = new App();
