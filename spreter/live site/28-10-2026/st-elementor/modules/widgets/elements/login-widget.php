<?php
namespace ST_Elementor\Modules\Widgets\Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ST_Login_Widget extends Widget_Base {

	public function get_name()  { return 'st_login_widget'; }
	public function get_title() { return __( 'ST Login Form', 'st-elementor' ); }
	public function get_icon()  { return 'eicon-lock-user'; }
	public function get_categories() { return [ 'st-store-transform' ]; }

	protected function register_controls() {

		// ═══════════════════════════════════════════════════════════════
		// CONTENT
		// ═══════════════════════════════════════════════════════════════

		$this->start_controls_section( 'section_content', [
			'label' => __( 'Form Content', 'st-elementor' ),
		] );

		$this->add_control( 'form_title', [
			'label'   => __( 'Form Title', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Welcome Back', 'st-elementor' ),
		] );

		$this->add_control( 'form_subtitle', [
			'label'   => __( 'Form Subtitle', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Sign in to your account', 'st-elementor' ),
		] );

		// ── Logo ─────────────────────────────────────────────────────
		$this->add_control( 'show_logo', [
			'label'        => __( 'Show Logo', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
			'separator'    => 'before',
		] );

		$this->add_control( 'logo_image', [
			'label'     => __( 'Logo Image', 'st-elementor' ),
			'type'      => Controls_Manager::MEDIA,
			'condition' => [ 'show_logo' => 'yes' ],
		] );

		$this->add_control( 'logo_url', [
			'label'       => __( 'Or Logo URL', 'st-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => 'https://example.com/logo.png',
			'condition'   => [ 'show_logo' => 'yes' ],
		] );

		$this->add_responsive_control( 'logo_width', [
			'label'      => __( 'Logo Width', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [ 'px' => [ 'min' => 20, 'max' => 300 ] ],
			'default'    => [ 'size' => 80, 'unit' => 'px' ],
			'condition'  => [ 'show_logo' => 'yes' ],
			'selectors'  => [ '{{WRAPPER}} .st-login-logo img' => 'width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'logo_link', [
			'label'     => __( 'Logo Link', 'st-elementor' ),
			'type'      => Controls_Manager::URL,
			'default'   => [ 'url' => home_url() ],
			'condition' => [ 'show_logo' => 'yes' ],
		] );

		$this->add_control( 'username_label', [
			'label'     => __( 'Username Label', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'Username or Email', 'st-elementor' ),
			'separator' => 'before',
		] );

		$this->add_control( 'username_placeholder', [
			'label'   => __( 'Username Placeholder', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Enter your username', 'st-elementor' ),
		] );

		$this->add_control( 'password_label', [
			'label'   => __( 'Password Label', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Password', 'st-elementor' ),
		] );

		$this->add_control( 'password_placeholder', [
			'label'   => __( 'Password Placeholder', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Enter your password', 'st-elementor' ),
		] );

		$this->add_control( 'btn_text', [
			'label'     => __( 'Button Text', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'Sign In', 'st-elementor' ),
			'separator' => 'before',
		] );

		$this->add_control( 'show_remember', [
			'label'        => __( 'Show Remember Me', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'show_forgot', [
			'label'        => __( 'Show Forgot Password', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'forgot_text', [
			'label'     => __( 'Forgot Password Text', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'Forgot password?', 'st-elementor' ),
			'condition' => [ 'show_forgot' => 'yes' ],
		] );

		$this->add_control( 'show_register', [
			'label'        => __( 'Show Register Link', 'st-elementor' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
			'separator'    => 'before',
		] );

		$this->add_control( 'register_text', [
			'label'     => __( 'Register Text', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( "Don't have an account?", 'st-elementor' ),
			'condition' => [ 'show_register' => 'yes' ],
		] );

		$this->add_control( 'register_link_text', [
			'label'     => __( 'Register Link Text', 'st-elementor' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'Sign Up', 'st-elementor' ),
			'condition' => [ 'show_register' => 'yes' ],
		] );

		$this->add_control( 'register_url', [
			'label'     => __( 'Register URL', 'st-elementor' ),
			'type'      => Controls_Manager::URL,
			'default'   => [ 'url' => wp_registration_url() ],
			'condition' => [ 'show_register' => 'yes' ],
		] );

		$this->add_control( 'redirect_url', [
			'label'       => __( 'Redirect After Login', 'st-elementor' ),
			'type'        => Controls_Manager::URL,
			'placeholder' => home_url(),
			'separator'   => 'before',
			'description' => __( 'Leave empty to use admin settings value.', 'st-elementor' ),
		] );

		$this->add_control( 'logged_in_message', [
			'label'   => __( 'Logged-in Message', 'st-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'You are already logged in.', 'st-elementor' ),
		] );

		$this->end_controls_section();

		// ═══════════════════════════════════════════════════════════════
		// STYLE
		// ═══════════════════════════════════════════════════════════════

		// ── Form Wrapper ─────────────────────────────────────────────
		$this->start_controls_section( 'style_wrapper', [
			'label' => __( 'Form Wrapper', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'wrapper_bg',
			'types'    => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .st-login-form-wrap',
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'wrapper_border',
			'selector' => '{{WRAPPER}} .st-login-form-wrap',
		] );

		$this->add_responsive_control( 'wrapper_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '12', 'right' => '12', 'bottom' => '12', 'left' => '12', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-login-form-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'wrapper_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'default'    => [ 'top' => '40', 'right' => '40', 'bottom' => '40', 'left' => '40', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-login-form-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'wrapper_shadow',
			'selector' => '{{WRAPPER}} .st-login-form-wrap',
		] );

		$this->add_responsive_control( 'wrapper_max_width', [
			'label'      => __( 'Max Width', 'st-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [ 'px' => [ 'min' => 200, 'max' => 900 ] ],
			'default'    => [ 'size' => 480, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .st-login-form-wrap' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ],
		] );

		$this->end_controls_section();

		// ── Title & Subtitle ─────────────────────────────────────────
		$this->start_controls_section( 'style_heading', [
			'label' => __( 'Title & Subtitle', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'title_color', [
			'label'     => __( 'Title Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-login-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'title_typo', 'selector' => '{{WRAPPER}} .st-login-title',
		] );

		$this->add_control( 'subtitle_color', [
			'label'     => __( 'Subtitle Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}} .st-login-subtitle' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'subtitle_typo', 'selector' => '{{WRAPPER}} .st-login-subtitle',
		] );

		$this->add_responsive_control( 'heading_spacing', [
			'label'     => __( 'Spacing Below Heading', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'   => [ 'size' => 28 ],
			'selectors' => [ '{{WRAPPER}} .st-login-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Labels ───────────────────────────────────────────────────
		$this->start_controls_section( 'style_labels', [
			'label' => __( 'Labels', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'label_color', [
			'label'     => __( 'Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-login-label' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'label_typo', 'selector' => '{{WRAPPER}} .st-login-label',
		] );

		$this->add_responsive_control( 'label_spacing', [
			'label'     => __( 'Spacing Below Label', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
			'default'   => [ 'size' => 6 ],
			'selectors' => [ '{{WRAPPER}} .st-login-label' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Input Fields ─────────────────────────────────────────────
		$this->start_controls_section( 'style_inputs', [
			'label' => __( 'Input Fields', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'input_text_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-login-input' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'input_placeholder_color', [
			'label'     => __( 'Placeholder Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-login-input::placeholder' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'input_bg',
			'types'    => [ 'classic' ],
			'selector' => '{{WRAPPER}} .st-login-input',
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'input_border',
			'selector' => '{{WRAPPER}} .st-login-input',
		] );

		$this->add_responsive_control( 'input_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-login-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'input_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '12', 'right' => '16', 'bottom' => '12', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-login-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'field_spacing', [
			'label'     => __( 'Field Spacing', 'st-elementor' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'   => [ 'size' => 20 ],
			'selectors' => [ '{{WRAPPER}} .st-login-field' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// ── Button ───────────────────────────────────────────────────
		$this->start_controls_section( 'style_button', [
			'label' => __( 'Button', 'st-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'btn_typo', 'selector' => '{{WRAPPER}} .st-login-btn',
		] );

		$this->start_controls_tabs( 'tabs_btn' );

		$this->start_controls_tab( 'tab_btn_normal', [ 'label' => __( 'Normal', 'st-elementor' ) ] );
		$this->add_control( 'btn_text_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .st-login-btn' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name' => 'btn_bg', 'types' => [ 'classic', 'gradient' ], 'selector' => '{{WRAPPER}} .st-login-btn',
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_btn_hover', [ 'label' => __( 'Hover', 'st-elementor' ) ] );
		$this->add_control( 'btn_hover_text_color', [
			'label'     => __( 'Text Color', 'st-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .st-login-btn:hover' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name' => 'btn_hover_bg', 'types' => [ 'classic', 'gradient' ], 'selector' => '{{WRAPPER}} .st-login-btn:hover',
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control( 'btn_padding', [
			'label'      => __( 'Padding', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'separator'  => 'before',
			'default'    => [ 'top' => '14', 'right' => '28', 'bottom' => '14', 'left' => '28', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .st-login-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'btn_radius', [
			'label'      => __( 'Border Radius', 'st-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'default'    => [ 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [ '{{WRAPPER}} .st-login-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'btn_shadow',
			'selector' => '{{WRAPPER}} .st-login-btn',
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode()
		          || \Elementor\Plugin::$instance->preview->is_preview_mode();

		// In editor/preview always show the form so designer can style it.
		// On frontend, show "logged in" message if user is already logged in.
		if ( ! $is_editor && is_user_logged_in() ) {
			echo '<div class="st-login-form-wrap"><p class="st-login-logged-in">' . esc_html( $settings['logged_in_message'] ) . '</p></div>';
			return;
		}

		$admin_settings = \ST_Elementor\Modules\Login\Login::get_settings();
		$redirect = ! empty( $settings['redirect_url']['url'] )
			? $settings['redirect_url']['url']
			: ( ! empty( $admin_settings['redirect_url'] ) ? $admin_settings['redirect_url'] : home_url( '/' ) );

		$a = $admin_settings;
		?>
		<style>
			.elementor-widget-st_login_widget .st-login-form-wrap {
				--st-input-bg: <?php echo esc_attr( $a['input_bg'] ); ?>;
				--st-input-border: <?php echo esc_attr( $a['input_border'] ); ?>;
				--st-input-color: <?php echo esc_attr( $a['input_color'] ); ?>;
				--st-btn-bg: <?php echo esc_attr( $a['btn_bg'] ); ?>;
				--st-btn-color: <?php echo esc_attr( $a['btn_color'] ); ?>;
				--st-btn-hover-bg: <?php echo esc_attr( $a['btn_hover_bg'] ); ?>;
				--st-label-color: <?php echo esc_attr( $a['label_color'] ); ?>;
			}
		</style>

		<div class="st-login-form-wrap">

			<?php
			// ── Logo ─────────────────────────────────────────────────
			if ( 'yes' === ( $settings['show_logo'] ?? '' ) ) :
				$logo_src = ! empty( $settings['logo_url'] ) ? $settings['logo_url'] : ( $settings['logo_image']['url'] ?? '' );
				if ( empty( $logo_src ) ) {
					$logo_id = get_theme_mod( 'custom_logo' );
					if ( $logo_id ) {
						$img = wp_get_attachment_image_src( $logo_id, 'full' );
						$logo_src = $img ? $img[0] : '';
					}
				}
				$logo_link_url = ! empty( $settings['logo_link']['url'] ) ? $settings['logo_link']['url'] : home_url();
				if ( $logo_src ) :
			?>
			<div class="st-login-logo">
				<a href="<?php echo esc_url( $logo_link_url ); ?>">
					<img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				</a>
			</div>
			<?php endif; endif; ?>

			<?php if ( ! empty( $settings['form_title'] ) || ! empty( $settings['form_subtitle'] ) ) : ?>
			<div class="st-login-heading">
				<?php if ( ! empty( $settings['form_title'] ) ) : ?>
					<h2 class="st-login-title"><?php echo esc_html( $settings['form_title'] ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $settings['form_subtitle'] ) ) : ?>
					<p class="st-login-subtitle"><?php echo esc_html( $settings['form_subtitle'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<?php if ( ! $is_editor && isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) : ?>
				<div class="st-login-error"><?php esc_html_e( 'Invalid username or password.', 'st-elementor' ); ?></div>
			<?php endif; ?>

			<?php if ( $is_editor ) : ?>
			<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:8px 12px;margin-bottom:16px;font-size:12px;color:#1d4ed8;text-align:center">
				<?php esc_html_e( 'Login form preview — styling applies on frontend', 'st-elementor' ); ?>
			</div>
			<?php endif; ?>

			<form class="st-login-form" method="post" action="<?php echo $is_editor ? '#' : esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" data-ajax-login="1">

				<div class="st-login-field">
					<label class="st-login-label" for="st_user_login_<?php echo esc_attr( $this->get_id() ); ?>">
						<?php echo esc_html( $settings['username_label'] ); ?>
					</label>
					<input
						id="st_user_login_<?php echo esc_attr( $this->get_id() ); ?>"
						class="st-login-input"
						type="text"
						name="log"
						autocomplete="off"
						placeholder="<?php echo esc_attr( $settings['username_placeholder'] ); ?>"
						<?php echo $is_editor ? '' : 'required'; ?>
					>
				</div>

				<div class="st-login-field">
					<label class="st-login-label" for="st_user_pass_<?php echo esc_attr( $this->get_id() ); ?>">
						<?php echo esc_html( $settings['password_label'] ); ?>
					</label>
					<input
						id="st_user_pass_<?php echo esc_attr( $this->get_id() ); ?>"
						class="st-login-input"
						type="password"
						name="pwd"
						autocomplete="off"
						placeholder="<?php echo esc_attr( $settings['password_placeholder'] ); ?>"
						<?php echo $is_editor ? '' : 'required'; ?>
					>
				</div>

				<?php if ( 'yes' === $settings['show_remember'] ) : ?>
				<div class="st-login-field st-login-remember">
					<label class="st-login-remember-label">
						<input type="checkbox" name="rememberme" value="forever">
						<?php esc_html_e( 'Remember me', 'st-elementor' ); ?>
					</label>
				</div>
				<?php endif; ?>

				<?php if ( ! $is_editor && ! empty( $redirect ) ) : ?>
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect ); ?>">
				<?php endif; ?>

				<?php if ( ! $is_editor ) : ?>
					<input type="hidden" name="wp-submit" value="Log In">
				<?php endif; ?>

				<div class="st-login-field st-login-submit">
					<button type="<?php echo $is_editor ? 'button' : 'submit'; ?>" class="st-login-btn">
						<?php echo esc_html( $settings['btn_text'] ); ?>
					</button>
				</div>

				<div class="st-login-links">
					<?php if ( 'yes' === ( $settings['show_forgot'] ?? 'yes' ) ) : ?>
					<a href="<?php echo $is_editor ? '#' : esc_url( wp_lostpassword_url() ); ?>" class="st-login-forgot">
						<?php echo esc_html( $settings['forgot_text'] ?? __( 'Forgot password?', 'st-elementor' ) ); ?>
					</a>
					<?php endif; ?>
				</div>

			</form>

			<?php if ( 'yes' === ( $settings['show_register'] ?? '' ) ) :
				$reg_url = ! empty( $settings['register_url']['url'] ) ? $settings['register_url']['url'] : wp_registration_url();
			?>
			<div class="st-login-register">
				<span><?php echo esc_html( $settings['register_text'] ?? __( "Don't have an account?", 'st-elementor' ) ); ?></span>
				<a href="<?php echo $is_editor ? '#' : esc_url( $reg_url ); ?>" class="st-login-register-link">
					<?php echo esc_html( $settings['register_link_text'] ?? __( 'Sign Up', 'st-elementor' ) ); ?>
				</a>
			</div>
			<?php endif; ?>

		</div>
		<?php
	}
}
