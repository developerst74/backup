(function ($) {
    "use strict";

    function initSlider($scope) {
        var $container = $scope.find(".st-pro-slider-container");
        if (!$container.length) return;
        if ($container.data("st-swiper")) return;

        var settings = $container.data("settings") || {};
        var el = $container[0];

        var params = {
            loop: !!settings.loop,
            speed: parseInt(settings.speed, 10) || 500,
            effect: settings.effect || "slide",
            autoplay: settings.autoplay ? { delay: 3000, disableOnInteraction: false } : false,
            pagination: { el: $container.find(".swiper-pagination")[0] || null, clickable: true },
            navigation: { nextEl: $container.find(".swiper-button-next")[0] || null, prevEl: $container.find(".swiper-button-prev")[0] || null },
            on: {
                init: function () { runAnimations(this); },
                slideChangeTransitionEnd: function () { runAnimations(this); }
            }
        };

        if (settings.effect === "fade") { params.fadeEffect = { crossFade: true }; }

        if (typeof elementorFrontend !== "undefined" && elementorFrontend.utils && typeof elementorFrontend.utils.swiper === "function") {
            new elementorFrontend.utils.swiper(el, params).then(function (swiper) {
                $container.data("st-swiper", swiper);
                runAnimations(swiper);
            });
            return;
        }

        if (typeof window.Swiper !== "undefined") {
            var swiper = new window.Swiper(el, params);
            $container.data("st-swiper", swiper);
        }
    }

    function runAnimations(swiper) {
        if (!swiper || !swiper.slides || !swiper.slides.length) return;
        $(swiper.slides).find(".st-pro-content-inner").each(function () {
            var anim = $(this).data("animation");
            if (anim) { $(this).removeClass(anim); }
        });
        var $active = $(swiper.slides[swiper.activeIndex]);
        $active.find(".st-pro-content-inner").each(function () {
            var anim = $(this).data("animation");
            if (anim) { void this.offsetWidth; $(this).addClass(anim); }
        });
    }

    function initAccordion($scope) {
        var $accordion = $scope.find(".st-accordion-wrapper");
        if (!$accordion.length) return;
        var speed = parseInt($accordion.data("speed"), 10) || 300;
        var $items = $accordion.find(".st-accordion-item");
        $accordion.find(".st-accordion-title").on("click", function () {
            var $item = $(this).closest(".st-accordion-item");
            var $content = $item.find(".st-accordion-content");
            if ($item.hasClass("st-active")) return;
            $items.not($item).removeClass("st-active").find(".st-accordion-content").stop(true, true).slideUp(speed);
            $item.addClass("st-active");
            $content.stop(true, true).slideDown(speed);
        });
    }

    function registerHooks() {
        elementorFrontend.hooks.addAction("frontend/element_ready/st_slider.default", initSlider);
        elementorFrontend.hooks.addAction("frontend/element_ready/st_accordion.default", initAccordion);
        elementorFrontend.hooks.addAction("frontend/element_ready/st_flex_box.default", initFlexBox);
        elementorFrontend.hooks.addAction("frontend/element_ready/st_flip_box.default", initFlipBox);
        elementorFrontend.hooks.addAction("frontend/element_ready/st_flexible_layout.default", initFlexibleLayout);
    }

    // ── ST Flip Box slider init ───────────────────────────────────────
    function initFlipBox($scope) {
        var $wrap = $scope.find(".st-flipbox-wrap");
        if (!$wrap.length) return;
        if (!$wrap.hasClass("swiper")) return;
        if ($wrap.data("st-flip-init")) return;
        $wrap.data("st-flip-init", true);

        var data   = $wrap.data("slider") || {};
        var spv    = parseInt(data.slidesPerView, 10) || 3;
        var spvTab = parseInt(data.slidesTablet,  10) || 2;
        var spvMob = parseInt(data.slidesMobile,  10) || 1;
        var space  = parseInt(data.spaceBetween,  10) || 24;
        var loop   = data.loop  !== false;
        var speed  = parseInt(data.speed, 10) || 500;
        var auto   = !!data.autoplay;
        var delay  = parseInt(data.autoplayDelay, 10) || 3000;

        var params = {
            slidesPerView: spvMob,
            spaceBetween:  Math.round(space / 2),
            loop:          loop,
            speed:         speed,
            autoplay:      auto ? { delay: delay, disableOnInteraction: false } : false,
            pagination: {
                el:        $wrap.find(".st-flipbox-dots")[0] || null,
                clickable: true,
            },
            navigation: {
                nextEl: $wrap.find(".swiper-button-next")[0] || null,
                prevEl: $wrap.find(".swiper-button-prev")[0] || null,
            },
            breakpoints: {
                320:  { slidesPerView: spvMob, spaceBetween: Math.round(space / 2) },
                768:  { slidesPerView: spvTab, spaceBetween: Math.round(space / 1.5) },
                1025: { slidesPerView: spv,    spaceBetween: space },
            },
        };

        var el = $wrap[0];

        if (typeof elementorFrontend !== "undefined" &&
            elementorFrontend.utils &&
            typeof elementorFrontend.utils.swiper === "function") {
            new elementorFrontend.utils.swiper(el, params);
        } else if (typeof window.Swiper !== "undefined") {
            new window.Swiper(el, params);
        }
    }

    // ── ST Flex Box slider init ───────────────────────────────────────
    function initFlexBox($scope) {
        var $wrap = $scope.find(".st-fb-wrap");
        if (!$wrap.length) return;
        if (!$wrap.hasClass("st-fb-slider")) return;
        if ($wrap.data("st-fb-init")) return;
        $wrap.data("st-fb-init", true);

        var data   = $wrap.data("slider") || {};
        var spv    = parseInt(data.slidesPerView, 10) || 3;
        var spvTab = parseInt(data.slidesTablet,  10) || 2;
        var spvMob = parseInt(data.slidesMobile,  10) || 1;
        var space  = parseInt(data.spaceBetween,  10) || 20;
        var loop   = data.loop  !== false;
        var speed  = parseInt(data.speed, 10) || 500;
        var auto   = !!data.autoplay;
        var delay  = parseInt(data.autoplayDelay, 10) || 3000;

        var params = {
            slidesPerView: spvMob,
            spaceBetween:  Math.round(space / 2),
            loop:          loop,
            speed:         speed,
            autoplay:      auto ? { delay: delay, disableOnInteraction: false } : false,
            pagination: {
                el:        $wrap.find(".st-fb-dots")[0] || null,
                clickable: true,
            },
            navigation: {
                nextEl: $wrap.find(".swiper-button-next")[0] || null,
                prevEl: $wrap.find(".swiper-button-prev")[0] || null,
            },
            breakpoints: {
                320:  { slidesPerView: spvMob, spaceBetween: Math.round(space / 2) },
                768:  { slidesPerView: spvTab, spaceBetween: Math.round(space / 1.5) },
                1025: { slidesPerView: spv,    spaceBetween: space },
            },
        };

        var el = $wrap[0];

        if (typeof elementorFrontend !== "undefined" &&
            elementorFrontend.utils &&
            typeof elementorFrontend.utils.swiper === "function") {
            new elementorFrontend.utils.swiper(el, params);
        } else if (typeof window.Swiper !== "undefined") {
            new window.Swiper(el, params);
        }
    }

    function initFlexibleLayout($scope) {
        var $container = $scope.find(".st-fl-swiper");
        if (!$container.length) return;
        if ($container.data("st-fl-initialized")) return;

        var $wrapper = $scope.find(".st-fl-wrapper");
        var uniqueId = $wrapper.attr("class").match(/st-fl-[^\s]+/);
        if (!uniqueId) return;
        uniqueId = uniqueId[0];

        var settings = {
            slidesPerView: parseInt($container.data("spv"), 10) || 3,
            slidesPerViewTablet: parseInt($container.data("spv-tablet"), 10) || 2,
            slidesPerViewMobile: parseInt($container.data("spv-mobile"), 10) || 1,
            spaceBetween: parseInt($container.data("space"), 10) || 20,
            loop: $container.data("loop") !== false,
            speed: parseInt($container.data("speed"), 10) || 500,
            effect: $container.data("effect") || "slide",
            direction: $container.data("direction") || "horizontal",
            autoplay: $container.data("autoplay") ? { delay: parseInt($container.data("autoplay-delay"), 10) || 3000, disableOnInteraction: false } : false,
            centeredSlides: $container.data("centered") === true
        };

        var params = {
            loop: settings.loop,
            speed: settings.speed,
            effect: settings.effect,
            direction: settings.direction,
            centeredSlides: settings.centeredSlides,
            pagination: {
                el: "." + uniqueId + " .st-fl-pagination",
                type: $container.data("pagination-type") || "bullets",
                clickable: true
            },
            navigation: {
                nextEl: "." + uniqueId + " .st-fl-arrow-next",
                prevEl: "." + uniqueId + " .st-fl-arrow-prev"
            },
            breakpoints: {
                768: {
                    slidesPerView: settings.slidesPerViewTablet,
                    spaceBetween: settings.spaceBetween / 2
                },
                480: {
                    slidesPerView: settings.slidesPerViewMobile,
                    spaceBetween: settings.spaceBetween / 2
                }
            },
            on: {
                init: function() {
                    $container.data("st-fl-initialized", true);
                }
            }
        };

        if (settings.autoplay) {
            params.autoplay = settings.autoplay;
        }

        if (settings.effect === "fade") {
            params.fadeEffect = { crossFade: true };
        } else if (settings.effect === "coverflow") {
            params.coverflowEffect = {
                rotate: 30,
                stretch: 0,
                depth: 60,
                modifier: 2,
                slideShadows: true
            };
        }

        if (typeof elementorFrontend !== "undefined" && elementorFrontend.utils && typeof elementorFrontend.utils.swiper === "function") {
            new elementorFrontend.utils.swiper($container[0], params);
        } else if (typeof window.Swiper !== "undefined") {
            new window.Swiper($container[0], params);
        }
    }

    $(window).on("elementor/frontend/init", registerHooks);

})(jQuery);

