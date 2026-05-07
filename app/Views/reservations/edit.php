<?php
use App\Core\Csrf;
ob_start();
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Reserva · <?= e($r['code']) ?></span>
    <h1 class="page-title">Editar reserva</h1>
  </div>
  <div class="page-head__actions">
    <a href="/reservations/<?= (int) $r['id'] ?>" class="btn btn--ghost">Cancelar</a>
  </div>
</div>

<form method="post" action="/reservations/<?= (int) $r['id'] ?>" data-reservation-form data-reservation-id="<?= (int) $r['id'] ?>">
  <?= Csrf::field() ?>

  <section style="max-width:680px;">
    <div class="eyebrow" style="margin-bottom:14px;">Huésped</div>
    <div class="autocomplete" data-guest-autocomplete>
      <label class="field">
        <span class="field__label">Buscar por nombre o documento</span>
        <input class="field__input" type="text" data-guest-search value="<?= e(($guest['first_name'] ?? '') . ' ' . ($guest['last_name'] ?? '')) ?>" autocomplete="off">
      </label>
      <input type="hidden" name="guest_id" value="<?= (int) $r['guest_id'] ?>">
      <div class="autocomplete__results" data-guest-results></div>
    </div>
  </section>

  <hr class="hr">

  <section style="max-width:680px;">
    <div class="eyebrow" style="margin-bottom:14px;">Fechas</div>
    <div class="row row--2">
      <label class="field">
        <span class="field__label">Entrada</span>
        <input class="field__input" type="date" name="check_in" required value="<?= e($r['check_in']) ?>">
      </label>
      <label class="field">
        <span class="field__label">Salida</span>
        <input class="field__input" type="date" name="check_out" required value="<?= e($r['check_out']) ?>">
      </label>
    </div>
  </section>

  <hr class="hr">

  <section>
    <div class="eyebrow" style="margin-bottom:14px;">Habitación</div>
    <p class="muted" style="margin-bottom:24px; font-size:13px;">
      La habitación actual está siempre habilitada. Otras se filtran si solapan con una reserva distinta.
    </p>
    <div data-availability>
      <p class="muted"><span class="spinner"></span> cargando…</p>
    </div>
    <input type="hidden" name="room_id" value="<?= (int) $r['room_id'] ?>" required>
  </section>

  <hr class="hr">

  <section style="max-width:680px;">
    <div class="eyebrow" style="margin-bottom:14px;">Detalles</div>
    <div class="row row--3">
      <label class="field">
        <span class="field__label">Adultos</span>
        <input class="field__input" type="number" min="1" max="6" name="adults" required value="<?= (int) $r['adults'] ?>">
      </label>
      <label class="field">
        <span class="field__label">Niños</span>
        <input class="field__input" type="number" min="0" max="6" name="children" required value="<?= (int) $r['children'] ?>">
      </label>
      <label class="field">
        <span class="field__label">Total</span>
        <input class="field__input" type="number" step="1000" name="total_amount" required value="<?= (int) $r['total_amount'] ?>">
        <span class="field__hint" data-price-hint></span>
      </label>
    </div>

    <label class="field">
      <span class="field__label">Notas</span>
      <textarea class="field__textarea" name="notes"><?= e($r['notes']) ?></textarea>
    </label>
  </section>

  <div style="margin-top:24px;">
    <button class="btn" type="submit">Actualizar</button>
    <a href="/reservations/<?= (int) $r['id'] ?>" class="btn btn--ghost">Cancelar</a>
  </div>
</form>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
