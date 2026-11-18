<?php
if ( ! defined( "ABSPATH" ) ) { exit; }
if ( ! function_exists( "st_elementor_get_template" ) ) {
function st_elementor_get_template( $template_name, $args = array() ) {}
}
if ( ! function_exists( "st_elementor_allow_image_formats" ) ) {
function st_elementor_allow_image_formats( $mimes ) {
$allow_svg = is_user_logged_in() && current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' );
if ( $allow_svg ) {
$mimes["svg"]  = "image/svg+xml";
$mimes["svgz"] = "image/svg+xml";
}
$mimes["webp"] = "image/webp";
$mimes["avif"] = "image/avif";
$mimes["heic"] = "image/heic";
$mimes["ico"]  = "image/x-icon";
$mimes["jfif"] = "image/jpeg";
return $mimes;
}
add_filter( "upload_mimes", "st_elementor_allow_image_formats" );
}
if ( ! function_exists( "st_elementor_fix_svg_mime" ) ) {
function st_elementor_fix_svg_mime( $data, $file, $filename, $mimes ) {
$parts = explode( ".", $filename );
$ext   = strtolower( end( $parts ) );
if ( "svg" === $ext || "svgz" === $ext ) {
$allow_svg = is_user_logged_in() && current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' );
if ( ! $allow_svg ) {
return $data;
}
$data["type"] = "image/svg+xml";
$data["ext"]  = $ext;
}
return $data;
}
add_filter( "wp_check_filetype_and_ext", "st_elementor_fix_svg_mime", 10, 4 );
}
