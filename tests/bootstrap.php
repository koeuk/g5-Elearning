<?php

/*
 * PHPUnit bootstrap.
 *
 * Mirrors what public/index.php does at the start of a web request: load the
 * Composer autoloader and then the .env file, so DB-backed tests connect with
 * the same credentials as the running app instead of requiring the caller to
 * export the variables by hand. Real environment variables still win — Env::load
 * never overwrites a value that is already set.
 */

require __DIR__ . '/../vendor/autoload.php';

App\Core\Env::load(__DIR__ . '/../.env');
