<?php
ob_start();
$today = today();
$monthStart = date('Y-m-01');
?>

<div class="page-head">
  <div class="page-head__title">
    <span class="eyebrow">Módulo · Reportes</span>
    <h1 class="page-title">Generar reportes</h1>
  </div>
</div>

<p class="muted" style="margin-bottom:48px; font-size:13px; max-width:520px;">
  Los reportes se generan en PDF a través de un componente Python (reportlab).
  Cada generación queda registrada en la auditoría.
</p>

<div class="row row--2">
  <div class="card">
    <div class="eyebrow">01 · Voucher de reserva</div>
    <h2 class="section-title" style="margin-top:14px;">Comprobante individual</h2>
    <p class="muted" style="margin-top:12px; font-size:13px;">
      Disponible desde la ficha de cualquier reserva. Incluye los datos del huésped,
      la habitación, fechas, total y código de reserva.
    </p>
    <div style="margin-top:24px;">
      <a class="btn btn--ghost" href="/reservations">Ir a reservas</a>
    </div>
  </div>

  <div class="card">
    <div class="eyebrow">02 · Ocupación del mes</div>
    <h2 class="section-title" style="margin-top:14px;">Reporte de ocupación</h2>
    <p class="muted" style="margin-top:12px; font-size:13px;">
      Gráfico de barras y tabla con la ocupación del periodo seleccionado.
    </p>

    <form action="/reports/occupancy.pdf" method="get" style="margin-top:24px;">
      <div class="row row--2">
        <label class="field" style="margin:0">
          <span class="field__label">Desde</span>
          <input class="field__input" type="date" name="start" value="<?= e($monthStart) ?>">
        </label>
        <label class="field" style="margin:0">
          <span class="field__label">Días</span>
          <input class="field__input" type="number" min="7" max="90" name="days" value="30">
        </label>
      </div>
      <div style="margin-top:16px;">
        <button class="btn" type="submit">Generar PDF</button>
      </div>
    </form>
  </div>
</div>

<?php $content = ob_get_clean(); require base_path('app/Views/layouts/app.php'); ?>
