<?php
namespace ST_Elementor\Modules\Extensions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds "ST Sticky" controls to Elementor Section and Container elements.
 * Conditions: Always / On Scroll Up / After Offset px
 */
class Sticky {
	private $script_registered = false;

	public function __construct() {
		// Add controls to Section (classic)
		add_action( 'elementor/element/section/section_layout/before_section_end',  array( $this, 'add_controls' ), 10, 2 );
		// Add controls to Container (Flexbox Container)
		add_action( 'elementor/element/container/section_layout/before_section_end', array( $this, 'add_controls' ), 10, 2 );

		// Inject inline CSS + data attributes on frontend
		add_action( 'elementor/frontend/section/before_render',   array( $this, 'before_render' ) );
		add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/* ── Controls ─────────────────────────────────────────────────── */

	public function add_controls( $element, $args ) {

		$element->add_control( 'st_sticky_heading', [
			'label'     => '<span style="color:#6366f1;font-weight:700">⬆ ST Sticky</span>',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$element->add_control( 'st_sticky_enable', [
			'label'        => __( 'Enable Sticky', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
			'description'  => __( 'Makes this section/container sticky on scroll.', 'st-elementor' ),
		] );

		$element->add_control( 'st_sticky_condition', [
			'label'     => __( 'Sticky Condition', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'always',
			'options'   => [
				'always'      => __( 'Always Sticky', 'st-elementor' ),
				'scroll_up'   => __( 'On Scroll Up Only', 'st-elementor' ),
				'after_offset'=> __( 'After Offset (px)', 'st-elementor' ),
			],
			'condition' => [ 'st_sticky_enable' => 'yes' ],
		] );

		$element->add_control( 'st_sticky_offset', [
			'label'       => __( 'Sticky After (px)', 'st-elementor' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 100,
			'min'         => 0,
			'description' => __( 'Scroll this many px before becoming sticky.', 'st-elementor' ),
			'condition'   => [
				'st_sticky_enable'    => 'yes',
				'st_sticky_condition' => 'after_offset',
			],
		] );

		$element->add_control( 'st_sticky_top', [
			'label'      => __( 'Sticky Top Position (px)', 'st-elementor' ),
			'type'       => Controls_Manager::NUMBER,
			'default'    => 0,
			'min'        => 0,
			'condition'  => [ 'st_sticky_enable' => 'yes' ],
			'selectors'  => [ '{{WRAPPER}}.st-is-sticky' => 'top: {{VALUE}}px;' ],
		] );

		$element->add_control( 'st_sticky_z_index', [
			'label'     => __( 'Z-Index (sticky)', 'st-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 999,
			'condition' => [ 'st_sticky_enable' => 'yes' ],
			'selectors' => [ '{{WRAPPER}}.st-is-sticky' => 'z-index: {{VALUE}};' ],
		] );

		// Background when sticky
		$element->add_control( 'st_sticky_bg_heading', [
			'label'     => __( 'Sticky Background', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [ 'st_sticky_enable' => 'yes' ],
		] );

		$element->add_control( 'st_sticky_bg_color', [
			'label'     => __( 'Background Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'condition' => [ 'st_sticky_enable' => 'yes' ],
			'selectors' => [ '{{WRAPPER}}.st-is-sticky' => 'background-color: {{VALUE}} !important;' ],
		] );

		$element->add_control( 'st_sticky_shadow', [
			'label'        => __( 'Add Shadow', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => [ 'st_sticky_enable' => 'yes' ],
			'selectors'    => [ '{{WRAPPER}}.st-is-sticky' => 'box-shadow: 0 2px 20px rgba(0,0,0,.12);' ],
		] );

		$element->add_control( 'st_sticky_transition', [
			'label'     => __( 'Transition Speed (s)', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
			'default'   => [ 'size' => 0.3 ],
			'condition' => [ 'st_sticky_enable' => 'yes' ],
			'selectors' => [ '{{WRAPPER}}' => 'transition: background-color {{SIZE}}s ease, box-shadow {{SIZE}}s ease;' ],
		] );
	}

	/* ── Before render — add data attributes ─────────────────────── */

	public function before_render( $element ) {
		$settings = $element->get_settings_for_display();

		if ( empty( $settings['st_sticky_enable'] ) || 'yes' !== $settings['st_sticky_enable'] ) {
			return;
		}

		if ( $this->script_registered ) {
			wp_enqueue_script( 'st-sticky' );
		}

		$element->add_render_attribute( '_wrapper', [
			'data-st-sticky'           => 'true',
			'data-st-sticky-condition' => $settings['st_sticky_condition'] ?? 'always',
			'data-st-sticky-offset'    => $settings['st_sticky_offset'] ?? 100,
		] );
	}

	public function register_assets() {
		wp_register_script(
			'st-sticky',
			ST_ELEMENTOR_URL . 'assets/js/sticky.js',
			[ 'jquery' ],
			ST_ELEMENTOR_VERSION,
			true
		);
		$this->script_registered = true;
	}
}
