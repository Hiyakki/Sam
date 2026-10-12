import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// SAM Orthotic Prosthetic Frontend Interactions
document.addEventListener('DOMContentLoaded', () => {
    // Scroll reveal observer for .animate-fade-up elements
    const fadeElements = document.querySelectorAll('.animate-fade-up');
    
    if (fadeElements.length > 0) {
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        if (prefersReduced) {
            fadeElements.forEach(el => el.classList.add('is-visible'));
        } else if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, {
                root: null,
                threshold: 0.1,
                rootMargin: '0px 0px -40px 0px',
            });

            fadeElements.forEach(el => observer.observe(el));
        } else {
            fadeElements.forEach(el => el.classList.add('is-visible'));
        }
    }
});
