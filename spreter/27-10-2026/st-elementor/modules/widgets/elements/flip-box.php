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

class ST_Flip_Box extends Widget_Base {

	public function get_name()           { return 'st_flip_box'; }
	public function get_title()          { return __( 'ST Flip Box', 'st-elementor' ); }
	public function get_icon()           { return 'eicon-flip-box'; }
	public function get_categories()     { return [ 'st-store-transform' ]; }
	public function get_script_depends() { return [ 'st-elementor-public' ]; }

	protected function register_controls() {

		// ── Layout ───────────────────────────────────────────────────
		$this->start_controls_section( 'section_layout', [
			'label' => __( 'Layout', 'st-elementor' ),
		] );

		$this->add_control( 'layout_type', [
			'label'   => __( 'Layout', 'st-elementor' ),
			'type'    => Controls_Manager::HIDDEN,
			'default' => 'slider',
		] );

		$this->add_responsive_control( 'grid_columns', [
			'label'          => __( 'Columns', 'st-elementor' ),
			'type'           => Controls_Manager::SELECT,
			'default'        => '3',
			'tablet_default' => '2',
			'mobile_default' => '1',
			'options'        => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ],
			'condition'      => [ 'layout_type' => 'grid' ],
			'selectors'      => [ '{{WRAPPER}} .st-flipbox-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);' ],
		] );

