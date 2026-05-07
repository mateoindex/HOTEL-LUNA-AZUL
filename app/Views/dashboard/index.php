<?php
ob_start();
$first = explode(' ', $user['full_name'] ?? '')[0];
$today = today();

// hora del saludo
$h = (int) date('H');
$saludo = $h < 12 ? 'Buenos días' : ($h < 19 ? 'Buenas tardes' : 'Buenas noches');
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Panel · <?= strtoupper(date_es($today, 'd \d\e M, Y')) ?></span>
    <h1 class="page-title"><?= e($saludo) ?>, <?= e($first) ?>.</h1>
    <p class="muted" style="margin-top:14px; font-size:13px; max-width:42ch;">
      Resumen de la operación del hotel al día de hoy. Las cifras se actualizan en tiempo real.
    </p>
  </div>
  <div class="page-head__actions">
    <a class="btn btn--ghost" href="/reservations">Ver reservas</a>
    <a class="btn" href="/reservations/create">Nueva reserva</a>
  </div>
</div>

<div class="kpi-row">
  <div class="kpi">
    <div class="kpi__label">Reservas activas</div>
    <div class="kpi__num"><?= e($active) ?></div>
    <div class="kpi__hint">en curso o por iniciar</div>
  </div>
  <div class="kpi">
    <div class="kpi__label">Llegadas hoy</div>
    <div class="kpi__num"><?= e(count($arrivals)) ?></div>
    <div class="kpi__hint">previstas para entrar</div>
  </div>
  <div class="kpi">
    <div class="kpi__label">Salidas hoy</div>
    <div class="kpi__num"><?= e(count($departures)) ?></div>
    <div class="kpi__hint">previstas para retirarse</div>
  </div>
  <div class="kpi">
    <div class="kpi__label">Ocupación</div>
    <div class="kpi__num"><?= e($occupancy) ?>%</div>
    <div class="kpi__hint">de habitaciones operativas</div>
  </div>
</div>

<div class="dash-cols">
  <section class="dash-col">
    <div class="dash-col__head">
      <span class="eyebrow">Llegadas de hoy</span>
      <span class="muted" style="font-size:11px;"><?= count($arrivals) ?> previstas</span>
    </div>
    <?php if (!$arrivals): ?>
      <p class="muted" style="padding: 24px 0;">Sin llegadas previstas para hoy.</p>
    <?php else: ?>
      <div class="listing">
        <?php foreach ($arrivals as $a): ?>
          <a href="/reservations/<?= (int) $a['id'] ?>" class="listing__item">
            <div>
              <div class="listing__name"><?= e($a['first_name'] . ' ' . $a['last_name']) ?></div>
              <div class="listing__meta">Habitación <?= e($a['room_code']) ?> · <?= (int) $a['nights'] ?> noche<?= ((int) $a['nights']) > 1 ? 's' : '' ?></div>
            </div>
            <span class="listing__code"><?= e($a['code']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <section class="dash-col">
    <div class="dash-col__head">
      <span class="eyebrow">Salidas de hoy</span>
      <span class="muted" style="font-size:11px;"><?= count($departures) ?> previstas</span>
    </div>
    <?php if (!$departures): ?>
      <p class="muted" style="padding: 24px 0;">Sin salidas previstas para hoy.</p>
    <?php else: ?>
      <div class="listing">
        <?php foreach ($departures as $d): ?>
          <a href="/reservations/<?= (int) $d['id'] ?>" class="listing__item">
            <div>
              <div class="listing__name"><?= e($d['first_name'] . ' ' . $d['last_name']) ?></div>
              <div class="listing__meta">Habitación <?= e($d['room_code']) ?> · sale hoy</div>
            </div>
            <span class="listing__code"><?= e($d['code']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <aside class="dash-aside">
    <div class="eyebrow" style="margin-bottom: 16px;">Acciones rápidas</div>
    <div class="quick-stack">
      <a href="/reservations/create" class="quick-link">
        <span class="quick-link__title">Nueva reserva</span>
        <span class="quick-link__sub">Crear con disponibilidad en vivo</span>
      </a>
      <a href="/guests" class="quick-link">
        <span class="quick-link__title">Buscar huésped</span>
        <span class="quick-link__sub">Listado y ficha histórica</span>
      </a>
      <a href="/rooms" class="quick-link">
        <span class="quick-link__title">Mapa de habitaciones</span>
        <span class="quick-link__sub">Estado al día de hoy</span>
      </a>
      <a href="/reports" class="quick-link">
        <span class="quick-link__title">Reportes</span>
        <span class="quick-link__sub">Voucher · ocupación · PDF</span>
      </a>
    </div>
  </aside>
</div>

<section style="margin-top: 56px;">
  <div class="dash-col__head" style="margin-bottom: 24px;">
    <span class="eyebrow">Ocupación · próximos 7 días</span>
    <span class="muted" style="font-size:11px;">desde <?= e(date_es($today)) ?></span>
  </div>
  <div class="chart">
    <?php foreach ($week as $w):
      $dayLabel = strtoupper(date_es($w['date'], 'D d'));
      $h = max(2, $w['pct']);
    ?>
      <div class="chart__bar">
        <div class="chart__col" style="height: <?= (int) $h ?>%"></div>
        <div class="chart__value"><?= (int) $w['pct'] ?>%</div>
        <div class="chart__label"><?= e($dayLabel) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
