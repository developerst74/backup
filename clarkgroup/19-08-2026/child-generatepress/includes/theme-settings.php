<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================================
 * 1. REGISTER MENU LOCATIONS
 * ========================================================================= */
function cg_register_theme_menus() {
	register_nav_menus( array(
		'primary_menu'      => __( 'Primary Menu (Header)', 'child-generatepress' ),
		'footer_main_menu'  => __( 'Footer - Main Menu', 'child-generatepress' ),
		'footer_projects'   => __( 'Footer - Projects', 'child-generatepress' ),
		'footer_other_links'=> __( 'Footer - Other Links', 'child-generatepress' ),
	) );
}
add_action( 'after_setup_theme', 'cg_register_theme_menus' );

/* =========================================================================
 * 2. ACF OPTIONS PAGE - Theme Settings with Header + Footer sub pages
 * ========================================================================= */
function cg_register_acf_options_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) || ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	acf_add_options_page( array(
		'page_title'  => __( 'Theme Settings', 'child-generatepress' ),
		'menu_title'  => __( 'Theme Settings', 'child-generatepress' ),
		'menu_slug'   => 'cg-theme-settings',
		'capability'  => 'edit_theme_options',
		'redirect'    => true,
		'icon_url'    => 'dashicons-admin-generic',
		'position'    => 59,
	) );

	acf_add_options_sub_page( array(
		'page_title'  => __( 'Header Settings', 'child-generatepress' ),
		'menu_title'  => __( 'Header Settings', 'child-generatepress' ),
		'parent_slug' => 'cg-theme-settings',
		'slug'        => 'cg-header-settings',
	) );

	acf_add_options_sub_page( array(
		'page_title'  => __( 'Footer Settings', 'child-generatepress' ),
		'menu_title'  => __( 'Footer Settings', 'child-generatepress' ),
		'parent_slug' => 'cg-theme-settings',
		'slug'        => 'cg-footer-settings',
	) );
}
add_action( 'acf/init', 'cg_register_acf_options_pages' );

/* =========================================================================
 * 3. HEADER - ACF FIELD GROUPS
 * ========================================================================= */
