<?php
namespace ST_Elementor\Modules\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widgets
 */
class Widgets {
	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_categories' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	public function register_categories( $elements_manager ) {
		$categories = $elements_manager->get_categories();
		if ( ! isset( $categories['st-store-transform'] ) ) {
			$elements_manager->add_category(
				'st-store-transform',
				array(
					'title' => __( 'Store Transform', 'st-elementor' ),
					'icon'  => 'eicon-font',
				)
			);
		}
	}

	public function register_widgets( $widgets_manager ) {
		try {
			$this->includes();

			// Register individual widgets
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Slider' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Slider() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Accordion' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Accordion() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Pricing_Grid' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Pricing_Grid() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Login_Widget' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Login_Widget() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Template_Widget' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Template_Widget() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Site_Logo' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Site_Logo() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Nav_Menu' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Nav_Menu() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Flex_Box' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Flex_Box() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Flip_Box' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Flip_Box() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Popup_Button' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Popup_Button() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Flexible_Layout' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Flexible_Layout() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Table_Of_Contents' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Table_Of_Contents() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Footer_Menu' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Footer_Menu() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Hotspot' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Hotspot() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Project_Details' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Project_Details() );
			}
			if ( class_exists( '\ST_Elementor\Modules\Widgets\Elements\ST_Projects_List' ) ) {
				$widgets_manager->register( new \ST_Elementor\Modules\Widgets\Elements\ST_Projects_List() );
			}
		} catch ( \Throwable $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'ST Elementor Widget Registration Error: ' . $e->getMessage() );
			}
		}
	}

	private function includes() {
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/slider.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/accordion.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/image-box.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/price-table.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/pricing-grid.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/login-widget.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/template-widget.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/site-logo.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/nav-menu.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/flex-box.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/flip-box.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/popup-button.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/flexible-layout.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/table-of-contents.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/footer-menu.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/hotspot.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/project-details.php';
		require_once ST_ELEMENTOR_PATH . 'modules/widgets/elements/projects-list.php';
	}
}
