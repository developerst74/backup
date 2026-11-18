(function ($) {
    'use strict';

    function initNavMenu($scope) {
        var $wrap = $scope.find('.st-nav-wrap');
        if (!$wrap.length) return;

        var s          = $wrap.data('settings') || {};
        var effect     = s.effect    || 'fade';
        var trigger    = s.trigger   || 'hover';
        var breakpoint = parseInt(s.breakpoint, 10); // 0 = None = always desktop
        var sticky     = !!s.sticky;
        var stickyOff  = parseInt(s.stickyOffset, 10) || 80;
        var stickyShadow = !!s.stickyShadow;

        var widgetId   = ($wrap.attr('id') || '').replace('st-nav-', '');
        var $hamburger = $wrap.find('.st-hamburger-btn');
        var $desktop   = $wrap.find('.st-nav-desktop');
        var $panel     = $('#st-panel-' + widgetId);
        var $backdrop  = $('#st-backdrop-' + widgetId);
        var isOpen     = false;

        // Inject logo into panel header
        var $siteLogo = $('.st-logo-wrap').first();
        var $panelLogo = $panel.find('.st-mob-panel-logo');
        if ($panelLogo.length && $siteLogo.length) {
            var logoHtml = $siteLogo.clone().html();
            $panelLogo.html(logoHtml);
        } else if ($panelLogo.length) {
            // Fallback: site name
            $panelLogo.text(typeof siteTitle !== 'undefined' ? siteTitle : document.title.split('–')[0].trim());
        }

        // ── Sticky ───────────────────────────────────────────────────
        if (sticky) {
            // Create spacer to prevent content jump
            var $spacer = $('<div class="st-sticky-spacer"></div>');
            $wrap.after($spacer);

            $(window).on('scroll.stSticky_' + widgetId, function () {
                var scrollTop = $(window).scrollTop();
                if (scrollTop > stickyOff) {
                    if (!$wrap.hasClass('st-sticky-active')) {
                        var h = $wrap.outerHeight();
                        $spacer.css('height', h + 'px').show();
                        $wrap.addClass('st-sticky-active');
                        if (stickyShadow) $wrap.css('box-shadow', '0 2px 20px rgba(0,0,0,.12)');
                        $('body').addClass('st-has-sticky-header');
                    }
                } else {
                    if ($wrap.hasClass('st-sticky-active')) {
                        $wrap.removeClass('st-sticky-active').css('box-shadow', '');
                        $spacer.hide().css('height', '');
                        $('body').removeClass('st-has-sticky-header');
                    }
                }
            });
        }

        // ── Desktop dropdown ─────────────────────────────────────────
        var $items = $desktop.find('.st-has-dropdown');

        function openDropdown($item) {
            $items.not($item).each(function () { closeDropdown($(this)); });
            $item.addClass('st-open');
            var $sub = $item.children('.st-sub-menu');
            $sub.stop(true, true);
            if (effect === 'slide') { $sub.slideDown(220); }
            else { $sub.fadeIn(200); }
        }

        function closeDropdown($item) {
            $item.removeClass('st-open');
            var $sub = $item.children('.st-sub-menu');
            if (effect === 'slide') { $sub.slideUp(180); } else { $sub.fadeOut(150); }
        }

        if (trigger === 'hover') {
            $items.on('mouseenter', function () {
                if (breakpoint === 0 || $(window).width() > breakpoint) openDropdown($(this));
            }).on('mouseleave', function () {
                if (breakpoint === 0 || $(window).width() > breakpoint) closeDropdown($(this));
            });
        } else {
            $items.children('a').on('click.stDd', function (e) {
                if (breakpoint === 0 || $(window).width() > breakpoint) {
                    e.preventDefault();
                    var $item = $(this).parent();
                    $item.hasClass('st-open') ? closeDropdown($item) : openDropdown($item);
                }
            });
        }

        $(document).on('click.stClose_' + widgetId, function (e) {
            if (!$(e.target).closest('.st-nav-wrap').length) {
                $items.each(function () { closeDropdown($(this)); });
            }
        });

        // ── Mobile panel open/close (CSS transform animation) ─────────
        function openPanel() {
            isOpen = true;
            $hamburger.attr('aria-expanded', 'true');
            $backdrop.fadeIn(200);
            $panel.css('display', 'flex');
            // Trigger reflow then add class for CSS transition
            $panel[0].offsetHeight;
            $panel.addClass('st-panel-open');
            $('body').css('overflow', 'hidden');
        }

        function closePanel() {
            isOpen = false;
            $hamburger.attr('aria-expanded', 'false');
            $backdrop.fadeOut(200);
            $panel.removeClass('st-panel-open');
            // Hide after transition ends
            setTimeout(function () {
                if (!isOpen) $panel.css('display', 'none');
            }, 340);
            $('body').css('overflow', '');
        }

        // Hamburger toggle
        $hamburger.off('click.stHb').on('click.stHb', function (e) {
            e.stopPropagation();
            isOpen ? closePanel() : openPanel();
        });

        // Close on backdrop click
        $backdrop.on('click', closePanel);

        // Close button inside panel
        $panel.find('.st-mob-close-btn').on('click', closePanel);

        // Mobile sub-menu accordion
        $panel.find('.st-has-dropdown > a').on('click', function (e) {
            e.preventDefault();
            var $item = $(this).parent();
            var $sub  = $item.children('.st-sub-menu');
            if ($item.hasClass('st-open')) {
                $item.removeClass('st-open');
                $sub.slideUp(200);
            } else {
                $panel.find('.st-has-dropdown.st-open').removeClass('st-open')
                      .children('.st-sub-menu').slideUp(200);
                $item.addClass('st-open');
                $sub.slideDown(220);
            }
        });

        // ── Responsive ───────────────────────────────────────────────
        function checkBreakpoint() {
            // breakpoint 0 = None = ALWAYS desktop, NEVER hamburger
            if (breakpoint > 0 && $(window).width() <= breakpoint) {
                $wrap.addClass('st-mobile-active');
                $hamburger.css('display', 'flex');
                $desktop.css('display', 'none');
            } else {
                $wrap.removeClass('st-mobile-active');
                $hamburger.css('display', 'none');
                $desktop.css('display', 'block');
                if (isOpen) closePanel();
            }
        }

        checkBreakpoint();
        $(window).on('resize.stNav_' + widgetId, checkBreakpoint);
    }

    // ── Init ─────────────────────────────────────────────────────────
    function registerHooks() {
        if (typeof elementorFrontend === 'undefined') return;
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/st_nav_menu.default',
            initNavMenu
        );
    }

    if (typeof elementorFrontend !== 'undefined' && elementorFrontend.isInit) {
        registerHooks();
    } else {
        $(window).on('elementor/frontend/init', registerHooks);
    }

    $(document).ready(function () {
        if (typeof elementorFrontend === 'undefined') {
            $('.elementor-widget-st_nav_menu').each(function () {
                initNavMenu($(this));
            });
        }
    });

})(jQuery);
