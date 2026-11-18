(function () {
  function setHeaderHeightVar() {
    var header = document.querySelector(".site-header");
    if (!header) return;
    document.documentElement.style.setProperty(
      "--header-height",
      header.offsetHeight + "px",
    );
  }

  document.addEventListener("DOMContentLoaded", setHeaderHeightVar);

  var resizeTimer;
  window.addEventListener("resize", function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(setHeaderHeightVar, 150);
  });

  if (window.ResizeObserver) {
    var header = document.querySelector(".site-header");
    if (header) new ResizeObserver(setHeaderHeightVar).observe(header);
  }
})();
document.addEventListener("DOMContentLoaded", function () {
  const open = document.querySelector(".mobile-toggle");
  const close = document.querySelector(".mobile-close");
  const menu = document.querySelector(".mobile-menu");

  open.addEventListener("click", function () {
    menu.classList.add("active");
    document.body.style.overflow = "hidden";
  });

  close.addEventListener("click", function () {
    menu.classList.remove("active");
    document.body.style.overflow = "";
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const counters = document.querySelectorAll(".counter");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        const counter = entry.target;

        const target = Number(counter.dataset.target);

        if (isNaN(target)) {
          console.error("Invalid counter value:", counter.dataset.target);
          return;
        }

        const prefix = counter.dataset.prefix || "";
        const suffix = counter.dataset.suffix || "";

        let current = 0;
        const duration = 2000;
        const stepTime = 16;
        const increment = target / (duration / stepTime);

        function updateCounter() {
          current += increment;

          if (current >= target) {
            counter.textContent = prefix + target.toLocaleString() + suffix;
            return;
          }

          counter.textContent =
            prefix + Math.floor(current).toLocaleString() + suffix;

          requestAnimationFrame(updateCounter);
        }

        updateCounter();

        observer.unobserve(counter);
      });
    },
    {
      threshold: 0.5,
    },
  );

  counters.forEach((counter) => observer.observe(counter));
});
document.addEventListener("DOMContentLoaded", function () {
  if (
    document.querySelector(".testimonials-slider") &&
    typeof Swiper !== "undefined"
  ) {
    new Swiper(".testimonials-slider", {
      slidesPerView: 1,
      spaceBetween: 0,
      speed: 500,
      navigation: {
        nextEl: ".testimonials-slider .swiper-button-next",
        prevEl: ".testimonials-slider .swiper-button-prev",
      },
      pagination: {
        el: ".testimonials-slider .swiper-pagination",
        clickable: true,
        type: "bullets", // was 'progressbar' — image shows segmented bars, not a single line
      },
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  var toggles = document.querySelectorAll(".footer-col-toggle");

  toggles.forEach(function (btn) {
    btn.addEventListener("click", function () {
      // The accordion only collapses on mobile — on desktop the CSS
      // keeps .footer-col-content fully open regardless of this
      // class, so clicking there (if it ever fires) is a no-op.
      if (window.innerWidth > 767) return;

      var content = btn.nextElementSibling;
      var isOpen = btn.getAttribute("aria-expanded") === "true";

      btn.setAttribute("aria-expanded", String(!isOpen));
      if (content) content.classList.toggle("is-open", !isOpen);
    });
  });
});


/* =========================================
   GLOBAL CTA ARROW HOVER
   Automatically handles CTA SVG arrows
   ========================================= */

document.addEventListener('DOMContentLoaded', function () {

    const ctaButtons = document.querySelectorAll(
        '.hero-btn, .cta-btn, .bottom-cta-btn, .faq-btn, .service-view-btn'
    );

    ctaButtons.forEach(function (button) {

        const svg = button.querySelector('svg');

        if (!svg) {
            return;
        }

        svg.classList.add('global-cta-arrow');

    });

});