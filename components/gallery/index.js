document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.gallery-slider').forEach((el) => {
        new Splide(el, {
            type: 'loop',
            perPage: 3,
            perMove: 1,
            gap: 'var(--space-5)',
            pagination: true,
            arrows: false,
            breakpoints: {
                1080: {
                    perPage: 2,
                },
                650: {
                    perPage: 1,
                },
            },
        }).mount();
    });
});
