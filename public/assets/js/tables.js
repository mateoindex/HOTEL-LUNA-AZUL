// busqueda en vivo dentro de tablas (input data-table-search="#tablaId")
document.addEventListener('input', (e) => {
  const inp = e.target.closest('[data-table-search]');
  if (!inp) return;
  const sel = inp.dataset.tableSearch;
  const table = document.querySelector(sel);
  if (!table) return;
  const q = inp.value.toLowerCase().trim();
  table.querySelectorAll('tbody tr').forEach(tr => {
    const txt = tr.textContent.toLowerCase();
    tr.style.display = (q === '' || txt.includes(q)) ? '' : 'none';
  });
});

// audit row toggle
document.addEventListener('click', (e) => {
  const btn = e.target.closest('[data-audit-toggle]');
  if (!btn) return;
  const id = btn.dataset.auditToggle;
  const data = document.querySelector('[data-audit-data="' + id + '"]');
  if (data) data.classList.toggle('is-open');
});
