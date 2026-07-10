<?php
/**
 * One-time flash banners. Reads and clears the 'success' / 'error' flashes.
 * Requires Bootstrap (already loaded by the site layouts).
 */
$success = \App\Core\Session::flash('success');
$error   = \App\Core\Session::flash('error');
?>
<?php if ($success !== null) : ?>
<div class="container-fluid px-4 pt-3">
    <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= e((string) $success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>
<?php if ($error !== null) : ?>
<div class="container-fluid px-4 pt-3">
    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= e((string) $error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>
