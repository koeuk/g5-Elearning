<?php
/**
 * Minimal authenticated student landing. Standalone page (own <head>/<body>).
 *
 * @var array<string, mixed> $student the session user (name, email, ...)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Account</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body p-4 p-sm-5 text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-circle text-secondary" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="mb-1">Welcome, <?= e($student['name'] ?? 'Student') ?>!</h3>
                        <p class="text-muted mb-4"><?= e($student['email'] ?? '') ?></p>

                        <div class="d-grid gap-2 col-10 mx-auto">
                            <a href="/" class="btn btn-primary"><i class="bi bi-mortarboard me-1"></i> Browse courses</a>
                            <a href="/orders" class="btn btn-outline-primary"><i class="bi bi-cart me-1"></i> My cart &amp; orders</a>
                            <a href="/logout" class="btn btn-link text-danger">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
