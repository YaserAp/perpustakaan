/* ==========================================================================
   LibrarySmart - Main Interactive Script
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Theme Toggle Logic
    const savedTheme = localStorage.getItem('library_theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    const themeBtn = document.getElementById('themeToggleBtn');
    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('library_theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }

    // 2. Sidebar Collapsible Toggle (Desktop & Tablet)
    const savedSidebarState = localStorage.getItem('library_sidebar_collapsed');
    if (savedSidebarState === 'true') {
        document.body.classList.add('sidebar-collapsed');
    }

    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('library_sidebar_collapsed', isCollapsed ? 'true' : 'false');
        });
    }

    // 3. Mobile Sidebar Toggle (Off-canvas drawer & Backdrop)
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.querySelector('.sidebar');
    
    // Create backdrop overlay for mobile
    let mobileOverlay = document.getElementById('mobileOverlay');
    if (!mobileOverlay) {
        mobileOverlay = document.createElement('div');
        mobileOverlay.id = 'mobileOverlay';
        mobileOverlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15,23,42,0.6); backdrop-filter: blur(2px); z-index: 1040; display: none; opacity: 0; transition: opacity 0.3s ease;';
        document.body.appendChild(mobileOverlay);
    }

    function toggleMobileSidebar() {
        if (!sidebar) return;
        const isOpen = sidebar.classList.contains('mobile-open');
        if (isOpen) {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.style.opacity = '0';
            setTimeout(() => { mobileOverlay.style.display = 'none'; }, 300);
        } else {
            sidebar.classList.add('mobile-open');
            mobileOverlay.style.display = 'block';
            setTimeout(() => { mobileOverlay.style.opacity = '1'; }, 10);
        }
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMobileSidebar();
        });
    }

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', (e) => {
            if (window.innerWidth <= 992) {
                e.stopPropagation();
                toggleMobileSidebar();
            }
        });
    }

    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', () => {
            if (sidebar && sidebar.classList.contains('mobile-open')) {
                toggleMobileSidebar();
            }
        });
    }

    // Auto-close mobile sidebar when clicking any nav item
    const navLinks = document.querySelectorAll('.sidebar .nav-item');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 992 && sidebar && sidebar.classList.contains('mobile-open')) {
                toggleMobileSidebar();
            }
        });
    });

    // 4. Client-side Live Filter for Tables & Cards
    const filterInput = document.getElementById('tableFilterInput');
    if (filterInput) {
        filterInput.addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            
            // Table Filter
            const tableRows = document.querySelectorAll('.custom-table tbody tr');
            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(val) ? '' : 'none';
            });

            // Card Filter
            const cards = document.querySelectorAll('.book-card');
            cards.forEach(card => {
                const text = card.innerText.toLowerCase();
                card.style.display = text.includes(val) ? '' : 'none';
            });
        });
    }
});

function updateThemeIcon(theme) {
    const themeBtn = document.getElementById('themeToggleBtn');
    if (themeBtn) {
        themeBtn.innerHTML = theme === 'dark' ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
    }
}

/* Modal Helper Functions */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

/* Toast Notifications */
function showToast(message, type = 'info') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `badge badge-${type}`;
    toast.style.cssText = 'padding: 12px 18px; font-size: 13.5px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; animation: slideIn 0.3s forwards;';
    toast.innerHTML = message;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}
