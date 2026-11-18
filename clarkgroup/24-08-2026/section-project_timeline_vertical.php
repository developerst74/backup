<?php
/**
 * Project Section - Project Timeline (Vertical + Image)
 * Layout name: project_timeline_vertical
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title         = get_sub_field( 'title' ) ?: 'Project Timeline';
$subtitle      = get_sub_field( 'subtitle' );
$subtitle_link = get_sub_field( 'subtitle_link' );
$image         = get_sub_field( 'image' );
$steps         = get_sub_field( 'steps' );

if ( empty( $steps ) ) {
	return;
}

$subtitle_url = '';
if ( $subtitle_link && is_array( $subtitle_link ) && ! empty( $subtitle_link['url'] ) ) {
	$subtitle_url = esc_url( $subtitle_link['url'] );
}
?>
<section class="cg-timeline-v">
	<div class="cg-timeline-v__inner">

		<div class="cg-timeline-v__list">
			<h2 class="cg-timeline-v__title"><?php echo esc_html( $title ); ?></h2>

			<?php if ( $subtitle ) : ?>
				<p class="cg-timeline-v__subtitle">
					<?php if ( $subtitle_url ) : ?>
						<a href="<?php echo $subtitle_url; ?>" target="_blank" rel="noopener"><?php echo esc_html( $subtitle ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $subtitle ); ?>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<ol class="cg-timeline-v__steps">
				<?php foreach ( $steps as $step ) :
					$active = ! empty( $step['is_active'] );
					$icon   = $step['icon'] ?? null;
				?>
				<li class="cg-timeline-v__step<?php echo $active ? ' is-active' : ''; ?>">
					<?php if ( $icon && is_array( $icon ) && ! empty( $icon['url'] ) ) : ?>
						<span class="cg-timeline-v__step-icon" aria-hidden="true">
							<img src="<?php echo esc_url( $icon['url'] ); ?>" alt="" width="20" height="20">
						</span>
					<?php else : ?>
						<span class="cg-timeline-v__marker" aria-hidden="true"></span>
					<?php endif; ?>
					<div class="cg-timeline-v__content">
						<span class="cg-timeline-v__label"><?php echo esc_html( $step['label'] ); ?></span>
						<span class="cg-timeline-v__status"><?php echo esc_html( $step['status'] ?: 'Details Coming Soon' ); ?></span>
					</div>
				</li>
				<?php endforeach; ?>
			</ol>
		</div>

		<?php if ( $image ) : ?>
		<div class="cg-timeline-v__media">
			<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy">
		</div>
		<?php endif; ?>

	</div>
</section>
