/**
 * GP Scroll-Reveal Animation System — init script (v2)
 * --------------------------------------------------------------------------
 * v2 fixes vs v1:
 *  1. Aware of the theme's older `.reveal` / `data-reveal` system — takes
 *     it over cleanly instead of double-animating the same elements.
 *  2. Reuses that system's own `data-reveal-delay` (ms) values for stagger
 *     where present, instead of guessing.
 *  3. Cards animate as ONE unit — their inner heading/image/paragraph are
 *     no longer animated separately, so nothing looks broken/half-loaded.
 *  4. Sliders/carousels (Slick, Swiper-style markup) and modals/dialogs are
 *     skipped — they manage their own visibility.
 *  5. Hard failsafe: any armed element that hasn't revealed itself within
 *     4s is force-revealed. Nothing can stay invisible forever.
 *
 * Safe by default: elements are only hidden AFTER this script tags them
 * with .gp-armed. If this file fails to load, nothing is ever hidden.
 */
(function () {
  "use strict";

  if (
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches
  ) {
    return;
  }
  if (!("IntersectionObserver" in window)) {
    return;
  }

  var ROOTS = document.querySelectorAll("main");
  if (!ROOTS.length) return;

  // Ancestors whose contents should never be auto-animated: sliders manage
  // their own reveal, modals/dialogs are hidden until opened, .gp-no-anim
  // is a manual opt-out.
  var EXCLUDE_SELECTOR =
    '.gp-no-anim, [role="dialog"], dialog, [class*="modal"], ' +
    '[class*="swiper"], [class*="slick"], .cg-project-overview__gallery';

  function isExcluded(el) {
    return !!el.closest(EXCLUDE_SELECTOR);
  }

  // True if el sits inside something already claimed as one atomic unit
  // (a card, or a data-reveal wrapper) — its children are skipped so a
  // card/section never animates twice.
  function isInsideAtomicUnit(el) {
    return !!el.closest(
      '.cg-card, .cg-blog-card, .cg-list, [class*="-card"], [data-reveal]',
    );
  }

  var h2Index = 0;
  var imgIndex = 0;
  var toObserve = [];

  // Text reveals when it's genuinely close to view — feels natural.
  var textObserver = new IntersectionObserver(
    function (entries, obs) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          reveal(entry.target);
          obs.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.15, rootMargin: "0px 0px -8% 0px" },
  );

  // Images/cards get a big head start (400px lookahead) so the file has
  // time to download + decode and the fade is already finished (or close
  // to it) by the time you actually scroll to it — fixes the "pops in
  // late" feeling. Pairs with the browser's own loading="lazy" fetch.
  var mediaObserver = new IntersectionObserver(
    function (entries, obs) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          reveal(entry.target);
          obs.unobserve(entry.target);
        }
      });
    },
    { threshold: 0, rootMargin: "0px 0px 400px 0px" },
  );

  var MEDIA_TYPES = { "img-a": 1, "img-b": 1, "img-c": 1, card: 1 };

  function reveal(el) {
    if (el.classList.contains("gp-in")) return;
    el.classList.add("gp-in");
  }

  function arm(el, gpType, delayMs) {
    if (isExcluded(el) || el.hasAttribute("data-gp")) return;
    if (gpType === "p" && !el.textContent.trim()) return;

    el.setAttribute("data-gp", gpType);
    el.classList.add("gp-armed");

    if (delayMs) {
      el.style.animationDelay = delayMs + "ms";
    }

    var isImgType =
      gpType === "img-a" || gpType === "img-b" || gpType === "img-c";
    var observer = textObserver;

    if (isImgType) {
      // Don't just wait to scroll near it — also wait for the actual file
      // to finish downloading, so the reveal never "finishes" on a blank
      // or half-painted image. Whichever condition is slower wins.
      armWithLoadGate(el, el);
    } else if (gpType === "card") {
      // A card's hero/thumbnail image is the slowest thing inside it —
      // gate the whole card on that image too, if it has one.
      var innerImg = el.querySelector("img");
      if (innerImg) {
        armWithLoadGate(el, innerImg);
      } else {
        mediaObserver.observe(el);
        toObserve.push(el);
        armFailsafe(el, mediaObserver, 4000);
      }
    } else {
      observer.observe(el);
      toObserve.push(el);
      armFailsafe(el, observer, 4000);
    }
  }

  // Reveals `target` only once BOTH it has scrolled near view AND
  // `imgEl` has finished loading (or errored — never hang forever).
  function armWithLoadGate(target, imgEl) {
    var seen = false;
    var loaded = imgEl.complete && imgEl.naturalWidth > 0;

    function tryReveal() {
      if (seen && loaded && !target.classList.contains("gp-in")) {
        reveal(target);
        observer.unobserve(target);
      }
    }

    if (!loaded) {
      imgEl.addEventListener(
        "load",
        function () {
          loaded = true;
          tryReveal();
        },
        { once: true },
      );
      imgEl.addEventListener(
        "error",
        function () {
          loaded = true;
          tryReveal();
        },
        { once: true },
      );
    }

    var localObserver = new IntersectionObserver(
      function (entries, obs) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            seen = true;
            tryReveal();
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0, rootMargin: "0px 0px 400px 0px" },
    );
    localObserver.observe(target);
    toObserve.push(target);

    // Slightly longer failsafe for images — real downloads can be slow —
    // but still guaranteed to never stay invisible.
    window.setTimeout(function () {
      if (!target.classList.contains("gp-in")) {
        reveal(target);
        localObserver.unobserve(target);
      }
    }, 6000);
  }

  function armFailsafe(el, observer, ms) {
    window.setTimeout(function () {
      if (!el.classList.contains("gp-in")) {
        reveal(el);
        observer.unobserve(el);
      }
    }, ms);
  }

  // ------------------------------------------------------------------------
  // PASS 1 — elements the theme already marks with [data-reveal]. Take
  // these over directly instead of letting Pass 2 re-scan their children.
  // ------------------------------------------------------------------------
  document.querySelectorAll("[data-reveal]").forEach(function (el) {
    if (isExcluded(el)) return;

    var delayAttr = el.getAttribute("data-reveal-delay");
    var delayMs = delayAttr ? parseInt(delayAttr, 10) : 0;

    var tag = el.tagName.toLowerCase();
    var cls = el.className || "";
    var gpType;

    if (tag === "h1") gpType = "h1";
    else if (tag === "h2") {
      gpType = ["h2-a", "h2-b", "h2-c"][h2Index % 3];
      h2Index++;
    } else if (tag === "h3") gpType = "h3";
    else if (tag === "p") gpType = "p";
    else if (tag === "img") {
      gpType = ["img-a", "img-b", "img-c"][imgIndex % 3];
      imgIndex++;
    } else if (/card/i.test(cls)) gpType = "card";
    else gpType = "block";

    arm(el, gpType, delayMs);
  });

  // ------------------------------------------------------------------------
  // PASS 2 — everything else in <main> not already covered above and not
  // nested inside a card / data-reveal wrapper (avoids double-animating).
  // ------------------------------------------------------------------------
  ROOTS.forEach(function (root) {
    root.querySelectorAll("h1").forEach(function (el) {
      if (isInsideAtomicUnit(el)) return;
      arm(el, "h1");
    });

    root.querySelectorAll("h2").forEach(function (el) {
      if (isInsideAtomicUnit(el)) return;
      var variant = ["h2-a", "h2-b", "h2-c"][h2Index % 3];
      h2Index++;
      arm(el, variant);
    });

    root.querySelectorAll("h3").forEach(function (el) {
      if (isInsideAtomicUnit(el)) return;
      arm(el, "h3");
    });

    root.querySelectorAll("p").forEach(function (el) {
      if (isInsideAtomicUnit(el)) return;
      arm(el, "p");
    });

    // Standalone images only — images inside a card are carried by the
    // card's own single animation, not animated a second time.
    root.querySelectorAll("img").forEach(function (el) {
      if (isInsideAtomicUnit(el)) return;
      var variant = ["img-a", "img-b", "img-c"][imgIndex % 3];
      imgIndex++;
      arm(el, variant);
    });

    // Card containers — animated once, as a whole unit.
    root
      .querySelectorAll(".cg-card, .cg-blog-card, .cg-list, [class*='-card']")
      .forEach(function (el) {
        if (isExcluded(el) || el.closest("[data-reveal]")) return;
        arm(el, "card");
      });

    root
      .querySelectorAll(
        ".btn, .button, .black-button, .white-button, a.cg-card__cta",
      )
      .forEach(function (el) {
        if (isInsideAtomicUnit(el)) return;
        arm(el, "btn");
      });

    root.querySelectorAll("blockquote, .content-block").forEach(function (el) {
      if (isInsideAtomicUnit(el)) return;
      arm(el, "block");
    });
  });
})();

