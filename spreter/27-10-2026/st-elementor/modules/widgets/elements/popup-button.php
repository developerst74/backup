<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit;

class ST_Popup_Button extends Widget_Base {

	public function get_name()       { return 'st_popup_button'; }
	public function get_title()      { return __( 'ST Popup Button', 'st-elementor' ); }
	public function get_icon()       { return 'eicon-button'; }
	public function get_categories() { return [ 'st-store-transform' ]; }

	protected function register_controls() {

		// ── Content ──────────────────────────────────────────────
		$this->start_controls_section( 'section_content', [
			'label' => __( 'Button', 'st-elementor' ),
		] );

		$this->add_control( 'button_text', [
			'label'   => __( 'Text', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Open Popup', 'st-elementor' ),
		] );

		// Fetch all published st_popup posts
		$popup_options = [ '0' => __( '— Select Popup —', 'st-elementor' ) ];
		$popups = get_posts( [
			'post_type'      => 'st_popup',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		] );
		foreach ( $popups as $p ) {
			$popup_options[ $p->ID ] = $p->post_title;
		}

		$this->add_control( 'popup_id', [
			'label'   => __( 'Select Popup', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '0',
			'options' => $popup_options,
		] );

		$this->add_control( 'button_icon', [
			'label'       => __( 'Icon', 'st-elementor' ),
			'type'        => Controls_Manager::ICONS,
			'skin'        => 'inline',
			'label_block' => false,
		] );

		$this->add_control( 'icon_position', [
			'label'   => __( 'Icon Position', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'before',
			'options' => [
				'before' => __( 'Before Text', 'st-elementor' ),
				'after'  => __( 'After Text', 'st-elementor' ),
			],
			'condition' => [ 'button_icon[value]!' => '' ],
		] );

		$this->add_responsive_control( 'align', [
			'label'     => __( 'Alignment', 'st-elementor' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'left'   => [ 'title' => __( 'Left', 'st-elementor' ),   'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => __( 'Center', 'st-elementor' ), 'icon' => 'eicon-text-align-center' ],
				'right'  => [ 'title' => __( 'Right', 'st-elementor' ),  'icon' => 'eicon-text-align-right' ],
			],
			'default'   => 'center',
			'selectors' => [ '{{WRAPPER}}' => 'text-align: {{VALUE}};' ],
		] );

		$this->end_controls_section();

		// ── Style ─────────────────────────────────────────────────
		$this->start_controls_section( 'section_style', [
			'label' => __( 'Button Style', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'btn_typography',
			'selector' => '{{WRAPPER}} .st-popup-btn',
		] );

		$this->add_responsive_control( 'btn_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '14', 'right' => '32', 'bottom' => '14', 'left' => '32', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-popup-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'btn_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-popup-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->start_controls_tabs( 'btn_tabs' );

		$this->start_controls_tab( 'btn_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );
		$this->add_control( 'btn_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-popup-btn' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-popup-btn' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'btn_border',
			'selector' => '{{WRAPPER}} .st-popup-btn',
		] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'btn_shadow',
			'selector' => '{{WRAPPER}} .st-popup-btn',
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'btn_hover', [ 'label' => __( 'Hover', 'st-elementor' ) ] );
		$this->add_control( 'btn_hover_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-popup-btn:hover' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_hover_bg', [
			'label'     => __( 'Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#4f46e5',
			'selectors' => [ '{{WRAPPER}} .st-popup-btn:hover' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_hover_shadow', [
			'label'     => __( 'Box Shadow', 'st-elementor' ),
			'type'      => Controls_Manager::BOX_SHADOW,
			'selectors' => [ '{{WRAPPER}} .st-popup-btn:hover' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};' ],
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control( 'btn_transition', [
			'label'     => __( 'Transition (s)', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
			'default'   => [ 'size' => 0.2 ],
			'selectors' => [ '{{WRAPPER}} .st-popup-btn' => 'transition: all {{SIZE}}s ease;' ],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s        = $this->get_settings_for_display();
		$popup_id = (int) ( $s['popup_id'] ?? 0 );
		$text     = $s['button_text'] ?? __( 'Open Popup', 'st-elementor' );
		$has_icon = ! empty( $s['button_icon']['value'] );
		$icon_pos = $s['icon_position'] ?? 'before';

		$attrs = 'class="st-popup-btn"';
		if ( $popup_id ) {
			$attrs .= ' data-st-popup="' . esc_attr( $popup_id ) . '"';
		}
		?>
		<button <?php echo $attrs; // phpcs:ignore ?>>
			<?php if ( $has_icon && 'before' === $icon_pos ) : ?>
				<span class="st-popup-btn-icon st-popup-btn-icon-before">
					<?php \Elementor\Icons_Manager::render_icon( $s['button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
				</span>
			<?php endif; ?>
			<span class="st-popup-btn-text"><?php echo esc_html( $text ); ?></span>
			<?php if ( $has_icon && 'after' === $icon_pos ) : ?>
				<span class="st-popup-btn-icon st-popup-btn-icon-after">
					<?php \Elementor\Icons_Manager::render_icon( $s['button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
				</span>
			<?php endif; ?>
		</button>
		<?php
	}
}
