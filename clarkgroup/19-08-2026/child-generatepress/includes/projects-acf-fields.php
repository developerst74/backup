<?php
if (!defined('ABSPATH')) {
	exit;
}


/**
 * =========================================================
 * 1. Enable drag-and-drop ordering for the Projects list.
 * =========================================================
 */

// Make sure the CPT supports menu_order (page-attributes) without touching
// the original register_post_type() call.
add_filter('register_post_type_args', function ($args, $post_type) {
	if ('projects' === $post_type) {
		if (!isset($args['supports']) || !is_array($args['supports'])) {
			$args['supports'] = array();
		}
		if (!in_array('page-attributes', $args['supports'], true)) {
			$args['supports'][] = 'page-attributes';
		}
	}
	return $args;
}, 20, 2);

// Default the admin list to menu_order (falls back to date for ties) unless
// the user has explicitly clicked a sortable column header.
add_action('pre_get_posts', function ($query) {
	if (!is_admin() || !$query->is_main_query()) {
		return;
	}
	if ('projects' !== $query->get('post_type')) {
		return;
	}
	if (empty($_GET['orderby'])) {
		$query->set('orderby', array('menu_order' => 'ASC', 'date' => 'DESC'));
	}
});

// Add a drag-handle column at the far left of the list table.
add_filter('manage_projects_posts_columns', function ($columns) {
	$new = array();
	foreach ($columns as $key => $label) {
		if ('cb' === $key) {
			$new[$key] = $label;
			$new['cg_drag_handle'] = '<span class="dashicons dashicons-menu" title="Drag to reorder"></span>';
			continue;
		}
		$new[$key] = $label;
	}
	return $new;
}, 20);

add_action('manage_projects_posts_custom_column', function ($column, $post_id) {
	if ('cg_drag_handle' === $column) {
		echo '<span class="cg-drag-handle dashicons dashicons-menu" style="cursor:grab;color:#a7aaad;"></span>';
	}
}, 20, 2);

// Only show/activate dragging on the default (unsorted, unsearched, unfiltered) view.
function cg_projects_dragdrop_active()
{
	if (!empty($_GET['orderby']) && 'menu_order' !== $_GET['orderby']) {
		return false;
	}
	if (!empty($_GET['s'])) {
		return false;
	}
	if (!empty($_GET['paged']) && (int) $_GET['paged'] > 1) {
		return false;
	}
	return true;
}

add_action('admin_enqueue_scripts', function ($hook) {
	if ('edit.php' !== $hook) {
		return;
	}

	if (empty($_GET['post_type']) || 'projects' !== $_GET['post_type']) {
		return;
	}

	if (!cg_projects_dragdrop_active()) {
		return;
	}

	wp_enqueue_script('jquery-ui-sortable');

	$nonce = wp_create_nonce('cg_reorder_projects');

	$inline_js = "
jQuery(function ($) {
	$('#the-list').sortable({
		items: 'tr',
		handle: '.cg-drag-handle',
		axis: 'y',
		cursor: 'grabbing',
		opacity: 0.6,

		update: function () {
			var order = [];

			$('#the-list tr').each(function () {
				var id = $(this).attr('id');

				if (id) {
					order.push(id.replace('post-', ''));
				}
			});

			$.post(ajaxurl, {
				action: 'cg_reorder_projects',
				nonce: '" . esc_js($nonce) . "',
				order: order
			});
		}
	});
});
";

	wp_add_inline_script('jquery-ui-sortable', $inline_js);

	wp_add_inline_style('wp-admin', '
		.cg-drag-handle:hover {
			color: #2271b1;
		}

		#the-list tr.ui-sortable-helper {
			background: #f6f7f7;
			box-shadow: 0 1px 4px rgba(0,0,0,.2);
			display: table;
		}

		.column-cg_drag_handle {
			width: 30px;
			text-align: center;
		}
	');
});

// AJAX handler that saves the new order as menu_order values.
add_action('wp_ajax_cg_reorder_projects', function () {
	check_ajax_referer('cg_reorder_projects', 'nonce');

	if (!current_user_can('edit_others_posts')) {
		wp_send_json_error('Not allowed');
	}

	$order = isset($_POST['order']) ? (array) $_POST['order'] : array();

	foreach ($order as $position => $post_id) {
		$post_id = (int) $post_id;
		if ('projects' !== get_post_type($post_id)) {
			continue;
		}
		wp_update_post(array(
			'ID' => $post_id,
			'menu_order' => (int) $position,
		));
	}

	wp_send_json_success();
});


/**
 * =========================================================
 * 2. "Duplicate" row action for Projects.
 * =========================================================
 */

