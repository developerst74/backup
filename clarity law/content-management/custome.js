// window.addEventListener("load", function () {
//   function setup() {
//     document.querySelectorAll(".autoplay-slider").forEach(function (box) {
//       var el = box.querySelector(".swiper") || box.querySelector(".swiper-container");
//       if (!el || !el.swiper) return;

//       var sw = el.swiper;
//       var reverse = box.classList.contains("reverse");

//       sw.params.loop = true;
//       sw.params.speed = 1000;              // jetlu vadhu, etlu dhimu
//       sw.params.allowTouchMove = false;
//       sw.params.autoplay = {
//         delay: 0,
//         disableOnInteraction: false,
//         pauseOnMouseEnter: false,
//         reverseDirection: reverse
//       };

//       sw.autoplay.stop();
//       sw.autoplay.start();
//     });
//   }
//   setTimeout(setup, 1500);
//   setTimeout(setup, 3500); // backup, jo pehli vaar na chale to
// });


jQuery(document).ready(function ($) {
 
    // Hide the guide content by default

    $('.content-box-parts').hide();
 
    // Hide the Close button by default

    $('.close-the-guide').hide();
 
    // Read the full guide button

    $('.read-full-guide').on('click', function (e) {

        e.preventDefault();
 
        $('.content-box-parts').slideDown(400);
 
        $('.read-full-guide').hide();

        $('.close-the-guide').show();

    });
 
    // Close the guide button

    $('.close-the-guide').on('click', function (e) {

        e.preventDefault();
 
        $('.content-box-parts').slideUp(400);
 
        $('.close-the-guide').hide();

        $('.read-full-guide').show();

    });
 
});

(function () {
  var tries = 0;
  var timer = setInterval(function () {
    tries++;
    var boxes = document.querySelectorAll(".autoplay-slider");
    var ready = boxes.length > 0;
    boxes.forEach(function (box) {
      var el = box.querySelector(".swiper");
      if (!el || !el.swiper) ready = false;
    });

    if (ready) {
      clearInterval(timer);
      boxes.forEach(function (box) {
        var el = box.querySelector(".swiper");
        el.swiper.destroy(true, true);
        var wrap = el.querySelector(".swiper-wrapper");
        var items = Array.from(wrap.children);
        items.forEach(function (n) {
          wrap.appendChild(n.cloneNode(true));
        });
        wrap.classList.add("marquee-on");
        box.classList.add("marquee-ready");
      });
    } else if (tries > 60) {
      clearInterval(timer);
      // jo swiper na male to pan carousel dekhay e mate
      document.querySelectorAll(".autoplay-slider").forEach(function (b) {
        b.classList.add("marquee-ready");
      });
    }
  }, 200);
})();



/* =========================================
   GUIDE TOGGLE FUNCTIONALITY
========================================= */

document.addEventListener('click', function(event) {

    const buttonWidget = event.target.closest('.guide-toggle-btn');

    if (!buttonWidget) return;

    event.preventDefault();

    const section = buttonWidget.closest('.guide-section');
    if (!section) return;

    const content = section.querySelector('.guide-content');
    if (!content) return;

    section.classList.toggle('is-open');

    const isOpen = section.classList.contains('is-open');

    content.style.display = isOpen ? 'flex' : 'none';

    const buttonText = buttonWidget.querySelector('.elementor-button-text');

    if (buttonText) {
        buttonText.textContent = isOpen
            ? 'Close the guide ↑'
            : 'Read the full guide ↓';
    }

}, true);





/* =========================================
   CLARITY LAW - GUIDE TOGGLE FUNCTIONALITY
========================================= */

document.addEventListener('click', function(event) {

    const buttonWidget = event.target.closest('.guide-toggle-btn');

    if (!buttonWidget) return;

    event.preventDefault();

    const section = buttonWidget.closest('.guide-section');
    if (!section) return;

    const content = section.querySelector('.guide-content');
    if (!content) return;

    section.classList.toggle('is-open');

    const isOpen = section.classList.contains('is-open');

    content.style.display = isOpen ? 'flex' : 'none';

    const buttonText = buttonWidget.querySelector('.elementor-button-text');

    if (buttonText) {
        buttonText.textContent = isOpen
            ? 'Close the guide ↑'
            : 'Read the full guide ↓';
    }

}, true);


