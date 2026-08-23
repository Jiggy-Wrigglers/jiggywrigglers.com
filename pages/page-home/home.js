document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.home-banner-slider').forEach((el) => {
        new Splide(el, {
            type: 'loop',
            autoplay: true,
            interval: 5000,
            speed: 800,
            pagination: true,
            arrows: false,
            pauseOnHover: false,
        }).mount();
    });
});
