document.addEventListener('DOMContentLoaded', () => {
    const themeToggles = document.querySelectorAll('.nf-theme-toggle');

    function updateIcons(isDark) {
        themeToggles.forEach(toggle => {
            const icon = toggle.querySelector('.material-icons-round');
            if (icon) {
                icon.textContent = isDark ? 'light_mode' : 'dark_mode';
            }
        });
    }

    function toggleTheme() {
        const html = document.documentElement;
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            updateIcons(false);
        } else {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            updateIcons(true);
        }
        
        // Dispatch event for other components if needed
        window.dispatchEvent(new Event('theme-changed'));
    }

    themeToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            toggleTheme();
        });
    });

    // Listen for system theme changes if no theme is explicitly set
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('theme')) {
            const isDark = e.matches;
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            updateIcons(isDark);
        }
    });

    // Initialize icons on load
    updateIcons(document.documentElement.classList.contains('dark'));
});
