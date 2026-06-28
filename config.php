<?php
// ============================================================
// includes/config.php — Database & App Configuration
// ============================================================

define('DB_HOST',     getenv('DB_HOST')     ?: 'sql206.infinityfree.com');
define('DB_PORT',     getenv('DB_PORT')     ?: '3306');
define('DB_NAME',     getenv('DB_NAME')     ?: 'if0_42286439_kilimo_smart');
define('DB_USER',     getenv('DB_USER')     ?: 'if0_42286439');
define('DB_PASS',     getenv('DB_PASS')     ?: 'maryJohn059');
define('DB_CHARSET',  'utf8mb4');

define('APP_NAME',    'Kilimo Smart');
define('APP_URL',     getenv('APP_URL')     ?: 'http://kilimosmart.infinityfreeapp.com');
define('APP_VERSION', '1.0.0');

// Session
define('SESSION_NAME',     'ks_session');
define('SESSION_LIFETIME', 60 * 60 * 8); // 8 hours

// Timezone
date_default_timezone_set('Africa/Dar_es_Salaam');

// Error display (set to false in production)
$debug = (bool)(getenv('APP_DEBUG') ?: true);
ini_set('display_errors', $debug ? '1' : '0');
error_reporting($debug ? E_ALL : 0);
