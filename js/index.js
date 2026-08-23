// Flag JS availability FIRST: the CSS only hides [data-animate] on
// html.anim, so content is never permanently invisible if anything
// below this line fails to load or throws.
document.documentElement.classList.add('anim');

// Scroll reveal: add .is-visible to [data-animate] elements as they
// enter the viewport. Works with the CSS animation utilities in style.css.
const initRevealAnimations = () => {
    const els = document.querySelectorAll('[data-animate]');
    if (!els.length) return;

    if (!('IntersectionObserver' in window)) {
        els.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -5% 0px',
    });

    els.forEach((el) => observer.observe(el));
};
document.addEventListener('DOMContentLoaded', initRevealAnimations);

// Smooth scroll. Guarded: if the Lenis CDN fails the rest of the file
// (and the reveal engine above) still runs. Retried on DOMContentLoaded
// in case script ordering left Lenis unavailable at parse time.
const initLenis = () => {
    if (window.lenis || !window.Lenis) return;
    window.lenis = new Lenis({
        autoRaf: true,
        autoToggle: true,
        anchors: true,
        allowNestedScroll: true,
        naiveDimensions: true,
        stopInertiaOnNavigate: true,
    });
};
initLenis();
document.addEventListener('DOMContentLoaded', initLenis);

document.addEventListener('alpine:init', () => {
    Alpine.data('parallaxItems', () => ({
        init() {
            this.running = true;
            this.loop();
        },
        destroy() {
            this.running = false;
        },
        loop() {
            if (!this.running) return;
            this.updateParallax();
            requestAnimationFrame(() => this.loop());
        },
        updateParallax() {
            const windowH = window.innerHeight;

            this.$el.querySelectorAll('[data-parallax]').forEach((el) => {
                const speed = parseFloat(el.dataset.parallax) || 0.5;
                const rect = el.getBoundingClientRect();
                const elCenter = rect.top + rect.height / 2;
                const offset = (elCenter - windowH / 2) * speed * -0.15;
                el.style.transform = `translateY(${offset}px)`;
            });

            this.$el.querySelectorAll('[data-parallax-img]').forEach((el) => {
                const img = el.querySelector('img');
                if (!img) return;
                const rect = el.getBoundingClientRect();
                const progress = (windowH - rect.top) / (windowH + rect.height);
                const clamped = Math.max(0, Math.min(1, progress));
                img.style.top = `${-10 * clamped}%`;
            });
        }
    }));
});

function headerMenuFocusTrap() {
    return {
        trapFocus(event) {
            const container = this.$el;
            const isReverse = event.shiftKey;
            const focusableSelectors = [
                'a[href]',
                'button:not([disabled])',
                'input:not([disabled]):not([type="hidden"])',
                'select:not([disabled])',
                'textarea:not([disabled])',
                '[tabindex]:not([tabindex="-1"])'
            ];

            const focusable = Array.from(
                container.querySelectorAll(focusableSelectors.join(','))
            ).filter(el =>
                !el.hasAttribute('disabled') &&
                !el.closest('[inert]') &&
                el.getAttribute('aria-hidden') !== 'true' &&
                el.getClientRects().length > 0
            );

            if (!focusable.length) {
                return;
            }

            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            const current = document.activeElement;
            const currentIndex = focusable.indexOf(current);

            if (isReverse) {
                if (current === first || currentIndex === -1) {
                    event.preventDefault();
                    last.focus();
                }
            } else {
                if (current === last || currentIndex === -1) {
                    event.preventDefault();
                    first.focus();
                }
            }
        }
    }
}