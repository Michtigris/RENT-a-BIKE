(() => {
  const base = document.querySelector('.brand')?.getAttribute('href') || '';

  document.querySelectorAll('[data-status]').forEach((button) => {
    button.addEventListener('click', async () => {
      const row = button.closest('[data-reservation]');
      const form = new FormData();
      form.set('reservation_id', row.dataset.reservation);
      form.set('status', button.dataset.status);
      const response = await fetch(base + '/dashboard/reservation/status', { method: 'POST', body: form });
      if (response.ok) row.remove();
    });
  });

  document.querySelectorAll('[data-delete-reservation]').forEach((button) => {
    button.addEventListener('click', async () => {
      if (!confirm('Supprimer cette réservation ?')) return;
      const response = await fetch(base + '/dashboard/reservation/delete/' + button.dataset.deleteReservation, { method: 'POST' });
      if (response.ok) button.closest('tr').remove();
    });
  });

  document.querySelectorAll('[data-delete-bike]').forEach((button) => {
    button.addEventListener('click', () => {
      if (confirm('Supprimer ce vélo ?')) {
        fetch(base + '/dashboard/bike/delete/' + button.dataset.deleteBike, { method: 'POST' }).then(() => location.reload());
      }
    });
  });

  document.querySelectorAll('[data-delete-user]').forEach((button) => {
    button.addEventListener('click', () => {
      if (confirm('Supprimer ce compte ?')) {
        fetch(base + '/dashboard/user/delete/' + button.dataset.deleteUser, { method: 'POST' }).then(() => location.reload());
      }
    });
  });

  const search = document.querySelector('#reservation-search');
  if (search) {
    let timeout;
    search.addEventListener('input', () => {
      clearTimeout(timeout);
      timeout = setTimeout(async () => {
        const response = await fetch(base + '/dashboard/search?q=' + encodeURIComponent(search.value));
        if (!response.ok) return;
        const data = await response.json();
        const tbody = document.querySelector('#reservations-table tbody');
        tbody.innerHTML = data.reservations.map((r) => `
          <tr>
            <td><strong>${escapeHtml(r.res_num)}</strong></td>
            <td>${escapeHtml(r.firstname + ' ' + r.lastname)}</td>
            <td>${escapeHtml(r.registration_number)}</td>
            <td>${formatDate(r.start_date)} – ${formatDate(r.end_date)}</td>
            <td><span class="status ${escapeHtml(r.status)}">${escapeHtml(r.status)}</span></td>
            <td></td>
          </tr>`).join('') || '<tr><td colspan="6" class="empty-state">Aucun résultat.</td></tr>';
      }, 180);
    });
  }

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (char) => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' }[char]));
  }
  function formatDate(value) {
    const date = new Date(value.replace(' ', 'T'));
    return date.toLocaleDateString('fr-FR');
  }
})();