function cg_register_header_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'    => 'group_cg_header_logo',
		'title'  => __( 'Header - Logo / Brand', 'child-generatepress' ),
		'fields' => array(
			array(
				'key'           => 'field_cg_header_logo_type',
				'label'         => __( 'Logo Type', 'child-generatepress' ),
				'name'          => 'cg_header_logo_type',
				'type'          => 'select',
				'choices'       => array(
					'text'  => __( 'Text Logo (default)', 'child-generatepress' ),
					'image' => __( 'Image Logo', 'child-generatepress' ),
				),
				'default_value' => 'text',
				'allow_null'    => 0,
				'ui'            => 1,
				'return_format' => 'value',
			),
			array(
				'key'               => 'field_cg_header_logo_image',
				'label'             => __( 'Header Logo (Image)', 'child-generatepress' ),
				'name'              => 'cg_header_logo_image',
				'type'              => 'image',
				'return_format'     => 'id',
				'preview_size'      => 'medium',
				'library'           => 'all',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_header_logo_type',
							'operator' => '==',
							'value'    => 'image',
						),
					),
				),
			),
			array(
				'key'               => 'field_cg_header_logo_mark',
				'label'             => __( 'Logo Mark', 'child-generatepress' ),
				'name'              => 'cg_header_logo_mark',
				'type'              => 'text',
				'instructions'      => __( 'Small mark to the left of logo text (e.g. "CG").', 'child-generatepress' ),
				'placeholder'       => 'CG',
				'default_value'     => 'CG',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_header_logo_type',
							'operator' => '==',
							'value'    => 'text',
						),
					),
				),
			),
			array(
				'key'               => 'field_cg_header_logo_text',
				'label'             => __( 'Logo Text', 'child-generatepress' ),
				'name'              => 'cg_header_logo_text',
				'type'              => 'text',
				'instructions'      => __( 'Main brand text beside the mark.', 'child-generatepress' ),
				'placeholder'       => 'CLARKE GROUP',
				'default_value'     => 'CLARKE GROUP',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_header_logo_type',
							'operator' => '==',
							'value'    => 'text',
						),
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'cg-header-settings',
				),
			),
		),
		'menu_order'            => 1,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'    => 'group_cg_header_cta',
		'title'  => __( 'Header - Call To Action Button', 'child-generatepress' ),
		'fields' => array(
			array(
				'key'           => 'field_cg_header_cta_show',
				'label'         => __( 'Show Header CTA Button', 'child-generatepress' ),
				'name'          => 'cg_header_cta_show',
				'type'          => 'true_false',
				'message'       => __( 'Display the CTA button in the header', 'child-generatepress' ),
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'child-generatepress' ),
				'ui_off_text'   => __( 'Hide', 'child-generatepress' ),
			),
			array(
				'key'               => 'field_cg_header_cta_link',
				'label'             => __( 'CTA Button (link + text + target)', 'child-generatepress' ),
				'name'              => 'cg_header_cta_link',
				'type'              => 'link',
				'instructions'      => __( 'Populate both URL and Title to show this button. If empty, defaults to "#contact anchor + Get In Touch" text.', 'child-generatepress' ),
				'return_format'     => 'array',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_header_cta_show',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
			),
			array(
				'key'               => 'field_cg_header_cta_arrow',
				'label'             => __( 'Arrow Symbol', 'child-generatepress' ),
				'name'              => 'cg_header_cta_arrow',
				'type'              => 'text',
				'instructions'      => __( 'Optional arrow character appended after the button text.', 'child-generatepress' ),
				'placeholder'       => '→',
				'default_value'     => '→',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_header_cta_show',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'cg-header-settings',
				),
			),
		),
		'menu_order'            => 10,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'    => 'group_cg_header_contact',
		'title'  => __( 'Header - Contact Information (optional)', 'child-generatepress' ),
		'fields' => array(
			array(
				'key'          => 'field_cg_header_phone',
				'label'        => __( 'Phone Number', 'child-generatepress' ),
				'name'         => 'cg_header_phone',
				'type'         => 'text',
				'instructions' => __( 'Available to templates; not displayed by default in current design.', 'child-generatepress' ),
				'placeholder'  => '+44 (0)123 456 7890',
			),
			array(
				'key'          => 'field_cg_header_email',
				'label'        => __( 'Email Address', 'child-generatepress' ),
				'name'         => 'cg_header_email',
				'type'         => 'email',
				'instructions' => __( 'Available to templates; not displayed by default in current design.', 'child-generatepress' ),
				'placeholder'  => 'hello@example.com',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'cg-header-settings',
				),
			),
		),
		'menu_order'            => 20,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );
}
add_action( 'acf/init', 'cg_register_header_acf_fields' );

/* =========================================================================
 * 4. FOOTER - ACF FIELD GROUPS
 * ========================================================================= */
