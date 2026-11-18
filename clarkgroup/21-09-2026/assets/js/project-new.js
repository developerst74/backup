/**
 * Clarke Group — Single Project New Design JS
 * Runs on single Projects only (enqueued conditionally). Header
 * scroll/menu/reveal/parallax already handled site-wide by
 * assets/js/homepage-new.js — this file only adds the two widgets
 * unique to the project template: the paired-photo carousel and the
 * neighbourhood amenity map.
 */
(function () {
	"use strict";

	/* ---------- Photo carousel (paired photos) ---------- */
	document.querySelectorAll(".photo-carousel").forEach(function (carousel) {
		var slides = [].slice.call(carousel.querySelectorAll(".gallery-slide"));
		var dots = [].slice.call(carousel.querySelectorAll(".gallery-dot"));
		var prevBtn = carousel.querySelector(".gallery-prev");
		var nextBtn = carousel.querySelector(".gallery-next");
		var countEl = carousel.querySelector(".gallery-count");
		var statusEl = carousel.querySelector(".gallery-status");
		var viewport = carousel.querySelector(".gallery-viewport");
		if (!slides.length) return;

		var index = 0;

		function render() {
			slides.forEach(function (slide, i) { slide.classList.toggle("is-active", i === index); });
			dots.forEach(function (dot, i) {
				var active = i === index;
				dot.classList.toggle("is-active", active);
				dot.setAttribute("aria-pressed", String(active));
			});
			if (countEl) countEl.textContent = String(index + 1).padStart(2, "0") + " / " + String(slides.length).padStart(2, "0");
			if (statusEl) {
				var label = slides[index].getAttribute("aria-label") || "";
				statusEl.textContent = "Showing " + label;
			}
		}

		function goTo(i) {
			index = (i + slides.length) % slides.length;
			render();
		}

		dots.forEach(function (dot, i) {
			dot.addEventListener("click", function () { goTo(i); });
		});
		if (prevBtn) prevBtn.addEventListener("click", function () { goTo(index - 1); });
		if (nextBtn) nextBtn.addEventListener("click", function () { goTo(index + 1); });
		if (viewport) {
			viewport.addEventListener("keydown", function (e) {
				if (e.key === "ArrowLeft") { e.preventDefault(); goTo(index - 1); }
				if (e.key === "ArrowRight") { e.preventDefault(); goTo(index + 1); }
			});
		}

		render();
	});

	/* ---------- Neighbourhood amenity map (numbered pins over a static image) ---------- */
	document.querySelectorAll(".location-panel[data-cg2-points]").forEach(function (panel) {
		var mapWrap = panel.querySelector(".neighbourhood-map-wrap");
		var loading = panel.querySelector(".neighbourhood-map-loading");
		var buttons = [].slice.call(panel.querySelectorAll(".amenity-list button"));
		var showAllBtn = panel.querySelector(".map-show-all");
		var statusEl = panel.querySelector(".map-selection-status");

		var points;
		try {
			points = JSON.parse(panel.getAttribute("data-cg2-points"));
		} catch (e) {
			points = [];
		}
		if (loading) loading.classList.add("is-hidden");
		if (!mapWrap || !points.length) return;

		// Build pin overlays positioned by the top%/left% ACF values.
		var pins = points.map(function (pt, i) {
			var pin = document.createElement("button");
			pin.type = "button";
			pin.className = "map-pin-overlay";
			pin.style.top = pt.top + "%";
			pin.style.left = pt.left + "%";
			pin.textContent = String(i + 1);
			pin.setAttribute("aria-label", pt.label || ("Place " + (i + 1)));
			mapWrap.appendChild(pin);
			return pin;
		});

		function setActive(index) {
			pins.forEach(function (pin, i) { pin.classList.toggle("is-active", i === index); });
			buttons.forEach(function (btn, i) {
				btn.setAttribute("aria-pressed", String(i === index));
			});
			if (statusEl && index !== null && points[index]) {
				statusEl.textContent = "Selected " + points[index].label;
			}
		}

		function clearActive() {
			pins.forEach(function (pin) { pin.classList.remove("is-active"); });
			buttons.forEach(function (btn) { btn.setAttribute("aria-pressed", "false"); });
			if (statusEl) statusEl.textContent = "Showing all nearby places";
		}

		buttons.forEach(function (btn, i) {
			btn.addEventListener("click", function () { setActive(i); });
		});
		pins.forEach(function (pin, i) {
			pin.addEventListener("click", function () { setActive(i); });
		});
		if (showAllBtn) showAllBtn.addEventListener("click", clearActive);
	});
})();

