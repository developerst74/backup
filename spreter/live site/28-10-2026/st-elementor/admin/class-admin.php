<?php
namespace ST_Elementor\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin {
	public function __construct() {
		add_action( 'admin_menu',            array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_post_st_create_template',       array( $this, 'handle_create_template' ) );
		add_action( 'wp_ajax_st_save_template_conditions', array( $this, 'ajax_save_conditions' ) );
	}

	public function ajax_save_conditions() {
		if ( ! check_ajax_referer( 'st_save_conditions', 'nonce', false ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		$post_id   = (int) ( $_POST['post_id'] ?? 0 );
		$cond_data = (array) ( $_POST['cond_data'] ?? array() );

		if ( ! $post_id ) {
			wp_send_json_error( 'Invalid post ID' );
		}

		$condition  = sanitize_key( $cond_data['condition'] ?? 'entire_site' );
		$sub        = sanitize_key( $cond_data['sub'] ?? '' );
		$post_type  = sanitize_key( $cond_data['post_type'] ?? '' );
		$page_id    = (int) ( $cond_data['page_id'] ?? 0 );

		$clean = array(
			'condition' => $condition,
			'sub'       => $sub,
			'post_type' => $post_type,
			'page_id'   => $page_id,
		);

		// Save to template meta
		update_post_meta( $post_id, '_st_display_conditions', $clean );

		// ── Sync to st_hf_rules if this is a header/footer template ──
		$tpl_type = get_post_meta( $post_id, '_st_template_type', true );
		if ( in_array( $tpl_type, array( 'header', 'footer' ), true ) ) {
			$rules = get_option( 'st_hf_rules', array() );

			// Remove existing rule for this template
			$rules = array_values(
				array_filter(
					$rules,
					function( $r ) use ( $post_id ) {
						return (int) ( $r['template_id'] ?? 0 ) !== $post_id;
					}
				)
			);

			// Add updated rule
			$rules[] = array(
				'role'        => $tpl_type,
				'template_id' => $post_id,
				'condition'   => $condition,
				'sub'         => $sub,
				'post_type'   => $post_type,
				'page_id'     => $page_id,
			);

			update_option( 'st_hf_rules', $rules );
		}

		// Build human-readable label
		$label = 'Entire Site';
		if ( 'all_website' === $condition ) {
			$sub_labels = array(
				'all_pages'  => 'All Pages',
				'archive'    => 'Archive',
				'single'     => 'Single',
				'singular'   => 'Singular',
				'search'     => 'Search',
				'404'        => '404',
				'logged_in'  => 'Logged In',
				'logged_out' => 'Logged Out',
			);
			$label = $sub_labels[ $sub ] ?? 'All Website';

			if ( $post_type ) {
				$pt_obj = get_post_type_object( $post_type );
				$label .= ' — ' . ( $pt_obj ? $pt_obj->label : $post_type );
			}
			if ( $page_id ) {
				$pg = get_post( $page_id );
				if ( $pg ) $label .= ' — ' . $pg->post_title;
			}
		}

		wp_send_json_success( array( 'label' => $label ) );
	}

	/**
	 * Processes the "Create Template" modal form and redirects to Elementor editor.
	 * Must run via admin_post_ so headers are not yet sent.
	 */
	public function handle_create_template() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}

		if ( ! isset( $_POST['st_create_template_nonce'] ) || ! wp_verify_nonce( $_POST['st_create_template_nonce'], 'st_create_template' ) ) {
			wp_die( 'Invalid nonce' );
		}

		$title     = sanitize_text_field( wp_unslash( $_POST['tpl_title'] ?? '' ) );
		$type      = sanitize_key( wp_unslash( $_POST['tpl_type'] ?? 'template' ) );
		$post_type = ( 'popup' === $type ) ? 'st_popup' : 'st_template';

		$cond_l1  = sanitize_key( wp_unslash( $_POST['tpl_condition_l1'] ?? 'entire_site' ) );
		$cond_l2  = sanitize_key( wp_unslash( $_POST['tpl_condition_l2'] ?? '' ) );
		$page_id  = (int) ( $_POST['tpl_condition_page_id'] ?? 0 );
		$cond_meta = array(
			'condition' => $cond_l1,
			'sub'       => ( 'all_website' === $cond_l1 ) ? $cond_l2 : '',
			'post_type' => '',
			'page_id'   => ( 'all_website' === $cond_l1 && 'singular' === $cond_l2 ) ? $page_id : 0,
		);

		if ( empty( $title ) ) {
			$title = ucfirst( $type ) . ' Template';
		}

		$post_id = wp_insert_post( array(
			'post_title'  => $title,
			'post_type'   => $post_type,
			'post_status' => 'draft',
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_st_template_type', $type );
			update_post_meta( $post_id, '_st_display_conditions', $cond_meta );

			// For popup — set default meta so preview shows correctly in Elementor
			if ( 'popup' === $type ) {
				$popup_condition = 'entire_site';
				$popup_page_id   = 0;
				if ( 'all_website' === $cond_meta['condition'] ) {
					if ( 'all_pages' === $cond_meta['sub'] ) {
						$popup_condition = 'all_pages';
					} elseif ( 'singular' === $cond_meta['sub'] && ! empty( $cond_meta['page_id'] ) ) {
						$popup_condition = 'single_page';
						$popup_page_id   = (int) $cond_meta['page_id'];
					} elseif ( 'archive' === $cond_meta['sub'] ) {
						$popup_condition = 'archive';
					} elseif ( 'single' === $cond_meta['sub'] ) {
						$popup_condition = 'single';
					} elseif ( '404' === $cond_meta['sub'] ) {
						$popup_condition = '404';
					}
				}

				update_post_meta( $post_id, '_st_popup_trigger',         'page_load' );
				update_post_meta( $post_id, '_st_popup_delay',           '0' );
				update_post_meta( $post_id, '_st_popup_condition',       $popup_condition );
				update_post_meta( $post_id, '_st_popup_page_id',         (string) $popup_page_id );
				update_post_meta( $post_id, '_st_popup_animation',       'fadeIn' );
				update_post_meta( $post_id, '_st_popup_width',           '600px' );
				update_post_meta( $post_id, '_st_popup_height',          'auto' );
				update_post_meta( $post_id, '_st_popup_radius',          '12px' );
				update_post_meta( $post_id, '_st_popup_bg_color',        '#ffffff' );
				update_post_meta( $post_id, '_st_popup_position',        'center' );
				update_post_meta( $post_id, '_st_popup_overlay_color',   '#000000' );
				update_post_meta( $post_id, '_st_popup_overlay_opacity', '0.6' );
				update_post_meta( $post_id, '_st_popup_close_overlay',   '1' );
				update_post_meta( $post_id, '_st_popup_show_close',      '1' );
				update_post_meta( $post_id, '_st_popup_close_bg',        '#374151' );
				update_post_meta( $post_id, '_st_popup_close_color',     '#ffffff' );
				update_post_meta( $post_id, '_st_popup_frequency',       'always' );
				update_post_meta( $post_id, '_st_popup_status',          'active' );
			}

			// Redirect directly to Elementor editor
			wp_redirect( add_query_arg( array( 'post' => $post_id, 'action' => 'elementor' ), admin_url( 'post.php' ) ) );
			exit;
		}

		// On failure, go back to templates page
		wp_redirect( add_query_arg( array( 'page' => 'st-elementor-templates', 'error' => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public function enqueue_admin_assets( $hook ) {
		if ( strpos( $hook, 'st-elementor' ) !== false ) {
			wp_enqueue_style(
				'st-elementor-admin-style',
				ST_ELEMENTOR_URL . 'assets/css/admin.css',
				array(),
				ST_ELEMENTOR_VERSION
			);
			// Full width override
			wp_add_inline_style( 'st-elementor-admin-style', '
				.st-elementor-admin-wrap,
				.wrap.st-dashboard-wrap,
				.wrap.st-templates-page,
				.wrap.st-widgets-wrap,
				.wrap.st-hf-page {
					max-width: 100% !important;
					margin-right: 0 !important;
				}
			' );
		}
	}

	public function add_admin_menu() {
		$parent = 'st-elementor';

		// ── Main menu ────────────────────────────────────────────────
		add_menu_page(
			__( 'ST Elementor', 'st-elementor' ),
			__( 'ST Elementor', 'st-elementor' ),
			'manage_options',
			$parent,
			array( $this, 'dashboard_page' ),
			'dashicons-layout',
			100
		);

		// ── Submenus ─────────────────────────────────────────────────
		add_submenu_page( $parent, __( 'Dashboard', 'st-elementor' ),    __( 'Dashboard', 'st-elementor' ),    'manage_options', $parent,                    array( $this, 'dashboard_page' ) );
		add_submenu_page( $parent, __( 'Templates', 'st-elementor' ),    __( 'Templates', 'st-elementor' ),    'manage_options', 'st-elementor-templates',    array( $this, 'templates_page' ) );
		add_submenu_page( $parent, __( 'Header & Footer', 'st-elementor' ), __( 'Header & Footer', 'st-elementor' ), 'manage_options', 'st-elementor-hf',    array( $this, 'hf_page' ) );
		add_submenu_page( $parent, __( 'Popups', 'st-elementor' ),       __( 'Popups', 'st-elementor' ),       'manage_options', 'edit.php?post_type=st_popup' );
		add_submenu_page( $parent, __( 'Login Form', 'st-elementor' ),   __( 'Login Form', 'st-elementor' ),   'manage_options', 'st-elementor-login',        array( $this, 'login_page' ) );
		add_submenu_page( $parent, __( 'Widgets', 'st-elementor' ),      __( 'Widgets', 'st-elementor' ),      'manage_options', 'st-elementor-widgets',      array( $this, 'widgets_page' ) );
	}

	public function dashboard_page()  { require_once ST_ELEMENTOR_PATH . 'admin/views/dashboard.php'; }
	public function templates_page()  { require_once ST_ELEMENTOR_PATH . 'admin/views/templates.php'; }
	public function hf_page()         { require_once ST_ELEMENTOR_PATH . 'admin/views/header-footer.php'; }
	public function popups_page()     { require_once ST_ELEMENTOR_PATH . 'admin/views/popups.php'; }
	public function login_page()      { require_once ST_ELEMENTOR_PATH . 'admin/views/login-form.php'; }
	public function widgets_page()    { require_once ST_ELEMENTOR_PATH . 'admin/views/widgets.php'; }
}
