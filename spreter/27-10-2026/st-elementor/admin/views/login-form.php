<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$s = \ST_Elementor\Modules\Login\Login::get_settings();
$saved = isset( $_GET['saved'] ) && '1' === $_GET['saved'];

// Check if a Login template exists
$login_templates = get_posts( array(
	'post_type'      => 'st_template',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'meta_query'     => array( array( 'key' => '_st_template_type', 'value' => 'login' ) ),
) );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Login Form Settings', 'st-elementor' ); ?></h1>
	<p><?php esc_html_e( 'Customize the WordPress login page. Changes apply to the WP login page and the Login Widget on the frontend.', 'st-elementor' ); ?></p>

	<?php if ( $saved ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'st-elementor' ); ?></p></div>
	<?php endif; ?>

	<!-- Login Template Status -->
	<?php
	$login_page_id = (int) get_option( 'st_login_page_id', 0 );
	?>
	<?php if ( ! empty( $login_templates ) ) : ?>
	<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px 20px;margin-bottom:20px">
		<strong style="color:#15803d;font-size:14px">&#10003; <?php esc_html_e( 'Custom Login Template Active', 'st-elementor' ); ?></strong>
		<p style="margin:6px 0 0;font-size:13px;color:#374151">
			<?php esc_html_e( 'Template:', 'st-elementor' ); ?>
			<?php foreach ( $login_templates as $tpl ) :
				$edit_url = add_query_arg( array( 'post' => $tpl->ID, 'action' => 'elementor' ), admin_url( 'post.php' ) );
			?>
			<strong><?php echo esc_html( $tpl->post_title ); ?></strong>
			— <a href="<?php echo esc_url( $edit_url ); ?>" style="color:#6366f1"><?php esc_html_e( 'Edit with Elementor', 'st-elementor' ); ?></a>
			<?php endforeach; ?>
		</p>
		<?php if ( $login_page_id && get_post_status( $login_page_id ) === 'publish' ) : ?>
		<p style="margin:6px 0 0;font-size:13px;color:#374151">
			<?php esc_html_e( 'Login Page:', 'st-elementor' ); ?>
			<a href="<?php echo esc_url( get_permalink( $login_page_id ) ); ?>" target="_blank" style="color:#0891b2">
				<?php echo esc_html( get_the_title( $login_page_id ) ); ?> &#8599;
			</a>
			— <a href="<?php echo esc_url( wp_login_url() ); ?>" target="_blank" style="color:#6b7280;font-size:12px">
				<?php esc_html_e( 'Test wp-login.php redirect', 'st-elementor' ); ?>
			</a>
		</p>
		<?php else : ?>
		<p style="margin:6px 0 0;font-size:12px;color:#d97706">
			&#9888; <?php esc_html_e( 'No login page assigned yet. Visit wp-login.php once to auto-create it, or select one below.', 'st-elementor' ); ?>
		</p>
		<?php endif; ?>
		<div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap">
			<a href="<?php echo esc_url( wp_login_url() ); ?>" target="_blank" class="button button-primary">
				<?php esc_html_e( 'Preview Login Page', 'st-elementor' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=st-elementor-templates&tpl_type=login' ) ); ?>" class="button">
				<?php esc_html_e( 'Manage Login Templates', 'st-elementor' ); ?>
			</a>
		</div>
	</div>
	<?php else : ?>
	<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
		<div>
			<strong style="color:#92400e;font-size:14px"><?php esc_html_e( 'No Login Template Found', 'st-elementor' ); ?></strong>
			<p style="margin:4px 0 0;font-size:13px;color:#374151">
				<?php esc_html_e( 'Create a "Login" type template to replace the default WordPress login page with your custom design.', 'st-elementor' ); ?>
			</p>
		</div>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=st-elementor-templates' ) ); ?>" class="button button-primary">
			+ <?php esc_html_e( 'Create Login Template', 'st-elementor' ); ?>
		</a>
	</div>
	<?php endif; ?>

	<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:13px;color:#374151">
		<strong style="color:#1d4ed8"><?php esc_html_e( 'How it works:', 'st-elementor' ); ?></strong>
		<ol style="margin:6px 0 0;padding-left:18px;line-height:1.8">
			<li><?php esc_html_e( 'Create a "Login" type template in Templates page.', 'st-elementor' ); ?></li>
			<li><?php esc_html_e( 'Design it with Elementor — add the ST Login Form widget.', 'st-elementor' ); ?></li>
			<li><?php esc_html_e( 'Publish the template.', 'st-elementor' ); ?></li>
			<li><?php esc_html_e( 'Visit wp-login.php — it will auto-redirect to your custom login page.', 'st-elementor' ); ?></li>
		</ol>
	</div>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php wp_nonce_field( 'st_login_settings_nonce' ); ?>
		<input type="hidden" name="action" value="st_save_login_settings">

		<table class="form-table" role="presentation">

			<?php
			// Login page selector
			$all_pages     = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
			$login_page_id = (int) get_option( 'st_login_page_id', 0 );
			?>
			<tr><th colspan="2"><h2 style="margin:0;color:#6366f1"><?php esc_html_e( 'Custom Login Page', 'st-elementor' ); ?></h2></th></tr>
			<tr>
				<th><?php esc_html_e( 'Login Page', 'st-elementor' ); ?></th>
				<td>
					<select name="st_login_page_id" style="min-width:280px">
						<option value="0"><?php esc_html_e( '— Use wp-login.php (default) —', 'st-elementor' ); ?></option>
						<?php foreach ( $all_pages as $pg ) : ?>
						<option value="<?php echo esc_attr( $pg->ID ); ?>" <?php selected( $login_page_id, $pg->ID ); ?>>
							<?php echo esc_html( $pg->post_title ); ?>
						</option>
						<?php endforeach; ?>
					</select>
					<p class="description" style="margin-top:6px">
						<?php esc_html_e( 'Select a WordPress page that contains the ST Login Form widget. Users will be redirected here instead of wp-login.php.', 'st-elementor' ); ?>
						<?php if ( $login_page_id ) : ?>
							<br><a href="<?php echo esc_url( get_permalink( $login_page_id ) ); ?>" target="_blank" style="color:#6366f1">
								<?php esc_html_e( 'Preview this page →', 'st-elementor' ); ?>
							</a>
						<?php endif; ?>
					</p>
				</td>
			</tr>

			<tr><th colspan="2"><h2 style="margin:0"><?php esc_html_e( 'Page Background', 'st-elementor' ); ?></h2></th></tr>

			<tr>
				<th><?php esc_html_e( 'Background Color', 'st-elementor' ); ?></th>
				<td><input type="color" name="bg_color" value="<?php echo esc_attr( $s['bg_color'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Background Image URL', 'st-elementor' ); ?></th>
				<td><input type="url" name="bg_image" value="<?php echo esc_attr( $s['bg_image'] ); ?>" class="regular-text" placeholder="https://..."></td>
			</tr>

			<tr><th colspan="2"><h2 style="margin:0"><?php esc_html_e( 'Logo', 'st-elementor' ); ?></h2></th></tr>

			<tr>
				<th><?php esc_html_e( 'Logo Image URL', 'st-elementor' ); ?></th>
				<td><input type="url" name="logo_url" value="<?php echo esc_attr( $s['logo_url'] ); ?>" class="regular-text" placeholder="https://..."></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Logo Width (px)', 'st-elementor' ); ?></th>
				<td><input type="number" name="logo_width" value="<?php echo esc_attr( $s['logo_width'] ); ?>" min="20" max="400" style="width:80px"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Logo Height (px)', 'st-elementor' ); ?></th>
				<td><input type="number" name="logo_height" value="<?php echo esc_attr( $s['logo_height'] ); ?>" min="20" max="400" style="width:80px"></td>
			</tr>

			<tr><th colspan="2"><h2 style="margin:0"><?php esc_html_e( 'Form Box', 'st-elementor' ); ?></h2></th></tr>

			<tr>
				<th><?php esc_html_e( 'Form Background', 'st-elementor' ); ?></th>
				<td><input type="color" name="form_bg" value="<?php echo esc_attr( $s['form_bg'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Border Radius (px)', 'st-elementor' ); ?></th>
				<td><input type="number" name="form_radius" value="<?php echo esc_attr( $s['form_radius'] ); ?>" min="0" max="50" style="width:80px"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Box Shadow', 'st-elementor' ); ?></th>
				<td><label><input type="checkbox" name="form_shadow" value="1" <?php checked( $s['form_shadow'], '1' ); ?>> <?php esc_html_e( 'Enable', 'st-elementor' ); ?></label></td>
			</tr>

			<tr><th colspan="2"><h2 style="margin:0"><?php esc_html_e( 'Labels & Inputs', 'st-elementor' ); ?></h2></th></tr>

			<tr>
				<th><?php esc_html_e( 'Label Color', 'st-elementor' ); ?></th>
				<td><input type="color" name="label_color" value="<?php echo esc_attr( $s['label_color'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Input Background', 'st-elementor' ); ?></th>
				<td><input type="color" name="input_bg" value="<?php echo esc_attr( $s['input_bg'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Input Border Color', 'st-elementor' ); ?></th>
				<td><input type="color" name="input_border" value="<?php echo esc_attr( $s['input_border'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Input Text Color', 'st-elementor' ); ?></th>
				<td><input type="color" name="input_color" value="<?php echo esc_attr( $s['input_color'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Input Border Radius (px)', 'st-elementor' ); ?></th>
				<td><input type="number" name="input_radius" value="<?php echo esc_attr( $s['input_radius'] ); ?>" min="0" max="50" style="width:80px"></td>
			</tr>

			<tr><th colspan="2"><h2 style="margin:0"><?php esc_html_e( 'Button', 'st-elementor' ); ?></h2></th></tr>

			<tr>
				<th><?php esc_html_e( 'Button Background', 'st-elementor' ); ?></th>
				<td><input type="color" name="btn_bg" value="<?php echo esc_attr( $s['btn_bg'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Button Text Color', 'st-elementor' ); ?></th>
				<td><input type="color" name="btn_color" value="<?php echo esc_attr( $s['btn_color'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Button Hover Background', 'st-elementor' ); ?></th>
				<td><input type="color" name="btn_hover_bg" value="<?php echo esc_attr( $s['btn_hover_bg'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Button Border Radius (px)', 'st-elementor' ); ?></th>
				<td><input type="number" name="btn_radius" value="<?php echo esc_attr( $s['btn_radius'] ); ?>" min="0" max="50" style="width:80px"></td>
			</tr>

			<tr><th colspan="2"><h2 style="margin:0"><?php esc_html_e( 'Links & Redirect', 'st-elementor' ); ?></h2></th></tr>

			<tr>
				<th><?php esc_html_e( 'Link Color', 'st-elementor' ); ?></th>
				<td><input type="color" name="link_color" value="<?php echo esc_attr( $s['link_color'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Redirect After Login', 'st-elementor' ); ?></th>
				<td><input type="url" name="redirect_url" value="<?php echo esc_attr( $s['redirect_url'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr( home_url() ); ?>">
				<p class="description"><?php esc_html_e( 'Leave empty to use WordPress default.', 'st-elementor' ); ?></p></td>
			</tr>

		</table>

		<?php submit_button( __( 'Save Settings', 'st-elementor' ) ); ?>
	</form>
</div>
