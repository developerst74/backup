(() => {
  "use strict";

  /* ---------------------------------------------------------
   * 1. REVEAL-ON-SCROLL ANIMATIONS
   * --------------------------------------------------------- */
  const rootEl = document.documentElement;
  if (rootEl && rootEl.classList) {
    rootEl.classList.remove("no-reveals");
    rootEl.classList.add("reveals-enabled");
  }

  const revealEls = document.querySelectorAll("[data-reveal]");

  revealEls.forEach((el) => {
    const delay = el.getAttribute("data-reveal-delay");
    if (delay) {
      el.style.setProperty("--reveal-delay", `${delay}ms`);
    }
  });

  if ("IntersectionObserver" in window) {
    const revealObserver = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            obs.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.15,
        rootMargin: "0px 0px -60px 0px",
      },
    );

    revealEls.forEach((el) => revealObserver.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add("is-visible"));
  }

  /* ---------------------------------------------------------
   * 2. ANIMATED STAT COUNTERS
   * --------------------------------------------------------- */
  const counters = document.querySelectorAll("[data-counter]");

  const easeOutQuad = (t) => t * (2 - t);

  const animateCounter = (el) => {
    const target = parseFloat(el.getAttribute("data-target"), 10) || 0;
    const duration = 1600;
    const start = performance.now();

    const step = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const eased = easeOutQuad(progress);
      const value = Math.round(target * eased);

      el.textContent = value.toLocaleString();

      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = target.toLocaleString();
      }
    };

    requestAnimationFrame(step);
  };

  if ("IntersectionObserver" in window) {
    const counterObserver = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animateCounter(entry.target);
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.6 },
    );

    counters.forEach((el) => counterObserver.observe(el));
  } else {
    counters.forEach(animateCounter);
  }

  /* ---------------------------------------------------------
   * 3. FAQ ACCORDION
   * --------------------------------------------------------- */
  const accordionItems = document.querySelectorAll(".accordion__item");

  accordionItems.forEach((item) => {
    const trigger = item.querySelector(".accordion__trigger");
    if (!trigger) return;

    trigger.addEventListener("click", () => {
      const isOpen = item.classList.contains("is-open");

      accordionItems.forEach((other) => {
        other.classList.remove("is-open");
        const otherTrigger = other.querySelector(".accordion__trigger");
        if (otherTrigger) otherTrigger.setAttribute("aria-expanded", "false");
      });

      if (!isOpen) {
        item.classList.add("is-open");
        trigger.setAttribute("aria-expanded", "true");
      }
    });
  });

  /* ---------------------------------------------------------
   * 4. STRATEGIC PARTNERS CAROUSEL
   *    (single source of truth — no duplicate instance elsewhere)
   * --------------------------------------------------------- */
  (() => {
    "use strict";

    const track = document.getElementById("partnersTrack");
    if (!track) return;

    const cards = Array.from(track.querySelectorAll(".partner-card"));
    const dotsWrap = document.getElementById("partnersDots");
    const prevBtn = document.getElementById("partnersPrev");
    const nextBtn = document.getElementById("partnersNext");

    if (!cards.length) return;

    let current = 0;

    const getCardsPerView = () => {
      if (window.innerWidth <= 767) return 1;
      if (window.innerWidth <= 1024) return 2;
      return 3;
    };

    const getTotalSlides = () => {
      const perView = getCardsPerView();
      return Math.max(1, Math.ceil(cards.length / perView));
    };

    const renderPartners = () => {
      const perView = getCardsPerView();
      const totalSlides = getTotalSlides();

      if (current >= totalSlides) current = totalSlides - 1;
      if (current < 0) current = 0;

      const start = current * perView;
      const end = Math.min(start + perView, cards.length);

      cards.forEach((card, i) => {
        card.classList.toggle("is-hidden", i < start || i >= end);
      });

      if (dotsWrap) {
        Array.from(dotsWrap.children).forEach((dot, i) => {
          dot.classList.toggle("is-active", i === current);
        });
      }

      if (prevBtn) prevBtn.disabled = totalSlides <= 1;
      if (nextBtn) nextBtn.disabled = totalSlides <= 1;
    };

    const goToPartnerSlide = (index) => {
      const totalSlides = getTotalSlides();
      current = ((index % totalSlides) + totalSlides) % totalSlides;
      renderPartners();
    };

    let lastDotCount = -1;

    const buildPartnerDots = () => {
      if (!dotsWrap) return;

      const totalSlides = getTotalSlides();
      if (totalSlides === lastDotCount) return;

      lastDotCount = totalSlides;
      dotsWrap.innerHTML = "";

      for (let i = 0; i < totalSlides; i++) {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.setAttribute("aria-label", `Go to partner slide ${i + 1}`);
        dot.addEventListener("click", () => goToPartnerSlide(i));
        dotsWrap.appendChild(dot);
      }
    };

    // IMPORTANT: force slide 1 / dot 1 active on first load, always.
    current = 0;
    buildPartnerDots();
    renderPartners();

    if (prevBtn) {
      prevBtn.addEventListener("click", () => goToPartnerSlide(current - 1));
    }
    if (nextBtn) {
      nextBtn.addEventListener("click", () => goToPartnerSlide(current + 1));
    }

    let touchStartX = 0;
    track.addEventListener(
      "touchstart",
      (e) => {
        touchStartX = e.touches[0].clientX;
      },
      { passive: true },
    );

    track.addEventListener(
      "touchend",
      (e) => {
        const delta = e.changedTouches[0].clientX - touchStartX;
        if (Math.abs(delta) < 40) return;
        goToPartnerSlide(delta < 0 ? current + 1 : current - 1);
      },
      { passive: true },
    );

    let resizeTimer;
    window.addEventListener("resize", () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        buildPartnerDots();
        renderPartners();
      }, 150);
    });
  })();

  /* ---------------------------------------------------------
   * 5. PROJECT FILTER BUTTONS (simple active-state toggle)
   * --------------------------------------------------------- */
  const filterButtons = document.querySelectorAll(".project-filters__btn");

  filterButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      filterButtons.forEach((b) => b.classList.remove("is-active"));
      btn.classList.add("is-active");
    });
  });
})();

