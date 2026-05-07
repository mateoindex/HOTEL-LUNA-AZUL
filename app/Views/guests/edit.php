<?php
use App\Core\Csrf;
ob_start();
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Huéspedes · Editar</span>
    <h1 class="page-title"><?= e($g['first_name'] . ' ' . $g['last_name']) ?></h1>
  </div>
  <div class="page-head__actions">
    <a href="/guests/<?= (int) $g['id'] ?>" class="btn btn--ghost">Cancelar</a>
  </div>
</div>

<form method="post" action="/guests/<?= (int) $g['id'] ?>" style="max-width:680px;">
  <?= Csrf::field() ?>

  <div class="row row--2">
    <label class="field">
      <span class="field__label">Tipo de documento</span>
      <select class="field__select" name="document_type" required>
        <?php foreach (['CC'=>'Cédula','CE'=>'Cédula extranjería','PAS'=>'Pasaporte'] as $k=>$v): ?>
          <option value="<?= $k ?>" <?= ($g['document_type'] === $k) ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="field">
      <span class="field__label">Número</span>
      <input class="field__input" name="document_number" required value="<?= e($g['document_number']) ?>">
    </label>
  </div>

  <div class="row row--2">
    <label class="field">
      <span class="field__label">Nombres</span>
      <input class="field__input" name="first_name" required value="<?= e($g['first_name']) ?>">
    </label>
    <label class="field">
      <span class="field__label">Apellidos</span>
      <input class="field__input" name="last_name" required value="<?= e($g['last_name']) ?>">
    </label>
  </div>

  <div class="row row--2">
    <label class="field">
      <span class="field__label">Correo</span>
      <input class="field__input" type="email" name="email" value="<?= e($g['email']) ?>">
    </label>
    <label class="field">
      <span class="field__label">Teléfono</span>
      <input class="field__input" name="phone" value="<?= e($g['phone']) ?>">
    </label>
  </div>

  <div class="row row--2">
    <label class="field">
      <span class="field__label">País</span>
      <input class="field__input" name="country" value="<?= e($g['country']) ?>">
    </label>
    <label class="field">
      <span class="field__label">Ciudad</span>
      <input class="field__input" name="city" value="<?= e($g['city']) ?>">
    </label>
  </div>

  <label class="field">
    <span class="field__label">Notas</span>
    <textarea class="field__textarea" name="notes"><?= e($g['notes']) ?></textarea>
  </label>

  <div style="margin-top:24px;">
    <button class="btn" type="submit">Actualizar</button>
    <a href="/guests/<?= (int) $g['id'] ?>" class="btn btn--ghost">Cancelar</a>
  </div>
</form>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
