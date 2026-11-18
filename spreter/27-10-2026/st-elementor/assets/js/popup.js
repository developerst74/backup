(function ($) {
    'use strict';

    // Never run in Elementor editor or preview
    if (typeof elementorFrontend !== 'undefined' && elementorFrontend.isEditMode && elementorFrontend.isEditMode()) return;
    if (window.location.search.indexOf('elementor-preview') !== -1) return;
    if (window.location.search.indexOf('elementor_library') !== -1) return;

    if (typeof stPopups === 'undefined' || !stPopups.length) return;

    var STORAGE_KEY = 'st_popup_data';

    // Load stored data: { id: { count, lastShown } }
    function loadData() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}'); } catch(e) { return {}; }
    }
    function saveData(data) {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(data)); } catch(e) {}
    }

    // Check if popup should show based on frequency
    function shouldShow(popup) {
        var freq      = popup.frequency  || 'always';
        var freqLimit = popup.freqLimit  || 1;
        var freqHours = popup.freqHours  || 24;
        var id        = popup.id;
        var data      = loadData();
        var entry     = data[id] || { count: 0, lastShown: 0, closed: false };
        var now       = Date.now();

        if (freq === 'always')  return true;
        if (freq === 'session') {
            return !sessionStorage.getItem('st_popup_session_' + id);
        }
        if (freq === 'once_24h') {
            // If user closed it, don't show again for 24h
            if (entry.closed && (now - entry.lastShown) < 86400000) return false;
            // If not closed but already shown in last 24h, don't show again
            if (entry.lastShown && (now - entry.lastShown) < 86400000) return false;
            return true;
        }
        if (freq === 'x_times') return entry.count < freqLimit;
        if (freq === 'hours') {
            return (now - entry.lastShown) >= (freqHours * 3600000);
        }
        return true;
    }

    // Record that popup was shown
    function recordShown(popup) {
        var freq = popup.frequency || 'always';
        var id   = popup.id;
        if (freq === 'always') return;
        if (freq === 'session') {
            sessionStorage.setItem('st_popup_session_' + id, '1');
            return;
        }
        var data  = loadData();
        var entry = data[id] || { count: 0, lastShown: 0, closed: false };
        entry.count++;
        entry.lastShown = Date.now();
        entry.closed = false;
        data[id] = entry;
        saveData(data);
    }

    // Record that user manually closed the popup
    function recordClosed(id) {
        var data  = loadData();
        var entry = data[id] || { count: 0, lastShown: 0, closed: false };
        entry.closed    = true;
        entry.lastShown = Date.now();
        data[id] = entry;
        saveData(data);
    }

    function openPopup(id, animation) {
        var $overlay = $('#st-popup-' + id);
        if (!$overlay.length) return;
        $overlay.addClass('st-popup-anim-' + animation);
        $overlay.fadeIn(250);
        $('body').css('overflow', 'hidden');
    }

    function closePopup($overlay) {
        $overlay.fadeOut(200, function () {
            $(this).removeClass(function (i, cls) {
                return (cls.match(/st-popup-anim-\S+/g) || []).join(' ');
            });
        });
        $('body').css('overflow', '');
    }

    $(document).on('click', '.st-popup-close', function () {
        var $overlay = $(this).closest('.st-popup-overlay');
        var id = $overlay.data('popup-id');
        if (id) recordClosed(id);
        closePopup($overlay);
    });

    $(document).on('click', '.st-popup-overlay', function (e) {
        var $overlay = $(this);
        if ($overlay.data('close-overlay') === '1' && $(e.target).is($overlay)) {
            var id = $overlay.data('popup-id');
            if (id) recordClosed(id);
            closePopup($overlay);
        }
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            $('.st-popup-overlay:visible').each(function () { closePopup($(this)); });
        }
    });

    // Global: open popup by ID via data-st-popup attribute (Popup Button widget)
    $(document).on('click', '[data-st-popup]', function (e) {
        e.preventDefault();
        var id = $(this).data('st-popup');
        var $overlay = $('#st-popup-' + id);
        if ($overlay.length) {
            var animation = $overlay.data('animation') || 'fadeIn';
            openPopup(id, animation);
        }
    });

    $.each(stPopups, function (i, popup) {
        var id        = popup.id;
        var trigger   = popup.trigger;
        var delay     = parseInt(popup.delay, 10) || 0;
        var scroll    = parseInt(popup.scroll, 10) || 50;
        var selector  = popup.clickSelector || '';
        var animation = popup.animation || 'fadeIn';

        if (!shouldShow(popup)) return;

        function show() {
            if (!shouldShow(popup)) return; // double-check
            openPopup(id, animation);
            recordShown(popup);
        }

        if (trigger === 'page_load') {
            setTimeout(show, Math.max(delay, 300));

        } else if (trigger === 'timer') {
            setTimeout(show, Math.max(delay, 1000));

        } else if (trigger === 'scroll') {
            $(window).on('scroll.stPopup_' + id, function () {
                var docH = $(document).height() - $(window).height();
                if (docH <= 0) return;
                var pct = ($(window).scrollTop() / docH) * 100;
                if (pct >= scroll) {
                    $(window).off('scroll.stPopup_' + id);
                    show();
                }
            });

        } else if (trigger === 'exit') {
            $(document).on('mouseleave.stPopup_' + id, function (e) {
                if (e.clientY <= 0) {
                    $(document).off('mouseleave.stPopup_' + id);
                    show();
                }
            });

        } else if (trigger === 'click' && selector) {
            $(document).on('click.stPopup_' + id, selector, function (e) {
                e.preventDefault();
                show();
            });
        }
    });

})(jQuery);
