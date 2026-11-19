<?php
/**
 * Luminate – News & Media filterable listing.
 * Usage: [lm_news_media]
 * Optional: [lm_news_media all_url="/news-media/"]  -> "View All" link goes to that URL.
 *           Without all_url, "View All" resets filters/search on the same page.
 */
if (!defined('ABSPATH'))
	exit;

/** Button label => category slug. EDIT SLUGS to match your WP categories. */
function lm_nm_filters()
{
	return array(
		'all' => 'All',
		'company' => 'Company',
		'projects' => 'Projects',
		'industry-insights' => 'Industry Insights',
		'conference' => 'Conferences',
		'events' => 'Events',
	);
}

const LM_NM_PER_PAGE = 10; // cards per "page" (5 per row x 2 rows). Featured post is extra.

function lm_nm_args($cat, $search, $per, $offset)
{
	$a = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => $per,
		'offset' => $offset,
		'ignore_sticky_posts' => true,
	);
	if ($cat && 'all' !== $cat)
		$a['category_name'] = $cat;
	if ($search)
		$a['s'] = $search;
	return $a;
}

function lm_nm_chip()
{
	$cats = get_the_category();
	return $cats ? '<span class="lm-chip">' . esc_html($cats[0]->name) . '</span>' : '';
}

function lm_nm_featured_html()
{
	ob_start(); ?>
	<article class="lm-feat lm-reveal">
		<a class="lm-feat__img" href="<?php the_permalink(); ?>">
			<?php if (has_post_thumbnail())
				the_post_thumbnail('large'); ?>
		</a>
		<div class="lm-feat__body">
			<span class="lm-feat__label">Featured</span>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<div class="lm-meta"><?php echo esc_html(get_the_date('M j, Y')); ?> <i></i> <?php echo lm_nm_chip(); ?>
			</div>
			<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 38)); ?></p>
			<a class="lm-read" href="<?php the_permalink(); ?>">READ MORE <span class="lm-arrow">&rarr;</span></a>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

function lm_nm_card_html()
{
	ob_start(); ?>
	<article class="lm-card lm-reveal">
		<a class="lm-card__img" href="<?php the_permalink(); ?>">
			<?php if (has_post_thumbnail())
				the_post_thumbnail('medium_large'); ?>
		</a>
		<div class="lm-card__body">
			<?php echo lm_nm_chip(); ?>
			<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<div class="lm-meta"><?php echo esc_html(get_the_date('M j, Y')); ?></div>
			<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>
			<a class="lm-read" href="<?php the_permalink(); ?>">READ MORE <span class="lm-arrow">&rarr;</span></a>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/** Core query used by both first render and AJAX. */
function lm_nm_get($cat, $search, $page)
{
	$page = max(1, (int) $page);
	$out = array('featured' => '', 'cards' => '', 'has_more' => false, 'total' => 0);

	$q1 = new WP_Query(lm_nm_args($cat, $search, 1, 0));
	$out['total'] = (int) $q1->found_posts;

	if (!$out['total']) {
		$out['featured'] = '<p class="lm-empty">No posts found. Try another filter or search term.</p>';
		return $out;
	}

	if (1 === $page && $q1->have_posts()) {
		$q1->the_post();
		$out['featured'] = lm_nm_featured_html();
		wp_reset_postdata();
	}

	$offset = 1 + ($page - 1) * LM_NM_PER_PAGE;
	$q2 = new WP_Query(lm_nm_args($cat, $search, LM_NM_PER_PAGE, $offset));
	while ($q2->have_posts()) {
		$q2->the_post();
		$out['cards'] .= lm_nm_card_html();
	}
	wp_reset_postdata();

	$out['has_more'] = ($offset + LM_NM_PER_PAGE) < $out['total'];
	return $out;
}

/** AJAX */
function lm_nm_ajax()
{
	$valid = array_keys(lm_nm_filters());
	$cat = isset($_POST['cat']) ? sanitize_key(wp_unslash($_POST['cat'])) : 'all';
	if (!in_array($cat, $valid, true))
		$cat = 'all';
	$s = isset($_POST['s']) ? sanitize_text_field(wp_unslash($_POST['s'])) : '';
	$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
	wp_send_json_success(lm_nm_get($cat, $s, $page));
}
add_action('wp_ajax_lm_news_filter', 'lm_nm_ajax');
add_action('wp_ajax_nopriv_lm_news_filter', 'lm_nm_ajax');

/** Shortcode */
function lm_nm_shortcode($atts = array())
{
	$atts = shortcode_atts(array('all_url' => ''), $atts, 'lm_news_media');

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	wp_enqueue_style('lm-nm', $uri . '/assets/lm-news-media.css', array(), filemtime($dir . '/assets/lm-news-media.css'));
	wp_enqueue_script('lm-nm', $uri . '/assets/lm-news-media.js', array(), filemtime($dir . '/assets/lm-news-media.js'), true);
	wp_localize_script('lm-nm', 'lmNews', array('ajax' => admin_url('admin-ajax.php')));

	$filters = lm_nm_filters();
	$init = isset($_GET['cat']) ? sanitize_key($_GET['cat']) : 'all';
	if (!isset($filters[$init]))
		$init = 'all';
	$data = lm_nm_get($init, '', 1);

	$all_url = $atts['all_url'] ? esc_url($atts['all_url']) : '';

	ob_start(); ?>
	<div class="lm-nm" data-cat="<?php echo esc_attr($init); ?>">
		<script>document.currentScript.parentNode.classList.add("lm-js");</script>
		<div class="lm-nm__bar">
			<div class="lm-nm__filters">
				<?php foreach ($filters as $slug => $label): ?>
					<button type="button" class="lm-pill<?php echo $slug === $init ? ' is-active' : ''; ?>"
						data-cat="<?php echo esc_attr($slug); ?>"><?php echo esc_html($label); ?></button>
				<?php endforeach; ?>
			</div>
			<label class="lm-nm__searchwrap">
				<svg class="lm-nm__searchicon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
					stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<circle cx="11" cy="11" r="7"></circle>
					<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
				</svg>
				<input type="search" class="lm-nm__search" placeholder="Search News &amp; Media..."
					aria-label="Search News and Media">
			</label>
		</div>

		<div class="lm-nm__results">
			<div class="lm-nm__featured"><?php echo $data['featured']; ?></div>

			<div class="lm-nm__head">
				<h3 class="lm-nm__heading">LATEST FROM LUMINATE</h3>
				<a class="lm-viewall" href="<?php echo $all_url ? $all_url : '#'; ?>" <?php echo $all_url ? ' data-link="1"' : ''; ?>>View All News &amp; Media <span class="lm-arrow">&rarr;</span></a>
			</div>

			<div class="lm-nm__grid"><?php echo $data['cards']; ?></div>
			<div class="lm-nm__more" <?php echo $data['has_more'] ? '' : ' hidden'; ?>>
				<button type="button" class="lm-loadmore">Load more</button>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('lm_news_media', 'lm_nm_shortcode');