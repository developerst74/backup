(function ($) {
    'use strict';

    function initStickyElements() {
        $('[data-st-sticky="true"]').each(function () {
            var $el        = $(this);
            var condition  = $el.data('st-sticky-condition') || 'always';
            var offset     = parseInt($el.data('st-sticky-offset'), 10) || 100;
            var lastScroll = 0;
            var $placeholder = null;

            function makeSticky() {
                if ($el.hasClass('st-is-sticky')) return;

                // Insert placeholder to prevent layout jump
                var height = $el.outerHeight();
                $placeholder = $('<div class="st-sticky-placeholder"></div>').css('height', height);
                $el.after($placeholder);

                $el.addClass('st-is-sticky');
            }

            function removeSticky() {
                if (!$el.hasClass('st-is-sticky')) return;
                $el.removeClass('st-is-sticky');
                if ($placeholder) {
                    $placeholder.remove();
                    $placeholder = null;
                }
            }

            $(window).on('scroll.stSticky', function () {
                var scrollTop = $(window).scrollTop();

                if (condition === 'always') {
                    scrollTop > 0 ? makeSticky() : removeSticky();

                } else if (condition === 'scroll_up') {
                    if (scrollTop < lastScroll && scrollTop > 100) {
                        makeSticky();
                    } else {
                        removeSticky();
                    }
                    lastScroll = scrollTop;

                } else if (condition === 'after_offset') {
                    scrollTop > offset ? makeSticky() : removeSticky();
                }
            });

            // Trigger once on load
            $(window).trigger('scroll.stSticky');
        });
    }

    // Run on DOM ready
    $(document).ready(function () {
        initStickyElements();
    });

    // Re-run when Elementor frontend initialises (editor preview)
    $(window).on('elementor/frontend/init', function () {
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/section', function () {
                initStickyElements();
            });
            elementorFrontend.hooks.addAction('frontend/element_ready/container', function () {
                initStickyElements();
            });
        }
    });

})(jQuery);
