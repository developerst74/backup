/* =========================================================
   Clark Group — Combined Frontend JS
   ---------------------------------------------------------
   Includes:
   1. Reveal animations
   2. Counter animations
   3. Accordion
   4. Partners carousel
   5. Project filters
   6. Team enquiry modal
   7. Blog category filters
   8. Blog image carousel
   9. Blog card tag toggle
   ---------------------------------------------------------
   Compatible with existing HTML / PHP / ACF markup.
   ========================================================= */

(function (window, document) {
  "use strict";

  /* =========================================================
     GLOBAL CONFIG
     ========================================================= */

  var REDUCED_MOTION =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  var RAF =
    window.requestAnimationFrame ||
    function (callback) {
      return window.setTimeout(function () {
        callback(Date.now());
      }, 16);
    };

  /* =========================================================
     HELPERS
     ========================================================= */

  function $all(selector, context) {
    return Array.prototype.slice.call(
      (context || document).querySelectorAll(selector),
    );
  }

  function $one(selector, context) {
    return (context || document).querySelector(selector);
  }

  function hasClass(el, className) {
    return el && el.classList && el.classList.contains(className);
  }

  function addClass(el, className) {
    if (el && el.classList) {
      el.classList.add(className);
    }
  }

  function removeClass(el, className) {
    if (el && el.classList) {
      el.classList.remove(className);
    }
  }

  function toggleClass(el, className, state) {
    if (el && el.classList) {
      el.classList.toggle(className, state);
    }
  }

  function parseNumber(value, fallback) {
    var number = parseFloat(value);
    return isNaN(number) ? fallback : number;
  }

  function parseInteger(value, fallback) {
    var number = parseInt(value, 10);
    return isNaN(number) ? fallback : number;
  }

  function formatNumber(number) {
    try {
      return new Intl.NumberFormat("en-US").format(number);
    } catch (e) {
      return String(number).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
  }

  function onReady(callback) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", callback, {
        once: true,
      });
    } else {
      callback();
    }
  }

  /* =========================================================
     1. REVEAL ANIMATIONS
     ========================================================= */

  function initRevealAnimations() {
    var root = document.documentElement;

    if (root && root.classList) {
      root.classList.remove("no-reveals");
      root.classList.add("reveals-enabled");
    }

    var elements = $all("[data-reveal]");

    if (!elements.length) {
      return;
    }

    elements.forEach(function (el) {
      var delay = el.getAttribute("data-reveal-delay");

      if (delay) {
        el.style.setProperty("--reveal-delay", delay + "ms");
      }
    });

    if (!("IntersectionObserver" in window)) {
      elements.forEach(function (el) {
        addClass(el, "is-visible");
      });

      return;
    }

    var observer = new IntersectionObserver(
      function (entries, obs) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }

          addClass(entry.target, "is-visible");
          obs.unobserve(entry.target);
        });
      },
      {
        threshold: 0.15,
        rootMargin: "0px 0px -60px 0px",
      },
    );

    elements.forEach(function (el) {
      observer.observe(el);
    });
  }

  /* =========================================================
     2. COUNTERS
     ---------------------------------------------------------
     Supports BOTH existing counter systems:

     System A:
       [data-counter]
       data-target="2000"

     System B:
       [data-count-target]
       data-count-suffix="+"
       data-count-duration="1800"

     This keeps existing markup compatible.
     ========================================================= */

  var COUNTER_DONE_ATTR = "data-count-done";
  var COUNTER_DURATION_DEFAULT = 1800;

  function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
  }

  function easeOutQuad(t) {
    return t * (2 - t);
  }

  function animateCounter(el) {
    if (!el || el.getAttribute(COUNTER_DONE_ATTR) === "1") {
      return;
    }

    var isNewCounter = el.hasAttribute("data-count-target");

    var target;

    if (isNewCounter) {
      target = parseInteger(el.getAttribute("data-count-target"), NaN);
    } else {
      target = parseInteger(el.getAttribute("data-target"), NaN);
    }

    if (isNaN(target) || target < 0) {
      return;
    }

    var suffix = isNewCounter ? el.getAttribute("data-count-suffix") || "" : "";

    var duration;

    if (isNewCounter) {
      duration = parseInteger(
        el.getAttribute("data-count-duration"),
        COUNTER_DURATION_DEFAULT,
      );
    } else {
      duration = 1600;
    }

    if (!duration || duration <= 0) {
      duration = COUNTER_DURATION_DEFAULT;
    }

    if (REDUCED_MOTION) {
      el.textContent = formatNumber(target) + suffix;

      el.setAttribute(COUNTER_DONE_ATTR, "1");

      return;
    }

    var startTime = null;

    function step(timestamp) {
      if (startTime === null) {
        startTime = timestamp;
      }

      var progress = Math.min((timestamp - startTime) / duration, 1);

      var eased = isNewCounter ? easeOutCubic(progress) : easeOutQuad(progress);

      var current = Math.round(target * eased);

      el.textContent = formatNumber(current) + suffix;

      if (progress < 1) {
        RAF(step);
      } else {
        el.textContent = formatNumber(target) + suffix;

        el.setAttribute(COUNTER_DONE_ATTR, "1");
      }
    }

    RAF(step);
  }

  function initCounters() {
    var counters = $all("[data-counter], [data-count-target]");

    if (!counters.length) {
      return;
    }

    if (!("IntersectionObserver" in window)) {
      counters.forEach(animateCounter);
      return;
    }

    var observer = new IntersectionObserver(
      function (entries, obs) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }

          animateCounter(entry.target);
          obs.unobserve(entry.target);
        });
      },
      {
        threshold: 0.4,
        rootMargin: "0px 0px -10% 0px",
      },
    );

    counters.forEach(function (counter) {
      observer.observe(counter);
    });
  }

  /* =========================================================
     3. ACCORDION
     ========================================================= */

  function initAccordion() {
    var items = $all(".accordion__item");

    if (!items.length) {
      return;
    }

    items.forEach(function (item) {
      var trigger = $one(".accordion__trigger", item);

      if (!trigger) {
        return;
      }

      trigger.addEventListener("click", function () {
        var isOpen = hasClass(item, "is-open");

        items.forEach(function (other) {
          removeClass(other, "is-open");

          var otherTrigger = $one(".accordion__trigger", other);

          if (otherTrigger) {
            otherTrigger.setAttribute("aria-expanded", "false");
          }
        });

        if (!isOpen) {
          addClass(item, "is-open");

          trigger.setAttribute("aria-expanded", "true");
        }
      });
    });
  }

  /* =========================================================
     4. PARTNERS CAROUSEL
     ========================================================= */

  function initPartnersCarousel() {
    var track = document.getElementById("partnersTrack");

    if (!track) {
      return;
    }

    var cards = $all(".partner-card", track);

    var dotsWrap = document.getElementById("partnersDots");

    var prevBtn = document.getElementById("partnersPrev");

    var nextBtn = document.getElementById("partnersNext");

    if (!cards.length) {
      return;
    }

    var current = 0;
    var lastDotCount = -1;
    var resizeTimer = null;
    var touchStartX = 0;

    function getCardsPerView() {
      if (window.innerWidth <= 767) {
        return 1;
      }

      if (window.innerWidth <= 1024) {
        return 2;
      }

      return 3;
    }

    function getTotalSlides() {
      var perView = getCardsPerView();

      return Math.max(1, Math.ceil(cards.length / perView));
    }

    function render() {
      var perView = getCardsPerView();

      var totalSlides = getTotalSlides();

      if (current >= totalSlides) {
        current = totalSlides - 1;
      }

      if (current < 0) {
        current = 0;
      }

      var start = current * perView;

      var end = Math.min(start + perView, cards.length);

      cards.forEach(function (card, index) {
        toggleClass(card, "is-hidden", index < start || index >= end);
      });

      if (dotsWrap) {
        $all("button", dotsWrap).forEach(function (dot, index) {
          toggleClass(dot, "is-active", index === current);
        });
      }

      if (prevBtn) {
        prevBtn.disabled = totalSlides <= 1;
      }

      if (nextBtn) {
        nextBtn.disabled = totalSlides <= 1;
      }
    }

    function goTo(index) {
      var totalSlides = getTotalSlides();

      current = ((index % totalSlides) + totalSlides) % totalSlides;

      render();
    }

    function buildDots() {
      if (!dotsWrap) {
        return;
      }

      var totalSlides = getTotalSlides();

      if (totalSlides === lastDotCount) {
        return;
      }

      lastDotCount = totalSlides;

      dotsWrap.innerHTML = "";

      for (var i = 0; i < totalSlides; i++) {
        var dot = document.createElement("button");

        dot.type = "button";

        dot.setAttribute("aria-label", "Go to partner slide " + (i + 1));

        dot.addEventListener(
          "click",
          (function (index) {
            return function () {
              goTo(index);
            };
          })(i),
        );

        dotsWrap.appendChild(dot);
      }
    }

    if (prevBtn) {
      prevBtn.addEventListener("click", function () {
        goTo(current - 1);
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", function () {
        goTo(current + 1);
      });
    }

    track.addEventListener(
      "touchstart",
      function (event) {
        if (event.touches && event.touches.length) {
          touchStartX = event.touches[0].clientX;
        }
      },
      { passive: true },
    );

    track.addEventListener(
      "touchend",
      function (event) {
        if (!event.changedTouches || !event.changedTouches.length) {
          return;
        }

        var delta = event.changedTouches[0].clientX - touchStartX;

        if (Math.abs(delta) < 40) {
          return;
        }

        goTo(delta < 0 ? current + 1 : current - 1);
      },
      { passive: true },
    );

    window.addEventListener("resize", function () {
      clearTimeout(resizeTimer);

      resizeTimer = setTimeout(function () {
        buildDots();
        render();
      }, 150);
    });

    buildDots();
    render();
  }

  /* =========================================================
     5. PROJECT FILTERS
     ---------------------------------------------------------
     Supports:
       .project-filters__btn
       [data-project-filters]
       [data-project-grid]

     Also keeps standalone filter buttons compatible.
     ========================================================= */

  function initProjectFilters() {
    var wrap = document.querySelector("[data-project-filters]");

    var grid = document.querySelector("[data-project-grid]");

    /* -------------------------------------------------------
       Standalone project filter buttons
       ------------------------------------------------------- */

    var standaloneButtons = $all(".project-filters__btn");

    if (standaloneButtons.length) {
      standaloneButtons.forEach(function (button) {
        if (button.getAttribute("data-filter-bound") === "1") {
          return;
        }

        button.setAttribute("data-filter-bound", "1");

        button.addEventListener("click", function () {
          standaloneButtons.forEach(function (btn) {
            removeClass(btn, "is-active");
          });

          addClass(button, "is-active");
        });
      });
    }

    /* -------------------------------------------------------
       Full project filter system
       ------------------------------------------------------- */

    if (!wrap || !grid) {
      return;
    }

    var buttons = $all(".project-filters__btn", wrap);

    var items = $all("[data-categories], figure", grid);

    if (!buttons.length) {
      return;
    }

    buttons.forEach(function (button) {
      if (button.getAttribute("data-project-filter-bound") === "1") {
        return;
      }

      button.setAttribute("data-project-filter-bound", "1");

      button.addEventListener("click", function (event) {
        event.preventDefault();
        event.stopPropagation();

        buttons.forEach(function (btn) {
          removeClass(btn, "is-active");
        });

        addClass(button, "is-active");

        var filter = button.getAttribute("data-filter");

        items.forEach(function (item) {
          if (filter === "all") {
            item.style.display = "";
            return;
          }

          var raw = item.getAttribute("data-categories") || "";

          var categories = raw.split(/\s+/).filter(Boolean);

          var hasCategories = item.hasAttribute("data-categories");

          var matches = categories.indexOf(filter) !== -1;

          item.style.display = !hasCategories || matches ? "" : "none";
        });
      });
    });
  }

  /* =========================================================
     6. TEAM ENQUIRY MODAL
     ========================================================= */

  function initTeamEnquiryModal() {
    var modal = document.getElementById("teamEnquiryModal");

    if (!modal) {
      return;
    }

    var form = document.getElementById("teamEnquiryForm");

    var teamField = document.getElementById("teamEnquiryTeamField");

    var status = $one(".team-modal__status", modal);

    var submitBtn = form ? $one(".team-modal__submit", form) : null;

    var submitLabel = form ? $one(".js-submit-label", form) : null;

    var lastFocused = null;
    var savedScrollY = 0;
    var isOpen = false;

    function openModal(teamName) {
      if (isOpen) {
        return;
      }

      lastFocused = document.activeElement;

      if (teamField) {
        teamField.value = teamName || "";
      }

      savedScrollY = window.scrollY || window.pageYOffset || 0;

      addClass(modal, "is-open");

      modal.setAttribute("aria-hidden", "false");

      addClass(document.body, "team-modal-open");

      document.body.style.overflow = "hidden";

      document.body.style.position = "fixed";

      document.body.style.top = "-" + savedScrollY + "px";

      document.body.style.left = "0";
      document.body.style.right = "0";

      isOpen = true;

      var firstField = form ? $one("input, textarea", form) : null;

      if (firstField) {
        window.setTimeout(function () {
          firstField.focus();
        }, 60);
      }
    }

    function closeModal() {
      if (!isOpen) {
        return;
      }

      removeClass(modal, "is-open");

      modal.setAttribute("aria-hidden", "true");

      removeClass(document.body, "team-modal-open");

      document.body.style.overflow = "";
      document.body.style.position = "";
      document.body.style.top = "";
      document.body.style.left = "";
      document.body.style.right = "";

      isOpen = false;

      window.scrollTo(0, savedScrollY);

      if (lastFocused && typeof lastFocused.focus === "function") {
        lastFocused.focus();
      }
    }

    document.addEventListener("click", function (event) {
      var target = event.target;

      if (!target || typeof target.closest !== "function") {
        return;
      }

      var trigger = target.closest(
        '.js-team-enquiry-trigger, [data-cg-action="open-team-enquiry"], [data-action="team-enquire"]',
      );

      if (!trigger) {
        return;
      }

      event.preventDefault();

      var teamName =
        trigger.getAttribute("data-team") ||
        trigger.getAttribute("data-subject") ||
        "";

      openModal(teamName);
    });

    $all(".js-team-modal-close", modal).forEach(function (element) {
      element.addEventListener("click", closeModal);
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && hasClass(modal, "is-open")) {
        closeModal();
      }
    });

    if (!form) {
      return;
    }

    form.addEventListener("submit", function (event) {
      event.preventDefault();

      if (typeof window.cgTeamEnquiry === "undefined") {
        if (status) {
          status.textContent =
            "Form is not configured correctly. Please try again later.";
        }

        return;
      }

      var data = new FormData(form);

      data.append("action", "cg_team_enquiry");

      data.append("nonce", window.cgTeamEnquiry.nonce);

      if (submitBtn) {
        submitBtn.disabled = true;
      }

      if (submitLabel) {
        submitLabel.textContent = "Sending…";
      }

      if (status) {
        status.textContent = "";
      }

      fetch(window.cgTeamEnquiry.ajax_url, {
        method: "POST",
        credentials: "same-origin",
        body: data,
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (json) {
          if (json && json.success) {
            if (status) {
              status.textContent = json.data.message;
            }

            form.reset();

            window.setTimeout(closeModal, 1800);
          } else {
            if (status) {
              status.textContent =
                json && json.data && json.data.message
                  ? json.data.message
                  : "Something went wrong. Please try again.";
            }
          }
        })
        .catch(function () {
          if (status) {
            status.textContent = "Something went wrong. Please try again.";
          }
        })
        .finally(function () {
          if (submitBtn) {
            submitBtn.disabled = false;
          }

          if (submitLabel) {
            submitLabel.textContent = "Submit Enquiry";
          }
        });
    });
  }

  /* =========================================================
     7. BLOG CATEGORY FILTER TABS
     ========================================================= */

  function initBlogTabs() {
    var tabsWrap = document.querySelector(".cg-blog-tabs");

    if (!tabsWrap) {
      return;
    }

    var tabs = $all(".cg-blog-tab[data-blog-category]", tabsWrap);

    if (!tabs.length) {
      return;
    }

    var grid = document.getElementById("cg-blog-grid");

    if (!grid) {
      return;
    }

    var cards = $all(".cg-blog-card[data-blog-categories]", grid);

    if (!cards.length) {
      return;
    }

    function applyFilter(slug) {
      var showAll = slug === "all";

      cards.forEach(function (card) {
        var raw = (card.getAttribute("data-blog-categories") || "").trim();

        var categories = raw
          ? raw
              .split(",")
              .map(function (value) {
                return value.trim();
              })
              .filter(Boolean)
          : ["all"];

        var matches = showAll || categories.indexOf(slug) !== -1;

        toggleClass(card, "is-hidden", !matches);

        card.style.display = matches ? "" : "none";
      });

      tabs.forEach(function (tab) {
        var tabSlug = tab.getAttribute("data-blog-category");

        var active = tabSlug === slug;

        toggleClass(tab, "is-active", active);

        tab.setAttribute("aria-pressed", active ? "true" : "false");
      });

      try {
        grid.scrollIntoView({
          behavior: REDUCED_MOTION ? "auto" : "smooth",
          block: "start",
        });
      } catch (e) {
        /* Ignore unsupported scroll options */
      }
    }

    tabs.forEach(function (tab) {
      tab.addEventListener("click", function () {
        applyFilter(tab.getAttribute("data-blog-category") || "all");
      });
    });

    try {
      var url = new URL(window.location.href);

      var query = url.searchParams.get("blog-category");

      if (
        query &&
        tabs.some(function (tab) {
          return tab.getAttribute("data-blog-category") === query;
        })
      ) {
        applyFilter(query);
      }
    } catch (e) {
      /* Ignore invalid URL parsing */
    }
  }

  /* =========================================================
     8. BLOG IMAGE CAROUSEL
     ========================================================= */

  function initBlogCarousel(carousel) {
    if (!carousel) {
      return;
    }

    if (carousel.getAttribute("data-carousel-init") === "1") {
      return;
    }

    var total = parseInteger(carousel.getAttribute("data-slide-count"), 0);

    var track = $one(".cg-carousel__track", carousel);

    var prev = $one(".cg-carousel__arrow--prev", carousel);

    var next = $one(".cg-carousel__arrow--next", carousel);

    var dots = $one(".cg-carousel__dots", carousel);

    var slides = track ? $all(".cg-carousel__slide", track) : [];

    if (!track || !slides.length || total === 0) {
      return;
    }

    carousel.setAttribute("data-carousel-init", "1");

    var current = 0;

    function goTo(index) {
      if (index < 0) {
        index = slides.length - 1;
      }

      if (index >= slides.length) {
        index = 0;
      }

      current = index;

      carousel.setAttribute("data-current", String(current));

      if (REDUCED_MOTION) {
        slides.forEach(function (slide) {
          slide.style.transition = "none";
        });
      }

      slides.forEach(function (slide, index) {
        toggleClass(slide, "is-active", index === current);
      });

      if (dots) {
        $all(".cg-carousel__dot", dots).forEach(function (dot, index) {
          var active = index === current;

          toggleClass(dot, "is-active", active);

          dot.setAttribute("aria-current", active ? "true" : "false");
        });
      }

      if (REDUCED_MOTION) {
        RAF(function () {
          slides.forEach(function (slide) {
            slide.style.transition = "";
          });
        });
      }
    }

    if (prev) {
      prev.addEventListener("click", function () {
        goTo(current - 1);
      });
    }

    if (next) {
      next.addEventListener("click", function () {
        goTo(current + 1);
      });
    }

    if (dots) {
      dots.addEventListener("click", function (event) {
        var target = event.target;

        if (!target || typeof target.closest !== "function") {
          return;
        }

        var dot = target.closest(".cg-carousel__dot");

        if (!dot) {
          return;
        }

        var index = parseInteger(dot.getAttribute("data-index"), 0);

        goTo(index);
      });
    }

    carousel.setAttribute("tabindex", "0");

    carousel.addEventListener("keydown", function (event) {
      if (event.key === "ArrowLeft") {
        event.preventDefault();
        goTo(current - 1);
      }

      if (event.key === "ArrowRight") {
        event.preventDefault();
        goTo(current + 1);
      }
    });

    goTo(0);
  }

  function initBlogCarousels() {
    $all(".cg-carousel[data-carousel]").forEach(initBlogCarousel);
  }

  /* =========================================================
     9. BLOG CARD TAG TOGGLE
     ---------------------------------------------------------
     Shows first 2 tags.
     Additional tags become +N.
     Expanded state changes +N to −.
     ========================================================= */

  function initBlogTagToggle() {
    var wrappers = $all(".cg-blog-grid-wrap .cg-blog-card__tags");

    if (!wrappers.length) {
      return;
    }

    wrappers.forEach(function (tagsWrap) {
      if (tagsWrap.getAttribute("data-tags-initialized") === "true") {
        return;
      }

      var tags = $all(".cg-blog-card__tag", tagsWrap);

      if (tags.length <= 2) {
        return;
      }

      tagsWrap.setAttribute("data-tags-initialized", "true");

      var remaining = tags.length - 2;

      tags.slice(2).forEach(function (tag) {
        addClass(tag, "is-extra-tag");
      });

      var toggle = document.createElement("button");

      toggle.type = "button";
      toggle.className = "cg-tags-toggle";

      toggle.textContent = "+" + remaining;

      toggle.setAttribute("aria-expanded", "false");

      toggle.setAttribute("aria-label", "Show " + remaining + " more tags");

      tags[1].insertAdjacentElement("afterend", toggle);

      toggle.addEventListener("click", function (event) {
        event.preventDefault();
        event.stopPropagation();

        var expanded = tagsWrap.classList.toggle("is-expanded");

        if (expanded) {
          tagsWrap.appendChild(toggle);

          toggle.textContent = "−";

          toggle.setAttribute("aria-expanded", "true");

          toggle.setAttribute("aria-label", "Show fewer tags");
        } else {
          tags[1].insertAdjacentElement("afterend", toggle);

          toggle.textContent = "+" + remaining;

          toggle.setAttribute("aria-expanded", "false");

          toggle.setAttribute("aria-label", "Show " + remaining + " more tags");
        }
      });
    });
  }

  /* =========================================================
     MAIN INITIALIZATION
     ========================================================= */

  function init() {
    initRevealAnimations();
    initCounters();
    initAccordion();

    initPartnersCarousel();

    initProjectFilters();

    initTeamEnquiryModal();

    initBlogTabs();
    initBlogCarousels();
    initBlogTagToggle();
  }

  onReady(init);
})(window, document);

