<?php
/**
 * Admin dashboard layout. Expects $content (rendered page HTML) in scope.
 *
 * @var string $content
 */
use App\Core\View;

echo View::partial('layouts/admin/header');
echo View::partial('layouts/admin/navbar');
echo $content ?? '';
echo View::partial('layouts/admin/footer');
