/**
 * Lurnixe Health Card System - Public Main JS
 * June 2026
 */

document.addEventListener("DOMContentLoaded", function() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#') {
                const target = document.querySelector(targetId);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // Bootstrap Form validation utility
    const validationForms = document.querySelectorAll('.needs-validation');
    Array.from(validationForms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Profile page logout button (URL is supplied via data-logout-url, rendered server-side)
    const logoutBtn = document.querySelector('.profile-logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            const logoutUrl = logoutBtn.dataset.logoutUrl;
            if (!logoutUrl) return;
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = logoutUrl;
            }
        });
    }
});

