</main>
</div>

<script src="/assets/js/main.js"></script>
<script>
// Admin user-menu dropdown
document.addEventListener('click', function(e) {
    if (!e.target.closest('#userMenu')) {
        document.querySelectorAll('.user-dropdown').forEach(d => d.classList.remove('open'));
    }
});
document.getElementById('userMenuBtn')?.addEventListener('click', function(e) {
    e.stopPropagation();
    document.getElementById('userDropdown')?.classList.toggle('open');
});
</script>
</body>
</html>
