<?php

/*
 * Opens the shared PDO connection used across the app.
 * Included by controllers/models/views as `require 'database/database.php'`
 * (resolved via the include_path set in public/index.php).
 */

$config = require dirname(__DIR__, 2) . '/config/database.php';

$dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
$connection = new PDO($dsn, $config['username'], $config['password']);
