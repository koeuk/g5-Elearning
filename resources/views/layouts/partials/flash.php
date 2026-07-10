<?php
/**
 * One-time flash toasts. Reads and clears the 'success' / 'error' flashes and
 * shows them as a fixed toast in the bottom-right corner (auto-dismissing).
 * Requires Bootstrap (already loaded by the site layouts).
 */
$success = \App\Core\Session::flash('success');
$error   = \App\Core\Session::flash('error');
?>
<?php if ($success !== null || $error !== null) : ?>
<div class="flash-toasts" style="position: fixed; bottom: 1rem; right: 1rem; z-index: 1080; max-width: 360px;">
    <?php if ($success !== null) : ?>
    <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= e((string) $success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    <?php if ($error !== null) : ?>
    <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= e((string) $error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
</div>
<script>
    // Auto-dismiss the flash toasts after 5 seconds.
    setTimeout(function () {
        document.querySelectorAll('.flash-toasts .alert').forEach(function (el) {
            el.classList.remove('show');
            setTimeout(function () { el.remove(); }, 200);
        });
    }, 5000);
</script>
<?php endif; ?>
