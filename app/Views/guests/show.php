<?php
use App\Core\Auth;
use App\Core\Csrf;
ob_start();

$statusBadge = [
    'reservada'  => 'badge--ink',
    'en_curso'   => 'badge--ok',
    'finalizada' => 'badge--neutral',
    'cancelada'  => 'badge--bad',
];
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Huésped · <?= e($g['document_type']) ?> <?= e($g['document_number']) ?></span>
    <h1 class="page-title"><?= e($g['first_name'] . ' ' . $g['last_name']) ?></h1>
  </div>
  <div class="page-head__actions">
    <?php if (Auth::can('guests','edit')): ?>
      <a class="btn btn--ghost" href="/guests/<?= (int) $g['id'] ?>/edit">Editar</a>
    <?php endif; ?>
    <?php if (Auth::can('guests','delete') && empty($reservations)): ?>
      <form method="post" action="/guests/<?= (int) $g['id'] ?>/delete" data-confirm="¿Eliminar este huésped?" style="display:inline">
        <?= Csrf::field() ?>
        <button class="btn btn--danger" type="submit">Eliminar</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<div class="ficha">
  <div>
    <div class="eyebrow" style="margin-bottom:16px;">Datos personales</div>
    <dl class="ficha__data">
      <dt>Documento</dt><dd><?= e($g['document_type']) ?> <?= e($g['document_number']) ?></dd>
      <dt>Correo</dt><dd><?= e($g['email'] ?: '—') ?></dd>
      <dt>Teléfono</dt><dd><?= e($g['phone'] ?: '—') ?></dd>
      <dt>Origen</dt><dd><?= e($g['city'] ?: '—') ?>, <?= e($g['country']) ?></dd>
      <dt>Notas</dt><dd><?= e($g['notes'] ?: '—') ?></dd>
      <dt>Registrado</dt><dd><?= e(date_es($g['created_at'])) ?></dd>
    </dl>
  </div>

  <div class="ficha__side">
    <div class="eyebrow" style="margin-bottom:12px;">Resumen</div>
    <div class="big-num"><?= count($reservations) ?></div>
    <div class="muted" style="margin-top:6px;">reservas históricas</div>
  </div>
</div>

<section style="margin-top:56px;">
  <div class="eyebrow" style="margin-bottom:18px;">Historial de reservas</div>
  <?php if (!$reservations): ?>
    <p class="muted">Sin reservas todavía.</p>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>Código</th>
          <th>Habitación</th>
          <th>Entrada</th>
          <th>Salida</th>
          <th>Noches</th>
          <th>Estado</th>
          <th class="text-right">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reservations as $r): ?>
          <tr>
            <td class="num"><a href="/reservations/<?= (int) $r['id'] ?>"><?= e($r['code']) ?></a></td>
            <td><?= e($r['room_code']) ?> · <?= e($r['room_type']) ?></td>
            <td><?= e(date_es($r['check_in'])) ?></td>
            <td><?= e(date_es($r['check_out'])) ?></td>
            <td class="num"><?= (int) $r['nights'] ?></td>
            <td><span class="badge <?= $statusBadge[$r['status']] ?? '' ?>"><?= e(str_replace('_', ' ', $r['status'])) ?></span></td>
            <td class="num text-right"><?= e(money($r['total_amount'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
