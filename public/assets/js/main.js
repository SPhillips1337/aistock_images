// Lazy loading for images
document.addEventListener('DOMContentLoaded', function () {
    // Lazy load images
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.add('fade-in-up');
                observer.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Add animation to elements on scroll
    const animateOnScroll = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('.category-card, .image-card').forEach(el => {
        animateOnScroll.observe(el);
    });
});

// Download tracking
function trackDownload(imageId) {
    fetch('/api/download.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ image_id: imageId })
    }).catch(err => console.error('Download tracking failed:', err));
}

// Search autocomplete (basic implementation)
const searchInput = document.querySelector('input[name="q"]');
if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value;

        if (query.length < 2) return;

        searchTimeout = setTimeout(() => {
            // Could implement autocomplete suggestions here
            console.log('Search query:', query);
        }, 300);
    });
}
