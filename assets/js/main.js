/* =========================================================
   Quartirnik — Main JS
   ========================================================= */

// ── Cart state ───────────────────────────────────────────
const Cart = {
  items: JSON.parse(localStorage.getItem('cart') || '[]'),

  save() {
    localStorage.setItem('cart', JSON.stringify(this.items));
    this.updateUI();
  },

  add(id, name, price, image) {
    const existing = this.items.find(i => i.id === id);
    if (existing) {
      existing.qty++;
    } else {
      this.items.push({ id, name, price: parseFloat(price), image: image || '', qty: 1 });
    }
    this.save();
    return true;
  },

  remove(id) {
    this.items = this.items.filter(i => i.id !== id);
    this.save();
  },

  setQty(id, qty) {
    const item = this.items.find(i => i.id === id);
    if (!item) return;
    if (qty <= 0) { this.remove(id); return; }
    item.qty = qty;
    this.save();
  },

  total() {
    return this.items.reduce((s, i) => s + i.price * i.qty, 0);
  },

  count() {
    return this.items.reduce((s, i) => s + i.qty, 0);
  },

  clear() {
    this.items = [];
    this.save();
  },

  updateUI() {
    const counts = document.querySelectorAll('.cart-count');
    const c = this.count();
    counts.forEach(el => {
      el.textContent = c;
      el.style.display = c > 0 ? 'flex' : 'none';
    });
  }
};

// ── Toast notifications ──────────────────────────────────
function toast(msg, type = 'info', duration = 3000) {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const icons = { success: '✓', error: '✕', info: '♪' };
  const el = document.createElement('div');
  el.className = `toast toast-${type}`;
  el.innerHTML = `<span>${icons[type] || '•'}</span><span class="toast-text">${msg}</span>`;
  container.appendChild(el);
  setTimeout(() => {
    el.classList.add('removing');
    el.addEventListener('animationend', () => el.remove());
  }, duration);
}

// ── Burger menu ──────────────────────────────────────────
function initBurger() {
  const burger = document.querySelector('.burger');
  const nav = document.querySelector('.nav');
  if (!burger || !nav) return;
  burger.addEventListener('click', () => {
    nav.classList.toggle('open');
    burger.classList.toggle('open');
  });
  document.addEventListener('click', (e) => {
    if (!burger.contains(e.target) && !nav.contains(e.target)) {
      nav.classList.remove('open');
      burger.classList.remove('open');
    }
  });
}

// ── Fade-up animation on scroll ─────────────────────────
function initFadeUp() {
  const els = document.querySelectorAll('.fade-up');
  if (!els.length) return;
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });
  els.forEach(el => observer.observe(el));
}

// ── Add to cart buttons ──────────────────────────────────
function initAddToCart() {
  document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const { id, name, price, image } = this.dataset;
      Cart.add(parseInt(id), name, price, image);
      const orig = this.innerHTML;
      this.classList.add('added');
      this.innerHTML = '✓ Добавлено';
      toast(`«${name}» добавлено в корзину`, 'success');
      setTimeout(() => {
        this.classList.remove('added');
        this.innerHTML = orig;
      }, 1500);
    });
  });
}

// ── Menu tabs ─────────────────────────────────────────────
function initMenuTabs() {
  const tabs = document.querySelectorAll('.menu-tab');
  const cats = document.querySelectorAll('.menu-category');
  if (!tabs.length) return;

  function showTab(slug) {
    tabs.forEach(t => t.classList.toggle('active', t.dataset.slug === slug));
    cats.forEach(c => c.classList.toggle('visible', c.dataset.slug === slug));
  }

  if (tabs.length) showTab(tabs[0].dataset.slug);

  tabs.forEach(tab => {
    tab.addEventListener('click', () => showTab(tab.dataset.slug));
  });
}

// ── Cart page ─────────────────────────────────────────────
function initCartPage() {
  if (!document.querySelector('.cart-page')) return;
  renderCart();
}

function renderCart() {
  const container = document.getElementById('cart-items-container');
  const summaryEl = document.getElementById('cart-summary');
  if (!container) return;

  if (!Cart.items.length) {
    container.innerHTML = `
      <div class="cart-empty">
        <div class="icon">🛒</div>
        <h3>Корзина пуста</h3>
        <p>Добавьте блюда из нашего меню</p>
        <a href="/menu.php" class="btn btn-primary" style="margin-top:20px">Перейти в меню</a>
      </div>`;
    if (summaryEl) summaryEl.style.display = 'none';
    return;
  }

  if (summaryEl) summaryEl.style.display = '';

  container.innerHTML = Cart.items.map(item => `
    <div class="cart-item" data-id="${item.id}">
      <div class="cart-item-img">
        ${item.image
          ? `<img src="${item.image}" alt="${escHtml(item.name)}">`
          : `<div class="no-img">🍽</div>`}
      </div>
      <div class="cart-item-info">
        <h4>${escHtml(item.name)}</h4>
        <span class="price">${fmtPrice(item.price)} × ${item.qty}</span>
      </div>
      <div class="cart-qty">
        <button class="qty-btn" data-action="dec" data-id="${item.id}">−</button>
        <span class="qty-val">${item.qty}</span>
        <button class="qty-btn" data-action="inc" data-id="${item.id}">+</button>
      </div>
      <div class="cart-item-total">${fmtPrice(item.price * item.qty)}</div>
      <button class="cart-item-del" data-id="${item.id}" title="Удалить">✕</button>
    </div>`).join('');

  updateCartSummary();

  container.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = parseInt(btn.dataset.id);
      const item = Cart.items.find(i => i.id === id);
      if (!item) return;
      Cart.setQty(id, item.qty + (btn.dataset.action === 'inc' ? 1 : -1));
      renderCart();
    });
  });

  container.querySelectorAll('.cart-item-del').forEach(btn => {
    btn.addEventListener('click', () => {
      Cart.remove(parseInt(btn.dataset.id));
      renderCart();
    });
  });
}

