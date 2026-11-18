/* =========================================================
   blog.js — Behaviour for the Blog section
   =========================================================
   1. Blog category filter tabs  (.cg-blog-tab + [data-blog-categories])
   2. Single Blog image carousel (.cg-carousel[data-carousel])
        • prev/next arrow clicks
        • dot pagination clicks
        • keyboard (←/→ arrows) when carousel has focus
        • prefers-reduced-motion: snap instantly, no crossfade
        • no autoplay (intentionally; accessibility + user controls)
   ========================================================= */

(function () {
  'use strict';

  const REDUCED_MOTION = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;


  /* ---------------------------------------------------------
     A.  Blog listing — category filter tabs
     Shows the 4 seeded tabs + "All Articles" (slug = 'all').
     Each card carries data-blog-categories="slug1,slug2"
     (or "all" when it has no category terms assigned).
     --------------------------------------------------------- */
  function initBlogTabs() {
    const tabsWrap = document.querySelector('.cg-blog-tabs');
    if (!tabsWrap) return;

    const tabs = Array.from(tabsWrap.querySelectorAll('.cg-blog-tab[data-blog-category]'));
    if (!tabs.length) return;

    const grid = document.getElementById('cg-blog-grid');
    if (!grid) return;
    const cards = Array.from(grid.querySelectorAll('.cg-blog-card[data-blog-categories]'));
    if (!cards.length) return;

    function applyFilter(slug) {
      const showAll = (slug === 'all');

      cards.forEach(card => {
        const raw = (card.getAttribute('data-blog-categories') || '').trim();
        const list = raw ? raw.split(',').map(s => s.trim()).filter(Boolean) : ['all'];

        const matches = showAll
          ? true
          : list.indexOf(slug) !== -1;

        if (matches) {
          card.classList.remove('is-hidden');
          card.style.display = '';
        } else {
          card.classList.add('is-hidden');
          card.style.display = 'none';
        }
      });

      tabs.forEach(tab => {
        const tabSlug = tab.getAttribute('data-blog-category');
        const active = (tabSlug === slug);
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-pressed', active ? 'true' : 'false');
      });

      // Scroll smoothly to grid so user sees the filtered result
      try {
        grid.scrollIntoView({ behavior: REDUCED_MOTION ? 'auto' : 'smooth', block: 'start' });
      } catch (_) { /* noop */ }
    }

    tabs.forEach(tab => {
      tab.addEventListener('click', function () {
        const slug = tab.getAttribute('data-blog-category') || 'all';
        applyFilter(slug);
      });
    });

    // Support ?blog-category=slug query arg so links can pre-select a tab
    try {
      const usp = new URL(window.location.href);
      const q = usp.searchParams.get('blog-category');
      if (q && q.length && tabs.some(t => t.getAttribute('data-blog-category') === q)) {
        applyFilter(q);
      }
    } catch (_) { /* noop */ }
  }


  /* ---------------------------------------------------------
     B.  Single Blog — Image Carousel
     --------------------------------------------------------- */
  function initCarousel(carousel) {
    if (!carousel) return;
    if (carousel.dataset.carouselInit === '1') return;
    carousel.dataset.carouselInit = '1';

    const total  = parseInt(carousel.getAttribute('data-slide-count') || '0', 10);
    const track  = carousel.querySelector('.cg-carousel__track');
    const prev   = carousel.querySelector('.cg-carousel__arrow--prev');
    const next   = carousel.querySelector('.cg-carousel__arrow--next');
    const dots   = carousel.querySelector('.cg-carousel__dots');
    const slides = track ? Array.from(track.querySelectorAll('.cg-carousel__slide')) : [];

    if (!track || total === 0 || slides.length === 0) return;

    let current = 0;

    function goTo(index) {
      if (REDUCED_MOTION) {
        // snap directly, no transition
        slides.forEach(s => { s.style.transition = 'none'; });
      }
      if (index < 0)        index = slides.length - 1;
      if (index >= slides.length) index = 0;
      current = index;

      carousel.setAttribute('data-current', String(current));

      // Slide active states, plus the prev/next "peek" neighbours
      // (used by blog.css to show the partial side images either
      // side of the active slide — see .cg-carousel__slide.is-prev
      // / .is-next).
      const prevIndex = (current - 1 + slides.length) % slides.length;
      const nextIndex = (current + 1) % slides.length;

      slides.forEach((s, i) => {
        s.classList.toggle('is-active', i === current);
        s.classList.toggle('is-prev', i === prevIndex && i !== current);
        s.classList.toggle('is-next', i === nextIndex && i !== current);
      });

      // Dots active states
      if (dots) {
        const dotBtns = Array.from(dots.querySelectorAll('.cg-carousel__dot'));
        dotBtns.forEach((d, i) => {
          const isOn = (i === current);
          d.classList.toggle('is-active', isOn);
          d.setAttribute('aria-current', isOn ? 'true' : 'false');
        });
      }

      // Restore transitions after a frame (for reduced-motion users only)
      if (REDUCED_MOTION) {
        requestAnimationFrame(() => {
          slides.forEach(s => { s.style.transition = ''; });
        });
      }
    }

    // Hook arrows
    if (prev) prev.addEventListener('click', () => goTo(current - 1));
    if (next) next.addEventListener('click', () => goTo(current + 1));

    // Hook dots
    if (dots) {
      dots.addEventListener('click', (e) => {
        const target = e.target;
        if (!(target instanceof HTMLElement)) return;
        const dot = target.closest('.cg-carousel__dot');
        if (!dot) return;
        const idx = parseInt(dot.getAttribute('data-index') || '0', 10);
        goTo(idx);
      });
    }

    // Keyboard navigation (Left/Right arrows when carousel focused)
    carousel.setAttribute('tabindex', '0');
    carousel.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft')  { e.preventDefault(); goTo(current - 1); }
      if (e.key === 'ArrowRight') { e.preventDefault(); goTo(current + 1); }
    });

    // Show first slide on init
    goTo(0);
  }


  /* ------ Boot ------ */
  function boot() {
    initBlogTabs();
    document.querySelectorAll('.cg-carousel[data-carousel]').forEach(initCarousel);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();


jQuery(function ($) {

    function blogTabsSlider() {

        var $tabs = $('.cg-blog-tabs');

        if (!$tabs.length) {
            return;
        }

        if ($(window).width() <= 641) {

            if (!$tabs.hasClass('slick-initialized')) {

                $tabs.slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: false,
                    arrows: false,
                    dots: false,
                    swipe: true,
                    draggable: true,
                    touchMove: true,
                    variableWidth: false,
                    centerMode: false,
                    adaptiveHeight: false
                });

            }

        } else {

            if ($tabs.hasClass('slick-initialized')) {
                $tabs.slick('unslick');
            }

        }
    }

    blogTabsSlider();

    var resizeTimer;

    $(window).on('resize', function () {
        clearTimeout(resizeTimer);

        resizeTimer = setTimeout(function () {
            blogTabsSlider();
        }, 150);
    });

});

