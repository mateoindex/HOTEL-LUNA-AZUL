// flujo del form de reserva: dispara availability segun fechas
(() => {
  const form = document.querySelector('[data-reservation-form]');
  if (!form) return;

  const inDate  = form.querySelector('[name="check_in"]');
  const outDate = form.querySelector('[name="check_out"]');
  const target  = form.querySelector('[data-availability]');
  const roomInput = form.querySelector('[name="room_id"]');
  const totalInput = form.querySelector('[name="total_amount"]');
  const priceLabel = form.querySelector('[data-price-hint]');
  const ignoreId = form.dataset.reservationId || '0';

  let prices = {};

  async function fetchAvailability() {
    if (!inDate.value || !outDate.value) return;
    if (new Date(outDate.value) <= new Date(inDate.value)) {
      target.innerHTML = '<p class="muted">La fecha de salida debe ser posterior a la de entrada.</p>';
      return;
    }
    target.innerHTML = '<p class="muted"><span class="spinner"></span> buscando habitaciones libres…</p>';
    try {
      const url = `/api/availability?from=${inDate.value}&to=${outDate.value}&ignore=${ignoreId}`;
      const data = await api(url);
      renderRooms(data.rooms || []);
      computeTotal();
    } catch (e) {
      target.innerHTML = '<p class="terracotta">No se pudo consultar disponibilidad.</p>';
    }
  }

  function renderRooms(rooms) {
    if (!rooms.length) {
      target.innerHTML = '<div class="empty"><p class="empty__title">Sin habitaciones libres</p><p class="empty__text">No hay habitaciones disponibles para esas fechas. Pruebe otro rango.</p></div>';
      return;
    }
    prices = {};
    const sel = roomInput.value;
    target.innerHTML = '<div class="room-map">' + rooms.map(r => {
      prices[r.id] = parseFloat(r.price_per_night);
      const isSel = String(r.id) === String(sel);
      return `
        <div class="room-tile ${isSel ? 'is-selected' : ''}" data-room-id="${r.id}" data-price="${r.price_per_night}">
          <div>
            <div class="room-tile__type">${r.type}</div>
            <div class="room-tile__code">${r.code}</div>
          </div>
          <div>
            <div class="room-tile__type">${r.capacity} pax · piso ${r.floor}</div>
            <div class="room-tile__price">${formatMoney(r.price_per_night)} / noche</div>
          </div>
        </div>`;
    }).join('') + '</div>';
  }

  function formatMoney(v) {
    return '$' + Number(v).toLocaleString('es-CO', { maximumFractionDigits: 0 });
  }

  function computeTotal() {
    const id = roomInput.value;
    if (!id || !prices[id] || !inDate.value || !outDate.value) return;
    const nights = Math.max(1, Math.round((new Date(outDate.value) - new Date(inDate.value)) / 86400000));
    const total = prices[id] * nights;
    if (totalInput) totalInput.value = total.toFixed(2);
    if (priceLabel) priceLabel.textContent = `${nights} noche${nights > 1 ? 's' : ''} × ${formatMoney(prices[id])} = ${formatMoney(total)}`;
  }

  // pick habitacion
  target.addEventListener('click', (e) => {
    const tile = e.target.closest('.room-tile');
    if (!tile) return;
    target.querySelectorAll('.room-tile').forEach(t => t.classList.remove('is-selected'));
    tile.classList.add('is-selected');
    roomInput.value = tile.dataset.roomId;
    computeTotal();
  });

  inDate.addEventListener('change', fetchAvailability);
  outDate.addEventListener('change', fetchAvailability);

  // primera carga si ya hay datos (edicion)
  if (inDate.value && outDate.value) fetchAvailability();
})();

// autocomplete huesped
(() => {
  const wrap = document.querySelector('[data-guest-autocomplete]');
  if (!wrap) return;
  const input  = wrap.querySelector('[data-guest-search]');
  const hidden = wrap.querySelector('[name="guest_id"]');
  const results = wrap.querySelector('[data-guest-results]');
  let t;

  input.addEventListener('input', () => {
    clearTimeout(t);
    const q = input.value.trim();
    if (q.length < 2) { results.classList.remove('is-open'); return; }
    t = setTimeout(async () => {
      try {
        const data = await api('/api/guests/search?q=' + encodeURIComponent(q));
        if (!data.guests || !data.guests.length) {
          results.innerHTML = '<div class="autocomplete__item muted">Sin resultados</div>';
        } else {
          results.innerHTML = data.guests.map(g => `
            <div class="autocomplete__item" data-id="${g.id}" data-label="${g.first_name} ${g.last_name}">
              ${g.first_name} ${g.last_name}<span class="doc">${g.document_type} ${g.document_number}</span>
            </div>
          `).join('');
        }
        results.classList.add('is-open');
      } catch (e) { /* nada */ }
    }, 220);
  });

  results.addEventListener('click', (e) => {
    const it = e.target.closest('.autocomplete__item[data-id]');
    if (!it) return;
    hidden.value = it.dataset.id;
    input.value = it.dataset.label;
    results.classList.remove('is-open');
  });

  document.addEventListener('click', (e) => {
    if (!wrap.contains(e.target)) results.classList.remove('is-open');
  });
})();
