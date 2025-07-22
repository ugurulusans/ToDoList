</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const icon = themeToggle.querySelector('i');

    const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : null;

    if (currentTheme) {
        body.classList.add(currentTheme);
        if (currentTheme === 'bg-dark') {
            icon.classList.remove('bi-moon');
            icon.classList.add('bi-sun');
        }
    }

    themeToggle.addEventListener('click', () => {
        if (body.classList.contains('bg-dark')) {
            body.classList.remove('bg-dark', 'text-white');
            body.classList.add('bg-light', 'text-dark');
            icon.classList.remove('bi-sun');
            icon.classList.add('bi-moon');
            localStorage.setItem('theme', 'bg-light');
        } else {
            body.classList.remove('bg-light', 'text-dark');
            body.classList.add('bg-dark', 'text-white');
            icon.classList.remove('bi-moon');
            icon.classList.add('bi-sun');
            localStorage.setItem('theme', 'bg-dark');
        }
    });
});
</script>
</body>
</html>
