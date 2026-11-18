(() => {
  'use strict';

  /* =========================================================
     FIRST: Confirm JS is alive.
     <html> starts with class="no-reveals" added via WordPress
     `language_attributes` filter so main.css can force:
       html.no-reveals .reveal { opacity: 1 !important; transform: none !important; }
     and keep content visible if this script never runs.
     If we get here -> JS ran OK -> strip the flag so dev2custom.css
     opacity:0 base state for .reveal applies and animations play.
  ========================================================= */
  const rootEl = document.documentElement;
  if (rootEl && rootEl.classList) {
    rootEl.classList.remove('no-reveals');
    rootEl.classList.add('reveals-enabled');
  }

  /* =========================================================
     HEADER
  ========================================================= */

  const header = document.getElementById('siteHeader');
  const navToggle = document.getElementById('navToggle');
  const primaryNav = document.getElementById('primaryNav');

  if (header) {
    const onScroll = () => {
      header.classList.toggle('is-scrolled', window.scrollY > 40);
    };

    onScroll();

    window.addEventListener('scroll', onScroll, {
      passive: true
    });
  }

  if (navToggle && primaryNav) {

    navToggle.addEventListener('click', () => {

      const expanded =
        navToggle.getAttribute('aria-expanded') === 'true';

      navToggle.setAttribute(
        'aria-expanded',
        String(!expanded)
      );

      primaryNav.classList.toggle(
        'is-open',
        !expanded
      );
    });

    primaryNav.querySelectorAll('a').forEach((link) => {

      link.addEventListener('click', () => {

        navToggle.setAttribute(
          'aria-expanded',
          'false'
        );

        primaryNav.classList.remove('is-open');

      });

    });
  }


  /* =========================================================
     SCROLL REVEAL
  ========================================================= */

  const revealEls =
    document.querySelectorAll('[data-reveal]');

  revealEls.forEach((el) => {

    const delay =
      el.getAttribute('data-reveal-delay');

    if (delay) {

      el.style.setProperty(
        '--reveal-delay',
        `${delay}ms`
      );

    }

  });


  if ('IntersectionObserver' in window) {

    const revealObserver =
      new IntersectionObserver(
        (entries, obs) => {

          entries.forEach((entry) => {

            if (entry.isIntersecting) {

              entry.target.classList.add(
                'is-visible'
              );

              obs.unobserve(entry.target);

            }

          });

        },
        {
          threshold: 0.15,
          rootMargin: '0px 0px -60px 0px'
        }
      );

    revealEls.forEach((el) => {

      revealObserver.observe(el);

    });

  } else {

    revealEls.forEach((el) => {

      el.classList.add('is-visible');

    });

  }


  /* =========================================================
     COUNTERS
  ========================================================= */

  const counters =
    document.querySelectorAll('[data-counter]');

  const easeOutQuad = (t) => {

    return t * (2 - t);

  };


  const animateCounter = (el) => {

    const target =
      parseFloat(
        el.getAttribute('data-target'),
        10
      ) || 0;

    const duration = 1600;

    const start = performance.now();


    const step = (now) => {

      const progress =
        Math.min(
          (now - start) / duration,
          1
        );

      const eased =
        easeOutQuad(progress);

      const value =
        Math.round(target * eased);

      el.textContent =
        value.toLocaleString();


      if (progress < 1) {

        requestAnimationFrame(step);

      } else {

        el.textContent =
          target.toLocaleString();

      }

    };


    requestAnimationFrame(step);

  };


  if ('IntersectionObserver' in window) {

    const counterObserver =
      new IntersectionObserver(
        (entries, obs) => {

          entries.forEach((entry) => {

            if (entry.isIntersecting) {

              animateCounter(entry.target);

              obs.unobserve(entry.target);

            }

          });

        },
        {
          threshold: 0.6
        }
      );


    counters.forEach((el) => {

      counterObserver.observe(el);

    });

  } else {

    counters.forEach(animateCounter);

  }


  /* =========================================================
     TEAM TABS
  ========================================================= */

  const tabButtons =
    document.querySelectorAll(
      '.team-tabs__btn'
    );

  const panels =
    document.querySelectorAll(
      '.team-panel'
    );


  tabButtons.forEach((btn) => {

    btn.addEventListener('click', () => {

      const target =
        btn.getAttribute('data-tab');


      tabButtons.forEach((b) => {

        const active = b === btn;

        b.classList.toggle(
          'is-active',
          active
        );

        b.setAttribute(
          'aria-selected',
          String(active)
        );

        b.tabIndex =
          active ? 0 : -1;

      });


      panels.forEach((panel) => {

        const match =
          panel.getAttribute(
            'data-panel'
          ) === target;

        panel.classList.toggle(
          'is-active',
          match
        );

        panel.hidden = !match;

      });


      const activePanel =
        document.querySelector(
          `.team-panel[data-panel="${target}"]`
        );


      if (activePanel) {

        activePanel
          .querySelectorAll('[data-reveal]')
          .forEach((el) => {

            el.classList.add(
              'is-visible'
            );

          });

      }

    });


    btn.addEventListener('keydown', (e) => {

      const list =
        Array.from(tabButtons);

      const idx =
        list.indexOf(btn);

      let nextIdx = null;


      if (e.key === 'ArrowRight') {

        nextIdx =
          (idx + 1) % list.length;

      }


      if (e.key === 'ArrowLeft') {

        nextIdx =
          (idx - 1 + list.length) %
          list.length;

      }


      if (nextIdx !== null) {

        e.preventDefault();

        list[nextIdx].focus();

        list[nextIdx].click();

      }

    });

  });


  /* =========================================================
     FAQ ACCORDION
  ========================================================= */

  const accordionItems =
    document.querySelectorAll(
      '.accordion__item'
    );


  accordionItems.forEach((item) => {

    const trigger =
      item.querySelector(
        '.accordion__trigger'
      );


    if (!trigger) {
      return;
    }


    trigger.addEventListener(
      'click',
      () => {

        const isOpen =
          item.classList.contains(
            'is-open'
          );


        accordionItems.forEach(
          (other) => {

            other.classList.remove(
              'is-open'
            );


            const otherTrigger =
              other.querySelector(
                '.accordion__trigger'
              );


            if (otherTrigger) {

              otherTrigger.setAttribute(
                'aria-expanded',
                'false'
              );

            }

          }
        );


        if (!isOpen) {

          item.classList.add(
            'is-open'
          );

          trigger.setAttribute(
            'aria-expanded',
            'true'
          );

        }

      }
    );

  });


  /* =========================================================
     STRATEGIC DELIVERY PARTNERS
  ========================================================= */

  const track =
    document.getElementById(
      'partnersTrack'
    );

  const cards =
    track
      ? Array.from(
          track.querySelectorAll(
            '.partner-card'
          )
        )
      : [];


  const dotsWrap =
    document.getElementById(
      'partnersDots'
    );

  const prevBtn =
    document.getElementById(
      'partnersPrev'
    );

  const nextBtn =
    document.getElementById(
      'partnersNext'
    );


  let current = 0;


  const render = () => {

    cards.forEach((card, i) => {

      card.classList.toggle(
        'is-active',
        i === current
      );

    });


    if (dotsWrap) {

      Array.from(
        dotsWrap.children
      ).forEach((dot, i) => {

        dot.classList.toggle(
          'is-active',
          i === current
        );

      });

    }

  };


  const goTo = (index) => {

    if (!cards.length) {
      return;
    }

    current =
      (index + cards.length) %
      cards.length;

    render();

  };


  const buildDots = () => {

    if (!dotsWrap) {
      return;
    }


    cards.forEach((_, i) => {

      const dot =
        document.createElement(
          'button'
        );

      dot.type = 'button';

      dot.setAttribute(
        'aria-label',
        `Go to partner ${i + 1}`
      );


      dot.addEventListener(
        'click',
        () => goTo(i)
      );


      dotsWrap.appendChild(dot);

    });

  };


  if (cards.length) {

    buildDots();

    render();


    if (prevBtn) {

      prevBtn.addEventListener(
        'click',
        () => goTo(current - 1)
      );

    }


    if (nextBtn) {

      nextBtn.addEventListener(
        'click',
        () => goTo(current + 1)
      );

    }


    if (track) {

      let touchStartX = 0;


      track.addEventListener(
        'touchstart',
        (e) => {

          touchStartX =
            e.touches[0].clientX;

        },
        {
          passive: true
        }
      );


      track.addEventListener(
        'touchend',
        (e) => {

          const delta =
            e.changedTouches[0].clientX -
            touchStartX;


          if (Math.abs(delta) > 40) {

            goTo(
              delta > 0
                ? current - 1
                : current + 1
            );

          }

        },
        {
          passive: true
        }
      );

    }

  }


  /* =========================================================
     ACTIVE PROJECT FILTERS
  ========================================================= */

  const filterButtons =
    document.querySelectorAll(
      '.project-filters__btn'
    );


  filterButtons.forEach((btn) => {

    btn.addEventListener(
      'click',
      () => {

        filterButtons.forEach((b) => {

          b.classList.remove(
            'is-active'
          );

        });


        btn.classList.add(
          'is-active'
        );

      }
    );

  });


})();