/**
 * ---------- Interactive Location + Amenities map (NEW section) ----------
 * Runs only on the new "interactive_location_map" section
 * (.cg2-loc-panel[data-cg2-loc-property]), which is a separate section
 * from the original static-image amenity map handled above
 * (.location-panel[data-cg2-points]) — the two use entirely separate
 * class namespaces now, so neither this JS/CSS nor the original
 * section/JS can ever interfere with each other again.
 *
 * Builds a real Leaflet + MapLibre map (same stack as the homepage's
 * Development Footprint maps) with one dark "home" pin for the property
 * and numbered white pins for each nearby place, using real lat/lng
 * coordinates from ACF instead of top%/left% guesses over a picture.
 */
(function () {
	"use strict";

	var reducedMotion = false;
	try {
		reducedMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	} catch (e) {}

	function escapeHtml(str) {
		var div = document.createElement("div");
		div.textContent = str == null ? "" : str;
		return div.innerHTML;
	}

	// Builds a Google Maps search link from an address (preferred) or,
	// failing that, raw lat/lng coordinates.
	function buildMapsLink(address, lat, lng) {
		var query = address ? address : (lat + "," + lng);
		return "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(query);
	}

	// Builds the <div class="cg2-loc-marker[...]"><div class="cg2-loc-pin">
	// <svg>...</svg></div></div> markup expected by the .cg2-loc-marker /
	// .cg2-loc-pin / .is-home CSS in project-new.css.
	function buildPinHtml(isHome, number) {
		var inner = isHome
			? '<path class="cg2-loc-pin-house" d="M11 21v-7h6v7M8 14l10-8 10 8" fill="none"/>'
			: '<text x="18" y="21">' + number + "</text>";
		return (
			'<div class="cg2-loc-marker' + (isHome ? " is-home" : "") + '">' +
			'<div class="cg2-loc-pin">' +
			'<svg viewBox="0 0 36 44" width="36" height="44" aria-hidden="true">' +
			'<path class="cg2-loc-pin-body" d="M18 2C9.729 2 3 8.729 3 17c0 11.5 15 25 15 25s15-13.5 15-25C33 8.729 26.271 2 18 2z"/>' +
			inner +
			"</svg>" +
			"</div>" +
			"</div>"
		);
	}

	document.querySelectorAll(".cg2-loc-panel[data-cg2-loc-property]").forEach(function (panel) {
		var mapNode = panel.querySelector(".cg2-loc-map");
		var loading = panel.querySelector(".cg2-loc-map-loading");
		var buttons = [].slice.call(panel.querySelectorAll(".cg2-loc-list button"));
		var showAllBtn = panel.querySelector(".cg2-loc-show-all");
		var statusEl = panel.querySelector(".cg2-loc-status");

		var property, places;
		try {
			property = JSON.parse(panel.getAttribute("data-cg2-loc-property"));
		} catch (e) {
			property = null;
		}
		try {
			places = JSON.parse(panel.getAttribute("data-cg2-loc-places") || "[]");
		} catch (e) {
			places = [];
		}
		var zoom = Number(panel.getAttribute("data-cg2-loc-zoom")) || 14;
		var maxZoom = Number(panel.getAttribute("data-cg2-loc-maxzoom")) || 16;

		if (!mapNode || !property || typeof property.lat !== "number" || typeof property.lng !== "number") {
			if (loading) loading.classList.add("is-hidden");
			return;
		}

		if (!window.L) {
			if (loading) loading.textContent = "Map unavailable. Use the list below or Open in maps.";
			return;
		}

		var map = L.map(mapNode, {
			center: [property.lat, property.lng],
			zoom: zoom,
			scrollWheelZoom: false,
			zoomControl: false,
		});

		var basemapLoaded = false;
		function finishMapLoading() {
			basemapLoaded = true;
			if (loading) loading.classList.add("is-hidden");
		}

		function startFallbackMap() {
			mapNode.classList.add("is-fallback");
			var fallback = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noreferrer">OpenStreetMap contributors</a>',
				maxZoom: 19,
			});
			fallback.on("tileload", finishMapLoading);
			fallback.addTo(map);
			setTimeout(function () {
				if (!basemapLoaded && loading) loading.textContent = "Map unavailable. Use the list below or Open in maps.";
			}, 10000);
		}

		if (window.maplibregl && typeof L.maplibreGL === "function") {
			try {
				var vectorLayer = L.maplibreGL({
					style: "https://tiles.openfreemap.org/styles/positron",
					attributionControl: { customAttribution: '<a href="https://openfreemap.org/" target="_blank" rel="noreferrer">OpenFreeMap</a> &copy; <a href="https://www.openmaptiles.org/" target="_blank" rel="noreferrer">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noreferrer">OpenStreetMap</a>' },
				});
				vectorLayer.addTo(map);
				vectorLayer.getMaplibreMap().on("load", finishMapLoading);
				setTimeout(function () { if (!basemapLoaded) startFallbackMap(); }, 10000);
			} catch (e) {
				startFallbackMap();
			}
		} else {
			startFallbackMap();
		}

		L.control.zoom({ position: "bottomright" }).addTo(map);

		// Property ("home") marker.
		var homeIcon = L.divIcon({
			className: "cg2-loc-marker-wrap",
			html: buildPinHtml(true, ""),
			iconSize: [36, 44],
			iconAnchor: [18, 44],
			popupAnchor: [0, -40],
		});
		var homeMarker = L.marker([property.lat, property.lng], {
			icon: homeIcon,
			title: property.name,
			alt: property.name + (property.address ? ", " + property.address : ""),
			zIndexOffset: 1000,
		}).addTo(map);
		homeMarker.bindPopup(
			"<strong>" + escapeHtml(property.name) + "</strong>" +
			(property.address ? "<p>" + escapeHtml(property.address) + "</p>" : "") +
			'<a href="' + buildMapsLink(property.address, property.lat, property.lng) + '" target="_blank" rel="noopener noreferrer">Open in maps</a>'
		);

		var bounds = L.latLngBounds([[property.lat, property.lng]]);
		var markers = [];

		function updateMarkerState(selectedIndex) {
			markers.forEach(function (marker, i) {
				var el = marker.getElement && marker.getElement();
				var pin = el ? el.querySelector(".cg2-loc-marker") : null;
				if (pin) pin.classList.toggle("is-selected", i === selectedIndex);
				marker.setZIndexOffset(i === selectedIndex ? 900 : 0);
			});
		}

		function selectPlace(index) {
			var place = places[index];
			if (!place) return;
			buttons.forEach(function (btn, i) {
				btn.setAttribute("aria-pressed", String(i === index));
			});
			updateMarkerState(index);
			if (statusEl) statusEl.textContent = "Selected " + place.name;
			map.flyTo([place.lat, place.lng], Math.max(map.getZoom(), Math.min(maxZoom, 16)), {
				duration: 0.65,
				animate: !reducedMotion,
			});
			if (markers[index]) markers[index].openPopup();
		}

		function clearSelection() {
			buttons.forEach(function (btn) { btn.setAttribute("aria-pressed", "false"); });
			updateMarkerState(-1);
			if (statusEl) statusEl.textContent = "Showing all nearby places";
			map.closePopup();
			map.fitBounds(bounds, { padding: [45, 45], animate: !reducedMotion, maxZoom: maxZoom });
		}

		places.forEach(function (place, index) {
			bounds.extend([place.lat, place.lng]);
			var icon = L.divIcon({
				className: "cg2-loc-marker-wrap",
				html: buildPinHtml(false, index + 1),
				iconSize: [36, 44],
				iconAnchor: [18, 44],
				popupAnchor: [0, -40],
			});
			var marker = L.marker([place.lat, place.lng], {
				icon: icon,
				title: place.name,
				alt: place.name + (place.address ? ", " + place.address : ""),
			}).addTo(map);
			marker.bindPopup(
				(place.category ? "<small>" + escapeHtml(place.category) + "</small>" : "") +
				"<strong>" + escapeHtml(place.name) + "</strong>" +
				(place.address ? "<p>" + escapeHtml(place.address) + "</p>" : "") +
				'<a href="' + buildMapsLink(place.address, place.lat, place.lng) + '" target="_blank" rel="noopener noreferrer">Open in maps</a>'
			);
			marker.on("click", function () { selectPlace(index); });
			markers.push(marker);
		});

		buttons.forEach(function (btn, index) {
			btn.addEventListener("click", function () { selectPlace(index); });
		});
		if (showAllBtn) showAllBtn.addEventListener("click", clearSelection);

		map.fitBounds(bounds, { padding: [45, 45], animate: false, maxZoom: maxZoom });

		if ("ResizeObserver" in window) {
			new ResizeObserver(function () { map.invalidateSize({ pan: false }); }).observe(mapNode);
		}
	});
})();

