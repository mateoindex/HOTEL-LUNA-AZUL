<?php
use App\Core\Auth;
$path = $_SERVER['REQUEST_URI'] ?? '/';
$active = function (string $prefix) use ($path) {
    return str_starts_with($path, $prefix) ? 'is-active' : '';
};
?>
<aside class="sidebar">
  <a class="sidebar__brand" href="/dashboard" aria-label="Volver al panel">
    <img src="/assets/img/logo.svg" alt="" class="logo">
    <div>
      <div class="name">Luna Azul</div>
      <div class="meta">Cartagena</div>
    </div>
  </a>

  <nav class="sidebar__nav">
    <div class="sidebar__group">
      <div class="sidebar__group-title">Operación</div>
      <a class="sidebar__link <?= $active('/dashboard') ?>" href="/dashboard">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
        Panel
      </a>
      <?php if (Auth::can('reservations','view')): ?>
      <a class="sidebar__link <?= $active('/reservations') ?>" href="/reservations">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="0"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Reservas
      </a>
      <?php endif; ?>
      <?php if (Auth::can('guests','view')): ?>
      <a class="sidebar__link <?= $active('/guests') ?>" href="/guests">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></svg>
        Huéspedes
      </a>
      <?php endif; ?>
      <?php if (Auth::can('rooms','view')): ?>
      <a class="sidebar__link <?= $active('/rooms') ?>" href="/rooms">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21V8l9-5 9 5v13"/><path d="M9 21v-7h6v7"/></svg>
        Habitaciones
      </a>
      <?php endif; ?>
    </div>

    <?php if (Auth::can('reports','view') || Auth::can('users','view') || Auth::can('audit','view')): ?>
    <div class="sidebar__group">
      <div class="sidebar__group-title">Administración</div>
      <?php if (Auth::can('reports','view')): ?>
      <a class="sidebar__link <?= $active('/reports') ?>" href="/reports">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Reportes
      </a>
      <?php endif; ?>
      <?php if (Auth::can('users','view')): ?>
      <a class="sidebar__link <?= $active('/users') ?>" href="/users">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Usuarios
      </a>
      <?php endif; ?>
      <?php if (Auth::can('audit','view')): ?>
      <a class="sidebar__link <?= $active('/audit') ?>" href="/audit">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Auditoría
      </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </nav>

  <div class="sidebar__footer">
    v1.0 · MVC manual
  </div>
</aside>
