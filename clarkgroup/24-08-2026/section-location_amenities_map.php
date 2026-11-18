<?php
/**
 * Project Section — Location + Amenities Map (numbered legend)
 * Layout name: location_amenities_map
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title        = get_sub_field( 'title' );
$text         = get_sub_field( 'text' );
$link_text    = get_sub_field( 'link_text' );
$link         = get_sub_field( 'link' );
$map_image    = get_sub_field( 'map_image' );
$map_mode     = get_sub_field( 'map_mode' ) ?: 'image';
$iframe_embed = get_sub_field( 'iframe_embed' );
$points       = get_sub_field( 'points' );

if ( ! $title || ! $map_image ) {
	return;
}
?>
<section class="cg-amenities">
	<div class="cg-amenities__inner">
		<h2 class="cg-amenities__title"><?php echo esc_html( $title ); ?></h2>

		<?php if ( $text ) : ?>
			<p class="cg-amenities__text">
				<?php echo esc_html( $text ); ?>
				<?php if ( $link_text && $link ) : ?>
					<a href="<?php echo esc_url( $link['url'] ); ?>" class="cg-amenities__link"><?php echo esc_html( $link_text ); ?></a>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>

	<div class="cg-amenities__body">
		<div class="cg-amenities__map">
			<?php if ( 'iframe' === $map_mode && $iframe_embed ) : ?>
				<?php echo $iframe_embed; // phpcs:ignore -- admin-entered embed code, trusted content editors only. ?>
			<?php else : ?>
				<img src="<?php echo esc_url( $map_image['url'] ); ?>" alt="<?php echo esc_attr( $map_image['alt'] ); ?>" loading="lazy">

				<?php if ( ! empty( $points ) ) : ?>
					<?php foreach ( $points as $i => $point ) :
						$top  = trim( (string) ( $point['position_top'] ?? '' ) );
						$left = trim( (string) ( $point['position_left'] ?? '' ) );
						if ( $top === '' || $left === '' ) {
							continue; // no position set for this point, skip the overlay pin (legend below still shows it)
						}
						$highlighted = ! empty( $point['is_highlighted'] );
					?>
						<span
							class="cg-amenities__pin<?php echo $highlighted ? ' cg-amenities__pin--highlight' : ''; ?>"
							style="top:<?php echo esc_attr( rtrim( $top, '%' ) ); ?>%; left:<?php echo esc_attr( rtrim( $left, '%' ) ); ?>%;"
						>
							<?php echo esc_html( $i + 1 ); ?>
						</span>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $points ) ) : ?>
		<ul class="cg-amenities__legend">
			<?php foreach ( $points as $i => $point ) : ?>
			<li class="cg-amenities__legend-item<?php echo ! empty( $point['is_highlighted'] ) ? ' cg-amenities__legend-item--highlight' : ''; ?>">
				<span class="cg-amenities__legend-num"><?php echo esc_html( $i + 1 ); ?></span>
				<span class="cg-amenities__legend-label"><?php echo esc_html( $point['label'] ); ?></span>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</div>
</section>
