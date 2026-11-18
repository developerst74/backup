<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit;

class ST_Footer_Menu extends Widget_Base {

	public function get_name()       { return 'st_footer_menu'; }
	public function get_title()      { return __( 'ST Footer Menu', 'st-elementor' ); }
	public function get_icon()       { return 'eicon-nav-menu'; }
	public function get_categories() { return [ 'st-store-transform' ]; }

	private function get_menus() {
		$menus = wp_get_nav_menus();
		$opts  = [ '' => __( '— Select Menu —', 'st-elementor' ) ];
		foreach ( $menus as $m ) {
			$opts[ $m->term_id ] = $m->name;
		}
		return $opts;
	}

	protected function register_controls() {

		// ── Content ──────────────────────────────────────────────────
		$this->start_controls_section( 'section_menu', [
			'label' => __( 'Footer Menu', 'st-elementor' ),
		] );

		$this->add_control( 'menu_id', [
			'label'   => __( 'Select Menu', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => $this->get_menus(),
			'default' => '',
		] );

		$this->add_control( 'layout', [
			'label'   => __( 'Layout', 'st-elementor' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => [
				'horizontal' => [ 'title' => __( 'Horizontal', 'st-elementor' ), 'icon' => 'eicon-h-align-left' ],
				'vertical'   => [ 'title' => __( 'Vertical', 'st-elementor' ),   'icon' => 'eicon-v-align-top' ],
			],
			'default' => 'horizontal',
			'toggle'  => false,
		] );

		$this->add_responsive_control( 'align', [
			'label'     => __( 'Alignment', 'st-elementor' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'flex-start' => [ 'title' => 'Left',   'icon' => 'eicon-h-align-left' ],
				'center'     => [ 'title' => 'Center', 'icon' => 'eicon-h-align-center' ],
				'flex-end'   => [ 'title' => 'Right',  'icon' => 'eicon-h-align-right' ],
			],
			'default'   => 'flex-start',
			'selectors' => [ '{{WRAPPER}} .st-fm-list' => 'justify-content: {{VALUE}};' ],
			'condition' => [ 'layout' => 'horizontal' ],
		] );

		$this->end_controls_section();

		// ── Style: Wrapper ───────────────────────────────────────────
		$this->start_controls_section( 'style_wrap', [
			'label' => __( 'Wrapper', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'wrap_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [ '{{WRAPPER}} .st-fm-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Style: Menu Items ────────────────────────────────────────
		$this->start_controls_section( 'style_items', [
			'label' => __( 'Menu Items', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'item_typo',
			'selector' => '{{WRAPPER}} .st-fm-list a',
		] );

		$this->add_responsive_control( 'item_spacing', [
			'label'     => __( 'Item Spacing', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'   => [ 'size' => 16 ],
			'selectors' => [
				'{{WRAPPER}} .st-fm-list.st-fm-horizontal li' => 'margin-right: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .st-fm-list.st-fm-vertical li'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'item_padding', [
			'label'      => __( 'Item Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'selectors'  => [ '{{WRAPPER}} .st-fm-list a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'item_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-fm-list a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->start_controls_tabs( 'item_tabs' );

		$this->start_controls_tab( 'tab_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );
		$this->add_control( 'item_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6b7280',
			'selectors' => [ '{{WRAPPER}} .st-fm-list a' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'item_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-fm-list a' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_hover', [ 'label' => __( 'Hover', 'st-elementor' ) ] );
		$this->add_control( 'item_hover_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-fm-list a:hover' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'item_hover_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-fm-list a:hover' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_active', [ 'label' => __( 'Active', 'st-elementor' ) ] );
		$this->add_control( 'item_active_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-fm-list li.current-menu-item > a' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'item_active_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-fm-list li.current-menu-item > a' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control( 'divider_heading', [
			'label'     => __( 'Divider', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'show_divider', [
			'label'        => __( 'Show Divider', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
		] );

		$this->add_control( 'divider_color', [
			'label'     => __( 'Divider Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#374151',
			'condition' => [ 'show_divider' => 'yes' ],
			'selectors' => [
				'{{WRAPPER}} .st-fm-list.st-fm-divider li:not(:last-child)::after' => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'divider_size', [
			'label'     => __( 'Divider Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 1, 'max' => 20 ] ],
			'default'   => [ 'size' => 1 ],
			'condition' => [ 'show_divider' => 'yes' ],
			'selectors' => [
				'{{WRAPPER}} .st-fm-list.st-fm-divider.st-fm-horizontal li:not(:last-child)::after' => 'width: {{SIZE}}{{UNIT}}; height: 14px;',
				'{{WRAPPER}} .st-fm-list.st-fm-divider.st-fm-vertical li:not(:last-child)::after'   => 'height: {{SIZE}}{{UNIT}}; width: 100%;',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$layout = $s['layout'] ?? 'horizontal';

		if ( empty( $s['menu_id'] ) ) {
			echo '<p style="padding:10px;color:#9ca3af">' . esc_html__( 'Please select a menu.', 'st-elementor' ) . '</p>';
			return;
		}

		$show_divider = 'yes' === ( $s['show_divider'] ?? '' );
		$list_class   = 'st-fm-list st-fm-' . esc_attr( $layout );
		if ( $show_divider ) $list_class .= ' st-fm-divider';

		$menu_html = wp_nav_menu( [
			'menu'            => $s['menu_id'],
			'container'       => false,
			'menu_class'      => $list_class,
			'echo'            => false,
			'fallback_cb'     => false,
			'depth'           => 1, // Only top-level — no dropdowns
		] );

		if ( ! $menu_html ) {
			echo '<p style="padding:10px;color:#9ca3af">' . esc_html__( 'Menu not found.', 'st-elementor' ) . '</p>';
			return;
		}

		echo '<div class="st-fm-wrap">' . $menu_html . '</div>'; // phpcs:ignore
	}
}
