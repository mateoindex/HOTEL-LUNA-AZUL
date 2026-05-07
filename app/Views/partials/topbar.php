<?php
use App\Core\Auth;
use App\Core\Csrf;
$user = Auth::user();
$initials = strtoupper(mb_substr($user['full_name'] ?? '?', 0, 1));
?>
<div class="topbar">
  <div></div>
  <div class="topbar__user">
    <button class="user-chip" data-dropdown type="button">
      <span class="user-chip__avatar"><?= e($initials) ?></span>
      <span class="user-chip__text">
        <span class="user-chip__name"><?= e($user['full_name'] ?? '') ?></span>
        <span class="user-chip__role"><?= e($user['role_display'] ?? '') ?></span>
      </span>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-left:6px; flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
    <div class="dropdown">
      <form method="post" action="/logout" style="margin:0">
        <?= Csrf::field() ?>
        <button type="submit" class="danger">Cerrar sesión</button>
      </form>
    </div>
  </div>
</div>