add_filter('post_row_actions', function ($actions, $post) {
	if ('projects' !== $post->post_type) {
		return $actions;
	}
	if (!current_user_can('edit_posts')) {
		return $actions;
	}

	$url = wp_nonce_url(
		add_query_arg(array(
			'action' => 'cg_duplicate_project',
			'post' => $post->ID,
		), admin_url('admin.php')),
		'cg_duplicate_project_' . $post->ID
	);

	$actions['cg_duplicate'] = '<a href="' . esc_url($url) . '">Duplicate</a>';

	return $actions;
}, 10, 2);

add_action('admin_action_cg_duplicate_project', function () {

	$post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;

	if (!$post_id || 'projects' !== get_post_type($post_id)) {
		wp_die('Invalid project.');
	}

	check_admin_referer('cg_duplicate_project_' . $post_id);

	if (!current_user_can('edit_posts')) {
		wp_die('Not allowed.');
	}

	$original = get_post($post_id);

	if (!$original) {
		wp_die('Project not found.');
	}

	// Create the duplicate post.
	$new_post_id = wp_insert_post(array(
		'post_title' => $original->post_title . ' (Copy)',
		'post_content' => $original->post_content,
		'post_excerpt' => $original->post_excerpt,
		'post_status' => 'draft',
		'post_type' => 'projects',
		'post_author' => get_current_user_id(),
		'menu_order' => $original->menu_order,
	), true);

	if (is_wp_error($new_post_id)) {
		wp_die('Could not duplicate project.');
	}

	// Copy all post meta (this covers every ACF field: status, for sale,
	// title, short description, location, hero image, gallery, etc.)
	$meta = get_post_meta($post_id);
	foreach ($meta as $key => $values) {
		foreach ($values as $value) {
			add_post_meta($new_post_id, $key, maybe_unserialize($value));
		}
	}

	// Copy taxonomy terms (project_category).
	$taxonomies = get_object_taxonomies('projects');
	foreach ($taxonomies as $taxonomy) {
		$terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'ids'));
		if (!is_wp_error($terms)) {
			wp_set_object_terms($new_post_id, $terms, $taxonomy);
		}
	}

	// Copy featured image.
	$thumbnail_id = get_post_thumbnail_id($post_id);
	if ($thumbnail_id) {
		set_post_thumbnail($new_post_id, $thumbnail_id);
	}

	wp_safe_redirect(admin_url('edit.php?post_type=projects&duplicated=1'));
	exit;
});

// Simple admin notice after a successful duplicate.
add_action('admin_notices', function () {
	if (!empty($_GET['duplicated']) && !empty($_GET['post_type']) && 'projects' === $_GET['post_type']) {
		echo '<div class="notice notice-success is-dismissible"><p>Project duplicated as a draft.</p></div>';
	}
});
function cg_register_project_acf_fields()
{

	if (!function_exists('acf_add_local_field_group')) {
		return;
	}

	acf_add_local_field_group(array(
		'key' => 'group_cg_project_status',
		'title' => __('Project Status', 'child-generatepress'),
		'fields' => array(
			array(
				'key' => 'field_cg_project_status',
				'label' => __('Project Status', 'child-generatepress'),
				'name' => 'cg_project_status',
				'type' => 'select',
				'instructions' => __('Independent from Project Category.', 'child-generatepress'),
				'required' => 1,
				'choices' => array(
					'current' => __('Current', 'child-generatepress'),
					'completed' => __('Completed', 'child-generatepress'),
				),
				'default_value' => 'current',
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 1,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'projects',
				),
			),
		),
		'menu_order' => 1,
		'position' => 'side',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
	));

	acf_add_local_field_group(array(
		'key' => 'group_cg_project_for_sale',
		'title' => __('For Sale', 'child-generatepress'),
		'fields' => array(
			array(
				'key' => 'field_cg_project_for_sale',
				'label' => __('For Sale', 'child-generatepress'),
				'name' => 'cg_project_for_sale',
				'type' => 'select',
				'instructions' => __('Independent from Project Status. Frontend displays FOR SALE label when set to Yes.', 'child-generatepress'),
				'required' => 1,
				'choices' => array(
					'no' => __('No', 'child-generatepress'),
					'yes' => __('Yes', 'child-generatepress'),
				),
				'default_value' => 'no',
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 1,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'projects',
				),
			),
		),
		'menu_order' => 2,
		'position' => 'side',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
	));

	acf_add_local_field_group(array(
		'key' => 'group_cg_project_information',
		'title' => __('Project Information', 'child-generatepress'),
		'fields' => array(
			array(
				'key' => 'field_cg_project_title',
				'label' => __('Project Title', 'child-generatepress'),
				'name' => 'cg_project_title',
				'type' => 'text',
				'instructions' => __('Display title for the project (separate from internal post title).', 'child-generatepress'),
				'required' => 0,
				'placeholder' => '',
				'maxlength' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_cg_project_short_description',
				'label' => __('Short Description', 'child-generatepress'),
				'name' => 'cg_project_short_description',
				'type' => 'textarea',
				'instructions' => __('Brief summary shown in cards/listing views.', 'child-generatepress'),
				'required' => 0,
				'placeholder' => '',
				'maxlength' => '',
				'rows' => 4,
				'new_lines' => 'wpautop',
			),
			array(
				'key' => 'field_cg_project_location',
				'label' => __('Location', 'child-generatepress'),
				'name' => 'cg_project_location',
				'type' => 'text',
				'instructions' => __('Project location (e.g. "Bath, Somerset").', 'child-generatepress'),
				'required' => 0,
				'placeholder' => '',
				'maxlength' => '',
				'prepend' => '',
				'append' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'projects',
				),
			),
		),
		'menu_order' => 10,
		'position' => 'acf_after_title',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
	));

	acf_add_local_field_group(array(
		'key' => 'group_cg_project_images',
		'title' => __('Project Images', 'child-generatepress'),
		'fields' => array(
			array(
				'key' => 'field_cg_project_hero_image',
				'label' => __('Hero Image', 'child-generatepress'),
				'name' => 'cg_project_hero_image',
				'type' => 'image',
				'instructions' => __('Primary large hero image used at top of single project page.', 'child-generatepress'),
				'required' => 0,
				'return_format' => 'id',
				'preview_size' => 'large',
				'library' => 'all',
				'min_width' => '',
				'min_height' => '',
				'min_size' => '',
				'max_width' => '',
				'max_height' => '',
				'max_size' => '',
				'mime_types' => '',
			),
			array(
				'key' => 'field_cg_project_gallery',
				'label' => __('Project Gallery', 'child-generatepress'),
				'name' => 'cg_project_gallery',
				'type' => 'gallery',
				'instructions' => __('Multiple gallery images for the project.', 'child-generatepress'),
				'required' => 0,
				'return_format' => 'id',
				'preview_size' => 'medium',
				'insert' => 'append',
				'library' => 'all',
				'min' => '',
				'max' => '',
				'min_width' => '',
				'min_height' => '',
				'min_size' => '',
				'max_width' => '',
				'max_height' => '',
				'max_size' => '',
				'mime_types' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'projects',
				),
			),
		),
		'menu_order' => 20,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
	));
}
add_action('acf/init', 'cg_register_project_acf_fields');

