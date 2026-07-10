<?php
/**
 * Admin dashboard layout. Expects $content (rendered page HTML) in scope.
 *
 * @var string $content
 */
use App\Core\View;

echo View::partial('layouts/admin/header');
echo View::partial('layouts/admin/navbar');
echo View::partial('layouts/partials/flash');
echo $content ?? '';
echo View::partial('layouts/admin/footer');
