// Admin Sidebar Toggle Functionality

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.admin-sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');

    // Load sidebar state from localStorage
    const sidebarState = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarState && window.innerWidth > 768) {
        sidebar?.classList.add('collapsed');
        sidebarToggle?.classList.add('collapsed');
    }

    // Toggle sidebar collapse (desktop)
    sidebarToggle?.addEventListener('click', function() {
        if (window.innerWidth > 768) {
            sidebar?.classList.toggle('collapsed');
            sidebarToggle?.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar?.classList.contains('collapsed'));
        } else {
            // Mobile: toggle show class
            sidebar?.classList.toggle('show');
            sidebarOverlay?.classList.toggle('show');
        }
    });

    // Close sidebar on overlay click (mobile)
    sidebarOverlay?.addEventListener('click', function() {
        sidebar?.classList.remove('show');
        sidebarOverlay?.classList.remove('show');
    });

    // Close sidebar when clicking a link (mobile)
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar?.classList.remove('show');
                sidebarOverlay?.classList.remove('show');
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar?.classList.remove('show');
            sidebarOverlay?.classList.remove('show');
        }
    });

    // Set active link based on current page
    setActiveNavLink();
});

function setActiveNavLink() {
    const currentPage = new URLSearchParams(window.location.search).get('page');
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        
        // Check if link href contains current page
        const href = link.getAttribute('href');
        if (href && href.includes(currentPage)) {
            link.classList.add('active');
        }
    });
}
