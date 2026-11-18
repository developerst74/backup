<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit;

class ST_Table_Of_Contents extends Widget_Base {

	public function get_name()       { return 'st_table_of_contents'; }
	public function get_title()      { return __( 'ST Table of Contents', 'st-elementor' ); }
	public function get_icon()       { return 'eicon-bullet-list'; }
	public function get_categories() { return [ 'st-store-transform' ]; }
	public function get_script_depends() { return [ 'st-elementor-public' ]; }

	protected function register_controls() {

		$this->start_controls_section( 'section_content', [
			'label' => __( 'Table of Contents', 'st-elementor' ),
		] );

		$this->add_control( 'title', [
			'label'   => __( 'Title', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Table of Contents', 'st-elementor' ),
		] );

		$this->add_control( 'heading_tags', [
			'label'       => __( 'Include Heading Tags', 'st-elementor' ),
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'label_block' => true,
			'default'     => [ 'h2', 'h3' ],
			'options'     => [
				'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3',
				'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
			],
		] );

		$this->add_control( 'list_style', [
			'label'   => __( 'List Style', 'st-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'numbered',
			'options' => [
				'numbered' => __( 'Numbered', 'st-elementor' ),
				'bullets'  => __( 'Bullets', 'st-elementor' ),
				'none'     => __( 'None', 'st-elementor' ),
			],
		] );

		$this->add_control( 'hierarchical', [
			'label'        => __( 'Hierarchical (Nested)', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'smooth_scroll', [
			'label'        => __( 'Smooth Scroll', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'scroll_offset', [
			'label'     => __( 'Scroll Offset (px)', 'st-elementor' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 80,
			'condition' => [ 'smooth_scroll' => 'yes' ],
		] );

		$this->end_controls_section();

		// ── Style: Container ─────────────────────────────────────────
		$this->start_controls_section( 'style_container', [
			'label' => __( 'Container', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name' => 'container_bg', 'types' => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-toc-wrap',
		] );

		$this->add_responsive_control( 'container_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-toc-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name' => 'container_border', 'selector' => '{{WRAPPER}} .st-toc-wrap',
		] );

		$this->add_responsive_control( 'container_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '12', 'right' => '12', 'bottom' => '12', 'left' => '12', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-toc-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name' => 'container_shadow', 'selector' => '{{WRAPPER}} .st-toc-wrap',
		] );

		$this->end_controls_section();

		// ── Style: Title ─────────────────────────────────────────────
		$this->start_controls_section( 'style_title', [
			'label' => __( 'Title', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'title_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111827',
			'selectors' => [ '{{WRAPPER}} .st-toc-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'title_typo', 'selector' => '{{WRAPPER}} .st-toc-title',
		] );

		$this->add_responsive_control( 'title_spacing', [
			'label'     => __( 'Spacing Below', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'   => [ 'size' => 16 ],
			'selectors' => [ '{{WRAPPER}} .st-toc-title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Style: List Items ────────────────────────────────────────
		$this->start_controls_section( 'style_items', [
			'label' => __( 'List Items', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'item_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#374151',
			'selectors' => [ '{{WRAPPER}} .st-toc-list a' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'item_hover_color', [
			'label'     => __( 'Hover Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-toc-list a:hover' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'item_active_color', [
			'label'     => __( 'Active Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [ '{{WRAPPER}} .st-toc-list a.st-toc-active' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'item_typo', 'selector' => '{{WRAPPER}} .st-toc-list a',
		] );

		$this->add_responsive_control( 'item_spacing', [
			'label'     => __( 'Item Spacing', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
			'default'   => [ 'size' => 8 ],
			'selectors' => [ '{{WRAPPER}} .st-toc-list li' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'item_padding', [
			'label'      => __( 'Item Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top' => '6', 'right' => '10', 'bottom' => '6', 'left' => '10', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-toc-list a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( 'item_hover_bg', [
			'label'     => __( 'Hover Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-toc-list a:hover' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'item_active_bg', [
			'label'     => __( 'Active Background', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#f5f3ff',
			'selectors' => [ '{{WRAPPER}} .st-toc-list a.st-toc-active' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'nested_indent', [
			'label'     => __( 'Nested Indent', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'   => [ 'size' => 20 ],
			'selectors' => [ '{{WRAPPER}} .st-toc-list ul' => 'padding-left: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Style: Marker ────────────────────────────────────────────
		$this->start_controls_section( 'style_marker', [
			'label' => __( 'Marker / Number', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'marker_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6366f1',
			'selectors' => [
				'{{WRAPPER}} .st-toc-numbered > li::before' => 'color: {{VALUE}};',
				'{{WRAPPER}} .st-toc-bullets > li::before'  => 'color: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$tags         = $s['heading_tags']  ?? [ 'h2', 'h3' ];
		$list_style   = $s['list_style']    ?? 'numbered';
		$hierarchical = 'yes' === ( $s['hierarchical']  ?? 'yes' );
		$smooth       = 'yes' === ( $s['smooth_scroll'] ?? 'yes' );
		$offset       = (int) ( $s['scroll_offset'] ?? 80 );

		if ( empty( $tags ) ) {
			echo '<p style="padding:16px;color:#9ca3af">' . esc_html__( 'Please select at least one heading tag.', 'st-elementor' ) . '</p>';
			return;
		}

		$list_class = 'st-toc-list st-toc-' . esc_attr( $list_style );
		if ( $hierarchical ) $list_class .= ' st-toc-hierarchical';
		?>
		<div class="st-toc-wrap"
		     data-smooth="<?php echo $smooth ? '1' : '0'; ?>"
		     data-offset="<?php echo esc_attr( $offset ); ?>"
		     data-tags="<?php echo esc_attr( implode( ',', (array) $tags ) ); ?>">
			<?php if ( ! empty( $s['title'] ) ) : ?>
			<h3 class="st-toc-title"><?php echo esc_html( $s['title'] ); ?></h3>
			<?php endif; ?>
			<div class="st-toc-list-wrap">
				<ul class="<?php echo esc_attr( $list_class ); ?>">
					<!-- JS will populate this -->
				</ul>
			</div>
		</div>
		<?php
	}
}
