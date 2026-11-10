<?php
// Router for PHP's built-in server: php -S localhost:8889 router.php
// Blocks direct web access to config.json and the src/ folder (Apache/IIS use .htaccess / web.config).
$path = rawurldecode((string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (preg_match('#(^|/)(src(/|$)|config\.json$)#i', $path)) {
    http_response_code(403);
    exit('Forbidden');
}
return false; // serve the requested file / index.php normally