function cg_register_footer_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'    => 'group_cg_footer_cta',
		'title'  => __( 'Footer - CTA Section', 'child-generatepress' ),
		'fields' => array(
			array(
				'key'           => 'field_cg_footer_cta_show',
				'label'         => __( 'Show Footer CTA Section', 'child-generatepress' ),
				'name'          => 'cg_footer_cta_show',
				'type'          => 'true_false',
				'message'       => __( 'Display the CTA section above the main footer.', 'child-generatepress' ),
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Show', 'child-generatepress' ),
				'ui_off_text'   => __( 'Hide', 'child-generatepress' ),
			),
			array(
				'key'               => 'field_cg_footer_cta_heading',
				'label'             => __( 'CTA Heading', 'child-generatepress' ),
				'name'              => 'cg_footer_cta_heading',
				'type'              => 'textarea',
				'instructions'      => __( 'Line breaks preserved via <br>. Use two lines like the original.', 'child-generatepress' ),
				'rows'              => 3,
				'default_value'     => 'Explore our developments
or speak with our team.',
				'new_lines'         => 'br',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_footer_cta_show',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
			),
			array(
				'key'               => 'field_cg_footer_cta_button_link',
				'label'             => __( 'CTA Button (link + text + target)', 'child-generatepress' ),
				'name'              => 'cg_footer_cta_button_link',
				'type'              => 'link',
				'return_format'     => 'array',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_footer_cta_show',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
			),
			array(
				'key'               => 'field_cg_footer_cta_button_arrow',
				'label'             => __( 'Button Arrow Symbol', 'child-generatepress' ),
				'name'              => 'cg_footer_cta_button_arrow',
				'type'              => 'text',
				'instructions'      => __( 'Optional arrow character appended to the button text.', 'child-generatepress' ),
				'placeholder'       => '→',
				'default_value'     => '→',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_footer_cta_show',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'cg-footer-settings',
				),
			),
		),
		'menu_order'            => 1,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'    => 'group_cg_footer_brand',
		'title'  => __( 'Footer - Brand / Logo', 'child-generatepress' ),
		'fields' => array(
			array(
				'key'           => 'field_cg_footer_logo_type',
				'label'         => __( 'Footer Logo Type', 'child-generatepress' ),
				'name'          => 'cg_footer_logo_type',
				'type'          => 'select',
				'choices'       => array(
					'text'  => __( 'Text / Icon (default)', 'child-generatepress' ),
					'image' => __( 'Image Logo', 'child-generatepress' ),
				),
				'default_value' => 'text',
				'allow_null'    => 0,
				'ui'            => 1,
				'return_format' => 'value',
			),
			array(
				'key'               => 'field_cg_footer_logo_image',
				'label'             => __( 'Footer Logo (Image)', 'child-generatepress' ),
				'name'              => 'cg_footer_logo_image',
				'type'              => 'image',
				'return_format'     => 'id',
				'preview_size'      => 'medium',
				'library'           => 'all',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_footer_logo_type',
							'operator' => '==',
							'value'    => 'image',
						),
					),
				),
			),
			array(
				'key'               => 'field_cg_footer_logo_icon',
				'label'             => __( 'Footer Logo Icon', 'child-generatepress' ),
				'name'              => 'cg_footer_logo_icon',
				'type'              => 'text',
				'instructions'      => __( 'Small symbol (e.g. ⚒).', 'child-generatepress' ),
				'placeholder'       => '⚒',
				'default_value'     => '⚒',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_footer_logo_type',
							'operator' => '==',
							'value'    => 'text',
						),
					),
				),
			),
			array(
				'key'               => 'field_cg_footer_logo_text_line1',
				'label'             => __( 'Footer Logo - Line 1', 'child-generatepress' ),
				'name'              => 'cg_footer_logo_text_line1',
				'type'              => 'text',
				'placeholder'       => 'CLARKE',
				'default_value'     => 'CLARKE',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_footer_logo_type',
							'operator' => '==',
							'value'    => 'text',
						),
					),
				),
			),
			array(
				'key'               => 'field_cg_footer_logo_text_line2',
				'label'             => __( 'Footer Logo - Line 2', 'child-generatepress' ),
				'name'              => 'cg_footer_logo_text_line2',
				'type'              => 'text',
				'placeholder'       => 'GROUP',
				'default_value'     => 'GROUP',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_cg_footer_logo_type',
							'operator' => '==',
							'value'    => 'text',
						),
					),
				),
			),
			array(
				'key'          => 'field_cg_footer_description',
				'label'        => __( 'Footer Description (optional)', 'child-generatepress' ),
				'name'         => 'cg_footer_description',
				'type'         => 'textarea',
				'instructions' => __( 'Short brand description below the footer logo/brand. Field available but not displayed in the original design - leave blank to hide.', 'child-generatepress' ),
				'rows'         => 3,
				'new_lines'    => 'wpautop',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'cg-footer-settings',
				),
			),
		),
		'menu_order'            => 10,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'    => 'group_cg_footer_contact',
		'title'  => __( 'Footer - Contact Details (optional)', 'child-generatepress' ),
		'fields' => array(
			array(
				'key'         => 'field_cg_footer_phone',
				'label'       => __( 'Phone', 'child-generatepress' ),
				'name'        => 'cg_footer_phone',
				'type'        => 'text',
				'placeholder' => '+44 (0)123 456 7890',
			),
			array(
				'key'         => 'field_cg_footer_email',
				'label'       => __( 'Email', 'child-generatepress' ),
				'name'        => 'cg_footer_email',
				'type'        => 'email',
				'placeholder' => 'hello@example.com',
			),
			array(
				'key'         => 'field_cg_footer_address',
				'label'       => __( 'Address', 'child-generatepress' ),
				'name'        => 'cg_footer_address',
				'type'        => 'textarea',
				'rows'        => 3,
				'placeholder' => '123 Street
Town
Postcode',
				'new_lines'   => 'br',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'cg-footer-settings',
				),
			),
		),
		'menu_order'            => 20,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'    => 'group_cg_footer_social',
		'title'  => __( 'Footer - Social Media Links', 'child-generatepress' ),
		'fields' => array(
			array(
				'key'          => 'field_cg_footer_social_facebook',
				'label'        => __( 'Facebook URL', 'child-generatepress' ),
				'name'         => 'cg_footer_social_facebook',
				'type'         => 'url',
				'instructions' => __( 'Leave empty to hide Facebook item.', 'child-generatepress' ),
				'placeholder'  => 'https://facebook.com/yourpage',
			),
			array(
				'key'          => 'field_cg_footer_social_instagram',
				'label'        => __( 'Instagram URL', 'child-generatepress' ),
				'name'         => 'cg_footer_social_instagram',
				'type'         => 'url',
				'placeholder'  => 'https://instagram.com/yourpage',
			),
			array(
				'key'          => 'field_cg_footer_social_twitter',
				'label'        => __( 'Twitter / X URL', 'child-generatepress' ),
				'name'         => 'cg_footer_social_twitter',
				'type'         => 'url',
				'placeholder'  => 'https://twitter.com/yourhandle',
			),
			array(
				'key'          => 'field_cg_footer_social_linkedin',
				'label'        => __( 'LinkedIn URL', 'child-generatepress' ),
				'name'         => 'cg_footer_social_linkedin',
				'type'         => 'url',
				'placeholder'  => 'https://linkedin.com/company/yourpage',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'cg-footer-settings',
				),
			),
		),
		'menu_order'            => 30,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );

	acf_add_local_field_group( array(
		'key'    => 'group_cg_footer_copyright',
		'title'  => __( 'Footer - Copyright', 'child-generatepress' ),
		'fields' => array(
			array(
				'key'           => 'field_cg_footer_copyright',
				'label'         => __( 'Copyright Text', 'child-generatepress' ),
				'name'          => 'cg_footer_copyright',
				'type'          => 'text',
				'instructions'  => __( 'Use {{year}} placeholder for the current year.', 'child-generatepress' ),
				'default_value' => '© {{year}} Clarke Group. All rights reserved.',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'cg-footer-settings',
				),
			),
		),
		'menu_order'            => 40,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	) );
}
add_action( 'acf/init', 'cg_register_footer_acf_fields' );

