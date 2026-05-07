// utilidades globales

// dropdown del avatar
document.addEventListener('click', (e) => {
  const trigger = e.target.closest('[data-dropdown]');
  if (trigger) {
    const dd = trigger.parentElement.querySelector('.dropdown');
    if (dd) {
      dd.classList.toggle('is-open');
      e.stopPropagation();
      return;
    }
  }
  // click fuera cierra todos
  document.querySelectorAll('.dropdown.is-open').forEach(d => d.classList.remove('is-open'));
});

// confirm modal generico (forms con data-confirm)
document.addEventListener('submit', (e) => {
  const form = e.target;
  const msg = form.dataset.confirm;
  if (!msg) return;
  if (!form.dataset.confirmed) {
    e.preventDefault();
    if (confirm(msg)) {
      form.dataset.confirmed = '1';
      form.submit();
    }
  }
});

// auto cierre toasts
window.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.toast').forEach(t => {
    setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; }, 3700);
    setTimeout(() => t.remove(), 4100);
  });
});

// helper: api fetch corto
window.api = async function(url, opts = {}) {
  const r = await fetch(url, {
    headers: { 'X-Requested-With': 'XMLHttpRequest', ...(opts.headers || {}) },
    ...opts,
  });
  if (!r.ok && r.status !== 409) throw new Error('HTTP ' + r.status);
  return r.json();
};

// helper: toast desde JS
window.toast = function(msg, type = 'ok') {
  let stack = document.querySelector('.toast-stack');
  if (!stack) {
    stack = document.createElement('div');
    stack.className = 'toast-stack';
    document.body.appendChild(stack);
  }
  const t = document.createElement('div');
  t.className = 'toast toast--' + type;
  t.textContent = msg;
  stack.appendChild(t);
  setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; }, 3700);
  setTimeout(() => t.remove(), 4100);
};
