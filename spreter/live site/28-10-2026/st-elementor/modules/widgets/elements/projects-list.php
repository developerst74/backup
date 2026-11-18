<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ST_Projects_List extends Widget_Base {
	public function get_name() { return 'st_projects_list'; }
	public function get_title() { return __( 'ST Projects List', 'st-elementor' ); }
	public function get_icon() { return 'eicon-post-list'; }
	public function get_categories() { return [ 'st-store-transform' ]; }

	protected function register_controls() {
		$this->start_controls_section( 'section_query', [
			'label' => __( 'Query', 'st-elementor' ),
		] );

		$this->add_control( 'post_type', [
			'label'       => __( 'Post Type', 'st-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'project',
			'placeholder' => 'project',
		] );

		$this->add_control( 'posts_per_page', [
			'label'   => __( 'Posts Per Page', 'st-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'min'     => -1,
			'step'    => 1,
			'default' => -1,
		] );

		$this->add_control( 'orderby', [
			'label'   => __( 'Order By', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'meta_year',
			'options' => [
				'meta_year'   => __( 'Year (ACF/meta)', 'st-elementor' ),
				'date'        => __( 'Publish Date', 'st-elementor' ),
				'title'       => __( 'Title', 'st-elementor' ),
				'menu_order'  => __( 'Menu Order', 'st-elementor' ),
				'rand'        => __( 'Random', 'st-elementor' ),
			],
		] );

		$this->add_control( 'order', [
			'label'   => __( 'Order', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'DESC',
			'options' => [
				'DESC' => 'DESC',
				'ASC'  => 'ASC',
			],
		] );

		$this->add_control( 'include_ids', [
			'label'       => __( 'Include IDs', 'st-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => '12,34,56',
			'description' => __( 'Optional. Comma-separated post IDs. If set, only these posts are shown (keeps this order).', 'st-elementor' ),
		] );

		$this->add_control( 'exclude_ids', [
			'label'       => __( 'Exclude IDs', 'st-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => '78,90',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_fields', [
			'label' => __( 'Fields', 'st-elementor' ),
		] );

		$this->add_control( 'year_key', [
			'label'   => __( 'Year Field Key', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'year',
		] );

		$this->add_control( 'venue_key', [
			'label'   => __( 'Venue Field Key', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'venue',
		] );

		$this->add_control( 'role_key', [
			'label'   => __( 'Role Field Key', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'role',
		] );

		$this->add_control( 'group_by_year', [
			'label'        => __( 'Group by Year', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'link_title', [
			'label'        => __( 'Link Title', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'hide_empty', [
			'label'        => __( 'Hide Empty Fields', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_box', [
			'label' => __( 'Box', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'box_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-projects-card' => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'box_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [
				'{{WRAPPER}} .st-projects-row' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'box_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'{{WRAPPER}} .st-projects-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_row', [
			'label' => __( 'Row', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'row_padding', [
			'label'      => __( 'Row Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [
				'{{WRAPPER}} .st-projects-row' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_control( 'row_border_color', [
			'label'     => __( 'Divider Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-projects-row + .st-projects-row' => 'border-top-color: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'row_border_width', [
			'label'     => __( 'Divider Width', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 10 ] ],
			'default'   => [ 'size' => 1 ],
			'selectors' => [
				'{{WRAPPER}} .st-projects-row + .st-projects-row' => 'border-top-style: solid; border-top-width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'row_hover_bg', [
			'label'     => __( 'Hover Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-projects-row:hover' => 'background-color: {{VALUE}};',
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_year', [
			'label' => __( 'Year', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'year_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-projects-year' => 'color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'year_typography',
			'selector' => '{{WRAPPER}} .st-projects-year',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_title', [
			'label' => __( 'Title', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'title_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-projects-title, {{WRAPPER}} .st-projects-title a' => 'color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'selector' => '{{WRAPPER}} .st-projects-title, {{WRAPPER}} .st-projects-title a',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_venue', [
			'label' => __( 'Venue', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'venue_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-projects-venue' => 'color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'venue_typography',
			'selector' => '{{WRAPPER}} .st-projects-venue',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_role', [
			'label' => __( 'Role', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'role_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-projects-role' => 'color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'role_typography',
			'selector' => '{{WRAPPER}} .st-projects-role',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_icon', [
			'label'     => __( 'Icon', 'st-elementor' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'show_icon' => 'yes' ],
		] );

		$this->add_control( 'icon_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .st-projects-icon' => 'color: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'icon_size', [
			'label'     => __( 'Size', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 8, 'max' => 60 ] ],
			'default'   => [ 'size' => 18 ],
			'selectors' => [
				'{{WRAPPER}} .st-projects-icon' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	private function parse_ids( $value ) {
		$value = is_string( $value ) ? $value : '';
		$value = trim( $value );
		if ( '' === $value ) {
			return [];
		}
		$parts = preg_split( '/\s*,\s*/', $value );
		$ids   = [];
		foreach ( $parts as $p ) {
			$p = (int) $p;
			if ( $p > 0 ) {
				$ids[] = $p;
			}
		}
		return array_values( array_unique( $ids ) );
	}

	private function get_field_value( $post_id, $key ) {
		$key = is_string( $key ) ? trim( $key ) : '';
		if ( '' === $key ) {
			return '';
		}

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

	private function build_query_args( array $settings ) {
		$post_type = sanitize_key( $settings['post_type'] ?? 'project' );
		if ( '' === $post_type ) {
			$post_type = 'project';
		}

		$group_by_year = ( $settings['group_by_year'] ?? 'yes' ) === 'yes';
		$posts_per_page = (int) ( $settings['posts_per_page'] ?? -1 );
		if ( $group_by_year ) {
			// Year-grouped accordion must load the full dataset, otherwise
			// some years disappear before grouping when the widget query is limited.
			$posts_per_page = -1;
		}

		$include_ids = $this->parse_ids( $settings['include_ids'] ?? '' );
		$exclude_ids = $this->parse_ids( $settings['exclude_ids'] ?? '' );

		$args = [
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => $posts_per_page,
			'no_found_rows'  => true,
		];

		if ( ! empty( $include_ids ) ) {
			$args['post__in'] = $include_ids;
			$args['orderby']  = 'post__in';
			return $args;
		}

		if ( ! empty( $exclude_ids ) ) {
			$args['post__not_in'] = $exclude_ids;
		}

		$order   = ( $settings['order'] ?? 'DESC' ) === 'ASC' ? 'ASC' : 'DESC';
		$orderby = $settings['orderby'] ?? 'meta_year';

		$args['order'] = $order;

		if ( 'meta_year' === $orderby ) {
			$meta_key = $settings['year_key'] ?? 'year';
			$args['meta_key'] = $meta_key;
			$args['orderby']  = 'meta_value_num';
		} else {
			$args['orderby'] = $orderby;
		}

		return $args;
	}

	private function normalize_year_key( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		$value = trim( $value );
		if ( '' === $value ) {
			return '';
		}
		if ( preg_match( '/\d{4}/', $value, $m ) ) {
			return $m[0];
		}
		return $value;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$q = new \WP_Query( $this->build_query_args( $settings ) );
		if ( ! $q->have_posts() ) {
			if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div style="padding:14px;border:2px dashed #d1d5db;border-radius:8px;color:#6b7280;text-align:center">' . esc_html__( 'No projects found for this query.', 'st-elementor' ) . '</div>';
			}
			return;
		}

		$year_key  = $settings['year_key'] ?? 'year';
		$venue_key = $settings['venue_key'] ?? 'venue';
		$role_key  = $settings['role_key'] ?? 'role';

		$link_title      = ( $settings['link_title'] ?? 'yes' ) === 'yes';
		$hide_empty      = ( $settings['hide_empty'] ?? 'yes' ) === 'yes';
		$group_by_year   = ( $settings['group_by_year'] ?? 'yes' ) === 'yes';

		$items = [];

		while ( $q->have_posts() ) {
			$q->the_post();
			$post_id = get_the_ID();

			$year_raw  = $this->get_field_value( $post_id, $year_key );
			$venue_raw = $this->get_field_value( $post_id, $venue_key );
			$role_raw  = $this->get_field_value( $post_id, $role_key );

			$year  = $this->normalize_year_key( $year_raw );
			$venue = is_scalar( $venue_raw ) ? trim( (string) $venue_raw ) : '';
			$role  = is_scalar( $role_raw ) ? trim( (string) $role_raw ) : '';

			$title = get_the_title();
			$url   = get_permalink();

			if ( $hide_empty ) {
				if ( '' === trim( (string) $year ) && '' === trim( (string) $venue ) && '' === trim( (string) $role ) && '' === trim( (string) $title ) ) {
					continue;
				}
			}

			$item = [
				'post_id'        => $post_id,
				'year'           => $year,
				'year_raw'       => is_scalar( $year_raw ) ? (string) $year_raw : '',
				'title'          => $title,
				'url'            => $url,
				'venue'          => $venue,
				'role'           => $role,
			];

			$group_key = $group_by_year ? ( '' !== $year ? $year : '0' ) : '_all';
			if ( ! isset( $items[ $group_key ] ) ) {
				$items[ $group_key ] = [];
			}
			$items[ $group_key ][] = $item;
		}

		wp_reset_postdata();

		if ( empty( $items ) ) {
			return;
		}

		if ( $group_by_year ) {
			$keys = array_keys( $items );
			usort( $keys, function( $a, $b ) {
				return (int) $b <=> (int) $a;
			} );
			$sorted = [];
			foreach ( $keys as $k ) {
				$sorted[ $k ] = $items[ $k ];
			}
			$items = $sorted;
		}

		echo '<div class="st-projects-wrap st-projects-accordion" data-mode="year_accordion">';
		echo '<div class="st-projects-list" role="list">';
		$card_index = 0;

		foreach ( $items as $group_key => $group_items ) {
			$card_index++;
			$year_attr = $group_by_year ? $group_key : '';
			echo '<div class="st-projects-card" role="listitem"' . ( '' !== $year_attr ? ' data-year="' . esc_attr( $year_attr ) . '"' : '' ) . '>';

			$header = $group_items[0] ?? null;
			if ( $header ) {
				$year_display = $header['year'] !== '' ? $header['year'] : $header['year_raw'];
				$has_more = count( $group_items ) > 1;
				$btn_attrs = $has_more ? '' : ' disabled';
				$panel_id = 'st-projects-panel-' . $this->get_id() . '-' . $card_index;

				echo '<div class="st-projects-row st-projects-row--year">';
				echo '<div class="st-projects-year" data-label="' . esc_attr__( 'Year', 'st-elementor' ) . '">' . esc_html( (string) $year_display ) . '</div>';

				echo '<div class="st-projects-title" data-label="' . esc_attr__( 'Project Title', 'st-elementor' ) . '">';
				if ( $link_title ) {
					echo '<a href="' . esc_url( $header['url'] ) . '">' . esc_html( $header['title'] ) . '</a>';
				} else {
					echo esc_html( $header['title'] );
				}
				echo '</div>';

				echo '<div class="st-projects-venue" data-label="' . esc_attr__( 'Organization / Venue', 'st-elementor' ) . '">' . esc_html( (string) $header['venue'] ) . '</div>';
				echo '<div class="st-projects-role" data-label="' . esc_attr__( 'Role', 'st-elementor' ) . '"><span class="st-projects-role-text">' . esc_html( (string) $header['role'] ) . '</span></div>';

				echo '<div class="st-projects-arrow">';
				echo '<button type="button" class="st-projects-year-toggle"' . $btn_attrs . ' aria-expanded="false" aria-controls="' . esc_attr( $panel_id ) . '" aria-label="' . esc_attr__( 'Expand year', 'st-elementor' ) . '">';
				echo '<span class="st-projects-icon" aria-hidden="true"></span>';
				echo '</button>';
				echo '</div>';
				echo '</div>';

				if ( $has_more ) {
					echo '<div class="st-projects-panel" id="' . esc_attr( $panel_id ) . '" hidden>';
					foreach ( array_slice( $group_items, 1 ) as $item ) {
						$year_display_inner = $item['year'] !== '' ? $item['year'] : $item['year_raw'];

						echo '<div class="st-projects-row st-projects-row--item">';
						echo '<div class="st-projects-year" data-label="' . esc_attr__( 'Year', 'st-elementor' ) . '">' . esc_html( (string) $year_display_inner ) . '</div>';

						echo '<div class="st-projects-title" data-label="' . esc_attr__( 'Project Title', 'st-elementor' ) . '">';
						if ( $link_title ) {
							echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['title'] ) . '</a>';
						} else {
							echo esc_html( $item['title'] );
						}
						echo '</div>';

						echo '<div class="st-projects-venue" data-label="' . esc_attr__( 'Organization / Venue', 'st-elementor' ) . '">' . esc_html( (string) $item['venue'] ) . '</div>';
						echo '<div class="st-projects-role" data-label="' . esc_attr__( 'Role', 'st-elementor' ) . '"><span class="st-projects-role-text">' . esc_html( (string) $item['role'] ) . '</span></div>';
						echo '<div class="st-projects-arrow"><span class="st-projects-arrow-spacer" aria-hidden="true"></span></div>';
						echo '</div>';
					}
					echo '</div>';
				}
			}

			echo '</div>';
		}

		echo '</div>';
		echo '</div>';
	}
}