document.addEventListener(
  "mousedown",
  function (e) {
    const button = e.target.closest('a, button, [role="button"]');

    if (!button) return;

    // Prevent mouse click from applying browser focus
    e.preventDefault();
  },
  true,
);

(function () {
  "use strict";

  function initGallery(gallery) {
    var dialog = gallery.nextElementSibling;
    if (!dialog || !dialog.classList.contains("hvs-lightbox")) {
      return;
    }
    if (typeof dialog.showModal !== "function") {
      // <dialog> unsupported: leave the grid as plain, non-interactive
      // images rather than risk a broken modal experience.
      return;
    }

    var tiles = Array.prototype.slice.call(
      gallery.querySelectorAll(".hvs-gallery__item"),
    );
    if (!tiles.length) {
      return;
    }

    var img = dialog.querySelector("img");
    var liveRegion = dialog.querySelector("[aria-live]");
    var closeButton = dialog.querySelector(".hvs-lightbox__close");
    var current = 0;
    var lastFocused = null;

    function show(index) {
      current = ((index % tiles.length) + tiles.length) % tiles.length;
      var tile = tiles[current];
      if (img) {
        img.src = tile.getAttribute("data-image") || "";
        img.alt = tile.getAttribute("data-alt") || "";
      }
      if (liveRegion) {
        liveRegion.textContent = current + 1 + " / " + tiles.length;
      }
    }

    function open(index) {
      lastFocused = document.activeElement;
      show(index);
      dialog.showModal();
    }

    function close() {
      dialog.close();
      if (lastFocused && typeof lastFocused.focus === "function") {
        lastFocused.focus({ preventScroll: true });
      }
    }

    tiles.forEach(function (tile, index) {
      tile.addEventListener("click", function () {
        open(index);
      });
    });

    if (closeButton) {
      closeButton.addEventListener("click", close);
    }

    dialog.querySelectorAll("[data-dir]").forEach(function (button) {
      button.addEventListener("click", function () {
        show(current + Number(button.getAttribute("data-dir")));
      });
    });

    dialog.addEventListener("keydown", function (event) {
      if (event.key === "ArrowRight") {
        show(current + 1);
      } else if (event.key === "ArrowLeft") {
        show(current - 1);
      } else if (event.key === "Escape") {
        // Native <dialog> already closes on Escape; this just
        // restores focus consistently with the close button path.
        close();
      }
    });

    dialog.addEventListener("cancel", function () {
      // Fires on native Escape-driven close.
      if (lastFocused && typeof lastFocused.focus === "function") {
        lastFocused.focus({ preventScroll: true });
      }
    });

    dialog.addEventListener("click", function (event) {
      if (event.target === dialog) {
        close();
      }
    });
  }

  function init() {
    document.querySelectorAll(".hvs-gallery").forEach(initGallery);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
(function () {
  "use strict";

  function initProjectReveals() {
    var elements = document.querySelectorAll(
      ".project-reusable .project-reveal",
    );

    if (!elements.length) {
      return;
    }

    if (
      window.matchMedia &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches
    ) {
      elements.forEach(function (element) {
        element.classList.add("is-visible");
      });
      return;
    }

    if (!("IntersectionObserver" in window)) {
      elements.forEach(function (element) {
        element.classList.add("is-visible");
      });
      return;
    }

    var observer = new IntersectionObserver(
      function (entries, currentObserver) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }

          entry.target.classList.add("is-visible");
          currentObserver.unobserve(entry.target);
        });
      },
      {
        root: null,
        rootMargin: "0px 0px -8% 0px",
        threshold: 0.12,
      },
    );

    elements.forEach(function (element) {
      observer.observe(element);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initProjectReveals);
  } else {
    initProjectReveals();
  }
})();
(function () {
  "use strict";
  function init() {
    document.querySelectorAll("[data-lm-gallery]").forEach(function (g) {
      var s = [].slice.call(g.querySelectorAll("[data-lm-slide]")),
        i = 0,
        c = g.querySelector("[data-lm-count]");
      function show(n) {
        if (!s.length) return;
        i = (n + s.length) % s.length;
        s.forEach(function (x, j) {
          x.classList.toggle("is-active", j === i);
          x.style.display = window.matchMedia("(max-width:600px)").matches
            ? j === i
              ? "block"
              : "none"
            : "block";
        });
        if (c)
          c.textContent =
            String(i + 1).padStart(2, "0") +
            " / " +
            String(s.length).padStart(2, "0");
      }
      var p = g.querySelector("[data-lm-prev]"),
        n = g.querySelector("[data-lm-next]");
      if (p)
        p.onclick = function () {
          show(i - 1);
        };
      if (n)
        n.onclick = function () {
          show(i + 1);
        };
      show(0);
    });
    var e = document.querySelectorAll(".lm-reveal");
    if (!e.length) return;
    if (
      window.matchMedia("(prefers-reduced-motion:reduce)").matches ||
      !("IntersectionObserver" in window)
    ) {
      e.forEach(function (x) {
        x.classList.add("is-visible");
      });
      return;
    }
    var o = new IntersectionObserver(
      function (a) {
        a.forEach(function (x) {
          if (x.isIntersecting) {
            x.target.classList.add("is-visible");
            o.unobserve(x.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -6% 0px" },
    );
    e.forEach(function (x) {
      o.observe(x);
    });
  }
  if (document.readyState === "loading")
    document.addEventListener("DOMContentLoaded", init);
  else init();
})();
