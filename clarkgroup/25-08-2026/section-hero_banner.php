<?php
/**
 * Project Section — Hero Banner
 * Layout name: hero_banner
 *
 * Full-featured hero. All fields OPTIONAL — missing fields show
 * nothing, no empty wrappers, so admins fill only what the project
 * actually uses (logo, pills, badge, location-only, 1 vs 2 buttons,
 * etc.).
 *
 * Falls back to top-level Project fields (cg_project_hero_image,
 * cg_project_title, cg_project_location, cg_project_short_description,
 * cg_project_for_sale) when the section-level overrides are left empty,
 * so admins don't have to duplicate data.
 */

if (!defined('ABSPATH')) {
	exit;
}

$project_id = get_the_ID();

/* ---------- Background image: section override → Project Images fallback ---------- */
$bg = get_sub_field('background_image');
if ($bg && is_array($bg) && !empty($bg['url'])) {
	$bg_url = esc_url($bg['url']);
} else {
	$hero_id = get_field('cg_project_hero_image', $project_id);
	$bg_url = $hero_id ? esc_url(wp_get_attachment_image_url((int) $hero_id, 'full')) : '';
}

/* ---------- Logo (overlay image) ---------- */
$logo = get_sub_field('logo');
$logo_url = '';
$logo_alt = '';
if ($logo && is_array($logo) && !empty($logo['url'])) {
	$logo_url = esc_url($logo['url']);
	$logo_alt = esc_attr($logo['alt'] ? $logo['alt'] : wp_strip_all_tags(get_the_title($project_id)));
}

/* ---------- Category Pills (top row, joined with bullet) ---------- */
$pill_1 = trim((string) get_sub_field('category_pill_1'));
$pill_2 = trim((string) get_sub_field('category_pill_2'));
$pills_html = '';
if ($pill_1 !== '' || $pill_2 !== '') {
	$chunks = array();
	if ($pill_1 !== '')
		$chunks[] = esc_html($pill_1);
	if ($pill_2 !== '')
		$chunks[] = esc_html($pill_2);
	$pills_html = implode(' <span aria-hidden="true">•</span> ', $chunks);
}

/* ---------- Status Badge ---------- */
$badge_mode = (string) get_sub_field('status_badge_mode');
if ($badge_mode === '') {
	$badge_mode = 'default';
}
$badge_html = '';
$badge_text = '';
switch ($badge_mode) {
	case 'hide':
		$badge_text = '';
		break;
	case 'under_construction':
		$badge_text = __('Under Construction', 'child-generatepress');
		break;
	case 'available_now':
		$badge_text = __('Available Now', 'child-generatepress');
		break;
	case 'for_sale':
		$badge_text = __('For Sale', 'child-generatepress');
		break;
	case 'sold':
		$badge_text = __('Sold', 'child-generatepress');
		break;
	case 'coming_soon':
		$badge_text = __('Coming Soon', 'child-generatepress');
		break;
	case 'custom':
		$custom = trim((string) get_sub_field('status_badge_custom'));
		$badge_text = ($custom !== '') ? $custom : '';
		break;
	case 'default':
	default:
		if (get_field('cg_project_for_sale', $project_id) === 'yes') {
			$badge_text = __('For Sale', 'child-generatepress');
		} else {
			$badge_text = '';
		}
		break;
}
if ($badge_text !== '') {
	$badge_slug = sanitize_title_with_dashes($badge_text);
	$badge_html = '<span class="property-banner__badge property-banner__badge--' . esc_attr($badge_slug) . '">' . esc_html($badge_text) . '</span>';
}

/* ---------- Title + Excerpt ---------- */
$title = trim((string) get_sub_field('title'));
if ($title === '') {
	$cg_title = trim((string) get_field('cg_project_title', $project_id));
	$title = ($cg_title !== '') ? $cg_title : get_the_title($project_id);
}

$excerpt = trim((string) get_sub_field('excerpt'));

/* ---------- Location / Date / Features (meta bar) ---------- */
$loc = trim((string) get_sub_field('location'));
if ($loc === '') {
	$loc = trim((string) get_field('cg_project_location', $project_id));
}
$date = trim((string) get_sub_field('date'));
$features = trim((string) get_sub_field('features'));

/* ---------- Buttons ---------- */
$btn1_text = trim((string) get_sub_field('button_text'));
$btn1_link = get_sub_field('button_link');
$btn1_url = '';
$btn1_target = '';
if ($btn1_link && is_array($btn1_link) && !empty($btn1_link['url'])) {
	$btn1_url = esc_url($btn1_link['url']);
	$btn1_target = !empty($btn1_link['target']) ? ' target="' . esc_attr($btn1_link['target']) . '" rel="noopener noreferrer"' : '';
}
$btn1_used = ($btn1_text !== '');
if ($btn1_used && $btn1_url === '') {
	$btn1_url = '#';
}

$btn2_text = trim((string) get_sub_field('button_2_text'));
$btn2_link = get_sub_field('button_2_link');
$btn2_url = '';
$btn2_target = '';
if ($btn2_link && is_array($btn2_link) && !empty($btn2_link['url'])) {
	$btn2_url = esc_url($btn2_link['url']);
	$btn2_target = !empty($btn2_link['target']) ? ' target="' . esc_attr($btn2_link['target']) . '" rel="noopener noreferrer"' : '';
}
$btn2_used = ($btn2_text !== '');
if ($btn2_used && $btn2_url === '') {
	$btn2_url = '#';
}

