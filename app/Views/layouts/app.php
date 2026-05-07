<?php
use App\Core\Auth;
$user = Auth::user();
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'Hotel Luna Azul') ?> · Hotel Luna Azul</title>
  <link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/reset.css">
  <link rel="stylesheet" href="/assets/css/tokens.css">
  <link rel="stylesheet" href="/assets/css/typography.css">
  <link rel="stylesheet" href="/assets/css/layout.css">
  <link rel="stylesheet" href="/assets/css/components.css">
  <link rel="stylesheet" href="/assets/css/modules.css">
</head>
<body>

<div class="app-shell">
  <?php require base_path('app/Views/partials/sidebar.php'); ?>

  <main class="main">
    <?php require base_path('app/Views/partials/topbar.php'); ?>
    <?php require base_path('app/Views/partials/flash.php'); ?>

    <?= $content ?>

    <hr class="hr">
    <p class="muted" style="font-size:11px; letter-spacing:0.05em;">Hotel Luna Azul · Cartagena · 2026</p>
  </main>
</div>

<div class="modal-back" id="confirmModal"></div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/modal.js"></script>
<script src="/assets/js/tables.js"></script>
<script src="/assets/js/reservations.js"></script>
</body>
</html>
