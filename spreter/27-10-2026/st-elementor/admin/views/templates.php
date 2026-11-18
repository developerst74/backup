<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Show error notice if template creation failed
if ( isset( $_GET['error'] ) && '1' === $_GET['error'] ) {
	echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Failed to create template. Please try again.', 'st-elementor' ) . '</p></div>';
}

// Collect entries
$st_templates = get_posts( [ 'post_type' => 'st_template', 'post_status' => [ 'publish', 'draft' ], 'posts_per_page' => -1, 'orderby' => 'modified', 'order' => 'DESC' ] );
$popups       = get_posts( [ 'post_type' => 'st_popup',    'post_status' => [ 'publish', 'draft' ], 'posts_per_page' => -1, 'orderby' => 'modified', 'order' => 'DESC' ] );

$all_entries = [];
foreach ( $st_templates as $p ) {
$sub = get_post_meta( $p->ID, '_st_template_type', true ) ?: 'template';
$conds = (array) get_post_meta( $p->ID, '_st_display_conditions', true );
$all_entries[] = [ 'id' => $p->ID, 'title' => $p->post_title, 'type' => $sub, 'status' => $p->post_status, 'author' => get_the_author_meta( 'display_name', $p->post_author ), 'created' => $p->post_date, 'modified' => $p->post_modified, 'post_type' => 'st_template', 'conditions' => $conds ];
}
foreach ( $popups as $p ) {
$conds = (array) get_post_meta( $p->ID, '_st_display_conditions', true );
$all_entries[] = [ 'id' => $p->ID, 'title' => $p->post_title, 'type' => 'popup', 'status' => $p->post_status, 'author' => get_the_author_meta( 'display_name', $p->post_author ), 'created' => $p->post_date, 'modified' => $p->post_modified, 'post_type' => 'st_popup', 'conditions' => $conds ];
}
usort(
	$all_entries,
	function( $a, $b ) {
		return strtotime( $b['modified'] ) - strtotime( $a['modified'] );
	}
);

$type_config = [
'template' => [ 'label' => 'Template',    'color' => '#6366f1', 'icon' => 'dashicons-layout',      'desc' => 'A reusable section or page template.' ],
'header'   => [ 'label' => 'Header',      'color' => '#0891b2', 'icon' => 'dashicons-align-center', 'desc' => 'Custom site header replacing the theme header.' ],
'footer'   => [ 'label' => 'Footer',      'color' => '#059669', 'icon' => 'dashicons-align-center', 'desc' => 'Custom site footer replacing the theme footer.' ],
'login'    => [ 'label' => 'Login Form',  'color' => '#7c3aed', 'icon' => 'dashicons-lock',         'desc' => 'Custom login page template.' ],
'popup'    => [ 'label' => 'Popup',       'color' => '#dc2626', 'icon' => 'dashicons-external',     'desc' => 'Popup triggered by conditions or user action.' ],
];

$condition_options = [
'entire_site'  => 'Entire Site',
'all_pages'    => 'All Pages',
'front_page'   => 'Front Page',
'single'       => 'Single Posts',
'single_page'  => 'Single Pages',
'archive'      => 'All Archives',
'category'     => 'Category Archives',
'tag'          => 'Tag Archives',
'search'       => 'Search Results',
'404'          => '404 Page',
'logged_in'    => 'Logged In Users',
'logged_out'   => 'Logged Out Users',
];

$filter_type = isset( $_GET['tpl_type'] ) ? sanitize_text_field( $_GET['tpl_type'] ) : '';
$filtered    = $filter_type
	? array_filter(
		$all_entries,
		function( $e ) use ( $filter_type ) {
			return $e['type'] === $filter_type;
		}
	)
	: $all_entries;
$total_all   = count( $all_entries );
?>
<div class="wrap st-templates-page">

<div class="st-tpl-header">
<div><h1><?php esc_html_e( 'All Templates', 'st-elementor' ); ?></h1><p><?php esc_html_e( 'All Elementor-designed templates in one place.', 'st-elementor' ); ?></p></div>
<button class="button button-primary st-open-modal" id="st-add-template-btn">+ <?php esc_html_e( 'Add Template', 'st-elementor' ); ?></button>
</div>

<div class="st-tpl-tabs">
<?php
$base = admin_url( 'admin.php?page=st-elementor-templates' );
$tabs = array( '' => 'All' ) + array_map(
	function( $c ) {
		return $c['label'];
	},
	$type_config
);
foreach ( $tabs as $k => $lbl ) :
$cnt = $k ? count(
	array_filter(
		$all_entries,
		function( $e ) use ( $k ) {
			return $e['type'] === $k;
		}
	)
) : $total_all;
$url = $k ? add_query_arg( 'tpl_type', $k, $base ) : $base;
$act = $filter_type === $k ? 'st-tab-active' : '';
?>
<a href="<?php echo esc_url($url); ?>" class="st-tpl-tab <?php echo esc_attr($act); ?>"><?php echo esc_html($lbl); ?> <span class="st-tab-count"><?php echo (int)$cnt; ?></span></a>
<?php endforeach; ?>
</div>

