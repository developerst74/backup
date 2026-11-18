<?php
/**
 * Project Section — Overview
 * Layout name: overview
 *
 * Falls back to cg_project_short_description / cg_project_gallery
 * at the top level when the section-level fields are left empty.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$project_id = get_the_ID();

$title = get_sub_field( 'title' ) ?: 'Overview';
$text  = get_sub_field( 'text' ) ?: get_field( 'cg_project_short_description', $project_id );

$gallery = get_sub_field( 'gallery' );
if ( ! $gallery ) {
	$fallback_ids = get_field( 'cg_project_gallery', $project_id );
	if ( $fallback_ids ) {
		$gallery = array();
		foreach ( (array) $fallback_ids as $img_id ) {
			$gallery[] = array(
				'url' => wp_get_attachment_image_url( $img_id, 'large' ),
				'alt' => get_post_meta( $img_id, '_wp_attachment_image_alt', true ),
			);
		}
	}
}

/* New, optional: single large image with numbered feature cards overlaid
 * at the bottom, plus a CTA (e.g. Download Brochure). Independent of the
 * Gallery/swiper above — safe to use either or both. */
$main_image = get_sub_field( 'main_image' );
$cards      = get_sub_field( 'cards' );
$cta_text   = get_sub_field( 'cta_text' );
$cta_link   = get_sub_field( 'cta_link' );
?>
<section class="cg-project-overview">
	<div class="cg-project-overview__inner">

		<?php if ( $title ) : ?>
			<h2><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<div class="cg-project-overview__text">
				<?php echo wp_kses_post( wpautop( $text ) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $gallery ) ) : ?>
		<div class="cg-project-overview__gallery swiper">
			<div class="swiper-wrapper">
				<?php foreach ( $gallery as $img ) : ?>
					<div class="swiper-slide">
						<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ?? '' ); ?>" loading="lazy">
					</div>
				<?php endforeach; ?>
			</div>
			<div class="swiper-pagination"></div>
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
		</div>
		<?php endif; ?>

		<?php if ( $main_image ) : ?>
		<div class="cg-project-overview__main">
			<img class="cg-project-overview__main-image" src="<?php echo esc_url( $main_image['url'] ); ?>" alt="<?php echo esc_attr( $main_image['alt'] ); ?>" loading="lazy">

			<?php if ( ! empty( $cards ) ) : ?>
			<div class="cg-project-overview__cards">
				<?php foreach ( $cards as $i => $card ) :
					$number = trim( (string) ( $card['number'] ?? '' ) );
					if ( $number === '' ) {
						$number = str_pad( $i + 1, 2, '0', STR_PAD_LEFT );
					}
				?>
				<div class="cg-project-overview__card">
					<span class="cg-project-overview__card-number"><?php echo esc_html( $number ); ?></span>
					<?php if ( ! empty( $card['title'] ) ) : ?>
						<h3 class="cg-project-overview__card-title"><?php echo esc_html( $card['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $card['text'] ) ) : ?>
						<p class="cg-project-overview__card-text"><?php echo esc_html( $card['text'] ); ?></p>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if ( $cta_text && $cta_link && ! empty( $cta_link['url'] ) ) : ?>
			<div class="cg-project-overview__cta">
				<a class="black-button" href="<?php echo esc_url( $cta_link['url'] ); ?>">
					<?php echo esc_html( $cta_text ); ?> →
				</a>
			</div>
		<?php endif; ?>

	</div>
</section>
