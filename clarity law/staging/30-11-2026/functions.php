<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 */

add_action('wp_enqueue_scripts', 'hello_elementor_child_style');
function hello_elementor_child_style()
{
	wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
	wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
}

/**
 * Your code goes below.
 */

/* =====================================================
 * Clarity Law – Global Office Map
 * Usage: [clarity_map]
 * ===================================================== */

/* 1. Central list of offices. Edit once, every map updates. */
function clarity_map_get_offices()
{
	$offices = array(
		'hervey-bay' => array('name' => 'Hervey Bay', 'lat' => -25.28651, 'lng' => 152.83551, 'phone' => '07 3485 0184', 'address' => 'Unit 3, 6 Liuzzi St, Hervey Bay QLD 4655'),
		'toowoomba' => array('name' => 'Toowoomba', 'lat' => -27.56247, 'lng' => 151.95203, 'phone' => '1300 952 255', 'address' => '123 Margaret Street, Toowoomba QLD 4350'),
		'sunshine-coast' => array('name' => 'Sunshine Coast', 'lat' => -26.65534, 'lng' => 153.07588, 'phone' => '1300 952 255', 'address' => 'Suite 8, 84 Wises Road, Maroochydore QLD 4558'),
		'north-brisbane' => array('name' => 'North Brisbane', 'lat' => -27.30951, 'lng' => 152.98862, 'phone' => '07 3485 0184', 'address' => '3/22-24 Strathwyn Street, Brendale QLD 4500'),
		'ipswich' => array('name' => 'Ipswich', 'lat' => -27.61556, 'lng' => 152.76141, 'phone' => '07 3485 0147', 'address' => '16 East Street, Ipswich QLD 4305'),
		'brisbane' => array('name' => 'Brisbane', 'lat' => -27.47055, 'lng' => 152.99927, 'phone' => '07 3067 7017', 'address' => 'Level 1, 16 McDougall St, Milton QLD 4064'),
		'logan' => array('name' => 'Logan', 'lat' => -27.67447, 'lng' => 153.10847, 'phone' => '07 3668 0683', 'address' => 'Level 2, 3972 Pacific Hwy, Loganholme QLD 4129'),
		'gold-coast' => array('name' => 'Gold Coast', 'lat' => -28.01253, 'lng' => 153.40862, 'phone' => '07 5613 2683', 'address' => 'Level 15, 2 Corporate Court, Bundall QLD 4217'),
	);
	return apply_filters('clarity_map_offices', $offices);
}

