document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('navToggle');
    var nav = document.getElementById('mainNav');
    var navClose = document.getElementById('navClose');
    var backdrop = document.getElementById('navBackdrop');

    function openNav() {
        nav.classList.add('open');
        if (backdrop) backdrop.classList.add('show');
        document.body.classList.add('nav-open-lock');
    }

    function closeNav() {
        nav.classList.remove('open');
        if (backdrop) backdrop.classList.remove('show');
        document.body.classList.remove('nav-open-lock');
    }

    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            nav.classList.contains('open') ? closeNav() : openNav();
        });
    }
    if (navClose) navClose.addEventListener('click', closeNav);
    if (backdrop) backdrop.addEventListener('click', closeNav);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeNav();
    });

    if (nav) {
        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeNav);
        });
    }

    // Добавление товара в корзину (AJAX)
    document.querySelectorAll('.js-add-to-cart').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            var originalText = btn ? btn.textContent : '';
            if (btn) { btn.disabled = true; btn.textContent = 'Добавляем...'; }

            fetch('/cart_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(new FormData(form))
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        updateCartBadge(data.cart_count);
                        if (btn) { btn.textContent = 'Добавлено ✓'; }
                        setTimeout(function () {
                            if (btn) { btn.disabled = false; btn.textContent = originalText; }
                        }, 1200);
                    } else {
                        alert(data.message || 'Не удалось добавить товар в корзину');
                        if (btn) { btn.disabled = false; btn.textContent = originalText; }
                    }
                })
                .catch(function () {
                    alert('Ошибка соединения. Попробуйте ещё раз.');
                    if (btn) { btn.disabled = false; btn.textContent = originalText; }
                });
        });
    });

    // Обновление количества / удаление в корзине
    document.querySelectorAll('.js-cart-update').forEach(function (input) {
        input.addEventListener('change', function () {
            var productId = input.dataset.productId;
            var qty = parseFloat(input.value) || 0;
            cartAction('update', productId, qty);
        });
    });

    document.querySelectorAll('.js-cart-remove').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            cartAction('remove', btn.dataset.productId, 0);
        });
    });

    function cartAction(action, productId, qty) {
        fetch('/cart_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: action, product_id: productId, qty: qty })
        })
            .then(function (r) { return r.json(); })
            .then(function () {
                window.location.reload();
            });
    }

    function updateCartBadge(count) {
        var badge = document.getElementById('cartBadge');
        if (!badge) return;
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }
});
