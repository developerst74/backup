<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ST_Slider extends Widget_Base {

	public function get_name() {
		return 'st_slider';
	}

	public function get_title() {
		return __( 'ST Pro Slider', 'st-elementor' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_categories() {
		return [ 'st-store-transform' ];
	}

	public function get_script_depends() {
		return [ 'st-elementor-public' ];
	}

	protected function register_controls() {

		// --- CONTENT TAB ---

		$this->start_controls_section(
			'section_slides',
			[
				'label' => __( 'Slides', 'st-elementor' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'slides_repeater_tabs' );

		$repeater->start_controls_tab( 'background_tab', [ 'label' => __( 'Background', 'st-elementor' ) ] );

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'slide_background',
				'label' => __( 'Background', 'st-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			]
		);

		$repeater->add_control(
			'show_overlay',
			[
				'label' => __( 'Background Overlay', 'st-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'st-elementor' ),
				'label_off' => __( 'Hide', 'st-elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_background',
				'label' => __( 'Overlay Background', 'st-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .st-pro-slide-overlay',
				'condition' => [
					'show_overlay' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'overlay_opacity',
			[
				'label' => __( 'Opacity', 'st-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .st-pro-slide-overlay' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'show_overlay' => 'yes',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'style_tab', [ 'label' => __( 'Style', 'st-elementor' ) ] );

		$repeater->add_control(
			'content_animation',
			[
				'label' => __( 'Content Animation', 'st-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fadeInUp',
				'options' => [
					'' => __( 'None', 'st-elementor' ),
					'fadeInUp' => __( 'Fade In Up', 'st-elementor' ),
					'fadeInDown' => __( 'Fade In Down', 'st-elementor' ),
					'fadeInLeft' => __( 'Fade In Left', 'st-elementor' ),
					'fadeInRight' => __( 'Fade In Right', 'st-elementor' ),
					'zoomIn' => __( 'Zoom In', 'st-elementor' ),
				],
			]
		);

		$repeater->add_control(
			'animation_duration',
			[
				'label' => __( 'Animation Duration (ms)', 'st-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 1000,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .st-pro-content-inner' => 'animation-duration: {{VALUE}}ms;',
				],
				'condition' => [
					'content_animation!' => '',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'content_tab', [ 'label' => __( 'Content', 'st-elementor' ) ] );

		$repeater->add_control(
			'sub_title',
			[
				'label' => __( 'Subheading', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Premium Quality', 'st-elementor' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'sub_title_tag',
			[
				'label'   => __( 'Subheading Tag', 'st-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => [ 'h1'=>'H1','h2'=>'H2','h3'=>'H3','h4'=>'H4','h5'=>'H5','h6'=>'H6','div'=>'div','p'=>'p','span'=>'span' ],
			]
		);

		$repeater->add_control(
			'heading',
			[
				'label' => __( 'Heading', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Modern Design Slider', 'st-elementor' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'heading_tag',
			[
				'label'   => __( 'Heading Tag', 'st-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => [ 'h1'=>'H1','h2'=>'H2','h3'=>'H3','h4'=>'H4','h5'=>'H5','h6'=>'H6','div'=>'div','p'=>'p' ],
			]
		);

		$repeater->add_control(
			'description',
			[
				'label' => __( 'Paragraph', 'st-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Experience the next level of design with our professional slider widget.', 'st-elementor' ),
			]
		);

		// ── Layout & Form ─────────────────────────────────────────────
		$repeater->add_control( 'slide_layout', [
			'label'     => __( 'Slide Layout', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'content-only',
			'separator' => 'before',
			'options'   => [
				'content-only'    => __( 'Content Only', 'st-elementor' ),
				'content-left'    => __( 'Content Left + Form Right', 'st-elementor' ),
				'content-right'   => __( 'Content Right + Form Left', 'st-elementor' ),
				'content-center'  => __( 'Content Center + Form Below', 'st-elementor' ),
			],
		] );

		$repeater->add_control( 'content_position', [
			'label'   => __( 'Content Alignment', 'st-elementor' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => [
				'flex-start' => [ 'title' => 'Left',   'icon' => 'eicon-h-align-left' ],
				'center'     => [ 'title' => 'Center', 'icon' => 'eicon-h-align-center' ],
				'flex-end'   => [ 'title' => 'Right',  'icon' => 'eicon-h-align-right' ],
			],
			'default' => 'center',
		] );

		// Right side — Image OR Form (only one)
		$repeater->add_control( 'right_content_type', [
			'label'     => __( 'Right Side', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'form',
			'separator' => 'before',
			'options'   => [
				'form'  => __( 'Form (Shortcode)', 'st-elementor' ),
				'image' => __( 'Image', 'st-elementor' ),
			],
			'condition' => [ 'slide_layout!' => 'content-only' ],
		] );

		// Form
		$repeater->add_control( 'form_shortcode', [
			'label'       => __( 'Form Shortcode', 'st-elementor' ),
			'type'        => Controls_Manager::TEXTAREA,
			'placeholder' => '[contact-form-7 id="1" title="Contact"]',
			'description' => __( 'Paste any form shortcode (CF7, WPForms, Gravity Forms etc.)', 'st-elementor' ),
			'rows'        => 3,
			'condition'   => [ 'slide_layout!' => 'content-only', 'right_content_type' => 'form' ],
		] );

		$repeater->add_control( 'form_bg_color', [
			'label'     => __( 'Form Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.95)',
			'condition' => [ 'slide_layout!' => 'content-only', 'right_content_type' => 'form' ],
		] );

		$repeater->add_control( 'form_border_radius', [
			'label'     => __( 'Form Border Radius', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'   => [ 'size' => 12 ],
			'condition' => [ 'slide_layout!' => 'content-only', 'right_content_type' => 'form' ],
		] );

		$repeater->add_control( 'form_padding', [
			'label'      => __( 'Form Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top' => '30', 'right' => '30', 'bottom' => '30', 'left' => '30', 'unit' => 'px', 'isLinked' => true ],
			'condition'  => [ 'slide_layout!' => 'content-only', 'right_content_type' => 'form' ],
		] );

		// Image
		$repeater->add_control( 'right_image', [
			'label'     => __( 'Image', 'st-elementor' ),
			'type'      => Controls_Manager::MEDIA,
			'condition' => [ 'slide_layout!' => 'content-only', 'right_content_type' => 'image' ],
		] );

		$repeater->add_control( 'right_image_fit', [
			'label'     => __( 'Image Fit', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'cover',
			'options'   => [ 'cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill' ],
			'condition' => [ 'slide_layout!' => 'content-only', 'right_content_type' => 'image' ],
		] );

		$repeater->add_control( 'right_image_radius', [
			'label'     => __( 'Image Border Radius', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'   => [ 'size' => 12 ],
			'condition' => [ 'slide_layout!' => 'content-only', 'right_content_type' => 'image' ],
		] );

		// Right column width (form or image)
		$repeater->add_control( 'right_col_width', [
			'label'       => __( 'Right Column Width', 'st-elementor' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ 'px', '%' ],
			'range'       => [
				'px' => [ 'min' => 100, 'max' => 800 ],
				'%'  => [ 'min' => 10,  'max' => 80 ],
			],
			'default'     => [ 'size' => 45, 'unit' => '%' ],
			'separator'   => 'before',
			'description' => __( 'Width of the form/image column. Content takes remaining space.', 'st-elementor' ),
			'condition'   => [ 'slide_layout!' => 'content-only' ],
		] );

		$repeater->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Shop Now', 'st-elementor' ),
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label' => __( 'Button Link', 'st-elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'st-elementor' ),
			]
		);

		$repeater->add_control(
			'show_review',
			[
				'label' => __( 'Show Review', 'st-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$repeater->add_control(
			'review_rating',
			[
				'label' => __( 'Rating', 'st-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 5,
				'step' => 0.5,
				'default' => 5,
				'condition' => [ 'show_review' => 'yes' ],
			]
		);

		$repeater->add_control(
			'review_text',
			[
				'label' => __( 'Review Text', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Excellent 5-star service!', 'st-elementor' ),
				'condition' => [ 'show_review' => 'yes' ],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'slides',
			[
				'label' => __( 'Slides List', 'st-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'heading' => __( 'Pro Slider #1', 'st-elementor' ),
						'description' => __( 'The best slider experience for your WordPress site.', 'st-elementor' ),
					],
				],
				'title_field' => '{{{ heading }}}',
			]
		);

		$this->end_controls_section();

		// --- SETTINGS SECTION ---

		$this->start_controls_section(
			'section_slider_settings',
			[
				'label' => __( 'Slider Settings', 'st-elementor' ),
			]
		);

		$this->add_control(
			'effect',
			[
				'label' => __( 'Effect', 'st-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => __( 'Slide', 'st-elementor' ),
					'fade'  => __( 'Fade', 'st-elementor' ),
				],
			]
		);

		$this->add_control(
			'navigation',
			[
				'label' => __( 'Navigation', 'st-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both'   => __( 'Arrows and Dots', 'st-elementor' ),
					'arrows' => __( 'Arrows', 'st-elementor' ),
					'dots'   => __( 'Dots', 'st-elementor' ),
					'none'   => __( 'None', 'st-elementor' ),
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'st-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'speed',
			[
				'label' => __( 'Animation Speed (ms)', 'st-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
			]
		);

		$this->add_control(
			'infinite',
			[
				'label' => __( 'Infinite Loop', 'st-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		// --- STYLE TAB ---

		// Slide Style
		$this->start_controls_section(
			'section_style_slides',
			[
				'label' => __( 'Slides', 'st-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'      => __( 'Height', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh', '%' ],
				'range'      => [
					'px' => [ 'min' => 100, 'max' => 1200 ],
					'vh' => [ 'min' => 10,  'max' => 100 ],
					'%'  => [ 'min' => 10,  'max' => 100 ],
				],
				'default'    => [ 'size' => 500, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .st-pro-slide-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control( 'slide_padding', [
			'label'      => __( 'Slide Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [ '{{WRAPPER}} .st-pro-slide-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'slide_margin', [
			'label'      => __( 'Slide Margin', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-pro-slide-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control(
			'content_align',
			[
				'label' => __( 'Alignment', 'st-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'st-elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'st-elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'st-elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .st-pro-slide-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control( 'content_max_width', [
			'label'      => __( 'Content Max Width', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [ 'px' => [ 'min' => 200, 'max' => 1400 ], '%' => [ 'min' => 10, 'max' => 100 ] ],
			'default'    => [ 'size' => 800, 'unit' => 'px' ],
			'description' => __( 'Control the width of the content area inside the slide.', 'st-elementor' ),
			'selectors'  => [ '{{WRAPPER}} .st-pro-slide-content' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		// ── Content Side Overlay Shadow ───────────────────────────────
		$this->add_control( 'content_overlay_heading', [
			'label'     => __( 'Content Side Overlay', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'content_overlay_enable', [
			'label'        => __( 'Enable Side Overlay', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
			'description'  => __( 'Adds a gradient shadow on the content side for better readability.', 'st-elementor' ),
		] );

		$this->add_control( 'content_overlay_side', [
			'label'     => __( 'Overlay Side', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'left',
			'options'   => [
				'left'   => __( 'Left', 'st-elementor' ),
				'right'  => __( 'Right', 'st-elementor' ),
				'center' => __( 'Center (both sides)', 'st-elementor' ),
				'full'   => __( 'Full (bottom fade)', 'st-elementor' ),
			],
			'condition' => [ 'content_overlay_enable' => 'yes' ],
		] );

		$this->add_control( 'content_overlay_color', [
			'label'     => __( 'Overlay Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#000000',
			'condition' => [ 'content_overlay_enable' => 'yes' ],
		] );

		$this->add_responsive_control( 'content_overlay_opacity', [
			'label'     => __( 'Opacity', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
			'default'   => [ 'size' => 0.6 ],
			'condition' => [ 'content_overlay_enable' => 'yes' ],
		] );

		$this->add_responsive_control( 'content_overlay_width', [
			'label'     => __( 'Overlay Width (%)', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 10, 'max' => 100 ] ],
			'default'   => [ 'size' => 60 ],
			'condition' => [ 'content_overlay_enable' => 'yes' ],
		] );

		$this->end_controls_section();

		// Content Style
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content Typography', 'st-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		// Sub-title
		$this->add_control( 'heading_sub_title', [ 'label' => __( 'Subheading', 'st-elementor' ), 'type' => Controls_Manager::HEADING ] );
		$this->add_control( 'sub_title_color', [ 'label' => __( 'Color', 'st-elementor' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .st-pro-subheading' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'sub_title_typography', 'selector' => '{{WRAPPER}} .st-pro-subheading' ] );
		$this->add_responsive_control( 'sub_title_spacing', [ 'label' => __( 'Spacing', 'st-elementor' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .st-pro-subheading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		// Heading
		$this->add_control( 'heading_title', [ 'label' => __( 'Heading', 'st-elementor' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'title_color', [ 'label' => __( 'Color', 'st-elementor' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .st-pro-heading' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .st-pro-heading' ] );
		$this->add_responsive_control( 'title_spacing', [ 'label' => __( 'Spacing', 'st-elementor' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .st-pro-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		// Paragraph
		$this->add_control( 'heading_desc', [ 'label' => __( 'Description', 'st-elementor' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'desc_color', [ 'label' => __( 'Color', 'st-elementor' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .st-pro-desc' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'desc_typography', 'selector' => '{{WRAPPER}} .st-pro-desc' ] );
		$this->add_responsive_control( 'desc_spacing', [ 'label' => __( 'Spacing', 'st-elementor' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .st-pro-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		// Review
		$this->add_control( 'heading_review', [ 'label' => __( 'Review', 'st-elementor' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'review_color', [ 'label' => __( 'Color', 'st-elementor' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .st-pro-review' => 'color: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'review_spacing', [ 'label' => __( 'Spacing', 'st-elementor' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .st-pro-review' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

		$this->end_controls_section();

		// Button Style
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => __( 'Button', 'st-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab( 'button_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );
		$this->add_control( 'btn_color', [ 'label' => __( 'Text Color', 'st-elementor' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .st-pro-btn' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'btn_bg_color', [ 'label' => __( 'Background Color', 'st-elementor' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .st-pro-btn' => 'background-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'button_hover', [ 'label' => __( 'Hover', 'st-elementor' ) ] );
		$this->add_control( 'btn_hover_color', [ 'label' => __( 'Text Color', 'st-elementor' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .st-pro-btn:hover' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'btn_hover_bg_color', [ 'label' => __( 'Background Color', 'st-elementor' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .st-pro-btn:hover' => 'background-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'btn_padding',
			[
				'label' => __( 'Padding', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [ '{{WRAPPER}} .st-pro-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// ── STYLE: ARROWS ────────────────────────────────────────────────
		$this->start_controls_section(
			'section_style_arrows',
			[
				'label'     => __( 'Arrows', 'st-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'navigation' => [ 'both', 'arrows' ] ],
			]
		);

		// Arrow Icon
		$this->add_control(
			'arrow_prev_icon',
			[
				'label'   => __( 'Prev Icon', 'st-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [ 'value' => 'eicon-chevron-left', 'library' => 'eicons' ],
			]
		);

		$this->add_control(
			'arrow_next_icon',
			[
				'label'   => __( 'Next Icon', 'st-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [ 'value' => 'eicon-chevron-right', 'library' => 'eicons' ],
			]
		);

		// Arrow Position
		$this->add_control(
			'arrow_position_heading',
			[
				'label'     => __( 'Position', 'st-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'arrow_vertical_position',
			[
				'label'      => __( 'Vertical Position (%)', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [ '%' => [ 'min' => 0, 'max' => 100 ], 'px' => [ 'min' => 0, 'max' => 500 ] ],
				'default'    => [ 'unit' => '%', 'size' => 50 ],
				'selectors'  => [
					'{{WRAPPER}} .st-pro-arrow' => 'top: {{SIZE}}{{UNIT}}; transform: translateY(-50%);',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_side_offset',
			[
				'label'      => __( 'Side Offset', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => -50, 'max' => 100 ] ],
				'default'    => [ 'size' => 10 ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button-prev.st-pro-arrow' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-button-next.st-pro-arrow' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Arrow Size
		$this->add_responsive_control(
			'arrow_size',
			[
				'label'      => __( 'Icon Size', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'separator'  => 'before',
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 60 ] ],
				'default'    => [ 'size' => 20 ],
				'selectors'  => [
					'{{WRAPPER}} .st-pro-arrow i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .st-pro-arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_box_size',
			[
				'label'      => __( 'Button Size', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 20, 'max' => 120 ] ],
				'default'    => [ 'size' => 50 ],
				'selectors'  => [
					'{{WRAPPER}} .st-pro-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Arrow Colors — Normal / Hover tabs
		$this->start_controls_tabs( 'arrow_color_tabs' );

		$this->start_controls_tab( 'arrow_normal_tab', [ 'label' => __( 'Normal', 'st-elementor' ) ] );

		$this->add_control(
			'arrow_icon_color',
			[
				'label'     => __( 'Icon Color', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .st-pro-arrow i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .st-pro-arrow svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label'     => __( 'Background Color', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.3)',
				'selectors' => [ '{{WRAPPER}} .st-pro-arrow' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'arrow_border_color',
			[
				'label'     => __( 'Border Color', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .st-pro-arrow' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'arrow_hover_tab', [ 'label' => __( 'Hover', 'st-elementor' ) ] );

		$this->add_control(
			'arrow_icon_hover_color',
			[
				'label'     => __( 'Icon Color', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-pro-arrow:hover i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .st-pro-arrow:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_hover_color',
			[
				'label'     => __( 'Background Color', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .st-pro-arrow:hover' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Arrow Border Radius
		$this->add_responsive_control(
			'arrow_border_radius',
			[
				'label'      => __( 'Border Radius', 'st-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator'  => 'before',
				'default'    => [ 'top' => '50', 'right' => '50', 'bottom' => '50', 'left' => '50', 'unit' => '%', 'isLinked' => true ],
				'selectors'  => [
					'{{WRAPPER}} .st-pro-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ── STYLE: PAGINATION (DOTS) ─────────────────────────────────────
		$this->start_controls_section(
			'section_style_pagination',
			[
				'label'     => __( 'Pagination', 'st-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'navigation' => [ 'both', 'dots' ] ],
			]
		);

		$this->add_responsive_control(
			'dots_bottom_offset',
			[
				'label'      => __( 'Bottom Offset', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'default'    => [ 'size' => 20 ],
				'selectors'  => [ '{{WRAPPER}} .st-pro-dots' => 'bottom: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'dot_size',
			[
				'label'      => __( 'Dot Size', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 30 ] ],
				'default'    => [ 'size' => 10 ],
				'selectors'  => [
					'{{WRAPPER}} .st-pro-dots .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_spacing',
			[
				'label'      => __( 'Dot Spacing', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
				'default'    => [ 'size' => 4 ],
				'selectors'  => [
					'{{WRAPPER}} .st-pro-dots .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dot_color',
			[
				'label'     => __( 'Dot Color', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.5)',
				'selectors' => [
					'{{WRAPPER}} .st-pro-dots .swiper-pagination-bullet' => 'background-color: {{VALUE}}; opacity: 1;',
				],
			]
		);

		$this->add_control(
			'dot_active_color',
			[
				'label'     => __( 'Active Dot Color', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .st-pro-dots .swiper-pagination-bullet-active' => 'background-color: {{VALUE}}; opacity: 1;',
				],
			]
		);

		$this->add_responsive_control(
			'dot_active_size',
			[
				'label'      => __( 'Active Dot Size', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 40 ] ],
				'selectors'  => [
					'{{WRAPPER}} .st-pro-dots .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_border_radius',
			[
				'label'      => __( 'Border Radius', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'unit' => '%', 'size' => 50 ],
				'selectors'  => [
					'{{WRAPPER}} .st-pro-dots .swiper-pagination-bullet' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ── STYLE: Form ──────────────────────────────────────────────────
		$this->start_controls_section( 'section_style_form', [
			'label' => __( 'Slide Form', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		// Form box
		$this->add_control( 'form_box_bg', [
			'label'     => __( 'Form Box Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.96)',
			'selectors' => [ '{{WRAPPER}} .st-slide-form-box' => 'background-color: {{VALUE}} !important;' ],
		] );

		$this->add_responsive_control( 'form_box_radius', [
			'label'      => __( 'Form Box Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top'=>'12','right'=>'12','bottom'=>'12','left'=>'12','unit'=>'px','isLinked'=>true ],
			'selectors'  => [ '{{WRAPPER}} .st-slide-form-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'form_box_shadow',
			'selector' => '{{WRAPPER}} .st-slide-form-box',
		] );

		$this->add_responsive_control( 'form_box_padding', [
			'label'      => __( 'Form Box Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top'=>'30','right'=>'30','bottom'=>'30','left'=>'30','unit'=>'px','isLinked'=>true ],
			'selectors'  => [ '{{WRAPPER}} .st-slide-form-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;' ],
		] );

		// Input fields
		$this->add_control( 'form_input_heading', [
			'label'     => __( 'Input Fields', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'form_input_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input:not([type=submit]):not([type=button]):not([type=checkbox]):not([type=radio])' => 'background-color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box textarea' => 'background-color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box select'   => 'background-color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'form_input_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111827',
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input:not([type=submit]):not([type=button])' => 'color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box textarea' => 'color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box select'   => 'color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'form_input_border_color', [
			'label'     => __( 'Border Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#d1d5db',
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input:not([type=submit]):not([type=button])' => 'border-color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box textarea' => 'border-color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box select'   => 'border-color: {{VALUE}} !important;',
			],
		] );

		$this->add_responsive_control( 'form_input_radius', [
			'label'     => __( 'Border Radius', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
			'default'   => [ 'size' => 6 ],
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input:not([type=submit]):not([type=button])' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				'{{WRAPPER}} .st-slide-form-box textarea' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				'{{WRAPPER}} .st-slide-form-box select'   => 'border-radius: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->add_responsive_control( 'form_input_padding', [
			'label'      => __( 'Input Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top'=>'10','right'=>'14','bottom'=>'10','left'=>'14','unit'=>'px','isLinked'=>false ],
			'selectors'  => [
				'{{WRAPPER}} .st-slide-form-box input:not([type=submit]):not([type=button])' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				'{{WRAPPER}} .st-slide-form-box textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'form_input_typo',
			'selector' => '{{WRAPPER}} .st-slide-form-box input:not([type=submit]):not([type=button]), {{WRAPPER}} .st-slide-form-box textarea, {{WRAPPER}} .st-slide-form-box select',
		] );

		// Input height
		$this->add_responsive_control( 'form_input_height', [
			'label'     => __( 'Input Height', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 30, 'max' => 80 ] ],
			'default'   => [ 'size' => 44 ],
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input:not([type=submit]):not([type=button]):not([type=checkbox]):not([type=radio])' => 'height: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		// Textarea height
		$this->add_responsive_control( 'form_textarea_height', [
			'label'     => __( 'Textarea Height', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 60, 'max' => 300 ] ],
			'default'   => [ 'size' => 120 ],
			'selectors' => [ '{{WRAPPER}} .st-slide-form-box textarea' => 'height: {{SIZE}}{{UNIT}} !important;' ],
		] );

		// Field spacing (between fields)
		$this->add_responsive_control( 'form_field_spacing', [
			'label'     => __( 'Field Spacing (between fields)', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'   => [ 'size' => 14 ],
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box .wpcf7-form-control-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				'{{WRAPPER}} .st-slide-form-box p'                        => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				'{{WRAPPER}} .st-slide-form-box .gfield'                  => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				'{{WRAPPER}} .st-slide-form-box .wpforms-field'           => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		// Label to input spacing
		$this->add_responsive_control( 'form_label_spacing', [
			'label'     => __( 'Label to Input Spacing', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
			'default'   => [ 'size' => 5 ],
			'selectors' => [ '{{WRAPPER}} .st-slide-form-box label' => 'margin-bottom: {{SIZE}}{{UNIT}} !important; display: block !important;' ],
		] );

		// Input hover
		$this->add_control( 'form_input_hover_heading', [
			'label'     => __( 'Input Hover', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'form_input_hover_border', [
			'label'     => __( 'Hover Border Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input:not([type=submit]):not([type=button]):hover' => 'border-color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box textarea:hover' => 'border-color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box select:hover'   => 'border-color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'form_input_focus_border', [
			'label'     => __( 'Focus Border Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input:not([type=submit]):not([type=button]):focus' => 'border-color: {{VALUE}} !important; box-shadow: 0 0 0 3px {{VALUE}}33 !important; outline: none !important;',
				'{{WRAPPER}} .st-slide-form-box textarea:focus' => 'border-color: {{VALUE}} !important; box-shadow: 0 0 0 3px {{VALUE}}33 !important; outline: none !important;',
			],
		] );

		$this->add_control( 'form_input_hover_bg', [
			'label'     => __( 'Hover Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input:not([type=submit]):not([type=button]):hover' => 'background-color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box textarea:hover' => 'background-color: {{VALUE}} !important;',
			],
		] );

		// Label
		$this->add_control( 'form_label_heading', [
			'label'     => __( 'Labels', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'form_label_color', [
			'label'     => __( 'Label Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#374151',
			'selectors' => [ '{{WRAPPER}} .st-slide-form-box label' => 'color: {{VALUE}} !important;' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'form_label_typo',
			'selector' => '{{WRAPPER}} .st-slide-form-box label',
		] );

		// Submit button
		$this->add_control( 'form_btn_heading', [
			'label'     => __( 'Submit Button', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'form_btn_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input[type=submit]'  => 'color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box button[type=submit]' => 'color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box .wpcf7-submit'       => 'color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'form_btn_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input[type=submit]'  => 'background-color: {{VALUE}} !important; border-color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box button[type=submit]' => 'background-color: {{VALUE}} !important; border-color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box .wpcf7-submit'       => 'background-color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'form_btn_hover_bg', [
			'label'     => __( 'Hover Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#4f46e5',
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input[type=submit]:hover'  => 'background-color: {{VALUE}} !important;',
				'{{WRAPPER}} .st-slide-form-box button[type=submit]:hover' => 'background-color: {{VALUE}} !important;',
			],
		] );

		$this->add_responsive_control( 'form_btn_radius', [
			'label'     => __( 'Border Radius', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
			'default'   => [ 'size' => 6 ],
			'selectors' => [
				'{{WRAPPER}} .st-slide-form-box input[type=submit]'  => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				'{{WRAPPER}} .st-slide-form-box button[type=submit]' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->add_responsive_control( 'form_btn_padding', [
			'label'      => __( 'Button Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top'=>'12','right'=>'24','bottom'=>'12','left'=>'24','unit'=>'px','isLinked'=>false ],
			'selectors'  => [
				'{{WRAPPER}} .st-slide-form-box input[type=submit]'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				'{{WRAPPER}} .st-slide-form-box button[type=submit]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'form_btn_typo',
			'selector' => '{{WRAPPER}} .st-slide-form-box input[type=submit], {{WRAPPER}} .st-slide-form-box button[type=submit]',
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['slides'] ) ) {
			return;
		}

		$slide_count = count( $settings['slides'] );

		// Swiper loop mode requires at least 3 slides to work correctly.
		// With fewer slides it clones incorrectly and slides vanish.
		$loop = ( 'yes' === $settings['infinite'] ) && ( $slide_count >= 3 );

		$slider_options = [
			'effect'   => $settings['effect'],
			'autoplay' => ( 'yes' === $settings['autoplay'] ),
			'speed'    => (int) $settings['speed'],
			'loop'     => $loop,
		];

		$this->add_render_attribute( 'slider_attr', [
			'class'         => 'st-pro-slider-container swiper',
			'data-settings' => wp_json_encode( $slider_options ),
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'slider_attr' ); ?>>
			<div class="swiper-wrapper">
				<?php foreach ( $settings['slides'] as $index => $slide ) : 
					$slide_id = 'elementor-repeater-item-' . $slide['_id'];
					?>
					<div class="swiper-slide st-pro-slide-item <?php echo esc_attr( $slide_id ); ?>">
						<?php if ( 'yes' === $slide['show_overlay'] ) : ?>
							<div class="st-pro-slide-overlay"></div>
						<?php endif; ?>

						<?php
						// Content side overlay
						$ov_enable  = 'yes' === ( $settings['content_overlay_enable'] ?? '' );
						if ( $ov_enable ) :
							$ov_side    = $settings['content_overlay_side']    ?? 'left';
							$ov_color   = $settings['content_overlay_color']   ?? '#000000';
							$ov_opacity = (float) ( $settings['content_overlay_opacity']['size'] ?? 0.6 );
							$ov_width   = (int) ( $settings['content_overlay_width']['size'] ?? 60 );

							// Build gradient based on side
							$hex = ltrim( $ov_color, '#' );
							$r = hexdec( substr( $hex, 0, 2 ) );
							$g = hexdec( substr( $hex, 2, 2 ) );
							$b = hexdec( substr( $hex, 4, 2 ) );
							$rgba_full  = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $ov_opacity . ')';
							$rgba_trans = 'rgba(' . $r . ',' . $g . ',' . $b . ',0)';

							$gradients = [
								'left'   => 'linear-gradient(to right, ' . $rgba_full . ' 0%, ' . $rgba_trans . ' ' . $ov_width . '%)',
								'right'  => 'linear-gradient(to left, '  . $rgba_full . ' 0%, ' . $rgba_trans . ' ' . $ov_width . '%)',
								'center' => 'linear-gradient(to right, ' . $rgba_full . ' 0%, ' . $rgba_trans . ' 30%, ' . $rgba_trans . ' 70%, ' . $rgba_full . ' 100%)',
								'full'   => 'linear-gradient(to top, '   . $rgba_full . ' 0%, ' . $rgba_trans . ' ' . $ov_width . '%)',
							];
							$gradient = $gradients[ $ov_side ] ?? $gradients['left'];
						?>
						<div class="st-pro-content-overlay" style="position:absolute;inset:0;z-index:2;pointer-events:none;background:<?php echo esc_attr( $gradient ); ?>"></div>
						<?php endif; ?>
						<div class="st-pro-slide-content">
							<div class="st-pro-content-inner animated" data-animation="<?php echo esc_attr( $slide['content_animation'] ); ?>">
								<?php
								$layout       = $slide['slide_layout']      ?? 'content-only';
								$content_pos  = $slide['content_position']  ?? 'center';
								$right_type   = $slide['right_content_type'] ?? 'form';
								$form_sc      = $slide['form_shortcode']     ?? '';
								$form_bg      = $slide['form_bg_color']      ?? 'rgba(255,255,255,0.95)';
								$form_radius  = (int) ( $slide['form_border_radius']['size'] ?? 12 );
								$form_pad     = $slide['form_padding']       ?? [ 'top'=>30,'right'=>30,'bottom'=>30,'left'=>30,'unit'=>'px' ];
								$pad_css      = ( $form_pad['top'] ?? 30 ) . 'px ' . ( $form_pad['right'] ?? 30 ) . 'px ' . ( $form_pad['bottom'] ?? 30 ) . 'px ' . ( $form_pad['left'] ?? 30 ) . 'px';
								$has_form     = ( 'content-only' !== $layout ) && ( 'form' === $right_type ) && ! empty( $form_sc );
								$has_right_img = ( 'content-only' !== $layout ) && ( 'image' === $right_type ) && ! empty( $slide['right_image']['url'] );

								// Right column width
								$right_w_size = $slide['right_col_width']['size'] ?? 45;
								$right_w_unit = $slide['right_col_width']['unit'] ?? '%';
								$right_w_css  = $right_w_size . $right_w_unit;

								$wrap_style = 'justify-content:' . esc_attr( $content_pos ) . ';';
								if ( $has_form || $has_right_img ) {
									$wrap_style = 'justify-content:space-between;align-items:center;';
								}
								?>
								<div class="st-slide-inner-wrap st-layout-<?php echo esc_attr( $layout ); ?>" style="<?php echo $wrap_style; ?>">

									<!-- Content side -->
									<div class="st-slide-content-col" style="flex:1;min-width:0;text-align:<?php echo 'flex-end' === $content_pos ? 'right' : ( 'center' === $content_pos ? 'center' : 'left' ); ?>">
								<?php if ( ! empty( $slide['sub_title'] ) ) :
									$sub_tag = ! empty( $slide['sub_title_tag'] ) ? $slide['sub_title_tag'] : 'div';
									$sub_tag = in_array( $sub_tag, [ 'h1','h2','h3','h4','h5','h6','div','p','span' ], true ) ? $sub_tag : 'div';
								?>
									<<?php echo $sub_tag; ?> class="st-pro-subheading"><?php echo wp_kses_post( $slide['sub_title'] ); ?></<?php echo $sub_tag; ?>>
								<?php endif; ?>

								<?php if ( ! empty( $slide['heading'] ) ) :
									$h_tag = ! empty( $slide['heading_tag'] ) ? $slide['heading_tag'] : 'h2';
									$h_tag = in_array( $h_tag, [ 'h1','h2','h3','h4','h5','h6','div','p' ], true ) ? $h_tag : 'h2';
								?>
									<<?php echo $h_tag; ?> class="st-pro-heading"><?php echo wp_kses_post( $slide['heading'] ); ?></<?php echo $h_tag; ?>>
								<?php endif; ?>

								<?php if ( ! empty( $slide['description'] ) ) : ?>
									<div class="st-pro-desc"><?php echo wp_kses_post( $slide['description'] ); ?></div>
								<?php endif; ?>

								<?php if ( 'yes' === $slide['show_review'] ) : ?>
									<div class="st-pro-review">
										<div class="st-pro-rating">
											<?php 
											$rating = $slide['review_rating'];
											for ( $i = 1; $i <= 5; $i++ ) {
												$icon = ( $i <= $rating ) ? 'eicon-star' : 'eicon-star-o';
												echo '<i class="' . esc_attr( $icon ) . '"></i>';
											}
											?>
										</div>
										<span class="st-pro-review-text"><?php echo esc_html( $slide['review_text'] ); ?></span>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $slide['button_text'] ) && ! empty( $slide['button_link']['url'] ) ) : 
									$this->add_link_attributes( 'button_link_' . $index, $slide['button_link'] );
									?>
									<div class="st-pro-btn-wrapper">
										<a class="st-pro-btn button" <?php echo $this->get_render_attribute_string( 'button_link_' . $index ); ?>>
											<?php echo esc_html( $slide['button_text'] ); ?>
										</a>
									</div>
								<?php endif; ?>
									</div><!-- .st-slide-content-col -->

									<?php if ( $has_form ) : ?>
									<div class="st-slide-form-col" style="flex:0 0 <?php echo esc_attr( $right_w_css ); ?>;max-width:<?php echo esc_attr( $right_w_css ); ?>;">
										<div class="st-slide-form-box" style="background:<?php echo esc_attr( $form_bg ); ?>;border-radius:<?php echo esc_attr( $form_radius ); ?>px;padding:<?php echo esc_attr( $pad_css ); ?>">
											<?php echo do_shortcode( wp_kses_post( $form_sc ) ); ?>
										</div>
									</div>
									<?php endif; ?>

									<?php if ( $has_right_img ) :
										$r_img     = $slide['right_image']['url']    ?? '';
										$r_fit     = $slide['right_image_fit']       ?? 'cover';
										$r_radius  = (int) ( $slide['right_image_radius']['size'] ?? 12 );
									?>
									<div class="st-slide-form-col" style="flex:0 0 <?php echo esc_attr( $right_w_css ); ?>;max-width:<?php echo esc_attr( $right_w_css ); ?>;">
										<div class="st-slide-right-img" style="border-radius:<?php echo esc_attr( $r_radius ); ?>px;overflow:hidden;height:100%;min-height:200px;">
											<img src="<?php echo esc_url( $r_img ); ?>" alt="" style="width:100%;height:100%;object-fit:<?php echo esc_attr( $r_fit ); ?>;display:block;border-radius:<?php echo esc_attr( $r_radius ); ?>px;">
										</div>
									</div>
									<?php endif; ?>

								</div><!-- .st-slide-inner-wrap -->
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( in_array( $settings['navigation'], [ 'both', 'arrows' ] ) ) : ?>
				<div class="swiper-button-prev st-pro-arrow">
					<?php
					if ( ! empty( $settings['arrow_prev_icon']['value'] ) ) {
						\Elementor\Icons_Manager::render_icon( $settings['arrow_prev_icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						echo '<i class="eicon-chevron-left"></i>';
					}
					?>
				</div>
				<div class="swiper-button-next st-pro-arrow">
					<?php
					if ( ! empty( $settings['arrow_next_icon']['value'] ) ) {
						\Elementor\Icons_Manager::render_icon( $settings['arrow_next_icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						echo '<i class="eicon-chevron-right"></i>';
					}
					?>
				</div>
			<?php endif; ?>

			<?php if ( in_array( $settings['navigation'], [ 'both', 'dots' ] ) ) : ?>
				<div class="swiper-pagination st-pro-dots"></div>
			<?php endif; ?>
		</div>

		<?php
	}
}

