<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit;

class ST_Flex_Box extends Widget_Base {

	public function get_name()       { return 'st_flex_box'; }
	public function get_title()      { return __( 'ST Flex Box', 'st-elementor' ); }
	public function get_icon()       { return 'eicon-gallery-grid'; }
	public function get_categories() { return [ 'st-store-transform' ]; }
	public function get_script_depends() { return [ 'st-elementor-public' ]; }

	protected function register_controls() {

		// ═══ CONTENT ════════════════════════════════════════════════

		// ── Layout ───────────────────────────────────────────────────
		$this->start_controls_section( 'section_layout', [
			'label' => __( 'Layout', 'st-elementor' ),
		] );

		$this->add_control( 'layout_type', [
			'label'   => __( 'Layout Type', 'st-elementor' ),
			'type'    => Controls_Manager::HIDDEN,
			'default' => 'slider',
		] );

		// Flex options
		$this->add_control( 'flex_direction', [
			'label'     => __( 'Direction', 'st-elementor' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'row'    => [ 'title' => 'Row',    'icon' => 'eicon-arrow-right' ],
				'column' => [ 'title' => 'Column', 'icon' => 'eicon-arrow-down' ],
			],
			'default'   => 'row',
			'condition' => [ 'layout_type' => 'flex' ],
			'selectors' => [ '{{WRAPPER}} .st-fb-wrap' => 'flex-direction: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'flex_wrap', [
			'label'     => __( 'Wrap', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'wrap',
			'options'   => [ 'wrap' => 'Wrap', 'nowrap' => 'No Wrap' ],
			'condition' => [ 'layout_type' => 'flex' ],
			'selectors' => [ '{{WRAPPER}} .st-fb-wrap' => 'flex-wrap: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'justify_content', [
			'label'     => __( 'Justify Content', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'flex-start',
			'options'   => [
				'flex-start'    => 'Start',
				'center'        => 'Center',
				'flex-end'      => 'End',
				'space-between' => 'Space Between',
				'space-around'  => 'Space Around',
				'space-evenly'  => 'Space Evenly',
			],
			'condition' => [ 'layout_type' => 'flex' ],
			'selectors' => [ '{{WRAPPER}} .st-fb-wrap' => 'justify-content: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'align_items', [
			'label'     => __( 'Align Items', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'stretch',
			'options'   => [
				'flex-start' => 'Start',
				'center'     => 'Center',
				'flex-end'   => 'End',
				'stretch'    => 'Stretch',
			],
			'condition' => [ 'layout_type' => 'flex' ],
			'selectors' => [ '{{WRAPPER}} .st-fb-wrap' => 'align-items: {{VALUE}};' ],
		] );

		// Grid options
		$this->add_responsive_control( 'grid_columns', [
			'label'              => __( 'Columns', 'st-elementor' ),
			'type'               => Controls_Manager::NUMBER,
			'default'            => 3,
			'tablet_default'     => 2,
			'mobile_default'     => 1,
			'min'                => 1,
			'max'                => 6,
			'condition'          => [ 'layout_type' => 'grid' ],
			'frontend_available' => true,
			'selectors'          => [
				'{{WRAPPER}} .st-fb-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
			],
		] );

		$this->add_responsive_control( 'grid_col_gap', [
			'label'      => __( 'Column Gap', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'    => [ 'size' => 24 ],
			'condition'  => [ 'layout_type' => 'grid' ],
			'selectors'  => [ '{{WRAPPER}} .st-fb-grid' => 'column-gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'grid_row_gap', [
			'label'      => __( 'Row Gap', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'    => [ 'size' => 24 ],
			'condition'  => [ 'layout_type' => 'grid' ],
			'selectors'  => [ '{{WRAPPER}} .st-fb-grid' => 'row-gap: {{SIZE}}{{UNIT}};' ],
		] );

		// Slider options
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

		$this->add_control( 'pagination_position', [
			'label'     => __( 'Pagination Position', 'st-elementor' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'top'    => [ 'title' => __( 'Top', 'st-elementor' ),    'icon' => 'eicon-v-align-top' ],
				'bottom' => [ 'title' => __( 'Bottom', 'st-elementor' ), 'icon' => 'eicon-v-align-bottom' ],
			],
			'default'   => 'bottom',
			'toggle'    => false,
			'condition' => [ 'layout_type' => 'slider' ],
		] );

		$this->add_control( 'arrow_prev_icon', [
			'label'     => __( 'Prev Arrow Icon', 'st-elementor' ),
			'type'      => Controls_Manager::ICONS,
			'default'   => [ 'value' => 'fas fa-chevron-left', 'library' => 'fa-solid' ],
			'separator' => 'before',
			'condition' => [ 'layout_type' => 'slider' ],
		] );

		$this->add_control( 'arrow_next_icon', [
			'label'     => __( 'Next Arrow Icon', 'st-elementor' ),
			'type'      => Controls_Manager::ICONS,
			'default'   => [ 'value' => 'fas fa-chevron-right', 'library' => 'fa-solid' ],
			'condition' => [ 'layout_type' => 'slider' ],
		] );

		$this->add_responsive_control( 'gap', [
			'label'      => __( 'Gap', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'    => [ 'size' => 20 ],
			'selectors'  => [
				'{{WRAPPER}} .st-fb-wrap'       => 'gap: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .swiper-container' => '--swiper-gap: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();

		// ── Items Repeater ───────────────────────────────────────────
		$this->start_controls_section( 'section_items', [
			'label' => __( 'Items', 'st-elementor' ),
		] );

		$repeater = new Repeater();

		// Item content type
		$repeater->add_control( 'item_type', [
			'label'   => __( 'Content', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'card',
			'options' => [
				'card'  => __( 'Card (Image + Text)', 'st-elementor' ),
				'image' => __( 'Image Only', 'st-elementor' ),
				'text'  => __( 'Text Only', 'st-elementor' ),
				'icon'  => __( 'Icon Box', 'st-elementor' ),
			],
		] );

		// Image
		$repeater->add_control( 'image', [
			'label'     => __( 'Image', 'st-elementor' ),
			'type'      => Controls_Manager::MEDIA,
			'default'   => [ 'url' => Utils::get_placeholder_image_src() ],
			'condition' => [ 'item_type' => [ 'card', 'image' ] ],
		] );

		// Lightbox
		$repeater->add_control( 'enable_lightbox', [
			'label'        => __( 'Enable Lightbox', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
			'description'  => __( 'Click image to open in fullscreen lightbox.', 'st-elementor' ),
			'condition'    => [ 'item_type' => 'image' ],
		] );

		// Icon
		$repeater->add_control( 'icon', [
			'label'     => __( 'Icon', 'st-elementor' ),
			'type'      => Controls_Manager::ICONS,
			'default'   => [ 'value' => 'fas fa-star', 'library' => 'fa-solid' ],
			'condition' => [ 'item_type' => 'icon' ],
		] );

		// Text fields
		$repeater->add_control( 'title', [
			'label'   => __( 'Title', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Item Title', 'st-elementor' ),
		] );

		$repeater->add_control( 'description', [
			'label'   => __( 'Description', 'st-elementor' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => __( 'Item description text goes here.', 'st-elementor' ),
			'rows'    => 3,
		] );

		$repeater->add_control( 'btn_text', [
			'label'   => __( 'Button Text', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '',
		] );

		$repeater->add_control( 'btn_link', [
			'label'   => __( 'Button Link', 'st-elementor' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		// Overlay
		$repeater->add_control( 'show_overlay', [
			'label'        => __( 'Show Overlay', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
			'separator'    => 'before',
		] );

		$repeater->add_control( 'overlay_color', [
			'label'     => __( 'Overlay Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.5)',
			'condition' => [ 'show_overlay' => 'yes' ],
		] );

		$this->add_control( 'items', [
			'label'       => __( 'Items', 'st-elementor' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[ 'title' => 'Item 1', 'description' => 'Description here.' ],
				[ 'title' => 'Item 2', 'description' => 'Description here.' ],
				[ 'title' => 'Item 3', 'description' => 'Description here.' ],
			],
			'title_field' => '{{{ title }}}',
		] );

		$this->end_controls_section();

		// ═══ STYLE ══════════════════════════════════════════════════

		// ── Wrapper ──────────────────────────────────────────────────
		$this->start_controls_section( 'style_wrap', [
			'label' => __( 'Wrapper', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'wrap_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-fb-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Item Card ────────────────────────────────────────────────
		$this->start_controls_section( 'style_item', [
			'label' => __( 'Item Card', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'item_bg',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-fb-item',
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'item_border',
			'selector' => '{{WRAPPER}} .st-fb-item',
		] );

		$this->add_responsive_control( 'item_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-fb-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'item_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '20', 'right' => '20', 'bottom' => '20', 'left' => '20', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-fb-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'item_shadow',
			'selector' => '{{WRAPPER}} .st-fb-item',
		] );
		$this->add_control( 'grid_custom_columns', [
			'label'       => __( 'Custom Grid Columns', 'st-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => 'e.g. 1fr 2fr 1fr OR repeat(4,1fr)',
			'description' => __( 'Leave empty to use default columns.', 'st-elementor' ),
			'condition'   => [
				'layout_type' => 'grid',
			],
		] );
		// Image
		$this->add_control( 'img_heading', [
			'label'     => __( 'Image', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_responsive_control( 'img_height', [
			'label'      => __( 'Image Height', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [ 'px' => [ 'min' => 50, 'max' => 600 ] ],
			'default'    => [ 'size' => 200, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .st-fb-img' => 'height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'img_fit', [
			'label'     => __( 'Object Fit', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'cover',
			'options'   => [ 'cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill' ],
			'selectors' => [ '{{WRAPPER}} .st-fb-img img' => 'object-fit: {{VALUE}};' ],
		] );

		// Title
		$this->add_control( 'title_heading', [
			'label'     => __( 'Title', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'title_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-fb-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'title_typo', 'selector' => '{{WRAPPER}} .st-fb-title',
		] );

		// Description
		$this->add_control( 'desc_heading', [
			'label'     => __( 'Description', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'desc_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-fb-desc' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'desc_typo', 'selector' => '{{WRAPPER}} .st-fb-desc',
		] );

		// Button
		$this->add_control( 'btn_heading', [
			'label'     => __( 'Button', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'btn_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-fb-btn' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-fb-btn' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'btn_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-fb-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Icon Style ───────────────────────────────────────────────
		$this->start_controls_section( 'style_icon', [
			'label' => __( 'Icon', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'icon_color', [
			'label'     => __( 'Icon Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [
				'{{WRAPPER}} .st-fb-icon'     => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-fb-icon svg' => 'fill: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'icon_size', [
			'label'      => __( 'Icon Size', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 10, 'max' => 120 ] ],
			'default'    => [ 'size' => 40 ],
			'selectors'  => [
				'{{WRAPPER}} .st-fb-icon'     => 'font-size: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .st-fb-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'icon_bg_color', [
			'label'     => __( 'Background Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-fb-icon' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'icon_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'selectors'  => [ '{{WRAPPER}} .st-fb-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'icon_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-fb-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'icon_border',
			'selector' => '{{WRAPPER}} .st-fb-icon',
		] );

		$this->add_responsive_control( 'icon_spacing', [
			'label'     => __( 'Spacing Below Icon', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'   => [ 'size' => 16 ],
			'selectors' => [ '{{WRAPPER}} .st-fb-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Slider Navigation (Arrows) ───────────────────────────────
		$this->start_controls_section( 'style_arrows', [
			'label'     => __( 'Slider Arrows', 'st-elementor' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'layout_type' => 'slider' ],
		] );

		$this->add_control( 'arrow_color', [
			'label'     => __( 'Arrow Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#374151',
			'selectors' => [
				'{{WRAPPER}} .st-fb-arrow'        => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-fb-arrow i'      => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-fb-arrow svg'    => 'fill: {{VALUE}};',
				'{{WRAPPER}} .st-fb-arrow path'   => 'fill: {{VALUE}};',
			],
		] );

		$this->add_control( 'arrow_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-fb-arrow' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'arrow_hover_color', [
			'label'     => __( 'Hover Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-fb-arrow:hover'      => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-fb-arrow:hover i'    => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-fb-arrow:hover svg'  => 'fill: {{VALUE}};',
				'{{WRAPPER}} .st-fb-arrow:hover path' => 'fill: {{VALUE}};',
			],
		] );

		$this->add_control( 'arrow_hover_bg', [
			'label'     => __( 'Hover Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-fb-arrow:hover' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'arrow_size', [
			'label'     => __( 'Button Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 20, 'max' => 80 ] ],
			'default'   => [ 'size' => 40 ],
			'selectors' => [ '{{WRAPPER}} .st-fb-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'arrow_icon_size', [
			'label'     => __( 'Icon Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 8, 'max' => 40 ] ],
			'default'   => [ 'size' => 14 ],
			'selectors' => [ '{{WRAPPER}} .st-fb-arrow::after' => 'font-size: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'arrow_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-fb-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'arrow_shadow',
			'selector' => '{{WRAPPER}} .st-fb-arrow',
		] );

		$this->end_controls_section();

		// ── Slider Pagination (Dots) ─────────────────────────────────
		$this->start_controls_section( 'style_pagination', [
			'label'     => __( 'Slider Pagination', 'st-elementor' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'layout_type' => 'slider' ],
		] );

		$this->add_control( 'dot_color', [
			'label'     => __( 'Dot Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(99,102,241,0.4)',
			'selectors' => [ '{{WRAPPER}} .st-fb-dots .swiper-pagination-bullet' => 'background-color: {{VALUE}}; opacity: 1;' ],
		] );

		$this->add_control( 'dot_active_color', [
			'label'     => __( 'Active Dot Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-fb-dots .swiper-pagination-bullet-active' => 'background-color: {{VALUE}}; opacity: 1;' ],
		] );

		$this->add_responsive_control( 'dot_size', [
			'label'     => __( 'Dot Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 4, 'max' => 24 ] ],
			'default'   => [ 'size' => 10 ],
			'selectors' => [ '{{WRAPPER}} .st-fb-dots .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'dot_active_size', [
			'label'     => __( 'Active Dot Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 4, 'max' => 30 ] ],
			'selectors' => [ '{{WRAPPER}} .st-fb-dots .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'dot_spacing', [
			'label'     => __( 'Dot Spacing', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 2, 'max' => 20 ] ],
			'default'   => [ 'size' => 4 ],
			'selectors' => [ '{{WRAPPER}} .st-fb-dots .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'dot_border_radius', [
			'label'     => __( 'Border Radius', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
			'default'   => [ 'size' => 50 ],
			'selectors' => [ '{{WRAPPER}} .st-fb-dots .swiper-pagination-bullet' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'dots_top_spacing', [
			'label'     => __( 'Spacing Above Dots', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'   => [ 'size' => 20 ],
			'selectors' => [ '{{WRAPPER}} .st-fb-dots' => 'margin-top: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$type = $s['layout_type'] ?? 'grid';

		$wrap_class = 'st-fb-wrap st-fb-' . esc_attr( $type );

		// Grid inline style
		$wrap_style = '';

		if ( 'grid' === $type ) {
			// '' ya 0 aaye toh default 3 use karo
			$cols_raw = $s['grid_columns'] ?? '';
			$cols     = ( '' !== $cols_raw && (int) $cols_raw > 0 ) ? (int) $cols_raw : 3;
			$col_gap  = max( 0, (int) ( $s['grid_col_gap']['size']  ?? 24 ) );
			$row_gap  = max( 0, (int) ( $s['grid_row_gap']['size']  ?? 24 ) );

			$custom_cols = $s['grid_custom_columns'] ?? '';

			if ( ! empty( $custom_cols ) ) {
				$wrap_style = 'grid-template-columns:' . esc_attr( $custom_cols ) . ';column-gap:' . $col_gap . 'px;row-gap:' . $row_gap . 'px;';
			} else {
				$wrap_style = 'grid-template-columns:repeat(' . $cols . ',1fr);column-gap:' . $col_gap . 'px;row-gap:' . $row_gap . 'px;';
			}
		}

		// Slider data
		$slider_data = '';
		if ( 'slider' === $type ) {
			$spv_raw     = $s['slides_per_view']        ?? '';
			$spv_tab_raw = $s['slides_per_view_tablet'] ?? '';
			$spv_mob_raw = $s['slides_per_view_mobile'] ?? '';
			$spv      = max( 1, (int) ( $spv_raw     !== '' ? $spv_raw     : 3 ) );
			$spv_tab  = max( 1, (int) ( $spv_tab_raw !== '' ? $spv_tab_raw : 2 ) );
			$spv_mob  = max( 1, (int) ( $spv_mob_raw !== '' ? $spv_mob_raw : 1 ) );
			$gap      = (int) ( $s['gap']['size']            ?? 20 );
			$autoplay = 'yes' === ( $s['slider_autoplay']    ?? 'yes' );
			$delay    = (int) ( $s['slider_autoplay_delay']  ?? 3000 );
			$loop     = 'yes' === ( $s['slider_loop']        ?? 'yes' );
			$speed    = (int) ( $s['slider_speed']           ?? 500 );
			$slider_data = wp_json_encode( [
				'slidesPerView' => $spv,
				'slidesTablet'  => $spv_tab,
				'slidesMobile'  => $spv_mob,
				'spaceBetween'  => $gap,
				'autoplay'      => $autoplay,
				'autoplayDelay' => $delay,
				'loop'          => $loop,
				'speed'         => $speed,
			] );
			$wrap_class .= ' swiper';
		}

		// Pagination position
		$pag_position = $s['pagination_position'] ?? 'bottom';
		$dots_class   = 'swiper-pagination st-fb-dots st-dots-' . esc_attr( $pag_position );
		$show_nav     = 'slider' === $type;
		?>
		<div class="st-fb-container">
			<div class="<?php echo esc_attr( $wrap_class ); ?>"
			     <?php if ( $wrap_style ) echo 'style="' . esc_attr( $wrap_style ) . '"'; ?>
			     <?php if ( $slider_data ) echo 'data-slider="' . esc_attr( $slider_data ) . '"'; ?>>

				<?php if ( 'slider' === $type ) : ?><div class="swiper-wrapper"><?php endif; ?>

				<?php foreach ( $s['items'] as $item ) :
					$item_class  = 'st-fb-item elementor-repeater-item-' . $item['_id'];
					$item_type   = $item['item_type'] ?? 'card';
					if ( 'slider' === $type ) $item_class .= ' swiper-slide';
					if ( 'image'  === $item_type ) $item_class .= ' st-fb-item-image-only';
					?>
					<div class="<?php echo esc_attr( $item_class ); ?>">

						<?php if ( 'yes' === ( $item['show_overlay'] ?? '' ) ) : ?>
						<div class="st-fb-overlay" style="background:<?php echo esc_attr( $item['overlay_color'] ?? 'rgba(0,0,0,0.5)' ); ?>"></div>
						<?php endif; ?>

						<?php if ( in_array( $item_type, [ 'card', 'image' ], true ) && ! empty( $item['image']['url'] ) ) :
							$lightbox = ( 'image' === $item_type ) && ( 'yes' === ( $item['enable_lightbox'] ?? '' ) );
						?>
						<div class="st-fb-img<?php echo $lightbox ? ' st-fb-lightbox-trigger' : ''; ?>"
						     <?php if ( $lightbox ) echo 'data-src="' . esc_url( $item['image']['url'] ) . '" data-lightbox="st-fb-gallery" role="button" tabindex="0"'; ?>>
							<img src="<?php echo esc_url( $item['image']['url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ?? '' ); ?>">
							<?php if ( $lightbox ) : ?>
							<span class="st-fb-lightbox-icon"><i class="fas fa-expand"></i></span>
							<?php endif; ?>
						</div>
						<?php endif; ?>

						<?php if ( 'icon' === $item_type && ! empty( $item['icon']['value'] ) ) : ?>
						<div class="st-fb-icon">
							<?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?>
						</div>
						<?php endif; ?>

						<?php if ( 'image' !== $item_type ) : ?>
						<div class="st-fb-body">
							<?php if ( ! empty( $item['title'] ) ) : ?>
							<h3 class="st-fb-title"><?php echo esc_html( $item['title'] ); ?></h3>
							<?php endif; ?>

							<?php if ( ! empty( $item['description'] ) ) : ?>
							<p class="st-fb-desc"><?php echo esc_html( $item['description'] ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $item['btn_text'] ) ) :
								$this->add_link_attributes( 'btn_' . $item['_id'], $item['btn_link'] );
								?>
							<a class="st-fb-btn" <?php echo $this->get_render_attribute_string( 'btn_' . $item['_id'] ); ?>>
								<?php echo esc_html( $item['btn_text'] ); ?>
							</a>
							<?php endif; ?>
						</div>
						<?php endif; ?>

					</div>
				<?php endforeach; ?>

				<?php if ( 'slider' === $type ) : ?></div>
					<?php if ( 'top' === $pag_position ) : ?>
					<div class="<?php echo esc_attr( $dots_class ); ?>"></div>
					<?php endif; ?>
					<div class="swiper-button-prev st-fb-arrow">
						<?php if ( ! empty( $s['arrow_prev_icon']['value'] ) ) \Elementor\Icons_Manager::render_icon( $s['arrow_prev_icon'], [ 'aria-hidden' => 'true' ] ); ?>
					</div>
					<div class="swiper-button-next st-fb-arrow">
						<?php if ( ! empty( $s['arrow_next_icon']['value'] ) ) \Elementor\Icons_Manager::render_icon( $s['arrow_next_icon'], [ 'aria-hidden' => 'true' ] ); ?>
					</div>
					<?php if ( 'bottom' === $pag_position ) : ?>
					<div class="<?php echo esc_attr( $dots_class ); ?>"></div>
					<?php endif; ?>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}
}
