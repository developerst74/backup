<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;

if ( ! defined( 'ABSPATH' ) ) exit;

class ST_Site_Logo extends Widget_Base {

	public function get_name()       { return 'st_site_logo'; }
	public function get_title()      { return __( 'ST Site Logo', 'st-elementor' ); }
	public function get_icon()       { return 'eicon-site-logo'; }
	public function get_categories() { return [ 'st-store-transform' ]; }

	protected function register_controls() {

		// ── CONTENT ──────────────────────────────────────────────────
		$this->start_controls_section( 'section_logo', [
			'label' => __( 'Logo', 'st-elementor' ),
		] );

		$this->add_control( 'logo_type', [
			'label'   => __( 'Logo Type', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'site_logo',
			'options' => [
				'site_logo' => __( 'Site Logo (from Customizer)', 'st-elementor' ),
				'custom'    => __( 'Custom Image / URL', 'st-elementor' ),
				'text'      => __( 'Text Logo', 'st-elementor' ),
			],
		] );

		// Custom image
		$this->add_control( 'custom_logo', [
			'label'     => __( 'Logo Image', 'st-elementor' ),
			'type'      => Controls_Manager::MEDIA,
			'condition' => [ 'logo_type' => 'custom' ],
		] );

		$this->add_control( 'custom_logo_url', [
			'label'       => __( 'Or Image URL', 'st-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => 'https://example.com/logo.png',
			'condition'   => [ 'logo_type' => 'custom' ],
		] );

		// Text logo
		$this->add_control( 'logo_text', [
			'label'     => __( 'Logo Text', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => get_bloginfo( 'name' ),
			'condition' => [ 'logo_type' => 'text' ],
		] );

		$this->add_control( 'logo_tagline', [
			'label'     => __( 'Tagline', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => get_bloginfo( 'description' ),
			'condition' => [ 'logo_type' => 'text' ],
		] );

		// Link
		$this->add_control( 'logo_link', [
			'label'     => __( 'Link', 'st-elementor' ),
			'type'      => Controls_Manager::URL,
			'separator' => 'before',
			'default'   => [ 'url' => home_url() ],
			'placeholder' => home_url(),
		] );

		$this->add_control( 'logo_alt', [
			'label'   => __( 'Alt Text', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => get_bloginfo( 'name' ),
		] );

		$this->end_controls_section();

		// ── STYLE: IMAGE ─────────────────────────────────────────────
		$this->start_controls_section( 'style_image', [
			'label'     => __( 'Image', 'st-elementor' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'logo_type!' => 'text' ],
		] );

		$this->add_responsive_control( 'logo_width', [
			'label'      => __( 'Width', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'vw' ],
			'range'      => [ 'px' => [ 'min' => 20, 'max' => 600 ] ],
			'default'    => [ 'size' => 160, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .st-logo-img' => 'width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'logo_max_height', [
			'label'      => __( 'Max Height', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 10, 'max' => 300 ] ],
			'selectors'  => [ '{{WRAPPER}} .st-logo-img' => 'max-height: {{SIZE}}{{UNIT}}; height: auto;' ],
		] );

		$this->add_responsive_control( 'logo_align', [
			'label'     => __( 'Alignment', 'st-elementor' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'flex-start' => [ 'title' => __( 'Left', 'st-elementor' ),   'icon' => 'eicon-h-align-left' ],
				'center'     => [ 'title' => __( 'Center', 'st-elementor' ), 'icon' => 'eicon-h-align-center' ],
				'flex-end'   => [ 'title' => __( 'Right', 'st-elementor' ),  'icon' => 'eicon-h-align-right' ],
			],
			'default'   => 'flex-start',
			'selectors' => [ '{{WRAPPER}} .st-logo-wrap' => 'justify-content: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'logo_border',
			'selector' => '{{WRAPPER}} .st-logo-img',
		] );

		$this->add_responsive_control( 'logo_border_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-logo-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'logo_shadow',
			'selector' => '{{WRAPPER}} .st-logo-img',
		] );

		// Normal / Hover tabs
		$this->start_controls_tabs( 'logo_img_tabs' );

		$this->start_controls_tab( 'logo_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );
		$this->add_control( 'logo_opacity', [
			'label'     => __( 'Opacity', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
			'default'   => [ 'size' => 1 ],
			'selectors' => [ '{{WRAPPER}} .st-logo-img' => 'opacity: {{SIZE}};' ],
		] );
		$this->add_group_control( Group_Control_Css_Filter::get_type(), [
			'name'     => 'logo_css_filter',
			'selector' => '{{WRAPPER}} .st-logo-img',
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'logo_hover', [ 'label' => __( 'Hover', 'st-elementor' ) ] );
		$this->add_control( 'logo_hover_opacity', [
			'label'     => __( 'Opacity', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
			'selectors' => [ '{{WRAPPER}} .st-logo-link:hover .st-logo-img' => 'opacity: {{SIZE}};' ],
		] );
		$this->add_group_control( Group_Control_Css_Filter::get_type(), [
			'name'     => 'logo_hover_css_filter',
			'selector' => '{{WRAPPER}} .st-logo-link:hover .st-logo-img',
		] );
		$this->add_control( 'logo_hover_transition', [
			'label'     => __( 'Transition (s)', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 2, 'step' => 0.1 ] ],
			'default'   => [ 'size' => 0.3 ],
			'selectors' => [ '{{WRAPPER}} .st-logo-img' => 'transition: all {{SIZE}}s ease;' ],
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();

		// ── STYLE: TEXT LOGO ─────────────────────────────────────────
		$this->start_controls_section( 'style_text', [
			'label'     => __( 'Text Logo', 'st-elementor' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'logo_type' => 'text' ],
		] );

		$this->add_control( 'text_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-logo-text' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'text_typo', 'selector' => '{{WRAPPER}} .st-logo-text',
		] );

		$this->add_control( 'tagline_color', [
			'label'     => __( 'Tagline Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}} .st-logo-tagline' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'tagline_typo', 'selector' => '{{WRAPPER}} .st-logo-tagline',
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$link_url = ! empty( $s['logo_link']['url'] ) ? $s['logo_link']['url'] : home_url();
		$target   = ! empty( $s['logo_link']['is_external'] ) ? ' target="_blank"' : '';
		$nofollow = ! empty( $s['logo_link']['nofollow'] ) ? ' rel="nofollow"' : '';

		echo '<div class="st-logo-wrap">';
		echo '<a class="st-logo-link" href="' . esc_url( $link_url ) . '"' . $target . $nofollow . '>';

		if ( 'text' === $s['logo_type'] ) {
			echo '<span class="st-logo-text">' . esc_html( $s['logo_text'] ) . '</span>';
			if ( ! empty( $s['logo_tagline'] ) ) {
				echo '<span class="st-logo-tagline">' . esc_html( $s['logo_tagline'] ) . '</span>';
			}
		} else {
			// Determine image src
			$src = '';
			if ( 'custom' === $s['logo_type'] ) {
				$src = ! empty( $s['custom_logo_url'] ) ? $s['custom_logo_url'] : ( $s['custom_logo']['url'] ?? '' );
			} else {
				// site_logo from customizer
				$logo_id = get_theme_mod( 'custom_logo' );
				if ( $logo_id ) {
					$img = wp_get_attachment_image_src( $logo_id, 'full' );
					$src = $img ? $img[0] : '';
				}
			}

			if ( $src ) {
				echo '<img class="st-logo-img" src="' . esc_url( $src ) . '" alt="' . esc_attr( $s['logo_alt'] ) . '">';
			} else {
				echo '<span class="st-logo-text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
			}
		}

		echo '</a>';
		echo '</div>';
	}
}
