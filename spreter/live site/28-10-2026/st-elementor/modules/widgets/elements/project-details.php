<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ST_Project_Details extends Widget_Base {
	public function get_name() { return 'st_project_details'; }
	public function get_title() { return __( 'ST Project Details', 'st-elementor' ); }
	public function get_icon() { return 'eicon-post-info'; }
	public function get_categories() { return [ 'st-store-transform' ]; }

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [
			'label' => __( 'Content', 'st-elementor' ),
		] );

		$this->add_control( 'source', [
			'label'   => __( 'Source', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'current',
			'options' => [
				'current' => __( 'Current Post', 'st-elementor' ),
				'manual'  => __( 'Manual Post ID', 'st-elementor' ),
			],
		] );

		$this->add_control( 'post_id', [
			'label'     => __( 'Post ID', 'st-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'min'       => 1,
			'step'      => 1,
			'default'   => 0,
			'condition' => [ 'source' => 'manual' ],
		] );

		$this->add_control( 'show_title', [
			'label'        => __( 'Show Title', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'title_tag', [
			'label'     => __( 'Title HTML Tag', 'st-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'h3',
			'options'   => [
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'div' => 'DIV',
				'span' => 'SPAN',
			],
			'condition' => [ 'show_title' => 'yes' ],
		] );

		$this->add_control( 'show_year', [
			'label'        => __( 'Show Year', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'label_year', [
			'label'     => __( 'Year Label', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'Year', 'st-elementor' ),
			'condition' => [ 'show_year' => 'yes' ],
		] );

		$this->add_control( 'show_venue', [
			'label'        => __( 'Show Venue', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'label_venue', [
			'label'     => __( 'Venue Label', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'Venue', 'st-elementor' ),
			'condition' => [ 'show_venue' => 'yes' ],
		] );

		$this->add_control( 'show_role', [
			'label'        => __( 'Show Role', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'label_role', [
			'label'     => __( 'Role Label', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'Role', 'st-elementor' ),
			'condition' => [ 'show_role' => 'yes' ],
		] );

		$this->add_control( 'hide_empty', [
			'label'        => __( 'Hide Empty Fields', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_wrapper', [
			'label' => __( 'Wrapper', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'wrapper_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-project-details' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'wrapper_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-project-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'wrapper_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [ '{{WRAPPER}} .st-project-details' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_title', [
			'label'     => __( 'Title', 'st-elementor' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'show_title' => 'yes' ],
		] );

		$this->add_control( 'title_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-project-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'selector' => '{{WRAPPER}} .st-project-title',
		] );

		$this->add_responsive_control( 'title_spacing', [
			'label'     => __( 'Spacing', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'   => [ 'size' => 16 ],
			'selectors' => [ '{{WRAPPER}} .st-project-title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_rows', [
			'label' => __( 'Rows', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'label_color', [
			'label'     => __( 'Label Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-project-row-label' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'label_typography',
			'selector' => '{{WRAPPER}} .st-project-row-label',
		] );

		$this->add_control( 'value_color', [
			'label'     => __( 'Value Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-project-row-value' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'value_typography',
			'selector' => '{{WRAPPER}} .st-project-row-value',
		] );

		$this->add_responsive_control( 'row_gap', [
			'label'     => __( 'Row Gap', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'   => [ 'size' => 10 ],
			'selectors' => [ '{{WRAPPER}} .st-project-rows' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'row_label_width', [
			'label'     => __( 'Label Width', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px', '%' ],
			'range'     => [
				'px' => [ 'min' => 60, 'max' => 400 ],
				'%'  => [ 'min' => 10, 'max' => 80 ],
			],
			'default'   => [ 'size' => 120, 'unit' => 'px' ],
			'selectors' => [
				'{{WRAPPER}} .st-project-row-label' => 'min-width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	private function resolve_post_id( array $settings ) {
		if ( 'manual' === ( $settings['source'] ?? 'current' ) ) {
			$id = (int) ( $settings['post_id'] ?? 0 );
			return $id > 0 ? $id : 0;
		}
		$id = get_the_ID();
		return $id ? (int) $id : 0;
	}

	private function get_field_value( $post_id, $key ) {
		if ( function_exists( 'get_field' ) ) {
			$v = get_field( $key, $post_id );
			if ( is_string( $v ) ) {
				$v = trim( $v );
			}
			return $v;
		}
		$v = get_post_meta( $post_id, $key, true );
		if ( is_string( $v ) ) {
			$v = trim( $v );
		}
		return $v;
	}

	private function build_rows( $post_id, array $settings ) {
		$rows = [];

		if ( 'yes' === ( $settings['show_year'] ?? 'yes' ) ) {
			$rows[] = [
				'label' => $settings['label_year'] ?? __( 'Year', 'st-elementor' ),
				'value' => $this->get_field_value( $post_id, 'year' ),
			];
		}

		if ( 'yes' === ( $settings['show_venue'] ?? 'yes' ) ) {
			$rows[] = [
				'label' => $settings['label_venue'] ?? __( 'Venue', 'st-elementor' ),
				'value' => $this->get_field_value( $post_id, 'venue' ),
			];
		}

		if ( 'yes' === ( $settings['show_role'] ?? 'yes' ) ) {
			$rows[] = [
				'label' => $settings['label_role'] ?? __( 'Role', 'st-elementor' ),
				'value' => $this->get_field_value( $post_id, 'role' ),
			];
		}

		if ( 'yes' === ( $settings['hide_empty'] ?? 'yes' ) ) {
			$rows = array_values( array_filter( $rows, function( $row ) {
				$v = $row['value'] ?? '';
				if ( is_array( $v ) ) {
					return ! empty( $v );
				}
				return '' !== (string) $v;
			} ) );
		}

		return $rows;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id  = $this->resolve_post_id( $settings );
		if ( ! $post_id ) {
			if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div style="padding:14px;border:2px dashed #d1d5db;border-radius:8px;color:#6b7280;text-align:center">' . esc_html__( 'No post found. Set Source to Manual Post ID or use inside a post context.', 'st-elementor' ) . '</div>';
			}
			return;
		}

		$title = get_the_title( $post_id );
		$rows  = $this->build_rows( $post_id, $settings );

		if ( empty( $rows ) && 'yes' === ( $settings['hide_empty'] ?? 'yes' ) && 'yes' !== ( $settings['show_title'] ?? 'yes' ) ) {
			return;
		}

		$title_tag = $settings['title_tag'] ?? 'h3';
		$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'div', 'span' ];
		if ( ! in_array( $title_tag, $allowed_tags, true ) ) {
			$title_tag = 'h3';
		}

		echo '<div class="st-project-details">';

		if ( 'yes' === ( $settings['show_title'] ?? 'yes' ) ) {
			echo '<' . esc_attr( $title_tag ) . ' class="st-project-title">' . esc_html( $title ) . '</' . esc_attr( $title_tag ) . '>';
		}

		if ( ! empty( $rows ) ) {
			echo '<div class="st-project-rows" style="display:flex;flex-direction:column">';
			foreach ( $rows as $row ) {
				$label = (string) ( $row['label'] ?? '' );
				$value = $row['value'] ?? '';
				if ( is_array( $value ) ) {
					$value = wp_json_encode( $value );
				}
				echo '<div class="st-project-row" style="display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap">';
				echo '<div class="st-project-row-label" style="font-weight:700">' . esc_html( $label ) . '</div>';
				echo '<div class="st-project-row-value" style="flex:1;min-width:160px">' . esc_html( (string) $value ) . '</div>';
				echo '</div>';
			}
			echo '</div>';
		}

		echo '</div>';
	}
}

