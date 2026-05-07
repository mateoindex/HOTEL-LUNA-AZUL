<?php
use App\Core\Auth;
use App\Core\Csrf;
ob_start();
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Módulo · Habitaciones</span>
    <h1 class="page-title">Mapa de habitaciones</h1>
  </div>
  <div class="page-head__actions">
    <?php if (Auth::can('rooms','create')): ?>
      <a class="btn" href="/rooms/create">Nueva habitación</a>
    <?php endif; ?>
  </div>
</div>

<div class="muted" style="margin-bottom:32px; font-size:12px;">
  Estado al día <?= e(date_es(today())) ?> · <?= count($rooms) ?> habitaciones registradas.
</div>

<div class="room-map">
  <?php foreach ($rooms as $r):
    $isOcc = !empty($occ[(int) $r['id']]) ? 'ocupada' : 'libre';
  ?>
    <div class="room-tile" data-status="<?= e($r['status']) ?>" data-occupancy="<?= $isOcc ?>"
         onclick="window.location='<?= Auth::can('rooms','edit') ? '/rooms/' . (int) $r['id'] . '/edit' : '#' ?>'">
      <div>
        <div class="room-tile__type"><?= e(strtoupper($r['type'])) ?></div>
        <div class="room-tile__code"><?= e($r['code']) ?></div>
      </div>
      <div>
        <div class="room-tile__type"><?= (int) $r['capacity'] ?> pax · piso <?= (int) $r['floor'] ?></div>
        <div class="room-tile__price"><?= e(money($r['price_per_night'])) ?> / noche</div>
      </div>

      <?php if ($r['status'] !== 'disponible'): ?>
        <span class="badge <?= $r['status'] === 'mantenimiento' ? 'badge--warn' : 'badge--bad' ?>" style="position:absolute; top:12px; right:12px; font-size:9px;">
          <?= e(str_replace('_', ' ', $r['status'])) ?>
        </span>
      <?php elseif ($isOcc === 'ocupada'): ?>
        <span class="badge badge--ok" style="position:absolute; top:12px; right:12px; font-size:9px;">ocupada</span>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<section style="margin-top:56px;">
  <div class="eyebrow" style="margin-bottom:18px;">Listado</div>
  <table class="table">
    <thead>
      <tr>
        <th>Código</th>
        <th>Tipo</th>
        <th>Capacidad</th>
        <th>Piso</th>
        <th>Estado</th>
        <th class="text-right">Precio / noche</th>
        <?php if (Auth::can('rooms','edit') || Auth::can('rooms','delete')): ?>
          <th class="text-right">Acciones</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rooms as $r): ?>
        <tr>
          <td class="num"><?= e($r['code']) ?></td>
          <td><?= e($r['type']) ?></td>
          <td class="num"><?= (int) $r['capacity'] ?></td>
          <td class="num"><?= (int) $r['floor'] ?></td>
          <td>
            <?php
              $b = ['disponible'=>'badge--ok','mantenimiento'=>'badge--warn','fuera_servicio'=>'badge--bad'];
              $cls = $b[$r['status']] ?? '';
            ?>
            <span class="badge <?= $cls ?>"><?= e(str_replace('_',' ',$r['status'])) ?></span>
          </td>
          <td class="num text-right"><?= e(money($r['price_per_night'])) ?></td>
          <?php if (Auth::can('rooms','edit') || Auth::can('rooms','delete')): ?>
          <td class="table-actions text-right">
            <?php if (Auth::can('rooms','edit')): ?>
              <a href="/rooms/<?= (int) $r['id'] ?>/edit">Editar</a>
            <?php endif; ?>
            <?php if (Auth::can('rooms','delete')): ?>
              <form method="post" action="/rooms/<?= (int) $r['id'] ?>/delete" data-confirm="¿Eliminar esta habitación?" style="display:inline">
                <?= Csrf::field() ?>
                <button class="danger" type="submit">Eliminar</button>
              </form>
            <?php endif; ?>
          </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
