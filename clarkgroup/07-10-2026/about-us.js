(() => {
  ("use strict");

  /* ---------------------------------------------------------
   * 1. REVEAL-ON-SCROLL ANIMATIONS
   * --------------------------------------------------------- */
  // const rootEl = document.documentElement;
  // if (rootEl && rootEl.classList) {
  //   rootEl.classList.remove("no-reveals");
  //   rootEl.classList.add("reveals-enabled");
  // }

  // const revealEls = document.querySelectorAll("[data-reveal]");

  // revealEls.forEach((el) => {
  //   const delay = el.getAttribute("data-reveal-delay");
  //   if (delay) {
  //     el.style.setProperty("--reveal-delay", `${delay}ms`);
  //   }
  // });

  // if ("IntersectionObserver" in window) {
  //   const revealObserver = new IntersectionObserver(
  //     (entries, obs) => {
  //       entries.forEach((entry) => {
  //         if (entry.isIntersecting) {
  //           entry.target.classList.add("is-visible");
  //           obs.unobserve(entry.target);
  //         }
  //       });
  //     },
  //     {
  //       threshold: 0.15,
  //       rootMargin: "0px 0px -60px 0px",
  //     },
  //   );

  //   revealEls.forEach((el) => revealObserver.observe(el));
  // } else {
  //   revealEls.forEach((el) => el.classList.add("is-visible"));
  // }
/* ---------------------------------------------------------
 * 2. ANIMATED STAT COUNTERS
 * ------------------------------------------------ */
(function () {

    const statsSection = document.querySelector(
        "body.page-template-page-about .about-stats"
    );

    if (!statsSection) return;

    const counters = statsSection.querySelectorAll(
        ".stats-grid .stat strong"
    );

    if (!counters.length) return;


    function animateCounter(el) {

        const original = el.textContent.trim();

        let prefix = "";
        let suffix = "";

        /* Prefix */
        if (original.charAt(0) === "$") {
            prefix = "$";
        }

        /* Suffix */
        if (original.indexOf("B+") !== -1) {
            suffix = "B+";
        } else if (original.indexOf("+") !== -1) {
            suffix = "+";
        }

        /* Numeric value */
        const numberString = original
            .replace("$", "")
            .replace("B+", "")
            .replace("+", "")
            .trim();

        const target = parseFloat(numberString);

        if (isNaN(target)) return;

        const hasDecimal = numberString.indexOf(".") !== -1;

        const duration = 1800;
        let startTime = null;


        function updateCounter(timestamp) {

            if (!startTime) {
                startTime = timestamp;
            }

            const progress = Math.min(
                (timestamp - startTime) / duration,
                1
            );

            /* Ease out */
            const eased =
                1 - Math.pow(1 - progress, 3);

            const currentValue = target * eased;

            let displayValue;

            if (hasDecimal) {
                displayValue = currentValue.toFixed(1);
            } else {
                displayValue = Math.floor(
                    currentValue
                ).toLocaleString();
            }

            el.textContent =
                prefix + displayValue + suffix;


            if (progress < 1) {

                requestAnimationFrame(
                    updateCounter
                );

            } else {

                /* Restore exact original value */
                el.textContent = original;

            }
        }

        requestAnimationFrame(updateCounter);
    }


    /* Start counters when section enters viewport */
    if ("IntersectionObserver" in window) {

        const observer = new IntersectionObserver(
            function (entries, obs) {

                entries.forEach(function (entry) {

                    if (!entry.isIntersecting) return;

                    /* Existing CSS entrance animation */
                    statsSection.classList.add(
                        "stats-entered"
                    );

                    counters.forEach(function (
                        counter,
                        index
                    ) {

                        setTimeout(function () {

                            animateCounter(
                                counter
                            );

                        }, index * 150);

                    });

                    /* Run only once */
                    obs.unobserve(
                        statsSection
                    );

                });

            },
            {
                threshold: 0.25
            }
        );

        observer.observe(statsSection);

    } else {

        /* Fallback */
        statsSection.classList.add(
            "stats-entered"
        );

        counters.forEach(
            animateCounter
        );

    }

})();

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
 *
 * About Us:
 * Existing #partnersTrack carousel remains unchanged.
 *
 * Ladies Mile:
 * Uses .partner-track + [data-partner-direction].
 *
 * Both pages are handled separately.
 * --------------------------------------------------------- */
(() => {
  "use strict";

  /* -------------------------------------------------------
   * 4A. LADIES MILE PARTNER SCROLLER
   * ------------------------------------------------------- */
  const ladiesMileSections = document.querySelectorAll(
    "body.page-template-ladiesmile-project-list .partners"
  );

  if (ladiesMileSections.length) {
    ladiesMileSections.forEach((section) => {
      const track = section.querySelector(".partner-track");
      const buttons = section.querySelectorAll(
        "[data-partner-direction]"
      );

      if (!track || !buttons.length) return;

      const reduceMotion =
        window.matchMedia &&
        window.matchMedia("(prefers-reduced-motion: reduce)").matches;

      const getStep = () => {
        const card = track.querySelector(".partner-card");

        if (!card) {
          return track.clientWidth * 0.8;
        }

        const styles = window.getComputedStyle(track);

        const gap =
          parseFloat(
            styles.columnGap ||
            styles.gap ||
            "20"
          ) || 20;

        return card.getBoundingClientRect().width + gap;
      };

      const updateButtons = () => {
        const maxScroll = Math.max(
          0,
          track.scrollWidth - track.clientWidth
        );

        buttons.forEach((button) => {
          const direction = parseInt(
            button.getAttribute(
              "data-partner-direction"
            ),
            10
          );

          if (maxScroll <= 1) {
            button.disabled = true;
            return;
          }

          if (direction < 0) {
            button.disabled =
              track.scrollLeft <= 1;
          } else {
            button.disabled =
              track.scrollLeft >= maxScroll - 1;
          }
        });
      };

      buttons.forEach((button) => {
        button.addEventListener("click", () => {
          const direction =
            parseInt(
              button.getAttribute(
                "data-partner-direction"
              ),
              10
            ) || 1;

          track.scrollBy({
            left: getStep() * direction,
            behavior: reduceMotion
              ? "auto"
              : "smooth",
          });
        });
      });

      track.addEventListener(
        "scroll",
        updateButtons,
        {
          passive: true,
        }
      );

      window.addEventListener(
        "resize",
        updateButtons
      );

      updateButtons();
    });

    /*
     * Important:
     * Ladies Mile has its own carousel markup.
     * Do not run the About Us carousel on this page.
     */
    return;
  }

  /* -------------------------------------------------------
   * 4B. ABOUT US PARTNER CAROUSEL
   * Existing About Us functionality preserved.
   * ------------------------------------------------------- */

  const track =
    document.getElementById("partnersTrack");

  if (!track) return;

  const cards = Array.from(
    track.querySelectorAll(".partner-card")
  );

  const dotsWrap =
    document.getElementById("partnersDots");

  const prevBtn =
    document.getElementById("partnersPrev");

  const nextBtn =
    document.getElementById("partnersNext");

  if (!cards.length) return;

  let current = 0;

  const getCardsPerView = () => {
    if (window.innerWidth <= 767) return 1;
    if (window.innerWidth <= 1024) return 2;
    return 3;
  };

  const getTotalSlides = () => {
    const perView = getCardsPerView();

    return Math.max(
      1,
      Math.ceil(cards.length / perView)
    );
  };

  const renderPartners = () => {
    const perView = getCardsPerView();
    const totalSlides = getTotalSlides();

    if (current >= totalSlides) {
      current = totalSlides - 1;
    }

    if (current < 0) {
      current = 0;
    }

    const start = current * perView;
    const end = Math.min(
      start + perView,
      cards.length
    );

    cards.forEach((card, i) => {
      card.classList.toggle(
        "is-hidden",
        i < start || i >= end
      );
    });

    if (dotsWrap) {
      Array.from(
        dotsWrap.children
      ).forEach((dot, i) => {
        dot.classList.toggle(
          "is-active",
          i === current
        );
      });
    }

    if (prevBtn) {
      prevBtn.disabled =
        totalSlides <= 1;
    }

    if (nextBtn) {
      nextBtn.disabled =
        totalSlides <= 1;
    }
  };

  const goToPartnerSlide = (index) => {
    const totalSlides =
      getTotalSlides();

    current =
      ((index % totalSlides) +
        totalSlides) %
      totalSlides;

    renderPartners();
  };

  let lastDotCount = -1;

  const buildPartnerDots = () => {
    if (!dotsWrap) return;

    const totalSlides =
      getTotalSlides();

    if (totalSlides === lastDotCount) {
      return;
    }

    lastDotCount = totalSlides;

    dotsWrap.innerHTML = "";

    for (
      let i = 0;
      i < totalSlides;
      i++
    ) {
      const dot =
        document.createElement("button");

      dot.type = "button";

      dot.setAttribute(
        "aria-label",
        `Go to partner slide ${i + 1}`
      );

      dot.addEventListener(
        "click",
        () => {
          goToPartnerSlide(i);
        }
      );

      dotsWrap.appendChild(dot);
    }
  };

  current = 0;

  buildPartnerDots();
  renderPartners();

  if (prevBtn) {
    prevBtn.addEventListener(
      "click",
      () => {
        goToPartnerSlide(
          current - 1
        );
      }
    );
  }

  if (nextBtn) {
    nextBtn.addEventListener(
      "click",
      () => {
        goToPartnerSlide(
          current + 1
        );
      }
    );
  }

  let touchStartX = 0;

  track.addEventListener(
    "touchstart",
    (e) => {
      touchStartX =
        e.touches[0].clientX;
    },
    {
      passive: true,
    }
  );

  track.addEventListener(
    "touchend",
    (e) => {
      const delta =
        e.changedTouches[0].clientX -
        touchStartX;

      if (Math.abs(delta) < 40) {
        return;
      }

      goToPartnerSlide(
        delta < 0
          ? current + 1
          : current - 1
      );
    },
    {
      passive: true,
    }
  );

  let resizeTimer;

  window.addEventListener(
    "resize",
    () => {
      clearTimeout(resizeTimer);

      resizeTimer = setTimeout(() => {
        buildPartnerDots();
        renderPartners();
      }, 150);
    }
  );
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
    var modals = document.querySelectorAll(".team-modal");
    if (!modals.length) return;

    var lastFocused = null;
    var openModalEl = null;

    function openModal(modal, triggerEl) {
      if (!modal || openModalEl) return;
      lastFocused = triggerEl instanceof HTMLElement ? triggerEl : null;
      modal.classList.add("is-open");
      modal.setAttribute("aria-hidden", "false");
      document.documentElement.classList.add("team-modal-open");
      document.documentElement.style.overflow = "hidden"; // just locks scrolling, doesn't move the page
      openModalEl = modal;
      var firstField = modal.querySelector("input, textarea, select");
      if (firstField) {
        setTimeout(function () {
          firstField.focus({ preventScroll: true });
        }, 60);
      }
    }

    function closeModal() {
      if (!openModalEl) return;
      openModalEl.classList.remove("is-open");
      openModalEl.setAttribute("aria-hidden", "true");
      document.documentElement.classList.remove("team-modal-open");
      document.documentElement.style.overflow = ""; // unlocks scrolling — page stays exactly where it was
      openModalEl = null;

      if (
        lastFocused &&
        lastFocused !== document.body &&
        typeof lastFocused.focus === "function"
      ) {
        lastFocused.focus({ preventScroll: true });
      }
    }

    document.addEventListener("click", function (e) {
      var trigger = e.target.closest(".js-team-gf-trigger");
      if (!trigger) return;
      e.preventDefault();
      var targetId = trigger.getAttribute("data-gf-modal");
      var modal = targetId ? document.getElementById(targetId) : null;
      openModal(modal, trigger);
    });

    modals.forEach(function (modal) {
      modal.querySelectorAll(".js-team-modal-close").forEach(function (el) {
        el.addEventListener("click", closeModal);
      });
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && openModalEl) {
        closeModal();
      }
    });
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

jQuery(document).ready(function ($) {
  $(".cg-project-overview").each(function () {
    var $section = $(this);
    var $slider = $section.find(
      ".cg-project-overview__gallery .swiper-wrapper",
    );

    $slider.slick({
      slidesToShow: 1,
      slidesToScroll: 1,

      centerMode: true,
      centerPadding: "12.5%",

      infinite: true,

      autoplay: true,
      autoplaySpeed: 4500,

      speed: 700,

      arrows: true,
      dots: true,

      adaptiveHeight: false,

      prevArrow: $section.find(".swiper-button-prev"),
      nextArrow: $section.find(".swiper-button-next"),

      appendDots: $section.find(".swiper-pagination"),
    });
  });
});