<?php if ( empty( $filtered ) ) : ?>
<div class="st-tpl-empty">
<span class="dashicons dashicons-layout"></span>
<h3><?php esc_html_e( 'No templates found', 'st-elementor' ); ?></h3>
<p><?php esc_html_e( 'Click "Add Template" to create your first one.', 'st-elementor' ); ?></p>
<button class="button button-primary button-large st-open-modal"><?php esc_html_e( 'Add Template', 'st-elementor' ); ?></button>
</div>
<?php else : ?>
<table class="st-tpl-table widefat">
<thead><tr>
<th class="col-name"><?php esc_html_e('Template Name','st-elementor'); ?></th>
<th class="col-type"><?php esc_html_e('Type','st-elementor'); ?></th>
<th class="col-cond"><?php esc_html_e('Conditions','st-elementor'); ?></th>
<th class="col-status"><?php esc_html_e('Status','st-elementor'); ?></th>
<th class="col-author"><?php esc_html_e('Author','st-elementor'); ?></th>
<th class="col-created"><?php esc_html_e('Created','st-elementor'); ?></th>
<th class="col-updated"><?php esc_html_e('Updated','st-elementor'); ?></th>
<th class="col-shortcode"><?php esc_html_e('Shortcode','st-elementor'); ?></th>
<th class="col-actions"><?php esc_html_e('Actions','st-elementor'); ?></th>
</tr></thead>
<tbody>
<?php foreach ( $filtered as $e ) :
$cfg   = $type_config[ $e['type'] ] ?? $type_config['template'];
$elurl = add_query_arg( [ 'post' => $e['id'], 'action' => 'elementor' ], admin_url('post.php') );
$delurl= get_delete_post_link( $e['id'] );
$slbl  = 'publish' === $e['status'] ? 'Published' : 'Draft';
$sclr  = 'publish' === $e['status'] ? '#059669' : '#d97706';
$sc    = 'st_template' === $e['post_type'] ? '[st_template id="'.$e['id'].'"]' : '—';

// For header/footer: read conditions from st_hf_rules
$is_hf = in_array( $e['type'], array( 'header', 'footer' ), true );
$cond_tag = '—';

if ( $is_hf ) {
	$hf_rules = get_option( 'st_hf_rules', array() );
	foreach ( $hf_rules as $r ) {
		if ( (int)($r['template_id']??0) === $e['id'] ) {
			$cond  = $r['condition'] ?? 'entire_site';
			$sub   = $r['sub']       ?? '';
			$pt    = $r['post_type'] ?? '';
			$pg_id = (int)($r['page_id'] ?? 0);
			$sub_labels = array( 'all_pages'=>'All Pages','archive'=>'Archive','single'=>'Single','singular'=>'Singular','search'=>'Search','404'=>'404','logged_in'=>'Logged In','logged_out'=>'Logged Out' );
			if ( 'entire_site' === $cond ) {
				$cond_tag = 'Entire Site';
			} else {
				$cond_tag = $sub_labels[$sub] ?? $sub;
				if ( $pt ) { $pto = get_post_type_object($pt); $cond_tag .= ' — '.($pto?$pto->label:$pt); }
				if ( $pg_id ) { $pg = get_post($pg_id); if($pg) $cond_tag .= ' — '.$pg->post_title; }
			}
			break;
		}
	}
} else {
	$saved_cond = get_post_meta( $e['id'], '_st_display_conditions', true );
	if ( is_array( $saved_cond ) && ! empty( $saved_cond['condition'] ) ) {
		$cond  = $saved_cond['condition'];
		$sub   = $saved_cond['sub']       ?? '';
		$pt    = $saved_cond['post_type'] ?? '';
		$pg_id = (int)($saved_cond['page_id'] ?? 0);
		$sub_labels = array( 'all_pages'=>'All Pages','archive'=>'Archive','single'=>'Single','singular'=>'Singular','search'=>'Search','404'=>'404','logged_in'=>'Logged In','logged_out'=>'Logged Out' );
		if ( 'entire_site' === $cond ) {
			$cond_tag = 'Entire Site';
		} else {
			$cond_tag = $sub_labels[$sub] ?? $sub;
			if ( $pt ) { $pto = get_post_type_object($pt); $cond_tag .= ' — '.($pto?$pto->label:$pt); }
			if ( $pg_id ) { $pg = get_post($pg_id); if($pg) $cond_tag .= ' — '.$pg->post_title; }
		}
	} elseif ( is_array( $saved_cond ) && ! empty( $saved_cond ) ) {
		$labels = array();
		foreach ( $saved_cond as $c ) {
			$c = sanitize_key( $c );
			if ( isset( $condition_options[ $c ] ) ) {
				$labels[] = $condition_options[ $c ];
			} elseif ( $c ) {
				$labels[] = ucwords( str_replace( array( '_', '-' ), ' ', $c ) );
			}
		}
		$labels = array_values( array_unique( array_filter( $labels ) ) );
		if ( ! empty( $labels ) ) {
			$cond_tag = implode( ', ', $labels );
		}
	}
}
?>
<tr>
<td class="col-name"><div class="st-tpl-name"><span class="dashicons <?php echo esc_attr($cfg['icon']); ?>" style="color:<?php echo esc_attr($cfg['color']); ?>"></span><strong><a href="<?php echo esc_url($elurl); ?>"><?php echo esc_html( $e['title'] ?: '(no title)' ); ?></a></strong></div></td>
<td class="col-type"><span class="st-type-badge" style="background:<?php echo esc_attr($cfg['color']); ?>1a;color:<?php echo esc_attr($cfg['color']); ?>;border:1px solid <?php echo esc_attr($cfg['color']); ?>44"><?php echo esc_html($cfg['label']); ?></span></td>
<td class="col-cond">
<?php if ( $cond_tag !== '—' ) : ?>
	<span class="st-cond-tag"><?php echo esc_html($cond_tag); ?></span>
	<?php if ( $is_hf ) : ?>
		<a href="<?php echo esc_url( admin_url('admin.php?page=st-elementor-hf') ); ?>" style="font-size:11px;color:#6366f1;display:block;margin-top:3px"><?php esc_html_e('Edit in H&F','st-elementor'); ?></a>
	<?php endif; ?>
