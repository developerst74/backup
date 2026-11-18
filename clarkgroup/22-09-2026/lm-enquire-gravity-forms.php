<?php
if (!defined('ABSPATH')) exit;

/**
 * Populates the "Gravity Form" select field (cg_lm_enq_gravity_form, inside
 * the Project — Enquiry Form flexible-content layout) with every form
 * currently in Gravity Forms, so the client picks a form by name in the
 * ACF admin instead of typing an ID.
 *
 * Requires the Gravity Forms plugin to be active. If it isn't, the field
 * just shows no choices and section-lm_enquire.php falls back to the
 * custom mailto form.
 */
add_filter('acf/load_field/key=cg_lm_enq_gravity_form', function ($field) {
	$field['choices'] = [];

	if (!class_exists('GFAPI')) {
		return $field;
	}

	$forms = GFAPI::get_forms(true, false); // active forms only
	if (empty($forms) || is_wp_error($forms)) {
		return $field;
	}

	foreach ($forms as $form) {
		$field['choices'][$form['id']] = $form['title'] . ' (#' . $form['id'] . ')';
	}

	return $field;
});