// ── ST Projects List ──────────────────────────────────────────────────
(function ($) {
    'use strict';

    function closeYearCard($card) {
        var $panel = $card.find('.st-projects-panel').first();
        if (!$panel.length) return;
        $card.removeClass('is-open');
        $card.find('.st-projects-year-toggle').attr('aria-expanded', 'false');
        $panel.stop(true, true).slideUp(180, function () {
            $panel.attr('hidden', 'hidden');
        });
    }

    function openYearCard($card) {
        var $panel = $card.find('.st-projects-panel').first();
        if (!$panel.length) return;
        $card.addClass('is-open');
        $card.find('.st-projects-year-toggle').attr('aria-expanded', 'true');

        if ($panel.attr('hidden')) {
            $panel.removeAttr('hidden').hide();
        }
        $panel.stop(true, true).slideDown(200);
    }

    $(document).on('click', '.st-projects-year-toggle', function () {
        var $btn = $(this);
        if ($btn.is(':disabled')) return;

        var $card = $btn.closest('.st-projects-card');
        if (!$card.length) return;

        var isOpen = $card.hasClass('is-open');

        $card.closest('.st-projects-wrap').find('.st-projects-card.is-open').not($card).each(function () {
            closeYearCard($(this));
        });

        if (isOpen) {
            closeYearCard($card);
        } else {
            openYearCard($card);
        }
    });
})(jQuery);

