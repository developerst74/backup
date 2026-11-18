<?php
namespace ST_Elementor\Modules\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Templates {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_shortcode( 'st_template', array( $this, 'shortcode' ) );
		add_action( 'wp', array( $this, 'setup_header_footer' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_header_footer_assets' ), 9 );
		// Show header/footer in Elementor editor preview
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'setup_header_footer' ) );
		// Add display conditions meta box in editor
		add_action( 'add_meta_boxes', array( $this, 'add_conditions_meta_box' ) );
		add_action( 'save_post_st_template', array( $this, 'save_conditions_meta' ) );
	}

	/* ── Meta box for display conditions ─────────────────────────── */

	public function add_conditions_meta_box() {
		add_meta_box(
			'st_template_conditions',
			__( 'Display Conditions', 'st-elementor' ),
			array( $this, 'render_conditions_meta_box' ),
			'st_template',
			'side',
			'high'
		);
	}

	public function render_conditions_meta_box( $post ) {
		wp_nonce_field( 'st_template_conditions_nonce', 'st_template_conditions_nonce' );

		$saved_conds = (array) get_post_meta( $post->ID, '_st_display_conditions', true );
		$saved_types = (array) get_post_meta( $post->ID, '_st_post_types', true );
		$saved_page  = (int) get_post_meta( $post->ID, '_st_specific_page_id', true );

		$all_post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $all_post_types['attachment'] );

		$all_pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC' ) );

		$cond_opts = array(
			'entire_site' => 'Entire Site',
			'front_page'  => 'Front Page',
			'all_pages'   => 'All Pages',
			'single_page' => 'Specific Page',
			'single'      => 'All Singles',
			'archive'     => 'All Archives',
			'category'    => 'Category',
			'tag'         => 'Tag',
			'search'      => 'Search',
			'404'         => '404',
		);
		?>
		<div style="font-size:12px">
			<p style="margin:0 0 10px;color:#6b7280"><?php esc_html_e( 'Where should this template appear?', 'st-elementor' ); ?></p>

			<?php foreach ( $cond_opts as $val => $lbl ) : ?>
			<label style="display:block;margin-bottom:6px;cursor:pointer">
				<input type="checkbox" name="st_display_conditions[]" value="<?php echo esc_attr( $val ); ?>" <?php checked( in_array( $val, $saved_conds, true ) ); ?>>
				<?php echo esc_html( $lbl ); ?>
			</label>
			<?php endforeach; ?>

			<div style="margin-top:12px;padding-top:12px;border-top:1px solid #e5e7eb">
				<label style="display:block;font-weight:600;margin-bottom:6px"><?php esc_html_e( 'Post Types (for Single/Archive)', 'st-elementor' ); ?></label>
				<?php foreach ( $all_post_types as $pt ) : ?>
				<label style="display:block;margin-bottom:4px;cursor:pointer">
					<input type="checkbox" name="st_post_types[]" value="<?php echo esc_attr( $pt->name ); ?>" <?php checked( in_array( $pt->name, $saved_types, true ) ); ?>>
					<?php echo esc_html( $pt->label ); ?>
				</label>
				<?php endforeach; ?>
			</div>

			<div style="margin-top:12px;padding-top:12px;border-top:1px solid #e5e7eb">
				<label style="display:block;font-weight:600;margin-bottom:6px"><?php esc_html_e( 'Specific Page', 'st-elementor' ); ?></label>
				<select name="st_specific_page_id" style="width:100%;padding:4px 6px;font-size:12px">
					<option value="0"><?php esc_html_e( '— Select Page —', 'st-elementor' ); ?></option>
					<?php foreach ( $all_pages as $pg ) : ?>
					<option value="<?php echo esc_attr( $pg->ID ); ?>" <?php selected( $saved_page, $pg->ID ); ?>>
						<?php echo esc_html( $pg->post_title ); ?>
					</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<?php
	}

	public function save_conditions_meta( $post_id ) {
		if ( ! isset( $_POST['st_template_conditions_nonce'] ) || ! wp_verify_nonce( $_POST['st_template_conditions_nonce'], 'st_template_conditions_nonce' ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;

		$conditions = array_map( 'sanitize_text_field', (array) ( $_POST['st_display_conditions'] ?? array() ) );
		$post_types = array_map( 'sanitize_key', (array) ( $_POST['st_post_types'] ?? array() ) );
		$page_id    = (int) ( $_POST['st_specific_page_id'] ?? 0 );

		update_post_meta( $post_id, '_st_display_conditions', $conditions );
		update_post_meta( $post_id, '_st_post_types', $post_types );
		update_post_meta( $post_id, '_st_specific_page_id', $page_id );
	}

	/* ── Post Type ───────────────────────────────────────────────── */

	public function register_post_type() {
		register_post_type( 'st_template', array(
			'labels'             => array(
				'name'          => _x( 'Templates', 'post type general name', 'st-elementor' ),
				'singular_name' => _x( 'Template', 'post type singular name', 'st-elementor' ),
				'add_new_item'  => __( 'Add New Template', 'st-elementor' ),
				'edit_item'     => __( 'Edit Template', 'st-elementor' ),
				'not_found'     => __( 'No templates found', 'st-elementor' ),
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'st-template', 'with_front' => false ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'elementor' ),
			'show_in_rest'       => true,
		) );
	}

	/* ── Shortcode ───────────────────────────────────────────────── */

	public function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'st_template' );
		$id   = (int) $atts['id'];
		if ( ! $id ) return '';
		return $this->render_template( $id );
	}

	/* ── Condition checker ───────────────────────────────────────── */

	/**
	 * Check if current page matches a set of display conditions.
	 * Conditions are stored as an array of strings like:
	 *   'entire_site', 'all_pages', 'front_page', 'single',
	 *   'single_page', 'archive', 'category', 'tag',
	 *   'search', '404', 'logged_in', 'logged_out',
	 *   'post_id:123'  (specific post/page by ID)
	 */
	public function matches_conditions( array $conditions, array $rule = array() ) {
		// New format: single condition + sub + post_type + page_id
		$cond      = $rule['condition'] ?? ( $conditions[0] ?? 'entire_site' );
		$sub       = $rule['sub']       ?? '';
		$post_type = $rule['post_type'] ?? '';
		$page_id   = (int) ( $rule['page_id'] ?? 0 );

		switch ( $cond ) {
			case 'entire_site':
				return true;

			case 'all_website':
				switch ( $sub ) {
					case 'all_pages':
						return is_page();

					case 'archive':
						if ( ! is_archive() ) return false;
						if ( $post_type ) {
							// Check if current archive is for this post type
							if ( is_post_type_archive( $post_type ) ) return true;
							$obj = get_queried_object();
							if ( $obj instanceof \WP_Term ) {
								// Check if this taxonomy belongs to the post type
								$tax = get_taxonomy( $obj->taxonomy );
								if ( $tax && in_array( $post_type, (array) $tax->object_type, true ) ) return true;
							}
							return false;
						}
						return true;

					case 'single':
						if ( ! is_singular() ) return false;
						if ( $post_type === 'single_post' ) return is_single();
						if ( $post_type === 'single_page' ) return is_page();
						if ( $post_type ) return is_singular( $post_type );
						return true;

					case 'singular':
						// Specific page by ID
						if ( $page_id ) {
							return ( is_singular() && (int) get_the_ID() === $page_id )
								|| is_page( $page_id );
						}
						// No specific page — match all singulars
						return is_singular();

					case 'search':
						return is_search();

					case '404':
						return is_404();

					case 'logged_in':
						return is_user_logged_in();

					case 'logged_out':
						return ! is_user_logged_in();

					default:
						return true;
				}
		}

		// Legacy array-based conditions fallback
		foreach ( $conditions as $c ) {
			$c = trim( $c );
			switch ( $c ) {
				case 'entire_site':  return true;
				case 'front_page':   if ( is_front_page() ) return true; break;
				case 'all_pages':    if ( is_page() ) return true; break;
				case 'single_page':  if ( is_page( $page_id ?: null ) ) return true; break;
				case 'single':       if ( is_singular() ) return true; break;
				case 'archive':      if ( is_archive() ) return true; break;
				case 'category':     if ( is_category() ) return true; break;
				case 'tag':          if ( is_tag() ) return true; break;
				case 'search':       if ( is_search() ) return true; break;
				case '404':          if ( is_404() ) return true; break;
				case 'logged_in':    if ( is_user_logged_in() ) return true; break;
				case 'logged_out':   if ( ! is_user_logged_in() ) return true; break;
			}
		}

		return false;
	}

	/* ── Find matching header/footer template ────────────────────── */

	/**
	 * Get the template ID for a given role ('header' or 'footer')
	 * by checking all saved rules in priority order.
	 *
	 * Rules are stored as:
	 *   st_hf_rules = [
	 *     [ 'role' => 'header', 'template_id' => 5, 'conditions' => ['entire_site'] ],
	 *     [ 'role' => 'header', 'template_id' => 8, 'conditions' => ['single'] ],
	 *     ...
	 *   ]
	 * More specific rules (lower index = higher priority) win first.
	 */
	public function get_active_template( $role ) {
		$rules = get_option( 'st_hf_rules', array() );
		if ( empty( $rules ) ) return 0;

		$fallback = 0;

		foreach ( $rules as $rule ) {
			if ( empty( $rule['role'] ) || $rule['role'] !== $role ) continue;
			if ( empty( $rule['template_id'] ) ) continue;

			// New format uses 'condition' key
			$cond = $rule['condition'] ?? '';

			// Legacy format uses 'conditions' array
			if ( ! $cond && ! empty( $rule['conditions'] ) ) {
				$conds = (array) $rule['conditions'];
				if ( in_array( 'entire_site', $conds, true ) && count( $conds ) === 1 ) {
					if ( ! $fallback ) $fallback = (int) $rule['template_id'];
					continue;
				}
				if ( $this->matches_conditions( $conds, $rule ) ) {
					return (int) $rule['template_id'];
				}
				continue;
			}

			// New format
			if ( 'entire_site' === $cond || '' === $cond ) {
				if ( ! $fallback ) $fallback = (int) $rule['template_id'];
				continue;
			}

			if ( $this->matches_conditions( array(), $rule ) ) {
				return (int) $rule['template_id'];
			}
		}

		return $fallback;
	}

	/* ── Frontend injection ──────────────────────────────────────── */

	private function is_elementor_request() {
		if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['elementor_library'] ) ) {
			return true;
		}

		if ( class_exists( '\Elementor\Plugin' ) ) {
			$plugin = \Elementor\Plugin::$instance;

			if ( isset( $plugin->preview ) && method_exists( $plugin->preview, 'is_preview_mode' ) && $plugin->preview->is_preview_mode() ) {
				return true;
			}
			if ( isset( $plugin->editor ) && method_exists( $plugin->editor, 'is_edit_mode' ) && $plugin->editor->is_edit_mode() ) {
				return true;
			}
		}

		return false;
	}

	private function maybe_enqueue_elementor_frontend_assets() {
		if ( is_admin() ) {
			return;
		}
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

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

	public function enqueue_header_footer_assets() {
		if ( is_admin() ) {
			return;
		}
		if ( $this->is_elementor_request() ) {
			return;
		}
		if ( is_singular( array( 'st_popup', 'st_template' ) ) ) {
			return;
		}

		$header_id = $this->get_active_template( 'header' );
		$footer_id = $this->get_active_template( 'footer' );
		if ( ! $header_id && ! $footer_id ) {
			return;
		}

		$this->maybe_enqueue_elementor_frontend_assets();
		$this->maybe_enqueue_elementor_post_css( $header_id );
		$this->maybe_enqueue_elementor_post_css( $footer_id );
	}

	public function setup_header_footer() {
		if ( is_admin() ) return;

		// Don't inject header/footer on st_popup or st_template post type pages
		if ( is_singular( array( 'st_popup', 'st_template' ) ) ) return;

		if ( $this->is_elementor_request() ) return;

		$header_id = $this->get_active_template( 'header' );
		$footer_id = $this->get_active_template( 'footer' );

		if ( $header_id || $footer_id ) {
			$this->maybe_enqueue_elementor_frontend_assets();
		}

		if ( $header_id ) {
			add_action( 'wp_body_open', array( $this, 'render_header' ), 1 );
			add_action( 'template_redirect', array( $this, 'start_header_buffer' ) );
			add_action( 'wp_head', array( $this, 'hide_theme_header_css' ), 99 );
		}

		if ( $footer_id ) {
			add_action( 'wp_footer', array( $this, 'render_footer' ), 1 );
			add_action( 'wp_head', array( $this, 'hide_theme_footer_css' ), 99 );
		}
	}

	public function start_header_buffer() {
		if ( $this->is_elementor_request() ) return;
		if ( is_singular( array( 'st_popup', 'st_template' ) ) ) return;
		ob_start( array( $this, 'inject_header_into_body' ) );
	}

	public function inject_header_into_body( $html ) {
		if ( did_action( 'wp_body_open' ) ) return $html;
		if ( $this->is_elementor_request() ) return $html;

		// Don't inject on popup/template singular pages
		if ( is_singular( array( 'st_popup', 'st_template' ) ) ) return $html;

		$id = $this->get_active_template( 'header' );
		if ( ! $id ) return $html;
		$header_html = '<div class="st-custom-header" id="st-site-header">' . $this->render_template( $id ) . '</div>';
		return preg_replace( '/(<body[^>]*>)/i', '$1' . $header_html, $html, 1 );
	}

	public function render_header() {
		$id = $this->get_active_template( 'header' );
		if ( ! $id ) return;
		echo '<div class="st-custom-header" id="st-site-header">' . $this->render_template( $id ) . '</div>'; // phpcs:ignore
	}

	public function render_footer() {
		$id = $this->get_active_template( 'footer' );
		if ( ! $id ) return;
		echo '<div class="st-custom-footer" id="st-site-footer">' . $this->render_template( $id ) . '</div>'; // phpcs:ignore
	}

	public function hide_theme_header_css() {
		$custom    = get_option( 'st_header_hide_selector', '' );
		$selectors = 'header.site-header,#masthead,#site-header,.site-header,#header,.header,nav.navbar,.navbar';
		if ( ! empty( $custom ) ) $selectors .= ',' . $custom;
		echo '<style id="st-hide-theme-header">' . esc_html( $selectors ) . '{display:none!important}.st-custom-header{position:relative;z-index:1000;width:100%}</style>'; // phpcs:ignore
	}

	public function hide_theme_footer_css() {
		$custom    = get_option( 'st_footer_hide_selector', '' );
		$selectors = 'footer.site-footer,#colophon,#site-footer,.site-footer,#footer,.footer';
		if ( ! empty( $custom ) ) $selectors .= ',' . $custom;
		echo '<style id="st-hide-theme-footer">' . esc_html( $selectors ) . '{display:none!important}.st-custom-footer{position:relative;z-index:100;width:100%}</style>'; // phpcs:ignore
	}

	/* ── Render helper ───────────────────────────────────────────── */

	public function render_template( $id ) {
		if ( ! $id ) return '';
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$this->maybe_enqueue_elementor_frontend_assets();
			$content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id, true );
			if ( ! empty( $content ) ) return $content;
		}
		$post = get_post( $id );
		return $post ? apply_filters( 'the_content', $post->post_content ) : '';
	}
}
