/* =========================================================
   Quartirnik — Admin JS
   ========================================================= */

// ── Modal ─────────────────────────────────────────────────
function openModal(id) {
  const modal = document.getElementById(id);
  if (!modal) return;
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}
function closeModal(id) {
  const modal = document.getElementById(id);
  if (!modal) return;
  modal.style.display = 'none';
  document.body.style.overflow = '';
}
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.style.display = 'none';
    document.body.style.overflow = '';
  }
  if (e.target.dataset.closeModal) {
    closeModal(e.target.dataset.closeModal);
  }
  if (e.target.dataset.openModal) {
    openModal(e.target.dataset.openModal);
  }
});

// ── Alert ─────────────────────────────────────────────────
function showAlert(msg, type = 'success') {
  const existing = document.querySelector('.alert.auto-alert');
  if (existing) existing.remove();
  const el = document.createElement('div');
  el.className = `alert alert-${type} auto-alert`;
  el.textContent = msg;
  const main = document.querySelector('.admin-main');
  if (main) main.prepend(el);
  setTimeout(() => el.remove(), 4000);
}

// ── Image preview ─────────────────────────────────────────
function initImagePreviews() {
  document.querySelectorAll('[data-img-preview]').forEach(input => {
    input.addEventListener('change', function () {
      const previewId = this.dataset.imgPreview;
      const preview = document.getElementById(previewId);
      if (!preview || !this.files[0]) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        let img = preview.querySelector('img');
        if (!img) {
          preview.innerHTML = '';
          img = document.createElement('img');
          preview.appendChild(img);
        }
        img.src = e.target.result;
        preview.querySelector('.placeholder')?.remove();
      };
      reader.readAsDataURL(this.files[0]);
    });
  });
}

// ── Table search ───────────────────────────────────────────
function initTableSearch() {
  document.querySelectorAll('[data-search-table]').forEach(input => {
    const tableId = input.dataset.searchTable;
    const table = document.getElementById(tableId);
    if (!table) return;
    input.addEventListener('input', function () {
      const q = this.value.toLowerCase();
      table.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  });
}

// ── Status updates via fetch ──────────────────────────────
async function updateStatus(type, id, status, el) {
  try {
    const res = await fetch('/api/admin/status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type, id, status })
    });
    const json = await res.json();
    if (json.success) {
      if (el) {
        el.closest('tr')?.querySelector('.status-badge')?.replaceWith(
          (() => {
            const s = document.createElement('span');
            s.className = `badge badge-${status}`;
            s.textContent = json.label;
            s.dataset.id = id;
            return s;
          })()
        );
      }
      showAlert('Статус обновлён');
    } else {
      showAlert(json.message || 'Ошибка', 'error');
    }
  } catch {
    showAlert('Ошибка соединения', 'error');
  }
}

// ── Delete confirm ────────────────────────────────────────
function initDeleteConfirm() {
  document.querySelectorAll('[data-delete-url]').forEach(btn => {
    btn.addEventListener('click', async function (e) {
      e.preventDefault();
      if (!confirm('Удалить?')) return;
      const url = this.dataset.deleteUrl;
      try {
        const res = await fetch(url, { method: 'POST' });
        const json = await res.json();
        if (json.success) {
          this.closest('tr')?.remove();
          showAlert('Удалено');
        } else {
          showAlert(json.message || 'Ошибка', 'error');
        }
      } catch {
        showAlert('Ошибка соединения', 'error');
      }
    });
  });
}

// ── Toggle active ─────────────────────────────────────────
function initToggles() {
  document.querySelectorAll('[data-toggle-url]').forEach(toggle => {
    toggle.addEventListener('change', async function () {
      const url = this.dataset.toggleUrl;
      const val = this.checked ? 1 : 0;
      try {
        await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ active: val })
        });
      } catch { /* silent */ }
    });
  });
}

// ── Real-time notifications (polling) ─────────────────────
let lastOrderId = 0;
function initNotifications() {
  const dot = document.querySelector('.notif-dot');
  async function poll() {
    try {
      const res = await fetch('/api/admin/notifications.php?last=' + lastOrderId);
      const json = await res.json();
      if (json.new_orders?.length) {
        lastOrderId = json.new_orders[json.new_orders.length - 1].id;
        dot?.classList.remove('hidden');
        json.new_orders.forEach(o => {
          adminToast(`Новый заказ #${o.id} — ${o.name}`, o.type === 'delivery' ? '🚴' : '🏠');
        });
      }
    } catch { /* silent */ }
  }
  const lastEl = document.querySelector('[data-last-order-id]');
  if (lastEl) lastOrderId = parseInt(lastEl.dataset.lastOrderId) || 0;
  setInterval(poll, 15000);
}

function adminToast(msg, icon = '🔔') {
  let container = document.getElementById('admin-toasts');
  if (!container) {
    container = document.createElement('div');
    container.id = 'admin-toasts';
    container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;';
    document.body.appendChild(container);
  }
  const el = document.createElement('div');
  el.style.cssText = `background:#2A221A;border:1px solid rgba(200,145,90,0.3);border-left:3px solid #C8915A;
    border-radius:8px;padding:14px 18px;display:flex;gap:10px;min-width:260px;max-width:360px;
    box-shadow:0 4px 24px rgba(0,0,0,0.5);animation:toast-in 0.3s ease;font-family:var(--font-sans);color:#F0E6D3;font-size:0.9rem;`;
  el.innerHTML = `<span>${icon}</span><span>${msg}</span>`;
  container.appendChild(el);
  setTimeout(() => el.remove(), 5000);
}

// ── Sidebar mobile toggle ─────────────────────────────────
function initSidebarToggle() {
  const toggleBtn = document.getElementById('sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  if (!toggleBtn || !sidebar) return;
  toggleBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
}

// ── Init ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initImagePreviews();
  initTableSearch();
  initDeleteConfirm();
  initToggles();
  initNotifications();
  initSidebarToggle();
});
