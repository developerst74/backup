/**
 * About page behaviour.
 *
 * Three things the About page needed and did not have:
 *
 * 1. Footprint maps.  homepage-new.js only initialises cards matching
 *    ".cg2-region-card[data-cg2-locations]".  about_footprint_v2.php renders
 *    ".region-card" with data-map-lat / data-map-lng / data-map-zoom and per
 *    development data-dev-lat / data-dev-lng, so the About maps were never
 *    booted and sat on "Loading map" forever.  This file boots those.
 *
 * 2. Partner arrows.  .partner-track is a horizontally scrolling grid in
 *    about-us-new.css, so the arrows scroll it rather than transforming it.
 *
 * 3. Parallax.  homepage-new.js looks for ".cg2-parallax-image"; the About
 *    hero and contact sections output ".parallax-image".
 *
 * Every block bails out quietly when its markup is absent, so this file is
 * safe to load on any page.
 */
(function () {
  "use strict";

  var reduceMotion =
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ------------------------------------------------------------------
   * 1. Development footprint maps
   * ---------------------------------------------------------------- */
  function initFootprintMaps() {
    var cards = document.querySelectorAll(".footprint-section .region-card");
    if (!cards.length || typeof window.L === "undefined") return;

    Array.prototype.forEach.call(cards, function (card) {
      var mapNode = card.querySelector(".leaflet-map");
      var loading = card.querySelector(".map-loading");
      var selection = card.querySelector(".region-selection");
      var directions = card.querySelector(".map-directions");
      var website = selection ? selection.querySelector(".development-website") : null;
      var buttons = [].slice.call(card.querySelectorAll(".region-panel ol li button"));

      if (!mapNode) return;

      // Build the location list straight from the rendered buttons, so the
      // markup stays the single source of truth (no duplicated JSON blob).
      var locations = buttons
        .map(function (btn) {
          var lat = parseFloat(btn.getAttribute("data-dev-lat"));
          var lng = parseFloat(btn.getAttribute("data-dev-lng"));
          if (isNaN(lat) || isNaN(lng)) return null;
          var strong = btn.querySelector("strong");
          var small = btn.querySelector("small");
          return {
            lat: lat,
            lng: lng,
            name: strong ? strong.textContent.trim() : "",
            address: small ? small.textContent.trim() : "",
            button: btn
          };
        })
        .filter(Boolean);

      if (!locations.length) {
        if (loading) loading.textContent = "Map unavailable";
        return;
      }

      var centerLat = parseFloat(card.getAttribute("data-map-lat"));
      var centerLng = parseFloat(card.getAttribute("data-map-lng"));
      var zoom = parseInt(card.getAttribute("data-map-zoom"), 10) || 12;

      if (isNaN(centerLat) || isNaN(centerLng)) {
        centerLat = locations[0].lat;
        centerLng = locations[0].lng;
      }

      var map = window.L.map(mapNode, {
        center: [centerLat, centerLng],
        zoom: zoom,
        scrollWheelZoom: false,
        attributionControl: true
      });

      window.L
        .tileLayer("https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png", {
          maxZoom: 19,
          attribution: "&copy; OpenStreetMap contributors &copy; CARTO"
        })
        .addTo(map);

      if (loading) loading.style.display = "none";

      var markers = locations.map(function (loc, index) {
        var icon = window.L.divIcon({
          className: "footprint-pin",
          html: '<span class="footprint-pin__dot">' + ("0" + (index + 1)).slice(-2) + "</span>",
          iconSize: [28, 28],
          iconAnchor: [14, 14]
        });
        var marker = window.L.marker([loc.lat, loc.lng], { icon: icon }).addTo(map);
        marker.bindTooltip(loc.name, { direction: "top", offset: [0, -12] });
        marker.on("click", function () {
          select(index);
        });
        return marker;
      });

      function select(index) {
        var loc = locations[index];
        if (!loc) return;

        if (selection) {
          var h4 = selection.querySelector("h4");
          var p = selection.querySelector("p");
          if (h4) h4.textContent = loc.name;
          if (p) p.textContent = loc.address;
        }

        if (website) {
          var href = loc.button.getAttribute("data-dev-url");
          if (href) {
            website.href = href;
            website.setAttribute("aria-label", "Explore " + loc.name + " (opens in a new tab)");
          }
        }

        if (directions) {
          directions.href =
            "https://www.google.com/maps/search/?api=1&query=" +
            encodeURIComponent(loc.address || loc.lat + "," + loc.lng);
        }

        buttons.forEach(function (btn, i) {
          btn.setAttribute("aria-pressed", String(i === index));
          var li = btn.closest("li");
          if (li) li.classList.toggle("is-active", i === index);
        });

        markers.forEach(function (m, i) {
          var el = m.getElement && m.getElement();
          if (el) el.classList.toggle("is-active", i === index);
          m.setZIndexOffset(i === index ? 1000 : 0);
        });

        map.flyTo([loc.lat, loc.lng], Math.max(map.getZoom(), Math.min(15, 14)), {
          duration: 0.65,
          animate: !reduceMotion
        });
      }

      locations.forEach(function (loc, index) {
        loc.button.addEventListener("click", function () {
          select(index);
        });
      });

      // Fit all pins, then mark the first as selected.
      if (locations.length > 1) {
        map.fitBounds(
          window.L.latLngBounds(
            locations.map(function (l) {
              return [l.lat, l.lng];
            })
          ),
          { padding: [36, 36], maxZoom: 13 }
        );
      }
      select(0);

      // Leaflet mis-measures when the container starts hidden/animating.
      setTimeout(function () {
        map.invalidateSize();
      }, 350);
    });
  }

  /* ------------------------------------------------------------------
   * 2. Partner track arrows
   * ---------------------------------------------------------------- */
  function initPartnerScroller() {
    var sections = document.querySelectorAll(".partners");
    if (!sections.length) return;

    Array.prototype.forEach.call(sections, function (section) {
      var track = section.querySelector(".partner-track");
      var buttons = section.querySelectorAll("[data-partner-direction]");
      if (!track || !buttons.length) return;

      function step() {
        var card = track.querySelector(".partner-card");
        if (!card) return track.clientWidth * 0.8;
        var styles = window.getComputedStyle(track);
        var gap = parseFloat(styles.columnGap || styles.gap || "20") || 20;
        return card.getBoundingClientRect().width + gap;
      }

      function sync() {
        var max = track.scrollWidth - track.clientWidth - 1;
        Array.prototype.forEach.call(buttons, function (btn) {
          var dir = parseInt(btn.getAttribute("data-partner-direction"), 10);
          var atEnd = dir > 0 ? track.scrollLeft >= max : track.scrollLeft <= 1;
          btn.disabled = max <= 0 || atEnd;
        });
      }

      Array.prototype.forEach.call(buttons, function (btn) {
        btn.addEventListener("click", function () {
          var dir = parseInt(btn.getAttribute("data-partner-direction"), 10) || 1;
          track.scrollBy({
            left: step() * dir,
            behavior: reduceMotion ? "auto" : "smooth"
          });
        });
      });

      track.addEventListener("scroll", sync, { passive: true });
      window.addEventListener("resize", sync);
      sync();
    });
  }

  /* ------------------------------------------------------------------
   * 3. Hero / contact parallax
   * ---------------------------------------------------------------- */
  function initParallax() {
    if (reduceMotion) return;

    var scenes = [].slice
      .call(document.querySelectorAll("[data-parallax-scene]"))
      .map(function (scene) {
        return { scene: scene, image: scene.querySelector(".parallax-image") };
      })
      .filter(function (s) {
        return s.image;
      });

    if (!scenes.length) return;

    var ticking = false;

    function update() {
      ticking = false;
      var viewport = window.innerHeight || 1;

      scenes.forEach(function (item) {
        var rect = item.scene.getBoundingClientRect();
        if (rect.bottom < -200 || rect.top > viewport + 200) return;
        var progress = (rect.top + rect.height / 2 - viewport / 2) / viewport;
        var shift = Math.max(-40, Math.min(40, progress * -40));
        item.image.style.transform = "translate3d(0," + shift.toFixed(2) + "px,0) scale(1.08)";
      });
    }

    function onScroll() {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(update);
    }

    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);
    update();
  }

  function boot() {
    initFootprintMaps();
    initPartnerScroller();
    initParallax();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
