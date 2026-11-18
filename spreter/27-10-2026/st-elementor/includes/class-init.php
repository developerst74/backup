<?php
namespace ST_Elementor\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Init class to initialize the plugin.
 */
class Init {
	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		$this->includes();
		$this->init_components();
	}

	private function includes() {
		require_once ST_ELEMENTOR_PATH . 'includes/class-loader.php';
		require_once ST_ELEMENTOR_PATH . 'admin/class-admin.php';
		require_once ST_ELEMENTOR_PATH . 'public/class-public.php';
	}

	private function init_components() {
		// Initialize Loader
		new Loader();

		// Initialize Admin
		if ( is_admin() ) {
			new \ST_Elementor\Admin\Admin();
		}

		// Initialize Public
		new \ST_Elementor\Public_Area\Public_Area();
	}
}