/* =============================================================
 * 6. TEAM ENQUIRY MODAL + (optional) AJAX PROJECT FILTER GRID
 * ============================================================= */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    initTeamEnquiryModal();
    initProjectFilters();
  });

  function initTeamEnquiryModal() {
    var modal = document.getElementById("teamEnquiryModal");
    if (!modal) return;

    var form = document.getElementById("teamEnquiryForm");
    var teamField = document.getElementById("teamEnquiryTeamField");
    var status = modal.querySelector(".team-modal__status");
    var submitBtn = form ? form.querySelector(".team-modal__submit") : null;
    var submitLabel = form ? form.querySelector(".js-submit-label") : null;
    var lastFocused = null;
    var savedScrollY = 0;
    var isOpen = false;

    function openModal(teamName) {
      if (isOpen) return;
      lastFocused = document.activeElement;
      if (teamField) teamField.value = teamName || "";
      savedScrollY = window.scrollY || window.pageYOffset || 0;
      modal.classList.add("is-open");
      modal.setAttribute("aria-hidden", "false");
      document.body.classList.add("team-modal-open");
      document.body.style.overflow = "hidden";
      document.body.style.position = "fixed";
      document.body.style.top = "-" + savedScrollY + "px";
      document.body.style.left = "0";
      document.body.style.right = "0";
      isOpen = true;
      var firstField = form ? form.querySelector("input, textarea") : null;
      if (firstField) {
        setTimeout(function () {
          firstField.focus();
        }, 60);
      }
    }

    function closeModal() {
      if (!isOpen) return;
      modal.classList.remove("is-open");
      modal.setAttribute("aria-hidden", "true");
      document.body.classList.remove("team-modal-open");
      document.body.style.overflow = "";
      document.body.style.position = "";
      document.body.style.top = "";
      document.body.style.left = "";
      document.body.style.right = "";
      isOpen = false;
      if (savedScrollY > 0) {
        window.scrollTo(0, savedScrollY);
      }
      if (lastFocused) lastFocused.focus();
    }

    document.addEventListener("click", function (e) {
      var trigger = e.target.closest(
        '.js-team-enquiry-trigger, [data-cg-action="open-team-enquiry"], [data-action="team-enquire"]',
      );
      if (!trigger) return;
      e.preventDefault();
      var teamName =
        trigger.getAttribute("data-team") ||
        trigger.getAttribute("data-subject") ||
        "";
      openModal(teamName);
    });

    modal.querySelectorAll(".js-team-modal-close").forEach(function (el) {
      el.addEventListener("click", closeModal);
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && modal.classList.contains("is-open")) {
        closeModal();
      }
    });

    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
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

        if (submitBtn) submitBtn.disabled = true;
        if (submitLabel) submitLabel.textContent = "Sending…";
        if (status) status.textContent = "";

        fetch(window.cgTeamEnquiry.ajax_url, {
          method: "POST",
          credentials: "same-origin",
          body: data,
        })
          .then(function (res) {
            return res.json();
          })
          .then(function (json) {
            if (json && json.success) {
              if (status) status.textContent = json.data.message;
              form.reset();
              setTimeout(closeModal, 1800);
            } else {
              if (status) {
                status.textContent =
                  (json && json.data && json.data.message) ||
                  "Something went wrong. Please try again.";
              }
            }
          })
          .catch(function () {
            if (status) {
              status.textContent = "Something went wrong. Please try again.";
            }
          })
          .finally(function () {
            if (submitBtn) submitBtn.disabled = false;
            if (submitLabel) submitLabel.textContent = "Submit Enquiry";
          });
      });
    }
  }

  function initProjectFilters() {
    var wrap = document.querySelector("[data-project-filters]");
    var grid = document.querySelector("[data-project-grid]");
    if (!wrap || !grid) return;

    var buttons = wrap.querySelectorAll(".project-filters__btn");
    var items = grid.querySelectorAll("[data-categories], figure");

    buttons.forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.stopPropagation();
        buttons.forEach(function (b) {
          b.classList.remove("is-active");
        });
        btn.classList.add("is-active");

        var filter = btn.getAttribute("data-filter");
        items.forEach(function (item) {
          if (filter === "all") {
            item.style.display = "";
            return;
          }
          var cats = (item.getAttribute("data-categories") || "").split(" ");
          var matches = cats.indexOf(filter) !== -1;
          var hasCats = item.hasAttribute("data-categories");
          item.style.display = !hasCats || matches ? "" : "none";
        });
      });
    });
  }
})();
