<?php
use App\Core\Csrf;
ob_start();
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Habitación · <?= e($r['code']) ?></span>
    <h1 class="page-title">Editar habitación</h1>
  </div>
  <div class="page-head__actions">
    <a href="/rooms" class="btn btn--ghost">Cancelar</a>
  </div>
</div>

<form method="post" action="/rooms/<?= (int) $r['id'] ?>" style="max-width:680px;">
  <?= Csrf::field() ?>

  <div class="row row--2">
    <label class="field">
      <span class="field__label">Código</span>
      <input class="field__input" name="code" required maxlength="10" value="<?= e($r['code']) ?>">
    </label>
    <label class="field">
      <span class="field__label">Tipo</span>
      <select class="field__select" name="type" required>
        <?php foreach (['estandar','superior','suite'] as $t): ?>
          <option value="<?= $t ?>" <?= $r['type'] === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
  </div>

  <div class="row row--3">
    <label class="field">
      <span class="field__label">Capacidad</span>
      <input class="field__input" type="number" min="1" max="6" name="capacity" required value="<?= (int) $r['capacity'] ?>">
    </label>
    <label class="field">
      <span class="field__label">Piso</span>
      <input class="field__input" type="number" min="1" name="floor" required value="<?= (int) $r['floor'] ?>">
    </label>
    <label class="field">
      <span class="field__label">Precio / noche</span>
      <input class="field__input" type="number" step="1000" min="0" name="price_per_night" required value="<?= (int) $r['price_per_night'] ?>">
    </label>
  </div>

  <label class="field">
    <span class="field__label">Estado</span>
    <select class="field__select" name="status" required>
      <?php foreach (['disponible','mantenimiento','fuera_servicio'] as $s): ?>
        <option value="<?= $s ?>" <?= $r['status'] === $s ? 'selected' : '' ?>><?= str_replace('_',' ',$s) ?></option>
      <?php endforeach; ?>
    </select>
  </label>

  <label class="field">
    <span class="field__label">Descripción</span>
    <textarea class="field__textarea" name="description"><?= e($r['description']) ?></textarea>
  </label>

  <div style="margin-top:24px;">
    <button class="btn" type="submit">Actualizar</button>
    <a href="/rooms" class="btn btn--ghost">Cancelar</a>
  </div>
</form>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
