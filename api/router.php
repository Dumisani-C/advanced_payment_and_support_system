<?php
/**
 * Router for PHP built-in server
 * Usage: php -S localhost:8080 api/router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the request is for the API
if (preg_match('#^/api(/.*)?$#', $uri, $matches)) {
    $_GET['request'] = isset($matches[1]) ? ltrim($matches[1], '/') : '';
    require __DIR__ . '/index.php';
    return true;
}

// For other requests, serve from the main directory
$file = __DIR__ . '/../' . $uri;
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    return false; // Serve the file directly
}

// Default: serve index.php from root
$_SERVER['PHP_SELF'] = '/index.php';
require __DIR__ . '/../index.php';
