<?php
use App\Core\Auth;
ob_start();

$badge = [
    'reservada'  => 'badge--ink',
    'en_curso'   => 'badge--ok',
    'finalizada' => 'badge--neutral',
    'cancelada'  => 'badge--bad',
];
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Módulo · Reservas</span>
    <h1 class="page-title">Reservas</h1>
  </div>
  <div class="page-head__actions">
    <?php if (Auth::can('reservations','create')): ?>
      <a class="btn" href="/reservations/create">Nueva reserva</a>
    <?php endif; ?>
  </div>
</div>

<form method="get" class="filters">
  <label class="field">
    <span class="field__label">Desde</span>
    <input type="date" class="field__input" name="from" value="<?= e($f['from']) ?>">
  </label>
  <label class="field">
    <span class="field__label">Hasta</span>
    <input type="date" class="field__input" name="to" value="<?= e($f['to']) ?>">
  </label>
  <label class="field">
    <span class="field__label">Estado</span>
    <select class="field__select" name="status">
      <option value="">Todos</option>
      <?php foreach (['reservada','en_curso','finalizada','cancelada'] as $s): ?>
        <option value="<?= $s ?>" <?= $f['status'] === $s ? 'selected' : '' ?>><?= str_replace('_',' ',$s) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="field">
    <span class="field__label">Habitación</span>
    <select class="field__select" name="room_id">
      <option value="">Todas</option>
      <?php foreach ($rooms as $rm): ?>
        <option value="<?= (int) $rm['id'] ?>" <?= ((int) $f['room_id'] === (int) $rm['id']) ? 'selected' : '' ?>>
          <?= e($rm['code']) ?> · <?= e($rm['type']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
  <button class="btn btn--small" type="submit">Filtrar</button>
  <a class="btn btn--ghost btn--small" href="/reservations">Limpiar</a>
</form>

<?php if (!$rows): ?>
  <div class="empty">
    <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5">
      <rect x="8" y="14" width="48" height="44"/>
      <line x1="20" y1="6" x2="20" y2="20"/>
      <line x1="44" y1="6" x2="44" y2="20"/>
      <line x1="8" y1="28" x2="56" y2="28"/>
    </svg>
    <div class="empty__title">Sin reservas en este filtro</div>
    <div class="empty__text">Ajuste el rango de fechas o el estado.</div>
  </div>
<?php else: ?>
  <table class="table">
    <thead>
      <tr>
        <th>Código</th>
        <th>Huésped</th>
        <th>Habitación</th>
        <th>Entrada</th>
        <th>Salida</th>
        <th>Noches</th>
        <th>Estado</th>
        <th class="text-right">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="num"><a href="/reservations/<?= (int) $r['id'] ?>"><?= e($r['code']) ?></a></td>
          <td><?= e($r['first_name'] . ' ' . $r['last_name']) ?>
            <div class="muted" style="font-size:11px;"><?= e($r['document_number']) ?></div>
          </td>
          <td><?= e($r['room_code']) ?> · <?= e($r['room_type']) ?></td>
          <td><?= e(date_es($r['check_in'])) ?></td>
          <td><?= e(date_es($r['check_out'])) ?></td>
          <td class="num"><?= (int) $r['nights'] ?></td>
          <td><span class="badge <?= $badge[$r['status']] ?? '' ?>"><?= e(str_replace('_',' ',$r['status'])) ?></span></td>
          <td class="num text-right"><?= e(money($r['total_amount'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
