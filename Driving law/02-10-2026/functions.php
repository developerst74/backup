<?php
/**
 * Hello Elementor Child Theme Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Child Theme Assets
 */
function drivinglaw_child_enqueue_assets() {

	wp_enqueue_style(
		'drivinglaw-child-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);

	$custom_css = get_stylesheet_directory() . '/assets/css/custom.css';

	if ( file_exists( $custom_css ) ) {
		wp_enqueue_style(
			'drivinglaw-custom-css',
			get_stylesheet_directory_uri() . '/assets/css/custom.css',
			array( 'drivinglaw-child-style' ),
			filemtime( $custom_css )
		);
	}

	$custom_js = get_stylesheet_directory() . '/assets/js/custom.js';

	if ( file_exists( $custom_js ) ) {
		wp_enqueue_script(
			'drivinglaw-custom-js',
			get_stylesheet_directory_uri() . '/assets/js/custom.js',
			array( 'jquery' ),
			filemtime( $custom_js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'drivinglaw_child_enqueue_assets', 20 );


/**
 * Reading Time Shortcode
 */
function da_reading_time_shortcode() {
	global $post;

	if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
		return '';
	}

	$content = get_post_field( 'post_content', $post->ID );
	$content = strip_shortcodes( $content );
	$content = wp_strip_all_tags( $content );

	$word_count    = str_word_count( $content );
	$reading_time  = max( 1, ceil( $word_count / 200 ) );

	return sprintf(
		esc_html__( '%d MIN READ', 'hello-elementor-child' ),
		$reading_time
	);
}
add_shortcode( 'reading_time', 'da_reading_time_shortcode' );



/**
 * Load Penalty Estimator
 */
function dl_load_penalty_estimator() {

	static $loaded = false;

	if ( $loaded ) {
		return;
	}

	$loaded = true;

	$penalty_estimator_file  = get_stylesheet_directory() . '/inc/penalty-estimator/penalty-estimator.php';
	$penalty_estimator_admin = get_stylesheet_directory() . '/inc/penalty-estimator/penalty-estimator-admin.php';

	if ( file_exists( $penalty_estimator_file ) ) {
		require_once $penalty_estimator_file;
	}

	if ( file_exists( $penalty_estimator_admin ) ) {
		require_once $penalty_estimator_admin;
	}

	if ( class_exists( 'DL_Penalty_Estimator' ) ) {
		DL_Penalty_Estimator::init();
	}

	if ( class_exists( 'DL_Penalty_Estimator_Admin' ) ) {
		DL_Penalty_Estimator_Admin::init();
	}
}
add_action( 'after_setup_theme', 'dl_load_penalty_estimator', 20 );


/**
 * Drinking functionality
 */
function richlands_work_licence_results() {

	return array(
		'eligible'    => get_field( 'work_licence_eligible' ),
		'mayNotQualify' => get_field( 'work_licence_may_not_qualify' ),
		'highRange'   => get_field( 'work_licence_high_range' ),
	);
}

function richlands_work_licence_results_script() {

	$results = richlands_work_licence_results();
	?>
	<script>
		window.richlandsWorkLicenceResults = <?php echo wp_json_encode( $results ); ?>;
	</script>
	<?php
}
add_action( 'wp_footer', 'richlands_work_licence_results_script' );


/**
 * DL Work Licence Checker
 */
function dl_load_work_licence_checker() {

	static $loaded = false;

	if ( $loaded ) {
		return;
	}

	$loaded = true;

	$wlc_file       = get_stylesheet_directory() . '/inc/work-licence-checker/work-licence-checker.php';
	$wlc_admin_file = get_stylesheet_directory() . '/inc/work-licence-checker/work-licence-checker-admin.php';

	if ( file_exists( $wlc_file ) ) {
		require_once $wlc_file;
	}

	if ( file_exists( $wlc_admin_file ) ) {
		require_once $wlc_admin_file;
	}

	if ( class_exists( 'DL_WLC' ) ) {
		DL_WLC::init();
	}

	if ( class_exists( 'DL_WLC_Admin' ) ) {
		DL_WLC_Admin::init();
	}
}
add_action( 'after_setup_theme', 'dl_load_work_licence_checker', 20 );


function dl_load_court_finder() {

    $court_finder_file = get_stylesheet_directory() . '/inc/court-finder/court-finder.php';
    $court_finder_admin = get_stylesheet_directory() . '/inc/court-finder/court-finder-admin.php';

    if ( file_exists( $court_finder_file ) ) {
        require_once $court_finder_file;
    }

    if ( file_exists( $court_finder_admin ) ) {
        require_once $court_finder_admin;
    }

    if ( class_exists( 'DL_CF' ) ) {
        DL_CF::init();
    }

    if ( class_exists( 'DL_CF_Admin' ) ) {
        DL_CF_Admin::init();
    }
}

add_action( 'after_setup_theme', 'dl_load_court_finder', 20 );
