<?php

/*
 * Application routes.
 *
 * Replaces the three route tables ($publicPages / $adminPages /
 * $standalonePages) from the old public/index.php. The layout each page uses
 * is now chosen inside the controller action, not by which table it lived in.
 *
 * Paths are kept identical to the originals so existing links keep working.
 */

use App\Core\Router;
use App\Controllers\HomeController;

$router = new Router();

/* -------------------------------------------------------------------------- */
/* Public site                                                                */
/* -------------------------------------------------------------------------- */
$router->get('/', [HomeController::class, 'index']);

/*
 * The remaining areas (admin, students, trainers, courses) are migrated
 * controller-by-controller. As each App\Controllers class is added, register
 * its routes here using the same paths as the legacy tables. See MIGRATION.md
 * for the full path → controller map.
 */

return $router;
