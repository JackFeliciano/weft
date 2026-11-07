<?php

declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use Weft\App\Front;

$base = getenv('WEFT_BASE') ?: '/';

// The built-in server sends every request to this file, so the stylesheet needs
// its own branch before the router sees the path.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$prefix = rtrim($base, '/');
if ($prefix !== '' && str_starts_with($path, $prefix)) {
    $path = substr($path, strlen($prefix));
}
if ($path === '/assets/site.css') {
    header('Content-Type: text/css; charset=utf-8');
    header('Cache-Control: public, max-age=300');
    echo file_get_contents(__DIR__ . '/assets/site.css');

    return;
}

// nginx hands the request on with the prefix in place, so the front controller
// reads the path from the URI and strips the prefix itself.
(new Front($base))->run();
