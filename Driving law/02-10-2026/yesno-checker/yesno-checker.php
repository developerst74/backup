<?php
/**
 * Plugin Name: Yes/No Checker Widget
 * Description: Elementor widget with Yes/No questions and dynamic result descriptions
 * Version: 1.0.0
 * Author: Custom
 * Elementor tested up to: 3.20.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'YESNO_CHECKER_PATH', plugin_dir_path( __FILE__ ) );
define( 'YESNO_CHECKER_URL', plugin_dir_url( __FILE__ ) );

// Register widget after Elementor loads
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    require_once YESNO_CHECKER_PATH . 'widget.php';
    $widgets_manager->register( new \YesNo_Checker_Widget() );
});

// Enqueue frontend assets
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'yesno-checker-style',
        YESNO_CHECKER_URL . 'assets/style.css',
        [],
        '1.0.0'
    );
    wp_enqueue_script(
        'yesno-checker-script',
        YESNO_CHECKER_URL . 'assets/script.js',
        [],
        '1.0.0',
        true
    );
});

// Also enqueue in Elementor editor preview
add_action( 'elementor/preview/enqueue_styles', function() {
    wp_enqueue_style(
        'yesno-checker-style',
        YESNO_CHECKER_URL . 'assets/style.css',
        [],
        '1.0.0'
    );
});
add_action( 'elementor/preview/enqueue_scripts', function() {
    wp_enqueue_script(
        'yesno-checker-script',
        YESNO_CHECKER_URL . 'assets/script.js',
        [],
        '1.0.0',
        true
    );
});
