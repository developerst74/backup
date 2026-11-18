<?php
namespace ST_Elementor\Modules\Login;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Login {

	const OPTION_KEY = 'st_elementor_login_settings';

	public function __construct() {
		add_action( 'admin_post_st_save_login_settings', array( $this, 'save_settings' ) );
		add_action( 'init', array( $this, 'intercept_login_page' ), 1 );
		add_filter( 'login_url', array( $this, 'filter_login_url' ), 10, 3 );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_login_styles' ) );
		add_filter( 'login_headerurl',  array( $this, 'login_logo_url' ) );
		add_filter( 'login_headertext', array( $this, 'login_logo_title' ) );
		add_action( 'login_head', array( $this, 'inject_login_logo' ) );
		add_action( 'wp_ajax_nopriv_st_ajax_login', array( $this, 'ajax_login' ) );
		add_action( 'wp_ajax_st_ajax_login',        array( $this, 'ajax_login' ) );
		// Add body class on custom login page
		add_filter( 'body_class', array( $this, 'add_login_body_class' ) );
	}

	public function intercept_login_page() {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
		if ( strpos( $request_uri, 'wp-login.php' ) === false ) return;
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) return;
		$action = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : 'login';
		if ( ! in_array( $action, array( 'login', '' ), true ) ) return;
		$target_url = $this->get_login_page_url();
		if ( ! $target_url ) return;
		$redirect_to = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : '';
		if ( $redirect_to ) {
			$target_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $target_url );
		}
		wp_safe_redirect( $target_url, 302 );
		exit;
	}

	private function get_login_page_url() {
		$page_id = (int) get_option( 'st_login_page_id', 0 );
		if ( $page_id && get_post_status( $page_id ) === 'publish' ) {
			return get_permalink( $page_id );
		}
		$templates = get_posts( array(
			'post_type' => 'st_template', 'post_status' => 'publish',
			'posts_per_page' => 1, 'fields' => 'ids',
			'meta_query' => array( array( 'key' => '_st_template_type', 'value' => 'login' ) ),
		) );
		if ( empty( $templates ) ) return false;
		$template_id = (int) $templates[0];
		$page_id     = $this->get_or_create_login_page( $template_id );
		if ( $page_id ) {
			update_option( 'st_login_page_id', $page_id );
			return get_permalink( $page_id );
		}
		return false;
	}

	public function filter_login_url( $login_url, $redirect, $force_reauth ) {
		$page_id = (int) get_option( 'st_login_page_id', 0 );
		if ( $page_id && get_post_status( $page_id ) === 'publish' ) {
			$url = get_permalink( $page_id );
			if ( $redirect ) $url = add_query_arg( 'redirect_to', urlencode( $redirect ), $url );
			return $url;
		}
		return $login_url;
	}

	private function do_redirect( $base_url ) {
		if ( empty( $base_url ) ) return;
		$redirect_to = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : '';
		$url = $base_url;
		if ( $redirect_to ) $url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $url );
		wp_safe_redirect( $url, 302 );
		exit;
	}

	private function get_or_create_login_page( $template_id ) {
		global $wpdb;
		$shortcode = '[st_template id="' . $template_id . '"]';
		$page_id   = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type='page' AND post_status='publish' AND post_content LIKE %s LIMIT 1",
			'%' . $wpdb->esc_like( $shortcode ) . '%'
		) );
		if ( $page_id ) return $page_id;
		$new_id = wp_insert_post( array(
			'post_title'   => __( 'Login', 'st-elementor' ),
			'post_content' => $shortcode,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => get_current_user_id(),
		) );
		return ( $new_id && ! is_wp_error( $new_id ) ) ? (int) $new_id : 0;
	}

	public static function get_settings() {
		$defaults = array(
			'bg_color' => '#f0f2f5', 'bg_image' => '', 'logo_url' => '',
			'logo_width' => '80', 'logo_height' => '80',
			'form_bg' => '#ffffff', 'form_radius' => '12', 'form_shadow' => '1',
			'label_color' => '#374151', 'input_bg' => '#f9fafb',
			'input_border' => '#d1d5db', 'input_color' => '#111827', 'input_radius' => '8',
			'btn_bg' => '#6366f1', 'btn_color' => '#ffffff', 'btn_hover_bg' => '#4f46e5',
			'btn_radius' => '8', 'link_color' => '#6366f1', 'redirect_url' => '',
		);
		$settings = wp_parse_args( get_option( self::OPTION_KEY, array() ), $defaults );
		foreach ( $defaults as $key => $default ) {
			if ( ! isset( $settings[ $key ] ) || '' === $settings[ $key ] ) {
				$settings[ $key ] = $default;
			}
		}
		return $settings;
	}

	public function save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
		check_admin_referer( 'st_login_settings_nonce' );
		$fields = array( 'bg_color','bg_image','logo_url','logo_width','logo_height','form_bg','form_radius','form_shadow','label_color','input_bg','input_border','input_color','input_radius','btn_bg','btn_color','btn_hover_bg','btn_radius','link_color','redirect_url' );
		$data = array();
		foreach ( $fields as $field ) {
			$data[ $field ] = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
		}
		update_option( self::OPTION_KEY, $data );
		if ( isset( $_POST['st_login_page_id'] ) ) update_option( 'st_login_page_id', (int) $_POST['st_login_page_id'] );
		wp_redirect( add_query_arg( array( 'page' => 'st-elementor-login', 'saved' => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public function enqueue_login_styles() {
		$s            = self::get_settings();
		$bg_image_css = ! empty( $s['bg_image'] ) ? 'background-image:url(' . esc_url( $s['bg_image'] ) . ');background-size:cover;background-position:center;' : '';
		$shadow_css   = ! empty( $s['form_shadow'] ) ? 'box-shadow:0 8px 40px rgba(0,0,0,0.12);' : 'box-shadow:none;';
		$logo_src = '';
		if ( ! empty( $s['logo_url'] ) ) {
			$logo_src = esc_url( $s['logo_url'] );
		} else {
			$logo_id = get_theme_mod( 'custom_logo' );
			if ( $logo_id ) { $img = wp_get_attachment_image_src( $logo_id, 'full' ); if ( $img ) $logo_src = esc_url( $img[0] ); }
		}
		$logo_w   = (int) ( $s['logo_width'] ?? 80 );
		$logo_h   = (int) ( $s['logo_height'] ?? 80 );
		$logo_css = $logo_src ? '#login h1 a{background-image:url(' . $logo_src . ');width:' . $logo_w . 'px;height:' . $logo_h . 'px;background-size:contain;background-repeat:no-repeat;background-position:center;}' : '';

		$css  = 'body.login{background-color:' . $s['bg_color'] . ';' . $bg_image_css . '}';
		$css .= $logo_css;
		$css .= '#loginform,#lostpasswordform,#resetpassform,#registerform{background:' . $s['form_bg'] . ';border-radius:' . $s['form_radius'] . 'px;' . $shadow_css . 'padding:30px 36px;border:none;}';
		$css .= '#loginform label,#lostpasswordform label,#resetpassform label,#registerform label{color:' . $s['label_color'] . ';font-weight:600;}';
		$css .= '#loginform input[type=text],#loginform input[type=password],#loginform input[type=email],#lostpasswordform input[type=text],#lostpasswordform input[type=email],#resetpassform input[type=text],#resetpassform input[type=password],#registerform input[type=text],#registerform input[type=email]{background:' . $s['input_bg'] . ';border:1px solid ' . $s['input_border'] . ';color:' . $s['input_color'] . ';border-radius:' . $s['input_radius'] . 'px;box-shadow:none;padding:10px 14px;height:auto;font-size:14px;width:100%;box-sizing:border-box;}';
		$css .= '#wp-submit,.button-primary,input[type=submit]{background:' . $s['btn_bg'] . '!important;border-color:' . $s['btn_bg'] . '!important;color:' . $s['btn_color'] . '!important;border-radius:' . $s['btn_radius'] . 'px!important;box-shadow:none!important;text-shadow:none!important;padding:10px 20px!important;height:auto!important;font-size:14px!important;font-weight:600!important;width:100%!important;}';
		$css .= '#wp-submit:hover,.button-primary:hover{background:' . $s['btn_hover_bg'] . '!important;border-color:' . $s['btn_hover_bg'] . '!important;}';
		$css .= '#nav a,#backtoblog a{color:' . $s['link_color'] . ';font-size:13px;}';
		$css .= '.message{border-left:4px solid ' . $s['btn_bg'] . ';background:' . $s['form_bg'] . ';color:' . $s['label_color'] . ';border-radius:' . $s['form_radius'] . 'px;padding:12px 16px;font-size:13px;}';

		wp_add_inline_style( 'login', $css );
	}

	public function login_logo_url()   { return home_url(); }
	public function login_logo_title() { return get_bloginfo( 'name' ); }

	public function add_login_body_class( $classes ) {
		$login_page_id = (int) get_option( 'st_login_page_id', 0 );
		if ( $login_page_id && is_page( $login_page_id ) ) {
			$classes[] = 'st-login-page';
		}
		return $classes;
	}

	public function inject_login_logo() {
		$s = self::get_settings();
		$logo_src = '';
		if ( ! empty( $s['logo_url'] ) ) {
			$logo_src = esc_url( $s['logo_url'] );
		} else {
			$logo_id = get_theme_mod( 'custom_logo' );
			if ( $logo_id ) { $img = wp_get_attachment_image_src( $logo_id, 'full' ); if ( $img ) $logo_src = esc_url( $img[0] ); }
		}
		if ( ! $logo_src ) return;
		$logo_w = (int) ( $s['logo_width'] ?? 80 );
		$logo_h = (int) ( $s['logo_height'] ?? 80 );
		echo '<style>#login h1 a{background-image:url("' . $logo_src . '")!important;background-size:contain!important;background-repeat:no-repeat!important;background-position:center!important;width:' . $logo_w . 'px!important;height:' . $logo_h . 'px!important;display:block!important;}</style>';
	}

	public function ajax_login() {
		check_ajax_referer( 'st_ajax_login', 'nonce' );

		$log      = sanitize_user( wp_unslash( $_POST['log'] ?? '' ) );
		$pwd      = wp_unslash( $_POST['pwd'] ?? '' );
		$remember = ! empty( $_POST['rememberme'] );
		$redirect = esc_url_raw( wp_unslash( $_POST['redirect'] ?? home_url( '/' ) ) );
		if ( empty( $log ) || empty( $pwd ) ) {
			wp_send_json_error( __( 'Please enter username and password.', 'st-elementor' ) );
		}
		$user = wp_signon( array(
			'user_login'    => $log,
			'user_password' => $pwd,
			'remember'      => $remember,
		), is_ssl() );
		if ( is_wp_error( $user ) ) {
			wp_send_json_error( __( 'Invalid username or password.', 'st-elementor' ) );
		}
		wp_send_json_success( array( 'redirect' => $redirect ) );
	}
}
