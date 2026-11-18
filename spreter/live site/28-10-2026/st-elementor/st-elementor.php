<?php
/**
 * Plugin Name: ST Elementor
 * Description: A custom Elementor addon plugin with widgets, templates, and more.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: st-elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Initialize the plugin using a main class.
 */
final class ST_Elementor {

	/**
	 * Instance of this class.
	 */
	private static $instance = null;

	/**
	 * Get instance of this class.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define plugin constants.
	 */
	private function define_constants() {
		if ( ! defined( 'ST_ELEMENTOR_VERSION' ) ) {
			define( 'ST_ELEMENTOR_VERSION', '1.0.0' );
		}
		if ( ! defined( 'ST_ELEMENTOR_FILE' ) ) {
			define( 'ST_ELEMENTOR_FILE', __FILE__ );
		}
		if ( ! defined( 'ST_ELEMENTOR_PATH' ) ) {
			define( 'ST_ELEMENTOR_PATH', plugin_dir_path( ST_ELEMENTOR_FILE ) );
		}
		if ( ! defined( 'ST_ELEMENTOR_URL' ) ) {
			define( 'ST_ELEMENTOR_URL', plugin_dir_url( ST_ELEMENTOR_FILE ) );
		}
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		require_once ST_ELEMENTOR_PATH . 'includes/helpers.php';
		require_once ST_ELEMENTOR_PATH . 'includes/class-init.php';
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		// Flush rewrite rules once after post types are registered
		add_action( 'init', array( $this, 'maybe_flush_rewrite' ), 99 );
	}

	public function maybe_flush_rewrite() {
		if ( get_option( 'st_elementor_flush_rewrite' ) !== ST_ELEMENTOR_VERSION ) {
			flush_rewrite_rules();
			update_option( 'st_elementor_flush_rewrite', ST_ELEMENTOR_VERSION );
		}
	}

	/**
	 * Init the plugin logic.
	 */
	public function init_plugin() {
		\ST_Elementor\Includes\Init::instance();
	}
}

/**
 * Global function to return the main instance.
 */
function st_elementor() {
	return ST_Elementor::get_instance();
}

// Start the plugin
st_elementor();

/**
 * Flush rewrite rules on activation so custom post type URLs work immediately.
 */
register_activation_hook( __FILE__, function() {
	require_once plugin_dir_path( __FILE__ ) . 'modules/templates/class-templates.php';
	require_once plugin_dir_path( __FILE__ ) . 'modules/popup/class-popup.php';

	( new \ST_Elementor\Modules\Templates\Templates() )->register_post_type();
	( new \ST_Elementor\Modules\Popup\Popup() )->register_post_type();

	update_option( 'st_elementor_flush_rewrite', ST_ELEMENTOR_VERSION );
	flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function() {
	flush_rewrite_rules();
} );
