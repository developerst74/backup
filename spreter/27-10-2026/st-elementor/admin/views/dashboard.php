<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
// Quick stats
$template_count = wp_count_posts( 'st_template' );
$popup_count    = wp_count_posts( 'st_popup' );
$tpl_total      = ( $template_count->publish ?? 0 ) + ( $template_count->draft ?? 0 );
$pop_total      = ( $popup_count->publish ?? 0 ) + ( $popup_count->draft ?? 0 );
$widget_total   = 13;
?>
<div class="wrap st-dashboard-wrap">

<style>
.st-dashboard-wrap { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; max-width: 1200px; }

/* Hero */
.st-dash-hero {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 16px;
    padding: 36px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 20px 0 28px;
    color: #fff;
    flex-wrap: wrap;
    gap: 20px;
}
.st-dash-hero h1 { margin: 0 0 6px; font-size: 26px; font-weight: 800; color: #fff; }
.st-dash-hero p  { margin: 0; font-size: 14px; opacity: .85; }
.st-dash-hero-badge {
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 20px;
    padding: 6px 16px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    backdrop-filter: blur(4px);
}

/* Stats row */
.st-dash-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}
@media (max-width: 900px) { .st-dash-stats { grid-template-columns: repeat(2,1fr); } }
.st-stat-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: box-shadow .15s;
}
.st-stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
.st-stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 22px;
}
.st-stat-icon.purple { background: #ede9fe; color: #6366f1; }
.st-stat-icon.blue   { background: #dbeafe; color: #2563eb; }
.st-stat-icon.green  { background: #d1fae5; color: #059669; }
.st-stat-icon.orange { background: #fef3c7; color: #d97706; }
.st-stat-num  { font-size: 28px; font-weight: 800; color: #111827; line-height: 1; }
.st-stat-label{ font-size: 12px; color: #6b7280; margin-top: 3px; font-weight: 500; }

/* Cards grid */
.st-dash-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 28px;
}
@media (max-width: 900px) { .st-dash-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 600px) { .st-dash-grid { grid-template-columns: 1fr; } }

.st-dash-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 24px;
    transition: box-shadow .15s, transform .15s;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.st-dash-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.09); transform: translateY(-2px); }
.st-dash-card-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}
.st-dash-card h3 { margin: 0; font-size: 15px; font-weight: 700; color: #111827; }
.st-dash-card p  { margin: 0; font-size: 13px; color: #6b7280; line-height: 1.6; flex: 1; }
.st-dash-card-footer { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 4px; }
.st-dash-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px;
    border-radius: 7px;
    font-size: 12px; font-weight: 600;
    text-decoration: none;
    transition: all .15s;
}
.st-dash-btn-primary { background: #6366f1; color: #fff; }
.st-dash-btn-primary:hover { background: #4f46e5; color: #fff; }
.st-dash-btn-outline { background: #f5f3ff; color: #6366f1; border: 1px solid #e0e7ff; }
.st-dash-btn-outline:hover { background: #ede9fe; color: #4f46e5; }
</style>

<!-- Hero -->
<div class="st-dash-hero">
    <div>
        <h1>👋 <?php esc_html_e( 'Welcome to ST Elementor', 'st-elementor' ); ?></h1>
        <p><?php esc_html_e( 'Your all-in-one Elementor addon — widgets, templates, popups & more.', 'st-elementor' ); ?></p>
    </div>
    <span class="st-dash-hero-badge">v<?php echo esc_html( ST_ELEMENTOR_VERSION ); ?></span>
</div>

<!-- Stats -->
<div class="st-dash-stats">
    <div class="st-stat-card">
        <div class="st-stat-icon purple"><span class="dashicons dashicons-screenoptions"></span></div>
        <div>
            <div class="st-stat-num"><?php echo (int) $widget_total; ?></div>
            <div class="st-stat-label"><?php esc_html_e( 'Widgets', 'st-elementor' ); ?></div>
        </div>
    </div>
    <div class="st-stat-card">
        <div class="st-stat-icon blue"><span class="dashicons dashicons-layout"></span></div>
        <div>
            <div class="st-stat-num"><?php echo (int) $tpl_total; ?></div>
            <div class="st-stat-label"><?php esc_html_e( 'Templates', 'st-elementor' ); ?></div>
        </div>
    </div>
    <div class="st-stat-card">
        <div class="st-stat-icon green"><span class="dashicons dashicons-megaphone"></span></div>
        <div>
            <div class="st-stat-num"><?php echo (int) $pop_total; ?></div>
            <div class="st-stat-label"><?php esc_html_e( 'Popups', 'st-elementor' ); ?></div>
        </div>
    </div>
    <div class="st-stat-card">
        <div class="st-stat-icon orange"><span class="dashicons dashicons-admin-site"></span></div>
        <div>
            <div class="st-stat-num"><?php echo esc_html( get_bloginfo( 'name' ) ? ( function_exists( 'mb_substr' ) ? mb_substr( get_bloginfo( 'name' ), 0, 1 ) : substr( get_bloginfo( 'name' ), 0, 1 ) ) : 'S' ); ?></div>
            <div class="st-stat-label"><?php esc_html_e( 'Your Site', 'st-elementor' ); ?></div>
        </div>
    </div>
</div>

<!-- Feature Cards -->
<div class="st-dash-grid">

    <div class="st-dash-card">
        <div class="st-dash-card-icon" style="background:#ede9fe;color:#6366f1"><span class="dashicons dashicons-screenoptions"></span></div>
        <h3><?php esc_html_e( 'Widgets', 'st-elementor' ); ?></h3>
        <p><?php esc_html_e( '13 custom Elementor widgets — Slider, Accordion, Nav Menu, Flip Box, Popup Button and more.', 'st-elementor' ); ?></p>
        <div class="st-dash-card-footer">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=st-elementor-widgets' ) ); ?>" class="st-dash-btn st-dash-btn-primary"><?php esc_html_e( 'View Widgets', 'st-elementor' ); ?></a>
        </div>
    </div>

    <div class="st-dash-card">
        <div class="st-dash-card-icon" style="background:#dbeafe;color:#2563eb"><span class="dashicons dashicons-layout"></span></div>
        <h3><?php esc_html_e( 'Templates', 'st-elementor' ); ?></h3>
        <p><?php esc_html_e( 'Create Header, Footer, Login, Popup and reusable section templates with display conditions.', 'st-elementor' ); ?></p>
        <div class="st-dash-card-footer">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=st-elementor-templates' ) ); ?>" class="st-dash-btn st-dash-btn-primary"><?php esc_html_e( 'Manage', 'st-elementor' ); ?></a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=st-elementor-hf' ) ); ?>" class="st-dash-btn st-dash-btn-outline"><?php esc_html_e( 'Header & Footer', 'st-elementor' ); ?></a>
        </div>
    </div>

    <div class="st-dash-card">
        <div class="st-dash-card-icon" style="background:#d1fae5;color:#059669"><span class="dashicons dashicons-megaphone"></span></div>
        <h3><?php esc_html_e( 'Popups', 'st-elementor' ); ?></h3>
        <p><?php esc_html_e( 'Build popups with triggers (scroll, exit, timer, click), display conditions and frequency control.', 'st-elementor' ); ?></p>
        <div class="st-dash-card-footer">
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=st_popup' ) ); ?>" target="_blank" class="st-dash-btn st-dash-btn-primary"><?php esc_html_e( 'Manage Popups', 'st-elementor' ); ?></a>
            <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=st_popup' ) ); ?>" target="_blank" class="st-dash-btn st-dash-btn-outline">+ <?php esc_html_e( 'New', 'st-elementor' ); ?></a>
        </div>
    </div>

    <div class="st-dash-card">
        <div class="st-dash-card-icon" style="background:#fce7f3;color:#db2777"><span class="dashicons dashicons-align-center"></span></div>
        <h3><?php esc_html_e( 'Header & Footer', 'st-elementor' ); ?></h3>
        <p><?php esc_html_e( 'Replace your theme header/footer with custom Elementor templates using display conditions.', 'st-elementor' ); ?></p>
        <div class="st-dash-card-footer">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=st-elementor-hf' ) ); ?>" class="st-dash-btn st-dash-btn-primary"><?php esc_html_e( 'Configure', 'st-elementor' ); ?></a>
        </div>
    </div>

    <div class="st-dash-card">
        <div class="st-dash-card-icon" style="background:#fef3c7;color:#d97706"><span class="dashicons dashicons-lock"></span></div>
        <h3><?php esc_html_e( 'Login Form', 'st-elementor' ); ?></h3>
        <p><?php esc_html_e( 'Custom login page with full style control — background, colors, logo, redirect and more.', 'st-elementor' ); ?></p>
        <div class="st-dash-card-footer">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=st-elementor-login' ) ); ?>" target="_blank" class="st-dash-btn st-dash-btn-primary"><?php esc_html_e( 'Settings', 'st-elementor' ); ?></a>
            <a href="<?php echo esc_url( wp_login_url() ); ?>" target="_blank" class="st-dash-btn st-dash-btn-outline"><?php esc_html_e( 'Preview', 'st-elementor' ); ?></a>
        </div>
    </div>

    <div class="st-dash-card">
        <div class="st-dash-card-icon" style="background:#e0f2fe;color:#0369a1"><span class="dashicons dashicons-admin-site-alt3"></span></div>
        <h3><?php esc_html_e( 'Your Site', 'st-elementor' ); ?></h3>
        <p><?php esc_html_e( 'View your site frontend or open Elementor to start designing pages.', 'st-elementor' ); ?></p>
        <div class="st-dash-card-footer">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="st-dash-btn st-dash-btn-primary"><?php esc_html_e( 'View Site', 'st-elementor' ); ?></a>
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>" class="st-dash-btn st-dash-btn-outline"><?php esc_html_e( 'Pages', 'st-elementor' ); ?></a>
        </div>
    </div>

</div>

</div>
