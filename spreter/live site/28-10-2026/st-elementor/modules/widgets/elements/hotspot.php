<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit;

class ST_Hotspot extends Widget_Base {

	public function get_name()       { return 'st_hotspot'; }
	public function get_title()      { return __( 'ST Hotspot', 'st-elementor' ); }
	public function get_icon()       { return 'eicon-image-hotspot'; }
	public function get_categories() { return [ 'st-store-transform' ]; }

	protected function register_controls() {

		// ── Image ────────────────────────────────────────────────────
		$this->start_controls_section( 'section_image', [
			'label' => __( 'Image', 'st-elementor' ),
		] );

		$this->add_control( 'image', [
			'label'   => __( 'Background Image', 'st-elementor' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => Utils::get_placeholder_image_src() ],
		] );

		$this->add_responsive_control( 'image_height', [
			'label'      => __( 'Image Height', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [ 'px' => [ 'min' => 100, 'max' => 1000 ] ],
			'default'    => [ 'size' => 500, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-wrap' => 'height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'image_fit', [
			'label'     => __( 'Image Fit', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'cover',
			'options'   => [ 'cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill' ],
			'selectors' => [ '{{WRAPPER}} .st-hs-image' => 'object-fit: {{VALUE}};' ],
		] );

		$this->end_controls_section();

		// ── Hotspot Points ───────────────────────────────────────────
		$this->start_controls_section( 'section_hotspots', [
			'label' => __( 'Hotspot Points', 'st-elementor' ),
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'label', [
			'label'   => __( 'Label', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Hotspot', 'st-elementor' ),
		] );

		// Tooltip image
		$repeater->add_control( 'tooltip_image', [
			'label'     => __( 'Tooltip Image', 'st-elementor' ),
			'type'      => Controls_Manager::MEDIA,
			'separator' => 'before',
		] );

		$repeater->add_control( 'tooltip_content', [
			'label'   => __( 'Tooltip Content', 'st-elementor' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => __( 'Tooltip description here.', 'st-elementor' ),
		] );

		$repeater->add_control( 'position_x', [
			'label'      => __( 'Position X (%)', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%' ],
			'range'      => [ '%' => [ 'min' => 0, 'max' => 100 ] ],
			'default'    => [ 'size' => 50, 'unit' => '%' ],
			'separator'  => 'before',
		] );

		$repeater->add_control( 'position_y', [
			'label'      => __( 'Position Y (%)', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%' ],
			'range'      => [ '%' => [ 'min' => 0, 'max' => 100 ] ],
			'default'    => [ 'size' => 50, 'unit' => '%' ],
		] );

		$repeater->add_control( 'icon', [
			'label'     => __( 'Icon', 'st-elementor' ),
			'type'      => Controls_Manager::ICONS,
			'default'   => [ 'value' => 'fas fa-plus', 'library' => 'fa-solid' ],
			'separator' => 'before',
		] );

		$repeater->add_control( 'tooltip_position', [
			'label'   => __( 'Tooltip Direction', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'top',
			'options' => [
				'top'    => __( 'Top', 'st-elementor' ),
				'bottom' => __( 'Bottom', 'st-elementor' ),
				'left'   => __( 'Left', 'st-elementor' ),
				'right'  => __( 'Right', 'st-elementor' ),
			],
		] );

		// Click action
		$repeater->add_control( 'click_action', [
			'label'     => __( 'On Click', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'tooltip',
			'separator' => 'before',
			'options'   => [
				'tooltip' => __( 'Show Tooltip', 'st-elementor' ),
				'popup'   => __( 'Open Popup', 'st-elementor' ),
				'link'    => __( 'Go to Link', 'st-elementor' ),
				'none'    => __( 'None', 'st-elementor' ),
			],
		] );

		// Popup ID (shown when click_action = popup)
		$popup_options = [ '0' => __( '— Select Popup —', 'st-elementor' ) ];
		$popups = get_posts( [ 'post_type' => 'st_popup', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ] );
		foreach ( $popups as $p ) { $popup_options[ $p->ID ] = $p->post_title; }

		$repeater->add_control( 'popup_id', [
			'label'     => __( 'Select Popup', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => $popup_options,
			'default'   => '0',
			'condition' => [ 'click_action' => 'popup' ],
		] );

		$repeater->add_control( 'link', [
			'label'     => __( 'Link', 'st-elementor' ),
			'type'      => Controls_Manager::URL,
			'default'   => [ 'url' => '' ],
			'condition' => [ 'click_action' => 'link' ],
		] );

		$repeater->add_control( 'custom_id', [
			'label'       => __( 'Custom ID', 'st-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => 'hotspot-1',
			'description' => __( 'Add custom ID to this hotspot point.', 'st-elementor' ),
		] );

		$repeater->add_control( 'custom_class', [
			'label'       => __( 'Custom Class', 'st-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => 'my-hotspot',
			'description' => __( 'Add custom CSS class to this hotspot point.', 'st-elementor' ),
		] );

		// ── Button in tooltip/popup ───────────────────────────────────
		$repeater->add_control( 'btn_text', [
			'label'     => __( 'Button Text', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => '',
			'separator' => 'before',
			'placeholder' => __( 'e.g. Learn More', 'st-elementor' ),
		] );

		$repeater->add_control( 'btn_link', [
			'label'   => __( 'Button Link', 'st-elementor' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		$this->add_control( 'hotspots', [
			'label'       => __( 'Hotspot Points', 'st-elementor' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[ 'label' => 'Point 1', 'position_x' => [ 'size' => 30, 'unit' => '%' ], 'position_y' => [ 'size' => 40, 'unit' => '%' ] ],
				[ 'label' => 'Point 2', 'position_x' => [ 'size' => 65, 'unit' => '%' ], 'position_y' => [ 'size' => 60, 'unit' => '%' ] ],
			],
			'title_field' => '{{{ label }}}',
		] );

		$this->add_control( 'trigger', [
			'label'     => __( 'Trigger', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'hover',
			'options'   => [
				'hover' => __( 'Hover', 'st-elementor' ),
				'click' => __( 'Click', 'st-elementor' ),
			],
			'separator' => 'before',
		] );

		$this->end_controls_section();

		// ── STYLE: Wrapper ───────────────────────────────────────────
		$this->start_controls_section( 'style_wrap', [
			'label' => __( 'Wrapper', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'wrap_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'wrap_shadow',
			'selector' => '{{WRAPPER}} .st-hs-wrap',
		] );

		$this->end_controls_section();

		// ── STYLE: Hotspot Button ────────────────────────────────────
		$this->start_controls_section( 'style_point', [
			'label' => __( 'Hotspot Button', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'point_size', [
			'label'     => __( 'Button Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 20, 'max' => 80 ] ],
			'default'   => [ 'size' => 36 ],
			'selectors' => [ '{{WRAPPER}} .st-hs-point' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'point_icon_size', [
			'label'     => __( 'Icon Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 8, 'max' => 40 ] ],
			'default'   => [ 'size' => 14 ],
			'selectors' => [
				'{{WRAPPER}} .st-hs-point i'   => 'font-size: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .st-hs-point svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->start_controls_tabs( 'point_tabs' );

		$this->start_controls_tab( 'point_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );
		$this->add_control( 'point_color', [
			'label'     => __( 'Icon Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .st-hs-point'     => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-hs-point svg' => 'fill: {{VALUE}};',
			],
		] );
		$this->add_control( 'point_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-hs-point' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'point_border',
			'selector' => '{{WRAPPER}} .st-hs-point',
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'point_hover', [ 'label' => __( 'Hover', 'st-elementor' ) ] );
		$this->add_control( 'point_hover_color', [
			'label'     => __( 'Icon Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-hs-point:hover' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'point_hover_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#4f46e5',
			'selectors' => [ '{{WRAPPER}} .st-hs-point:hover' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control( 'point_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-point' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'point_shadow',
			'selector' => '{{WRAPPER}} .st-hs-point',
		] );

		// Pulse animation
		$this->add_control( 'show_pulse', [
			'label'        => __( 'Pulse Animation', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'separator'    => 'before',
		] );

		$this->add_control( 'pulse_color', [
			'label'     => __( 'Pulse Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'condition' => [ 'show_pulse' => 'yes' ],
			'selectors' => [ '{{WRAPPER}} .st-hs-pulse' => 'background-color: {{VALUE}};' ],
		] );

		$this->end_controls_section();

		// ── STYLE: Tooltip ───────────────────────────────────────────
		$this->start_controls_section( 'style_tooltip', [
			'label' => __( 'Tooltip', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'tooltip_bg',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-hs-tooltip',
		] );

		$this->add_control( 'tooltip_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111827',
			'selectors' => [ '{{WRAPPER}} .st-hs-tooltip' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'tooltip_typo',
			'selector' => '{{WRAPPER}} .st-hs-tooltip',
		] );

		$this->add_responsive_control( 'tooltip_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '12', 'right' => '16', 'bottom' => '12', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'tooltip_width', [
			'label'      => __( 'Width', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 100, 'max' => 400 ] ],
			'default'    => [ 'size' => 200 ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-tooltip' => 'width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'tooltip_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'tooltip_border',
			'selector' => '{{WRAPPER}} .st-hs-tooltip',
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'tooltip_shadow',
			'selector' => '{{WRAPPER}} .st-hs-tooltip',
		] );

		// Tooltip title style
		$this->add_control( 'tooltip_title_heading', [
			'label'     => __( 'Title', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'tooltip_title_color', [
			'label'     => __( 'Title Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111827',
			'selectors' => [ '{{WRAPPER}} .st-hs-tooltip-title' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'tooltip_title_typo', 'selector' => '{{WRAPPER}} .st-hs-tooltip-title',
		] );

		// Tooltip description style
		$this->add_control( 'tooltip_desc_heading', [
			'label'     => __( 'Description', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'tooltip_desc_color', [
			'label'     => __( 'Description Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6b7280',
			'selectors' => [ '{{WRAPPER}} .st-hs-tooltip-content' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'tooltip_desc_typo', 'selector' => '{{WRAPPER}} .st-hs-tooltip-content',
		] );

		// Tooltip button style
		$this->add_control( 'tooltip_btn_heading', [
			'label'     => __( 'Button', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'tooltip_btn_color', [
			'label'     => __( 'Button Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-hs-tooltip-btn' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'tooltip_btn_bg', [
			'label'     => __( 'Button Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-hs-tooltip-btn' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_responsive_control( 'tooltip_btn_radius', [
			'label'      => __( 'Button Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
			'default'    => [ 'size' => 6 ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-tooltip-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── STYLE: Popup ─────────────────────────────────────────────
		$this->start_controls_section( 'style_popup', [
			'label' => __( 'Popup', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'popup_bg', [
			'label'     => __( 'Popup Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-hs-popup-inner' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'popup_width', [
			'label'      => __( 'Popup Width', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 200, 'max' => 700 ] ],
			'default'    => [ 'size' => 400 ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-popup-inner' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'popup_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-popup-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'popup_shadow',
			'selector' => '{{WRAPPER}} .st-hs-popup-inner',
		] );

		// Popup image height
		$this->add_responsive_control( 'popup_img_height', [
			'label'      => __( 'Image Height', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 80, 'max' => 400 ] ],
			'default'    => [ 'size' => 220 ],
			'separator'  => 'before',
			'selectors'  => [ '{{WRAPPER}} .st-hs-popup-img img' => 'height: {{SIZE}}{{UNIT}};' ],
		] );

		// Popup title
		$this->add_control( 'popup_title_heading', [
			'label'     => __( 'Title', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'popup_title_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111827',
			'selectors' => [ '{{WRAPPER}} .st-hs-popup-title' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'popup_title_typo', 'selector' => '{{WRAPPER}} .st-hs-popup-title',
		] );

		// Popup description
		$this->add_control( 'popup_desc_heading', [
			'label'     => __( 'Description', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'popup_desc_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6b7280',
			'selectors' => [ '{{WRAPPER}} .st-hs-popup-content' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'popup_desc_typo', 'selector' => '{{WRAPPER}} .st-hs-popup-content',
		] );

		// Popup button
		$this->add_control( 'popup_btn_heading', [
			'label'     => __( 'Button', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'popup_btn_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-hs-popup-btn' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'popup_btn_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-hs-popup-btn' => 'background: {{VALUE}}; box-shadow: 0 4px 14px {{VALUE}}55;' ],
		] );
		$this->add_responsive_control( 'popup_btn_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
			'default'    => [ 'size' => 8 ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-popup-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_responsive_control( 'popup_btn_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top' => '11', 'right' => '26', 'bottom' => '11', 'left' => '26', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-hs-popup-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		// Overlay
		$this->add_control( 'popup_overlay_color', [
			'label'     => __( 'Overlay Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.55)',
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}} .st-hs-popup' => 'background-color: {{VALUE}};' ],
		] );

		$this->end_controls_section();

	} // end register_controls

	protected function render() {
			$s          = $this->get_settings_for_display();
			$trigger    = $s['trigger'] ?? 'hover';
			$img_url    = $s['image']['url'] ?? '';
			$show_pulse = 'yes' === ( $s['show_pulse'] ?? 'yes' );
			$is_editor  = \Elementor\Plugin::$instance->editor->is_edit_mode();
			?>
			<div class="st-hs-wrap<?php echo $is_editor ? ' st-hs-editor' : ''; ?>"
			     data-trigger="<?php echo esc_attr( $trigger ); ?>">

				<?php if ( $img_url ) : ?>
				<img class="st-hs-image" src="<?php echo esc_url( $img_url ); ?>" alt="">
				<?php endif; ?>

				<?php foreach ( $s['hotspots'] as $index => $hs ) :
					$x            = (float) ( $hs['position_x']['size'] ?? 50 );
					$y            = (float) ( $hs['position_y']['size'] ?? 50 );
					$dir          = $hs['tooltip_position'] ?? 'top';
					$click_action = $hs['click_action'] ?? 'tooltip';
					$custom_id    = ! empty( $hs['custom_id'] ) ? ' id="' . esc_attr( $hs['custom_id'] ) . '"' : '';
					$custom_cls   = ! empty( $hs['custom_class'] ) ? ' ' . esc_attr( $hs['custom_class'] ) : '';
					$popup_id     = (int) ( $hs['popup_id'] ?? 0 );
					$has_link     = ( 'link' === $click_action ) && ! empty( $hs['link']['url'] );
					$tag          = $has_link ? 'a' : 'button';
					$link_attr    = $has_link ? ' href="' . esc_url( $hs['link']['url'] ) . '"' . ( ! empty( $hs['link']['is_external'] ) ? ' target="_blank"' : '' ) : ' type="button"';
					$popup_attr   = ( 'popup' === $click_action && $popup_id ) ? ' data-st-popup="' . esc_attr( $popup_id ) . '"' : '';
					$data_action  = ' data-action="' . esc_attr( $click_action ) . '"';
					?>
					<div class="st-hs-item elementor-repeater-item-<?php echo esc_attr( $hs['_id'] ); ?>"
					     style="left:<?php echo esc_attr( $x ); ?>%;top:<?php echo esc_attr( $y ); ?>%"<?php echo $custom_id; ?>>

						<?php if ( $show_pulse ) : ?>
						<span class="st-hs-pulse"></span>
						<?php endif; ?>

						<<?php echo $tag; ?> class="st-hs-point<?php echo $custom_cls; ?>"<?php echo $link_attr . $popup_attr . $data_action; ?>>
							<?php if ( ! empty( $hs['icon']['value'] ) ) : ?>
								<?php \Elementor\Icons_Manager::render_icon( $hs['icon'], [ 'aria-hidden' => 'true' ] ); ?>
							<?php endif; ?>
						</<?php echo $tag; ?>>

						<?php if ( 'tooltip' === $click_action || 'none' !== $click_action ) : ?>
						<div class="st-hs-tooltip st-hs-tooltip-<?php echo esc_attr( $dir ); ?>">
							<?php if ( ! empty( $hs['tooltip_image']['url'] ) ) : ?>
							<div class="st-hs-tooltip-img">
								<img src="<?php echo esc_url( $hs['tooltip_image']['url'] ); ?>" alt="">
							</div>
							<?php endif; ?>
							<?php if ( ! empty( $hs['label'] ) ) : ?>
							<div class="st-hs-tooltip-title"><?php echo esc_html( $hs['label'] ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $hs['tooltip_content'] ) ) : ?>
							<div class="st-hs-tooltip-content"><?php echo wp_kses_post( $hs['tooltip_content'] ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $hs['btn_text'] ) ) :
								$btn_url    = $hs['btn_link']['url'] ?? '#';
								$btn_target = ! empty( $hs['btn_link']['is_external'] ) ? ' target="_blank"' : '';
							?>
							<a href="<?php echo esc_url( $btn_url ); ?>"<?php echo $btn_target; ?> class="st-hs-tooltip-btn">
								<?php echo esc_html( $hs['btn_text'] ); ?>
							</a>
							<?php endif; ?>
						</div>
						<?php endif; ?>

						<?php if ( 'popup' === $click_action ) : ?>
						<div class="st-hs-popup" role="dialog" aria-modal="true">
							<div class="st-hs-popup-inner">
								<button class="st-hs-popup-close" type="button" aria-label="Close">&#x2715;</button>
								<?php if ( ! empty( $hs['tooltip_image']['url'] ) ) : ?>
								<div class="st-hs-popup-img">
									<img src="<?php echo esc_url( $hs['tooltip_image']['url'] ); ?>" alt="">
								</div>
								<?php endif; ?>
								<div class="st-hs-popup-body">
									<?php if ( ! empty( $hs['label'] ) ) : ?>
									<h3 class="st-hs-popup-title"><?php echo esc_html( $hs['label'] ); ?></h3>
									<?php endif; ?>
									<?php if ( ! empty( $hs['tooltip_content'] ) ) : ?>
									<div class="st-hs-popup-content"><?php echo wp_kses_post( $hs['tooltip_content'] ); ?></div>
									<?php endif; ?>
									<?php if ( ! empty( $hs['btn_text'] ) ) :
										$btn_url    = $hs['btn_link']['url'] ?? '#';
										$btn_target = ! empty( $hs['btn_link']['is_external'] ) ? ' target="_blank"' : '';
									?>
									<a href="<?php echo esc_url( $btn_url ); ?>"<?php echo $btn_target; ?> class="st-hs-popup-btn">
										<?php echo esc_html( $hs['btn_text'] ); ?>
									</a>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<?php endif; ?>

					</div>
				<?php endforeach; ?>

			</div>
			<?php
		}

}
