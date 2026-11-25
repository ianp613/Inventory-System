<?php
// php -S 0.0.0.0:8080 -t public dev/router.php   (development only)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('#^/guest/s/([^/]+)/?#', $path, $m)) {
    $_GET['site'] = urldecode($m[1]);
    require __DIR__ . '/../public/guest/index.php';
    return true;
}
return false;