<?php else : ?>
	<span style="color:#9ca3af;font-size:12px">—</span>
<?php endif; ?>
</td>
<td class="col-status"><span class="st-status-badge" style="background:<?php echo esc_attr($sclr); ?>1a;color:<?php echo esc_attr($sclr); ?>"><?php echo esc_html($slbl); ?></span></td>
<td class="col-author"><div class="st-author"><?php echo get_avatar( get_post_field('post_author',$e['id']), 22, '', '', ['class'=>'st-avatar'] ); ?><?php echo esc_html($e['author']); ?></div></td>
<td class="col-created"><?php echo esc_html( date_i18n('M j, Y', strtotime($e['created'])) ); ?></td>
<td class="col-updated"><?php echo esc_html( date_i18n('M j, Y g:i a', strtotime($e['modified'])) ); ?></td>
<td class="col-shortcode"><?php if('—'!==$sc): ?><code class="st-shortcode" onclick="navigator.clipboard.writeText(this.innerText);this.classList.add('copied')" title="Click to copy"><?php echo esc_html($sc); ?></code><?php else: ?><span style="color:#9ca3af">—</span><?php endif; ?></td>
<td class="col-actions"><div class="st-row-actions">
<a href="<?php echo esc_url($elurl); ?>" class="st-btn-edit"><span class="dashicons dashicons-edit"></span><?php esc_html_e('Edit','st-elementor'); ?></a>
<?php if ( ! $is_hf ) : ?>
<button type="button" class="st-btn-cond"
	data-id="<?php echo esc_attr($e['id']); ?>"
	data-conds="<?php
		$saved_cond = get_post_meta( $e['id'], '_st_display_conditions', true );
		echo esc_attr( wp_json_encode( is_array($saved_cond) ? $saved_cond : array() ) );
	?>"
	data-title="<?php echo esc_attr( $e['title'] ); ?>">
	<span class="dashicons dashicons-filter"></span><?php esc_html_e('Conditions','st-elementor'); ?>
</button>
<?php endif; ?>
<a href="<?php echo esc_url($delurl); ?>" class="st-btn-delete" onclick="return confirm('Delete this template?')"><span class="dashicons dashicons-trash"></span><?php esc_html_e('Delete','st-elementor'); ?></a>
</div></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<p class="st-tpl-count"><?php printf( esc_html__('Showing %d entries','st-elementor'), count($filtered) ); ?></p>
<?php endif; ?>

<!-- ═══ CREATE TEMPLATE MODAL ═══════════════════════════════════════════ -->
<div id="st-create-modal" class="st-modal-overlay" style="display:none">
<div class="st-modal-box">
<div class="st-modal-head">
<h2><?php esc_html_e('Create New Template','st-elementor'); ?></h2>
<button class="st-modal-close" id="st-modal-close">&#x2715;</button>
</div>

<form method="post" id="st-create-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<?php wp_nonce_field('st_create_template','st_create_template_nonce'); ?>
<input type="hidden" name="action" value="st_create_template">

<!-- Step 1: Choose Type -->
<div class="st-modal-step" id="step-type">
<p class="st-modal-label"><?php esc_html_e('What do you want to create?','st-elementor'); ?></p>
<div class="st-type-grid">
<?php foreach ( $type_config as $key => $cfg ) : ?>
<label class="st-type-card" data-type="<?php echo esc_attr($key); ?>">
<input type="radio" name="tpl_type" value="<?php echo esc_attr($key); ?>" style="display:none">
<span class="st-type-icon" style="background:<?php echo esc_attr($cfg['color']); ?>1a;color:<?php echo esc_attr($cfg['color']); ?>">
<span class="dashicons <?php echo esc_attr($cfg['icon']); ?>"></span>
</span>
<span class="st-type-name"><?php echo esc_html($cfg['label']); ?></span>
<span class="st-type-desc"><?php echo esc_html($cfg['desc']); ?></span>
</label>
<?php endforeach; ?>
</div>
</div>

