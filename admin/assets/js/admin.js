document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('adminNavToggle');
    var close = document.getElementById('adminNavClose');
    var sidebar = document.getElementById('adminSidebar');
    var backdrop = document.getElementById('adminNavBackdrop');

    function openNav() {
        sidebar.classList.add('open');
        if (backdrop) backdrop.classList.add('show');
        document.body.classList.add('admin-nav-open-lock');
    }
    function closeNav() {
        sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('show');
        document.body.classList.remove('admin-nav-open-lock');
    }

    if (toggle && sidebar) toggle.addEventListener('click', openNav);
    if (close) close.addEventListener('click', closeNav);
    if (backdrop) backdrop.addEventListener('click', closeNav);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeNav();
    });
});
