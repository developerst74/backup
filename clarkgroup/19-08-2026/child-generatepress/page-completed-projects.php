<?php
/**
 * Template Name: Completed Projects
 * Description: Grid listing of all Projects where cg_project_status = completed.
 */

get_header(); ?>

<header class="cg-projects-hero">
	<div class="cg-projects-hero__overlay"></div>
	<div class="cg-projects-hero__inner">
		<h1 class="cg-projects-hero__title">Completed Projects</h1>
		<p class="cg-projects-hero__subtitle">
			<?php the_content(); ?>
		</p>
		<a href="#cg-projects-grid" class="cg-projects-hero__cta">VIEW ALL</a>
	</div>
</header>

<div id="cg-projects-grid" class="cg-projects-grid-wrap">
	<div class="cg-projects-grid">
		<?php
		$query = new WP_Query( array(
			'post_type'      => 'projects',
			'posts_per_page' => -1,
			'meta_key'       => 'cg_project_status',
			'meta_value'     => 'completed',
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
		) );

		if ( $query->have_posts() ) :
			$i = 0;
			while ( $query->have_posts() ) : $query->the_post();
				$i++;
				set_query_var( 'cg_card_index', $i );
				get_template_part( 'template-parts/project-card' );
			endwhile;
			wp_reset_postdata();
		else :
			echo '<p class="cg-projects-empty">No completed projects yet.</p>';
		endif;
		?>
	</div>
</div>

<?php get_footer(); ?>
