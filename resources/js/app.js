import Chart from 'chart.js/auto';

window.Chart = Chart;

// ---------- Scroll reveal ----------
document.addEventListener('DOMContentLoaded', () => {
    const revealEls = document.querySelectorAll('.reveal, .img-reveal');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12 }
        );
        revealEls.forEach((el) => observer.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('is-visible'));
    }

    // ---------- Counters ----------
    document.querySelectorAll('[data-counter]').forEach((el) => {
        const target = parseFloat(el.dataset.counter || '0');
        const suffix = el.dataset.suffix || '';
        const decimals = parseInt(el.dataset.decimals || '0', 10);
        const duration = 1600;
        const start = performance.now();

        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const value = target * eased;
            el.textContent = (decimals > 0 ? value.toFixed(decimals) : Math.round(value).toLocaleString('en-US')) + suffix;
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };
        requestAnimationFrame(step);
    });

    // ---------- Sidebar ----------
    const sidebar = document.getElementById('app-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const openBtn = document.getElementById('sidebar-open');
    const closeBtn = document.getElementById('sidebar-close');

    const openSidebar = () => {
        if (sidebar) sidebar.classList.remove('-translate-x-full');
        if (overlay) overlay.classList.remove('hidden');
    };
    const closeSidebar = () => {
        if (sidebar) sidebar.classList.add('-translate-x-full');
        if (overlay) overlay.classList.add('hidden');
    };
    if (openBtn) openBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // ---------- Mobile menu (landing) ----------
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOpen = document.getElementById('menu-open');
    const menuClose = document.getElementById('menu-close');
    const openMenu = () => {
        if (mobileMenu) mobileMenu.classList.remove('hidden');
        if (menuOpen) menuOpen.classList.add('hidden');
        if (menuClose) menuClose.classList.remove('hidden');
    };
    const closeMenu = () => {
        if (mobileMenu) mobileMenu.classList.add('hidden');
        if (menuOpen) menuOpen.classList.remove('hidden');
        if (menuClose) menuClose.classList.add('hidden');
    };
    if (menuOpen) menuOpen.addEventListener('click', openMenu);
    if (menuClose) menuClose.addEventListener('click', closeMenu);

    // ---------- Confirm modal ----------
    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            const message = form.dataset.confirm || 'Are you sure you want to proceed?';
            if (!window.confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ---------- Generic confirm via link ----------
    document.querySelectorAll('a[data-confirm]').forEach((link) => {
        link.addEventListener('click', (e) => {
            if (!window.confirm(link.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });

    // ---------- Print ----------
    document.querySelectorAll('[data-print]').forEach((btn) => {
        btn.addEventListener('click', () => window.print());
    });

    // ---------- Notification unread count ----------
    const notifBadge = document.getElementById('notif-badge');
    const unreadUrl = document.querySelector('meta[name="notif-url"]')?.content;
    if (notifBadge && unreadUrl) {
        const updateBadge = async () => {
            try {
                const res = await fetch(unreadUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                const data = await res.json();
                if (data.count > 0) {
                    notifBadge.textContent = data.count > 99 ? '99+' : data.count;
                    notifBadge.classList.remove('hidden');
                    notifBadge.classList.add('flex');
                } else {
                    notifBadge.classList.add('hidden');
                }
            } catch (e) {
                /* ignore */
            }
        };
        updateBadge();
        window.setInterval(updateBadge, 60000);
    }
});

// ---------- Chart factory ----------
window.equipflowChart = function (ctx, config) {
    return new Chart(ctx, config);
};

window.chartDefaults = {
    color: '#71717a',
    font: { family: 'Inter', size: 12 },
};

Chart.defaults.color = '#71717a';
Chart.defaults.font.family = 'Inter';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.tooltip.backgroundColor = '#0b1d33';
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.cornerRadius = 2;
Chart.defaults.borderColor = '#e4e4e7';

// ---------- Fetch helper ----------
window.equipflowFetch = async function (url, options = {}) {
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', ...(options.headers || {}) },
        ...options,
    });
    if (!res.ok) {
        throw new Error('Request failed');
    }
    return res.json();
};
