<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Save ─────────────────────────────────────────────────────────── */
if ( isset( $_POST['st_hf_rules_nonce'] ) && wp_verify_nonce( $_POST['st_hf_rules_nonce'], 'st_hf_rules_save' ) && current_user_can( 'manage_options' ) ) {
	$raw   = isset( $_POST['st_hf_rule'] ) ? (array) $_POST['st_hf_rule'] : array();
	$clean = array();
	foreach ( $raw as $rule ) {
		$role = sanitize_key( $rule['role'] ?? '' );
		$tid  = (int) ( $rule['template_id'] ?? 0 );
		if ( ! $tid || ! in_array( $role, array( 'header', 'footer' ), true ) ) continue;
		$clean[] = array(
			'role'        => $role,
			'template_id' => $tid,
			'condition'   => sanitize_text_field( $rule['condition'] ?? 'entire_site' ),
			'sub'         => sanitize_text_field( $rule['sub'] ?? '' ),
			'post_type'   => sanitize_key( $rule['post_type'] ?? '' ),
			'page_id'     => (int) ( $rule['page_id'] ?? 0 ),
		);
	}
	update_option( 'st_header_hide_selector', sanitize_text_field( wp_unslash( $_POST['st_header_selector'] ?? '' ) ) );
	update_option( 'st_footer_hide_selector', sanitize_text_field( wp_unslash( $_POST['st_footer_selector'] ?? '' ) ) );
	update_option( 'st_hf_rules', $clean );

	// ── Sync back to each template's meta ────────────────────────────
	foreach ( $clean as $rule ) {
		$tid = (int) ( $rule['template_id'] ?? 0 );
		if ( ! $tid ) continue;
		$cond_meta = array(
			'condition' => $rule['condition'] ?? 'entire_site',
			'sub'       => $rule['sub']       ?? '',
			'post_type' => $rule['post_type'] ?? '',
			'page_id'   => (int) ( $rule['page_id'] ?? 0 ),
		);
		update_post_meta( $tid, '_st_display_conditions', $cond_meta );
	}

	echo '<div class="notice notice-success is-dismissible"><p>&#10003; ' . esc_html__( 'Settings saved.', 'st-elementor' ) . '</p></div>';
}

