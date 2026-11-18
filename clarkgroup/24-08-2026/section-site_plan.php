<?php
/**
 * Project Section — Site Plan (Coming Soon watermark variant)
 * Layout name: site_plan
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title       = get_sub_field( 'title' ) ?: 'Site Plan';
$text        = get_sub_field( 'text' );
$bg          = get_sub_field( 'background_image' );
$coming_soon = get_sub_field( 'coming_soon' );
?>
<section class="cg-siteplan"
	<?php if ( $bg ) : ?>
		style="background-image:url('<?php echo esc_url( $bg['url'] ); ?>')"
	<?php endif; ?>>
	<div class="cg-siteplan__inner">
		<h2 class="cg-siteplan__title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $text ) : ?>
			<p class="cg-siteplan__text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( $coming_soon ) : ?>
		<span class="cg-siteplan__watermark" aria-hidden="true"><?php esc_html_e( 'Coming Soon', 'child-generatepress' ); ?></span>
	<?php endif; ?>
</section>
