<?php
/**
 * Header Template
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <?php wp_body_open(); ?>


    <!-- ============ HEADER ============ -->

    <header class="site-header">

        <div class="header-container">

            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
                <?php
                $logo_type = cg_opt('cg_header_logo_type', 'text');
                if ('image' === $logo_type) :
                    $logo_img_id = cg_opt('cg_header_logo_image');
                    if ($logo_img_id) :
                        echo wp_get_attachment_image($logo_img_id, 'full', false, array('class' => 'header-logo-image', 'alt' => get_bloginfo('name')));
                    endif;
                else :
                    $line1 = cg_opt('cg_header_logo_text_line1', 'CLARKE');
                    $line2 = cg_opt('cg_header_logo_text_line2', 'GROUP');
                    ?>
                    <span class="logo-icon" aria-hidden="true">
                        <svg viewBox="0 0 49 58" class="logo-icon-svg" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8,2 H41 Q47,2 47,8 V33 L24.5,56 L2,33 V8 Q2,2 8,2 Z" />
                            <circle cx="24.5" cy="14" r="1.8" />
                            <path d="M24.5,16 L16,36 M24.5,16 L33,36 M19.4,29 H29.6" />
                        </svg>
                    </span>
                    <?php if ($line1 || $line2) : ?>
                        <span class="logo-text">
                            <?php if ($line1) : ?><span class="logo-text-strong"><?php cg_e($line1); ?></span><?php endif; ?>
                            <?php if ($line2) : ?><span class="logo-text-light"><?php cg_e($line2); ?></span><?php endif; ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </a>


            <!-- Navigation -->
            <nav class="main-nav" id="primary-navigation">

                <ul class="nav-list">
                    <?php
                    cg_the_nav_menu_li('primary_menu', array(
                        array('url' => home_url('/#properties'), 'label' => __('Properties', 'child-generatepress')),
                        array('url' => home_url('/#queenstown'), 'label' => __('Queenstown', 'child-generatepress')),
                        array('url' => home_url('/#about'),      'label' => __('About Us', 'child-generatepress')),
                        array('url' => home_url('/#contact'),    'label' => __('Contact Us', 'child-generatepress')),
                    ));
                    ?>
                </ul>

            </nav>


            <!-- Header CTA -->
            <?php
            $show_cta = cg_opt('cg_header_cta_show', true);
            if ($show_cta) :
                $cta_link   = cg_opt('cg_header_cta_link');
                $cta_url    = (!empty($cta_link['url']))   ? $cta_link['url']   : home_url('/#contact');
                $cta_title  = (!empty($cta_link['title'])) ? $cta_link['title'] : __('GET IN TOUCH', 'child-generatepress');
                $cta_target = (!empty($cta_link['target'])) ? ' target="' . esc_attr($cta_link['target']) . '" rel="noopener noreferrer"' : '';
                $cta_arrow  = cg_opt('cg_header_cta_arrow', '→');
                ?>
                <a
                    href="<?php echo esc_url($cta_url); ?>"
                    class="header-cta"
                    <?php echo $cta_target; /* escaped above */ ?>
                >
                    <?php cg_e($cta_title); ?><?php if ($cta_arrow) : ?> <span class="cta-arrow"><?php cg_e($cta_arrow); ?></span><?php endif; ?>
                </a>
            <?php endif; ?>


            <!-- Mobile Menu Button -->
            <button class="nav-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="primary-navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div>

    </header>

    <script>
    (function () {
        var toggle = document.querySelector('.nav-toggle');
        var header = document.querySelector('.site-header');
        if (!toggle || !header) return;

        toggle.addEventListener('click', function () {
            var isOpen = header.classList.toggle('nav-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    })();
    </script>


    <!-- ============ MAIN CONTENT START ============ -->

    <main>