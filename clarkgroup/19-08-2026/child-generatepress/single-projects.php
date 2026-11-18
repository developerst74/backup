<?php
/**
 * Single template for the "projects" CPT.
 * Save as single-projects.php in your theme root — WordPress picks this up
 * automatically for single Project posts, no Template Name needed.
 */

get_header();

while ( have_posts() ) : the_post();

	$post_id       = get_the_ID();
	$hero_id       = get_field( 'cg_project_hero_image', $post_id );
	$project_title = get_field( 'cg_project_title', $post_id );
	$short_desc    = get_field( 'cg_project_short_description', $post_id );
	$location      = get_field( 'cg_project_location', $post_id );
	$for_sale      = get_field( 'cg_project_for_sale', $post_id );
	$gallery       = get_field( 'cg_project_gallery', $post_id );

	if ( ! $project_title ) {
		$project_title = get_the_title();
	}
	?>

	<section class="cg-project-single-hero">
		<?php if ( $hero_id ) : ?>
			<?php echo wp_get_attachment_image( $hero_id, 'full', false, array( 'class' => 'cg-project-single-hero__img' ) ); ?>
		<?php elseif ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'full', array( 'class' => 'cg-project-single-hero__img' ) ); ?>
		<?php endif; ?>

		<div class="cg-project-single-hero__overlay"></div>

		<div class="cg-project-single-hero__content">
			<h1 class="cg-project-single-hero__title"><?php echo esc_html( $project_title ); ?></h1>

			<?php if ( $location ) : ?>
				<p class="cg-project-single-hero__location"><?php echo esc_html( $location ); ?></p>
			<?php endif; ?>

			<?php if ( $short_desc ) : ?>
				<div class="cg-project-single-hero__excerpt"><?php echo wp_kses_post( wpautop( $short_desc ) ); ?></div>
			<?php endif; ?>

			<a href="#cg-project-details" class="cg-project-single-hero__cta">LEARN MORE <span class="cg-arrow">&rarr;</span></a>

			<?php if ( 'yes' === $for_sale ) : ?>
				<span class="cg-project-card__badge cg-project-card__badge--sale">FOR SALE</span>
			<?php endif; ?>

			<span class="cg-project-single-hero__number">01</span>
		</div>
	</section>

	<div id="cg-project-details" class="cg-project-single-body">
		<div class="cg-project-single-body__content">
			<?php the_content(); // full write-up, edited in the post editor ?>
		</div>

		<?php if ( $gallery ) : ?>
			<div class="cg-project-gallery">
				<?php foreach ( $gallery as $image_id ) : ?>
					<?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'cg-project-gallery__img' ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php
endwhile;

get_footer();