/**
 * GP Scroll Extras — progress bar, hero parallax, back-to-top (v1)
 * --------------------------------------------------------------------------
 * Fully separate IIFE from the reveal system above, on purpose: none of
 * this touches `.gp-armed` elements, `transform`, or the IntersectionObserver
 * logic above, so it can never break or be broken by it. Each of the three
 * features below also guards and fails independently — if one throws, the
 * other two still run.
 */
(function () {
  ("use strict");

  var reduceMotion =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // --------------------------------------------------------------------
  // 1. SCROLL PROGRESS BAR — thin bar at the top that fills as you read
  //    down the page. Tied 1:1 to the user's own scroll input (not an
  //    autoplaying animation), so it runs even under reduced-motion.
  // --------------------------------------------------------------------
  // function initScrollProgress() {
  //   var bar = document.createElement("div");
  //   bar.className = "gp-progress";
  //   bar.setAttribute("aria-hidden", "true");
  //   document.body.appendChild(bar);

  //   var ticking = false;

  //   function update() {
  //     ticking = false;
  //     var doc = document.documentElement;
  //     var scrollTop = window.pageYOffset || doc.scrollTop;
  //     var trackHeight = doc.scrollHeight - doc.clientHeight;
  //     var pct = trackHeight > 0 ? scrollTop / trackHeight : 0;
  //     if (pct < 0) pct = 0;
  //     if (pct > 1) pct = 1;
  //     bar.style.transform = "scaleX(" + pct.toFixed(4) + ")";
  //   }

  //   function onScroll() {
  //     if (!ticking) {
  //       window.requestAnimationFrame(update);
  //       ticking = true;
  //     }
  //   }

  //   window.addEventListener("scroll", onScroll, { passive: true });
  //   window.addEventListener("resize", onScroll, { passive: true });
  //   update();
  // }

  // --------------------------------------------------------------------
  // 2. HERO PARALLAX — the big banner photos drift slightly slower than
  //    the page as you scroll past them, for depth. Only ever touches the
  //    `background-position-y` LONGHAND property via inline style — never
  //    `transform` — so it is structurally impossible for this to fight
  //    the entrance-animation system above, regardless of whether a hero's
  //    background-image was set inline (PHP) or via a CSS class.
  //    Desktop only, and skipped entirely under reduced-motion.
  // --------------------------------------------------------------------
  function initHeroParallax() {
    if (reduceMotion) return;
    if (window.innerWidth < 769) return;

    // Only sections confirmed to actually render a background-image.
    var heroes = document.querySelectorAll(
      ".about-hero, .contact-hero, .property-banner",
    );
    if (!heroes.length) return;

    var ticking = false;

    function update() {
      ticking = false;
      var vh = window.innerHeight;
      heroes.forEach(function (el) {
        var rect = el.getBoundingClientRect();
        if (rect.bottom < -200 || rect.top > vh + 200) return; // nowhere near view — skip
        var drift = (rect.top + rect.height / 2 - vh / 2) * 0.1;
        if (drift > 40) drift = 40;
        if (drift < -40) drift = -40;
        el.style.backgroundPositionY = "calc(50% + " + drift.toFixed(1) + "px)";
      });
    }

    function onScroll() {
      if (!ticking) {
        window.requestAnimationFrame(update);
        ticking = true;
      }
    }

    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll, { passive: true });
    update();
  }

  // --------------------------------------------------------------------
  // 3. BACK-TO-TOP BUTTON — fades in after ~80% of a viewport's worth of
  //    scrolling, smooth-scrolls to top on click (instant jump instead
  //    under reduced-motion).
  // --------------------------------------------------------------------
  function initBackToTop() {
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "gp-to-top";
    btn.setAttribute("aria-label", "Back to top");
    btn.innerHTML =
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
      'stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5">' +
      '</line><polyline points="5 12 12 5 19 12"></polyline></svg>';
    document.body.appendChild(btn);

    var ticking = false;

    function update() {
      ticking = false;
      if (window.pageYOffset > window.innerHeight * 0.8) {
        btn.classList.add("gp-visible");
      } else {
        btn.classList.remove("gp-visible");
      }
    }

    function onScroll() {
      if (!ticking) {
        window.requestAnimationFrame(update);
        ticking = true;
      }
    }

    window.addEventListener("scroll", onScroll, { passive: true });
    update();

    btn.addEventListener("click", function () {
      window.scrollTo({ top: 0, behavior: reduceMotion ? "auto" : "smooth" });
    });
  }

  function safeInit(fn) {
    try {
      fn();
    } catch (e) {
      /* one feature failing must never block the others */
    }
  }

  // safeInit(initScrollProgress);
  safeInit(initHeroParallax);
  safeInit(initBackToTop);
})();
