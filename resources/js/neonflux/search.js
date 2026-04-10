/**
 * NEON FLUX — SMART SEARCH LOGIC (Vanilla JS)
 */
(function() {
    const modal = document.getElementById('nf-search-modal');
    const content = document.getElementById('nf-search-content');
    const input = document.getElementById('nf-search-input');
    const overlay = document.getElementById('nf-search-close-overlay');
    const triggers = document.querySelectorAll('.nf-search-trigger');
    const resultsWrap = document.getElementById('nf-search-results-list');
    const initialView = document.getElementById('nf-search-initial');
    const loadingView = document.getElementById('nf-search-loading');
    const emptyView = document.getElementById('nf-search-empty');
    
    if (!modal || !input) return;

    let searchTimeout = null;
    let selectedIndex = -1;

    function openModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(() => input.focus(), 100);
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        input.value = '';
        resetResults();
    }

    function resetResults() {
        resultsWrap.innerHTML = '';
        resultsWrap.classList.add('hidden');
        initialView.classList.remove('hidden');
        loadingView.classList.add('hidden');
        emptyView.classList.add('hidden');
        selectedIndex = -1;
    }

    // Toggle shortcut (Cmd+K or Ctrl+K)
    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            openModal();
        }
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    if (triggers.length > 0) {
        triggers.forEach(trigger => {
            trigger.addEventListener('click', openModal);
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeModal);
    }

    // Search Input Logic
    input.addEventListener('input', (e) => {
        const val = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        if (val.length < 2) {
            resetResults();
            return;
        }

        searchTimeout = setTimeout(() => performSearch(val), 300);
    });

    async function performSearch(q) {
        initialView.classList.add('hidden');
        loadingView.classList.remove('hidden');
        emptyView.classList.add('hidden');
        resultsWrap.classList.add('hidden');

        try {
            const res = await fetch(`/api/v1/search?q=${encodeURIComponent(q)}`);
            const data = await res.json();
            
            loadingView.classList.add('hidden');
            
            if (data.length === 0) {
                emptyView.classList.remove('hidden');
                return;
            }

            renderResults(data);
        } catch (err) {
            console.error('Search failed:', err);
            loadingView.classList.add('hidden');
        }
    }

    function renderResults(games) {
        resultsWrap.innerHTML = games.map((game, i) => `
            <a href="${game.slug}" class="search-result-item flex items-center gap-4 px-6 py-3 hover:bg-primary/10 transition-colors group" data-index="${i}">
                <div class="size-10 rounded-lg overflow-hidden border border-white/10 shrink-0">
                    <img src="${game.icon}" alt="${game.name}" class="size-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white group-hover:text-primary transition-colors truncate">${game.name}</p>
                    <p class="text-[10px] text-gray-400 capitalize">${game.platform || game.type || 'Game'}</p>
                </div>
                <span class="material-icons-round text-sm text-gray-600 group-hover:text-primary transition-colors">chevron_right</span>
            </a>
        `).join('');
        
        resultsWrap.classList.remove('hidden');
        selectedIndex = -1;
    }

    // Keyboard Navigation in Results
    input.addEventListener('keydown', (e) => {
        const items = resultsWrap.querySelectorAll('.search-result-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            updateSelection(items);
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            items[selectedIndex].click();
        }
    });

    function updateSelection(items) {
        items.forEach((item, i) => {
            if (i === selectedIndex) {
                item.classList.add('bg-primary/10', 'border-l-4', 'border-primary');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('bg-primary/10', 'border-l-4', 'border-primary');
            }
        });
    }

})();
