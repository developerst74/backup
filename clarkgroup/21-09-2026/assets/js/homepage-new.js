/**
 * Clarke Group — New Site Design System JS
 * Loaded site-wide (header + mobile menu are shared everywhere).
 * Map init only runs if it finds `.cg2-leaflet-map` elements on the page,
 * so it's a no-op on pages without the footprint-maps section.
 */
(function () {
	"use strict";

	/* ---------- Header scroll state + progress bar ---------- */
	var header = document.querySelector(".site-header");
	var progress = document.querySelector(".scroll-progress");

	function onScroll() {
		var max = Math.max(document.documentElement.scrollHeight - window.innerHeight, 1);
		if (header) header.classList.toggle("is-scrolled", window.scrollY > 36);
		if (progress) progress.style.transform = "scaleX(" + Math.min(window.scrollY / max, 1) + ")";
	}
	onScroll();
	window.addEventListener("scroll", onScroll, { passive: true });

	/* ---------- Slide-out navigation menu ---------- */
	var menuButton = document.querySelector(".menu-button");
	var menu = document.querySelector(".handoff-menu");
	var backdrop = document.querySelector(".handoff-menu-backdrop");
	var main = document.querySelector("main");
	var menuCloseButton = menu ? menu.querySelector(".handoff-menu-close") : null;
	var previousFocus;

	function setMenuOpen(opening, restoreFocus) {
		if (!menu || !menuButton) return;
		if (typeof restoreFocus === "undefined") restoreFocus = true;
		if (opening) previousFocus = document.activeElement;

		menu.classList.toggle("is-open", opening);
		menuButton.classList.toggle("is-open", opening);
		if (backdrop) backdrop.classList.toggle("is-open", opening);
		document.documentElement.classList.toggle("navigation-open", opening);
		menuButton.setAttribute("aria-expanded", String(opening));
		menuButton.setAttribute("aria-label", opening ? "Close navigation" : "Open navigation");

		if (!opening && menu.contains(document.activeElement)) document.activeElement.blur();
		menu.setAttribute("aria-hidden", String(!opening));
		try { menu.inert = !opening; } catch (e) {}
		if (main) { try { main.inert = opening; } catch (e) {} }

		if (opening && menuCloseButton) {
			menuCloseButton.focus({ preventScroll: true });
		} else if (!opening && restoreFocus) {
			var destination = (previousFocus && previousFocus.isConnected) ? previousFocus : menuButton;
			destination.focus({ preventScroll: true });
		}
	}

	function closeMenu() { setMenuOpen(false); }

	if (menuButton) {
		menuButton.addEventListener("click", function () {
			setMenuOpen(!menu.classList.contains("is-open"));
		});
	}
	if (menuCloseButton) menuCloseButton.addEventListener("click", closeMenu);
	if (backdrop) backdrop.addEventListener("click", closeMenu);

	if (menu) {
		var navLinks = menu.querySelectorAll("nav a");
		navLinks.forEach(function (link) {
			link.addEventListener("click", function (e) {
				var href = link.getAttribute("href") || "";
				if (href.charAt(0) === "#") {
					var destination = document.querySelector(href);
					if (destination) {
						e.preventDefault();
						setMenuOpen(false, false);
						destination.setAttribute("tabindex", "-1");
						setTimeout(function () {
							destination.scrollIntoView({ behavior: "smooth" });
							destination.focus({ preventScroll: true });
						}, 50);
						return;
					}
				}
				setMenuOpen(false, false);
			});
		});

		menu.addEventListener("keydown", function (event) {
			if (event.key === "Escape") {
				event.preventDefault();
				closeMenu();
			} else if (event.key === "Tab") {
				var focusable = [].slice.call(menu.querySelectorAll("button, a[href]"));
				var first = focusable[0];
				var last = focusable[focusable.length - 1];
				if (event.shiftKey && document.activeElement === first) {
					event.preventDefault();
					last.focus();
				} else if (!event.shiftKey && document.activeElement === last) {
					event.preventDefault();
					first.focus();
				}
			}
		});
	}

	/* ---------- Parallax (hero / ladies mile / contact photos) ---------- */
	var parallaxPreference = window.matchMedia("(prefers-reduced-motion: reduce)");
	var parallaxScenes = [].slice.call(document.querySelectorAll("[data-parallax-scene]")).map(function (scene) {
		return {
			scene: scene,
			image: scene.querySelector(".cg2-parallax-image"),
			top: 0,
			height: 0,
			bleed: 0,
			strength: Number(scene.dataset.parallaxStrength) || 0.15,
		};
	}).filter(function (item) { return item.image; });

	var parallaxFrame = 0;
	var parallaxGeometryDirty = true;

	function measureParallax() {
		parallaxScenes.forEach(function (item) {
			item.top = 0;
			for (var node = item.scene; node; node = node.offsetParent) item.top += node.offsetTop;
			item.height = item.scene.clientHeight;
			item.bleed = Math.min(120, item.height * item.strength);
			item.image.style.setProperty("--parallax-bleed", item.bleed.toFixed(2) + "px");
		});
		parallaxGeometryDirty = false;
	}

	function renderParallax() {
		parallaxFrame = 0;
		if (parallaxPreference.matches) return;
		if (parallaxGeometryDirty) measureParallax();
		var viewportHeight = window.innerHeight;
		var pageScroll = window.scrollY;
		parallaxScenes.forEach(function (item) {
			var top = item.top - pageScroll;
			if (top > viewportHeight || top + item.height < 0) return;
			var progressVal = Math.max(0, Math.min(1, (viewportHeight - top) / (viewportHeight + item.height)));
			var offset = (progressVal * 2 - 1) * item.bleed;
			item.image.style.setProperty("--parallax-y", offset.toFixed(2) + "px");
		});
	}

	function queueParallax() {
		if (!parallaxPreference.matches && !parallaxFrame) parallaxFrame = requestAnimationFrame(renderParallax);
	}

	function resizeParallax() {
		parallaxGeometryDirty = true;
		queueParallax();
	}

	function syncParallaxPreference() {
		if (parallaxFrame) cancelAnimationFrame(parallaxFrame);
		parallaxFrame = 0;
		parallaxScenes.forEach(function (item) {
			item.image.classList.toggle("is-parallax-active", !parallaxPreference.matches);
			if (parallaxPreference.matches) {
				item.image.style.removeProperty("--parallax-y");
				item.image.style.removeProperty("--parallax-bleed");
			}
		});
		resizeParallax();
	}

	if (parallaxScenes.length) {
		window.addEventListener("scroll", queueParallax, { passive: true });
		window.addEventListener("resize", resizeParallax, { passive: true });
		window.addEventListener("pageshow", resizeParallax);
		if (parallaxPreference.addEventListener) parallaxPreference.addEventListener("change", syncParallaxPreference);
		if ("ResizeObserver" in window) {
			var parallaxResizeObserver = new ResizeObserver(resizeParallax);
			parallaxScenes.forEach(function (item) { parallaxResizeObserver.observe(item.scene); });
			if (main) parallaxResizeObserver.observe(main);
		}
		if (document.fonts && document.fonts.ready) document.fonts.ready.then(resizeParallax);
		syncParallaxPreference();
	}

	/* ---------- Scroll reveal ---------- */
	document.documentElement.classList.add("cg2-motion-ready");
	if ("IntersectionObserver" in window) {
		var revealObserver = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				entry.target.classList.add("is-visible");
				revealObserver.unobserve(entry.target);
			});
		}, { threshold: 0.12, rootMargin: "0px 0px -6% 0px" });
		document.querySelectorAll("[data-reveal]").forEach(function (el) { revealObserver.observe(el); });
	} else {
		document.querySelectorAll("[data-reveal]").forEach(function (el) { el.classList.add("is-visible"); });
	}

	/* ---------- Animated stat counters ---------- */
	if ("IntersectionObserver" in window) {
		var countObserver = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				countObserver.unobserve(entry.target);
				var el = entry.target;
				var target = Number(el.getAttribute("data-target")) || 0;
				var prefixEl = el.previousElementSibling && el.previousElementSibling.hasAttribute("data-counter-prefix") ? el.previousElementSibling : null;
				var suffixEl = el.nextElementSibling && el.nextElementSibling.hasAttribute("data-counter-suffix") ? el.nextElementSibling : null;

				if (parallaxPreference.matches) {
					el.textContent = String(target);
					return;
				}
				var start = performance.now();
				function tick(now) {
					var p = Math.min((now - start) / 1650, 1);
					var value = Math.round(target * (1 - Math.pow(1 - p, 4)));
					el.textContent = String(value);
					if (p < 1) requestAnimationFrame(tick);
				}
				requestAnimationFrame(tick);
			});
		}, { threshold: 0.6 });
		document.querySelectorAll("[data-counter]").forEach(function (el) { countObserver.observe(el); });
	}

	/* ---------- Development footprint — interactive maps ---------- */
	var mapCards = document.querySelectorAll(".cg2-region-card[data-cg2-locations]");
	if (!mapCards.length) return;

	function escapeHtml(str) {
		return String(str).replace(/[&<>"']/g, function (ch) {
			return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[ch];
		});
	}

	var reducedMotion = parallaxPreference.matches;

	mapCards.forEach(function (card) {
		var mapNode = card.querySelector(".cg2-leaflet-map");
		var loading = card.querySelector(".cg2-map-loading");
		var panel = card.querySelector(".cg2-region-panel");
		var selection = card.querySelector(".cg2-region-selection");
		var buttons = [].slice.call(card.querySelectorAll(".cg2-region-panel li button"));
		var directions = panel ? panel.querySelector(".cg2-map-directions") : null;
		var developmentWebsite = selection ? selection.querySelector(".cg2-development-website") : null;

		var locations;
		try {
			locations = JSON.parse(card.getAttribute("data-cg2-locations"));
		} catch (e) {
			locations = [];
		}
		var center = JSON.parse(card.getAttribute("data-cg2-center") || "[0,0]");
		var zoom = Number(card.getAttribute("data-cg2-zoom")) || 10;
		var maxZoom = Number(card.getAttribute("data-cg2-maxzoom")) || 14;
		var regionId = card.getAttribute("data-region") || "";

		if (!locations.length || !mapNode) return;

		var map, markers = [], selectedIndex = 0;

		function updatePinState() {
			markers.forEach(function (marker, index) {
				var el = marker.getElement && marker.getElement();
				if (el) el.classList.toggle("is-active", index === selectedIndex);
				marker.setZIndexOffset(index === selectedIndex ? 1000 : 0);
			});
		}

		function selectLocation(index) {
			selectedIndex = index;
			var loc = locations[index];
			if (selection) {
				var spanEl = selection.querySelector("span");
				var h4El = selection.querySelector("h4");
				var pEl = selection.querySelector("p");
				if (spanEl) spanEl.textContent = loc.area || "";
				if (h4El) h4El.textContent = loc.name || "";
				if (pEl) pEl.textContent = loc.address || "";
			}
			if (developmentWebsite && loc.url) {
				developmentWebsite.href = loc.url;
				developmentWebsite.setAttribute("aria-label", "Explore " + loc.name + " (opens in a new tab)");
			}
			if (directions) {
				var q = regionId === "queenstown" ? loc.lat + "," + loc.lng : (loc.address || loc.name);
				directions.href = "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(q);
			}
			buttons.forEach(function (button, i) {
				button.closest("li").classList.toggle("is-active", i === index);
				button.setAttribute("aria-pressed", String(i === index));
			});
			if (map && markers[index]) {
				updatePinState();
				map.flyTo([loc.lat, loc.lng], Math.max(map.getZoom(), Math.min(maxZoom, 13)), { duration: 0.65, animate: !reducedMotion });
				if (markers[index].openTooltip) markers[index].openTooltip();
			}
		}

		buttons.forEach(function (button, index) {
			button.addEventListener("click", function () { selectLocation(index); });
		});

		if (!window.L) {
			if (loading) loading.textContent = "Map unavailable. Select a project and use Open in maps below.";
			return;
		}

		map = L.map(mapNode, { center: center, zoom: zoom, scrollWheelZoom: false, zoomControl: false });

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
				if (!basemapLoaded && loading) loading.textContent = "Map unavailable. Select a project and use Open in maps below.";
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

		// Classic teardrop pin head (hollow white dot), reused for every
		// marker — sits above the logo badge, connected by a thin stem.
		var PIN_HEAD_SVG =
			'<svg class="cg2-pin-head" width="24" height="32" viewBox="0 0 24 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
			'<path d="M12 0C5.373 0 0 5.373 0 12c0 9 12 20 12 20s12-11 12-20C24 5.373 18.627 0 12 0z" fill="#14181a" stroke="#fff" stroke-width="1.5"/>' +
			'<circle cx="12" cy="12" r="5" fill="#fff"/>' +
			"</svg>";

		var bounds = L.latLngBounds([]);
		markers = locations.map(function (loc, index) {
			// Logo badge: shows the development's uploaded logo (ACF "Pin
			// Logo" field) inside the dark circle, or falls back to the
			// development name as text when no logo has been set.
			var badgeInner = loc.logo
				? '<img src="' + escapeHtml(loc.logo) + '" alt="" loading="lazy" decoding="async">'
				: '<span class="cg2-logo-text">' + escapeHtml(loc.name || "") + "</span>";
			var pin = L.divIcon({
				className: "cg2-minimal-marker",
				html:
					PIN_HEAD_SVG +
					'<span class="cg2-pin-stem"></span>' +
					'<span class="cg2-logo-badge">' + badgeInner + "</span>",
				iconSize: [52, 100],
				iconAnchor: [26, 32],
				tooltipAnchor: [0, -6],
			});
			var marker = L.marker([loc.lat, loc.lng], { icon: pin, title: loc.name, alt: loc.name + ", " + loc.address }).addTo(map);
			var el = marker.getElement();
			if (el) el.setAttribute("aria-label", loc.name + ", " + loc.address);

			var tooltip = document.createElement("div");
			var strongEl = document.createElement("strong");
			strongEl.textContent = loc.name;
			tooltip.appendChild(strongEl);
			tooltip.appendChild(document.createElement("br"));
			tooltip.appendChild(document.createTextNode(loc.address || ""));
			marker.bindTooltip(tooltip, { direction: "top", className: "cg2-map-tooltip" });
			marker.on("click", function () { selectLocation(index); });
			bounds.extend([loc.lat, loc.lng]);
			return marker;
		});

		map.fitBounds(bounds, { padding: [45, 45], animate: false, maxZoom: maxZoom });
		updatePinState();
		/* First location's details are already server-rendered in the markup,
		   so we only sync pin state here rather than re-triggering a flyTo. */

		if ("ResizeObserver" in window) {
			new ResizeObserver(function () { map.invalidateSize({ pan: false }); }).observe(mapNode);
		}
	});
})();