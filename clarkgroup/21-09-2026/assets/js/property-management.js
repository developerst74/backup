/* global window, document, IntersectionObserver, requestAnimationFrame, performance */
(function () {
  'use strict';

  var COUNT_ATTR = 'data-count-target';
  var PREFIX_ATTR = 'data-count-prefix'; // NEW — optional, e.g. "$" for "$200M". Absent = '' (no behavior change for existing counters).
  var SUFFIX_ATTR = 'data-count-suffix';
  var DURATION_ATTR = 'data-count-duration';
  var DONE_ATTR = 'data-count-done';
  var DEFAULT_DURATION = 1800;

  /**
   * Ease-out cubic — 0..1 -> 0..1
   */
  function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
  }

  /**
   * Format a raw integer with commas as thousands separators (en-US style,
   * matches the PHP output convention e.g. "2,000,000").
   */
  function formatNumber(n) {
    try {
      return new Intl.NumberFormat('en-US').apply(null, [n]);
    } catch (e) {
      return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
  }

  /**
   * Animate a single counter element from 0 to its target integer.
   * Uses requestAnimationFrame so it syncs with the refresh (60fps smooth)
   * and respects the user's `prefers-reduced-motion` by jumping straight to end.
   */
  function animateCounter(el) {
    if (el.getAttribute(DONE_ATTR) === '1') return;

    var targetRaw = el.getAttribute(COUNT_ATTR);
    if (!targetRaw) return;
    var target = parseInt(targetRaw, 10);
    if (isNaN(target) || target < 0) return;

    var prefix = el.getAttribute(PREFIX_ATTR) || '';
    var suffix = el.getAttribute(SUFFIX_ATTR) || '';
    var durationRaw = parseInt(el.getAttribute(DURATION_ATTR), 10);
    var duration = isNaN(durationRaw) || durationRaw <= 0
      ? DEFAULT_DURATION
      : durationRaw;

    var reduceMotion = window.matchMedia
      && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion) {
      el.textContent = prefix + formatNumber(target) + suffix;
      el.setAttribute(DONE_ATTR, '1');
      return;
    }

    var startTs = null;
    var tick = function (nowTs) {
      if (startTs === null) startTs = nowTs;
      var elapsed = nowTs - startTs;
      var t = Math.min(1, elapsed / duration);
      var eased = easeOutCubic(t);
      var current = Math.round(target * eased);
      el.textContent = prefix + formatNumber(current) + suffix;
      if (t < 1) {
        requestAnimationFrame(tick);
      } else {
        el.textContent = prefix + formatNumber(target) + suffix;
        el.setAttribute(DONE_ATTR, '1');
      }
    };

    requestAnimationFrame(tick);
  }

  function init() {
    if (typeof IntersectionObserver === 'undefined') {
      var nodes = document.querySelectorAll('[' + COUNT_ATTR + ']');
      for (var i = 0; i < nodes.length; i++) animateCounter(nodes[i]);
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      for (var i = 0; i < entries.length; i++) {
        var entry = entries[i];
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      }
    }, {
      root: null,
      threshold: 0.4,
      rootMargin: '0px 0px -10% 0px'
    });

    var nodes = document.querySelectorAll('[' + COUNT_ATTR + ']');
    for (var j = 0; j < nodes.length; j++) observer.observe(nodes[j]);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
