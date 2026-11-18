<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ST_Image_Box extends Widget_Base {

	public function get_name() {
		return 'st_image_box';
	}

	public function get_title() {
		return __( 'ST Image Box', 'st-elementor' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_categories() {
		return [ 'st-store-transform' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_image',
			[
				'label' => __( 'Image Box', 'st-elementor' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'st-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'title_text',
			[
				'label' => __( 'Title', 'st-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the heading', 'st-elementor' ),
				'placeholder' => __( 'Enter your title', 'st-elementor' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description_text',
			[
				'label' => __( 'Description', 'st-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'st-elementor' ),
				'placeholder' => __( 'Enter your description', 'st-elementor' ),
				'separator' => 'none',
				'rows' => 10,
				'show_label' => false,
			]
		);

		$this->end_controls_section();

		// Style Tab
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __( 'Image', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label' => __( 'Width', 'st-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .st-image-box-img img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'st-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-image-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .st-image-box-title',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Description Color', 'st-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st-image-box-description' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .st-image-box-description',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'title_text', 'class', 'st-image-box-title' );
		$this->add_render_attribute( 'description_text', 'class', 'st-image-box-description' );

		$this->add_inline_editing_attributes( 'title_text', 'none' );
		$this->add_inline_editing_attributes( 'description_text' );
		?>
		<div class="st-image-box-wrapper">
			<?php if ( ! empty( $settings['image']['url'] ) ) : ?>
				<div class="st-image-box-img">
					<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' ); ?>
				</div>
			<?php endif; ?>
			<div class="st-image-box-content">
				<?php if ( ! empty( $settings['title_text'] ) ) : ?>
					<h3 <?php echo $this->get_render_attribute_string( 'title_text' ); ?>><?php echo $settings['title_text']; ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $settings['description_text'] ) ) : ?>
					<p <?php echo $this->get_render_attribute_string( 'description_text' ); ?>><?php echo $settings['description_text']; ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
