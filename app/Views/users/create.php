<?php
use App\Core\Csrf;
ob_start();
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Usuarios · Nuevo</span>
    <h1 class="page-title">Crear usuario</h1>
  </div>
  <div class="page-head__actions">
    <a href="/users" class="btn btn--ghost">Cancelar</a>
  </div>
</div>

<form method="post" action="/users" style="max-width:520px;">
  <?= Csrf::field() ?>
  <label class="field">
    <span class="field__label">Nombre completo</span>
    <input class="field__input" name="full_name" required value="<?= e(old('full_name')) ?>">
  </label>
  <label class="field">
    <span class="field__label">Correo</span>
    <input class="field__input" type="email" name="email" required value="<?= e(old('email')) ?>">
  </label>
  <label class="field">
    <span class="field__label">Contraseña</span>
    <input class="field__input" type="password" name="password" required minlength="6">
    <span class="field__hint">Mínimo 6 caracteres.</span>
  </label>
  <label class="field">
    <span class="field__label">Rol</span>
    <select class="field__select" name="role_id" required>
      <?php foreach ($roles as $r): ?>
        <option value="<?= (int) $r['id'] ?>"><?= e($r['display_name']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button class="btn" type="submit">Guardar</button>
  <a class="btn btn--ghost" href="/users">Cancelar</a>
</form>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
