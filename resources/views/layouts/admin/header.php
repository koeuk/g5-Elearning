<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>E-Learning Backend</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Theme: set data-theme before paint to avoid a flash (shared key with the student UI). -->
    <script>(function(){try{var s=localStorage.getItem('eLearnTheme');document.documentElement.setAttribute('data-theme',s||((window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'));}catch(e){document.documentElement.setAttribute('data-theme','dark');}})();</script>

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet"> 
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="vendor/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="vendor/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="vendor/css/admin.css" rel="stylesheet">

    <!-- Dark/light theming layer (must come after admin.css to override it).
         Cache-busted by file mtime so edits always reach the browser. -->
    <link href="assets/admin-theme.css?v=<?= @filemtime(BASE_PATH . '/public/assets/admin-theme.css') ?: '1' ?>" rel="stylesheet">

    <!--
        Shared admin-modal form styling. The dark admin theme styles
        .form-control dark, which is unreadable inside the white modals
        (Add Student / Category / Trainer, etc.). Make every modal form
        input fit the white modal, with an orange (brand) focus.
    -->
    <style>
        .modal-body .form-control,
        .modal-body .form-select {
            background-color: #fff;
            color: #212529;
            border: 1px solid #ced4da;
        }
        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            background-color: #fff;
            color: #212529;
            border-color: #F28500;
            box-shadow: 0 0 0 .2rem rgba(242, 133, 0, .25);
        }
        .modal-body .form-floating > label { color: #6c757d; }
        .modal-body .form-floating > .form-control:focus ~ label,
        .modal-body .form-floating > .form-control:not(:placeholder-shown) ~ label { color: #F28500; }
    </style>
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <!-- <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div> -->
        <!-- Spinner End -->