$any_button = ($btn1_used || $btn2_used);

/* ---------- Social / Share Links ---------- */
$social_links = get_sub_field('social_links');
$default_social_icons = array(
	'email' => '/wp-content/uploads/2026/08/Icons-3.png',
	'facebook' => '/wp-content/uploads/2026/08/Vector-1-1.png',
);

/* ============================ RENDER ============================ */
?>
<section class="property-banner project-hero-banner" <?php if ($bg_url): ?>
		style="background-image:url('<?php echo $bg_url; ?>')" <?php endif; ?>>
	<div class="property-banner__overlay" aria-hidden="true"></div>
	<div class="content-section">
		<?php if ($logo_url || $pills_html !== ''): ?>
			<div class="property-banner__top-row">
				<?php if ($logo_url): ?>
					<div class="property-banner__logo">
						<img src="<?php echo $logo_url; ?>" alt="<?php echo $logo_alt; ?>" />
					</div>
				<?php endif; ?>
				<?php if ($pills_html !== ''): ?>
					<div class="property-banner__pills">
						<?php echo $pills_html; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="property-banner__content">
			<?php echo $badge_html; ?>

			<?php if ($title !== ''): ?>
				<h1 class="property-banner__title"><?php echo esc_html($title); ?></h1>
			<?php endif; ?>

			<?php if ($excerpt !== ''): ?>
				<p class="property-banner__excerpt">
					<?php echo wp_kses_post(wpautop($excerpt) === $excerpt ? $excerpt : nl2br(esc_html($excerpt))); ?>
				</p>
			<?php endif; ?>

			<?php if ($any_button): ?>
				<div class="property-banner__buttons">
					<?php if ($btn1_used): ?>
						<a class="button button--primary" href="<?php echo esc_url($btn1_url); ?>" <?php echo $btn1_target; ?>>
							<?php echo esc_html($btn1_text); ?>
							<span class="button__arrow" aria-hidden="true">→</span>
						</a>
					<?php endif; ?>

					<?php if ($btn2_used): ?>
						<a class="button button--secondary" href="<?php echo esc_url($btn2_url); ?>" <?php echo $btn2_target; ?>>
							<?php echo esc_html($btn2_text); ?>
							<span class="button__arrow" aria-hidden="true">→</span>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ($loc !== '' || $date !== '' || $features !== ''): ?>
		<div class="property-banner__meta-bar">
			<div class="row">
				<div class="inform-bar">

					<?php if ($loc !== ''): ?>
						<div class="informbar-cards">
							<img src="/wp-content/uploads/2026/08/pin.png" alt="Location">
							<span class="property-banner__meta-item property-banner__meta-item--location">
								<strong><?php esc_html_e('Location', 'child-generatepress'); ?></strong>
								<span class="property-banner__meta-value">
									<?php echo esc_html($loc); ?>
								</span>
							</span>
						</div>
					<?php endif; ?>

					<?php if ($date !== ''): ?>
						<div class="informbar-cards">
							<img src="/wp-content/uploads/2026/08/Icons-3-2.png" alt="Date">
							<span class="property-banner__meta-item property-banner__meta-item--date">
								<strong><?php esc_html_e('Date', 'child-generatepress'); ?></strong>
								<span class="property-banner__meta-value">
									<?php echo esc_html($date); ?>
								</span>
							</span>
						</div>
					<?php endif; ?>

					<?php if ($features !== ''): ?>
						<div class="informbar-cards">
							<img src="/wp-content/uploads/2026/08/Icons-2-2.png" alt="Features">
							<span class="property-banner__meta-item property-banner__meta-item--features">
								<strong><?php esc_html_e('Features', 'child-generatepress'); ?></strong>
								<span class="property-banner__meta-value">
									<?php echo esc_html($features); ?>
								</span>
							</span>
						</div>
					<?php endif; ?>

				</div>
				<div class="social-bar">
					<div class="text" style="display:none">
						<p>Share</p>
					</div>
					<div class="buttons">
						<?php if (!empty($social_links)): ?>
							<?php foreach ($social_links as $social):
								$platform = $social['platform'] ?? 'email';
								$url = trim((string) ($social['url'] ?? ''));
								$icon = $social['icon'] ?? null;
								$icon_src = '';
								if ('custom' === $platform && $icon && is_array($icon) && !empty($icon['url'])) {
									$icon_src = esc_url($icon['url']);
								} elseif (isset($default_social_icons[$platform])) {
									$icon_src = esc_url($default_social_icons[$platform]);
								}
								if ($url === '' || $icon_src === '') {
									continue;
								}
								?>
								<a href="<?php echo esc_url($url); ?>" <?php echo ('email' !== $platform) ? ' target="_blank" rel="noopener"' : ''; ?>><img src="<?php echo $icon_src; ?>"
										alt="<?php echo esc_attr(ucfirst($platform)); ?>"></a>
							<?php endforeach; ?>
						<?php else: ?>
							<a href="#"><img src="/wp-content/uploads/2026/08/Icons-3.png"></a>
							<a href="#"><img src="/wp-content/uploads/2026/08/Vector-1-1.png"></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>