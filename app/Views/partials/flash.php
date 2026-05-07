<?php
$ok  = flash('ok');
$bad = flash('bad');
$errors = $_SESSION['_errors'] ?? null;
if (isset($_SESSION['_errors'])) unset($_SESSION['_errors']);
if (isset($_SESSION['_old']))    unset($_SESSION['_old']);
?>
<?php if ($ok): ?>
  <div class="alert alert--ok"><?= e($ok) ?></div>
<?php endif; ?>
<?php if ($bad): ?>
  <div class="alert alert--bad"><?= e($bad) ?></div>
<?php endif; ?>
<?php if (!empty($errors) && is_array($errors)): ?>
  <div class="alert alert--bad">
    <strong>Revisa los datos:</strong>
    <ul style="margin:6px 0 0 16px;">
      <?php foreach ($errors as $f => $msg): ?>
        <li><?= e(is_array($msg) ? $msg[0] : $msg) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
