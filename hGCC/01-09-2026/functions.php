<?php
/**
 * HGCC Child Theme Functions
 *
 * @package HGCC_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue parent and child theme styles.
 */
function hgcc_enqueue_styles() {

	wp_enqueue_style(
		'hello-elementor-parent',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( 'hello-elementor' )->get( 'Version' )
	);

	wp_enqueue_style(
		'hgcc-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'hello-elementor-parent' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'hgcc_enqueue_styles' );

function hgcc_enqueue_custom_css() {
    wp_enqueue_style(
        'hgcc-custom-css',
        get_template_directory_uri() . '/assets/css/custom.css',
        array(),
        filemtime(get_template_directory() . '/assets/css/custom.css')
    );
}
add_action('wp_enqueue_scripts', 'hgcc_enqueue_custom_css');