/* 2. Shortcode */
function clarity_map_shortcode($atts)
{
	$a = shortcode_atts(array(
		'offices' => 'all',   // "all", "none", or comma list of slugs
		'custom' => '',      // extra pins: "Name|lat|lng;Name2|lat|lng"
		'height' => '480',   // px
		'zoom' => '13',    // zoom for single-pin maps
		'labels' => 'yes',   // yes / no
		'caption' => 'South East Queensland',
	), $atts, 'clarity_map');

	$all = clarity_map_get_offices();
	$pins = array();

	if (strtolower(trim($a['offices'])) === 'all') {
		$pins = array_values($all);
	} elseif (strtolower(trim($a['offices'])) !== 'none') {
		foreach (array_map('trim', explode(',', $a['offices'])) as $slug) {
			$slug = sanitize_title($slug);
			if (isset($all[$slug]))
				$pins[] = $all[$slug];
		}
	}

	if ($a['custom'] !== '') {
		foreach (explode(';', $a['custom']) as $row) {
			$p = array_map('trim', explode('|', $row));
			if (count($p) >= 3 && is_numeric($p[1]) && is_numeric($p[2])) {
				$pins[] = array('name' => sanitize_text_field($p[0]), 'lat' => (float) $p[1], 'lng' => (float) $p[2]);
			}
		}
	}

	if (empty($pins))
		return '';

	// Leaflet assets (only loaded on pages using the shortcode)
	wp_enqueue_style('clarity-leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
	wp_enqueue_script('clarity-leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
	clarity_map_print_js();

	static $n = 0;
	$n++;
	$id = 'clarity-map-' . $n;
	$data = array_map(function ($p) {
		return array('n' => $p['name'], 'c' => array((float) $p['lat'], (float) $p['lng']));
	}, $pins);

	ob_start(); ?>
	<div class="clarity-map-wrap"
		style="position:relative;width:100%;<?php echo $a['height'] === 'auto' ? 'aspect-ratio:1/1;' : 'height:' . intval($a['height']) . 'px;'; ?>background:#CFE4F1;overflow:hidden;border:1px solid #E7E0D0">
		<div id="<?php echo esc_attr($id); ?>" class="clarity-map"
			data-pins="<?php echo esc_attr(wp_json_encode($data)); ?>" data-zoom="<?php echo intval($a['zoom']); ?>"
			data-labels="<?php echo $a['labels'] === 'no' ? '0' : '1'; ?>"
			style="position:absolute;inset:0;width:100%;height:100%"></div>
		<?php if ($a['caption'] !== ''): ?>
			<div
				style="position:absolute;left:14px;bottom:14px;z-index:500;background:rgba(19,43,94,.92);color:#fff;padding:8px 14px;font-size:10px;letter-spacing:.18em;text-transform:uppercase">
				<?php echo esc_html($a['caption']); ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('clarity_map', 'clarity_map_shortcode');

/* 3. Shared JS (added once, supports many maps per page) */
function clarity_map_print_js()
{
	static $done = false;
	if ($done)
		return;
	$done = true;

	$js = <<<'JS'
(function(){
  function initMap(c){
    if (c._inited || !window.L) return;
    c._inited = true;
    var pins = JSON.parse(c.getAttribute('data-pins') || '[]');
    var maxZoom = parseInt(c.getAttribute('data-zoom') || '13', 10);
    var showLabels = c.getAttribute('data-labels') === '1';
    var wrap = c.parentElement;

    var map = L.map(c, {zoomControl:false, attributionControl:false, dragging:false,
      scrollWheelZoom:false, doubleClickZoom:false, touchZoom:false, boxZoom:false,
      keyboard:false, zoomSnap:0.25});

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Physical_Map/MapServer/tile/{z}/{y}/{x}', {maxZoom:8}).addTo(map);
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {maxZoom:13, opacity:0.6}).addTo(map);

    var dot = L.divIcon({className:'', iconSize:[13,13], iconAnchor:[6.5,6.5],
      html:'<div style="width:13px;height:13px;background:#E8641E;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.35)"></div>'});
    pins.forEach(function(o){ L.marker(o.c,{icon:dot,interactive:false}).addTo(map); });

    function fit(){
      map.invalidateSize();
      if (pins.length === 1) map.setView(pins[0].c, maxZoom);
      else map.fitBounds(L.latLngBounds(pins.map(function(o){return o.c;})), {padding:[54,54]});
    }
    fit();

    var layer = document.createElement('div');
    layer.style.cssText = 'position:absolute;inset:0;z-index:450;pointer-events:none';
    wrap.appendChild(layer);

    function placeLabels(){
      layer.innerHTML = '';
      if (!showLabels) return;
      var W = c.clientWidth, H = c.clientHeight;
      var items = pins.map(function(o){
        var el = document.createElement('div');
        el.textContent = o.n;
        el.style.cssText = "position:absolute;background:#132B5E;color:#fff;font-family:'Inter Tight',sans-serif;font-size:10.5px;font-weight:600;letter-spacing:.04em;padding:2px 7px;white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,.3);border:1px solid rgba(255,255,255,.35);left:-9999px;top:-9999px";
        layer.appendChild(el);
        var p = map.latLngToContainerPoint(L.latLng(o.c));
        return {el:el, p:p, w:el.offsetWidth, h:el.offsetHeight};
      });
      items.sort(function(a,b){return a.p.y-b.p.y;});
      var G = 11, placed = [];
      function hit(a,b){ return !(a.x+a.w<=b.x || b.x+b.w<=a.x || a.y+a.h<=b.y || b.y+b.h<=a.y); }
      items.forEach(function(it){
        var p=it.p, w=it.w, h=it.h;
        var cands = [
          [p.x+G,p.y-h/2],[p.x-G-w,p.y-h/2],[p.x-w/2,p.y-G-h],[p.x-w/2,p.y+G],
          [p.x+G,p.y-G-h],[p.x-G-w,p.y-G-h],[p.x+G,p.y+G],[p.x-G-w,p.y+G],
          [p.x+G+34,p.y-h/2],[p.x-G-w-34,p.y-h/2],[p.x-w/2,p.y-G-h-20],[p.x-w/2,p.y+G+20]
        ];
        var best=null, bestPen=Infinity;
        for (var i=0;i<cands.length;i++){
          var x=cands[i][0], y=cands[i][1], box={x:x,y:y,w:w,h:h};
          if (x<2||y<2||x+w>W-2||y+h>H-2) continue;
          var pen=0; placed.forEach(function(q){ if(hit(box,q)) pen++; });
          if (pen===0){ best=box; break; }
          if (pen<bestPen){ bestPen=pen; best=box; }
        }
        if(!best) best={x:Math.min(Math.max(2,p.x+G),W-w-2), y:Math.min(Math.max(2,p.y-h/2),H-h-2), w:w, h:h};
        placed.push(best);
        it.el.style.left = Math.round(best.x)+'px';
        it.el.style.top  = Math.round(best.y)+'px';
      });
    }

    map.whenReady(function(){ placeLabels(); setTimeout(function(){ fit(); placeLabels(); }, 300); });
    map.on('zoomend moveend', placeLabels);
    if (window.ResizeObserver) new ResizeObserver(function(){ fit(); placeLabels(); }).observe(c);
  }

  function initAll(){ document.querySelectorAll('.clarity-map').forEach(initMap); }
  if (document.readyState === 'complete') initAll();
  else window.addEventListener('load', initAll);
  if (window.jQuery) jQuery(window).on('elementor/frontend/init', function(){ setTimeout(initAll, 500); });
})();
JS;
	wp_add_inline_script('clarity-leaflet', $js);
}

// kirtan custome file include code 


function mytheme_enqueue_custom_assets() {

    // Include Custom CSS
    wp_enqueue_style(
        'mytheme-custom-css',
        get_stylesheet_directory_uri() . '/assets/css/custom.css',
        array(),
        '1.0.0'
    );

    // Include Custom JS
    wp_enqueue_script(
        'mytheme-custom-js',
        get_stylesheet_directory_uri() . '/assets/js/custome.js',
        array(),
        '1.0.0',
        true
    );

}

add_action('wp_enqueue_scripts', 'mytheme_enqueue_custom_assets');





/**

* Display the full post content in Elementor Loop Cards.

*/

add_shortcode( 'full_post_content', function () {

    $post_id = get_the_ID();
 
    if ( ! $post_id ) {

        return '';

    }
 
    $content = get_post_field( 'post_content', $post_id );
 
    return apply_filters( 'the_content', $content );

} );

 