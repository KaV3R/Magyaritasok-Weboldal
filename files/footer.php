    </main>
    <footer class="mt-auto border-t border-border-light dark:border-border-dark">
        <div class="container mx-auto px-6 py-4 text-center text-muted-light dark:text-muted-dark">
            <p>&copy; <?php echo date('Y'); ?> Magyarítások Portál. CatFlux Entertainment. Minden jog fenntartva.</p>
        </div>
    </footer>
</div>

<script>
    const themeToggle = document.getElementById('theme-toggle');
    const htmlEl = document.documentElement;

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        htmlEl.classList.add('dark');
    } else {
        htmlEl.classList.remove('dark');
    }

    if(themeToggle) {
        themeToggle.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('theme', htmlEl.classList.contains('dark') ? 'dark' : 'light');
        });
    }
</script>
</body>
</html>