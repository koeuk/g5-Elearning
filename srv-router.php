<?php
$p = parse_url($_SERVER['REQUEST_URI'])['path'];
$file = __DIR__ . '/public' . $p;
if ($p !== '/' && is_file($file)) { return false; }   // let built-in server serve static asset
require __DIR__ . '/public/index.php';
