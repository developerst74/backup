<?php
namespace ST_Elementor\Modules\Popup;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Popup {

	public function __construct() {
		add_action( 'init',               array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes',     array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_st_popup', array( $this, 'save_meta' ) );
		add_action( 'wp_footer',          array( $this, 'render_popups' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		// Elementor editor: inject Conditions button + panel
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor_assets' ) );
		// Inject preview styles into the preview iframe
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );
		// Add body class on popup preview
		add_filter( 'body_class', array( $this, 'add_preview_body_class' ) );
		// Save from editor AJAX
		add_action( 'wp_ajax_st_save_popup_settings', array( $this, 'ajax_save_popup_settings' ) );
		// Add controls to document settings tab
		add_action( 'elementor/documents/register_controls', array( $this, 'register_document_controls' ) );
		add_action( 'elementor/document/after_save',         array( $this, 'save_document_settings' ), 10, 2 );
	}

	public function add_preview_body_class( $classes ) {
		if ( is_singular( 'st_popup' ) ) {
			$classes[] = 'st-popup-preview';
		}
		return $classes;
	}

	/* ── Elementor Preview: show popup overlay + container ──────── */

	public function enqueue_preview_styles() {
		$post_id = get_the_ID();
		if ( ! $post_id ) return;
		$post = get_post( $post_id );
		if ( ! $post || 'st_popup' !== $post->post_type ) return;

		$m = $this->get_popup_meta( $post_id );

		$hex     = ltrim( $m['overlay_color'], '#' );
		$r       = hexdec( substr( $hex, 0, 2 ) );
		$g       = hexdec( substr( $hex, 2, 2 ) );
		$b       = hexdec( substr( $hex, 4, 2 ) );
		$overlay = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $m['overlay_opacity'] . ')';

		$width  = esc_attr( $m['width']    ?: '600px' );
		$radius = esc_attr( $m['radius']   ?: '12px' );
		$bg     = esc_attr( $m['bg_color'] ?: '#ffffff' );

		// Use body class .st-popup-preview instead of overriding .elementor globally
		$css = "
			/* ST Popup Preview — scoped to popup post type only */
			body.st-popup-preview {
				margin: 0 !important;
				padding: 30px !important;
				background: {$overlay} !important;
				min-height: 100vh;
				display: flex !important;
				align-items: center !important;
				justify-content: center !important;
				box-sizing: border-box;
			}
			body.st-popup-preview > .elementor {
				max-width: {$width} !important;
				width: 100% !important;
				border-radius: {$radius} !important;
				background: {$bg} !important;
				box-shadow: 0 24px 60px rgba(0,0,0,.3) !important;
				overflow: hidden;
			}
		";

		wp_add_inline_style( 'elementor-frontend', $css );

		// Add body class via inline script
		wp_add_inline_script( 'elementor-frontend',
			'document.body.classList.add("st-popup-preview");
			// Inject close button into preview
			document.addEventListener("DOMContentLoaded", function(){
				var show = ' . ( '1' === $m['show_close'] ? 'true' : 'false' ) . ';
				if(!show) return;
				var pos = "' . esc_js( $m['close_position'] ?? 'inside-top-right' ) . '";
				var size = ' . (int)($m['close_size'] ?? 32) . ';
				var iconSize = ' . (int)($m['close_icon_size'] ?? 16) . ';
				var bg = "' . esc_js( $m['close_bg'] ) . '";
				var color = "' . esc_js( $m['close_color'] ) . '";
				var radius = "' . esc_js( $m['close_radius'] ?? '50%' ) . '";
				var posStyle = "position:absolute;z-index:9999;";
				if(pos==="inside-top-right")  posStyle += "top:12px;right:12px;";
				if(pos==="inside-top-left")   posStyle += "top:12px;left:12px;";
				if(pos==="outside-top-right") posStyle += "top:-"+(size+8)+"px;right:0;";
				if(pos==="outside-top-left")  posStyle += "top:-"+(size+8)+"px;left:0;";
				var btn = document.createElement("button");
				btn.innerHTML = "&#x2715;";
				btn.setAttribute("style", posStyle+"width:"+size+"px;height:"+size+"px;font-size:"+iconSize+"px;background:"+bg+";color:"+color+";border-radius:"+radius+";border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;");
				btn.title = "Close (preview)";
				var wrap = document.querySelector(".elementor");
				if(wrap){ wrap.style.position="relative"; wrap.appendChild(btn); }
			});',
			'after'
		);
	}

	/* ── Elementor Editor: Conditions Button + Panel ────────────── */

	public function enqueue_editor_assets() {
		// Only on st_popup post type
		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
		if ( ! $post_id ) return;
		$post = get_post( $post_id );
		if ( ! $post || 'st_popup' !== $post->post_type ) return;

		$m         = $this->get_popup_meta( $post_id );
		$all_pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
		$page_opts = array( array( 'value' => '0', 'label' => __( '— Select Page —', 'st-elementor' ) ) );
		foreach ( $all_pages as $pg ) {
			$page_opts[] = array( 'value' => (string) $pg->ID, 'label' => $pg->post_title );
		}

		// Inject popup preview CSS into editor — shows overlay + container width
		$hex     = ltrim( $m['overlay_color'], '#' );
		$r       = hexdec( substr( $hex, 0, 2 ) );
		$g       = hexdec( substr( $hex, 2, 2 ) );
		$b       = hexdec( substr( $hex, 4, 2 ) );
		$overlay = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $m['overlay_opacity'] . ')';

		$preview_css = "
			/* ST Popup Editor Preview */
			#elementor-preview-iframe { background: {$overlay} !important; }
			.elementor-section-wrap,
			.e-con-inner {
				max-width: {$m['width']} !important;
				margin: 40px auto !important;
				border-radius: {$m['radius']} !important;
				background: {$m['bg_color']} !important;
				box-shadow: 0 24px 60px rgba(0,0,0,.25) !important;
				min-height: 100px;
			}
		";
		wp_add_inline_style( 'elementor-editor', $preview_css );

		wp_enqueue_style(
			'st-popup-editor',
			ST_ELEMENTOR_URL . 'assets/css/popup-editor.css',
			array(),
			ST_ELEMENTOR_VERSION
		);

		wp_enqueue_script(
			'st-popup-editor',
			ST_ELEMENTOR_URL . 'assets/js/popup-editor.js',
			array( 'jquery', 'elementor-editor' ),
			ST_ELEMENTOR_VERSION,
			true
		);

		wp_localize_script( 'st-popup-editor', 'stPopupEditor', array(
			'postId'    => $post_id,
			'nonce'     => wp_create_nonce( 'st_popup_editor_nonce' ),
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'pageOpts'  => $page_opts,
			'settings'  => $m,
			'triggers'  => $this->get_triggers(),
			'animations'=> $this->get_animations(),
			'i18n'      => array(
				'conditions'    => __( 'Conditions', 'st-elementor' ),
				'trigger'       => __( 'Trigger', 'st-elementor' ),
				'openOn'        => __( 'Open On', 'st-elementor' ),
				'delay'         => __( 'Delay (ms)', 'st-elementor' ),
				'scrollPct'     => __( 'Scroll %', 'st-elementor' ),
				'clickSelector' => __( 'Click Selector', 'st-elementor' ),
				'displayCond'   => __( 'Display Conditions', 'st-elementor' ),
				'showOn'        => __( 'Show On', 'st-elementor' ),
				'selectPage'    => __( 'Select Page', 'st-elementor' ),
				'design'        => __( 'Design', 'st-elementor' ),
				'width'         => __( 'Width', 'st-elementor' ),
				'maxHeight'     => __( 'Max Height', 'st-elementor' ),
				'radius'        => __( 'Border Radius', 'st-elementor' ),
				'bgColor'       => __( 'Background Color', 'st-elementor' ),
				'position'      => __( 'Position', 'st-elementor' ),
				'animation'     => __( 'Animation', 'st-elementor' ),
				'overlay'       => __( 'Overlay', 'st-elementor' ),
				'overlayColor'  => __( 'Overlay Color', 'st-elementor' ),
				'overlayOpacity'=> __( 'Opacity', 'st-elementor' ),
				'closeOnClick'  => __( 'Close on Overlay Click', 'st-elementor' ),
				'closeBtn'      => __( 'Close Button', 'st-elementor' ),
				'showClose'     => __( 'Show Close Button', 'st-elementor' ),
				'closeBg'       => __( 'Button Background', 'st-elementor' ),
				'closeColor'    => __( 'Button Icon Color', 'st-elementor' ),
				'behaviour'     => __( 'Behaviour', 'st-elementor' ),
				'onceSession'   => __( 'Once per Session', 'st-elementor' ),
				'status'        => __( 'Status', 'st-elementor' ),
				'active'        => __( 'Active', 'st-elementor' ),
				'inactive'      => __( 'Inactive', 'st-elementor' ),
				'save'          => __( 'Save Settings', 'st-elementor' ),
				'saved'         => __( 'Saved!', 'st-elementor' ),
				'cancel'        => __( 'Cancel', 'st-elementor' ),
				'entireSite'    => __( 'Entire Site', 'st-elementor' ),
				'frontPage'     => __( 'Front Page', 'st-elementor' ),
				'allPages'      => __( 'All Pages', 'st-elementor' ),
				'specificPage'  => __( 'Specific Page', 'st-elementor' ),
				'allSingles'    => __( 'All Singles', 'st-elementor' ),
				'allArchives'   => __( 'All Archives', 'st-elementor' ),
				'page404'       => __( '404 Page', 'st-elementor' ),
				'center'        => __( 'Center', 'st-elementor' ),
				'top'           => __( 'Top', 'st-elementor' ),
				'bottom'        => __( 'Bottom', 'st-elementor' ),
				'left'          => __( 'Left', 'st-elementor' ),
				'right'         => __( 'Right', 'st-elementor' ),
			),
		) );
	}

	public function ajax_save_popup_settings() {
		check_ajax_referer( 'st_popup_editor_nonce', 'nonce' );
		if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( 'Unauthorized' );

		$post_id = (int) ( $_POST['post_id'] ?? 0 );
		if ( ! $post_id ) wp_send_json_error( 'Invalid post ID' );

		$text_fields = array(
			'trigger', 'delay', 'scroll', 'click_selector', 'condition', 'page_id',
			'animation', 'width', 'height', 'radius', 'bg_color', 'position',
			'overlay_color', 'overlay_opacity', 'close_bg', 'close_color', 'status',
			'frequency', 'freq_limit', 'freq_hours',
		);

		foreach ( $text_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, '_st_popup_' . $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}

		foreach ( array( 'close_overlay', 'show_close', 'once' ) as $key ) {
			update_post_meta( $post_id, '_st_popup_' . $key, isset( $_POST[ $key ] ) && '1' === $_POST[ $key ] ? '1' : '0' );
		}

		wp_send_json_success( array( 'message' => __( 'Saved!', 'st-elementor' ) ) );
	}

	/* ── Elementor Document Controls ────────────────────────────── */

	public function register_document_controls( $document ) {
		// Only add to st_popup post type
		if ( ! $document instanceof \Elementor\Core\Base\Document ) return;
		$post = $document->get_post();
		if ( ! $post || 'st_popup' !== $post->post_type ) return;

		$m          = $this->get_popup_meta( $post->ID );
		$all_pages  = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
		$page_opts  = array( '0' => __( '— Select Page —', 'st-elementor' ) );
		foreach ( $all_pages as $pg ) { $page_opts[ $pg->ID ] = $pg->post_title; }

		$document->start_controls_section( 'st_popup_trigger_section', array(
			'label' => __( 'Popup Trigger', 'st-elementor' ),
			'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
		) );

		$document->add_control( 'st_popup_trigger', array(
			'label'   => __( 'Open On', 'st-elementor' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => $m['trigger'],
			'options' => $this->get_triggers(),
		) );

		$document->add_control( 'st_popup_delay', array(
			'label'     => __( 'Delay / Timer (ms)', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => (int) $m['delay'],
			'min'       => 0,
			'condition' => array( 'st_popup_trigger' => array( 'page_load', 'timer' ) ),
		) );

		$document->add_control( 'st_popup_scroll', array(
			'label'     => __( 'Scroll % to trigger', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::SLIDER,
			'default'   => array( 'size' => (int) $m['scroll'] ),
			'range'     => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
			'condition' => array( 'st_popup_trigger' => 'scroll' ),
		) );

		$document->add_control( 'st_popup_click_selector', array(
			'label'       => __( 'Click Selector', 'st-elementor' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => $m['click_selector'],
			'placeholder' => '.my-button, #open-popup',
			'description' => __( 'CSS selector of element that opens this popup.', 'st-elementor' ),
			'condition'   => array( 'st_popup_trigger' => 'click' ),
		) );

		$document->end_controls_section();

		// ── Conditions ──────────────────────────────────────────────
		$document->start_controls_section( 'st_popup_condition_section', array(
			'label' => __( 'Display Conditions', 'st-elementor' ),
			'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
		) );

		$document->add_control( 'st_popup_condition', array(
			'label'   => __( 'Show On', 'st-elementor' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => $m['condition'],
			'options' => array(
				'entire_site' => __( 'Entire Site', 'st-elementor' ),
				'front_page'  => __( 'Front Page', 'st-elementor' ),
				'all_pages'   => __( 'All Pages', 'st-elementor' ),
				'single_page' => __( 'Specific Page', 'st-elementor' ),
				'single'      => __( 'All Singles', 'st-elementor' ),
				'archive'     => __( 'All Archives', 'st-elementor' ),
				'404'         => __( '404 Page', 'st-elementor' ),
			),
		) );

		$document->add_control( 'st_popup_page_id', array(
			'label'     => __( 'Select Page', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'default'   => $m['page_id'],
			'options'   => $page_opts,
			'condition' => array( 'st_popup_condition' => 'single_page' ),
		) );

		$document->end_controls_section();

		// ── Design ──────────────────────────────────────────────────
		$document->start_controls_section( 'st_popup_design_section', array(
			'label' => __( 'Popup Design', 'st-elementor' ),
			'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
		) );

		$document->add_control( 'st_popup_width', array(
			'label'       => __( 'Width', 'st-elementor' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => $m['width'],
			'placeholder' => '600px',
		) );

		$document->add_control( 'st_popup_height', array(
			'label'       => __( 'Max Height', 'st-elementor' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => $m['height'],
			'placeholder' => 'auto',
		) );

		$document->add_control( 'st_popup_radius', array(
			'label'       => __( 'Border Radius', 'st-elementor' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => $m['radius'],
			'placeholder' => '12px',
		) );

		$document->add_control( 'st_popup_bg_color', array(
			'label'   => __( 'Background Color', 'st-elementor' ),
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => $m['bg_color'],
		) );

		$document->add_control( 'st_popup_position', array(
			'label'   => __( 'Position', 'st-elementor' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => $m['position'],
			'options' => array(
				'center' => __( 'Center', 'st-elementor' ),
				'top'    => __( 'Top', 'st-elementor' ),
				'bottom' => __( 'Bottom', 'st-elementor' ),
				'left'   => __( 'Left', 'st-elementor' ),
				'right'  => __( 'Right', 'st-elementor' ),
			),
		) );

		$document->add_control( 'st_popup_animation', array(
			'label'   => __( 'Open Animation', 'st-elementor' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => $m['animation'],
			'options' => $this->get_animations(),
		) );

		$document->end_controls_section();

		// ── Overlay ─────────────────────────────────────────────────
		$document->start_controls_section( 'st_popup_overlay_section', array(
			'label' => __( 'Overlay', 'st-elementor' ),
			'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
		) );

		$document->add_control( 'st_popup_overlay_color', array(
			'label'   => __( 'Overlay Color', 'st-elementor' ),
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => $m['overlay_color'],
		) );

		$document->add_control( 'st_popup_overlay_opacity', array(
			'label'   => __( 'Overlay Opacity', 'st-elementor' ),
			'type'    => \Elementor\Controls_Manager::SLIDER,
			'default' => array( 'size' => (float) $m['overlay_opacity'] ),
			'range'   => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
		) );

		$document->add_control( 'st_popup_close_overlay', array(
			'label'        => __( 'Close on Overlay Click', 'st-elementor' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'default'      => '1' === $m['close_overlay'] ? 'yes' : '',
			'return_value' => 'yes',
		) );

		$document->end_controls_section();

		// ── Close Button ─────────────────────────────────────────────
		$document->start_controls_section( 'st_popup_close_section', array(
			'label' => __( 'Close Button', 'st-elementor' ),
			'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
		) );

		$document->add_control( 'st_popup_show_close', array(
			'label'        => __( 'Show Close Button', 'st-elementor' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'default'      => '1' === $m['show_close'] ? 'yes' : '',
			'return_value' => 'yes',
		) );

		$document->add_control( 'st_popup_close_position', array(
			'label'     => __( 'Position', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'default'   => $m['close_position'] ?? 'inside-top-right',
			'options'   => array(
				'inside-top-right'  => __( 'Inside — Top Right', 'st-elementor' ),
				'inside-top-left'   => __( 'Inside — Top Left', 'st-elementor' ),
				'outside-top-right' => __( 'Outside — Top Right', 'st-elementor' ),
				'outside-top-left'  => __( 'Outside — Top Left', 'st-elementor' ),
			),
			'condition' => array( 'st_popup_show_close' => 'yes' ),
		) );

		$document->add_control( 'st_popup_close_size', array(
			'label'     => __( 'Button Size (px)', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => (int) ( $m['close_size'] ?? 32 ),
			'min'       => 20,
			'max'       => 80,
			'condition' => array( 'st_popup_show_close' => 'yes' ),
		) );

		$document->add_control( 'st_popup_close_icon_size', array(
			'label'     => __( 'Icon Size (px)', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => (int) ( $m['close_icon_size'] ?? 16 ),
			'min'       => 8,
			'max'       => 40,
			'condition' => array( 'st_popup_show_close' => 'yes' ),
		) );

		$document->add_control( 'st_popup_close_bg', array(
			'label'     => __( 'Button Background', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => $m['close_bg'],
			'condition' => array( 'st_popup_show_close' => 'yes' ),
		) );

		$document->add_control( 'st_popup_close_color', array(
			'label'     => __( 'Button Icon Color', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => $m['close_color'],
			'condition' => array( 'st_popup_show_close' => 'yes' ),
		) );

		$document->add_control( 'st_popup_close_radius', array(
			'label'     => __( 'Border Radius', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::TEXT,
			'default'   => $m['close_radius'] ?? '50%',
			'placeholder' => '50%',
			'condition' => array( 'st_popup_show_close' => 'yes' ),
		) );

		$document->end_controls_section();

		// ── Behaviour ────────────────────────────────────────────────
		$document->start_controls_section( 'st_popup_behaviour_section', array(
			'label' => __( 'Behaviour', 'st-elementor' ),
			'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
		) );

		$document->add_control( 'st_popup_frequency', array(
			'label'       => __( 'Show Frequency', 'st-elementor' ),
			'type'        => \Elementor\Controls_Manager::SELECT,
			'default'     => $m['frequency'],
			'description' => __( 'How often should this popup appear to the same visitor?', 'st-elementor' ),
			'options'     => array(
				'always'   => __( 'Always (every page load)', 'st-elementor' ),
				'session'  => __( 'Once per Session', 'st-elementor' ),
				'once_24h' => __( 'Once per 24 Hours', 'st-elementor' ),
				'x_times'  => __( 'Limit to X times total', 'st-elementor' ),
				'hours'    => __( 'Once every X hours', 'st-elementor' ),
			),
		) );

		$document->add_control( 'st_popup_freq_limit', array(
			'label'     => __( 'Max times to show', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => (int) $m['freq_limit'],
			'min'       => 1,
			'max'       => 100,
			'condition' => array( 'st_popup_frequency' => 'x_times' ),
		) );

		$document->add_control( 'st_popup_freq_hours', array(
			'label'       => __( 'Show again after (hours)', 'st-elementor' ),
			'type'        => \Elementor\Controls_Manager::NUMBER,
			'default'     => (int) $m['freq_hours'],
			'min'         => 1,
			'max'         => 720,
			'description' => __( 'e.g. 24 = once per day, 168 = once per week', 'st-elementor' ),
			'condition'   => array( 'st_popup_frequency' => 'hours' ),
		) );

		$document->add_control( 'st_popup_status', array(
			'label'     => __( 'Status', 'st-elementor' ),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'default'   => $m['status'],
			'separator' => 'before',
			'options'   => array(
				'active'   => __( 'Active', 'st-elementor' ),
				'inactive' => __( 'Inactive', 'st-elementor' ),
			),
		) );

		$document->end_controls_section();
	}

	/**
	 * When Elementor saves the document, sync settings to post meta.
	 */
	public function save_document_settings( $document, $data ) {
		$post = $document->get_post();
		if ( ! $post || 'st_popup' !== $post->post_type ) return;

		$settings = $document->get_settings();
		if ( empty( $settings ) ) return;

		$map = array(
			'st_popup_trigger'         => 'trigger',
			'st_popup_delay'           => 'delay',
			'st_popup_click_selector'  => 'click_selector',
			'st_popup_condition'       => 'condition',
			'st_popup_page_id'         => 'page_id',
			'st_popup_animation'       => 'animation',
			'st_popup_width'           => 'width',
			'st_popup_height'          => 'height',
			'st_popup_radius'          => 'radius',
			'st_popup_bg_color'        => 'bg_color',
			'st_popup_position'        => 'position',
			'st_popup_overlay_color'   => 'overlay_color',
			'st_popup_close_bg'        => 'close_bg',
			'st_popup_close_color'     => 'close_color',
			'st_popup_frequency'       => 'frequency',
			'st_popup_status'          => 'status',
		);

		foreach ( $map as $setting_key => $meta_key ) {
			if ( isset( $settings[ $setting_key ] ) ) {
				update_post_meta( $post->ID, '_st_popup_' . $meta_key, sanitize_text_field( $settings[ $setting_key ] ) );
			}
		}

		// Sliders
		if ( isset( $settings['st_popup_scroll']['size'] ) ) {
			update_post_meta( $post->ID, '_st_popup_scroll', (int) $settings['st_popup_scroll']['size'] );
		}
		if ( isset( $settings['st_popup_overlay_opacity']['size'] ) ) {
			update_post_meta( $post->ID, '_st_popup_overlay_opacity', (float) $settings['st_popup_overlay_opacity']['size'] );
		}

		// Number fields
		if ( isset( $settings['st_popup_freq_limit'] ) ) {
			update_post_meta( $post->ID, '_st_popup_freq_limit', (int) $settings['st_popup_freq_limit'] );
		}
		if ( isset( $settings['st_popup_freq_hours'] ) ) {
			update_post_meta( $post->ID, '_st_popup_freq_hours', (int) $settings['st_popup_freq_hours'] );
		}

		// Switchers
		$switchers = array(
			'st_popup_close_overlay' => 'close_overlay',
			'st_popup_show_close'    => 'show_close',
		);
		foreach ( $switchers as $setting_key => $meta_key ) {
			if ( isset( $settings[ $setting_key ] ) ) {
				update_post_meta( $post->ID, '_st_popup_' . $meta_key, 'yes' === $settings[ $setting_key ] ? '1' : '0' );
			}
		}
		// Sync once flag from frequency
		if ( isset( $settings['st_popup_frequency'] ) ) {
			update_post_meta( $post->ID, '_st_popup_once', 'session' === $settings['st_popup_frequency'] ? '1' : '0' );
		}
	}

	/* ── Post Type ───────────────────────────────────────────────── */

	public function register_post_type() {
		register_post_type( 'st_popup', array(
			'labels'             => array(
				'name'          => __( 'Popups', 'st-elementor' ),
				'singular_name' => __( 'Popup', 'st-elementor' ),
				'add_new_item'  => __( 'Add New Popup', 'st-elementor' ),
				'edit_item'     => __( 'Edit Popup', 'st-elementor' ),
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'rewrite'            => array( 'slug' => 'st-popup', 'with_front' => false ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'elementor' ),
			'show_in_rest'       => true,
		) );
	}

	/* ── Meta Box ────────────────────────────────────────────────── */

	public function add_meta_boxes() {
		add_meta_box( 'st_popup_settings', __( 'Popup Settings', 'st-elementor' ), array( $this, 'render_meta_box' ), 'st_popup', 'side', 'high' );
	}

	public function render_meta_box( $post ) {
		wp_nonce_field( 'st_popup_meta_nonce', 'st_popup_nonce' );
		$m = $this->get_popup_meta( $post->ID );

		$all_pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
		$pub_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $pub_types['attachment'], $pub_types['st_template'], $pub_types['st_popup'] );
		?>
		<style>
		.st-pm-section { margin: 10px 0 6px; padding: 6px 0 0; border-top: 1px solid #e5e7eb; font-size: 11px; font-weight: 700; color: #6366f1; text-transform: uppercase; letter-spacing: .5px; }
		.st-pm-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
		.st-pm-row label { font-size: 12px; color: #374151; font-weight: 600; flex-shrink: 0; margin-right: 8px; }
		.st-pm-row input, .st-pm-row select { width: 100%; font-size: 12px; padding: 4px 6px; border: 1px solid #d1d5db; border-radius: 4px; }
		.st-pm-row input[type=color] { height: 28px; padding: 2px; }
		.st-pm-row input[type=checkbox] { width: auto; }
		.st-pm-hint { font-size: 10px; color: #9ca3af; margin: -4px 0 6px; }
		</style>

		<!-- ── TRIGGER ── -->
		<div class="st-pm-section"><?php esc_html_e( 'Trigger', 'st-elementor' ); ?></div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Open On', 'st-elementor' ); ?></label>
			<select name="st_popup_trigger" id="st_popup_trigger" style="width:100%">
				<?php foreach ( $this->get_triggers() as $val => $label ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $m['trigger'], $val ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="st-pm-row" id="st-delay-row">
			<label><?php esc_html_e( 'Delay (ms)', 'st-elementor' ); ?></label>
			<input type="number" name="st_popup_delay" value="<?php echo esc_attr( $m['delay'] ); ?>" min="0">
		</div>

		<div class="st-pm-row" id="st-scroll-row" style="display:none">
			<label><?php esc_html_e( 'Scroll %', 'st-elementor' ); ?></label>
			<input type="number" name="st_popup_scroll" value="<?php echo esc_attr( $m['scroll'] ); ?>" min="0" max="100">
		</div>

		<div class="st-pm-row" id="st-click-row" style="display:none">
			<label><?php esc_html_e( 'Click Selector', 'st-elementor' ); ?></label>
			<input type="text" name="st_popup_click_selector" value="<?php echo esc_attr( $m['click_selector'] ); ?>" placeholder=".my-btn, #open-popup">
		</div>
		<p class="st-pm-hint" id="st-click-hint" style="display:none"><?php esc_html_e( 'CSS selector of element that opens this popup.', 'st-elementor' ); ?></p>

		<!-- ── DISPLAY CONDITIONS ── -->
		<div class="st-pm-section"><?php esc_html_e( 'Display Conditions', 'st-elementor' ); ?></div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Show On', 'st-elementor' ); ?></label>
			<select name="st_popup_condition" id="st_popup_condition" style="width:100%">
				<option value="entire_site" <?php selected( $m['condition'], 'entire_site' ); ?>><?php esc_html_e( 'Entire Site', 'st-elementor' ); ?></option>
				<option value="front_page"  <?php selected( $m['condition'], 'front_page' ); ?>><?php esc_html_e( 'Front Page', 'st-elementor' ); ?></option>
				<option value="all_pages"   <?php selected( $m['condition'], 'all_pages' ); ?>><?php esc_html_e( 'All Pages', 'st-elementor' ); ?></option>
				<option value="single_page" <?php selected( $m['condition'], 'single_page' ); ?>><?php esc_html_e( 'Specific Page', 'st-elementor' ); ?></option>
				<option value="single"      <?php selected( $m['condition'], 'single' ); ?>><?php esc_html_e( 'All Singles', 'st-elementor' ); ?></option>
				<option value="archive"     <?php selected( $m['condition'], 'archive' ); ?>><?php esc_html_e( 'All Archives', 'st-elementor' ); ?></option>
				<option value="404"         <?php selected( $m['condition'], '404' ); ?>><?php esc_html_e( '404 Page', 'st-elementor' ); ?></option>
			</select>
		</div>

		<div class="st-pm-row" id="st-page-row" style="<?php echo 'single_page' === $m['condition'] ? '' : 'display:none'; ?>">
			<label><?php esc_html_e( 'Page', 'st-elementor' ); ?></label>
			<select name="st_popup_page_id" style="width:100%">
				<option value="0"><?php esc_html_e( '— Select —', 'st-elementor' ); ?></option>
				<?php foreach ( $all_pages as $pg ) : ?>
				<option value="<?php echo esc_attr( $pg->ID ); ?>" <?php selected( (int)$m['page_id'], $pg->ID ); ?>><?php echo esc_html( $pg->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<!-- ── DESIGN ── -->
		<div class="st-pm-section"><?php esc_html_e( 'Design', 'st-elementor' ); ?></div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Width', 'st-elementor' ); ?></label>
			<input type="text" name="st_popup_width" value="<?php echo esc_attr( $m['width'] ); ?>" placeholder="600px">
		</div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Max Height', 'st-elementor' ); ?></label>
			<input type="text" name="st_popup_height" value="<?php echo esc_attr( $m['height'] ); ?>" placeholder="auto">
		</div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Border Radius', 'st-elementor' ); ?></label>
			<input type="text" name="st_popup_radius" value="<?php echo esc_attr( $m['radius'] ); ?>" placeholder="12px">
		</div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Popup BG Color', 'st-elementor' ); ?></label>
			<input type="color" name="st_popup_bg_color" value="<?php echo esc_attr( $m['bg_color'] ); ?>">
		</div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Position', 'st-elementor' ); ?></label>
			<select name="st_popup_position" style="width:100%">
				<option value="center" <?php selected( $m['position'], 'center' ); ?>><?php esc_html_e( 'Center', 'st-elementor' ); ?></option>
				<option value="top"    <?php selected( $m['position'], 'top' ); ?>><?php esc_html_e( 'Top', 'st-elementor' ); ?></option>
				<option value="bottom" <?php selected( $m['position'], 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'st-elementor' ); ?></option>
				<option value="left"   <?php selected( $m['position'], 'left' ); ?>><?php esc_html_e( 'Left', 'st-elementor' ); ?></option>
				<option value="right"  <?php selected( $m['position'], 'right' ); ?>><?php esc_html_e( 'Right', 'st-elementor' ); ?></option>
			</select>
		</div>

		<!-- ── ANIMATION ── -->
		<div class="st-pm-section"><?php esc_html_e( 'Animation', 'st-elementor' ); ?></div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Effect', 'st-elementor' ); ?></label>
			<select name="st_popup_animation" style="width:100%">
				<?php foreach ( $this->get_animations() as $val => $label ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $m['animation'], $val ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<!-- ── OVERLAY ── -->
		<div class="st-pm-section"><?php esc_html_e( 'Overlay', 'st-elementor' ); ?></div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Color', 'st-elementor' ); ?></label>
			<input type="color" name="st_popup_overlay_color" value="<?php echo esc_attr( $m['overlay_color'] ); ?>">
		</div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Opacity', 'st-elementor' ); ?></label>
			<input type="number" name="st_popup_overlay_opacity" value="<?php echo esc_attr( $m['overlay_opacity'] ); ?>" min="0" max="1" step="0.05">
		</div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Close on Click', 'st-elementor' ); ?></label>
			<input type="checkbox" name="st_popup_close_overlay" value="1" <?php checked( $m['close_overlay'], '1' ); ?>>
		</div>

		<!-- ── CLOSE BUTTON ── -->
		<div class="st-pm-section"><?php esc_html_e( 'Close Button', 'st-elementor' ); ?></div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Show', 'st-elementor' ); ?></label>
			<input type="checkbox" name="st_popup_show_close" value="1" <?php checked( $m['show_close'], '1' ); ?>>
		</div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Close BG', 'st-elementor' ); ?></label>
			<input type="color" name="st_popup_close_bg" value="<?php echo esc_attr( $m['close_bg'] ); ?>">
		</div>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Close Color', 'st-elementor' ); ?></label>
			<input type="color" name="st_popup_close_color" value="<?php echo esc_attr( $m['close_color'] ); ?>">
		</div>

		<!-- ── BEHAVIOUR ── -->
		<div class="st-pm-section"><?php esc_html_e( 'Behaviour', 'st-elementor' ); ?></div>

		<!-- Frequency -->
		<div class="st-pm-row">
			<label><?php esc_html_e( 'Show Frequency', 'st-elementor' ); ?></label>
			<select name="st_popup_frequency" id="st_popup_frequency" style="width:100%">
				<option value="always"    <?php selected( $m['frequency'], 'always' ); ?>><?php esc_html_e( 'Always', 'st-elementor' ); ?></option>
				<option value="once_24h"  <?php selected( $m['frequency'], 'once_24h' ); ?>><?php esc_html_e( 'Once per 24 Hours (close = skip)', 'st-elementor' ); ?></option>
				<option value="session"   <?php selected( $m['frequency'], 'session' ); ?>><?php esc_html_e( 'Once per Session', 'st-elementor' ); ?></option>
				<option value="x_times"   <?php selected( $m['frequency'], 'x_times' ); ?>><?php esc_html_e( 'X Times Total', 'st-elementor' ); ?></option>
				<option value="hours"     <?php selected( $m['frequency'], 'hours' ); ?>><?php esc_html_e( 'Once every X Hours', 'st-elementor' ); ?></option>
			</select>
		</div>

		<div class="st-pm-row" id="st-freq-limit-row" style="<?php echo 'x_times' === $m['frequency'] ? '' : 'display:none'; ?>">
			<label><?php esc_html_e( 'Max Times', 'st-elementor' ); ?></label>
			<input type="number" name="st_popup_freq_limit" value="<?php echo esc_attr( $m['freq_limit'] ); ?>" min="1" max="100">
		</div>

		<div class="st-pm-row" id="st-freq-hours-row" style="<?php echo 'hours' === $m['frequency'] ? '' : 'display:none'; ?>">
			<label><?php esc_html_e( 'Every (hours)', 'st-elementor' ); ?></label>
			<input type="number" name="st_popup_freq_hours" value="<?php echo esc_attr( $m['freq_hours'] ); ?>" min="1" max="720">
		</div>
		<p class="st-pm-hint" id="st-freq-hours-hint" style="<?php echo 'hours' === $m['frequency'] ? '' : 'display:none'; ?>"><?php esc_html_e( 'e.g. 24 = once per day', 'st-elementor' ); ?></p>

		<div class="st-pm-row">
			<label><?php esc_html_e( 'Status', 'st-elementor' ); ?></label>
			<select name="st_popup_status" style="width:100%">
				<option value="active"   <?php selected( $m['status'], 'active' ); ?>><?php esc_html_e( 'Active', 'st-elementor' ); ?></option>
				<option value="inactive" <?php selected( $m['status'], 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'st-elementor' ); ?></option>
			</select>
		</div>

		<script>
		(function($){
			function updateTriggerFields() {
				var t = $('#st_popup_trigger').val();
				$('#st-delay-row').toggle(t === 'page_load' || t === 'timer');
				$('#st-scroll-row').toggle(t === 'scroll');
				$('#st-click-row, #st-click-hint').toggle(t === 'click');
			}
			function updateCondFields() {
				var c = $('#st_popup_condition').val();
				$('#st-page-row').toggle(c === 'single_page');
			}
			function updateFreqFields() {
				var f = $('#st_popup_frequency').val();
				$('#st-freq-limit-row').toggle(f === 'x_times');
				$('#st-freq-hours-row, #st-freq-hours-hint').toggle(f === 'hours');
			}
			$('#st_popup_trigger').on('change', updateTriggerFields);
			$('#st_popup_condition').on('change', updateCondFields);
			$('#st_popup_frequency').on('change', updateFreqFields);
			updateTriggerFields();
			updateCondFields();
			updateFreqFields();
		})(jQuery);
		</script>
		<?php
	}

	public function save_meta( $post_id ) {
		if ( ! isset( $_POST['st_popup_nonce'] ) || ! wp_verify_nonce( $_POST['st_popup_nonce'], 'st_popup_meta_nonce' ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;

		$text_fields = array(
			'st_popup_trigger'         => 'trigger',
			'st_popup_delay'           => 'delay',
			'st_popup_scroll'          => 'scroll',
			'st_popup_click_selector'  => 'click_selector',
			'st_popup_condition'       => 'condition',
			'st_popup_page_id'         => 'page_id',
			'st_popup_animation'       => 'animation',
			'st_popup_width'           => 'width',
			'st_popup_height'          => 'height',
			'st_popup_radius'          => 'radius',
			'st_popup_bg_color'        => 'bg_color',
			'st_popup_position'        => 'position',
			'st_popup_overlay_color'   => 'overlay_color',
			'st_popup_overlay_opacity' => 'overlay_opacity',
			'st_popup_close_bg'        => 'close_bg',
			'st_popup_close_color'     => 'close_color',
			'st_popup_close_position'  => 'close_position',
			'st_popup_close_size'      => 'close_size',
			'st_popup_close_icon_size' => 'close_icon_size',
			'st_popup_close_radius'    => 'close_radius',
			'st_popup_frequency'       => 'frequency',
			'st_popup_freq_limit'      => 'freq_limit',
			'st_popup_freq_hours'      => 'freq_hours',
			'st_popup_status'          => 'status',
		);

		foreach ( $text_fields as $post_key => $meta_key ) {
			if ( isset( $_POST[ $post_key ] ) ) {
				update_post_meta( $post_id, '_st_popup_' . $meta_key, sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) ) );
			}
		}

		foreach ( array( 'close_overlay', 'show_close', 'once' ) as $key ) {
			update_post_meta( $post_id, '_st_popup_' . $key, isset( $_POST[ 'st_popup_' . $key ] ) ? '1' : '0' );
		}
	}

	/* ── Helpers ─────────────────────────────────────────────────── */

	public function get_popup_meta( $post_id ) {
		$g = function( $k, $d ) use ( $post_id ) {
			$v = get_post_meta( $post_id, '_st_popup_' . $k, true );
			return ( '' !== $v && null !== $v ) ? $v : $d;
		};
		return array(
			'trigger'         => $g( 'trigger',         'page_load' ),
			'delay'           => $g( 'delay',           '0' ),
			'scroll'          => $g( 'scroll',          '50' ),
			'click_selector'  => $g( 'click_selector',  '' ),
			'condition'       => $g( 'condition',       'entire_site' ),
			'page_id'         => $g( 'page_id',         '0' ),
			'animation'       => $g( 'animation',       'fadeIn' ),
			'width'           => $g( 'width',           '600px' ),
			'height'          => $g( 'height',          'auto' ),
			'radius'          => $g( 'radius',          '12px' ),
			'bg_color'        => $g( 'bg_color',        '#ffffff' ),
			'position'        => $g( 'position',        'center' ),
			'overlay_color'   => $g( 'overlay_color',   '#000000' ),
			'overlay_opacity' => $g( 'overlay_opacity', '0.6' ),
			'close_overlay'   => $g( 'close_overlay',   '1' ),
			'show_close'      => $g( 'show_close',      '1' ),
			'close_position'  => $g( 'close_position',  'inside-top-right' ),
			'close_size'      => $g( 'close_size',      '32' ),
			'close_icon_size' => $g( 'close_icon_size', '16' ),
			'close_bg'        => $g( 'close_bg',        '#374151' ),
			'close_color'     => $g( 'close_color',     '#ffffff' ),
			'close_radius'    => $g( 'close_radius',    '50%' ),
			// Frequency: always | session | x_times | hours
			'frequency'       => $g( 'frequency',       'always' ),
			'freq_limit'      => $g( 'freq_limit',      '1' ),
			'freq_hours'      => $g( 'freq_hours',      '24' ),
			'once'            => $g( 'once',            '0' ),
			'status'          => $g( 'status',          'active' ),
		);
	}

	public function get_triggers() {
		return array(
			'page_load' => __( 'On Page Load', 'st-elementor' ),
			'timer'     => __( 'After Timer (ms)', 'st-elementor' ),
			'scroll'    => __( 'On Scroll (%)', 'st-elementor' ),
			'exit'      => __( 'On Exit Intent', 'st-elementor' ),
			'click'     => __( 'On Click (selector)', 'st-elementor' ),
		);
	}

	public function get_animations() {
		return array(
			'fadeIn'    => __( 'Fade In', 'st-elementor' ),
			'slideDown' => __( 'Slide Down', 'st-elementor' ),
			'slideUp'   => __( 'Slide Up', 'st-elementor' ),
			'zoomIn'    => __( 'Zoom In', 'st-elementor' ),
			'bounceIn'  => __( 'Bounce In', 'st-elementor' ),
			'flipIn'    => __( 'Flip In', 'st-elementor' ),
		);
	}

	/* ── Condition check ─────────────────────────────────────────── */

	private function matches_condition( $m ) {
		$cond    = $m['condition'];
		$page_id = (int) $m['page_id'];

		switch ( $cond ) {
			case 'entire_site':  return true;
			case 'front_page':   return is_front_page();
			case 'all_pages':    return is_page();
			case 'single_page':  return $page_id ? is_page( $page_id ) : is_page();
			case 'single':       return is_singular();
			case 'archive':      return is_archive();
			case '404':          return is_404();
			default:             return true;
		}
	}

	/* ── Frontend ────────────────────────────────────────────────── */

	private function maybe_enqueue_elementor_post_css( $post_id ) {
		$post_id = (int) $post_id;
		if ( ! $post_id ) {
			return;
		}
		if ( ! class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			return;
		}

		$css_file = new \Elementor\Core\Files\CSS\Post( $post_id );
		if ( method_exists( $css_file, 'enqueue' ) ) {
			$css_file->enqueue();
		}
	}

	public function enqueue_assets() {
		$popups = $this->get_active_popups();
		if ( empty( $popups ) ) return;

		if ( class_exists( '\Elementor\Plugin' ) ) {
			$plugin = \Elementor\Plugin::instance();
			if ( isset( $plugin->frontend ) ) {
				if ( method_exists( $plugin->frontend, 'enqueue_styles' ) ) {
					$plugin->frontend->enqueue_styles();
				}
				if ( method_exists( $plugin->frontend, 'enqueue_scripts' ) ) {
					$plugin->frontend->enqueue_scripts();
				}
			}
		}

		// Never show popups in Elementor editor or preview
		if ( class_exists( '\Elementor\Plugin' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) return;
			if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) return;
		}

		// Never show popups when admin is previewing via "View Page" with elementor preview
		if ( isset( $_GET['elementor-preview'] ) ) return;

		// Filter by condition
		$matching = array_filter(
			$popups,
			function( $p ) {
				return $this->matches_condition( $this->get_popup_meta( $p->ID ) );
			}
		);
		if ( empty( $matching ) ) return;

		foreach ( $matching as $popup ) {
			$this->maybe_enqueue_elementor_post_css( $popup->ID );
		}

		wp_enqueue_style(  'st-popup-style',  ST_ELEMENTOR_URL . 'assets/css/popup.css',  array(), ST_ELEMENTOR_VERSION );
		wp_enqueue_script( 'st-popup-script', ST_ELEMENTOR_URL . 'assets/js/popup.js', array( 'jquery' ), ST_ELEMENTOR_VERSION, true );

		$data = array();
		foreach ( $matching as $popup ) {
			$m      = $this->get_popup_meta( $popup->ID );
			$data[] = array(
				'id'             => $popup->ID,
				'trigger'        => $m['trigger'],
				'delay'          => (int) $m['delay'],
				'scroll'         => (int) $m['scroll'],
				'clickSelector'  => $m['click_selector'],
				'animation'      => $m['animation'],
				'closeOverlay'   => $m['close_overlay'],
				'frequency'      => $m['frequency'],
				'freqLimit'      => (int) $m['freq_limit'],
				'freqHours'      => (int) $m['freq_hours'],
			);
		}

		wp_localize_script( 'st-popup-script', 'stPopups', $data );
	}

	public function render_popups() {
		$popups = $this->get_active_popups();
		if ( empty( $popups ) ) return;

		foreach ( $popups as $popup ) {
			$m = $this->get_popup_meta( $popup->ID );
			if ( ! $this->matches_condition( $m ) ) continue;

			$content = class_exists( '\Elementor\Plugin' )
				? \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $popup->ID, true )
				: apply_filters( 'the_content', $popup->post_content );

			$hex     = ltrim( $m['overlay_color'], '#' );
			$r       = hexdec( substr( $hex, 0, 2 ) );
			$g       = hexdec( substr( $hex, 2, 2 ) );
			$b       = hexdec( substr( $hex, 4, 2 ) );
			$overlay = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $m['overlay_opacity'] . ')';

			$pos_class = 'st-popup-pos-' . esc_attr( $m['position'] );
			?>
			<div id="st-popup-<?php echo esc_attr( $popup->ID ); ?>"
			     class="st-popup-overlay <?php echo esc_attr( $pos_class ); ?>"
			     style="background:<?php echo esc_attr( $overlay ); ?>;display:none"
			     data-animation="<?php echo esc_attr( $m['animation'] ); ?>"
			     data-close-overlay="<?php echo esc_attr( $m['close_overlay'] ); ?>"
			     data-popup-id="<?php echo esc_attr( $popup->ID ); ?>"
			     role="dialog" aria-modal="true"
			     aria-label="<?php echo esc_attr( $popup->post_title ); ?>">
				<div class="st-popup-container"
				     style="max-width:<?php echo esc_attr( $m['width'] ); ?>;max-height:<?php echo esc_attr( $m['height'] ); ?>;border-radius:<?php echo esc_attr( $m['radius'] ); ?>;background:<?php echo esc_attr( $m['bg_color'] ); ?>">
					<?php if ( '1' === $m['show_close'] ) : ?>
					<button class="st-popup-close"
					        style="background:<?php echo esc_attr( $m['close_bg'] ); ?>;color:<?php echo esc_attr( $m['close_color'] ); ?>"
					        aria-label="<?php esc_attr_e( 'Close', 'st-elementor' ); ?>">&#x2715;</button>
					<?php endif; ?>
					<div class="st-popup-content"><?php echo $content; // phpcs:ignore ?></div>
				</div>
			</div>
			<?php
		}
	}

	private function get_active_popups() {
		return get_posts( array(
			'post_type'      => 'st_popup',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array( array( 'key' => '_st_popup_status', 'value' => 'active' ) ),
		) );
	}
}
