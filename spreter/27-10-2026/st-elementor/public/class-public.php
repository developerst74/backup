<?php
namespace ST_Elementor\Public_Area;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Public_Area {
	public function __construct() {
		// Priority 20 — runs after Elementor (priority 10) has registered its handles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
		// Inject hide-theme-header CSS into Elementor preview iframe
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );
	}

	public function enqueue_scripts() {
		// Main public CSS
		wp_enqueue_style( 'st-elementor-public', ST_ELEMENTOR_URL . 'assets/css/public.css', array(), ST_ELEMENTOR_VERSION );

		// Nav menu CSS + JS
		wp_enqueue_style( 'st-nav-menu', ST_ELEMENTOR_URL . 'assets/css/nav-menu.css', array(), ST_ELEMENTOR_VERSION );
		wp_enqueue_script( 'st-nav-menu', ST_ELEMENTOR_URL . 'assets/js/nav-menu.js', array( 'jquery' ), ST_ELEMENTOR_VERSION, true );

		// Main public JS
		wp_enqueue_script( 'st-elementor-public', ST_ELEMENTOR_URL . 'assets/js/public.js', array( 'jquery' ), ST_ELEMENTOR_VERSION, true );

		wp_localize_script( 'st-elementor-public', 'stLoginData', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'st_ajax_login' ),
		) );

		// Hide header/footer on custom login page
		$login_page_id = (int) get_option( 'st_login_page_id', 0 );
		if ( $login_page_id && is_page( $login_page_id ) ) {
			wp_add_inline_style( 'st-elementor-public',
				'header,footer,#masthead,#colophon,.site-header,.site-footer,#header,#footer,.header,.footer,.st-custom-header,.st-custom-footer,nav.navbar,.navbar,.main-navigation{display:none!important}'
			);
		}	}

	public function enqueue_preview_styles() {
		// Hide ALL theme header/footer inside Elementor preview iframe
		$css = '
			header,
			footer,
			#masthead,
			#colophon,
			#site-header,
			#site-footer,
			.site-header,
			.site-footer,
			#header,
			#footer,
			.header,
			.footer,
			nav.navbar,
			.navbar,
			.main-navigation,
			#main-navigation,
			.primary-navigation,
			#primary-navigation,
			.st-custom-header,
			.st-custom-footer {
				display: none !important;
			}
		';
		wp_add_inline_style( 'elementor-frontend', $css );
	}
}
