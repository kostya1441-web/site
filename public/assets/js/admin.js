/** Админка: боковое меню, переключатели, клик по строке таблицы. */
(function () {
    'use strict';

    const side = document.querySelector('[data-admin-side]');
    const toggle = document.querySelector('[data-admin-menu]');
    if (side && toggle) {
        toggle.addEventListener('click', () => side.classList.toggle('is-open'));
    }

    // Переход по строке заказа, но не по ссылке/кнопке внутри неё
    document.querySelectorAll('.row-link').forEach((row) => {
        row.addEventListener('click', (event) => {
            if (event.target.closest('a, button, form, input')) return;
            window.location.href = row.dataset.href;
        });
    });

    // Переключатели «показывать» и «хит» — без перезагрузки страницы
    document.querySelectorAll('[data-toggle-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const control = form.querySelector('button');
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const data = await response.json();
                if (data.ok && control) control.classList.toggle('is-on', Boolean(data.value));
            } catch (error) {
                form.submit();
            }
        });
    });

    // Автогенерация slug из названия при создании
    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="slug"]');
    if (nameInput && slugInput && !slugInput.value) {
        const map = {
            а:'a',б:'b',в:'v',г:'g',д:'d',е:'e',ё:'e',ж:'zh',з:'z',и:'i',й:'y',к:'k',л:'l',м:'m',
            н:'n',о:'o',п:'p',р:'r',с:'s',т:'t',у:'u',ф:'f',х:'h',ц:'c',ч:'ch',ш:'sh',щ:'sch',
            ъ:'',ы:'y',ь:'',э:'e',ю:'yu',я:'ya'
        };
        let touched = false;
        slugInput.addEventListener('input', () => { touched = true; });
        nameInput.addEventListener('input', () => {
            if (touched) return;
            slugInput.value = nameInput.value.toLowerCase().split('')
                .map((char) => (map[char] !== undefined ? map[char] : char))
                .join('')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        });
    }
})();