<!-- Step 2: Name + Conditions -->
<div class="st-modal-step" id="step-details" style="display:none">
<div class="st-modal-field">
<label class="st-modal-label"><?php esc_html_e('Template Name','st-elementor'); ?></label>
<input type="text" name="tpl_title" id="tpl_title" class="st-modal-input" placeholder="<?php esc_attr_e('e.g. My Header','st-elementor'); ?>" required>
</div>

<div class="st-modal-field" id="conditions-wrap">
<label class="st-modal-label"><?php esc_html_e('Display Conditions','st-elementor'); ?></label>
<p class="st-modal-hint"><?php esc_html_e('Where should this template appear?','st-elementor'); ?></p>

<!-- Level 1 -->
<div class="st-cm-field">
	<label class="st-cm-label"><?php esc_html_e('Display On','st-elementor'); ?></label>
	<select name="tpl_condition_l1" id="tpl-cond-l1" class="st-modal-input" style="padding:9px 12px">
		<option value="entire_site"><?php esc_html_e('Entire Site','st-elementor'); ?></option>
		<option value="all_website"><?php esc_html_e('All Website','st-elementor'); ?></option>
	</select>
</div>

<!-- Level 2 (shown when all_website) -->
<div class="st-cm-field st-cm-sub" id="tpl-cond-l2-wrap" style="display:none">
	<label class="st-cm-label"><?php esc_html_e('Condition','st-elementor'); ?></label>
	<select name="tpl_condition_l2" id="tpl-cond-l2" class="st-modal-input" style="padding:9px 12px">
		<option value=""><?php esc_html_e('— Select —','st-elementor'); ?></option>
		<option value="all_pages"><?php esc_html_e('All Pages','st-elementor'); ?></option>
		<option value="archive"><?php esc_html_e('Archive','st-elementor'); ?></option>
		<option value="single"><?php esc_html_e('Single','st-elementor'); ?></option>
		<option value="singular"><?php esc_html_e('Singular (Specific Page)','st-elementor'); ?></option>
		<option value="search"><?php esc_html_e('Search Results','st-elementor'); ?></option>
		<option value="404"><?php esc_html_e('404 Page','st-elementor'); ?></option>
		<option value="logged_in"><?php esc_html_e('Logged In Users','st-elementor'); ?></option>
		<option value="logged_out"><?php esc_html_e('Logged Out Users','st-elementor'); ?></option>
	</select>
</div>

<!-- Specific page (shown when singular) -->
<div class="st-cm-field st-cm-sub" id="tpl-cond-page-wrap" style="display:none">
	<label class="st-cm-label"><?php esc_html_e('Select Page','st-elementor'); ?></label>
	<select name="tpl_condition_page_id" id="tpl-cond-page" class="st-modal-input" style="padding:9px 12px">
		<option value="0"><?php esc_html_e('— Select Page —','st-elementor'); ?></option>
		<?php
		$_pages = get_posts( [ 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ] );
		foreach ( $_pages as $_pg ) :
		?>
		<option value="<?php echo esc_attr( $_pg->ID ); ?>"><?php echo esc_html( $_pg->post_title ); ?></option>
		<?php endforeach; ?>
	</select>
</div>

<!-- Hidden fields for form submission -->
<input type="hidden" name="tpl_conditions[]" id="tpl-cond-hidden" value="entire_site">
</div>

<script>
(function($){
	$('#tpl-cond-l1').on('change', function(){
		var v = $(this).val();
		$('#tpl-cond-l2-wrap').toggle(v === 'all_website');
		if(v !== 'all_website'){
			$('#tpl-cond-page-wrap').hide();
			$('#tpl-cond-hidden').val('entire_site');
		}
	});
	$('#tpl-cond-l2').on('change', function(){
		var v = $(this).val();
		$('#tpl-cond-page-wrap').toggle(v === 'singular');
		$('#tpl-cond-hidden').val($('#tpl-cond-l1').val() === 'all_website' && v ? v : 'entire_site');
	});
	$('#tpl-cond-page').on('change', function(){
		// page_id stored separately
	});
})(jQuery);
</script>
</div>

<div class="st-modal-footer">
<button type="button" class="button" id="st-modal-back" style="display:none"><?php esc_html_e('Back','st-elementor'); ?></button>
<button type="button" class="button button-primary" id="st-modal-next"><?php esc_html_e('Next','st-elementor'); ?></button>
<button type="submit" class="button button-primary" id="st-modal-create" style="display:none"><?php esc_html_e('Create & Edit','st-elementor'); ?></button>
</div>
</form>
</div>
</div>

</div><!-- .wrap -->

