/**
 * Витрина «Ваш фермер»: мобильное меню, AJAX-корзина, счётчики, маска телефона.
 */
(function () {
    'use strict';

    const $  = (selector, root = document) => root.querySelector(selector);
    const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));

    /* ---------- Всплывающее сообщение ---------- */
    let toastTimer = null;
    function toast(message, isError = false) {
        const el = $('#toast');
        if (!el) return;
        el.textContent = message;
        el.classList.toggle('is-error', isError);
        el.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => el.classList.remove('is-visible'), 3200);
    }

    /* ---------- Мобильное меню ---------- */
    const menuToggle = $('[data-menu-toggle]');
    const menu = $('[data-menu]');
    if (menuToggle && menu) {
        menuToggle.addEventListener('click', () => {
            const open = menu.classList.toggle('is-open');
            menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    /* ---------- Фильтры каталога на мобильных ---------- */
    const filtersToggle = $('[data-filters-toggle]');
    const filtersPanel = $('[data-filters]');
    if (filtersToggle && filtersPanel) {
        filtersToggle.addEventListener('click', () => filtersPanel.classList.toggle('is-open'));
    }

    /* ---------- Счётчик количества ---------- */
    document.addEventListener('click', (event) => {
        const minus = event.target.closest('[data-qty-minus]');
        const plus  = event.target.closest('[data-qty-plus]');
        if (!minus && !plus) return;

        const wrap  = (minus || plus).closest('[data-qty]');
        const input = $('input', wrap);
        if (!input) return;

        const min  = parseInt(input.min || '1', 10);
        const max  = parseInt(input.max || '99', 10);
        let value  = parseInt(input.value || '1', 10);
        value = plus ? Math.min(max, value + 1) : Math.max(min, value - 1);
        input.value = value;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });

    /* ---------- Общий помощник для POST-форм корзины ---------- */
    async function postForm(form) {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });
        return response.json();
    }

    function updateCartCount(count) {
        const badge = $('[data-cart-count]');
        if (!badge) return;
        badge.textContent = count;
        badge.classList.toggle('is-empty', !count);
    }

    /* ---------- Добавление в корзину ---------- */
    $$('[data-cart-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const button = $('button[type="submit"]', form);
            if (button) { button.disabled = true; button.dataset.text = button.textContent; button.textContent = 'Добавляем…'; }

            try {
                const data = await postForm(form);
                if (data.ok) {
                    updateCartCount(data.count);
                    toast(data.message || 'Товар в корзине');
                } else {
                    toast(data.error || data.message || 'Не удалось добавить товар', true);
                }
            } catch (error) {
                toast('Ошибка сети, попробуйте ещё раз', true);
            } finally {
                if (button) { button.disabled = false; button.textContent = button.dataset.text || 'В корзину'; }
            }
        });
    });

    /* ---------- Изменение количества в корзине ---------- */
    $$('[data-cart-update]').forEach((form) => {
        const input = $('input[name="quantity"]', form);
        if (!input) return;

        let timer = null;
        const submit = async () => {
            try {
                const data = await postForm(form);
                if (data.ok) {
                    updateCartCount(data.count);
                    refreshCartSummary(data);
                    if (parseInt(input.value, 10) === 0) window.location.reload();
                    else updateLineSum(form, data);
                }
            } catch (error) {
                toast('Не удалось обновить корзину', true);
            }
        };

        input.addEventListener('change', () => { clearTimeout(timer); timer = setTimeout(submit, 250); });
        form.addEventListener('submit', (event) => { event.preventDefault(); submit(); });
    });

    function updateLineSum(form, data) {
        const line = form.closest('[data-line]');
        if (!line || !data.lines) return;
        const id   = parseInt(line.dataset.line, 10);
        const item = data.lines.find((entry) => entry.product_id === id);
        const cell = $('.cart-line__sum', line);
        if (item && cell) cell.textContent = formatPrice(item.sum);
    }

    function refreshCartSummary(data) {
        const subtotal = $('[data-summary-subtotal]');
        const delivery = $('[data-summary-delivery]');
        const total    = $('[data-summary-total]');
        if (subtotal) subtotal.textContent = data.subtotal_text || formatPrice(data.subtotal);
        if (delivery) delivery.textContent = data.delivery_text || '';
        if (total)    total.textContent    = data.total_text || formatPrice(data.total);
    }

    function formatPrice(value) {
        const number = Number(value) || 0;
        const fixed  = Number.isInteger(number) ? number.toFixed(0) : number.toFixed(2);
        return fixed.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' ₽';
    }

    /* ---------- Удаление товара ---------- */
    $$('[data-cart-remove]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            try {
                const data = await postForm(form);
                if (data.ok) {
                    updateCartCount(data.count);
                    const line = form.closest('[data-line]');
                    if (line) line.remove();
                    if (!data.count) window.location.reload();
                    else refreshCartSummary(data);
                    toast('Товар удалён');
                }
            } catch (error) {
                form.submit();
            }
        });
    });

    /* ---------- Автоотправка сортировки ---------- */
    $$('[data-autosubmit] select').forEach((select) => {
        select.addEventListener('change', () => select.form.submit());
    });

    /* ---------- Оформление: адрес и стоимость доставки ---------- */
    const addressField = $('[data-address-field]');
    const summary      = $('[data-summary]');

    function recalcCheckout(deliveryType) {
        if (!summary) return;
        const subtotal = parseFloat(summary.dataset.subtotal || '0');
        const price    = parseFloat(summary.dataset.deliveryPrice || '0');
        const freeFrom = parseFloat(summary.dataset.freeFrom || '0');
        const delivery = (deliveryType === 'pickup' || subtotal >= freeFrom) ? 0 : price;

        const deliveryCell = $('[data-delivery-sum]', summary);
        const totalCell    = $('[data-total-sum]', summary);
        if (deliveryCell) deliveryCell.textContent = delivery > 0 ? formatPrice(delivery) : 'бесплатно';
        if (totalCell)    totalCell.textContent    = formatPrice(subtotal + delivery);
    }

    $$('[data-delivery-type]').forEach((radio) => {
        radio.addEventListener('change', () => {
            if (!radio.checked) return;
            const isPickup = radio.value === 'pickup';

            if (addressField) {
                addressField.hidden = isPickup;
                const input = $('input', addressField);
                if (input) input.required = !isPickup;
            }
            recalcCheckout(radio.value);
        });
    });

    /* ---------- Маска телефона ---------- */
    $$('[data-phone]').forEach((input) => {
        const format = (value) => {
            let digits = value.replace(/\D/g, '');
            if (digits.startsWith('8')) digits = '7' + digits.slice(1);
            if (!digits.startsWith('7')) digits = '7' + digits;
            digits = digits.slice(0, 11);

            let out = '+7';
            if (digits.length > 1) out += ' (' + digits.slice(1, 4);
            if (digits.length >= 5) out += ') ' + digits.slice(4, 7);
            if (digits.length >= 8) out += '-' + digits.slice(7, 9);
            if (digits.length >= 10) out += '-' + digits.slice(9, 11);
            return out;
        };

        input.addEventListener('focus', () => { if (!input.value) input.value = '+7 ('; });
        input.addEventListener('input', () => { input.value = format(input.value); });
        input.addEventListener('blur', () => { if (input.value.replace(/\D/g, '').length <= 1) input.value = ''; });
    });
})();
