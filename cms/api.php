<?php
/**
 * Public JSON API consumed by the static website (index.html, blog.html,
 * portfolio.html, services.html, contact.html).
 *
 *   GET  api.php?resource=blog
 *   GET  api.php?resource=portfolio
 *   GET  api.php?resource=services
 *   POST api.php?resource=contact   (used by the Contact page form)
 */
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

// ---- CORS ----
$allowed = array_map('trim', explode(',', defined('ALLOWED_ORIGINS') ? ALLOWED_ORIGINS : ''));
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && in_array($origin, $allowed, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function json_out($data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$resource = $_GET['resource'] ?? '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        switch ($resource) {
            case 'blog':
                $rows = db()->query("SELECT title, slug, category, excerpt, cover_gradient_start, cover_gradient_end, published_at
                    FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC")->fetchAll();
                json_out(['ok' => true, 'data' => $rows]);
                break;

            case 'portfolio':
                $rows = db()->query("SELECT title, category, description, thumb_label, project_url, cover_gradient_start, cover_gradient_end
                    FROM portfolio_items WHERE status = 'published' ORDER BY sort_order ASC")->fetchAll();
                json_out(['ok' => true, 'data' => $rows]);
                break;

            case 'services':
                $rows = db()->query("SELECT name, slug, summary, starting_price
                    FROM services WHERE status = 'published' ORDER BY sort_order ASC")->fetchAll();
                json_out(['ok' => true, 'data' => $rows]);
                break;

            default:
                json_out(['ok' => false, 'error' => 'Unknown resource.'], 404);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $resource === 'contact') {
        $raw = file_get_contents('php://input');
        $body = [];
        if ($raw !== false && $raw !== '' && str_starts_with(trim($raw), '{')) {
            $body = json_decode($raw, true) ?: [];
        } else {
            $body = $_POST;
        }

        // Honeypot: a hidden field real users never fill in. Bots usually do.
        if (!empty($body['website'])) {
            json_out(['ok' => true]); // pretend success, discard silently
        }

        $name = trim((string) ($body['name'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));
        $phone = trim((string) ($body['phone'] ?? ''));
        $service = trim((string) ($body['service'] ?? ''));
        $message = trim((string) ($body['message'] ?? ''));

        $errors = [];
        if ($name === '') $errors[] = 'Name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
        if ($message === '') $errors[] = 'Message is required.';

        if ($errors) {
            json_out(['ok' => false, 'errors' => $errors], 422);
        }

        $stmt = db()->prepare('INSERT INTO contact_messages (name, email, phone, service_interest, message)
            VALUES (:name, :email, :phone, :service, :message)');
        $stmt->execute([
            'name' => mb_substr($name, 0, 150),
            'email' => mb_substr($email, 0, 190),
            'phone' => mb_substr($phone, 0, 40),
            'service' => mb_substr($service, 0, 120),
            'message' => mb_substr($message, 0, 5000),
        ]);

        json_out(['ok' => true]);
    }

    json_out(['ok' => false, 'error' => 'Unsupported request.'], 405);
} catch (Throwable $e) {
    json_out(['ok' => false, 'error' => 'Server error.'], 500);
}
