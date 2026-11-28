<?php
/** Small shared helpers used across the admin panel. */

/** Escape a value for safe HTML output. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** True only for http:// or https:// URLs — rejects javascript:, data: etc. before use in an href. */
function is_safe_http_url(?string $value): bool
{
    if (!$value) {
        return false;
    }
    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true) && filter_var($value, FILTER_VALIDATE_URL) !== false;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function flash_set(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function flash_get(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(400);
        die('Your session expired or this form was submitted incorrectly. Go back and try again.');
    }
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text) ?: $text;
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text === '' ? 'item-' . substr(md5((string) microtime(true)), 0, 6) : $text;
}

function unique_slug(string $table, string $baseSlug, ?int $excludeId = null): string
{
    $pdo = db();
    $slug = $baseSlug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM {$table} WHERE slug = :slug" . ($excludeId ? ' AND id != :id' : '');
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        if ($excludeId) {
            $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}
