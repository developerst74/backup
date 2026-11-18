/*
 * Clarke Group — Ladies Mile branded pin map.
 * Simplified to match the reference implementation exactly:
 * plain OpenStreetMap raster tiles (grayscale via .is-fallback),
 * a minimal dark circular pin — no logo badge/leader line.
 * Requires Leaflet (window.L) already loaded on the page.
 */
(function () {
	'use strict';
	if (typeof L === 'undefined') return;

	function initMap(el) {
		var lat = parseFloat(el.dataset.lat);
		var lng = parseFloat(el.dataset.lng);
		var zoom = parseInt(el.dataset.zoom, 10) || 15;
		var label = el.dataset.label || '';
		var wrap = el.closest('.lm-map-wrap');
		var status = wrap ? wrap.querySelector('[data-lm-map-status], .map-status') : null;

		if (isNaN(lat) || isNaN(lng)) {
			if (status) status.textContent = 'Map unavailable — see address above.';
			return;
		}

		try {
			el.classList.add('is-fallback');

			var map = L.map(el, {
				scrollWheelZoom: false,
				zoomControl: true
			}).setView([lat, lng], zoom);

			L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noreferrer">OpenStreetMap contributors</a>',
				maxZoom: 19
			}).addTo(map);

			var icon = L.divIcon({
				className: 'clarke-logo-marker',
				html: '<div class="clarke-logo-pin-content" aria-hidden="true"><span class="clarke-minimal-pin"></span></div>',
				iconSize: [30, 34],
				iconAnchor: [15, 30]
			});

			var marker = L.marker([lat, lng], {
				icon: icon,
				title: label,
				keyboard: true
			}).addTo(map);

			if (label) {
				marker.bindTooltip('<strong>' + label + '</strong>', {
					direction: 'top',
					offset: [0, -32]
				});
			}

			if (status) status.setAttribute('hidden', '');
		} catch (e) {
			if (status) status.textContent = 'Map unavailable — see address above.';
		}
	}

	function boot() {
		document.querySelectorAll('[data-lm-map]').forEach(initMap);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
