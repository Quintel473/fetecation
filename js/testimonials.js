/* =========================================================
   TESTIMONIALS SLIDER — arrow navigation with loop
   ========================================================= */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        var slider = document.getElementById('testimonialsSlider');
        if (!slider) return;

        var prevBtn = document.querySelector('.testimonials-nav-prev');
        var nextBtn = document.querySelector('.testimonials-nav-next');
        if (!prevBtn || !nextBtn) return;

        var cards = slider.querySelectorAll('.testimonial-card');
        if (!cards.length) return;

        /* Scroll amount = one card width + gap */
        function stepSize() {
            var first = cards[0];
            var style = window.getComputedStyle(slider);
            var gap = parseInt(style.columnGap || style.gap || '24', 10) || 24;
            return first.offsetWidth + gap;
        }

        /* Scroll by one card; wrap when at the ends */
        function scrollByOne(direction) {
            var step = stepSize();
            var maxScroll = slider.scrollWidth - slider.clientWidth;

            var atStart = slider.scrollLeft <= 4;
            var atEnd = slider.scrollLeft >= maxScroll - 4;

            if (direction === 1) {
                if (atEnd) {
                    slider.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    slider.scrollBy({ left: step, behavior: 'smooth' });
                }
            } else {
                if (atStart) {
                    slider.scrollTo({ left: slider.scrollWidth, behavior: 'smooth' });
                } else {
                    slider.scrollBy({ left: -step, behavior: 'smooth' });
                }
            }
        }

        nextBtn.addEventListener('click', function () { scrollByOne(1); });
        prevBtn.addEventListener('click', function () { scrollByOne(-1); });

        slider.setAttribute('tabindex', '0');
        slider.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                scrollByOne(1);
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                scrollByOne(-1);
            }
        });

    });

})();