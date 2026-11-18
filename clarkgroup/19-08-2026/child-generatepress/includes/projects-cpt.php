<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cg_register_projects_cpt() {

	$labels = array(
		'name'                  => _x( 'Projects', 'Post Type General Name', 'child-generatepress' ),
		'singular_name'         => _x( 'Project', 'Post Type Singular Name', 'child-generatepress' ),
		'menu_name'             => __( 'Projects', 'child-generatepress' ),
		'name_admin_bar'        => __( 'Project', 'child-generatepress' ),
		'archives'              => __( 'Project Archives', 'child-generatepress' ),
		'attributes'            => __( 'Project Attributes', 'child-generatepress' ),
		'parent_item_colon'     => __( 'Parent Project:', 'child-generatepress' ),
		'all_items'             => __( 'All Projects', 'child-generatepress' ),
		'add_new_item'          => __( 'Add New Project', 'child-generatepress' ),
		'add_new'               => __( 'Add New', 'child-generatepress' ),
		'new_item'              => __( 'New Project', 'child-generatepress' ),
		'edit_item'             => __( 'Edit Project', 'child-generatepress' ),
		'update_item'           => __( 'Update Project', 'child-generatepress' ),
		'view_item'             => __( 'View Project', 'child-generatepress' ),
		'view_items'            => __( 'View Projects', 'child-generatepress' ),
		'search_items'          => __( 'Search Project', 'child-generatepress' ),
		'not_found'             => __( 'Not found', 'child-generatepress' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'child-generatepress' ),
		'featured_image'        => __( 'Featured Image', 'child-generatepress' ),
		'set_featured_image'    => __( 'Set featured image', 'child-generatepress' ),
		'remove_featured_image' => __( 'Remove featured image', 'child-generatepress' ),
		'use_featured_image'    => __( 'Use as featured image', 'child-generatepress' ),
		'insert_into_item'      => __( 'Insert into project', 'child-generatepress' ),
		'uploaded_to_this_item' => __( 'Uploaded to this project', 'child-generatepress' ),
		'items_list'            => __( 'Projects list', 'child-generatepress' ),
		'items_list_navigation' => __( 'Projects list navigation', 'child-generatepress' ),
		'filter_items_list'     => __( 'Filter projects list', 'child-generatepress' ),
	);

	$args = array(
		'label'               => __( 'Project', 'child-generatepress' ),
		'description'         => __( 'Projects custom post type', 'child-generatepress' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
		'taxonomies'          => array( 'project_category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-admin-multisite',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => 'projects',
		'rewrite'             => array( 'slug' => 'projects', 'with_front' => false ),
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_in_rest'        => true,
		'rest_base'           => 'projects',
		'capability_type'     => 'post',
	);

	register_post_type( 'projects', $args );
}
add_action( 'init', 'cg_register_projects_cpt', 0 );

function cg_register_project_category_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Project Categories', 'Taxonomy General Name', 'child-generatepress' ),
		'singular_name'              => _x( 'Project Category', 'Taxonomy Singular Name', 'child-generatepress' ),
		'menu_name'                  => __( 'Project Categories', 'child-generatepress' ),
		'all_items'                  => __( 'All Categories', 'child-generatepress' ),
		'parent_item'                => __( 'Parent Category', 'child-generatepress' ),
		'parent_item_colon'          => __( 'Parent Category:', 'child-generatepress' ),
		'new_item_name'              => __( 'New Category Name', 'child-generatepress' ),
		'add_new_item'               => __( 'Add New Category', 'child-generatepress' ),
		'edit_item'                  => __( 'Edit Category', 'child-generatepress' ),
		'update_item'                => __( 'Update Category', 'child-generatepress' ),
		'view_item'                  => __( 'View Category', 'child-generatepress' ),
		'separate_items_with_commas' => __( 'Separate categories with commas', 'child-generatepress' ),
		'add_or_remove_items'        => __( 'Add or remove categories', 'child-generatepress' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'child-generatepress' ),
		'popular_items'              => __( 'Popular Categories', 'child-generatepress' ),
		'search_items'               => __( 'Search Categories', 'child-generatepress' ),
		'not_found'                  => __( 'Not Found', 'child-generatepress' ),
		'items_list'                 => __( 'Categories list', 'child-generatepress' ),
		'items_list_navigation'      => __( 'Categories list navigation', 'child-generatepress' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => false,
		'show_in_rest'      => true,
		'rest_base'         => 'project-categories',
		'rewrite'           => array( 'slug' => 'project-category', 'with_front' => false ),
	);

	register_taxonomy( 'project_category', array( 'projects' ), $args );
}
add_action( 'init', 'cg_register_project_category_taxonomy', 0 );

function cg_seed_default_project_categories() {

	$taxonomy = 'project_category';

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$defaults = array(
		'ladies-mile'       => 'Ladies Mile',
		'premium-homes'     => 'Premium Homes',
		'affordable-homes'  => 'Affordable Homes',
		'build-to-rent'     => 'Build to Rent',
		'special-projects'  => 'Special Projects',
	);

	foreach ( $defaults as $slug => $name ) {
		if ( ! term_exists( $name, $taxonomy ) && ! term_exists( $slug, $taxonomy ) ) {
			wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
		}
	}
}
add_action( 'admin_init', 'cg_seed_default_project_categories' );
add_action( 'init', 'cg_seed_default_project_categories', 20 );
