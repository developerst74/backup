<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ST_Accordion extends Widget_Base {

	public function get_name() {
		return 'st_accordion';
	}

	public function get_title() {
		return __( 'ST Pro Accordion', 'st-elementor' );
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function get_categories() {
		return [ 'st-store-transform' ];
	}

	public function get_script_depends() {
		return [ 'st-elementor-public' ];
	}

	protected function get_elementor_templates() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return [ '' => __( 'Select Template', 'st-elementor' ) ];
		}

		$templates = get_posts( [
			'post_type'      => 'elementor_library',
			'posts_per_page' => 50,
			'post_status'    => 'publish',
		] );

		$options = [ '' => __( 'Select Template', 'st-elementor' ) ];

		if ( ! empty( $templates ) && ! is_wp_error( $templates ) ) {
			foreach ( $templates as $template ) {
				$options[ $template->ID ] = $template->post_title;
			}
		}

		return $options;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_accordion',
			[
				'label' => __( 'Accordion Items', 'st-elementor' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'accordion_title',
			[
				'label' => __( 'Title', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Accordion Title', 'st-elementor' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'content_type',
			[
				'label' => __( 'Content Type', 'st-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => __( 'Text / WYSIWYG', 'st-elementor' ),
					'template' => __( 'Elementor Template', 'st-elementor' ),
				],
			]
		);

		$repeater->add_control(
			'accordion_content',
			[
				'label' => __( 'Content', 'st-elementor' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Accordion Content Description', 'st-elementor' ),
				'show_label' => false,
				'condition' => [
					'content_type' => 'text',
				],
			]
		);

		$repeater->add_control(
			'template_id',
			[
				'label' => __( 'Select Template', 'st-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_elementor_templates(),
				'condition' => [
					'content_type' => 'template',
				],
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Items', 'st-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'accordion_title' => __( 'What is ST Elementor?', 'st-elementor' ),
						'accordion_content' => __( 'ST Elementor is a premium modular page builder extension system.', 'st-elementor' ),
					],
					[
						'accordion_title' => __( 'Is it SEO friendly?', 'st-elementor' ),
						'accordion_content' => __( 'Yes, our widgets are built with clean code and SEO standards.', 'st-elementor' ),
					],
				],
				'title_field' => '{{{ accordion_title }}}',
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'st-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'traditional',
				'options' => [
					'traditional' => __( 'Traditional', 'st-elementor' ),
					'separated' => __( 'Separated', 'st-elementor' ),
				],
				'prefix_class' => 'st-accordion-view-',
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'st-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'active_icon',
			[
				'label' => __( 'Active Icon', 'st-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-minus',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label' => __( 'Icon Position', 'st-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left' => __( 'Left', 'st-elementor' ),
					'right' => __( 'Right', 'st-elementor' ),
				],
				'prefix_class' => 'st-accordion-icon-at-',
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label' => __( 'Transition Speed (ms)', 'st-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 300,
				'min' => 100,
				'max' => 2000,
				'step' => 50,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		// --- STYLE TAB ---

		// General Style
		$this->start_controls_section(
			'section_accordion_style',
			[
				'label' => __( 'Accordion Style', 'st-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[
				'label' => __( 'Item Spacing', 'st-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}.st-accordion-view-separated .st-accordion-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'view' => 'separated',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'accordion_border',
				'selector' => '{{WRAPPER}} .st-accordion-item',
			]
		);

		$this->add_responsive_control(
			'accordion_border_radius',
			[
				'label' => __( 'Border Radius', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .st-accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .st-accordion-item:first-child .st-accordion-title' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .st-accordion-item:last-child .st-accordion-content' => 'border-bottom-left-radius: {{BOTTOM}}{{UNIT}}; border-bottom-right-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'accordion_box_shadow',
				'selector' => '{{WRAPPER}} .st-accordion-item',
			]
		);

		$this->add_control(
			'accordion_box_shadow_hover',
			[
				'label' => __( 'Box Shadow Hover', 'st-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'accordion_box_shadow_h',
				'selector' => '{{WRAPPER}} .st-accordion-item:hover',
			]
		);

		$this->end_controls_section();

		// Title Style
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'st-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'title_state_tabs' );

		$this->start_controls_tab( 'title_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'title_background_group',
				'label' => __( 'Background', 'st-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .st-accordion-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-accordion-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'title_active', [ 'label' => __( 'Active', 'st-elementor' ) ] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'title_active_background_group',
				'label' => __( 'Background', 'st-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .st-accordion-item.st-active .st-accordion-title',
			]
		);

		$this->add_control(
			'title_active_color',
			[
				'label' => __( 'Text Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-accordion-item.st-active .st-accordion-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .st-accordion-title',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label' => __( 'Padding', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .st-accordion-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Icon Style
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => __( 'Icon', 'st-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'icon_state_tabs' );

		$this->start_controls_tab( 'icon_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-accordion-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .st-accordion-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label' => __( 'Background Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-accordion-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'icon_active', [ 'label' => __( 'Active', 'st-elementor' ) ] );

		$this->add_control(
			'icon_active_color',
			[
				'label' => __( 'Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-active .st-accordion-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .st-active .st-accordion-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_active_bg_color',
			[
				'label' => __( 'Background Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-active .st-accordion-icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'st-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [ 'min' => 10, 'max' => 50 ],
				],
				'selectors' => [
					'{{WRAPPER}} .st-accordion-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .st-accordion-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => __( 'Spacing', 'st-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [ 'min' => 0, 'max' => 50 ],
				],
				'selectors' => [
					'{{WRAPPER}}.st-accordion-icon-at-right .st-accordion-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.st-accordion-icon-at-left .st-accordion-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label' => __( 'Padding', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .st-accordion-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label' => __( 'Border Radius', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .st-accordion-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Content Style
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'st-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_background',
			[
				'label' => __( 'Background Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-accordion-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Text Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-accordion-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .st-accordion-content',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .st-accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['tabs'] ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', [
			'class' => 'st-accordion-wrapper',
			'data-speed' => $settings['transition_speed'],
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php foreach ( $settings['tabs'] as $index => $item ) : 
				$active_class = ( 0 === $index ) ? 'st-active' : '';
				?>
				<div class="st-accordion-item <?php echo esc_attr( $active_class ); ?>">
					<div class="st-accordion-title">
						<span class="st-accordion-title-text"><?php echo esc_html( $item['accordion_title'] ); ?></span>
						<span class="st-accordion-icon">
							<span class="st-accordion-icon-closed"><?php \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
							<span class="st-accordion-icon-opened"><?php \Elementor\Icons_Manager::render_icon( $settings['active_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
						</span>
					</div>
					<div class="st-accordion-content" style="<?php echo ( 0 === $index ) ? 'display:block;' : 'display:none;'; ?>">
						<?php 
						if ( 'template' === $item['content_type'] && ! empty( $item['template_id'] ) ) {
							echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $item['template_id'] );
						} else {
							echo $this->parse_text_editor( $item['accordion_content'] );
						}
						?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
