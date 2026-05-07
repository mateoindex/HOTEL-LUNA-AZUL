// modal/drawer abrir-cerrar generico
document.addEventListener('click', (e) => {
  const open = e.target.closest('[data-open]');
  if (open) {
    const sel = open.dataset.open;
    const target = document.querySelector(sel);
    if (target) target.classList.add('is-open');
    return;
  }
  const close = e.target.closest('[data-close]');
  if (close) {
    const sel = close.dataset.close;
    const target = sel ? document.querySelector(sel) : close.closest('.modal-back, .drawer-back, .drawer');
    if (target) {
      target.classList.remove('is-open');
      // si es drawer, cerrar tambien el back
      const back = document.querySelector('.drawer-back.is-open');
      if (back) back.classList.remove('is-open');
    }
  }
});

// click en backdrop cierra
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-back') || e.target.classList.contains('drawer-back')) {
    e.target.classList.remove('is-open');
    document.querySelectorAll('.drawer.is-open').forEach(d => d.classList.remove('is-open'));
  }
});