/* =========================================
   CLARITY LAW - OFFICE MAP FUNCTIONALITY
========================================= */

(function () {

    'use strict';

    /* -------------------------------------
       EXTRACT COORDINATES FROM GOOGLE URL
    ------------------------------------- */

    function getCoordinates(url) {

        // Google Maps embed: !2dLONGITUDE!3dLATITUDE
        let match = url.match(
            /!2d(-?\d+(?:\.\d+)?)!3d(-?\d+(?:\.\d+)?)/
        );

        if (match) {
            return {
                lat: parseFloat(match[2]),
                lng: parseFloat(match[1])
            };
        }

        // Alternative format: !3dLATITUDE!2dLONGITUDE
        match = url.match(
            /!3d(-?\d+(?:\.\d+)?)!2d(-?\d+(?:\.\d+)?)/
        );

        if (match) {
            return {
                lat: parseFloat(match[1]),
                lng: parseFloat(match[2])
            };
        }

        // Fallback: @LATITUDE,LONGITUDE
        match = url.match(
            /@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/
        );

        if (match) {
            return {
                lat: parseFloat(match[1]),
                lng: parseFloat(match[2])
            };
        }

        return null;
    }


    /* -------------------------------------
       INITIALISE ALL OFFICE MAPS
    ------------------------------------- */

    function initClarityOfficeMaps() {

        if (typeof L === 'undefined') return;

        const officeMaps = document.querySelectorAll(
            '.claritylaw-office-map'
        );

        officeMaps.forEach(function(wrapper) {

            if (wrapper.dataset.initialized === 'true') return;

            const iframe = wrapper.querySelector('iframe');

            if (!iframe) return;

            const coordinates = getCoordinates(
                iframe.getAttribute('src') || ''
            );

            if (!coordinates) {

                console.warn(
                    'Clarity Law Map: Unable to extract coordinates from Google Maps embed URL.',
                    iframe.src
                );

                return;
            }

            const lat = coordinates.lat;
            const lng = coordinates.lng;

            if (
                !Number.isFinite(lat) ||
                !Number.isFinite(lng) ||
                Math.abs(lat) > 90 ||
                Math.abs(lng) > 180
            ) {
                console.warn('Clarity Law Map: Invalid coordinates.');
                return;
            }

            // Prevent duplicate initialisation
            wrapper.dataset.initialized = 'true';

            /* ---------------------------------
               MAP SETTINGS FROM HTML
            --------------------------------- */

            const label =
                wrapper.dataset.label || 'Clarity Law';

            const zoom =
                parseInt(wrapper.dataset.zoom, 10) || 16;


            /* ---------------------------------
               CREATE MAP CONTAINER
            --------------------------------- */

            const mapElement = document.createElement('div');

            mapElement.className = 'claritylaw-map-render';

            mapElement.id =
                'claritylaw-map-' +
                Math.random().toString(36).substring(2, 9);

            wrapper.appendChild(mapElement);


            /* ---------------------------------
               HIDE ORIGINAL GOOGLE MAP IFRAME
            --------------------------------- */

            iframe.style.display = 'none';


            /* ---------------------------------
               INITIALISE LEAFLET MAP
            --------------------------------- */

            const map = L.map(mapElement, {

                scrollWheelZoom: false,

                zoomControl: true,

                attributionControl: true

            }).setView([lat, lng], zoom);


            /* ---------------------------------
               ESRI STREET MAP TILES
            --------------------------------- */

            const tileLayer = L.tileLayer(

                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',

                {
                    attribution: 'Tiles &copy; Esri',

                    maxZoom: 19
                }

            ).addTo(map);


            /* ---------------------------------
               CUSTOM ORANGE MARKER
            --------------------------------- */

            const markerIcon = L.divIcon({

                className: '',

                html: '<div class="clarity-map-marker"></div>',

                iconSize: [18, 18],

                iconAnchor: [9, 9]

            });


            /* ---------------------------------
               ADD MARKER AND OFFICE LABEL
            --------------------------------- */

            L.marker([lat, lng], {

                icon: markerIcon

            })

            .addTo(map)

            .bindTooltip(label, {

                permanent: true,

                direction: 'top',

                offset: [0, -8],

                className: 'clarity-map-label'

            })

            .openTooltip();


            /* ---------------------------------
               REFRESH MAP DIMENSIONS
            --------------------------------- */

            function refreshMap() {

                map.invalidateSize({

                    animate: false,

                    pan: false

                });

            }

            // Refresh after Elementor renders
            setTimeout(refreshMap, 300);

            setTimeout(refreshMap, 1000);

            setTimeout(refreshMap, 2000);


            /* ---------------------------------
               OBSERVE CONTAINER RESIZING
            --------------------------------- */

            if (typeof ResizeObserver !== 'undefined') {

                const resizeObserver = new ResizeObserver(function() {

                    refreshMap();

                });

                resizeObserver.observe(mapElement);

            }


            /* ---------------------------------
               TILE LOADING ERROR DETECTION
            --------------------------------- */

            tileLayer.on('tileerror', function(event) {

                console.warn(
                    'Clarity Law Map: Map tile failed to load.',
                    event
                );

            });

        });

    }


    /* =====================================
       LOAD LEAFLET LIBRARY
    ===================================== */

    function loadClarityLeaflet() {

        // Leaflet already loaded
        if (typeof L !== 'undefined') {

            initClarityOfficeMaps();

            return;
        }


        // Load Leaflet CSS once
        if (!document.querySelector('[data-clarity-leaflet-css]')) {

            const css = document.createElement('link');

            css.rel = 'stylesheet';

            css.href =
                'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';

            css.setAttribute(
                'data-clarity-leaflet-css',
                'true'
            );

            document.head.appendChild(css);

        }


        // Load Leaflet JavaScript once
        if (!document.querySelector('[data-clarity-leaflet-js]')) {

            const script = document.createElement('script');

            script.src =
                'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';

            script.setAttribute(
                'data-clarity-leaflet-js',
                'true'
            );

            script.onload = function() {

                initClarityOfficeMaps();

            };

            script.onerror = function() {

                console.error(
                    'Clarity Law Map: Unable to load Leaflet library.'
                );

            };

            document.body.appendChild(script);

        } else {

            // Wait for an existing Leaflet request
            const checkLeaflet = setInterval(function() {

                if (typeof L !== 'undefined') {

                    clearInterval(checkLeaflet);

                    initClarityOfficeMaps();

                }

            }, 200);

        }

    }


    /* =====================================
       ELEMENTOR AND FRONTEND INITIALISATION
    ===================================== */

    function startClarityMaps() {

        loadClarityLeaflet();

    }

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            startClarityMaps
        );

    } else {

        startClarityMaps();

    }


    // Support Elementor frontend rendering
    if (window.jQuery) {

        jQuery(window).on(
            'elementor/frontend/init',
            function() {

                startClarityMaps();

            }
        );

    }


    // Detect dynamically added Elementor map widgets
    const mapObserver = new MutationObserver(function(mutations) {

        const hasNewMap = mutations.some(function(mutation) {

            return Array.from(mutation.addedNodes).some(function(node) {

                return node.nodeType === 1 && (

                    node.matches?.('.claritylaw-office-map') ||

                    node.querySelector?.('.claritylaw-office-map')

                );

            });

        });

        if (hasNewMap) {

            if (typeof L !== 'undefined') {

                initClarityOfficeMaps();

            } else {

                loadClarityLeaflet();

            }

        }

    });

    mapObserver.observe(document.documentElement, {

        childList: true,

        subtree: true

    });

})();
