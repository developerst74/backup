<?php
/**
 * Project Section — Image Collage (5-tile mosaic)
 * Layout name: image_collage
 *
 * A mosaic of 5 images in the "enclave-collage" pattern:
 *   - Tile 1 (first row): large image on the LEFT, spans BOTH rows
 *   - Tiles 2–5: a 2×2 grid on the right (top-left, top-right, bottom-left, bottom-right)
 * Each tile is a <button> that opens the original image in a full-screen
 * lightbox, using the existing data-enlarge / image-dialog wiring that
 * the shared design pages already ship with (the pages.css + gallery JS).
 *
 * Matches the reference HTML:
 *   <section class="gallery-section collage-section">
 *     <div class="section-row">
 *       <div><p class="eyebrow">...</p><h2>...</h2></div>
 *       <p>...</p>
 *     </div>
 *     <div class="enclave-collage">
 *       <button class="collage-tile" data-enlarge>
 *         <img src="..." alt="...">
 *         <span>Caption <b>↗</b></span>
 *       </button>
 *       ...
 *     </div>
 *   </section>
 *
 * ACF sub_fields: eyebrow, title, intro, tiles (repeater > image, caption)
 */

if (!defined('ABSPATH')) {
	exit;
}

$eyebrow = trim((string) get_sub_field('eyebrow'));
$title   = get_sub_field('title');
$intro   = trim((string) get_sub_field('intro'));
$tiles   = get_sub_field('tiles');

// A title is required; otherwise the whole section is skipped.
if (!$title) {
	return;
}

// Flatten tiles, skipping rows without an image.
$tile_data = array();
if (is_array($tiles)) {
	foreach ($tiles as $t) {
		$img = !empty($t['image']) ? $t['image'] : null;
		if (!is_array($img) || empty($img['url'])) {
			continue;
		}
		$tile_data[] = array(
			'url'     => esc_url($img['url']),
			'alt'     => !empty($img['alt']) ? esc_attr($img['alt']) : '',
			'caption' => trim((string) ($t['caption'] ?? '')),
		);
	}
}

if (!$tile_data) {
	return;
}

$row = get_row_index();
$heading_id = 'project-collage-' . $row . '-title';
?>
<section class="gallery-section collage-section section-pad" aria-labelledby="<?php echo esc_attr($heading_id); ?>">
	<div class="section-row">
		<div>
			<?php if ($eyebrow !== '') : ?>
				<p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
			<?php endif; ?>
			<h2 id="<?php echo esc_attr($heading_id); ?>"><?php echo wp_kses_post($title); ?></h2>
		</div>
		<?php if ($intro !== '') : ?>
			<p><?php echo esc_html($intro); ?></p>
		<?php endif; ?>
	</div>

	<div class="enclave-collage">
		<?php foreach ($tile_data as $i => $tile) :
			$label = $tile['caption'] !== ''
				? sprintf(esc_attr__('Enlarge: %s', 'child-generatepress'), $tile['caption'])
				: esc_attr__('Enlarge image', 'child-generatepress');
			?>
			<button class="collage-tile"
				type="button"
				data-enlarge
				aria-label="<?php echo $label; ?>"
				data-src="<?php echo esc_attr($tile['url']); ?>">
				<img src="<?php echo esc_url($tile['url']); ?>"
					alt="<?php echo esc_attr($tile['alt']); ?>"
					loading="lazy">
				<?php if ($tile['caption'] !== '') : ?>
					<span><?php echo esc_html($tile['caption']); ?> <b aria-hidden="true">&#8599;</b></span>
				<?php endif; ?>
			</button>
		<?php endforeach; ?>
	</div>
</section>