		$this->add_responsive_control( 'grid_col_gap', [
			'label'      => __( 'Gap', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'    => [ 'size' => 24 ],
			'condition'  => [ 'layout_type' => 'grid' ],
			'selectors'  => [ '{{WRAPPER}} .st-flipbox-grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'slider_autoplay', [
			'label'        => __( 'Autoplay', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => [ 'layout_type' => 'slider' ],
		] );

		$this->add_control( 'slider_autoplay_delay', [
			'label'     => __( 'Autoplay Delay (ms)', 'st-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 3000,
			'min'       => 500,
			'max'       => 10000,
			'condition' => [ 'layout_type' => 'slider', 'slider_autoplay' => 'yes' ],
		] );

		$this->add_control( 'slider_speed', [
			'label'     => __( 'Transition Speed (ms)', 'st-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 500,
			'min'       => 100,
			'max'       => 3000,
			'condition' => [ 'layout_type' => 'slider' ],
		] );

		$this->add_control( 'slider_loop', [
			'label'        => __( 'Loop', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => [ 'layout_type' => 'slider' ],
		] );

		$this->add_responsive_control( 'slides_per_view', [
			'label'          => __( 'Slides Per View', 'st-elementor' ),
			'type'           => Controls_Manager::NUMBER,
			'default'        => 3,
			'tablet_default' => 2,
			'mobile_default' => 1,
			'min'            => 1,
			'max'            => 6,
			'condition'      => [ 'layout_type' => 'slider' ],
		] );

		$this->add_responsive_control( 'gap', [
			'label'      => __( 'Gap', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'    => [ 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .st-flipbox-wrap' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'arrow_prev_icon', [
			'label'     => __( 'Prev Arrow Icon', 'st-elementor' ),
			'type'      => Controls_Manager::ICONS,
			'default'   => [ 'value' => 'fas fa-chevron-left', 'library' => 'fa-solid' ],
			'condition' => [ 'layout_type' => 'slider' ],
		] );

		$this->add_control( 'arrow_next_icon', [
			'label'     => __( 'Next Arrow Icon', 'st-elementor' ),
			'type'      => Controls_Manager::ICONS,
			'default'   => [ 'value' => 'fas fa-chevron-right', 'library' => 'fa-solid' ],
			'condition' => [ 'layout_type' => 'slider' ],
		] );

		$this->end_controls_section();

		// ── Flip Settings ─────────────────────────────────────────
		$this->start_controls_section( 'section_flip', [
			'label' => __( 'Flip Settings', 'st-elementor' ),
		] );

		$this->add_control( 'flip_direction', [
			'label'   => __( 'Flip Direction', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'horizontal',
			'options' => [
				'horizontal' => __( 'Horizontal', 'st-elementor' ),
				'vertical'   => __( 'Vertical', 'st-elementor' ),
				'fade'       => __( 'Fade', 'st-elementor' ),
				'zoom'       => __( 'Zoom', 'st-elementor' ),
				'push'       => __( 'Push/Slide', 'st-elementor' ),
			],
		] );

		$this->add_responsive_control( 'card_height', [
			'label'      => __( 'Card Height', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [ 'px' => [ 'min' => 100, 'max' => 800 ] ],
			'default'    => [ 'size' => 280, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .st-flip-box' => 'height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'flip_speed', [
			'label'     => __( 'Speed (ms)', 'st-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 600,
			'min'       => 100,
			'max'       => 2000,
			'selectors' => [
				'{{WRAPPER}} .st-flip-inner' => 'transition-duration: {{VALUE}}ms;',
				'{{WRAPPER}} .st-flip-front' => 'transition-duration: {{VALUE}}ms;',
				'{{WRAPPER}} .st-flip-back'  => 'transition-duration: {{VALUE}}ms;',
			],
		] );

		$this->end_controls_section();

		// ── Items Repeater ────────────────────────────────────────
		$this->start_controls_section( 'section_items', [
			'label' => __( 'Flip Cards', 'st-elementor' ),
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'front_heading', [
			'label' => __( '— FRONT SIDE —', 'st-elementor' ),
			'type'  => Controls_Manager::HEADING,
		] );

		$repeater->add_control( 'front_type', [
			'label'   => __( 'Front Content', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'icon',
			'options' => [ 'icon' => __( 'Icon', 'st-elementor' ), 'image' => __( 'Image', 'st-elementor' ), 'none' => __( 'None', 'st-elementor' ) ],
		] );

		$repeater->add_control( 'front_icon', [
			'label'     => __( 'Icon', 'st-elementor' ),
			'type'      => Controls_Manager::ICONS,
			'default'   => [ 'value' => 'fas fa-star', 'library' => 'fa-solid' ],
			'condition' => [ 'front_type' => 'icon' ],
		] );

		$repeater->add_control( 'front_image', [
			'label'     => __( 'Image', 'st-elementor' ),
			'type'      => Controls_Manager::MEDIA,
			'default'   => [ 'url' => Utils::get_placeholder_image_src() ],
			'condition' => [ 'front_type' => 'image' ],
		] );

		$repeater->add_control( 'front_title', [
			'label'   => __( 'Title', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Card Title', 'st-elementor' ),
		] );

		$repeater->add_control( 'front_desc', [
			'label'   => __( 'Description', 'st-elementor' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => __( 'Hover to flip.', 'st-elementor' ),
			'rows'    => 2,
		] );

		$repeater->add_control( 'front_bg', [
			'label'     => __( 'Front Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .st-flip-front' => 'background-color: {{VALUE}};' ],
		] );

		$repeater->add_control( 'front_overlay', [
			'label'        => __( 'Front Overlay', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
			'separator'    => 'before',
		] );

		$repeater->add_control( 'front_overlay_color', [
			'label'     => __( 'Overlay Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.4)',
			'condition' => [ 'front_overlay' => 'yes' ],
			'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .st-flip-front .st-flip-overlay' => 'background: {{VALUE}};' ],
		] );

		$repeater->add_control( 'back_heading', [
			'label'     => __( '— BACK SIDE —', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$repeater->add_control( 'back_type', [
			'label'   => __( 'Back Content', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'icon',
			'options' => [ 'icon' => __( 'Icon', 'st-elementor' ), 'image' => __( 'Image', 'st-elementor' ), 'none' => __( 'None', 'st-elementor' ) ],
		] );

		$repeater->add_control( 'back_icon', [
			'label'     => __( 'Icon', 'st-elementor' ),
			'type'      => Controls_Manager::ICONS,
			'default'   => [ 'value' => 'fas fa-check', 'library' => 'fa-solid' ],
			'condition' => [ 'back_type' => 'icon' ],
		] );

		$repeater->add_control( 'back_image', [
			'label'     => __( 'Image', 'st-elementor' ),
			'type'      => Controls_Manager::MEDIA,
			'condition' => [ 'back_type' => 'image' ],
		] );

		$repeater->add_control( 'back_title', [
			'label'   => __( 'Title', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Back Side', 'st-elementor' ),
		] );

		$repeater->add_control( 'back_desc', [
			'label'   => __( 'Description', 'st-elementor' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => __( 'This is the back side content.', 'st-elementor' ),
			'rows'    => 2,
		] );

		$repeater->add_control( 'back_btn_text', [
			'label'   => __( 'Button Text', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '',
		] );

		$repeater->add_control( 'back_btn_link', [
			'label'   => __( 'Button Link', 'st-elementor' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		$repeater->add_control( 'back_bg', [
			'label'     => __( 'Back Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .st-flip-back' => 'background-color: {{VALUE}};' ],
		] );

		$repeater->add_control( 'back_overlay', [
			'label'        => __( 'Back Overlay', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
			'separator'    => 'before',
		] );

		$repeater->add_control( 'back_overlay_color', [
			'label'     => __( 'Overlay Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.4)',
			'condition' => [ 'back_overlay' => 'yes' ],
			'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .st-flip-back .st-flip-overlay' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'items', [
			'label'       => __( 'Cards', 'st-elementor' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[ 'front_title' => 'Card 1', 'back_title' => 'Back 1', 'back_desc' => 'Back content here.' ],
				[ 'front_title' => 'Card 2', 'back_title' => 'Back 2', 'back_desc' => 'Back content here.' ],
				[ 'front_title' => 'Card 3', 'back_title' => 'Back 3', 'back_desc' => 'Back content here.' ],
			],
			'title_field' => '{{{ front_title }}}',
		] );

		$this->end_controls_section();

		// ── Front Style ───────────────────────────────────────────
		$this->start_controls_section( 'style_front', [
			'label' => __( 'Front Side', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'front_bg_global',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-flip-front',
		] );

		$this->add_responsive_control( 'front_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '30', 'right' => '24', 'bottom' => '30', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-flip-front' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( 'front_icon_color', [
			'label'     => __( 'Icon Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-flip-front .st-flip-icon' => 'color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'front_icon_size', [
			'label'     => __( 'Icon Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 16, 'max' => 100 ] ],
			'default'   => [ 'size' => 48 ],
			'selectors' => [ '{{WRAPPER}} .st-flip-front .st-flip-icon i' => 'font-size: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'front_title_color', [
			'label'     => __( 'Title Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111827',
			'selectors' => [ '{{WRAPPER}} .st-flip-front .st-flip-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'front_title_typo', 'selector' => '{{WRAPPER}} .st-flip-front .st-flip-title',
		] );

		$this->add_control( 'front_desc_color', [
			'label'     => __( 'Description Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6b7280',
			'selectors' => [ '{{WRAPPER}} .st-flip-front .st-flip-desc' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_section();

		// ── Back Style ────────────────────────────────────────────
		$this->start_controls_section( 'style_back', [
			'label' => __( 'Back Side', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'back_bg_global',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-flip-back',
		] );

		$this->add_responsive_control( 'back_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '30', 'right' => '24', 'bottom' => '30', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-flip-back' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( 'back_icon_color', [
			'label'     => __( 'Icon Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-flip-back .st-flip-icon' => 'color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'back_icon_size', [
			'label'     => __( 'Icon Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 16, 'max' => 100 ] ],
			'default'   => [ 'size' => 48 ],
			'selectors' => [ '{{WRAPPER}} .st-flip-back .st-flip-icon i' => 'font-size: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'back_title_color', [
			'label'     => __( 'Title Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-flip-back .st-flip-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'back_title_typo', 'selector' => '{{WRAPPER}} .st-flip-back .st-flip-title',
		] );

		$this->add_control( 'back_desc_color', [
			'label'     => __( 'Description Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.85)',
			'selectors' => [ '{{WRAPPER}} .st-flip-back .st-flip-desc' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'back_btn_color', [
			'label'     => __( 'Button Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-flip-btn' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'back_btn_bg', [
			'label'     => __( 'Button Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-flip-btn' => 'background-color: {{VALUE}};' ],
		] );

		$this->end_controls_section();

		// ── Card Style ────────────────────────────────────────────
		$this->start_controls_section( 'style_card', [
			'label' => __( 'Card Box', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'card_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '12', 'right' => '12', 'bottom' => '12', 'left' => '12', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [
				'{{WRAPPER}} .st-flip-box'   => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .st-flip-front' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .st-flip-back'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'card_shadow',
			'selector' => '{{WRAPPER}} .st-flip-box',
		] );

		$this->end_controls_section();

		// ── Arrow Style ───────────────────────────────────────────
		$this->start_controls_section( 'style_arrows', [
			'label'     => __( 'Arrows', 'st-elementor' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'layout_type' => 'slider' ],
		] );

		$this->add_responsive_control( 'arrow_size', [
			'label'     => __( 'Button Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 20, 'max' => 80 ] ],
			'default'   => [ 'size' => 44 ],
			'selectors' => [ '{{WRAPPER}} .st-flipbox-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'arrow_icon_size', [
			'label'     => __( 'Icon Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 8, 'max' => 40 ] ],
			'default'   => [ 'size' => 16 ],
			'selectors' => [
				'{{WRAPPER}} .st-flipbox-arrow i'   => 'font-size: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .st-flipbox-arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->start_controls_tabs( 'arrow_tabs' );

		$this->start_controls_tab( 'arrow_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );
		$this->add_control( 'arrow_color', [
			'label'     => __( 'Icon Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#374151',
			'selectors' => [
				'{{WRAPPER}} .st-flipbox-arrow'     => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-flipbox-arrow svg' => 'fill: {{VALUE}};',
			],
		] );
		$this->add_control( 'arrow_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-flipbox-arrow' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'arrow_hover', [ 'label' => __( 'Hover', 'st-elementor' ) ] );
		$this->add_control( 'arrow_hover_color', [
			'label'     => __( 'Icon Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-flipbox-arrow:hover'     => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-flipbox-arrow:hover svg' => 'fill: {{VALUE}};',
			],
		] );
		$this->add_control( 'arrow_hover_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-flipbox-arrow:hover' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control( 'arrow_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-flipbox-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'arrow_offset', [
			'label'     => __( 'Vertical Position (%)', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'default'   => [ 'size' => 50 ],
			'selectors' => [
				'{{WRAPPER}} .st-flipbox-arrow' => 'top: {{SIZE}}%; transform: translateY(-50%);',
			],
		] );

		$this->end_controls_section();

		// ── Dots Style ────────────────────────────────────────────
		$this->start_controls_section( 'style_dots', [
			'label'     => __( 'Pagination Dots', 'st-elementor' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'layout_type' => 'slider' ],
		] );

		$this->add_responsive_control( 'dot_size', [
			'label'     => __( 'Dot Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 4, 'max' => 24 ] ],
			'default'   => [ 'size' => 10 ],
			'selectors' => [ '{{WRAPPER}} .st-flipbox-dots .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'dot_color', [
			'label'     => __( 'Dot Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(99,102,241,0.4)',
			'selectors' => [ '{{WRAPPER}} .st-flipbox-dots .swiper-pagination-bullet' => 'background: {{VALUE}}; opacity: 1;' ],
		] );

		$this->add_control( 'dot_active_color', [
			'label'     => __( 'Active Dot Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-flipbox-dots .swiper-pagination-bullet-active' => 'background: {{VALUE}}; opacity: 1;' ],
		] );

		$this->add_responsive_control( 'dot_spacing', [
			'label'     => __( 'Dot Spacing', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
			'default'   => [ 'size' => 4 ],
			'selectors' => [ '{{WRAPPER}} .st-flipbox-dots .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

	} // end register_controls()

	protected function render() {
		$s         = $this->get_settings_for_display();
		$layout    = $s['layout_type']    ?? 'grid';
		$direction = $s['flip_direction'] ?? 'horizontal';
		$items     = $s['items']          ?? [];
		$is_slider = 'slider' === $layout;

		// Grid inline style — removed, Elementor selectors handle this
		$grid_style = '';

		$slider_data = '';
		if ( $is_slider ) {
			$slider_data = wp_json_encode( [
				'slidesPerView' => (int) ( $s['slides_per_view']        ?? 3 ),
				'slidesTablet'  => (int) ( $s['slides_per_view_tablet'] ?? 2 ),
				'slidesMobile'  => (int) ( $s['slides_per_view_mobile'] ?? 1 ),
				'spaceBetween'  => (int) ( $s['gap']['size']            ?? 24 ),
				'autoplay'      => 'yes' === ( $s['slider_autoplay']    ?? 'yes' ),
				'autoplayDelay' => (int) ( $s['slider_autoplay_delay']  ?? 3000 ),
				'loop'          => 'yes' === ( $s['slider_loop']        ?? 'yes' ),
				'speed'         => (int) ( $s['slider_speed']           ?? 500 ),
			] );
		}

		$wrap_class = 'st-flipbox-wrap st-flipbox-' . esc_attr( $layout );
		if ( $is_slider ) $wrap_class .= ' swiper';
		?>
		<div class="st-flipbox-container">
			<div class="<?php echo esc_attr( $wrap_class ); ?>"
			     <?php if ( $grid_style )  echo 'style="' . esc_attr( $grid_style ) . '"'; ?>
			     <?php if ( $slider_data ) echo 'data-slider="' . esc_attr( $slider_data ) . '"'; ?>>

				<?php if ( $is_slider ) : ?><div class="swiper-wrapper"><?php endif; ?>

				<?php foreach ( $items as $item ) :
					$item_class = 'st-flip-box st-flip-' . esc_attr( $direction ) . ' elementor-repeater-item-' . $item['_id'];
					if ( $is_slider ) $item_class .= ' swiper-slide';
				?>
					<div class="<?php echo esc_attr( $item_class ); ?>">
						<div class="st-flip-inner">

							<!-- FRONT -->
							<div class="st-flip-front">
								<?php if ( 'yes' === ( $item['front_overlay'] ?? '' ) ) : ?>
								<div class="st-flip-overlay"></div>
								<?php endif; ?>
								<?php if ( 'icon' === $item['front_type'] && ! empty( $item['front_icon']['value'] ) ) : ?>
								<div class="st-flip-icon">
									<?php \Elementor\Icons_Manager::render_icon( $item['front_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</div>
								<?php elseif ( 'image' === $item['front_type'] && ! empty( $item['front_image']['url'] ) ) : ?>
								<div class="st-flip-img">
									<img src="<?php echo esc_url( $item['front_image']['url'] ); ?>" alt="">
								</div>
								<?php endif; ?>
								<?php if ( ! empty( $item['front_title'] ) ) : ?>
								<h3 class="st-flip-title"><?php echo esc_html( $item['front_title'] ); ?></h3>
								<?php endif; ?>
								<?php if ( ! empty( $item['front_desc'] ) ) : ?>
								<p class="st-flip-desc"><?php echo esc_html( $item['front_desc'] ); ?></p>
								<?php endif; ?>
							</div>

							<!-- BACK -->
							<div class="st-flip-back">
								<?php if ( 'yes' === ( $item['back_overlay'] ?? '' ) ) : ?>
								<div class="st-flip-overlay"></div>
								<?php endif; ?>
								<?php if ( 'icon' === $item['back_type'] && ! empty( $item['back_icon']['value'] ) ) : ?>
								<div class="st-flip-icon">
									<?php \Elementor\Icons_Manager::render_icon( $item['back_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</div>
								<?php elseif ( 'image' === $item['back_type'] && ! empty( $item['back_image']['url'] ) ) : ?>
								<div class="st-flip-img">
									<img src="<?php echo esc_url( $item['back_image']['url'] ); ?>" alt="">
								</div>
								<?php endif; ?>
								<?php if ( ! empty( $item['back_title'] ) ) : ?>
								<h3 class="st-flip-title"><?php echo esc_html( $item['back_title'] ); ?></h3>
								<?php endif; ?>
								<?php if ( ! empty( $item['back_desc'] ) ) : ?>
								<p class="st-flip-desc"><?php echo esc_html( $item['back_desc'] ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $item['back_btn_text'] ) ) :
									$this->add_link_attributes( 'btn_' . $item['_id'], $item['back_btn_link'] );
								?>
								<a class="st-flip-btn" <?php echo $this->get_render_attribute_string( 'btn_' . $item['_id'] ); ?>>
									<?php echo esc_html( $item['back_btn_text'] ); ?>
								</a>
								<?php endif; ?>
							</div>

						</div>
					</div>
				<?php endforeach; ?>

				<?php if ( $is_slider ) : ?>
				</div><!-- .swiper-wrapper -->
				<div class="swiper-pagination st-flipbox-dots"></div>
				<div class="swiper-button-prev st-flipbox-arrow">
					<?php if ( ! empty( $s['arrow_prev_icon']['value'] ) ) \Elementor\Icons_Manager::render_icon( $s['arrow_prev_icon'], [ 'aria-hidden' => 'true' ] ); ?>
				</div>
				<div class="swiper-button-next st-flipbox-arrow">
					<?php if ( ! empty( $s['arrow_next_icon']['value'] ) ) \Elementor\Icons_Manager::render_icon( $s['arrow_next_icon'], [ 'aria-hidden' => 'true' ] ); ?>
				</div>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}
}