/* =========================================================================
 * 5. HELPERS
 * ========================================================================= */

/**
 * Outputs only the <li> items from wp_nav_menu for the provided location,
 * falling back to an array of 'url'/'label' pairs if no menu is assigned.
 *
 * @param string $location Registered nav menu location slug.
 * @param array  $fallback Fallback menu items array( array( 'url'=>'', 'label'=>'' ), ... )
 * @return void
 */
function cg_the_nav_menu_li( $location, $fallback = array() ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu( array(
			'theme_location' => $location,
			'container'      => false,
			'items_wrap'     => '%3$s',
			'depth'          => 2,
			'fallback_cb'    => '__return_empty_string',
		) );
		return;
	}
	foreach ( $fallback as $item ) {
		if ( empty( $item['label'] ) || empty( $item['url'] ) ) {
			continue;
		}
		$target_attr = '';
		if ( ! empty( $item['target'] ) ) {
			$target_attr = ' target="' . esc_attr( $item['target'] ) . '" rel="noopener noreferrer"';
		}
		printf(
			'<li><a href="%s"%s>%s</a></li>',
			esc_url( $item['url'] ),
			$target_attr,
			esc_html( $item['label'] )
		);
	}
}

/**
 * Safely echo a string (with optional escaping).
 *
 * @param mixed  $value       The value to output.
 * @param string $escape      'html' | 'attr' | 'url' | 'none'.
 * @return void
 */
function cg_e( $value, $escape = 'html' ) {
	if ( null === $value || false === $value || '' === $value ) {
		return;
	}
	$v = (string) $value;
	switch ( $escape ) {
		case 'attr':
			echo esc_attr( $v );
			break;
		case 'url':
			echo esc_url( $v );
			break;
		case 'none':
			echo $v;
			break;
		case 'html':
		default:
			echo esc_html( $v );
			break;
	}
}

/**
 * Get ACF field value from the options page with a default fallback.
 *
 * @param string $key     ACF field name/key.
 * @param mixed  $default Default value when field empty/missing.
 * @return mixed
 */
function cg_opt( $key, $default = '' ) {
	$val = function_exists( 'get_field' ) ? get_field( $key, 'option' ) : null;
	if ( null === $val || false === $val || '' === $val ) {
		return $default;
	}
	return $val;
}
