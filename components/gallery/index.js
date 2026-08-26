document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.gallery-slider').forEach((el) => {
        const prevBtn = el.querySelector('.gallery-prev');
        const nextBtn = el.querySelector('.gallery-next');

        const splide = new Splide(el, {
            type: 'loop',
            perPage: 3,
            perMove: 1,
            gap: 'var(--space-5)',
            pagination: false,
            arrows: false,
            breakpoints: {
                1080: {
                    perPage: 2,
                },
                650: {
                    perPage: 1,
                },
            },
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                splide.go('-1');
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                splide.go('+1');
            });
        }

        splide.mount();
    });
});
