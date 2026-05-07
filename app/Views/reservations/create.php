<?php
use App\Core\Csrf;
ob_start();
$today = today();
$tomorrow = date('Y-m-d', strtotime('+1 day'));
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Reservas · Nueva</span>
    <h1 class="page-title">Crear reserva</h1>
  </div>
  <div class="page-head__actions">
    <a href="/reservations" class="btn btn--ghost">Cancelar</a>
  </div>
</div>

<div class="steps">
  <div class="step is-active"><span class="step__num">01</span> Huésped</div>
  <div class="step is-active"><span class="step__num">02</span> Fechas</div>
  <div class="step is-active"><span class="step__num">03</span> Habitación</div>
  <div class="step is-active"><span class="step__num">04</span> Confirmar</div>
</div>

<form method="post" action="/reservations" data-reservation-form>
  <?= Csrf::field() ?>

  <section style="max-width:680px;">
    <div class="eyebrow" style="margin-bottom:14px;">01 · Huésped</div>
    <div class="autocomplete" data-guest-autocomplete>
      <label class="field">
        <span class="field__label">Buscar por nombre o documento</span>
        <input class="field__input" type="text" data-guest-search placeholder="Empiece a escribir…" autocomplete="off" value="<?= e(old('guest_name')) ?>">
      </label>
      <input type="hidden" name="guest_id" value="<?= e(old('guest_id')) ?>">
      <div class="autocomplete__results" data-guest-results></div>
    </div>
    <p class="muted" style="font-size:12px; margin-top:-12px;">¿Huésped nuevo? <a href="/guests/create" target="_blank" class="brass">Regístrelo aquí</a> y vuelva.</p>
  </section>

  <hr class="hr">

  <section style="max-width:680px;">
    <div class="eyebrow" style="margin-bottom:14px;">02 · Fechas</div>
    <div class="row row--2">
      <label class="field">
        <span class="field__label">Entrada</span>
        <input class="field__input" type="date" name="check_in" required min="<?= $today ?>" value="<?= e(old('check_in', $today)) ?>">
      </label>
      <label class="field">
        <span class="field__label">Salida</span>
        <input class="field__input" type="date" name="check_out" required min="<?= $tomorrow ?>" value="<?= e(old('check_out', $tomorrow)) ?>">
      </label>
    </div>
  </section>

  <hr class="hr">

  <section>
    <div class="eyebrow" style="margin-bottom:14px;">03 · Habitación</div>
    <p class="muted" style="margin-bottom:24px; font-size:13px;">
      Solo se muestran habitaciones disponibles para las fechas elegidas. Las que se cruzan con otra reserva quedan filtradas automáticamente.
    </p>
    <div data-availability>
      <p class="muted">Seleccione fechas para ver las habitaciones libres.</p>
    </div>
    <input type="hidden" name="room_id" value="<?= e(old('room_id')) ?>" required>
  </section>

  <hr class="hr">

  <section style="max-width:680px;">
    <div class="eyebrow" style="margin-bottom:14px;">04 · Detalles</div>
    <div class="row row--3">
      <label class="field">
        <span class="field__label">Adultos</span>
        <input class="field__input" type="number" min="1" max="6" name="adults" required value="<?= e(old('adults', 1)) ?>">
      </label>
      <label class="field">
        <span class="field__label">Niños</span>
        <input class="field__input" type="number" min="0" max="6" name="children" required value="<?= e(old('children', 0)) ?>">
      </label>
      <label class="field">
        <span class="field__label">Total</span>
        <input class="field__input" type="number" step="1000" name="total_amount" required value="<?= e(old('total_amount')) ?>">
        <span class="field__hint" data-price-hint></span>
      </label>
    </div>

    <label class="field">
      <span class="field__label">Notas</span>
      <textarea class="field__textarea" name="notes"><?= e(old('notes')) ?></textarea>
    </label>
  </section>

  <div style="margin-top:24px;">
    <button class="btn" type="submit">Confirmar reserva</button>
    <a href="/reservations" class="btn btn--ghost">Cancelar</a>
  </div>
</form>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
