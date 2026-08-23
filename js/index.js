window.lenis = new Lenis({
    autoRaf: true,
    autoToggle: true,
    anchors: true,
    allowNestedScroll: true,
    naiveDimensions: true,
    stopInertiaOnNavigate: true,
});

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