function updateCartSummary() {
  const subtotalEl = document.getElementById('cart-subtotal');
  const totalEl    = document.getElementById('cart-total');
  if (subtotalEl) subtotalEl.textContent = fmtPrice(Cart.total());
  if (totalEl)    totalEl.textContent    = fmtPrice(Cart.total());
}

// ── Checkout page ─────────────────────────────────────────
function initCheckoutPage() {
  if (!document.querySelector('.checkout-page')) return;

  // Render order summary
  const summaryList = document.getElementById('checkout-items');
  if (summaryList) {
    summaryList.innerHTML = Cart.items.map(i =>
      `<li class="summary-row">
        <span>${escHtml(i.name)} × ${i.qty}</span>
        <span>${fmtPrice(i.price * i.qty)}</span>
      </li>`
    ).join('');
  }
  const totEl = document.getElementById('checkout-total');
  if (totEl) totEl.textContent = fmtPrice(Cart.total());

  // Delivery toggle
  const typeInputs = document.querySelectorAll('input[name="type"]');
  const addressGroup = document.getElementById('address-group');
  typeInputs.forEach(inp => {
    inp.addEventListener('change', () => {
      if (addressGroup) {
        addressGroup.style.display = inp.value === 'delivery' ? '' : 'none';
      }
    });
  });

  // Submit
  const form = document.getElementById('checkout-form');
  if (!form) return;
  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    if (!Cart.items.length) { toast('Корзина пуста', 'error'); return; }
    const btn = form.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Оформляем...';

    const data = {
      name: form.name.value.trim(),
      phone: form.phone.value.trim(),
      type: form.type.value,
      address: form.address ? form.address.value.trim() : '',
      comment: form.comment ? form.comment.value.trim() : '',
      items: Cart.items,
      total: Cart.total()
    };

    try {
      const res = await fetch('/api/order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const json = await res.json();
      if (json.success) {
        if (json.payment_url) {
          Cart.clear();
          window.location.href = json.payment_url;
        } else {
          Cart.clear();
          window.location.href = '/order-confirm.php?id=' + json.order_id;
        }
      } else {
        toast(json.message || 'Ошибка оформления', 'error');
        btn.disabled = false;
        btn.innerHTML = 'Оформить заказ';
      }
    } catch {
      toast('Ошибка соединения', 'error');
      btn.disabled = false;
      btn.innerHTML = 'Оформить заказ';
    }
  });
}

// ── Booking form ──────────────────────────────────────────
function initBookingForm() {
  const form = document.getElementById('booking-form');
  if (!form) return;
  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = form.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Отправляем...';
    const fd = new FormData(form);
    try {
      const res = await fetch('/api/booking.php', { method: 'POST', body: fd });
      const json = await res.json();
      if (json.success) {
        form.innerHTML = `
          <div class="form-success">
            <div style="font-size:2.5rem;margin-bottom:12px">🎉</div>
            <h3 style="color:#7ecf98;margin-bottom:8px">Стол забронирован!</h3>
            <p>Мы ждём вас ${json.date} в ${json.time}.<br>Скоро позвоним для подтверждения.</p>
          </div>`;
      } else {
        toast(json.message || 'Ошибка бронирования', 'error');
        btn.disabled = false;
        btn.innerHTML = 'Забронировать';
      }
    } catch {
      toast('Ошибка соединения', 'error');
      btn.disabled = false;
      btn.innerHTML = 'Забронировать';
    }
  });
}

// ── Events filter ─────────────────────────────────────────
function initEventsFilter() {
  const form = document.getElementById('events-filter-form');
  if (!form) return;
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const from = form.from.value;
    const to   = form.to.value;
    let url = '/events.php?';
    if (from) url += 'from=' + from + '&';
    if (to)   url += 'to=' + to;
    window.location.href = url;
  });
  form.querySelector('.btn-reset')?.addEventListener('click', () => {
    window.location.href = '/events.php';
  });
}

// ── Helpers ───────────────────────────────────────────────
function fmtPrice(n) {
  return new Intl.NumberFormat('ru-RU').format(Math.round(n)) + ' ₽';
}
function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Init ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  Cart.updateUI();
  initBurger();
  initFadeUp();
  initAddToCart();
  initMenuTabs();
  initCartPage();
  initCheckoutPage();
  initBookingForm();
  initEventsFilter();
});
