/**
 * NEON FLUX — Catalog JS
 * Placeholder for catalog interactivity like search filtering,
 * category switching, etc.
 */

document.addEventListener('DOMContentLoaded', () => {
    console.log('Neon Flux Catalog Initialized');

    // Future: Add search filter logic here
    const searchInput = document.querySelector('input[placeholder="Cari game favoritmu..."]');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            // Implement filtering logic...
        });
    }
});
