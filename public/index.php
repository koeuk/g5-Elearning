<?php

/*
 * Single front controller for the whole application.
 *
 * The document root is this `public/` directory. Every request that is not a
 * real file (see .htaccess) lands here. We load Composer's PSR-4 autoloader,
 * start the session, and hand the request to the Router defined in
 * routes/web.php. Application code lives in ../src under the App\ namespace.
 */

// Under `php -S` (the built-in dev server) there is no .htaccess, so every
// request — including real static files like /vendor/css/style.css — reaches
// this front controller. Serve on-disk files directly instead of routing them,
// otherwise assets 404 and pages render with no CSS/JS. No-op under Apache/FPM.
if (PHP_SAPI === 'cli-server') {
    $requested = __DIR__ . urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
    if (is_file($requested)) {
        return false;
    }
}

use App\Core\Env;
use App\Core\Router;
use App\Core\Session;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

// Load .env into the environment so getenv() sees DB_* without a shell export.
Env::load(BASE_PATH . '/.env');

// Uploads are written with paths relative to the web root (e.g. 'uploading/x').
chdir(__DIR__);

Session::start();

/** @var Router $router */
$router = require BASE_PATH . '/routes/web.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'] ?? 'GET');
