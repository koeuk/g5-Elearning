<?php
/**
 * Public site layout. Expects $content (rendered page HTML) in scope.
 *
 * @var string $content
 */
use App\Core\View;

echo View::partial('layouts/public/header');
echo View::partial('layouts/public/navbar');
echo $content ?? '';
echo View::partial('layouts/public/footer');
