<?php
use App\Core\Auth;
use App\Core\Csrf;
ob_start();

$badge = [
    'reservada'  => 'badge--ink',
    'en_curso'   => 'badge--ok',
    'finalizada' => 'badge--neutral',
    'cancelada'  => 'badge--bad',
];
$canCheckIn  = $r['status'] === 'reservada';
$canCheckOut = $r['status'] === 'en_curso';
$canCancel   = in_array($r['status'], ['reservada','en_curso'], true);
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Reserva</span>
    <h1 class="page-title mono" style="font-family: var(--font-display); font-weight: 400;"><?= e($r['code']) ?></h1>
    <div style="margin-top:12px;">
      <span class="badge <?= $badge[$r['status']] ?? '' ?>"><?= e(str_replace('_',' ',$r['status'])) ?></span>
    </div>
  </div>
  <div class="page-head__actions">
    <a class="btn btn--ghost" href="/reservations/<?= (int) $r['id'] ?>/pdf" target="_blank">Voucher PDF</a>
    <?php if (Auth::can('reservations','edit')): ?>
      <a class="btn btn--ghost" href="/reservations/<?= (int) $r['id'] ?>/edit">Editar</a>
    <?php endif; ?>
  </div>
</div>

<div class="ficha">
  <div>
    <div class="eyebrow" style="margin-bottom:16px;">Huésped</div>
    <dl class="ficha__data">
      <dt>Nombre</dt><dd><a href="/guests/<?= (int) $r['guest_id'] ?>" class="brass"><?= e($r['first_name'] . ' ' . $r['last_name']) ?></a></dd>
      <dt>Documento</dt><dd><?= e($r['document_type']) ?> <?= e($r['document_number']) ?></dd>
      <dt>Teléfono</dt><dd><?= e($r['phone'] ?: '—') ?></dd>
      <dt>Correo</dt><dd><?= e($r['guest_email'] ?: '—') ?></dd>
    </dl>

    <div class="eyebrow" style="margin:32px 0 16px;">Habitación</div>
    <dl class="ficha__data">
      <dt>Habitación</dt><dd><?= e($r['room_code']) ?> · <?= e($r['room_type']) ?> (<?= (int) $r['capacity'] ?> pax)</dd>
      <dt>Piso</dt><dd><?= (int) $r['floor'] ?></dd>
      <dt>Tarifa / noche</dt><dd><?= e(money($r['price_per_night'])) ?></dd>
    </dl>

    <div class="eyebrow" style="margin:32px 0 16px;">Estancia</div>
    <dl class="ficha__data">
      <dt>Entrada</dt><dd><?= e(date_es($r['check_in'])) ?></dd>
      <dt>Salida</dt><dd><?= e(date_es($r['check_out'])) ?></dd>
      <dt>Noches</dt><dd><?= (int) $r['nights'] ?></dd>
      <dt>Adultos / niños</dt><dd><?= (int) $r['adults'] ?> · <?= (int) $r['children'] ?></dd>
      <dt>Notas</dt><dd><?= e($r['notes'] ?: '—') ?></dd>
      <dt>Creada por</dt><dd><?= e($r['created_by_name']) ?> · <?= e(date_es($r['created_at'], 'd \d\e M Y H:i')) ?></dd>
    </dl>
  </div>

  <div class="ficha__side">
    <div class="eyebrow" style="margin-bottom:12px;">Total</div>
    <div class="big-num"><?= e(money($r['total_amount'])) ?></div>
    <div class="muted" style="margin-top:6px;"><?= (int) $r['nights'] ?> noche<?= ((int) $r['nights']) > 1 ? 's' : '' ?> × <?= e(money($r['price_per_night'])) ?></div>

    <hr class="hr" style="margin: 32px 0;">

    <div class="eyebrow" style="margin-bottom:14px;">Acciones</div>
    <div class="stack" style="display:flex; flex-direction:column; gap:8px;">

      <?php if ($canCheckIn): ?>
        <form method="post" action="/reservations/<?= (int) $r['id'] ?>/check-in">
          <?= Csrf::field() ?>
          <button class="btn btn--block" type="submit">Marcar entrada</button>
        </form>
      <?php endif; ?>

      <?php if ($canCheckOut): ?>
        <form method="post" action="/reservations/<?= (int) $r['id'] ?>/check-out">
          <?= Csrf::field() ?>
          <button class="btn btn--block" type="submit">Marcar salida</button>
        </form>
      <?php endif; ?>

      <?php if ($canCancel && Auth::can('reservations','edit')): ?>
        <form method="post" action="/reservations/<?= (int) $r['id'] ?>/cancel" data-confirm="¿Cancelar esta reserva?">
          <?= Csrf::field() ?>
          <button class="btn btn--danger btn--block" type="submit">Cancelar reserva</button>
        </form>
      <?php endif; ?>

      <a class="btn btn--ghost btn--block" href="/reservations/<?= (int) $r['id'] ?>/pdf" target="_blank">Generar voucher PDF</a>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
