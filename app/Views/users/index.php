<?php
use App\Core\Auth;
use App\Core\Csrf;
ob_start();
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Administración · Usuarios</span>
    <h1 class="page-title">Personal del sistema</h1>
  </div>
  <div class="page-head__actions">
    <a class="btn" href="/users/create">Nuevo usuario</a>
  </div>
</div>

<table class="table">
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Correo</th>
      <th>Rol</th>
      <th>Último acceso</th>
      <th>Estado</th>
      <th class="text-right">Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $u): ?>
      <tr>
        <td><?= e($u['full_name']) ?></td>
        <td class="mono" style="font-size:12px;"><?= e($u['email']) ?></td>
        <td><?= e($u['role_display']) ?></td>
        <td class="muted" style="font-size:12px;">
          <?= e($u['last_login_at'] ? date_es($u['last_login_at'], 'd \d\e M Y H:i') : 'nunca') ?>
        </td>
        <td>
          <?php if ((int) $u['is_active'] === 1): ?>
            <span class="badge badge--ok">activo</span>
          <?php else: ?>
            <span class="badge badge--bad">inactivo</span>
          <?php endif; ?>
        </td>
        <td class="table-actions text-right">
          <a href="/users/<?= (int) $u['id'] ?>/edit">Editar</a>
          <?php if ((int) $u['is_active'] === 1 && (int) $u['id'] !== Auth::id()): ?>
            <form method="post" action="/users/<?= (int) $u['id'] ?>/deactivate" data-confirm="¿Desactivar este usuario?" style="display:inline">
              <?= Csrf::field() ?>
              <button class="danger" type="submit">Desactivar</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
