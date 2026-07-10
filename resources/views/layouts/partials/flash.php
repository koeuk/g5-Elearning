<?php
/**
 * One-time success banner. Reads and clears the 'success' flash set on login.
 * Requires Bootstrap (already loaded by both site layouts).
 */
$flash = \App\Core\Session::flash('success');
?>
<?php if ($flash !== null) : ?>
<div class="container-fluid px-4 pt-3">
    <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= e((string) $flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>
