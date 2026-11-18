<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Vehicle Record System';
$current   = basename($_SERVER['PHP_SELF']);
$flash     = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Vehicle Record System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark app-nav">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-car-front-fill me-2"></i>Vehicle Records</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?= $current === 'index.php' ? 'active' : '' ?>" href="index.php">All vehicles</a></li>
                <li class="nav-item"><a class="nav-link <?= $current === 'create.php' ? 'active' : '' ?>" href="create.php">Add vehicle</a></li>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
