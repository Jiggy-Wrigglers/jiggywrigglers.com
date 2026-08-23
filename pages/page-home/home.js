document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.home-banner-slider').forEach((el) => {
        new Splide(el, {
            type: 'fade',
            rewind: true,
            autoplay: true,
            interval: 7000,
            speed: 1400,
            pagination: false,
            arrows: false,
            pauseOnHover: false,
            cover: true,
            height: '75lvh',
        }).mount();
    });
});
