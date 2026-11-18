<?php
namespace ST_Elementor\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loader class to handle all registrations.
 */
class Loader {
	public function __construct() {
		$this->includes();
		$this->init_modules();
	}

	private function includes() {
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/class-widgets.php';
		require_once ST_ELEMENTOR_PATH . 'modules/templates/class-templates.php';
		require_once ST_ELEMENTOR_PATH . 'modules/popup/class-popup.php';
		require_once ST_ELEMENTOR_PATH . 'modules/login/class-login.php';
		require_once ST_ELEMENTOR_PATH . 'modules/extensions/class-sticky.php';
	}

	private function init_modules() {
		new \ST_Elementor\Modules\Widgets\Widgets();
		new \ST_Elementor\Modules\Templates\Templates();
		new \ST_Elementor\Modules\Popup\Popup();
		new \ST_Elementor\Modules\Login\Login();
		new \ST_Elementor\Modules\Extensions\Sticky();
	}
}
