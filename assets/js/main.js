// Мобильное меню
const burger = document.getElementById('burger');
const mobileMenu = document.getElementById('mobileMenu');
if (burger && mobileMenu) {
    burger.addEventListener('click', () => {
        mobileMenu.classList.toggle('open');
    });
}

// Онлайн серверов через API
async function loadServerStatus() {
    try {
        const res = await fetch('/api/server_status.php');
        if (!res.ok) return;
        const servers = await res.json();
        servers.forEach((srv) => {
            document.querySelectorAll('[data-ip="' + srv.ip + '"][data-port="' + srv.port + '"]').forEach(el => {
                if (el.classList.contains('server-players')) {
                    el.querySelector('.players-count').textContent = srv.players;
                    el.querySelector('.players-max').textContent = srv.max;
                } else if (el.classList.contains('server-map')) {
                    el.textContent = 'Карта: ' + srv.map;
                }
                const dot = el.closest('.server-card')?.querySelector('.server-status-dot');
                if (dot) {
                    dot.classList.toggle('offline', !srv.online);
                }
            });
        });
    } catch (e) {
        // API недоступен — не критично
    }
}

if (document.querySelector('.server-card')) {
    loadServerStatus();
    setInterval(loadServerStatus, 30000); // обновляем каждые 30 сек
}

// Плавная прокрутка к якорям (правила)
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (!target) return;
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
