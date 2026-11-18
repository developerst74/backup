<?php
/*
 * This is the child theme for GeneratePress theme, generated with Generate Child Theme plugin by catchthemes.
 *
 * (Please see https://developer.wordpress.org/themes/advanced-topics/child-themes/#how-to-create-a-child-theme)
 */
add_action( 'wp_enqueue_scripts', 'child_generatepress_enqueue_styles' );
function child_generatepress_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );
}

function child_theme_enqueue_developer_css() {

    $theme_directory = get_stylesheet_directory();
    $theme_uri       = get_stylesheet_directory_uri();

    wp_enqueue_style(
        'main',
        $theme_uri . '/assets/css/main.css',
        array(),
        filemtime($theme_directory . '/assets/css/main.css')
    );

    wp_enqueue_style(
        'dev1custom',
        $theme_uri . '/assets/css/dev1custom.css',
        array('main'),
        filemtime($theme_directory . '/assets/css/dev1custom.css')
    );

    wp_enqueue_style(
        'dev2custom',
        $theme_uri . '/assets/css/dev2custom.css',
        array('dev1custom'),
        filemtime($theme_directory . '/assets/css/dev2custom.css')
    );

    wp_enqueue_style(
        'header-footer',
        $theme_uri . '/assets/css/header-footer.css',
        array('dev2custom'),
        filemtime($theme_directory . '/assets/css/header-footer.css')
    );

    wp_enqueue_style(
        'dev3custom',
        $theme_uri . '/assets/css/dev3custom.css',
        array('header-footer'),
        filemtime($theme_directory . '/assets/css/dev3custom.css')
    );
}

add_action('wp_enqueue_scripts', 'child_theme_enqueue_developer_css');

/**
 * Enqueue site scripts.
 *
 * - about-us.js:
 *     • Header .is-scrolled state on scroll
 *     • Mobile nav toggle (aria-expanded + primaryNav.is-open)
 *     • Scroll reveal (IntersectionObserver): observes [data-reveal]
 *       elements and toggles `.is-visible` class when in viewport.
 *       Required by dev2custom.css `.reveal { opacity:0; transform: translateY(28px) }`
 *       base state — without this script, those 50+ About-Us blocks stay
 *       permanently invisible.  Loaded on every page because header + nav
 *       are used everywhere;  only 10.6 KB and async-safe.
 */
function child_theme_enqueue_scripts() {

    $theme_directory = get_stylesheet_directory();
    $theme_uri       = get_stylesheet_directory_uri();

    wp_enqueue_script(
        'cg-about-us',
        $theme_uri . '/assets/js/about-us.js',
        array(),
        filemtime( $theme_directory . '/assets/js/about-us.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_scripts' );

/**
 * Add a "no-reveals" flag on <html> so main.css can ship a safe fallback:
 *   html.no-reveals .reveal { opacity: 1 !important; transform: none !important; }
 * about-us.js' very first action removes this class.  If JS never loads
 * (blocked, network issue, syntax error) the class stays — content stays
 * visible.  If JS loads OK the class is removed and dev2custom.css
 * opacity:0 base state applies until IntersectionObserver reveals them.
 */
function child_theme_html_no_reveals_class( $output ) {
    if ( stripos( $output, 'no-reveals' ) === false ) {
        $output = rtrim( $output ) . ' class="no-reveals"';
    }
    return $output;
}
add_filter( 'language_attributes', 'child_theme_html_no_reveals_class', 20 );

/* ------------------------------------
 * Projects Custom Post Type + Fields
 * ------------------------------------ */
require_once trailingslashit( get_stylesheet_directory() ) . 'includes/projects-cpt.php';
require_once trailingslashit( get_stylesheet_directory() ) . 'includes/projects-acf-fields.php';

/* ------------------------------------
 * Theme Settings (Header + Footer ACF Options, Menu Locations)
 * ------------------------------------ */
require_once trailingslashit( get_stylesheet_directory() ) . 'includes/theme-settings.php';