// ── ST Login Form — set testcookie before submit ─────────────────────
(function ($) {
    'use strict';

    // Set testcookie on page load so WordPress cookie check passes
    document.cookie = 'wordpress_test_cookie=WP+Cookie+check; path=/';

    $(document).on('submit', '.st-login-form[data-ajax-login]', function (e) {
        e.preventDefault();

        var $form   = $(this);
        var $btn    = $form.find('.st-login-btn');
        var $error  = $form.find('.st-login-error');

        $btn.prop('disabled', true).css('opacity', '0.7');
        if (!$error.length) {
            $form.prepend('<div class="st-login-error" style="display:none"></div>');
            $error = $form.find('.st-login-error');
        }
        $error.hide();

        var data = {
            action:   'st_ajax_login',
            log:      $form.find('input[name="log"]').val(),
            pwd:      $form.find('input[name="pwd"]').val(),
            rememberme: $form.find('input[name="rememberme"]').is(':checked') ? 'forever' : '',
            redirect:   $form.find('input[name="redirect_to"]').val() || window.location.href,
            nonce:    (typeof stLoginData !== 'undefined') ? stLoginData.nonce : '',
        };

        $.post((typeof stLoginData !== 'undefined') ? stLoginData.ajaxUrl : '/wp-admin/admin-ajax.php', data, function (res) {
            if (res.success) {
                window.location.href = res.data.redirect || window.location.href;
            } else {
                $error.text(res.data || 'Invalid username or password.').show();
                $btn.prop('disabled', false).css('opacity', '1');
            }
        }).fail(function () {
            $error.text('Login failed. Please try again.').show();
            $btn.prop('disabled', false).css('opacity', '1');
        });
    });
})(jQuery);

