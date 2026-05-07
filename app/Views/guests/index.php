<?php
use App\Core\Auth;
ob_start();
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Módulo · Huéspedes</span>
    <h1 class="page-title">Personas registradas</h1>
  </div>
  <div class="page-head__actions">
    <?php if (Auth::can('guests','create')): ?>
      <a class="btn" href="/guests/create">Nuevo huésped</a>
    <?php endif; ?>
  </div>
</div>

<div class="filters">
  <div class="field" style="flex:1; max-width:480px;">
    <span class="field__label">Buscar</span>
    <input type="search" data-table-search="#guestsTable" placeholder="Nombre, documento o correo">
  </div>
  <div class="muted" style="font-size:12px; margin-bottom:8px;">
    <?= e($total) ?> huéspedes en total
  </div>
</div>

<?php if (!$rows): ?>
  <div class="empty">
    <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5">
      <rect x="14" y="10" width="36" height="48" rx="0"/>
      <line x1="22" y1="22" x2="42" y2="22"/>
      <line x1="22" y1="30" x2="42" y2="30"/>
      <line x1="22" y1="38" x2="34" y2="38"/>
    </svg>
    <div class="empty__title">Sin huéspedes registrados todavía</div>
    <div class="empty__text">Cree el primer huésped para comenzar a registrar reservas.</div>
    <?php if (Auth::can('guests','create')): ?>
      <a class="btn" href="/guests/create">Crear el primero</a>
    <?php endif; ?>
  </div>
<?php else: ?>
  <table class="table" id="guestsTable">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Documento</th>
        <th>Contacto</th>
        <th>Origen</th>
        <th class="text-right">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $g): ?>
        <tr>
          <td>
            <a href="/guests/<?= (int) $g['id'] ?>" style="font-weight:500"><?= e($g['first_name'] . ' ' . $g['last_name']) ?></a>
            <?php if (!empty($g['notes'])): ?>
              <div class="muted" style="font-size:11px; margin-top:4px;"><?= e(mb_strimwidth($g['notes'], 0, 50, '…')) ?></div>
            <?php endif; ?>
          </td>
          <td class="num"><?= e($g['document_type']) ?> <?= e($g['document_number']) ?></td>
          <td>
            <?= e($g['phone'] ?: '—') ?><br>
            <?php if ($g['email']): ?><span class="muted" style="font-size:11px;"><?= e($g['email']) ?></span><?php endif; ?>
          </td>
          <td><?= e($g['city'] ?: '—') ?>, <?= e($g['country']) ?></td>
          <td class="table-actions text-right">
            <a href="/guests/<?= (int) $g['id'] ?>">Ver</a>
            <?php if (Auth::can('guests','edit')): ?>
              <a href="/guests/<?= (int) $g['id'] ?>/edit">Editar</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($pages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?page=<?= $i ?><?= $search ? '&q=' . urlencode($search) : '' ?>" class="<?= $i === $page ? 'is-current' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
