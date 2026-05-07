<?php
ob_start();
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Módulo · Auditoría</span>
    <h1 class="page-title">Bitácora del sistema</h1>
  </div>
</div>

<p class="muted" style="margin-bottom:32px; font-size:13px;">
  Cada acción importante (crear/editar/eliminar) queda registrada en archivos JSON diarios bajo <code class="mono">storage/audit/</code>.
  Esta es la capa no-relacional del sistema.
</p>

<form method="get" class="filters">
  <label class="field">
    <span class="field__label">Día</span>
    <select class="field__select" name="date">
      <?php if (!$dates): ?>
        <option value="<?= today() ?>"><?= e(date_es(today())) ?></option>
      <?php endif; ?>
      <?php foreach ($dates as $d): ?>
        <option value="<?= e($d) ?>" <?= $date === $d ? 'selected' : '' ?>><?= e(date_es($d)) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="field">
    <span class="field__label">Acción</span>
    <select class="field__select" name="action">
      <option value="">Todas</option>
      <?php foreach ($actions as $a): ?>
        <option value="<?= e($a) ?>" <?= $filterAction === $a ? 'selected' : '' ?>><?= e($a) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="field">
    <span class="field__label">Usuario</span>
    <input class="field__input" type="text" name="user" value="<?= e($filterUser) ?>" placeholder="Nombre">
  </label>
  <button class="btn btn--small" type="submit">Filtrar</button>
</form>

<?php if (!$entries): ?>
  <div class="empty">
    <div class="empty__title">Sin actividad para este día</div>
    <div class="empty__text">Pruebe seleccionar otra fecha en el filtro.</div>
  </div>
<?php else: ?>
  <table class="table">
    <thead>
      <tr>
        <th>Hora</th>
        <th>Usuario</th>
        <th>Rol</th>
        <th>Acción</th>
        <th>Entidad</th>
        <th>IP</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($entries as $i => $e):
        $ts = $e['ts'] ?? '';
        $time = $ts ? date('H:i:s', strtotime($ts)) : '';
        $entId = $e['id'] ?? ('row' . $i);
      ?>
        <tr class="audit-row">
          <td class="mono"><?= e($time) ?></td>
          <td><?= e($e['actor']['name'] ?? '?') ?></td>
          <td class="muted" style="font-size:11px;"><?= e($e['actor']['role'] ?? '?') ?></td>
          <td class="mono"><?= e($e['action'] ?? '') ?></td>
          <td><?= e(($e['entity']['type'] ?? '') . (isset($e['entity']['id']) ? ' #' . $e['entity']['id'] : '')) ?></td>
          <td class="muted mono" style="font-size:11px;"><?= e($e['ip'] ?? '') ?></td>
          <td class="text-right">
            <button type="button" class="table-actions" data-audit-toggle="<?= e($entId) ?>">ver datos</button>
          </td>
        </tr>
        <tr>
          <td colspan="7" style="padding:0; border:0;">
            <div class="audit-row__data" data-audit-data="<?= e($entId) ?>"><?= e(json_encode($e, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