// ── ST Hotspot Widget ─────────────────────────────────────────────────
(function ($) {
    'use strict';

    function initHotspot($scope) {
        var $wrap    = $scope.find('.st-hs-wrap');
        if (!$wrap.length) return;
        var trigger  = $wrap.data('trigger') || 'hover';

        $wrap.on('click', '.st-hs-point', function (e) {
            var $btn    = $(this);
            var action  = $btn.data('action') || 'tooltip';
            var $item   = $btn.closest('.st-hs-item');

            // Mini popup action
            if (action === 'popup') {
                e.preventDefault();
                e.stopPropagation();
                var $popup = $item.find('.st-hs-popup');
                if ($popup.length) {
                    $popup.addClass('st-hs-popup-open');
                    $('body').css('overflow', 'hidden');
                }
                return;
            }

            // ST Popup widget action
            if (action === 'st-popup') {
                var popupId = $btn.data('st-popup');
                if (popupId) {
                    e.preventDefault();
                    var $overlay = $('#st-popup-' + popupId);
                    if ($overlay.length) {
                        var animation = $overlay.data('animation') || 'fadeIn';
                        $overlay.addClass('st-popup-anim-' + animation).fadeIn(250);
                        $('body').css('overflow', 'hidden');
                    }
                }
                return;
            }

            // Tooltip click toggle
            if (trigger === 'click' && action === 'tooltip') {
                e.stopPropagation();
                var isActive = $item.hasClass('st-hs-active');
                $wrap.find('.st-hs-item').removeClass('st-hs-active');
                if (!isActive) $item.addClass('st-hs-active');
            }
        });

        // Close mini popup
        $wrap.on('click', '.st-hs-popup-close', function (e) {
            e.stopPropagation();
            $(this).closest('.st-hs-popup').removeClass('st-hs-popup-open');
            $('body').css('overflow', '');
        });
        $wrap.on('click', '.st-hs-popup', function (e) {
            if ($(e.target).is('.st-hs-popup')) {
                $(this).removeClass('st-hs-popup-open');
                $('body').css('overflow', '');
            }
        });

        // Close tooltip on outside click
        if (trigger === 'click') {
            $(document).on('click.stHotspot', function () {
                $wrap.find('.st-hs-item').removeClass('st-hs-active');
            });
        }
    }

    if (typeof elementorFrontend !== 'undefined') {
        $(window).on('elementor/frontend/init', function () {
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/st_hotspot.default',
                initHotspot
            );
        });
    }

    $(document).ready(function () {
        if (typeof elementorFrontend === 'undefined') {
            $('.elementor-widget-st_hotspot').each(function () {
                initHotspot($(this));
            });
        }
    });
})(jQuery);

