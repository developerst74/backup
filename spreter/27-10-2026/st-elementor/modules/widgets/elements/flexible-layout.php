<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit;

class ST_Flexible_Layout extends Widget_Base {

	public function get_name() {
		return 'st_flexible_layout';
	}

	public function get_title() {
		return __( 'ST Flexible Layout', 'st-elementor' );
	}

	public function get_icon() {
		return 'eicon-apps';
	}

	public function get_categories() {
		return [ 'st-store-transform' ];
	}

	public function get_script_depends() {
		return [ 'st-elementor-swiper', 'st-elementor-public' ];
	}

	public function get_style_depends() {
		return [ 'st-elementor-swiper' ];
	}

	protected function register_controls() {
		$this->register_layout_controls();
		$this->register_flex_controls();
		$this->register_grid_controls();
		$this->register_slider_controls();
		$this->register_style_controls();
		$this->register_advanced_controls();
	}

	private function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'layout_type',
			[
				'label'   => __( 'Layout Type', 'st-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'flex',
				'options' => [
					'flex'   => __( 'Flex', 'st-elementor' ),
					'grid'   => __( 'Grid', 'st-elementor' ),
					'slider' => __( 'Slider', 'st-elementor' ),
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_flex_controls() {
		$this->start_controls_section(
			'section_flex_options',
			[
				'label'     => __( 'Flex Options', 'st-elementor' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [ 'layout_type' => 'flex' ],
			]
		);

		$this->add_control(
			'flex_direction',
			[
				'label'   => __( 'Direction', 'st-elementor' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'row'            => [ 'title' => 'Row', 'icon' => 'eicon-arrow-right' ],
					'row-reverse'    => [ 'title' => 'Row Reverse', 'icon' => 'eicon-arrow-left' ],
					'column'         => [ 'title' => 'Column', 'icon' => 'eicon-arrow-down' ],
					'column-reverse' => [ 'title' => 'Column Reverse', 'icon' => 'eicon-arrow-up' ],
				],
				'default' => 'row',
			]
		);

		$this->add_responsive_control(
			'flex_wrap',
			[
				'label'   => __( 'Wrap', 'st-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'wrap',
				'options' => [
					'nowrap' => __( 'No Wrap', 'st-elementor' ),
					'wrap'   => __( 'Wrap', 'st-elementor' ),
					'wrap-reverse' => __( 'Wrap Reverse', 'st-elementor' ),
				],
			]
		);

		$this->add_responsive_control(
			'justify_content',
			[
				'label'   => __( 'Justify Content', 'st-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'flex-start',
				'options' => [
					'flex-start'    => __( 'Start', 'st-elementor' ),
					'center'        => __( 'Center', 'st-elementor' ),
					'flex-end'      => __( 'End', 'st-elementor' ),
					'space-between' => __( 'Space Between', 'st-elementor' ),
					'space-around'  => __( 'Space Around', 'st-elementor' ),
					'space-evenly'  => __( 'Space Evenly', 'st-elementor' ),
				],
			]
		);

		$this->add_responsive_control(
			'align_items',
			[
				'label'   => __( 'Align Items', 'st-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'stretch',
				'options' => [
					'flex-start' => __( 'Start', 'st-elementor' ),
					'center'     => __( 'Center', 'st-elementor' ),
					'flex-end'   => __( 'End', 'st-elementor' ),
					'stretch'    => __( 'Stretch', 'st-elementor' ),
					'baseline'   => __( 'Baseline', 'st-elementor' ),
				],
			]
		);

		$this->add_responsive_control(
			'align_content',
			[
				'label'   => __( 'Align Content', 'st-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'stretch',
				'options' => [
					'flex-start'    => __( 'Start', 'st-elementor' ),
					'center'        => __( 'Center', 'st-elementor' ),
					'flex-end'      => __( 'End', 'st-elementor' ),
					'space-between' => __( 'Space Between', 'st-elementor' ),
					'space-around'  => __( 'Space Around', 'st-elementor' ),
					'stretch'       => __( 'Stretch', 'st-elementor' ),
				],
			]
		);

		$this->add_responsive_control(
			'flex_gap',
			[
				'label'      => __( 'Gap', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 100 ],
					'em' => [ 'min' => 0, 'max' => 10 ],
					'%'  => [ 'min' => 0, 'max' => 20 ],
				],
				'default'    => [ 'size' => 20, 'unit' => 'px' ],
			]
		);

		$this->end_controls_section();
	}

	private function register_grid_controls() {
		$this->start_controls_section(
			'section_grid_options',
			[
				'label'     => __( 'Grid Options', 'st-elementor' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [ 'layout_type' => 'grid' ],
			]
		);

		$this->add_responsive_control(
			'grid_columns',
			[
				'label'       => __( 'Columns', 'st-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3,
				'min'         => 1,
				'max'         => 12,
				'input_attrs' => [ 'min' => 1, 'max' => 12 ],
			]
		);

		$this->add_responsive_control(
			'grid_row_gap',
			[
				'label'      => __( 'Row Gap', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 100 ],
					'em' => [ 'min' => 0, 'max' => 10 ],
				],
				'default'    => [ 'size' => 20, 'unit' => 'px' ],
			]
		);

		$this->add_responsive_control(
			'grid_column_gap',
			[
				'label'      => __( 'Column Gap', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 100 ],
					'em' => [ 'min' => 0, 'max' => 10 ],
				],
				'default'    => [ 'size' => 20, 'unit' => 'px' ],
			]
		);

		$this->add_control(
			'grid_responsive',
			[
				'label'        => __( 'Responsive Settings', 'st-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_responsive_control(
			'grid_columns_tablet',
			[
				'label'       => __( 'Tablet Columns', 'st-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 2,
				'min'         => 1,
				'max'         => 6,
				'condition'   => [ 'grid_responsive' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'grid_columns_mobile',
			[
				'label'       => __( 'Mobile Columns', 'st-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1,
				'min'         => 1,
				'max'         => 4,
				'condition'   => [ 'grid_responsive' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	private function register_slider_controls() {
		$this->start_controls_section(
			'section_slider_options',
			[
				'label'     => __( 'Slider Options', 'st-elementor' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [ 'layout_type' => 'slider' ],
			]
		);

		$this->add_responsive_control(
			'slides_per_view',
			[
				'label'          => __( 'Slides Per View', 'st-elementor' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 3,
				'min'            => 1,
				'max'            => 10,
			]
		);

		$this->add_responsive_control(
			'slides_per_view_tablet',
			[
				'label'   => __( 'Slides Per View (Tablet)', 'st-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2,
				'min'     => 1,
				'max'     => 6,
			]
		);

		$this->add_responsive_control(
			'slides_per_view_mobile',
			[
				'label'   => __( 'Slides Per View (Mobile)', 'st-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 1,
				'max'     => 4,
			]
		);

		$this->add_control(
			'slider_autoplay',
			[
				'label'        => __( 'Autoplay', 'st-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'slider_autoplay_speed',
			[
				'label'     => __( 'Autoplay Speed (ms)', 'st-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3000,
				'condition' => [ 'slider_autoplay' => 'yes' ],
			]
		);

		$this->add_control(
			'slider_speed',
			[
				'label'   => __( 'Transition Speed (ms)', 'st-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
			]
		);

		$this->add_control(
			'slider_loop',
			[
				'label'        => __( 'Loop', 'st-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'slider_direction',
			[
				'label'   => __( 'Direction', 'st-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => __( 'Horizontal', 'st-elementor' ),
					'vertical'   => __( 'Vertical', 'st-elementor' ),
				],
			]
		);

		$this->add_control(
			'slider_effect',
			[
				'label'   => __( 'Effect', 'st-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => __( 'Slide', 'st-elementor' ),
					'fade'  => __( 'Fade', 'st-elementor' ),
					'cube'  => __( 'Cube', 'st-elementor' ),
					'coverflow' => __( 'Coverflow', 'st-elementor' ),
					'flip'  => __( 'Flip', 'st-elementor' ),
				],
			]
		);

		$this->add_responsive_control(
			'slider_space_between',
			[
				'label'      => __( 'Space Between Slides', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 100 ],
				],
				'default'    => [ 'size' => 20, 'unit' => 'px' ],
			]
		);

		$this->add_control(
			'slider_nav_heading',
			[
				'label'     => __( 'Navigation', 'st-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slider_arrows',
			[
				'label'        => __( 'Show Arrows', 'st-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'slider_arrows_position',
			[
				'label'     => __( 'Arrows Position', 'st-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inside',
				'options'   => [
					'inside'  => __( 'Inside', 'st-elementor' ),
					'outside' => __( 'Outside', 'st-elementor' ),
				],
				'condition' => [ 'slider_arrows' => 'yes' ],
			]
		);

		$this->add_control(
			'slider_pagination',
			[
				'label'        => __( 'Show Pagination', 'st-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'slider_pagination_type',
			[
				'label'     => __( 'Pagination Type', 'st-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bullets',
				'options'   => [
					'bullets'      => __( 'Bullets', 'st-elementor' ),
					'fraction'     => __( 'Fraction', 'st-elementor' ),
					'progressbar'  => __( 'Progress', 'st-elementor' ),
				],
				'condition' => [ 'slider_pagination' => 'yes' ],
			]
		);

		$this->add_control(
			'slider_pagination_position',
			[
				'label'     => __( 'Pagination Position', 'st-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => [
					'bottom'   => __( 'Bottom', 'st-elementor' ),
					'top'      => __( 'Top', 'st-elementor' ),
					'both'     => __( 'Both', 'st-elementer' ),
				],
				'condition' => [ 'slider_pagination' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'slider_centered',
			[
				'label'        => __( 'Center Slides', 'st-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => [ 'slides_per_view!' => '1' ],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_controls() {
		$this->start_controls_section(
			'section_style_wrapper',
			[
				'label' => __( 'Wrapper', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'wrapper_background',
				'label'    => __( 'Background', 'st-elementor' ),
				'types'    => [ 'classic', 'gradient' ],
			]
		);

		$this->add_responsive_control(
			'wrapper_padding',
			[
				'label'      => __( 'Padding', 'st-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .st-fl-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'wrapper_margin',
			[
				'label'      => __( 'Margin', 'st-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .st-fl-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_border',
			[
				'label' => __( 'Border', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'wrapper_border',
				'label'    => __( 'Border', 'st-elementor' ),
				'selector' => '{{WRAPPER}} .st-fl-wrapper',
			]
		);

		$this->add_responsive_control(
			'wrapper_border_radius',
			[
				'label'      => __( 'Border Radius', 'st-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .st-fl-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_box_shadow',
			[
				'label' => __( 'Box Shadow', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'wrapper_box_shadow',
				'label'    => __( 'Box Shadow', 'st-elementor' ),
				'selector' => '{{WRAPPER}} .st-fl-wrapper',
			]
		);

		$this->end_controls_section();
	}

	private function register_advanced_controls() {
		$this->start_controls_section(
			'section_advanced',
			[
				'label' => __( 'Advanced', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'custom_css_class',
			[
				'label'       => __( 'Custom CSS Class', 'st-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Add custom CSS class to the widget wrapper for custom styling.', 'st-elementor' ),
			]
		);

		$this->add_control(
			'custom_css_id',
			[
				'label'       => __( 'Custom ID', 'st-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Add custom ID to the widget wrapper for custom styling.', 'st-elementor' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_arrow_style',
			[
				'label'     => __( 'Slider Arrow Style', 'st-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'layout_type' => 'slider', 'slider_arrows' => 'yes' ],
			]
		);

		$this->add_control(
			'slider_arrow_size',
			[
				'label'      => __( 'Arrow Size', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 20, 'max' => 60 ] ],
				'default'    => [ 'size' => 40, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .st-fl-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'slider_arrow_color',
			[
				'label'     => __( 'Arrow Color', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .st-fl-arrow' => 'color: {{VALUE}};',
					'{{WRAPPER}} .st-fl-arrow svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slider_arrow_bg',
			[
				'label'     => __( 'Arrow Background', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .st-fl-arrow' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'slider_arrow_radius',
			[
				'label'      => __( 'Arrow Border Radius', 'st-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .st-fl-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_pagination_style',
			[
				'label'     => __( 'Slider Pagination Style', 'st-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'layout_type' => 'slider', 'slider_pagination' => 'yes' ],
			]
		);

		$this->add_control(
			'slider_pagination_color',
			[
				'label'     => __( 'Pagination Color', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slider_pagination_active_color',
			[
				'label'     => __( 'Active Pagination Color', 'st-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6366f1',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'slider_pagination_spacing',
			[
				'label'      => __( 'Pagination Spacing', 'st-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 5, 'max' => 30 ] ],
				'default'    => [ 'size' => 10, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .st-fl-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$layout_type = $settings['layout_type'] ?? 'flex';

		$wrapper_class = 'st-fl-wrapper';
		$wrapper_id = '';

		if ( ! empty( $settings['custom_css_class'] ) ) {
			$wrapper_class .= ' ' . esc_attr( $settings['custom_css_class'] );
		}

		if ( ! empty( $settings['custom_css_id'] ) ) {
			$wrapper_id = ' id="' . esc_attr( $settings['custom_css_id'] ) . '"';
		}

		$unique_id = 'st-fl-' . $this->get_id();
		$wrapper_class .= ' ' . $unique_id;

		$this->add_render_attribute( 'wrapper', 'class', $wrapper_class );
		$this->add_render_attribute( 'wrapper', 'data-layout', $layout_type );

		if ( 'flex' === $layout_type ) {
			$this->add_flex_render_attributes( $settings );
		} elseif ( 'grid' === $layout_type ) {
			$this->add_grid_render_attributes( $settings );
		} elseif ( 'slider' === $layout_type ) {
			$this->add_slider_render_attributes( $settings );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?><?php echo $wrapper_id; ?>>
			<?php if ( 'slider' === $layout_type ) : ?>
				<div class="swiper-container st-fl-swiper" <?php echo $this->get_render_attribute_string( 'swiper-container' ); ?>>
					<div class="swiper-wrapper">
						<?php $this->render_slider_content(); ?>
					</div>
					<?php if ( 'yes' === ( $settings['slider_arrows'] ?? 'yes' ) ) : ?>
						<div class="st-fl-arrow st-fl-arrow-prev swiper-button-prev"></div>
						<div class="st-fl-arrow st-fl-arrow-next swiper-button-next"></div>
					<?php endif; ?>
					<?php if ( 'yes' === ( $settings['slider_pagination'] ?? 'yes' ) ) : ?>
						<div class="st-fl-pagination swiper-pagination"></div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="st-fl-inner-container">
					<?php $this->render_container_content( $layout_type ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	private function add_flex_render_attributes( $settings ) {
		$flex_props = [
			'display'         => 'flex',
			'flex-direction'  => $settings['flex_direction'] ?? 'row',
			'flex-wrap'       => $settings['flex_wrap'] ?? 'wrap',
			'justify-content' => $settings['justify_content'] ?? 'flex-start',
			'align-items'     => $settings['align_items'] ?? 'stretch',
			'align-content'   => $settings['align_content'] ?? 'stretch',
		];

		$css = '';
		foreach ( $flex_props as $prop => $value ) {
			$css .= $prop . ': ' . $value . '; ';
		}

		if ( ! empty( $settings['flex_gap']['size'] ) ) {
			$gap_unit = $settings['flex_gap']['unit'] ?? 'px';
			$css .= 'gap: ' . $settings['flex_gap']['size'] . $gap_unit . '; ';
		}

		$this->add_render_attribute( 'wrapper', 'style', trim( $css ) );
	}

	private function add_grid_render_attributes( $settings ) {
		$css = 'display: grid; ';
		$css .= 'grid-template-columns: repeat(' . ( $settings['grid_columns'] ?? 3 ) . ', 1fr); ';

		if ( ! empty( $settings['grid_row_gap']['size'] ) ) {
			$row_gap_unit = $settings['grid_row_gap']['unit'] ?? 'px';
			$css .= 'grid-row-gap: ' . $settings['grid_row_gap']['size'] . $row_gap_unit . '; ';
		}

		if ( ! empty( $settings['grid_column_gap']['size'] ) ) {
			$col_gap_unit = $settings['grid_column_gap']['unit'] ?? 'px';
			$css .= 'grid-column-gap: ' . $settings['grid_column_gap']['size'] . $col_gap_unit . '; ';
		}

		$grid_columns_tablet = $settings['grid_columns_tablet'] ?? 2;
		$grid_columns_mobile = $settings['grid_columns_mobile'] ?? 1;

		if ( 'yes' === ( $settings['grid_responsive'] ?? 'yes' ) ) {
			$css .= '--grid-col-tablet: ' . $grid_columns_tablet . '; ';
			$css .= '--grid-col-mobile: ' . $grid_columns_mobile . '; ';
		}

		$this->add_render_attribute( 'wrapper', 'style', trim( $css ) );
	}

	private function add_slider_render_attributes( $settings ) {
		$spv = $settings['slides_per_view'] ?? 3;
		$spv_tablet = $settings['slides_per_view_tablet'] ?? 2;
		$spv_mobile = $settings['slides_per_view_mobile'] ?? 1;
		$space_between = $settings['slider_space_between']['size'] ?? 20;
		$loop = 'yes' === ( $settings['slider_loop'] ?? 'yes' );
		$speed = intval( $settings['slider_speed'] ?? 500 );
		$effect = $settings['slider_effect'] ?? 'slide';
		$direction = $settings['slider_direction'] ?? 'horizontal';
		$autoplay = $settings['slider_autoplay'] ?? 'yes';
		$autoplay_delay = intval( $settings['slider_autoplay_speed'] ?? 3000 );
		$centered = 'yes' === ( $settings['slider_centered'] ?? '' );
		$pagination_type = $settings['slider_pagination_type'] ?? 'bullets';

		$this->add_render_attribute( 'swiper-container', 'data-spv', esc_attr( $spv ) );
		$this->add_render_attribute( 'swiper-container', 'data-spv-tablet', esc_attr( $spv_tablet ) );
		$this->add_render_attribute( 'swiper-container', 'data-spv-mobile', esc_attr( $spv_mobile ) );
		$this->add_render_attribute( 'swiper-container', 'data-space', esc_attr( $space_between ) );
		$this->add_render_attribute( 'swiper-container', 'data-loop', $loop ? 'true' : 'false' );
		$this->add_render_attribute( 'swiper-container', 'data-speed', esc_attr( $speed ) );
		$this->add_render_attribute( 'swiper-container', 'data-effect', esc_attr( $effect ) );
		$this->add_render_attribute( 'swiper-container', 'data-direction', esc_attr( $direction ) );
		$this->add_render_attribute( 'swiper-container', 'data-autoplay', esc_attr( $autoplay ) );
		$this->add_render_attribute( 'swiper-container', 'data-autoplay-delay', esc_attr( $autoplay_delay ) );
		$this->add_render_attribute( 'swiper-container', 'data-centered', $centered ? 'true' : 'false' );
		$this->add_render_attribute( 'swiper-container', 'data-pagination-type', esc_attr( $pagination_type ) );
	}

	private function render_container_content( $layout_type ) {
		echo '<div class="st-fl-inner elementor-element-edit-mode">';
		echo '<p class="st-fl-placeholder">' . esc_html__( 'Add Elementor inner containers here', 'st-elementor' ) . '</p>';
		echo '</div>';
	}

	private function render_slider_content() {
		echo '<div class="st-fl-slide swiper-slide">';
		echo '<div class="st-fl-slide-content">';
		echo '<p class="st-fl-placeholder">' . esc_html__( 'Add Elementor inner containers here', 'st-elementor' ) . '</p>';
		echo '</div>';
		echo '</div>';
	}

	public function render_plain_content() {
		echo '<div class="st-fl-wrapper"></div>';
	}
}