(function ($) {
    'use strict';

    var cfg  = window.stPopupEditor || {};
    var s    = cfg.settings || {};
    var i18n = cfg.i18n || {};

    // ── State ────────────────────────────────────────────────────────
    var state = {
        conditions: s.condition ? [{ type: s.condition, page_id: s.page_id || '0' }] : [],
        triggers: {
            page_load: { enabled: s.trigger === 'page_load', delay: s.delay || '0' },
            timer:     { enabled: s.trigger === 'timer',     delay: s.delay || '3000' },
            scroll:    { enabled: s.trigger === 'scroll',    pct:   s.scroll || '50' },
            exit:      { enabled: s.trigger === 'exit' },
            click:     { enabled: s.trigger === 'click',     selector: s.click_selector || '' },
        },
        rules: {
            frequency:  s.frequency  || 'always',
            freqLimit:  s.freq_limit || '1',
            freqHours:  s.freq_hours || '24',
            logged_in:  false,
            logged_out: false,
        },
        status: s.status || 'active',
    };

    var pageOpts = cfg.pageOpts || [];

    // ── Build page options HTML ──────────────────────────────────────
    function buildPageOpts(selected) {
        return pageOpts.map(function (o) {
            return '<option value="' + o.value + '"' + (o.value === selected ? ' selected' : '') + '>' + o.label + '</option>';
        }).join('');
    }

    // ── Build condition type options ─────────────────────────────────
    var condTypes = [
        { value: 'entire_site', label: 'Entire Site' },
        { value: 'front_page',  label: 'Front Page' },
        { value: 'all_pages',   label: 'All Pages' },
        { value: 'single_page', label: 'Specific Page' },
        { value: 'single',      label: 'All Singles' },
        { value: 'archive',     label: 'All Archives' },
        { value: '404',         label: '404 Page' },
    ];

    function buildCondTypeOpts(selected) {
        return condTypes.map(function (o) {
            return '<option value="' + o.value + '"' + (o.value === selected ? ' selected' : '') + '>' + o.label + '</option>';
        }).join('');
    }

    // ── Render conditions panel ──────────────────────────────────────
    function renderConditions() {
        var html = '<p class="st-pm-section-title">Where Do You Want to Display Your Popup?</p>';

        if (!state.conditions.length) {
            html += '<div class="st-pm-empty">';
            html += '<div class="st-pm-empty-icon">&#9992;</div>';
            html += '<h3>Where Do You Want to Display Your Popup?</h3>';
            html += '<p>Set the conditions that determine where your Popup is used throughout your site.</p>';
            html += '<button class="st-pm-add-btn" id="st-pm-add-cond-empty">ADD CONDITION</button>';
            html += '</div>';
        } else {
            state.conditions.forEach(function (cond, idx) {
                html += '<div class="st-pm-cond-row" data-idx="' + idx + '">';
                html += '<select class="st-pm-cond-type">' + buildCondTypeOpts(cond.type) + '</select>';
                if (cond.type === 'single_page') {
                    html += '<select class="st-pm-cond-page">' + buildPageOpts(cond.page_id) + '</select>';
                }
                html += '<button class="st-pm-remove" data-idx="' + idx + '">&#x2715;</button>';
                html += '</div>';
            });
            html += '<span class="st-pm-add-link" id="st-pm-add-cond">+ ADD CONDITION</span>';
        }

        $('#st-pm-panel-conditions').html(html);
    }

    // ── Render triggers panel ────────────────────────────────────────
    var triggerDefs = [
        { key: 'page_load', icon: '&#9654;', title: 'On Page Load',    desc: 'Show popup when the page loads.' },
        { key: 'timer',     icon: '&#9201;', title: 'After Delay',     desc: 'Show popup after a set time.' },
        { key: 'scroll',    icon: '&#8595;', title: 'On Scroll',       desc: 'Show popup when user scrolls.' },
        { key: 'exit',      icon: '&#8599;', title: 'On Exit Intent',  desc: 'Show popup when user is about to leave.' },
        { key: 'click',     icon: '&#128432;', title: 'On Click',      desc: 'Show popup when an element is clicked.' },
    ];

    function renderTriggers() {
        var html = '<p class="st-pm-section-title">What Action Opens the Popup?</p>';

        triggerDefs.forEach(function (t) {
            var enabled = state.triggers[t.key] && state.triggers[t.key].enabled;
            html += '<div class="st-pm-trigger-row' + (enabled ? ' active' : '') + '" data-trigger="' + t.key + '">';
            html += '<div class="st-pm-trigger-info"><h4>' + t.icon + ' ' + t.title + '</h4><p>' + t.desc + '</p></div>';
            html += '<div class="st-pm-trigger-toggle' + (enabled ? ' on' : '') + '" data-trigger="' + t.key + '"></div>';
            html += '</div>';

            // Sub-settings
            html += '<div class="st-pm-trigger-settings' + (enabled ? ' show' : '') + '" id="st-trigger-settings-' + t.key + '">';
            if (t.key === 'page_load' || t.key === 'timer') {
                html += '<label>Delay (milliseconds)</label>';
                html += '<input type="number" class="st-trigger-delay" data-trigger="' + t.key + '" value="' + (state.triggers[t.key].delay || '0') + '" min="0" placeholder="0">';
            }
            if (t.key === 'scroll') {
                html += '<label>Scroll Percentage: <strong id="st-scroll-pct-label">' + (state.triggers.scroll.pct || '50') + '%</strong></label>';
                html += '<input type="range" class="st-trigger-scroll-pct" min="0" max="100" value="' + (state.triggers.scroll.pct || '50') + '">';
            }
            if (t.key === 'click') {
                html += '<label>CSS Selector (e.g. .my-button, #open-popup)</label>';
                html += '<input type="text" class="st-trigger-selector" value="' + (state.triggers.click.selector || '') + '" placeholder=".open-popup">';
            }
            html += '</div>';
        });

        $('#st-pm-panel-triggers').html(html);
    }

    // ── Render advanced rules ────────────────────────────────────────
    function renderRules() {
        var html = '<p class="st-pm-section-title">Advanced Display Rules</p>';

        html += '<div style="display:flex;flex-direction:column;gap:16px">';

        // ── Frequency control ────────────────────────────────────────
        html += '<div class="st-pm-freq-box">';
        html += '<label style="display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:8px">Show Frequency</label>';
        html += '<p style="font-size:12px;color:#6b7280;margin:0 0 10px">Control how often this popup appears to the same visitor.</p>';

        var freqOpts = [
            { value: 'always',   label: 'Always (every page load)' },
            { value: 'session',  label: 'Once per Session' },
            { value: 'once_24h', label: 'Once per 24 Hours' },
            { value: 'x_times',  label: 'Limit to X times total' },
            { value: 'hours',    label: 'Once every X hours' },
        ];
        html += '<select id="st-rule-frequency" style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;margin-bottom:10px">';
        freqOpts.forEach(function (o) {
            html += '<option value="' + o.value + '"' + (state.rules.frequency === o.value ? ' selected' : '') + '>' + o.label + '</option>';
        });
        html += '</select>';

        // x_times sub-field
        html += '<div id="st-freq-xtimes-wrap" style="' + (state.rules.frequency === 'x_times' ? '' : 'display:none') + '">';
        html += '<label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Max times to show</label>';
        html += '<input type="number" id="st-rule-freq-limit" value="' + (state.rules.freqLimit || '1') + '" min="1" style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px">';
        html += '</div>';

        // hours sub-field
        html += '<div id="st-freq-hours-wrap" style="' + (state.rules.frequency === 'hours' ? '' : 'display:none') + '">';
        html += '<label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px">Show again after (hours)</label>';
        html += '<input type="number" id="st-rule-freq-hours" value="' + (state.rules.freqHours || '24') + '" min="1" style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px">';
        html += '</div>';

        html += '</div>'; // .st-pm-freq-box

        html += '<label style="display:flex;align-items:center;gap:10px;font-size:14px;cursor:pointer">';
        html += '<input type="checkbox" id="st-rule-logged-in"' + (state.rules.logged_in ? ' checked' : '') + ' style="width:16px;height:16px;accent-color:#6366f1">';
        html += '<span><strong>Logged In Users Only</strong><br><small style="color:#6b7280">Only show to users who are logged in.</small></span>';
        html += '</label>';

        html += '<label style="display:flex;align-items:center;gap:10px;font-size:14px;cursor:pointer">';
        html += '<input type="checkbox" id="st-rule-logged-out"' + (state.rules.logged_out ? ' checked' : '') + ' style="width:16px;height:16px;accent-color:#6366f1">';
        html += '<span><strong>Logged Out Users Only</strong><br><small style="color:#6b7280">Only show to users who are not logged in.</small></span>';
        html += '</label>';

        html += '</div>';

        $('#st-pm-panel-rules').html(html);
    }

    // ── Build full modal ─────────────────────────────────────────────
    function buildModal() {
        return [
            '<div id="st-publish-modal-overlay">',
            '<div id="st-publish-modal">',

            // Head
            '<div class="st-pm-modal-head">',
            '<div class="st-pm-logo"><span>&#9632;</span> PUBLISH SETTINGS</div>',
            '<button class="st-pm-modal-close" id="st-pm-close">&#x2715;</button>',
            '</div>',

            // Body
            '<div class="st-pm-modal-body">',

            // Sidebar
            '<div class="st-pm-sidebar">',
            '<div class="st-pm-tab active" data-tab="conditions">',
            '<div class="st-pm-tab-icon">&#9783;</div>',
            '<div class="st-pm-tab-text"><h4>Conditions</h4><p>Apply current template to these pages.</p></div>',
            '</div>',
            '<div class="st-pm-tab" data-tab="triggers">',
            '<div class="st-pm-tab-icon">&#128432;</div>',
            '<div class="st-pm-tab-text"><h4>Triggers</h4><p>What action the user needs to do for the popup to open.</p></div>',
            '</div>',
            '<div class="st-pm-tab" data-tab="rules">',
            '<div class="st-pm-tab-icon">&#9881;</div>',
            '<div class="st-pm-tab-text"><h4>Advanced Rules</h4><p>Requirements that have to be met for the popup to open.</p></div>',
            '</div>',
            '</div>',

            // Content
            '<div class="st-pm-content">',
            '<div class="st-pm-panel active" id="st-pm-panel-conditions"></div>',
            '<div class="st-pm-panel" id="st-pm-panel-triggers"></div>',
            '<div class="st-pm-panel" id="st-pm-panel-rules"></div>',
            '</div>',

            '</div>',

            // Footer
            '<div class="st-pm-modal-foot">',
            '<button class="st-pm-foot-save-close" id="st-pm-save-close">SAVE &amp; CLOSE</button>',
            '<button class="st-pm-foot-next" id="st-pm-next">NEXT</button>',
            '</div>',

            '</div>',
            '</div>',
        ].join('');
    }

    // ── Save to server ───────────────────────────────────────────────
    function saveSettings(callback) {
        // Determine primary trigger
        var primaryTrigger = 'page_load';
        var primaryDelay   = '0';
        var primaryScroll  = '50';
        var primarySelector = '';

        $.each(state.triggers, function (key, t) {
            if (t.enabled) {
                primaryTrigger  = key;
                primaryDelay    = t.delay || '0';
                primaryScroll   = t.pct   || '50';
                primarySelector = t.selector || '';
            }
        });

        var primaryCond   = state.conditions.length ? state.conditions[0].type    : 'entire_site';
        var primaryPageId = state.conditions.length ? state.conditions[0].page_id : '0';

        $.post(cfg.ajaxUrl, {
            action:          'st_save_popup_settings',
            nonce:           cfg.nonce,
            post_id:         cfg.postId,
            trigger:         primaryTrigger,
            delay:           primaryDelay,
            scroll:          primaryScroll,
            click_selector:  primarySelector,
            condition:       primaryCond,
            page_id:         primaryPageId,
            once:            state.rules.frequency === 'session' ? '1' : '0',
            frequency:       state.rules.frequency,
            freq_limit:      state.rules.freqLimit,
            freq_hours:      state.rules.freqHours,
            status:          state.status,
        }, function (res) {
            if (callback) callback(res);
        });
    }

    // ── Tab navigation ───────────────────────────────────────────────
    var tabs = ['conditions', 'triggers', 'rules'];
    var currentTab = 0;

    function switchTab(idx) {
        currentTab = idx;
        var tabName = tabs[idx];
        $('.st-pm-tab').removeClass('active');
        $('.st-pm-tab[data-tab="' + tabName + '"]').addClass('active');
        $('.st-pm-panel').removeClass('active');
        $('#st-pm-panel-' + tabName).addClass('active');
        $('#st-pm-next').text(idx < tabs.length - 1 ? 'NEXT' : 'SAVE & PUBLISH');
    }

    // ── Init ─────────────────────────────────────────────────────────
    function init() {
        if (!cfg.postId) return;

        // Inject "Conditions" button INSIDE the Publish dropdown menu
        // The dropdown opens when user clicks the ▼ arrow next to Publish
        injectConditionsMenuItem();

        // Open modal
        $(document).on('click', '#st-popup-conditions-btn', function (e) {
            e.preventDefault();
            e.stopPropagation();
            // Close the dropdown first
            $(document).trigger('click');
            setTimeout(function () {
                if ($('#st-publish-modal-overlay').length) return;
                $('body').append(buildModal());
                renderConditions();
                renderTriggers();
                renderRules();
                switchTab(0);
            }, 100);
        });

        // Close modal
        $(document).on('click', '#st-pm-close, #st-pm-save-close', function () {
            saveSettings(function () {
                $('#st-publish-modal-overlay').remove();
            });
        });

        // Click outside modal box (NOT overlay — overlay should not block editor)
        $(document).on('click', '#st-publish-modal-overlay', function (e) {
            if ($(e.target).is('#st-publish-modal-overlay')) {
                $('#st-publish-modal-overlay').remove();
            }
        });

        // Tab click
        $(document).on('click', '.st-pm-tab', function () {
            var tab = $(this).data('tab');
            switchTab(tabs.indexOf(tab));
        });

        // Next button
        $(document).on('click', '#st-pm-next', function () {
            if (currentTab < tabs.length - 1) {
                switchTab(currentTab + 1);
            } else {
                var $btn = $(this);
                $btn.text('Saving...').prop('disabled', true);
                saveSettings(function () {
                    $btn.prop('disabled', false);
                    $('#st-publish-modal-overlay').remove();
                });
            }
        });

        // ── Conditions events ────────────────────────────────────────
        $(document).on('click', '#st-pm-add-cond, #st-pm-add-cond-empty', function () {
            state.conditions.push({ type: 'entire_site', page_id: '0' });
            renderConditions();
        });

        $(document).on('click', '.st-pm-cond-row .st-pm-remove', function () {
            var idx = parseInt($(this).data('idx'), 10);
            state.conditions.splice(idx, 1);
            renderConditions();
        });

        $(document).on('change', '.st-pm-cond-type', function () {
            var idx = parseInt($(this).closest('.st-pm-cond-row').data('idx'), 10);
            state.conditions[idx].type = $(this).val();
            renderConditions();
        });

        $(document).on('change', '.st-pm-cond-page', function () {
            var idx = parseInt($(this).closest('.st-pm-cond-row').data('idx'), 10);
            state.conditions[idx].page_id = $(this).val();
        });

        // ── Trigger events ───────────────────────────────────────────
        $(document).on('click', '.st-pm-trigger-toggle', function () {
            var key = $(this).data('trigger');
            state.triggers[key].enabled = !state.triggers[key].enabled;
            renderTriggers();
        });

        $(document).on('input', '.st-trigger-delay', function () {
            var key = $(this).data('trigger');
            state.triggers[key].delay = $(this).val();
        });

        $(document).on('input', '.st-trigger-scroll-pct', function () {
            state.triggers.scroll.pct = $(this).val();
            $('#st-scroll-pct-label').text($(this).val() + '%');
        });

        $(document).on('input', '.st-trigger-selector', function () {
            state.triggers.click.selector = $(this).val();
        });

        // ── Rules events ─────────────────────────────────────────────
        $(document).on('change', '#st-rule-frequency', function () {
            state.rules.frequency = $(this).val();
            $('#st-freq-xtimes-wrap').toggle(state.rules.frequency === 'x_times');
            $('#st-freq-hours-wrap').toggle(state.rules.frequency === 'hours');
        });
        $(document).on('input', '#st-rule-freq-limit', function () { state.rules.freqLimit = $(this).val(); });
        $(document).on('input', '#st-rule-freq-hours', function () { state.rules.freqHours = $(this).val(); });
        $(document).on('change', '#st-rule-logged-in',  function () { state.rules.logged_in  = this.checked; });
        $(document).on('change', '#st-rule-logged-out', function () { state.rules.logged_out = this.checked; });
    }

    function injectConditionsMenuItem() {
        if ($('#st-popup-conditions-btn').length) return;

        // Build the menu item — styled like Elementor's dropdown items
        var $item = $(
            '<div id="st-popup-conditions-btn" class="elementor-panel-footer-sub-menu-item">' +
            '<i class="eicon-filter" style="margin-right:8px"></i>' +
            '<span class="elementor-title">Popup Conditions</span>' +
            '</div>'
        );

        // Try multiple selectors to find the dropdown menu
        var $menu = $('.elementor-panel-footer-sub-menu');
        if (!$menu.length) $menu = $('#elementor-panel-saver-button-publish').closest('.elementor-panel-footer-sub-menu-item').find('.elementor-panel-footer-sub-menu');

        if ($menu.length) {
            $menu.prepend($item);
        } else {
            // Fallback: watch for the dropdown to appear
            var observer = new MutationObserver(function () {
                var $m = $('.elementor-panel-footer-sub-menu');
                if ($m.length && !$('#st-popup-conditions-btn').length) {
                    $m.prepend($item);
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
    }

    // Wait for Elementor editor
    $(window).on('elementor:init', init);
    $(document).ready(function () { setTimeout(init, 1500); });

})(jQuery);
