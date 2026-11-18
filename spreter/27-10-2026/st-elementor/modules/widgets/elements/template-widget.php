<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ST Template Widget
 * Renders any saved Elementor template (st_template or elementor_library) inline.
 */
class ST_Template_Widget extends Widget_Base {

	public function get_name()        { return 'st_template_widget'; }
	public function get_title()       { return __( 'ST Template', 'st-elementor' ); }
	public function get_icon()        { return 'eicon-library-save'; }
	public function get_categories()  { return [ 'st-store-transform' ]; }

	/* ── Controls ─────────────────────────────────────────────────── */

	protected function register_controls() {

		$this->start_controls_section( 'section_template', [
			'label' => __( 'Template', 'st-elementor' ),
		] );

		// ── Source ──────────────────────────────────────────────────
		$this->add_control( 'template_source', [
			'label'   => __( 'Template Source', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'st_template',
			'options' => [
				'st_template'       => __( 'ST Templates', 'st-elementor' ),
				'elementor_library' => __( 'Elementor Library', 'st-elementor' ),
			],
		] );

		// ── ST Templates list ────────────────────────────────────────
		$this->add_control( 'st_template_id', [
			'label'     => __( 'Select ST Template', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => $this->get_templates_list( 'st_template' ),
			'default'   => '',
			'condition' => [ 'template_source' => 'st_template' ],
		] );

		// ── Elementor Library list ───────────────────────────────────
		$this->add_control( 'elementor_template_id', [
			'label'     => __( 'Select Elementor Template', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => $this->get_templates_list( 'elementor_library' ),
			'default'   => '',
			'condition' => [ 'template_source' => 'elementor_library' ],
		] );

		// ── Edit shortcut ────────────────────────────────────────────
		$this->add_control( 'edit_template_notice', [
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => sprintf(
				'<a href="%s" target="_blank" class="elementor-button elementor-button-default" style="margin-top:8px;display:inline-block">%s</a>',
				esc_url( admin_url( 'post-new.php?post_type=st_template' ) ),
				__( '+ Create New ST Template', 'st-elementor' )
			),
			'content_classes' => 'elementor-panel-alert',
		] );

		$this->end_controls_section();

		// ── Style: Wrapper ───────────────────────────────────────────
		$this->start_controls_section( 'style_wrapper', [
			'label' => __( 'Wrapper', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'wrapper_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-template-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'wrapper_margin', [
			'label'      => __( 'Margin', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-template-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( 'wrapper_bg', [
			'label'     => __( 'Background Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-template-wrap' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'wrapper_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-template-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	/* ── Helpers ──────────────────────────────────────────────────── */

	private function get_templates_list( $post_type ) {
		$options = [ '' => __( '— Select Template —', 'st-elementor' ) ];

		$posts = get_posts( [
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'title',
			'order'          => 'ASC',
		] );

		foreach ( $posts as $post ) {
			$options[ $post->ID ] = $post->post_title;
		}

		return $options;
	}

	/* ── Render ───────────────────────────────────────────────────── */

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Determine which template ID to use
		if ( 'elementor_library' === $settings['template_source'] ) {
			$template_id = (int) $settings['elementor_template_id'];
		} else {
			$template_id = (int) $settings['st_template_id'];
		}

		if ( ! $template_id ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div style="padding:20px;background:#f0f2f5;border:2px dashed #d1d5db;border-radius:8px;text-align:center;color:#6b7280;">'
					. esc_html__( 'Please select a template from the panel.', 'st-elementor' )
					. '</div>';
			}
			return;
		}

		// Prevent infinite loop — don't render a template inside itself
		if ( get_the_ID() === $template_id ) {
			echo '<div style="padding:12px;background:#fef2f2;border:1px solid #fca5a5;border-radius:6px;color:#dc2626;">'
				. esc_html__( 'Cannot embed a template inside itself.', 'st-elementor' )
				. '</div>';
			return;
		}

		$content = '';

		if ( class_exists( '\Elementor\Plugin' ) ) {
			$content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
		}

		// Fallback: use post content if no Elementor data
		if ( empty( $content ) ) {
			$post = get_post( $template_id );
			if ( $post ) {
				$content = apply_filters( 'the_content', $post->post_content );
			}
		}

		if ( empty( $content ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div style="padding:20px;background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;color:#92400e;">'
					. esc_html__( 'Template is empty. Edit it with Elementor first.', 'st-elementor' )
					. '</div>';
			}
			return;
		}

		echo '<div class="st-template-wrap">' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
