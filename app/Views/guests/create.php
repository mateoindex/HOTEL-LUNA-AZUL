<?php
use App\Core\Csrf;
ob_start();
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Huéspedes · Nuevo registro</span>
    <h1 class="page-title">Registrar huésped</h1>
  </div>
  <div class="page-head__actions">
    <a href="/guests" class="btn btn--ghost">Cancelar</a>
  </div>
</div>

<form method="post" action="/guests" style="max-width:680px;">
  <?= Csrf::field() ?>

  <div class="row row--2">
    <label class="field">
      <span class="field__label">Tipo de documento</span>
      <select class="field__select" name="document_type" required>
        <?php foreach (['CC'=>'Cédula','CE'=>'Cédula extranjería','PAS'=>'Pasaporte'] as $k=>$v): ?>
          <option value="<?= $k ?>" <?= old('document_type') === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="field">
      <span class="field__label">Número</span>
      <input class="field__input" name="document_number" required value="<?= e(old('document_number')) ?>">
    </label>
  </div>

  <div class="row row--2">
    <label class="field">
      <span class="field__label">Nombres</span>
      <input class="field__input" name="first_name" required value="<?= e(old('first_name')) ?>">
    </label>
    <label class="field">
      <span class="field__label">Apellidos</span>
      <input class="field__input" name="last_name" required value="<?= e(old('last_name')) ?>">
    </label>
  </div>

  <div class="row row--2">
    <label class="field">
      <span class="field__label">Correo</span>
      <input class="field__input" type="email" name="email" value="<?= e(old('email')) ?>">
    </label>
    <label class="field">
      <span class="field__label">Teléfono</span>
      <input class="field__input" name="phone" value="<?= e(old('phone')) ?>">
    </label>
  </div>

  <div class="row row--2">
    <label class="field">
      <span class="field__label">País</span>
      <input class="field__input" name="country" value="<?= e(old('country', 'Colombia')) ?>">
    </label>
    <label class="field">
      <span class="field__label">Ciudad</span>
      <input class="field__input" name="city" value="<?= e(old('city')) ?>">
    </label>
  </div>

  <label class="field">
    <span class="field__label">Notas</span>
    <textarea class="field__textarea" name="notes"><?= e(old('notes')) ?></textarea>
  </label>

  <div style="margin-top:24px;">
    <button class="btn" type="submit">Guardar huésped</button>
    <a href="/guests" class="btn btn--ghost">Cancelar</a>
  </div>
</form>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
