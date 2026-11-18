<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Repeater;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ST_Price_Table extends Widget_Base {

	public function get_name() {
		return 'st_price_table';
	}

	public function get_title() {
		return __( 'ST Price Table', 'st-elementor' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'st-store-transform' ];
	}

	public function get_script_depends() {
		return [ 'st-elementor-public' ];
	}

	protected function register_controls() {

		// Header Section
		$this->start_controls_section(
			'section_header',
			[
				'label' => __( 'Header', 'st-elementor' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Pricing Plan', 'st-elementor' ),
				'placeholder' => __( 'Enter your title', 'st-elementor' ),
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'The best choice', 'st-elementor' ),
				'placeholder' => __( 'Enter your subtitle', 'st-elementor' ),
			]
		);

		$this->end_controls_section();

		// Pricing Section
		$this->start_controls_section(
			'section_pricing',
			[
				'label' => __( 'Pricing', 'st-elementor' ),
			]
		);

		$this->add_control(
			'currency',
			[
				'label' => __( 'Currency', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '$',
			]
		);

		$this->add_control(
			'price',
			[
				'label' => __( 'Price', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '29.99',
			]
		);

		$this->add_control(
			'discount_price',
			[
				'label' => __( 'Discount Price', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'e.g. 39.99', 'st-elementor' ),
			]
		);

		$this->add_control(
			'period',
			[
				'label' => __( 'Period', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '/mo',
			]
		);

		$this->end_controls_section();

		// Features Section
		$this->start_controls_section(
			'section_features',
			[
				'label' => __( 'Features', 'st-elementor' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'feature_text',
			[
				'label' => __( 'Text', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Feature item', 'st-elementor' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'feature_icon',
			[
				'label' => __( 'Icon', 'st-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-check',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} i' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'features',
			[
				'label' => __( 'Features List', 'st-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'feature_text' => __( 'Unlimited Support', 'st-elementor' ),
						'feature_icon' => [ 'value' => 'fas fa-check', 'library' => 'fa-solid' ],
					],
					[
						'feature_text' => __( 'Custom Domain', 'st-elementor' ),
						'feature_icon' => [ 'value' => 'fas fa-check', 'library' => 'fa-solid' ],
					],
					[
						'feature_text' => __( '24/7 Monitoring', 'st-elementor' ),
						'feature_icon' => [ 'value' => 'fas fa-check', 'library' => 'fa-solid' ],
					],
					[
						'feature_text' => __( 'Free Updates', 'st-elementor' ),
						'feature_icon' => [ 'value' => 'fas fa-times', 'library' => 'fa-solid' ],
					],
				],
				'title_field' => '{{{ feature_text }}}',
			]
		);

		$this->end_controls_section();

		// Footer Section
		$this->start_controls_section(
			'section_footer',
			[
				'label' => __( 'Footer', 'st-elementor' ),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Get Started', 'st-elementor' ),
			]
		);

		$this->add_control(
			'button_link',
			[
				'label' => __( 'Link', 'st-elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'st-elementor' ),
				'default' => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'footer_text',
			[
				'label' => __( 'Additional Text', 'st-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 2,
				'default' => __( 'No credit card required', 'st-elementor' ),
			]
		);

		$this->end_controls_section();

		// Ribbon Section
		$this->start_controls_section(
			'section_ribbon',
			[
				'label' => __( 'Ribbon', 'st-elementor' ),
			]
		);

		$this->add_control(
			'show_ribbon',
			[
				'label' => __( 'Show Ribbon', 'st-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'st-elementor' ),
				'label_off' => __( 'Hide', 'st-elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'ribbon_title',
			[
				'label' => __( 'Ribbon Title', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Popular', 'st-elementor' ),
				'condition' => [
					'show_ribbon' => 'yes',
				],
			]
		);

		$this->add_control(
			'ribbon_horizontal_position',
			[
				'label' => __( 'Horizontal Position', 'st-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'st-elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'st-elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'right',
				'condition' => [
					'show_ribbon' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE TAB ---

		// General Style
		$this->start_controls_section(
			'section_style_general',
			[
				'label' => __( 'General', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'table_background',
				'label' => __( 'Background', 'st-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .st-price-table-wrapper',
			]
		);

		$this->add_responsive_control(
			'table_padding',
			[
				'label' => __( 'Padding', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .st-price-table-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'table_border_radius',
			[
				'label' => __( 'Border Radius', 'st-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 100 ],
				],
				'selectors' => [
					'{{WRAPPER}} .st-price-table-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'table_box_shadow',
				'selector' => '{{WRAPPER}} .st-price-table-wrapper',
			]
		);

		$this->end_controls_section();

		// Header Style
		$this->start_controls_section(
			'section_style_header',
			[
				'label' => __( 'Header', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'header_background',
				'label' => __( 'Background', 'st-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .st-price-header',
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label' => __( 'Padding', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .st-price-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_title_style',
			[
				'label' => __( 'Title', 'st-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-price-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .st-price-title',
			]
		);

		$this->add_control(
			'heading_subtitle_style',
			[
				'label' => __( 'Subtitle', 'st-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => __( 'Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-price-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .st-price-subtitle',
			]
		);

		$this->end_controls_section();

		// Pricing Style
		$this->start_controls_section(
			'section_style_pricing',
			[
				'label' => __( 'Pricing', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'pricing_color',
			[
				'label' => __( 'Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-price-value' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pricing_typography',
				'selector' => '{{WRAPPER}} .st-price-number',
			]
		);

		$this->add_control(
			'heading_discount_style',
			[
				'label' => __( 'Discount Price', 'st-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'discount_color',
			[
				'label' => __( 'Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-price-discount' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'discount_typography',
				'selector' => '{{WRAPPER}} .st-price-discount',
			]
		);

		$this->end_controls_section();

		// Features Style
		$this->start_controls_section(
			'section_style_features',
			[
				'label' => __( 'Features', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'features_color',
			[
				'label' => __( 'Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-price-features li' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'features_typography',
				'selector' => '{{WRAPPER}} .st-price-features li',
			]
		);

		$this->add_responsive_control(
			'features_padding',
			[
				'label' => __( 'Item Padding', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .st-price-features li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'features_divider',
			[
				'label' => __( 'Divider', 'st-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'st-elementor' ),
				'label_off' => __( 'Off', 'st-elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
				'selectors' => [
					'{{WRAPPER}} .st-price-features li:not(:last-child)' => 'border-bottom: 1px solid #eee;',
				],
			]
		);

		$this->end_controls_section();

		// Button Style
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => __( 'Button', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .st-price-button',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'st-elementor' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __( 'Text Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-price-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background',
				'label' => __( 'Background', 'st-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .st-price-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'st-elementor' ),
			]
		);

		$this->add_control(
			'button_hover_text_color',
			[
				'label' => __( 'Text Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-price-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_hover_background',
				'label' => __( 'Background', 'st-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .st-price-button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => __( 'Padding', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .st-price-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'st-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .st-price-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Footer Style
		$this->start_controls_section(
			'section_style_footer_note',
			[
				'label' => __( 'Footer Note', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'footer_note_color',
			[
				'label' => __( 'Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-price-footer-note' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'footer_note_typography',
				'selector' => '{{WRAPPER}} .st-price-footer-note',
			]
		);

		$this->end_controls_section();

		// Ribbon Style
		$this->start_controls_section(
			'section_style_ribbon',
			[
				'label' => __( 'Ribbon', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_ribbon' => 'yes',
				],
			]
		);

		$this->add_control(
			'ribbon_bg_color',
			[
				'label' => __( 'Background Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-price-ribbon-inner' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ribbon_text_color',
			[
				'label' => __( 'Text Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-price-ribbon-inner' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ribbon_typography',
				'selector' => '{{WRAPPER}} .st-price-ribbon-inner',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'st-price-table-wrapper' );

		if ( 'yes' === $settings['show_ribbon'] && ! empty( $settings['ribbon_title'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'st-has-ribbon' );
			$this->add_render_attribute( 'ribbon-wrapper', 'class', 'st-price-ribbon' );
			$this->add_render_attribute( 'ribbon-wrapper', 'class', 'st-ribbon-' . $settings['ribbon_horizontal_position'] );
		}

		$target = ! empty( $settings['button_link']['is_external'] ) ? ' target="_blank"' : '';
		$nofollow = ! empty( $settings['button_link']['nofollow'] ) ? ' rel="nofollow"' : '';
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			
			<?php if ( 'yes' === $settings['show_ribbon'] && ! empty( $settings['ribbon_title'] ) ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'ribbon-wrapper' ); ?>>
					<div class="st-price-ribbon-inner"><?php echo esc_html( $settings['ribbon_title'] ); ?></div>
				</div>
			<?php endif; ?>

			<div class="st-price-header">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h3 class="st-price-title"><?php echo esc_html( $settings['title'] ); ?></h3>
				<?php endif; ?>

				<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
					<span class="st-price-subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></span>
				<?php endif; ?>
			</div>

			<div class="st-price-value">
				<?php if ( ! empty( $settings['discount_price'] ) ) : ?>
					<span class="st-price-discount"><?php echo esc_html( $settings['currency'] . $settings['discount_price'] ); ?></span>
				<?php endif; ?>
				
				<div class="st-price-main">
					<span class="st-price-currency"><?php echo esc_html( $settings['currency'] ); ?></span>
					<span class="st-price-number"><?php echo esc_html( $settings['price'] ); ?></span>
					<span class="st-price-period"><?php echo esc_html( $settings['period'] ); ?></span>
				</div>
			</div>

			<?php if ( ! empty( $settings['features'] ) ) : ?>
				<ul class="st-price-features">
					<?php foreach ( $settings['features'] as $item ) : 
						$repeater_setting_key = $this->get_repeater_setting_key( 'feature_text', 'features', $item['_id'] );
						$this->add_render_attribute( $repeater_setting_key, 'class', 'st-price-feature-item' );
						?>
						<li <?php echo $this->get_render_attribute_string( $repeater_setting_key ); ?>>
							<?php if ( ! empty( $item['feature_icon']['value'] ) ) : ?>
								<span class="st-feature-icon">
									<?php Icons_Manager::render_icon( $item['feature_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</span>
							<?php endif; ?>
							<span class="st-feature-text"><?php echo esc_html( $item['feature_text'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="st-price-footer">
				<?php if ( ! empty( $settings['button_text'] ) ) : ?>
					<a href="<?php echo esc_url( $settings['button_link']['url'] ); ?>" class="st-price-button" <?php echo $target . $nofollow; ?>>
						<?php echo esc_html( $settings['button_text'] ); ?>
					</a>
				<?php endif; ?>

				<?php if ( ! empty( $settings['footer_text'] ) ) : ?>
					<div class="st-price-footer-note"><?php echo wp_kses_post( $settings['footer_text'] ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
