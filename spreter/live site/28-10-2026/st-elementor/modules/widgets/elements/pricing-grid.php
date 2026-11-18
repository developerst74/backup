<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ST_Pricing_Grid extends Widget_Base {

	public function get_name() {
		return 'st_pricing_grid';
	}

	public function get_title() {
		return __( 'ST Pricing Grid', 'st-elementor' );
	}

	public function get_icon() {
		return 'eicon-price-list';
	}

	public function get_categories() {
		return [ 'st-store-transform' ];
	}

	protected function register_controls() {

		// ═══════════════════════════════════════════════════════════════
		// CONTENT TAB
		// ═══════════════════════════════════════════════════════════════

		// ── Section Header ───────────────────────────────────────────────
		$this->start_controls_section(
			'section_header',
			[
				'label' => __( 'Section Header', 'st-elementor' ),
			]
		);

		$this->add_control(
			'section_title',
			[
				'label'   => __( 'Section Title', 'st-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Our Pricing Plans', 'st-elementor' ),
			]
		);

		$this->add_control(
			'section_subtitle',
			[
				'label'   => __( 'Section Subtitle', 'st-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => __( 'Choose the plan that works best for you.', 'st-elementor' ),
			]
		);

		$this->add_responsive_control(
			'header_align',
			[
				'label'     => __( 'Alignment', 'st-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => __( 'Left', 'st-elementor' ),   'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => __( 'Center', 'st-elementor' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => __( 'Right', 'st-elementor' ),  'icon' => 'eicon-text-align-right' ],
				],
				'default'   => 'center',
				'selectors' => [ '{{WRAPPER}} .st-pg-section-header' => 'text-align: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		// ── Plans Repeater ───────────────────────────────────────────────
		$this->start_controls_section(
			'section_plans',
			[
				'label' => __( 'Pricing Plans', 'st-elementor' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control( 'plan_title', [
			'label'   => __( 'Plan Title', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Basic', 'st-elementor' ),
		] );

		$repeater->add_control( 'plan_subtitle', [
			'label'   => __( 'Subtitle', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'For individuals', 'st-elementor' ),
		] );

		$repeater->add_control( 'plan_currency', [
			'label'   => __( 'Currency Symbol', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '$',
		] );

		$repeater->add_control( 'plan_price', [
			'label'   => __( 'Price', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '9',
		] );

		$repeater->add_control( 'plan_original_price', [
			'label'       => __( 'Original Price (for discount)', 'st-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => '19',
			'description' => __( 'Shows as strikethrough above the main price.', 'st-elementor' ),
		] );

		$repeater->add_control( 'plan_period', [
			'label'   => __( 'Period', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '/mo',
		] );

		$repeater->add_control( 'plan_features', [
			'label'       => __( 'Features (one per line)', 'st-elementor' ),
			'type'        => Controls_Manager::TEXTAREA,
			'default'     => "5 Projects\n10 GB Storage\nEmail Support",
			'rows'        => 5,
			'description' => __( 'Prefix with "-" to show as crossed out. e.g. "-No API Access"', 'st-elementor' ),
		] );

		$repeater->add_control( 'plan_btn_text', [
			'label'   => __( 'Button Text', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Get Started', 'st-elementor' ),
		] );

		$repeater->add_control( 'plan_btn_link', [
			'label'       => __( 'Button Link', 'st-elementor' ),
			'type'        => Controls_Manager::URL,
			'placeholder' => 'https://your-link.com',
			'default'     => [ 'url' => '#' ],
		] );

		$repeater->add_control( 'plan_featured', [
			'label'        => __( 'Featured Plan', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'no',
			'separator'    => 'before',
		] );

		$repeater->add_control( 'plan_ribbon', [
			'label'     => __( 'Ribbon Label', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'Popular', 'st-elementor' ),
			'condition' => [ 'plan_featured' => 'yes' ],
		] );

		$this->add_control( 'plans', [
			'label'       => __( 'Plans', 'st-elementor' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'plan_title'    => 'Basic',
					'plan_subtitle' => 'For individuals',
					'plan_currency' => '$',
					'plan_price'    => '9',
					'plan_period'   => '/mo',
					'plan_features' => "5 Projects\n10 GB Storage\nEmail Support",
					'plan_btn_text' => 'Get Started',
				],
				[
					'plan_title'    => 'Pro',
					'plan_subtitle' => 'For teams',
					'plan_currency' => '$',
					'plan_price'    => '29',
					'plan_period'   => '/mo',
					'plan_features' => "Unlimited Projects\n50 GB Storage\nPriority Support\nCustom Domain",
					'plan_btn_text' => 'Get Started',
					'plan_featured' => 'yes',
					'plan_ribbon'   => 'Popular',
				],
				[
					'plan_title'    => 'Enterprise',
					'plan_subtitle' => 'For large business',
					'plan_currency' => '$',
					'plan_price'    => '79',
					'plan_period'   => '/mo',
					'plan_features' => "Unlimited Projects\n500 GB Storage\n24/7 Support\nCustom Domain\nDedicated Manager",
					'plan_btn_text' => 'Contact Us',
				],
			],
			'title_field' => '{{{ plan_title }}}',
		] );

		$this->end_controls_section();

		// ── Features Icon ────────────────────────────────────────────────
		$this->start_controls_section( 'section_features_icon', [
			'label' => __( 'Features List', 'st-elementor' ),
		] );

		$this->add_control( 'features_icon_type', [
			'label'   => __( 'Check Icon', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'check',
			'options' => [
				'check' => __( '✓ Checkmark', 'st-elementor' ),
				'dot'   => __( '• Dot', 'st-elementor' ),
				'arrow' => __( '→ Arrow', 'st-elementor' ),
				'star'  => __( '★ Star', 'st-elementor' ),
				'none'  => __( 'None', 'st-elementor' ),
			],
		] );

		$this->end_controls_section();

		// ── Grid Layout ──────────────────────────────────────────────────
		$this->start_controls_section(
			'section_grid',
			[ 'label' => __( 'Grid Settings', 'st-elementor' ) ]
		);

		$this->add_responsive_control( 'columns', [
			'label'          => __( 'Columns', 'st-elementor' ),
			'type'           => Controls_Manager::SELECT,
			'default'        => '3',
			'tablet_default' => '2',
			'mobile_default' => '1',
			'options'        => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ],
			'selectors'      => [ '{{WRAPPER}} .st-pg-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);' ],
		] );

		$this->add_responsive_control( 'column_gap', [
			'label'     => __( 'Gap', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'   => [ 'size' => 30, 'unit' => 'px' ],
			'selectors' => [ '{{WRAPPER}} .st-pg-grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ═══════════════════════════════════════════════════════════════
		// STYLE TAB
		// ═══════════════════════════════════════════════════════════════

		// ── Section Wrapper ──────────────────────────────────────────────
		$this->start_controls_section(
			'style_section_wrapper',
			[ 'label' => __( 'Section Wrapper', 'st-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'section_bg',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-pg-section',
		] );

		$this->add_responsive_control( 'section_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'default'    => [ 'top' => '80', 'right' => '20', 'bottom' => '80', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-pg-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'section_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-pg-section' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Section Header Style ─────────────────────────────────────────
		$this->start_controls_section(
			'style_section_header',
			[ 'label' => __( 'Section Header', 'st-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control( 'section_title_color', [
			'label'     => __( 'Title Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-pg-section-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'section_title_typo',
			'selector' => '{{WRAPPER}} .st-pg-section-title',
		] );

		$this->add_control( 'section_subtitle_color', [
			'label'     => __( 'Subtitle Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}} .st-pg-section-subtitle' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'section_subtitle_typo',
			'selector' => '{{WRAPPER}} .st-pg-section-subtitle',
		] );

		$this->add_responsive_control( 'header_bottom_spacing', [
			'label'     => __( 'Spacing Below Header', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'default'   => [ 'size' => 50 ],
			'selectors' => [ '{{WRAPPER}} .st-pg-section-header' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Card Style ───────────────────────────────────────────────────
		$this->start_controls_section(
			'style_card',
			[ 'label' => __( 'Card', 'st-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'card_bg',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-pg-card',
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'card_border',
			'selector' => '{{WRAPPER}} .st-pg-card',
		] );

		$this->add_responsive_control( 'card_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '12', 'right' => '12', 'bottom' => '12', 'left' => '12', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-pg-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'card_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'default'    => [ 'top' => '40', 'right' => '30', 'bottom' => '40', 'left' => '30', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-pg-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'card_shadow',
			'selector' => '{{WRAPPER}} .st-pg-card',
		] );

		// Featured card
		$this->add_control( 'heading_featured_card', [
			'label'     => __( 'Featured Card', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'featured_card_bg',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-pg-card.st-pg-featured',
		] );

		$this->add_responsive_control( 'featured_scale', [
			'label'     => __( 'Scale Up', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 1, 'max' => 1.2, 'step' => 0.01 ] ],
			'default'   => [ 'size' => 1.05 ],
			'selectors' => [ '{{WRAPPER}} .st-pg-card.st-pg-featured' => 'transform: scale({{SIZE}});' ],
		] );

		$this->end_controls_section();

		// ── Title & Subtitle ─────────────────────────────────────────────
		$this->start_controls_section(
			'style_title',
			[ 'label' => __( 'Plan Title & Subtitle', 'st-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control( 'title_color', [
			'label'     => __( 'Title Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-pg-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'title_typo', 'selector' => '{{WRAPPER}} .st-pg-title',
		] );

		$this->add_control( 'subtitle_color', [
			'label'     => __( 'Subtitle Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}} .st-pg-subtitle' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'subtitle_typo', 'selector' => '{{WRAPPER}} .st-pg-subtitle',
		] );

		$this->end_controls_section();

		// ── Price ────────────────────────────────────────────────────────
		$this->start_controls_section(
			'style_price',
			[ 'label' => __( 'Price', 'st-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control( 'price_color', [
			'label'     => __( 'Amount Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-pg-amount' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'price_typo', 'selector' => '{{WRAPPER}} .st-pg-amount',
		] );

		$this->add_control( 'currency_color', [
			'label'     => __( 'Currency Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}} .st-pg-currency' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'period_color', [
			'label'     => __( 'Period Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-pg-period' => 'color: {{VALUE}};' ],
		] );

		// ── Discount / Original Price ─────────────────────────────────
		$this->add_control( 'discount_heading', [
			'label'     => __( 'Discount / Original Price', 'st-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'original_price_color', [
			'label'     => __( 'Original Price Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ef4444',
			'selectors' => [
				'{{WRAPPER}} .st-pg-original-amount'          => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-pg-original-price .st-pg-currency-orig' => 'color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'original_price_typo',
			'selector' => '{{WRAPPER}} .st-pg-original-amount',
		] );

		$this->add_control( 'original_price_bg', [
			'label'     => __( 'Badge Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#fee2e2',
			'selectors' => [ '{{WRAPPER}} .st-pg-original-price' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'original_price_padding', [
			'label'      => __( 'Badge Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top' => '2', 'right' => '10', 'bottom' => '2', 'left' => '10', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-pg-original-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'original_price_border_radius', [
			'label'      => __( 'Badge Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
			'default'    => [ 'size' => 20 ],
			'selectors'  => [ '{{WRAPPER}} .st-pg-original-price' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'original_price_spacing', [
			'label'     => __( 'Spacing Below', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
			'default'   => [ 'size' => 6 ],
			'selectors' => [ '{{WRAPPER}} .st-pg-original-price' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Features ─────────────────────────────────────────────────────
		$this->start_controls_section(
			'style_features',
			[ 'label' => __( 'Features List', 'st-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control( 'features_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-pg-features li' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'features_typo', 'selector' => '{{WRAPPER}} .st-pg-features li',
		] );

		$this->add_control( 'features_icon_color', [
			'label'     => __( 'Check Icon Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}} .st-pg-features li::before' => 'color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'features_spacing', [
			'label'     => __( 'Item Spacing', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
			'selectors' => [ '{{WRAPPER}} .st-pg-features li' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Button ───────────────────────────────────────────────────────
		$this->start_controls_section(
			'style_button',
			[ 'label' => __( 'Button', 'st-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->start_controls_tabs( 'tabs_btn' );

		$this->start_controls_tab( 'tab_btn_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );
		$this->add_control( 'btn_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-pg-btn' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name' => 'btn_bg', 'types' => [ 'classic', 'gradient' ], 'selector' => '{{WRAPPER}} .st-pg-btn',
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_btn_hover', [ 'label' => __( 'Hover', 'st-elementor' ) ] );
		$this->add_control( 'btn_hover_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-pg-btn:hover' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name' => 'btn_hover_bg', 'types' => [ 'classic', 'gradient' ], 'selector' => '{{WRAPPER}} .st-pg-btn:hover',
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control( 'btn_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'separator'  => 'before',
			'selectors'  => [ '{{WRAPPER}} .st-pg-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'btn_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-pg-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Ribbon ───────────────────────────────────────────────────────
		$this->start_controls_section(
			'style_ribbon',
			[ 'label' => __( 'Ribbon', 'st-elementor' ), 'tab' => Controls_Manager::TAB_STYLE ]
		);

		$this->add_control( 'ribbon_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-pg-ribbon' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'ribbon_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-pg-ribbon' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'ribbon_typo', 'selector' => '{{WRAPPER}} .st-pg-ribbon',
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['plans'] ) ) {
			return;
		}

		$icon_type = $settings['features_icon_type'] ?? 'check';
		$icon_map  = [
			'check' => '✓',
			'dot'   => '•',
			'arrow' => '→',
			'star'  => '★',
			'none'  => '',
		];
		$check_icon = $icon_map[ $icon_type ] ?? '✓';
		?>
		<div class="st-pg-section">

			<?php if ( ! empty( $settings['section_title'] ) || ! empty( $settings['section_subtitle'] ) ) : ?>
			<div class="st-pg-section-header">
				<?php if ( ! empty( $settings['section_title'] ) ) : ?>
					<h2 class="st-pg-section-title"><?php echo esc_html( $settings['section_title'] ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $settings['section_subtitle'] ) ) : ?>
					<p class="st-pg-section-subtitle"><?php echo esc_html( $settings['section_subtitle'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="st-pg-grid">
				<?php foreach ( $settings['plans'] as $plan ) :
					$is_featured    = ( 'yes' === $plan['plan_featured'] );
					$card_class     = 'st-pg-card' . ( $is_featured ? ' st-pg-featured' : '' );
					$features       = array_filter( array_map( 'trim', explode( "\n", $plan['plan_features'] ) ) );
					$original_price = trim( $plan['plan_original_price'] ?? '' );

					if ( ! empty( $plan['plan_btn_link']['url'] ) ) {
						$this->add_link_attributes( 'btn_' . $plan['_id'], $plan['plan_btn_link'] );
					}
					?>
					<div class="<?php echo esc_attr( $card_class ); ?>">

						<?php if ( $is_featured && ! empty( $plan['plan_ribbon'] ) ) : ?>
							<div class="st-pg-ribbon"><?php echo esc_html( $plan['plan_ribbon'] ); ?></div>
						<?php endif; ?>

						<div class="st-pg-header">
							<h3 class="st-pg-title"><?php echo esc_html( $plan['plan_title'] ); ?></h3>
							<?php if ( ! empty( $plan['plan_subtitle'] ) ) : ?>
								<p class="st-pg-subtitle"><?php echo esc_html( $plan['plan_subtitle'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="st-pg-price">
							<?php if ( ! empty( $original_price ) ) : ?>
								<div class="st-pg-original-price">
									<span class="st-pg-currency-orig"><?php echo esc_html( $plan['plan_currency'] ); ?></span>
									<span class="st-pg-original-amount"><?php echo esc_html( $original_price ); ?></span>
								</div>
							<?php endif; ?>
							<div class="st-pg-price-row">
								<span class="st-pg-currency"><?php echo esc_html( $plan['plan_currency'] ); ?></span>
								<span class="st-pg-amount"><?php echo esc_html( $plan['plan_price'] ); ?></span>
								<span class="st-pg-period"><?php echo esc_html( $plan['plan_period'] ); ?></span>
							</div>
						</div>

						<?php if ( ! empty( $features ) ) : ?>
							<ul class="st-pg-features">
								<?php foreach ( $features as $feature ) :
									$disabled = ( strpos( $feature, '-' ) === 0 );
									$text     = $disabled ? ltrim( substr( $feature, 1 ) ) : $feature;
									$li_class = $disabled ? 'st-pg-feat-disabled' : '';
									$icon     = $disabled ? '✕' : $check_icon;
								?>
									<li class="<?php echo esc_attr( $li_class ); ?>">
										<?php if ( 'none' !== $icon_type || $disabled ) : ?>
											<span class="st-pg-feat-icon"><?php echo esc_html( $icon ); ?></span>
										<?php endif; ?>
										<span class="st-pg-feat-text"><?php echo esc_html( $text ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( ! empty( $plan['plan_btn_text'] ) ) : ?>
							<div class="st-pg-footer">
								<a class="st-pg-btn" <?php echo $this->get_render_attribute_string( 'btn_' . $plan['_id'] ); ?>>
									<?php echo esc_html( $plan['plan_btn_text'] ); ?>
								</a>
							</div>
						<?php endif; ?>

					</div>
				<?php endforeach; ?>
			</div>

		</div>
		<?php
	}
}