<!-- ═══ CONDITIONS MODAL ════════════════════════════════════════════════ -->
<div id="st-cond-modal" class="st-modal-overlay" style="display:none" role="dialog" aria-modal="true">
<div class="st-modal-box" style="max-width:520px">

	<div class="st-modal-head">
		<div>
			<h2 style="margin:0;font-size:17px"><?php esc_html_e('Display Conditions','st-elementor'); ?></h2>
			<p id="st-cond-modal-subtitle" style="margin:4px 0 0;font-size:13px;color:#6b7280"></p>
		</div>
		<button class="st-modal-close" id="st-cond-modal-close">&#x2715;</button>
	</div>

	<div class="st-modal-step" style="padding:24px 28px">
		<p style="margin:0 0 16px;font-size:13px;color:#374151"><?php esc_html_e('Where should this template appear?','st-elementor'); ?></p>

		<!-- L1: Entire Site / All Website -->
		<div class="st-cm-field">
			<label class="st-cm-label"><?php esc_html_e('Display On','st-elementor'); ?></label>
			<select id="st-cm-l1" class="st-cm-sel">
				<option value="entire_site"><?php esc_html_e('Entire Site','st-elementor'); ?></option>
				<option value="all_website"><?php esc_html_e('All Website','st-elementor'); ?></option>
			</select>
		</div>

		<!-- L2: sub-condition (shown when all_website) -->
		<div class="st-cm-field st-cm-sub" id="st-cm-l2-wrap" style="display:none">
			<label class="st-cm-label"><?php esc_html_e('Condition','st-elementor'); ?></label>
			<select id="st-cm-l2" class="st-cm-sel">
				<option value=""><?php esc_html_e('— Select —','st-elementor'); ?></option>
				<option value="all_pages"><?php esc_html_e('All Pages','st-elementor'); ?></option>
				<option value="archive"><?php esc_html_e('Archive','st-elementor'); ?></option>
				<option value="single"><?php esc_html_e('Single','st-elementor'); ?></option>
				<option value="singular"><?php esc_html_e('Singular (Specific Page)','st-elementor'); ?></option>
				<option value="search"><?php esc_html_e('Search Results','st-elementor'); ?></option>
				<option value="404"><?php esc_html_e('404 Page','st-elementor'); ?></option>
				<option value="logged_in"><?php esc_html_e('Logged In Users','st-elementor'); ?></option>
				<option value="logged_out"><?php esc_html_e('Logged Out Users','st-elementor'); ?></option>
			</select>
		</div>

		<!-- Post type (shown when archive or single) -->
		<div class="st-cm-field st-cm-sub" id="st-cm-pt-wrap" style="display:none">
			<label class="st-cm-label"><?php esc_html_e('Post Type','st-elementor'); ?></label>
			<select id="st-cm-pt" class="st-cm-sel">
				<option value=""><?php esc_html_e('All Post Types','st-elementor'); ?></option>
				<?php
				$pub_types = get_post_types( array( 'public' => true ), 'objects' );
				unset( $pub_types['attachment'], $pub_types['st_template'], $pub_types['st_popup'] );
				foreach ( $pub_types as $pt ) :
				?>
				<option value="<?php echo esc_attr($pt->name); ?>"><?php echo esc_html($pt->label); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<!-- Single sub-type (shown when single) -->
		<div class="st-cm-field st-cm-sub" id="st-cm-single-wrap" style="display:none">
			<label class="st-cm-label"><?php esc_html_e('Single Type','st-elementor'); ?></label>
			<select id="st-cm-single" class="st-cm-sel">
				<option value=""><?php esc_html_e('All Singles','st-elementor'); ?></option>
				<option value="single_post"><?php esc_html_e('Single Post','st-elementor'); ?></option>
				<option value="single_page"><?php esc_html_e('Single Page','st-elementor'); ?></option>
				<?php foreach ( $pub_types as $pt ) : ?>
				<option value="<?php echo esc_attr($pt->name); ?>"><?php echo esc_html($pt->label); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<!-- Specific page (shown when singular) -->
		<div class="st-cm-field st-cm-sub" id="st-cm-page-wrap" style="display:none">
			<label class="st-cm-label"><?php esc_html_e('Select Page','st-elementor'); ?></label>
			<select id="st-cm-page" class="st-cm-sel">
				<option value="0"><?php esc_html_e('— Select Page —','st-elementor'); ?></option>
				<?php
				$all_pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
				foreach ( $all_pages as $pg ) :
				?>
				<option value="<?php echo esc_attr($pg->ID); ?>"><?php echo esc_html($pg->post_title); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div id="st-cond-save-msg" style="display:none;margin-top:16px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;color:#15803d;font-size:13px">
			&#10003; <?php esc_html_e('Conditions saved.','st-elementor'); ?>
		</div>
	</div>

	<div class="st-modal-footer">
		<button type="button" class="button" id="st-cond-cancel"><?php esc_html_e('Cancel','st-elementor'); ?></button>
		<button type="button" class="button button-primary" id="st-cond-save">
			<span class="st-cond-save-txt"><?php esc_html_e('Save Conditions','st-elementor'); ?></span>
			<span class="st-cond-saving" style="display:none"><?php esc_html_e('Saving…','st-elementor'); ?></span>
		</button>
	</div>

