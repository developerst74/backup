<?php
/**
 * Project card partial.
 *
 * Usage:
 *   set_query_var( 'cg_card_index', $i ); // 1-based counter for the "01", "02" numbering
 *   get_template_part( 'template-parts/project-card' );
 *
 * Must be called inside the loop (uses get_the_ID(), get_permalink(), etc.)
 */

$post_id       = get_the_ID();
$index         = get_query_var( 'cg_card_index', 1 );
$hero_id       = get_field( 'cg_project_hero_image', $post_id );
$project_title = get_field( 'cg_project_title', $post_id );
$short_desc    = get_field( 'cg_project_short_description', $post_id );
$for_sale      = get_field( 'cg_project_for_sale', $post_id );
$status        = get_field( 'cg_project_status', $post_id );
$terms         = get_the_terms( $post_id, 'project_category' );
$category_name = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0]->name : '';

if ( ! $project_title ) {
	$project_title = get_the_title();
}
?>
<article class="cg-project-card">
	<a href="<?php echo esc_url( get_permalink() ); ?>" class="cg-project-card__link">

		<div class="cg-project-card__media">
			<?php if ( $hero_id ) : ?>
				<?php echo wp_get_attachment_image( $hero_id, 'large', false, array( 'class' => 'cg-project-card__img' ) ); ?>
			<?php elseif ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'large', array( 'class' => 'cg-project-card__img' ) ); ?>
			<?php endif; ?>

			<div class="cg-project-card__overlay"></div>

			<?php if ( $category_name ) : ?>
				<span class="cg-project-card__badge cg-project-card__badge--category"><?php echo esc_html( $category_name ); ?></span>
			<?php endif; ?>

			<?php if ( 'yes' === $for_sale ) : ?>
				<span class="cg-project-card__badge cg-project-card__badge--sale">FOR SALE</span>
			<?php endif; ?>

			<div class="cg-project-card__content">
				<h3 class="cg-project-card__title"><?php echo esc_html( $project_title ); ?></h3>

				<?php if ( $short_desc ) : ?>
					<div class="cg-project-card__excerpt"><?php echo wp_kses_post( wpautop( $short_desc ) ); ?></div>
				<?php endif; ?>

				<span class="cg-project-card__learn-more">LEARN MORE <span class="cg-arrow">&rarr;</span></span>
			</div>

			<span class="cg-project-card__number"><?php echo esc_html( sprintf( '%02d', $index ) ); ?></span>
		</div>

	</a>
</article>
