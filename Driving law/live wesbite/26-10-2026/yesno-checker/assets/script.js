(function () {
    'use strict';

    function initYesNoChecker(card) {
        if (!card) return;
        var dataEl = card.querySelector('.ync-data');
        if (!dataEl) return;
        var config = JSON.parse(dataEl.textContent);
        config.layout === 'tabbed' ? initTabbed(card, config) : initNormal(card, config);
    }

    /* ── NORMAL ─────────────────────────────────── */
    function initNormal(card, config) {
        var rows     = card.querySelectorAll('.ync-question-row');
        var resultEl = card.querySelector('.ync-result');
        var textEl   = card.querySelector('.ync-result-text');
        var total    = config.total;
        var answers  = new Array(total).fill(null);

        rows.forEach(function (row, idx) {
            bindRow(row, idx, answers, function () {
                evaluateRules(answers, total, config.rules, resultEl, textEl);
            });
        });
    }

    /* ── TABBED ─────────────────────────────────── */
    function initTabbed(card, config) {
        var tabBtns       = card.querySelectorAll('.ync-tab-btn');
        var panels        = card.querySelectorAll('.ync-tab-panel');
        var panelsWrapper = card.querySelector('.ync-tab-panels');
        var preselectEl   = card.querySelector('.ync-preselect-text');

        if (panelsWrapper) panelsWrapper.style.display = 'none';

        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = parseInt(btn.getAttribute('data-tab'), 10);
                if (btn.classList.contains('ync-tab-active')) return;

                if (preselectEl) preselectEl.style.display = 'none';
                if (panelsWrapper) panelsWrapper.style.display = '';

                tabBtns.forEach(function (b) { b.classList.remove('ync-tab-active'); b.setAttribute('aria-selected', 'false'); });
                panels.forEach(function (p)  { p.style.display = 'none'; p.setAttribute('aria-hidden', 'true'); });

                btn.classList.add('ync-tab-active');
                btn.setAttribute('aria-selected', 'true');

                var panel = card.querySelector('#ync-tab-panel-' + target);
                if (panel) {
                    panel.style.display = 'block';
                    panel.setAttribute('aria-hidden', 'false');
                    if (!panel.dataset.yncReady) {
                        panel.dataset.yncReady = '1';
                        initTabPanel(panel, config.tabs[target]);
                    }
                }
            });
        });
    }

    function initTabPanel(panel, tabConfig) {
        var rows     = panel.querySelectorAll('.ync-question-row');
        var resultEl = panel.querySelector('.ync-result');
        var textEl   = panel.querySelector('.ync-result-text');
        var total    = tabConfig ? tabConfig.total : rows.length;
        var answers  = new Array(rows.length).fill(null);

        rows.forEach(function (row, idx) {
            bindRow(row, idx, answers, function () {
                evaluateRules(answers, total, tabConfig ? tabConfig.rules : [], resultEl, textEl);
            });
        });
    }

    /* ── Bind Yes/No buttons ─────────────────────── */
    function bindRow(row, idx, answers, onChange) {
        var btns = row.querySelectorAll('.ync-btn');
        btns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var val = btn.getAttribute('data-value');
                if (answers[idx] === val) {
                    answers[idx] = null;
                    btns.forEach(function (b) { b.classList.remove('active-yes', 'active-no'); b.setAttribute('aria-pressed', 'false'); });
                } else {
                    answers[idx] = val;
                    btns.forEach(function (b) { b.classList.remove('active-yes', 'active-no'); b.setAttribute('aria-pressed', 'false'); });
                    btn.classList.add(val === 'yes' ? 'active-yes' : 'active-no');
                    btn.setAttribute('aria-pressed', 'true');
                }
                onChange();
            });
        });
    }

    /* ── Condition engine ───────────────────────── */
    function evaluateRules(answers, total, rules, resultEl, textEl) {
        if (!resultEl || !textEl) return;

        var answered = answers.filter(function (a) { return a !== null; });

        // Hide result until ALL questions are answered
        if (answered.length < total) { resultEl.style.display = 'none'; return; }

        var yesCount = answered.filter(function (a) { return a === 'yes'; }).length;
        var noCount  = answered.filter(function (a) { return a === 'no';  }).length;

        // Check each rule top-to-bottom, first match wins
        for (var i = 0; i < rules.length; i++) {
            var rule       = rules[i];
            var conditions = rule.conditions || [];
            if (conditions.length === 0) continue;

            // ALL conditions must pass (AND logic)
            var allPass = true;
            for (var j = 0; j < conditions.length; j++) {
                if (!checkCondition(conditions[j], answers, yesCount, noCount, total)) {
                    allPass = false;
                    break;
                }
            }

            if (allPass) {
                var cls = resolveStyle(rule.style, yesCount, noCount, total);
                showResult(resultEl, textEl, cls, rule.message);
                return;
            }
        }

        resultEl.style.display = 'none';
    }

    /* ── Single condition check ──────────────────── */
    function checkCondition(cond, answers, yesCount, noCount, total) {
        var subj = cond.subject;

        if (subj === 'yes' || subj === 'no') {
            var lhs = subj === 'yes' ? yesCount : noCount;
            var rhs = cond.value === 'all' ? total : parseInt(cond.value, 10);
            if (isNaN(rhs)) return false;
            return compare(lhs, cond.operator, rhs);
        }

        if (subj.indexOf('question_') === 0) {
            var qIndex  = parseInt(subj.replace('question_', ''), 10) - 1;
            if (qIndex < 0 || qIndex >= answers.length) return false;
            var qAnswer = answers[qIndex];
            if (cond.operator === 'is_yes') return qAnswer === 'yes';
            if (cond.operator === 'is_no')  return qAnswer === 'no';
            if (cond.operator === 'is_eq')  return qAnswer === cond.q_value;
        }

        return false;
    }

    function compare(lhs, op, rhs) {
        switch (op) {
            case '==': return lhs === rhs;
            case '!=': return lhs !== rhs;
            case '>':  return lhs >   rhs;
            case '>=': return lhs >=  rhs;
            case '<':  return lhs <   rhs;
            case '<=': return lhs <=  rhs;
        }
        return false;
    }

    function resolveStyle(style, yesCount, noCount, total) {
        if (style !== 'auto') {
            var map = { success: 'result-all-yes', warning: 'result-mixed', danger: 'result-all-no', neutral: 'result-neutral' };
            return map[style] || 'result-neutral';
        }
        if (yesCount === total) return 'result-all-yes';
        if (noCount  === total) return 'result-all-no';
        return 'result-mixed';
    }

    function showResult(resultEl, textEl, cls, message) {
        resultEl.className     = 'ync-result ' + cls;
        textEl.textContent     = message;
        resultEl.style.display = 'block';
    }

    /* ── Boot ────────────────────────────────────── */
    function initAll() {
        document.querySelectorAll('.ync-card').forEach(function (card) { initYesNoChecker(card); });
    }

    document.addEventListener('DOMContentLoaded', initAll);

    document.addEventListener('DOMContentLoaded', function () {
        if (window.elementorFrontend) {
            window.elementorFrontend.hooks.addAction('frontend/element_ready/yesno_checker.default', function ($scope) {
                var card = $scope[0] && $scope[0].querySelector('.ync-card');
                if (card) initYesNoChecker(card);
            });
        }
    });
})();