</div>
</div>

<style>
.st-templates-page{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
.st-tpl-header{display:flex;align-items:flex-start;justify-content:space-between;margin:20px 0 16px;flex-wrap:wrap;gap:12px}
.st-tpl-header h1{margin:0;font-size:22px;font-weight:700;color:#111827}
.st-tpl-header p{margin:4px 0 0;color:#6b7280;font-size:13px}
.st-tpl-tabs{display:flex;gap:4px;margin-bottom:16px;border-bottom:2px solid #e5e7eb}
.st-tpl-tab{padding:8px 14px;font-size:13px;font-weight:500;color:#6b7280;text-decoration:none;border-radius:6px 6px 0 0;border:1px solid transparent;border-bottom:none;display:flex;align-items:center;gap:6px;transition:all .15s}
.st-tpl-tab:hover{color:#6366f1;background:#f5f3ff}
.st-tpl-tab.st-tab-active{color:#6366f1;background:#fff;border-color:#e5e7eb;border-bottom-color:#fff;margin-bottom:-2px}
.st-tab-count{background:#f3f4f6;color:#374151;padding:1px 7px;border-radius:20px;font-size:11px;font-weight:600}
.st-tab-active .st-tab-count{background:#ede9fe;color:#6366f1}
.st-tpl-table{border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;border-collapse:collapse}
.st-tpl-table thead tr{background:#f9fafb}
.st-tpl-table th{padding:12px 14px;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e5e7eb}
.st-tpl-table td{padding:11px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle}
.st-tpl-table tbody tr:last-child td{border-bottom:none}
.st-tpl-table tbody tr:hover td{background:#fafafa}
.col-name{width:20%}.col-type{width:8%}.col-cond{width:14%}.col-status{width:8%}.col-author{width:11%}.col-created{width:9%}.col-updated{width:13%}.col-shortcode{width:12%}.col-actions{width:5%}
.st-tpl-name{display:flex;align-items:center;gap:8px}
.st-tpl-name .dashicons{font-size:18px;width:18px;height:18px;flex-shrink:0}
.st-tpl-name a{color:#111827;text-decoration:none;font-weight:600}
.st-tpl-name a:hover{color:#6366f1}
.st-type-badge,.st-status-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.st-cond-tag{display:inline-block;background:#f1f5f9;color:#374151;padding:2px 7px;border-radius:4px;font-size:11px;margin:2px 2px 0 0}
.st-author{display:flex;align-items:center;gap:6px}
.st-avatar{border-radius:50%;width:22px;height:22px}
.st-shortcode{background:#f1f5f9;padding:3px 8px;border-radius:4px;font-size:11px;cursor:pointer;display:inline-block;transition:background .15s;user-select:all}
.st-shortcode:hover{background:#e0e7ff}
.st-shortcode.copied{background:#d1fae5;color:#059669}
.st-row-actions{display:flex;gap:5px;align-items:center;flex-wrap:wrap}
.st-btn-edit,.st-btn-delete,.st-btn-cond{display:inline-flex;align-items:center;gap:4px;padding:4px 9px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s;cursor:pointer;border:none}
.st-btn-edit{background:#ede9fe;color:#6366f1}
.st-btn-edit:hover{background:#6366f1;color:#fff}
.st-btn-cond{background:#e0f2fe;color:#0369a1}
.st-btn-cond:hover{background:#0369a1;color:#fff}
.st-btn-delete{background:#fef2f2;color:#dc2626}
.st-btn-delete:hover{background:#dc2626;color:#fff}
.st-btn-edit .dashicons,.st-btn-delete .dashicons,.st-btn-cond .dashicons{font-size:14px;width:14px;height:14px}
.st-tpl-table th{padding:12px 14px;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e5e7eb}
.st-tpl-table td{padding:11px 14px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle}
.st-tpl-table tbody tr:last-child td{border-bottom:none}
.st-tpl-table tbody tr:hover td{background:#fafafa}
.col-name{width:20%}.col-type{width:8%}.col-cond{width:14%}.col-status{width:8%}.col-author{width:11%}.col-created{width:9%}.col-updated{width:13%}.col-shortcode{width:12%}.col-actions{width:5%}
.st-tpl-name{display:flex;align-items:center;gap:8px}
.st-tpl-name .dashicons{font-size:18px;width:18px;height:18px;flex-shrink:0}
.st-tpl-name a{color:#111827;text-decoration:none;font-weight:600}
.st-tpl-name a:hover{color:#6366f1}
.st-type-badge,.st-status-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.st-cond-tag{display:inline-block;background:#f1f5f9;color:#374151;padding:2px 7px;border-radius:4px;font-size:11px;margin:2px 2px 0 0}
.st-author{display:flex;align-items:center;gap:6px}
.st-avatar{border-radius:50%;width:22px;height:22px}
.st-shortcode{background:#f1f5f9;padding:3px 8px;border-radius:4px;font-size:11px;cursor:pointer;display:inline-block;transition:background .15s;user-select:all}
.st-shortcode:hover{background:#e0e7ff}
.st-shortcode.copied{background:#d1fae5;color:#059669}
.st-row-actions{display:flex;gap:6px;align-items:center}
.st-btn-edit,.st-btn-delete{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s}
.st-btn-edit{background:#ede9fe;color:#6366f1}
.st-btn-edit:hover{background:#6366f1;color:#fff}
.st-btn-delete{background:#fef2f2;color:#dc2626}
.st-btn-delete:hover{background:#dc2626;color:#fff}
.st-btn-edit .dashicons,.st-btn-delete .dashicons{font-size:14px;width:14px;height:14px}
.st-tpl-empty{text-align:center;padding:60px 40px;background:#fff;border:2px dashed #d1d5db;border-radius:12px}
.st-tpl-empty .dashicons{font-size:48px;color:#d1d5db;display:block;margin-bottom:12px}
.st-tpl-empty h3{color:#374151;margin:0 0 8px}
.st-tpl-empty p{color:#6b7280;margin:0 0 20px}
.st-tpl-count{color:#9ca3af;font-size:12px;margin-top:10px}
/* Modal */
.st-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:99999;display:flex;align-items:center;justify-content:center}
.st-modal-box{background:#fff;border-radius:14px;width:680px;max-width:95vw;max-height:90vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,.2)}
.st-modal-head{display:flex;align-items:center;justify-content:space-between;padding:22px 28px 0;border-bottom:1px solid #f3f4f6;padding-bottom:16px}
.st-modal-head h2{margin:0;font-size:18px;font-weight:700;color:#111827}
.st-modal-close{background:none;border:none;font-size:18px;cursor:pointer;color:#6b7280;padding:4px 8px;border-radius:6px;line-height:1}
.st-modal-close:hover{background:#f3f4f6;color:#111827}
.st-modal-step{padding:24px 28px}
.st-modal-label{font-size:13px;font-weight:600;color:#374151;margin:0 0 14px;display:block}
.st-modal-hint{font-size:12px;color:#6b7280;margin:-8px 0 12px}
.st-modal-field{margin-bottom:20px}
.st-modal-input{width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;box-sizing:border-box;outline:none;transition:border .15s}
.st-modal-input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.12)}
/* Type grid */
.st-type-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.st-type-card{display:flex;flex-direction:column;align-items:center;padding:18px 12px;border:2px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:all .15s;text-align:center}
.st-type-card:hover{border-color:#6366f1;background:#fafafe}
.st-type-card.selected{border-color:#6366f1;background:#f5f3ff}
.st-type-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:10px}
.st-type-icon .dashicons{font-size:24px;width:24px;height:24px}
.st-type-name{font-size:13px;font-weight:700;color:#111827;margin-bottom:4px}
.st-type-desc{font-size:11px;color:#6b7280;line-height:1.4}
/* Conditions */
.st-cond-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.st-cond-item{display:flex;align-items:center;gap:8px;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer;font-size:13px;color:#374151;transition:all .15s}
.st-cond-item:hover{border-color:#6366f1;background:#fafafe}
.st-cond-item input:checked + span{color:#6366f1;font-weight:600}
.st-cond-item:has(input:checked),
.st-cond-item.st-cond-active{border-color:#6366f1;background:#f5f3ff}
/* Modal footer */
.st-modal-footer{padding:16px 28px 24px;display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #f3f4f6}
/* Conditions modal fields */
.st-cm-field{margin-bottom:14px}
.st-cm-label{display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px}
.st-cm-sel{width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;background:#fff;color:#111827}
.st-cm-sel:focus{border-color:#6366f1;outline:none;box-shadow:0 0 0 2px rgba(99,102,241,.12)}
.st-cm-sub{background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px;margin-top:4px}
.st-cm-sub .st-cm-label{color:#1d4ed8 !important}
</style>

<script>
(function($){
// Open modal
$(document).on('click','.st-open-modal, #st-add-template-btn',function(e){
e.preventDefault();
$('#st-create-modal').fadeIn(150);
resetModal();
});
// Close
$('#st-modal-close').on('click',function(){ $('#st-create-modal').fadeOut(150); });
$('#st-create-modal').on('click',function(e){ if($(e.target).is('#st-create-modal')) $(this).fadeOut(150); });

function resetModal(){
$('#step-type').show();
$('#step-details').hide();
$('#st-modal-next').show();
$('#st-modal-create').hide();
$('#st-modal-back').hide();
$('.st-type-card').removeClass('selected');
$('input[name="tpl_type"]').prop('checked',false);
$('#tpl_title').val('');
$('input[name="tpl_conditions[]"]').prop('checked',false);
}

// Select type card
$(document).on('click','.st-type-card',function(){
$('.st-type-card').removeClass('selected');
$(this).addClass('selected');
$(this).find('input[type=radio]').prop('checked',true);
var type = $(this).data('type');
// Auto-fill title placeholder
$('#tpl_title').attr('placeholder','e.g. My '+$(this).find('.st-type-name').text());
// Hide conditions for login/header/footer (they have fixed display)
if(['header','footer','login'].indexOf(type)>=0){
$('#conditions-wrap').hide();
} else {
$('#conditions-wrap').show();
}
});

// Next step
$('#st-modal-next').on('click',function(){
if(!$('input[name="tpl_type"]:checked').val()){
alert('<?php echo esc_js(__("Please select a template type.","st-elementor")); ?>');
return;
}
$('#step-type').hide();
$('#step-details').show();
$('#st-modal-next').hide();
$('#st-modal-create').show();
$('#st-modal-back').show();
$('#tpl_title').focus();
});

// Back
$('#st-modal-back').on('click',function(){
$('#step-details').hide();
$('#step-type').show();
$('#st-modal-next').show();
$('#st-modal-create').hide();
$('#st-modal-back').hide();
});

// Submit validation
$('#st-create-form').on('submit',function(){
if(!$('#tpl_title').val().trim()){
$('#tpl_title').focus();
return false;
}
});
})(jQuery);
</script>

<script>
// ── Conditions Modal ─────────────────────────────────────────────────
(function($){
	var currentId   = 0;
	var currentData = {};

	// Open modal
	$(document).on('click', '.st-btn-cond', function(){
		currentId   = $(this).data('id');
		var title   = $(this).data('title') || '';
		var conds   = $(this).data('conds') || {};
		if (typeof conds === 'string') { try { conds = JSON.parse(conds); } catch(e){ conds = {}; } }
		currentData = conds;

		$('#st-cond-modal-subtitle').text(title ? '"' + title + '"' : '');

		// Restore saved values
		var l1 = conds.condition || 'entire_site';
		var l2 = conds.sub       || '';
		var pt = conds.post_type || '';
		var pg = conds.page_id   || 0;

		$('#st-cm-l1').val(l1);
		$('#st-cm-l2').val(l2);
		$('#st-cm-pt').val(pt);
		$('#st-cm-single').val(pt);
		$('#st-cm-page').val(pg);

		// Show/hide sub-fields
		updateCondVisibility(l1, l2);

		$('#st-cond-save-msg').hide();
		$('#st-cond-modal').fadeIn(150);
	});

	function updateCondVisibility(l1, l2) {
		$('#st-cm-l2-wrap').toggle(l1 === 'all_website');
		$('#st-cm-pt-wrap').toggle(l1 === 'all_website' && l2 === 'archive');
		$('#st-cm-single-wrap').toggle(l1 === 'all_website' && l2 === 'single');
		$('#st-cm-page-wrap').toggle(l1 === 'all_website' && l2 === 'singular');
	}

	// L1 change
	$(document).on('change', '#st-cm-l1', function(){
		updateCondVisibility($(this).val(), $('#st-cm-l2').val());
	});

	// L2 change
	$(document).on('change', '#st-cm-l2', function(){
		updateCondVisibility($('#st-cm-l1').val(), $(this).val());
	});

	// Close
	function closeCondModal(){
		$('#st-cond-modal').fadeOut(150);
		currentId = 0;
	}
	$('#st-cond-modal-close, #st-cond-cancel').on('click', closeCondModal);
	$('#st-cond-modal').on('click', function(e){
		if ($(e.target).is('#st-cond-modal')) closeCondModal();
	});

	// Save via AJAX
	$('#st-cond-save').on('click', function(){
		if (!currentId) return;

		var l1 = $('#st-cm-l1').val();
		var l2 = $('#st-cm-l2').val();
		var pt = '';
		var pg = 0;

		if (l1 === 'all_website') {
			if (l2 === 'archive')  pt = $('#st-cm-pt').val();
			if (l2 === 'single')   pt = $('#st-cm-single').val();
			if (l2 === 'singular') pg = parseInt($('#st-cm-page').val(), 10) || 0;
		}

		var condData = { condition: l1, sub: l2, post_type: pt, page_id: pg };

		var $btn = $(this);
		$btn.find('.st-cond-save-txt').hide();
		$btn.find('.st-cond-saving').show();
		$btn.prop('disabled', true);

		$.post(ajaxurl, {
			action:    'st_save_template_conditions',
			post_id:   currentId,
			cond_data: condData,
			nonce:     '<?php echo esc_js( wp_create_nonce( "st_save_conditions" ) ); ?>'
		}, function(res){
			$btn.find('.st-cond-save-txt').show();
			$btn.find('.st-cond-saving').hide();
			$btn.prop('disabled', false);

			if (res.success) {
				$('#st-cond-save-msg').fadeIn(200);

				// Update tag in table
				var $row = $('[data-id="' + currentId + '"]').closest('tr');
				var label = res.data.label || '';
				$row.find('.col-cond').html(label
					? '<span class="st-cond-tag">' + $('<div>').text(label).html() + '</span>'
					: '<span style="color:#9ca3af;font-size:12px">—</span>'
				);

				// Update button data
				$('[data-id="' + currentId + '"]').data('conds', condData);

				setTimeout(function(){ closeCondModal(); }, 700);
			}
		});
	});
})(jQuery);
</script>
