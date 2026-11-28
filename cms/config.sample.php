<?php
/**
 * Abrar IT CMS — configuration
 *
 * Copy this file to "config.php" (same folder) and fill in your own values.
 * config.php is ignored by git and must NEVER be committed or shared —
 * it holds your real database password.
 */

// ---- Database (from your hosting's cPanel -> MySQL Databases) ----
define('DB_DSN', 'mysql:host=localhost;dbname=YOUR_DB_NAME;charset=utf8mb4');
define('DB_USER', 'YOUR_DB_USER');
define('DB_PASS', 'YOUR_DB_PASSWORD');

// ---- Site ----
// The admin panel's own URL, no trailing slash. Used for redirects/links only.
define('CMS_URL', 'https://yourdomain.com/cms');

// The public website's origin(s) allowed to call api.php from the browser
// (your Vercel/GitHub Pages URL). Comma-separate multiple origins.
// Example: 'https://abrar-it.vercel.app,https://adibaashrafinfo-source.github.io'
define('ALLOWED_ORIGINS', 'https://abrar-it.vercel.app');

// Reserved for future use (e.g. signing tokens for a feature added later).
// Not currently read by any code — PHP's own session handling (httponly,
// secure, SameSite, strict mode, ID regeneration on login) is what actually
// protects admin sessions today, configured in includes/bootstrap.php.
// Still: set this to a long random string (e.g. from https://randomkeygen.com/)
// and never share it, so it's ready if a future feature needs it.
define('APP_SECRET', 'change-this-to-a-long-random-string');
