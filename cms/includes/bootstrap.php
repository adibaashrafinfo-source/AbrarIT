<?php
/** Loaded at the top of every admin page: session, config, includes. */

declare(strict_types=1);

// Never show raw PHP errors/warnings to visitors; log them instead. Set at
// the PHP level (not via .htaccess php_flag) so it works under any SAPI,
// including PHP-FPM hosts where php_flag in .htaccess causes a 500 error.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Lax');
$isHttps = ($_SERVER['HTTPS'] ?? '') !== '' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
if ($isHttps) {
    ini_set('session.cookie_secure', '1');
}
session_start();

$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    die('Setup needed: copy cms/config.sample.php to cms/config.php and fill in your database details.');
}
require_once $configPath;
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
