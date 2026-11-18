<?php
/**
 * Project Section — Editorial (asymmetric 2-col text)
 * Layout name: editorial
 *
 * An asymmetric two-column editorial block. The left column holds a
 * small eyebrow/eyebrow heading, the right column holds a large serif
 * title, an optional lead paragraph (larger type), body paragraphs,
 * and an optional CTA button with the dark-outline style.
 *
 * Matches the reference HTML (from pages.css):
 *   <section class="editorial section-pad">
 *     <p class="eyebrow">...</p>
 *     <div>
 *       <h2>...</h2>
 *       <p class="lead">...</p>
 *       <p>...</p>
 *       <a class="outline-link dark-outline" href="...">...</a>
 *     </div>
 *   </section>
 *
 * ACF sub_fields: eyebrow, title, lead, body (wysiwyg), cta_text, cta_link
 */

if (!defined('ABSPATH')) {
	exit;
}

$eyebrow = trim((string) get_sub_field('eyebrow'));
$title   = get_sub_field('title');
$lead    = trim((string) get_sub_field('lead'));
$body    = get_sub_field('body');
$cta_text = trim((string) get_sub_field('cta_text'));
$cta_link = get_sub_field('cta_link');

// A title is required; otherwise the whole section is skipped.
if (!$title) {
	return;
}

$cta_url = '';
$cta_target = '';
if (is_array($cta_link) && !empty($cta_link['url'])) {
	$cta_url = esc_url($cta_link['url']);
	$cta_target = !empty($cta_link['target']) ? ' target="' . esc_attr($cta_link['target']) . '" rel="noopener noreferrer"' : '';
}

$row = get_row_index();
$heading_id = 'project-editorial-' . $row . '-title';
?>
<section class="editorial section-pad" aria-labelledby="<?php echo esc_attr($heading_id); ?>">
	<?php if ($eyebrow !== '') : ?>
		<p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
	<?php endif; ?>
	<div>
		<h2 id="<?php echo esc_attr($heading_id); ?>"><?php echo wp_kses_post($title); ?></h2>

		<?php if ($lead !== '') : ?>
			<p class="lead"><?php echo esc_html($lead); ?></p>
		<?php endif; ?>

		<?php if ($body) : ?>
			<?php echo wp_kses_post(wpautop($body)); ?>
		<?php endif; ?>

		<?php if ($cta_text !== '') : ?>
			<a class="outline-link dark-outline"
				href="<?php echo $cta_url ? $cta_url : '#contact'; ?>"<?php echo $cta_target; /* escaped above */ ?>>
				<span class="arrow-link-inner">
					<span><?php echo esc_html($cta_text); ?></span>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M5 12h14"/>
						<path d="m12 5 7 7-7 7"/>
					</svg>
				</span>
			</a>
		<?php endif; ?>
	</div>
</section>