/* ── Data ─────────────────────────────────────────────────────────── */
$rules     = get_option( 'st_hf_rules', array() );
$templates = get_posts( array( 'post_type' => 'st_template', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
$pub_types = get_post_types( array( 'public' => true ), 'objects' );
unset( $pub_types['attachment'], $pub_types['st_template'], $pub_types['st_popup'] );
$all_pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );

/*
 * Condition tree (matches screenshot):
 *
 * Level 1 (condition):
 *   entire_site  → no sub
 *   all_website  → sub: all_pages | archive | single | singular
 *
 * Level 2 (sub):
 *   all_pages  → no further
 *   archive    → post_type dropdown
 *   single     → sub2: single_post | single_page
 *   singular   → page_id dropdown (specific page)
 */
$l1_opts = array(
	'entire_site' => 'Entire Site',
	'all_website' => 'All Website',
);
$l2_opts = array(
	'all_pages' => 'All Pages',
	'archive'   => 'Archive',
	'single'    => 'Single',
	'singular'  => 'Singular',
);
$l3_single_opts = array(
	'single_post' => 'Single Post',
	'single_page' => 'Single Page',
);

$header_rules = array_values(
	array_filter(
		$rules,
		function( $r ) {
			return ( $r['role'] ?? '' ) === 'header';
		}
	)
);
$footer_rules = array_values(
	array_filter(
		$rules,
		function( $r ) {
			return ( $r['role'] ?? '' ) === 'footer';
		}
	)
);

function st_hf_rule_row( $role, $key, $rule, $templates, $l1_opts, $l2_opts, $l3_single_opts, $pub_types, $all_pages ) {
	$cond     = $rule['condition'] ?? 'entire_site';
	$sub      = $rule['sub']       ?? '';
	$pt       = $rule['post_type'] ?? '';
	$page_id  = (int)($rule['page_id'] ?? 0);
	$tid      = (int)($rule['template_id'] ?? 0);
	?>
	<div class="st-rule-row">
		<input type="hidden" name="st_hf_rule[<?php echo esc_attr($key); ?>][role]" value="<?php echo esc_attr($role); ?>">
		<button type="button" class="st-remove-rule">&#x2715;</button>

		<div class="st-rule-inner">

			<!-- Template -->
			<div class="st-rf">
				<label><?php esc_html_e('Template','st-elementor'); ?></label>
				<select name="st_hf_rule[<?php echo esc_attr($key); ?>][template_id]" class="st-sel">
					<option value=""><?php esc_html_e('— Select —','st-elementor'); ?></option>
					<?php foreach($templates as $t): ?>
					<option value="<?php echo esc_attr($t->ID); ?>" <?php selected($tid,$t->ID); ?>><?php echo esc_html($t->post_title); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<!-- Level 1: Entire Site / All Website -->
			<div class="st-rf">
				<label><?php esc_html_e('Display On','st-elementor'); ?></label>
				<select name="st_hf_rule[<?php echo esc_attr($key); ?>][condition]" class="st-sel st-l1">
					<?php foreach($l1_opts as $v=>$l): ?>
					<option value="<?php echo esc_attr($v); ?>" <?php selected($cond,$v); ?>><?php echo esc_html($l); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<!-- Level 2: sub-condition (shown when all_website) -->
			<div class="st-rf st-l2-wrap" <?php echo $cond==='all_website' ? '' : 'style="display:none"'; ?>>
				<label><?php esc_html_e('Condition','st-elementor'); ?></label>
				<select name="st_hf_rule[<?php echo esc_attr($key); ?>][sub]" class="st-sel st-l2">
					<option value=""><?php esc_html_e('— Select —','st-elementor'); ?></option>
					<?php foreach($l2_opts as $v=>$l): ?>
					<option value="<?php echo esc_attr($v); ?>" <?php selected($sub,$v); ?>><?php echo esc_html($l); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<!-- Post type (shown when archive) -->
			<div class="st-rf st-pt-wrap" <?php echo ($cond==='all_website'&&$sub==='archive') ? '' : 'style="display:none"'; ?>>
				<label><?php esc_html_e('Post Type','st-elementor'); ?></label>
				<select name="st_hf_rule[<?php echo esc_attr($key); ?>][post_type]" class="st-sel">
					<option value=""><?php esc_html_e('All Post Types','st-elementor'); ?></option>
					<?php foreach($pub_types as $pto): ?>
					<option value="<?php echo esc_attr($pto->name); ?>" <?php selected($pt,$pto->name); ?>><?php echo esc_html($pto->label); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<!-- Single sub (shown when single) -->
			<div class="st-rf st-single-wrap" <?php echo ($cond==='all_website'&&$sub==='single') ? '' : 'style="display:none"'; ?>>
				<label><?php esc_html_e('Single Type','st-elementor'); ?></label>
				<select name="st_hf_rule[<?php echo esc_attr($key); ?>][post_type]" class="st-sel">
					<option value=""><?php esc_html_e('All Singles','st-elementor'); ?></option>
					<?php foreach($l3_single_opts as $v=>$l): ?>
					<option value="<?php echo esc_attr($v); ?>" <?php selected($pt,$v); ?>><?php echo esc_html($l); ?></option>
					<?php endforeach; ?>
					<?php foreach($pub_types as $pto): ?>
					<option value="<?php echo esc_attr($pto->name); ?>" <?php selected($pt,$pto->name); ?>><?php echo esc_html($pto->label); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<!-- Specific page (shown when singular) -->
			<div class="st-rf st-page-wrap" <?php echo ($cond==='all_website'&&$sub==='singular') ? '' : 'style="display:none"'; ?>>
				<label><?php esc_html_e('Select Page','st-elementor'); ?></label>
				<select name="st_hf_rule[<?php echo esc_attr($key); ?>][page_id]" class="st-sel">
					<option value="0"><?php esc_html_e('— Select Page —','st-elementor'); ?></option>
					<?php foreach($all_pages as $pg): ?>
					<option value="<?php echo esc_attr($pg->ID); ?>" <?php selected($page_id,$pg->ID); ?>><?php echo esc_html($pg->post_title); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

		</div>
	</div>
	<?php
}
?>

<div class="wrap st-hf-page">
<div class="st-admin-page-header">
    <div>
        <h1><?php esc_html_e( 'Header & Footer', 'st-elementor' ); ?></h1>
        <p><?php esc_html_e( 'Assign Elementor templates as header/footer with display conditions.', 'st-elementor' ); ?></p>
    </div>
</div>

<form method="post">
<?php wp_nonce_field('st_hf_rules_save','st_hf_rules_nonce'); ?>

<div class="st-hf-grid">

	<!-- HEADER -->
	<div class="st-hf-section">
		<div class="st-hf-head" style="border-left:4px solid #0891b2">
			<span class="dashicons dashicons-align-center" style="color:#0891b2"></span>
			<h2><?php esc_html_e('Header Templates','st-elementor'); ?></h2>
		</div>
		<p class="st-hf-desc"><?php esc_html_e('First matching rule wins. Entire Site = fallback.','st-elementor'); ?></p>
		<div id="header-rules-list">
			<?php foreach($header_rules as $i=>$rule): st_hf_rule_row('header','h'.$i,$rule,$templates,$l1_opts,$l2_opts,$l3_single_opts,$pub_types,$all_pages); endforeach; ?>
		</div>
		<button type="button" class="st-add-rule button" data-role="header" data-idx="h">+ <?php esc_html_e('Add Header Rule','st-elementor'); ?></button>
	</div>

	<!-- FOOTER -->
	<div class="st-hf-section">
		<div class="st-hf-head" style="border-left:4px solid #059669">
			<span class="dashicons dashicons-align-center" style="color:#059669"></span>
			<h2><?php esc_html_e('Footer Templates','st-elementor'); ?></h2>
		</div>
		<p class="st-hf-desc"><?php esc_html_e('First matching rule wins. Entire Site = fallback.','st-elementor'); ?></p>
		<div id="footer-rules-list">
			<?php foreach($footer_rules as $i=>$rule): st_hf_rule_row('footer','f'.$i,$rule,$templates,$l1_opts,$l2_opts,$l3_single_opts,$pub_types,$all_pages); endforeach; ?>
		</div>
		<button type="button" class="st-add-rule button" data-role="footer" data-idx="f">+ <?php esc_html_e('Add Footer Rule','st-elementor'); ?></button>
	</div>

</div>

<!-- CSS Selectors -->
<div class="st-hf-selectors">
	<h3><?php esc_html_e('Theme Override (CSS Selectors)','st-elementor'); ?></h3>
	<p><?php esc_html_e('If theme header/footer still shows, add its CSS selector to hide it.','st-elementor'); ?></p>
	<div style="display:flex;gap:20px;flex-wrap:wrap">
		<div>
			<label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px"><?php esc_html_e('Header Selector','st-elementor'); ?></label>
			<input type="text" name="st_header_selector" value="<?php echo esc_attr(get_option('st_header_hide_selector','')); ?>" placeholder=".my-header" style="width:260px;padding:7px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px">
		</div>
		<div>
			<label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px"><?php esc_html_e('Footer Selector','st-elementor'); ?></label>
			<input type="text" name="st_footer_selector" value="<?php echo esc_attr(get_option('st_footer_hide_selector','')); ?>" placeholder=".my-footer" style="width:260px;padding:7px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px">
		</div>
	</div>
</div>

<?php submit_button(__('Save All Settings','st-elementor'),'primary large'); ?>
</form>

<!-- Hidden templates for JS -->
<script type="text/html" id="st-tpl-header">
<?php st_hf_rule_row('header','{{K}}',array(),$templates,$l1_opts,$l2_opts,$l3_single_opts,$pub_types,$all_pages); ?>
</script>
<script type="text/html" id="st-tpl-footer">
<?php st_hf_rule_row('footer','{{K}}',array(),$templates,$l1_opts,$l2_opts,$l3_single_opts,$pub_types,$all_pages); ?>
</script>

</div>

<style>
.st-hf-page{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;max-width:1100px}
.st-hf-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:20px}
@media(max-width:900px){.st-hf-grid{grid-template-columns:1fr}}
.st-hf-section{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:22px}
.st-hf-head{display:flex;align-items:center;gap:10px;margin-bottom:6px;padding-left:10px}
.st-hf-head h2{margin:0;font-size:15px;font-weight:700;color:#111827}
.st-hf-desc{color:#6b7280;font-size:12px;margin:0 0 14px}
.st-rule-row{background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:14px 36px 14px 14px;margin-bottom:10px;position:relative}
.st-rule-inner{display:flex;flex-direction:column;gap:10px}
.st-rf label{display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px}
.st-sel{width:100%;padding:7px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;background:#fff}
.st-sel:focus{border-color:#6366f1;outline:none;box-shadow:0 0 0 2px rgba(99,102,241,.12)}
.st-l2-wrap,.st-pt-wrap,.st-single-wrap,.st-page-wrap{background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px}
.st-l2-wrap .st-rf label,.st-pt-wrap .st-rf label,.st-single-wrap .st-rf label,.st-page-wrap .st-rf label{color:#1d4ed8 !important}
.st-remove-rule{position:absolute;top:10px;right:10px;background:none;border:none;color:#9ca3af;font-size:15px;cursor:pointer;padding:2px 6px;border-radius:4px}
.st-remove-rule:hover{background:#fef2f2;color:#dc2626}
.st-add-rule{margin-top:6px;border-style:dashed !important;color:#6366f1 !important;border-color:#6366f1 !important;background:#fafafe !important}
.st-add-rule:hover{background:#ede9fe !important}
.st-hf-selectors{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:18px 22px;margin-bottom:18px}
.st-hf-selectors h3{margin:0 0 5px;font-size:14px;color:#92400e}
.st-hf-selectors p{margin:0 0 12px;font-size:13px;color:#78350f}
</style>

<script>
(function($){
	var ctrs = {h:<?php echo count($header_rules);?>,f:<?php echo count($footer_rules);?>};

	// L1 change → show/hide L2
	$(document).on('change','.st-l1',function(){
		var $row=$(this).closest('.st-rule-row');
		var show=$(this).val()==='all_website';
		$row.find('.st-l2-wrap').toggle(show);
		if(!show){$row.find('.st-pt-wrap,.st-single-wrap,.st-page-wrap').hide();}
	});

	// L2 change → show/hide sub-fields
	$(document).on('change','.st-l2',function(){
		var $row=$(this).closest('.st-rule-row');
		var v=$(this).val();
		$row.find('.st-pt-wrap').toggle(v==='archive');
		$row.find('.st-single-wrap').toggle(v==='single');
		$row.find('.st-page-wrap').toggle(v==='singular');
	});

	// Remove
	$(document).on('click','.st-remove-rule',function(){
		$(this).closest('.st-rule-row').fadeOut(150,function(){$(this).remove();});
	});

	// Add
	$(document).on('click','.st-add-rule',function(){
		var role=$(this).data('role');
		var idx=$(this).data('idx');
		var key=idx+(ctrs[idx]++);
		var html=$('#st-tpl-'+role).html().replace(/\{\{K\}\}/g,key);
		$('#'+role+'-rules-list').append(html);
	});
})(jQuery);
</script>
