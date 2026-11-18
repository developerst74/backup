<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit;

class ST_Nav_Menu extends Widget_Base {

	public function get_name()           { return 'st_nav_menu'; }
	public function get_title()          { return __( 'ST Nav Menu', 'st-elementor' ); }
	public function get_icon()           { return 'eicon-nav-menu'; }
	public function get_categories()     { return [ 'st-store-transform' ]; }
	public function get_script_depends() { return [ 'st-nav-menu' ]; }
	public function get_style_depends()  { return [ 'st-nav-menu' ]; }

	private function get_menus() {
		$menus = wp_get_nav_menus();
		$opts  = [ '' => __( '— Select Menu —', 'st-elementor' ) ];
		foreach ( $menus as $m ) {
			$opts[ $m->term_id ] = $m->name;
		}
		return $opts;
	}

	protected function register_controls() {

		// ── Menu ─────────────────────────────────────────────────────
		$this->start_controls_section( 'section_menu', [
			'label' => __( 'Navigation Menu', 'st-elementor' ),
		] );

		$this->add_control( 'menu_id', [
			'label'   => __( 'Select Menu', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => $this->get_menus(),
			'default' => '',
		] );

		$this->add_control( 'layout', [
			'label'   => __( 'Layout', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'horizontal',
			'options' => [ 'horizontal' => 'Horizontal', 'vertical' => 'Vertical' ],
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
			'selectors' => [ '{{WRAPPER}} .st-nav-menu' => 'justify-content: {{VALUE}};' ],
			'condition' => [ 'layout' => 'horizontal' ],
		] );

		$this->end_controls_section();

		// ── Sticky ───────────────────────────────────────────────────
		$this->start_controls_section( 'section_sticky', [
			'label' => __( 'Sticky Header', 'st-elementor' ),
		] );

		$this->add_control( 'sticky_enable', [
			'label'        => __( 'Enable Sticky', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
		] );

		$this->add_control( 'sticky_offset', [
			'label'     => __( 'Sticky After (px scroll)', 'st-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 80,
			'min'       => 0,
			'condition' => [ 'sticky_enable' => 'yes' ],
		] );

		$this->add_control( 'default_bg', [
			'label'     => __( 'Default Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-nav-wrap' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'sticky_bg', [
			'label'     => __( 'Sticky Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'condition' => [ 'sticky_enable' => 'yes' ],
			'selectors' => [ '{{WRAPPER}} .st-nav-wrap.st-sticky-active' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'sticky_shadow', [
			'label'        => __( 'Sticky Box Shadow', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => [ 'sticky_enable' => 'yes' ],
		] );

		$this->end_controls_section();

		// ── Dropdown ─────────────────────────────────────────────────
		$this->start_controls_section( 'section_dropdown', [
			'label' => __( 'Dropdown', 'st-elementor' ),
		] );

		$this->add_control( 'dropdown_effect', [
			'label'   => __( 'Open Effect', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'fade',
			'options' => [ 'fade' => 'Fade', 'slide' => 'Slide Down', 'zoom' => 'Zoom In', 'flip' => 'Flip' ],
		] );

		$this->add_control( 'dropdown_trigger', [
			'label'   => __( 'Open On', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'hover',
			'options' => [ 'hover' => 'Hover', 'click' => 'Click' ],
		] );

		$this->add_control( 'show_arrow', [
			'label'        => __( 'Show Dropdown Arrow', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->end_controls_section();

		// ── Mobile / Hamburger ────────────────────────────────────────
		$this->start_controls_section( 'section_mobile', [
			'label' => __( 'Mobile / Hamburger', 'st-elementor' ),
		] );

		$this->add_control( 'mobile_breakpoint', [
			'label'       => __( 'Breakpoint', 'st-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => '768',
			'options'     => [
				'480'  => 'Mobile (480px)',
				'768'  => 'Tablet (768px)',
				'1024' => 'Laptop (1024px)',
			],
		] );

		$this->add_control( 'hamburger_position', [
			'label'   => __( 'Hamburger Position', 'st-elementor' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => [
				'left'  => [ 'title' => 'Left',  'icon' => 'eicon-h-align-left' ],
				'right' => [ 'title' => 'Right', 'icon' => 'eicon-h-align-right' ],
			],
			'default' => 'right',
			'toggle'  => false,
		] );

		$this->add_control( 'mobile_menu_side', [
			'label'   => __( 'Menu Slides From', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'top',
			'options' => [
				'top'     => __( 'Top (Slide Down)', 'st-elementor' ),
				'left'    => __( 'Left Side Panel', 'st-elementor' ),
				'right'   => __( 'Right Side Panel', 'st-elementor' ),
				'overlay' => __( 'Full Screen Overlay', 'st-elementor' ),
			],
		] );

		$this->add_control( 'hamburger_open_icon', [
			'label'   => __( 'Open Icon', 'st-elementor' ),
			'type'    => Controls_Manager::ICONS,
			'default' => [ 'value' => 'fas fa-bars', 'library' => 'fa-solid' ],
		] );

		$this->add_control( 'hamburger_close_icon', [
			'label'   => __( 'Close Icon', 'st-elementor' ),
			'type'    => Controls_Manager::ICONS,
			'default' => [ 'value' => 'fas fa-times', 'library' => 'fa-solid' ],
		] );

		$this->end_controls_section();

		// ── STYLE: Wrapper ───────────────────────────────────────────
		$this->start_controls_section( 'style_wrap', [
			'label' => __( 'Wrapper', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'wrap_bg',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-nav-wrap',
		] );

		$this->add_responsive_control( 'wrap_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-nav-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'wrap_border',
			'selector' => '{{WRAPPER}} .st-nav-wrap',
		] );

		$this->add_responsive_control( 'wrap_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-nav-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'wrap_shadow',
			'selector' => '{{WRAPPER}} .st-nav-wrap',
		] );

		$this->end_controls_section();

		// ── STYLE: Menu Items ─────────────────────────────────────────
		$this->start_controls_section( 'style_items', [
			'label' => __( 'Menu Items', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'item_typo',
			'selector' => '{{WRAPPER}} .st-nav-menu > li > a',
		] );

		$this->add_responsive_control( 'item_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '10', 'right' => '16', 'bottom' => '10', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-nav-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'item_spacing', [
			'label'     => __( 'Item Spacing', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'   => [ 'size' => 4 ],
			'selectors' => [
				'{{WRAPPER}} .st-nav-menu.st-horizontal > li' => 'margin-right: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .st-nav-menu.st-vertical > li'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'item_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-nav-menu > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'item_border',
			'selector' => '{{WRAPPER}} .st-nav-menu > li > a',
		] );

		$this->start_controls_tabs( 'item_tabs' );

		$this->start_controls_tab( 'item_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );
		$this->add_control( 'item_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111827',
			'selectors' => [ '{{WRAPPER}} .st-nav-menu > li > a' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'item_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-nav-menu > li > a' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'item_shadow',
			'selector' => '{{WRAPPER}} .st-nav-menu > li > a',
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'item_hover', [ 'label' => __( 'Hover', 'st-elementor' ) ] );
		$this->add_control( 'item_hover_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-nav-menu > li > a:hover' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'item_hover_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-nav-menu > li > a:hover' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_control( 'item_hover_border_color', [
			'label'     => __( 'Border Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-nav-menu > li > a:hover' => 'border-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'item_active', [ 'label' => __( 'Active', 'st-elementor' ) ] );
		$this->add_control( 'item_active_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-nav-menu > li.current-menu-item > a, {{WRAPPER}} .st-nav-menu > li.current-menu-ancestor > a' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'item_active_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-nav-menu > li.current-menu-item > a, {{WRAPPER}} .st-nav-menu > li.current-menu-ancestor > a' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control( 'active_indicator', [
			'label'     => __( 'Active Indicator', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'separator' => 'before',
			'default'   => 'none',
			'options'   => [ 'none' => 'None', 'underline' => 'Underline', 'dot' => 'Dot', 'left_border' => 'Left Border' ],
		] );
		$this->add_control( 'active_indicator_color', [
			'label'     => __( 'Indicator Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'condition' => [ 'active_indicator!' => 'none' ],
			'selectors' => [
				'{{WRAPPER}} .st-nav-menu > li.current-menu-item > a::after' => 'background-color: {{VALUE}};',
				'{{WRAPPER}} .st-nav-menu > li.current-menu-item > a'        => 'border-left-color: {{VALUE}};',
			],
		] );

		$this->end_controls_section();

		// ── STYLE: Dropdown ───────────────────────────────────────────
		$this->start_controls_section( 'style_dropdown', [
			'label' => __( 'Dropdown', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'dropdown_bg',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-sub-menu',
		] );

		$this->add_responsive_control( 'dropdown_width', [
			'label'      => __( 'Min Width', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 100, 'max' => 600 ] ],
			'default'    => [ 'size' => 220 ],
			'selectors'  => [ '{{WRAPPER}} .st-sub-menu' => 'min-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'dropdown_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top' => '6', 'right' => '0', 'bottom' => '6', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-sub-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'dropdown_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'dropdown_border',
			'selector' => '{{WRAPPER}} .st-sub-menu',
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'dropdown_shadow',
			'selector' => '{{WRAPPER}} .st-sub-menu',
		] );

		$this->add_control( 'dropdown_items_heading', [
			'label'     => __( 'Items', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'dropdown_item_typo',
			'selector' => '{{WRAPPER}} .st-sub-menu li a',
		] );

		$this->add_responsive_control( 'dropdown_item_padding', [
			'label'      => __( 'Item Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top' => '9', 'right' => '18', 'bottom' => '9', 'left' => '18', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-sub-menu li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->start_controls_tabs( 'dropdown_item_tabs' );

		$this->start_controls_tab( 'dd_item_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );
		$this->add_control( 'dropdown_item_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#374151',
			'selectors' => [ '{{WRAPPER}} .st-sub-menu li a' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'dropdown_item_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-sub-menu li a' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'dd_item_hover', [ 'label' => __( 'Hover', 'st-elementor' ) ] );
		$this->add_control( 'dropdown_item_hover_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-sub-menu li a:hover' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'dropdown_item_hover_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-sub-menu li a:hover' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();

		// ── STYLE: Mobile Panel ───────────────────────────────────────
		$this->start_controls_section( 'style_mobile_panel', [
			'label' => __( 'Mobile Panel', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'panel_bg_color', [
			'label'     => __( 'Panel Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '#st-panel-{{ID}}' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'panel_width', [
			'label'      => __( 'Panel Width', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [ 'px' => [ 'min' => 200, 'max' => 600 ], '%' => [ 'min' => 50, 'max' => 100 ] ],
			'default'    => [ 'size' => 320, 'unit' => 'px' ],
			'selectors'  => [ '#st-panel-{{ID}}' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'panel_head_bg', [
			'label'     => __( 'Header Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'separator' => 'before',
			'selectors' => [ '#st-panel-{{ID}} .st-mob-panel-head' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'panel_head_border_color', [
			'label'     => __( 'Header Border Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#f0f0f0',
			'selectors' => [ '#st-panel-{{ID}} .st-mob-panel-head' => 'border-bottom-color: {{VALUE}};' ],
		] );

		$this->add_control( 'panel_close_color', [
			'label'     => __( 'Close Button Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'#st-panel-{{ID}} .st-mob-close-btn'     => 'color: {{VALUE}};',
				'#st-panel-{{ID}} .st-mob-close-btn svg' => 'fill: {{VALUE}};',
				'#st-panel-{{ID}} .st-mob-close-btn i'   => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( 'panel_close_bg', [
			'label'     => __( 'Close Button Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '#st-panel-{{ID}} .st-mob-close-btn' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'panel_close_size', [
			'label'     => __( 'Close Button Size (px)', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 20, 'max' => 60 ] ],
			'default'   => [ 'size' => 36 ],
			'selectors' => [
				'#st-panel-{{ID}} .st-mob-close-btn' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'panel_close_icon_size', [
			'label'     => __( 'Close Icon Size (px)', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 8, 'max' => 40 ] ],
			'default'   => [ 'size' => 18 ],
			'selectors' => [
				'#st-panel-{{ID}} .st-mob-close-btn'     => 'font-size: {{SIZE}}{{UNIT}};',
				'#st-panel-{{ID}} .st-mob-close-btn i'   => 'font-size: {{SIZE}}{{UNIT}};',
				'#st-panel-{{ID}} .st-mob-close-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'panel_close_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true ],
			'selectors'  => [ '#st-panel-{{ID}} .st-mob-close-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'panel_close_border',
			'selector' => '#st-panel-{{ID}} .st-mob-close-btn',
		] );

		$this->add_control( 'panel_close_hover_color', [
			'label'     => __( 'Hover Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'#st-panel-{{ID}} .st-mob-close-btn:hover'     => 'color: {{VALUE}};',
				'#st-panel-{{ID}} .st-mob-close-btn:hover svg' => 'fill: {{VALUE}};',
			],
		] );

		$this->add_control( 'panel_close_hover_bg', [
			'label'     => __( 'Hover Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '#st-panel-{{ID}} .st-mob-close-btn:hover' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'panel_items_heading', [
			'label'     => __( 'Menu Items', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'panel_item_typo',
			'selector' => '#st-panel-{{ID}} .st-nav-menu > li > a',
		] );

		$this->add_control( 'panel_item_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111827',
			'selectors' => [ '#st-panel-{{ID}} .st-nav-menu > li > a' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'panel_item_hover_color', [
			'label'     => __( 'Hover Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '#st-panel-{{ID}} .st-nav-menu > li > a:hover' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'panel_item_hover_bg', [
			'label'     => __( 'Hover Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '#st-panel-{{ID}} .st-nav-menu > li > a:hover' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'panel_divider_color', [
			'label'     => __( 'Item Divider Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#f3f4f6',
			'selectors' => [ '#st-panel-{{ID}} .st-mob-panel-nav .st-nav-menu > li' => 'border-bottom-color: {{VALUE}};' ],
		] );

		// ── Dropdown Arrow (submenu toggle) ───────────────────────────
		$this->add_control( 'panel_arrow_heading', [
			'label'     => __( 'Dropdown Arrow', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_responsive_control( 'panel_arrow_size', [
			'label'     => __( 'Arrow Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 8, 'max' => 40 ] ],
			'default'   => [ 'size' => 14 ],
			'selectors' => [
				'#st-panel-{{ID}} .st-dropdown-arrow'     => 'font-size: {{SIZE}}{{UNIT}};',
				'#st-panel-{{ID}} .st-dropdown-arrow i'   => 'font-size: {{SIZE}}{{UNIT}};',
				'#st-panel-{{ID}} .st-dropdown-arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'panel_arrow_color', [
			'label'     => __( 'Arrow Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6b7280',
			'selectors' => [
				'#st-panel-{{ID}} .st-dropdown-arrow'     => 'color: {{VALUE}};',
				'#st-panel-{{ID}} .st-dropdown-arrow i'   => 'color: {{VALUE}};',
				'#st-panel-{{ID}} .st-dropdown-arrow svg' => 'fill: {{VALUE}};',
			],
		] );

		$this->add_control( 'panel_arrow_bg', [
			'label'     => __( 'Arrow Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '#st-panel-{{ID}} .st-dropdown-arrow' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'panel_arrow_padding', [
			'label'      => __( 'Arrow Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top' => '4', 'right' => '6', 'bottom' => '4', 'left' => '6', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '#st-panel-{{ID}} .st-dropdown-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'panel_arrow_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '#st-panel-{{ID}} .st-dropdown-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( 'panel_overlay_color', [
			'label'     => __( 'Overlay Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'separator' => 'before',
			'default'   => 'rgba(0,0,0,0.5)',
			'selectors' => [ '#st-backdrop-{{ID}}' => 'background-color: {{VALUE}};' ],
		] );

		$this->end_controls_section();

		// ── STYLE: Hamburger ──────────────────────────────────────────
		$this->start_controls_section( 'style_hamburger', [
			'label' => __( 'Hamburger Button', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'hamburger_icon_color', [
			'label'     => __( 'Icon Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111827',
			'selectors' => [
				'{{WRAPPER}} .st-hamburger-btn'     => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-hamburger-btn svg' => 'fill: {{VALUE}};',
			],
		] );

		$this->add_control( 'hamburger_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-hamburger-btn' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'hamburger_icon_size', [
			'label'     => __( 'Icon Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 12, 'max' => 60 ] ],
			'default'   => [ 'size' => 22 ],
			'selectors' => [
				'{{WRAPPER}} .st-hamburger-btn i'   => 'font-size: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .st-hamburger-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'hamburger_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top' => '8', 'right' => '10', 'bottom' => '8', 'left' => '10', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-hamburger-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'hamburger_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-hamburger-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'hamburger_border',
			'selector' => '{{WRAPPER}} .st-hamburger-btn',
		] );

		$this->end_controls_section();
	}


	protected function render() {
		$s = $this->get_settings_for_display();

		if ( empty( $s['menu_id'] ) ) {
			echo '<p style="padding:10px;color:#9ca3af">' . esc_html__( 'Please select a menu.', 'st-elementor' ) . '</p>';
			return;
		}

		$layout     = $s['layout'] ?? 'horizontal';
		$effect     = $s['dropdown_effect'] ?? 'fade';
		$trigger    = $s['dropdown_trigger'] ?? 'hover';
		$breakpoint = (int) ( $s['mobile_breakpoint'] ?? 768 );
		$mob_side   = $s['mobile_menu_side'] ?? 'top';
		$hb_pos     = $s['hamburger_position'] ?? 'right';
		$show_arrow = 'yes' === ( $s['show_arrow'] ?? 'yes' );
		$indicator  = $s['active_indicator'] ?? 'none';
		$sticky     = 'yes' === ( $s['sticky_enable'] ?? '' );
		$sticky_off = (int) ( $s['sticky_offset'] ?? 80 );
		$sticky_shd = 'yes' === ( $s['sticky_shadow'] ?? 'yes' );

		$args = [
			'menu'        => $s['menu_id'],
			'container'   => false,
			'menu_class'  => 'st-nav-menu st-' . esc_attr( $layout ),
			'walker'      => new ST_Nav_Walker( $show_arrow ),
			'echo'        => false,
			'fallback_cb' => false,
		];

		$menu_html = wp_nav_menu( $args );

		if ( ! $menu_html ) {
			echo '<p style="padding:10px;color:#9ca3af">' . esc_html__( 'Menu not found.', 'st-elementor' ) . '</p>';
			return;
		}

		$data = wp_json_encode( [
			'effect'        => $effect,
			'trigger'       => $trigger,
			'breakpoint'    => $breakpoint,
			'mobSide'       => $mob_side,
			'hbPos'         => $hb_pos,
			'sticky'        => $sticky,
			'stickyOffset'  => $sticky_off,
			'stickyShadow'  => $sticky_shd,
		] );

		$wrap_class = 'st-nav-wrap st-indicator-' . esc_attr( $indicator ) . ' st-hb-' . esc_attr( $hb_pos );
		if ( $sticky ) {
			$wrap_class .= ' st-sticky-wrap';
		}
		// None breakpoint — always desktop
		if ( 0 === $breakpoint ) {
			$wrap_class .= ' st-bp-none';
		}
		?>
		<div class="<?php echo esc_attr( $wrap_class ); ?>"
		     id="st-nav-<?php echo esc_attr( $this->get_id() ); ?>"
		     data-settings="<?php echo esc_attr( $data ); ?>">

			<!-- Desktop nav (hidden on mobile) -->
			<nav class="st-nav-desktop st-nav-container" aria-label="<?php esc_attr_e( 'Site Navigation', 'st-elementor' ); ?>">
				<?php echo $menu_html; // phpcs:ignore ?>
			</nav>

			<!-- Hamburger button (shown on mobile) -->
			<button class="st-hamburger-btn" aria-label="<?php esc_attr_e( 'Open Menu', 'st-elementor' ); ?>" aria-expanded="false">
				<span class="st-hb-open-icon">
					<?php \Elementor\Icons_Manager::render_icon( $s['hamburger_open_icon'] ?? [ 'value' => 'fas fa-bars', 'library' => 'fa-solid' ], [ 'aria-hidden' => 'true' ] ); ?>
				</span>
			</button>

		</div>

		<!-- Mobile overlay backdrop -->
		<div class="st-mob-backdrop" id="st-backdrop-<?php echo esc_attr( $this->get_id() ); ?>" style="display:none"></div>

		<!-- Mobile side panel (outside wrap so it can be full height) -->
		<div class="st-mob-panel st-mob-panel-<?php echo esc_attr( $mob_side ); ?>"
		     id="st-panel-<?php echo esc_attr( $this->get_id() ); ?>"
		     aria-hidden="true">

			<!-- Panel header: logo + close button -->
			<div class="st-mob-panel-head">
				<a class="st-mob-panel-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php
					$logo_id = get_theme_mod( 'custom_logo' );
					if ( $logo_id ) {
						$logo_img = wp_get_attachment_image_src( $logo_id, 'full' );
						if ( $logo_img ) {
							echo '<img src="' . esc_url( $logo_img[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
						} else {
							echo esc_html( get_bloginfo( 'name' ) );
						}
					} else {
						echo esc_html( get_bloginfo( 'name' ) );
					}
					?>
				</a>
				<button class="st-mob-close-btn" aria-label="<?php esc_attr_e( 'Close Menu', 'st-elementor' ); ?>">
					<?php \Elementor\Icons_Manager::render_icon( $s['hamburger_close_icon'] ?? [ 'value' => 'fas fa-times', 'library' => 'fa-solid' ], [ 'aria-hidden' => 'true' ] ); ?>
				</button>
			</div>

			<!-- Panel menu -->
			<nav class="st-mob-panel-nav" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'st-elementor' ); ?>">
				<?php echo $menu_html; // phpcs:ignore ?>
			</nav>

		</div>
		<?php
	}
}

class ST_Nav_Walker extends \Walker_Nav_Menu {

	private $show_arrow;

	public function __construct( $show_arrow = true ) {
		$this->show_arrow = $show_arrow;
	}

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="st-sub-menu st-sub-depth-' . $depth . '">';
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item         = $data_object;
		$classes      = empty( $item->classes ) ? [] : (array) $item->classes;
		$classes[]    = 'menu-item-' . $item->ID;
		$has_children = in_array( 'menu-item-has-children', $classes, true );

		if ( $has_children ) {
			$classes[] = 'st-has-dropdown';
		}

		$class_str = implode( ' ', array_filter( array_map( 'sanitize_html_class', $classes ) ) );
		$href      = ! empty( $item->url ) ? $item->url : '#';
		$target    = ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$cls       = 'st-menu-link' . ( $item->current ? ' st-active' : '' );

		$output .= '<li class="' . esc_attr( $class_str ) . '">';
		$output .= '<a href="' . esc_url( $href ) . '" class="' . esc_attr( $cls ) . '"' . $target . '>';
		$output .= esc_html( $item->title );

		if ( $has_children && $this->show_arrow ) {
			$output .= '<span class="st-dropdown-arrow" aria-hidden="true"><i class="fas fa-chevron-down"></i></span>';
		}

		$output .= '</a>';
	}

	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}
