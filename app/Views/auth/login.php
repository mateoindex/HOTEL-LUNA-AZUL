<?php
use App\Core\Csrf;
ob_start();
?>
<div class="guest-shell">
  <div class="guest-shell__art">
    <div class="guest-shell__art-inner">
      <div class="meta">Hotel Luna Azul · Cartagena</div>
      <h2>Hospitalidad curada<br>frente al mar.</h2>
      <div class="meta">Sistema interno · Personal autorizado</div>
    </div>
  </div>

  <div class="guest-shell__form">
    <div class="guest-shell__form-inner">
      <img src="/assets/img/logo.svg" alt="" style="width:64px; height:64px; margin-bottom: 32px;">
      <div class="eyebrow" style="margin-bottom:12px">Acceso de personal</div>
      <h1 class="page-title" style="font-size:36px; margin-bottom:32px;">Bienvenido</h1>

      <?php if ($error ?? null): ?>
        <div class="alert alert--bad"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" action="/login">
        <?= Csrf::field() ?>

        <label class="field">
          <span class="field__label">Correo</span>
          <input class="field__input" type="email" name="email" required autofocus value="<?= e(old('email')) ?>">
        </label>

        <label class="field">
          <span class="field__label">Contraseña</span>
          <input class="field__input" type="password" name="password" required>
        </label>

        <button type="submit" class="btn btn--block" style="margin-top: 16px;">Ingresar</button>
      </form>

      <p class="muted" style="font-size:11px; margin-top:32px; letter-spacing:0.05em;">
        ¿Problemas de acceso? Contacte a la gerencia.
      </p>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require base_path('app/Views/layouts/guest.php');
