// filter-controls.js
// Behavior moved out of CSS file. Loads after parsing (`defer`) so DOM is available.
document.addEventListener('DOMContentLoaded', function() {
    const sortButtons = document.querySelectorAll('.sort-btn');
    sortButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            // Update active state
            sortButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Reload page with new sort
            const sort = this.dataset.sort;
            try {
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('sort', sort);
                window.location.href = currentUrl.toString();
            } catch (err) {
                // Fallback for older browsers
                const params = new URLSearchParams(window.location.search);
                params.set('sort', sort);
                window.location.href = window.location.pathname + '?' + params.toString();
            }
        });
    });

    // Handle alphabet navigation
    const alphaLinks = document.querySelectorAll('.alpha-link');
    alphaLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const letter = this.dataset.letter;
            const targetSection = document.getElementById('letter-' + letter);

            if (targetSection) {
                targetSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });

                // Update active state
                alphaLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // Update active alphabet letter based on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const letter = entry.target.id.replace('letter-', '');
                alphaLinks.forEach(l => {
                    l.classList.remove('active');
                    if (l.dataset.letter === letter) {
                        l.classList.add('active');
                    }
                });
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('.category-section').forEach(section => {
        observer.observe(section);
    });
});

// Example: injecting additional runtime-only styles (optional)
// const injected = document.createElement('style');
// injected.id = 'filter-controls-inline';
// injected.textContent = '.alpha-letter { transition: transform 0.15s ease; }';
// document.head.appendChild(injected);
