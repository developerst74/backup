<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
$widgets = [
    [
        'name'  => 'ST Slider',
        'icon'  => 'dashicons-images-alt2',
        'color' => '#6366f1',
        'bg'    => '#ede9fe',
        'desc'  => 'Smooth slider with autoplay, fade/slide effects, arrows, dots and per-slide animations.',
        'tag'   => 'Content',
    ],
    [
        'name'  => 'ST Accordion',
        'icon'  => 'dashicons-list-view',
        'color' => '#0891b2',
        'bg'    => '#e0f2fe',
        'desc'  => 'Animated accordion with icon support, WYSIWYG or Elementor template content per item.',
        'tag'   => 'Content',
    ],
    [
        'name'  => 'ST Pricing Grid',
        'icon'  => 'dashicons-grid-view',
        'color' => '#7c3aed',
        'bg'    => '#f5f3ff',
        'desc'  => 'Multi-column pricing grid with toggle (monthly/yearly), popular badge and CTA buttons.',
        'tag'   => 'Marketing',
    ],
    [
        'name'  => 'ST Login Form',
        'icon'  => 'dashicons-lock',
        'color' => '#d97706',
        'bg'    => '#fef3c7',
        'desc'  => 'Custom login form with full style control, redirect, remember me and forgot password.',
        'tag'   => 'Forms',
    ],
    [
        'name'  => 'ST Template',
        'icon'  => 'dashicons-library-save',
        'color' => '#0369a1',
        'bg'    => '#e0f2fe',
        'desc'  => 'Embed any ST Template or Elementor Library template inline on any page.',
        'tag'   => 'Layout',
    ],
    [
        'name'  => 'ST Site Logo',
        'icon'  => 'dashicons-admin-appearance',
        'color' => '#059669',
        'bg'    => '#d1fae5',
        'desc'  => 'Display site logo from Customizer, custom image URL or text logo with tagline.',
        'tag'   => 'Header',
    ],
    [
        'name'  => 'ST Nav Menu',
        'icon'  => 'dashicons-menu',
        'color' => '#6366f1',
        'bg'    => '#ede9fe',
        'desc'  => 'Full-featured nav menu — horizontal/vertical, dropdown, sticky header, mobile panel.',
        'tag'   => 'Header',
    ],
    [
        'name'  => 'ST Flex Box',
        'icon'  => 'dashicons-layout',
        'color' => '#db2777',
        'bg'    => '#fce7f3',
        'desc'  => 'Flexible container with full flexbox controls — direction, wrap, gap, alignment.',
        'tag'   => 'Layout',
    ],
    [
        'name'  => 'ST Flip Box',
        'icon'  => 'dashicons-image-rotate',
        'color' => '#7c3aed',
        'bg'    => '#f5f3ff',
        'desc'  => 'Two-sided flip card with front/back content, icon, title and button on hover.',
        'tag'   => 'Content',
    ],
    [
        'name'  => 'ST Popup Button',
        'icon'  => 'dashicons-button',
        'color' => '#dc2626',
        'bg'    => '#fee2e2',
        'desc'  => 'Button that opens any ST Popup by ID — with icon, alignment and full style control.',
        'tag'   => 'Marketing',
    ],
    [
        'name'  => 'ST Flexible Layout',
        'icon'  => 'dashicons-columns',
        'color' => '#0891b2',
        'bg'    => '#e0f2fe',
        'desc'  => 'Responsive multi-column layout builder with custom breakpoints and gap control.',
        'tag'   => 'Layout',
    ],
];

$total = count( $widgets );
?>
<div class="wrap st-widgets-wrap">

<style>
.st-widgets-wrap { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; max-width: 1200px; }

.st-wg-header {
    display: flex; align-items: center; justify-content: space-between;
    margin: 20px 0 24px; flex-wrap: wrap; gap: 12px;
}
.st-wg-header h1 { margin: 0; font-size: 22px; font-weight: 800; color: #111827; }
.st-wg-header p  { margin: 4px 0 0; color: #6b7280; font-size: 13px; }
.st-wg-badge {
    background: #6366f1; color: #fff;
    padding: 6px 16px; border-radius: 20px;
    font-size: 13px; font-weight: 700;
}

.st-wg-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 30px;
}
@media (max-width: 1100px) { .st-wg-grid { grid-template-columns: repeat(3,1fr); } }
@media (max-width: 780px)  { .st-wg-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 500px)  { .st-wg-grid { grid-template-columns: 1fr; } }

.st-wg-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: box-shadow .15s, transform .15s;
    position: relative;
    overflow: hidden;
}
.st-wg-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,.09); transform: translateY(-2px); }

.st-wg-card-top { display: flex; align-items: center; justify-content: space-between; }
.st-wg-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.st-wg-tag {
    font-size: 10px; font-weight: 700;
    padding: 3px 8px; border-radius: 20px;
    background: #f3f4f6; color: #6b7280;
    text-transform: uppercase; letter-spacing: .4px;
}
.st-wg-card h4 { margin: 0; font-size: 14px; font-weight: 700; color: #111827; }
.st-wg-card p  { margin: 0; font-size: 12px; color: #6b7280; line-height: 1.6; flex: 1; }
.st-wg-status {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600;
    color: #059669;
}
.st-wg-status::before {
    content: '';
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #059669;
    display: inline-block;
}
</style>

<div class="st-admin-page-header">
    <div>
        <h1><?php esc_html_e( 'ST Elementor Widgets', 'st-elementor' ); ?></h1>
        <p><?php esc_html_e( 'All widgets available in Elementor editor under "Store Transform" category.', 'st-elementor' ); ?></p>
    </div>
    <span style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);border-radius:20px;padding:6px 18px;font-size:13px;font-weight:700;color:#fff"><?php echo (int) $total; ?> <?php esc_html_e( 'Active', 'st-elementor' ); ?></span>
</div>

<div class="st-wg-grid">
<?php foreach ( $widgets as $w ) : ?>
<div class="st-wg-card">
    <div class="st-wg-card-top">
        <div class="st-wg-icon" style="background:<?php echo esc_attr( $w['bg'] ); ?>;color:<?php echo esc_attr( $w['color'] ); ?>">
            <span class="dashicons <?php echo esc_attr( $w['icon'] ); ?>"></span>
        </div>
        <span class="st-wg-tag"><?php echo esc_html( $w['tag'] ); ?></span>
    </div>
    <h4><?php echo esc_html( $w['name'] ); ?></h4>
    <p><?php echo esc_html( $w['desc'] ); ?></p>
    <span class="st-wg-status"><?php esc_html_e( 'Active', 'st-elementor' ); ?></span>
</div>
<?php endforeach; ?>
</div>

</div>