// ── ST Table of Contents ─────────────────────────────────────────────
(function ($) {
    'use strict';

    function initTOC($scope) {
        var $wrap = $scope.find('.st-toc-wrap');
        if (!$wrap.length) return;
        if ($wrap.data('st-toc-init')) return;
        $wrap.data('st-toc-init', true);

        // Wait for Elementor to render all widgets
        setTimeout(function() { buildTOC($scope, $wrap); }, 500);
    }

    function buildTOC($scope, $wrap) {
        var smooth   = $wrap.data('smooth') == '1';
        var offset   = parseInt($wrap.data('offset'), 10) || 80;
        var tagsAttr = $wrap.data('tags') || 'h2,h3';
        var tags     = tagsAttr.split(',').map(function(t){ return t.trim(); }).filter(Boolean);
        var $list    = $wrap.find('.st-toc-list');

        // Search ALL heading tags on page — plain + Elementor heading widget
        var selector = tags.join(',');

        var $tocWidget = $scope;
        var $headings  = $(selector).filter(function() {
            return !$.contains($tocWidget[0], this);
        });

        if (!$headings.length) {
            $list.html('<li style="color:#9ca3af;font-size:13px;padding:8px 0">' +
                'No headings found. Add H2/H3 headings to this page.' + '</li>');
            return;
        }

        // Assign IDs
        var slugCount = {};
        $headings.each(function () {
            if (!this.id) {
                var base = $(this).text().trim()
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .substring(0, 60) || 'heading';
                slugCount[base] = (slugCount[base] || 0) + 1;
                this.id = slugCount[base] > 1 ? base + '-' + slugCount[base] : base;
            }
        });

        // Tag level map
        var tagLevels = {};
        tags.forEach(function(t, i) { tagLevels[t.toLowerCase()] = i + 1; });

        var isHierarchical = $list.hasClass('st-toc-hierarchical');
        var html = '', prevLevel = 0, openUls = 0;

        $headings.each(function () {
            var tag   = this.tagName.toLowerCase();
            var level = tagLevels[tag] || 1;
            var id    = this.id;
            var text  = $(this).text().trim();

            if (isHierarchical) {
                if (prevLevel === 0) {
                    // first
                } else if (level > prevLevel) {
                    html += '<ul>'; openUls++;
                } else if (level < prevLevel) {
                    for (var i = level; i < prevLevel; i++) {
                        html += '</li></ul>';
                        if (openUls > 0) openUls--;
                    }
                    html += '</li>';
                } else {
                    html += '</li>';
                }
            } else {
                if (prevLevel > 0) html += '</li>';
            }

            html += '<li class="st-toc-item st-toc-' + tag + '">';
            html += '<a href="#' + id + '" data-target="' + id + '">' + text + '</a>';
            prevLevel = level;
        });

        html += '</li>';
        for (var k = 0; k < openUls; k++) { html += '</ul></li>'; }
        $list.html(html);

        // Smooth scroll
        $list.on('click', 'a', function (e) {
            e.preventDefault();
            var targetId = $(this).data('target');
            var $target  = $('#' + targetId);
            if ($target.length) {
                var top = $target.offset().top - offset;
                if (smooth) {
                    $('html, body').animate({ scrollTop: top }, 400);
                } else {
                    $(window).scrollTop(top);
                }
            }
        });

        // Active on scroll
        $(window).on('scroll.stTOC_' + $scope.attr('data-id'), function () {
            var scrollTop = $(window).scrollTop() + offset + 20;
            var activeId  = '';
            $headings.each(function () {
                if ($(this).offset().top <= scrollTop) activeId = this.id;
            });
            $list.find('a').removeClass('st-toc-active');
            if (activeId) $list.find('a[data-target="' + activeId + '"]').addClass('st-toc-active');
        });
    }

    // Register
    if (typeof elementorFrontend !== 'undefined') {
        $(window).on('elementor/frontend/init', function () {
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/st_table_of_contents.default',
                initTOC
            );
        });
    }

    $(document).ready(function () {
        if (typeof elementorFrontend === 'undefined') {
            $('.elementor-widget-st_table_of_contents').each(function () {
                initTOC($(this));
            });
        }
    });

})(jQuery);

// ── ST Flex Box Lightbox ─────────────────────────────────────────────
(function ($) {
    'use strict';

    // Create lightbox overlay once
    var $overlay = null;

    function getLightbox() {
        if ($overlay && $overlay.length) return $overlay;
        $overlay = $(
            '<div class="st-lightbox-overlay" id="st-lightbox">' +
            '<div class="st-lightbox-img-wrap">' +
            '<button class="st-lightbox-close" aria-label="Close">&#x2715;</button>' +
            '<img src="" alt="">' +
            '</div>' +
            '</div>'
        );
        $('body').append($overlay);

        // Close on overlay click
        $overlay.on('click', function (e) {
            if ($(e.target).is($overlay)) closeLightbox();
        });
        // Close button
        $overlay.on('click', '.st-lightbox-close', closeLightbox);
        // ESC key
        $(document).on('keydown.stLightbox', function (e) {
            if (e.key === 'Escape') closeLightbox();
        });

        return $overlay;
    }

    function openLightbox(src) {
        var $lb = getLightbox();
        $lb.find('img').attr('src', src);
        $lb.addClass('st-lightbox-open');
        $('body').css('overflow', 'hidden');
    }

    function closeLightbox() {
        if ($overlay) $overlay.removeClass('st-lightbox-open');
        $('body').css('overflow', '');
    }

    // Delegate click on lightbox triggers
    $(document).on('click', '.st-fb-lightbox-trigger', function (e) {
        e.preventDefault();
        var src = $(this).data('src') || $(this).find('img').attr('src');
        if (src) openLightbox(src);
    });

    // Keyboard accessibility
    $(document).on('keydown', '.st-fb-lightbox-trigger', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });

})(jQuery);
