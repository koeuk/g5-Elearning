<?php

/*
 * Single front controller for the whole application.
 *
 * The document root is this `public/` directory. Every request that is not a
 * real file (see .htaccess) lands here. We load Composer's PSR-4 autoloader,
 * start the session, and hand the request to the Router defined in
 * routes/web.php. Application code lives in ../src under the App\ namespace.
 */

use App\Core\Router;
use App\Core\Session;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

// Uploads are written with paths relative to the web root (e.g. 'uploading/x').
chdir(__DIR__);

Session::start();

/** @var Router $router */
$router = require BASE_PATH . '/routes/web.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'] ?? 'GET');