(function () {

    function initBlogCardTags() {

        var wrappers = document.querySelectorAll(
            '.cg-blog-card__tags'
        );

        if (!wrappers.length) {
            return;
        }

        wrappers.forEach(function (wrapper) {

            /* -----------------------------------------
               Get all tags
            ----------------------------------------- */

            var tags = Array.from(
                wrapper.querySelectorAll('.cg-blog-card__tag')
            );

            if (!tags.length) {
                return;
            }


            /* -----------------------------------------
               Find existing +N button
            ----------------------------------------- */

            var moreButton = wrapper.querySelector(
                '.cg-tag-more'
            );


            /* -----------------------------------------
               Show first 2 tags
               Hide remaining tags
            ----------------------------------------- */

            tags.forEach(function (tag, index) {

                if (index < 2) {

                    tag.classList.remove(
                        'cg-tag-hidden'
                    );

                } else {

                    tag.classList.add(
                        'cg-tag-hidden'
                    );

                }

            });


            /* -----------------------------------------
               Number of hidden tags
            ----------------------------------------- */

            var remaining = Math.max(
                tags.length - 2,
                0
            );


            /* -----------------------------------------
               No hidden tags
            ----------------------------------------- */

            if (remaining === 0) {

                if (moreButton) {
                    moreButton.remove();
                }

                wrapper.classList.remove(
                    'cg-tags-open'
                );

                return;
            }


            /* -----------------------------------------
               Create +N button if missing
            ----------------------------------------- */

            if (!moreButton) {

                moreButton = document.createElement(
                    'button'
                );

                moreButton.type = 'button';

                moreButton.className =
                    'cg-tag-more';

                wrapper.appendChild(
                    moreButton
                );
            }


            /* -----------------------------------------
               Prevent duplicate event listener
            ----------------------------------------- */

            if (
                moreButton.dataset.tagButtonBound ===
                'true'
            ) {
                return;
            }


            /* -----------------------------------------
               Initial button state
            ----------------------------------------- */

            moreButton.textContent =
                '+' + remaining;

            moreButton.setAttribute(
                'aria-label',
                'Show ' + remaining + ' more tags'
            );


            /* -----------------------------------------
               Click +N / −
            ----------------------------------------- */

            moreButton.addEventListener(
                'click',
                function (event) {

                    event.preventDefault();
                    event.stopPropagation();

                    var isOpen =
                        wrapper.classList.toggle(
                            'cg-tags-open'
                        );

                    var hiddenTags =
                        wrapper.querySelector(
                            '.cg-blog-card__hidden-tags'
                        );


                    /* ===============================
                       OPEN
                    =============================== */

                    if (isOpen) {

                        /*
                         * Remove HTML hidden attribute
                         */
                        if (hiddenTags) {

                            hiddenTags.removeAttribute(
                                'hidden'
                            );

                        }


                        /*
                         * Show all hidden tags
                         */
                        tags.forEach(function (tag) {

                            tag.classList.remove(
                                'cg-tag-hidden'
                            );

                        });


                        /*
                         * IMPORTANT:
                         * Move − button to the very
                         * end of the tags wrapper.
                         */
                        wrapper.appendChild(
                            moreButton
                        );


                        /*
                         * Change button to −
                         */
                        moreButton.textContent =
                            '−';

                        moreButton.setAttribute(
                            'aria-label',
                            'Hide additional tags'
                        );

                    }


                    /* ===============================
                       CLOSE
                    =============================== */

                    else {

                        /*
                         * Hide extra tags again
                         */
                        if (hiddenTags) {

                            hiddenTags.setAttribute(
                                'hidden',
                                ''
                            );

                        }


                        /*
                         * First 2 visible,
                         * remaining hidden
                         */
                        tags.forEach(
                            function (tag, index) {

                                if (index < 2) {

                                    tag.classList.remove(
                                        'cg-tag-hidden'
                                    );

                                } else {

                                    tag.classList.add(
                                        'cg-tag-hidden'
                                    );

                                }

                            }
                        );


                        /*
                         * Put +N immediately after
                         * the second visible tag.
                         */
                        var visibleTags =
                            wrapper.querySelectorAll(
                                ':scope > .cg-blog-card__tag:not(.cg-tag-hidden)'
                            );


                        if (visibleTags.length >= 2) {

                            visibleTags[
                                visibleTags.length - 1
                            ].after(
                                moreButton
                            );

                        } else {

                            wrapper.appendChild(
                                moreButton
                            );

                        }


                        /*
                         * Change button back to +N
                         */
                        moreButton.textContent =
                            '+' + remaining;

                        moreButton.setAttribute(
                            'aria-label',
                            'Show ' +
                            remaining +
                            ' more tags'
                        );

                    }

                }
            );


            /*
             * Mark button as initialized
             */
            moreButton.dataset.tagButtonBound =
                'true';

        });

    }


    /* =============================================
       INITIAL LOAD
    ============================================= */

    if (
        document.readyState ===
        'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initBlogCardTags
        );

    } else {

        initBlogCardTags();

    }


    /* =============================================
       DYNAMIC CONTENT SUPPORT
       ============================================= */

    var observer =
        new MutationObserver(
            function () {

                initBlogCardTags();

            }
        );


    observer.observe(
        document.body,
        {
            childList: true,
            subtree: true
        }
    );

})();