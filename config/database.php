<?php

/*
 * Database credentials.
 * Values come from environment variables (see .env.example) and fall back to
 * sensible local defaults so the app still runs on a fresh machine.
 */

return [
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_NAME') ?: 'e_learning_db',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') ?: '',
];