function cg_projects_admin_columns($columns)
{

	$new = array();

	foreach ($columns as $k => $v) {
		$new[$k] = $v;
		if ('title' === $k) {
			$new['cg_project_status'] = __('Status', 'child-generatepress');
			$new['cg_project_for_sale'] = __('For Sale', 'child-generatepress');
			$new['cg_project_category'] = __('Category', 'child-generatepress');
		}
	}

	return $new;
}
add_filter('manage_projects_posts_columns', 'cg_projects_admin_columns');

function cg_projects_admin_column_values($column, $post_id)
{

	switch ($column) {

		case 'cg_project_status':
			$val = get_field('cg_project_status', $post_id);
			if ($val) {
				$labels = array(
					'current' => __('Current', 'child-generatepress'),
					'completed' => __('Completed', 'child-generatepress'),
				);
				$color = ('current' === $val) ? '#2271b1' : '#00a32a';
				echo '<span style="display:inline-block;padding:2px 8px;border-radius:10px;color:#fff;background:' . esc_attr($color) . '">' . esc_html(isset($labels[$val]) ? $labels[$val] : $val) . '</span>';
			} else {
				echo '<span style="color:#999;">—</span>';
			}
			break;

		case 'cg_project_for_sale':
			$val = get_field('cg_project_for_sale', $post_id);
			if ('yes' === $val) {
				echo '<span style="display:inline-block;padding:2px 8px;border-radius:10px;color:#fff;background:#d63638;font-weight:600;">FOR SALE</span>';
			} else {
				echo '<span style="color:#999;">No</span>';
			}
			break;

		case 'cg_project_category':
			$terms = get_the_terms($post_id, 'project_category');
			if (!is_wp_error($terms) && !empty($terms)) {
				$names = wp_list_pluck($terms, 'name');
				echo esc_html(implode(', ', $names));
			} else {
				echo '<span style="color:#999;">—</span>';
			}
			break;
	}
}
add_action('manage_projects_posts_custom_column', 'cg_projects_admin_column_values', 10, 2);

function cg_projects_admin_sortable_columns($columns)
{
	$columns['cg_project_status'] = 'cg_project_status';
	$columns['cg_project_for_sale'] = 'cg_project_for_sale';
	return $columns;
}
add_filter('manage_edit-projects_sortable_columns', 'cg_projects_admin_sortable_columns');