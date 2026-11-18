<?php
/**
 * Hello Elementor Child Theme Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Child Theme Assets
 */
function drivinglaw_child_enqueue_assets() {

	// Child Theme Style
	wp_enqueue_style(
		'drivinglaw-child-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// Custom CSS
	$custom_css = get_stylesheet_directory() . '/assets/css/custom.css';

	if ( file_exists( $custom_css ) ) {
		wp_enqueue_style(
			'drivinglaw-custom-css',
			get_stylesheet_directory_uri() . '/assets/css/custom.css',
			array( 'drivinglaw-child-style' ),
			filemtime( $custom_css )
		);
	}

	// Custom JS
	$custom_js = get_stylesheet_directory() . '/assets/js/custom.js';

	if ( file_exists( $custom_js ) ) {
		wp_enqueue_script(
			'drivinglaw-custom-js',
			get_stylesheet_directory_uri() . '/assets/js/custom.js',
			array( 'jquery' ),
			filemtime( $custom_js ),
			true
		);
	}

}
add_action( 'wp_enqueue_scripts', 'drivinglaw_child_enqueue_assets', 20 );


/**
 * Reading Time Shortcode
 * Usage: [reading_time]
 */
function da_reading_time_shortcode() {
    global $post;

    if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
        return '';
    }

    $content = get_post_field( 'post_content', $post->ID );

    // Remove shortcodes and HTML
    $content = strip_shortcodes( $content );
    $content = wp_strip_all_tags( $content );

    // Count words
    $word_count = str_word_count( $content );

    // Average reading speed: 200 words per minute
    $reading_time = max( 1, ceil( $word_count / 200 ) );

    return sprintf(
        esc_html__( '%d MIN READ', 'hello-elementor-child' ),
        $reading_time
    );
}

add_shortcode( 'reading_time', 'da_reading_time_shortcode' );


// ... your existing theme code above ...

require_once get_stylesheet_directory() . '/court-finder-shortcode.php';


	/**
 * Load Penalty Estimator
 */
function dl_load_penalty_estimator() {

    $penalty_estimator_file = get_stylesheet_directory() . '/inc/penalty-estimator/penalty-estimator.php';
    $penalty_estimator_admin = get_stylesheet_directory() . '/inc/penalty-estimator/penalty-estimator-admin.php';

    // Load main calculator file
    if ( file_exists( $penalty_estimator_file ) ) {
        require_once $penalty_estimator_file;
    }

    // Load admin file
    if ( file_exists( $penalty_estimator_admin ) ) {
        require_once $penalty_estimator_admin;
    }

    // Initialize main calculator
    if ( class_exists( 'DL_Penalty_Estimator' ) ) {
        DL_Penalty_Estimator::init();
    }

    // Initialize admin settings
    if ( class_exists( 'DL_Penalty_Estimator_Admin' ) ) {
        DL_Penalty_Estimator_Admin::init();
    }
}

add_action( 'after_setup_theme', 'dl_load_penalty_estimator', 20 );