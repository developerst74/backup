<?php
/**
 * Footer Template
 */
?>

<!-- ==============================
     FOOTER CTA SECTION
================================= -->
<?php
$show_footer_cta = cg_opt('cg_footer_cta_show', true);
if ($show_footer_cta) :
    $cta_heading = cg_opt('cg_footer_cta_heading', "Explore our developments\nor speak with our team.");
    $cta_btn     = cg_opt('cg_footer_cta_button_link');
    $cta_url     = (!empty($cta_btn['url']))     ? $cta_btn['url']     : home_url('/#contact');
    $cta_title   = (!empty($cta_btn['title']))   ? $cta_btn['title']   : __('CONTACT US', 'child-generatepress');
    $cta_target  = (!empty($cta_btn['target']))  ? ' target="' . esc_attr($cta_btn['target']) . '" rel="noopener noreferrer"' : '';
    $cta_arrow   = cg_opt('cg_footer_cta_button_arrow', '→');
?>
    <section class="footer-cta-section">

        <div class="footer-cta-overlay"></div>

        <div class="footer-cta-content">
            <?php if ($cta_heading) : ?>
                <h2>
                    <?php cg_e($cta_heading, 'none'); /* new_lines => <br> allowed */ ?>
                </h2>
            <?php endif; ?>

            <a
                href="<?php echo esc_url($cta_url); ?>"
                class="footer-cta-button"
                <?php echo $cta_target; /* escaped above */ ?>
            >
                <?php cg_e($cta_title); ?><?php if ($cta_arrow) : ?> <span><?php cg_e($cta_arrow); ?></span><?php endif; ?>
            </a>

        </div>

    </section>
<?php endif; ?>


<!-- ==============================
     MAIN FOOTER
================================= -->

<footer class="site-footer">

    <div class="footer-container">

        <!-- Logo -->
        <div class="footer-brand">

            <a
                href="<?php echo esc_url(home_url('/')); ?>"
                class="footer-logo"
            >
                <?php
                $flogo_type = cg_opt('cg_footer_logo_type', 'text');
                if ('image' === $flogo_type) :
                    $flogo_img_id = cg_opt('cg_footer_logo_image');
                    if ($flogo_img_id) :
                        echo wp_get_attachment_image($flogo_img_id, 'full', false, array('class' => 'footer-logo-image', 'alt' => get_bloginfo('name')));
                    endif;
                else :
                    $flogo_line1 = cg_opt('cg_footer_logo_text_line1', 'CLARKE');
                    $flogo_line2 = cg_opt('cg_footer_logo_text_line2', 'GROUP');
                ?>
                    <span class="footer-logo-icon" aria-hidden="true">
                        <svg viewBox="0 0 49 58" class="logo-icon-svg" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8,2 H41 Q47,2 47,8 V33 L24.5,56 L2,33 V8 Q2,2 8,2 Z" />
                            <circle cx="24.5" cy="14" r="1.8" />
                            <path d="M24.5,16 L16,36 M24.5,16 L33,36 M19.4,29 H29.6" />
                        </svg>
                    </span>
                    <?php if ($flogo_line1 || $flogo_line2) : ?>
                        <span class="footer-logo-text">
                            <?php if ($flogo_line1) : ?><span class="logo-text-strong"><?php cg_e($flogo_line1); ?></span><?php endif; ?>
                            <?php if ($flogo_line2) : ?><span class="logo-text-light"><?php cg_e($flogo_line2); ?></span><?php endif; ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </a>

            <?php
            $footer_desc = cg_opt('cg_footer_description');
            if ($footer_desc) :
            ?>
                <div class="footer-description">
                    <?php cg_e($footer_desc, 'none'); /* wpautop applied by ACF new_lines setting */ ?>
                </div>
            <?php endif; ?>

            <?php
            $fphone   = cg_opt('cg_footer_phone');
            $femail   = cg_opt('cg_footer_email');
            $faddress = cg_opt('cg_footer_address');
            if ($fphone || $femail || $faddress) :
            ?>
                <div class="footer-contact-info">
                    <?php if ($fphone) : ?>
                        <p class="footer-contact footer-contact-phone">
                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $fphone)); ?>"><?php cg_e($fphone); ?></a>
                        </p>
                    <?php endif; ?>
                    <?php if ($femail) : ?>
                        <p class="footer-contact footer-contact-email">
                            <a href="mailto:<?php echo esc_attr($femail); ?>"><?php cg_e($femail); ?></a>
                        </p>
                    <?php endif; ?>
                    <?php if ($faddress) : ?>
                        <p class="footer-contact footer-contact-address">
                            <?php cg_e($faddress, 'none'); /* <br> linebreaks allowed */ ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>


        <!-- Main Menu -->
        <div class="footer-column">

            <h4><?php esc_html_e('MAIN MENU', 'child-generatepress'); ?></h4>

            <ul>
                <?php
                cg_the_nav_menu_li('footer_main_menu', array(
                    array('url' => home_url('/'),         'label' => __('Home', 'child-generatepress')),
                    array('url' => home_url('/#about'),    'label' => __('About', 'child-generatepress')),
                    array('url' => '#',                    'label' => __('News and Updates', 'child-generatepress')),
                ));
                ?>
            </ul>

        </div>


        <!-- Projects -->
        <div class="footer-column">

            <h4><?php esc_html_e('PROJECTS', 'child-generatepress'); ?></h4>

            <ul>
                <?php
                cg_the_nav_menu_li('footer_projects', array(
                    array('url' => '#', 'label' => __('Tephra', 'child-generatepress')),
                    array('url' => '#', 'label' => __('Enclave', 'child-generatepress')),
                    array('url' => '#', 'label' => __('Kepa', 'child-generatepress')),
                    array('url' => '#', 'label' => __('Ladies Mile', 'child-generatepress')),
                ));
                ?>
            </ul>

        </div>


        <!-- Other Links -->
        <div class="footer-column">

            <h4><?php esc_html_e('OTHER LINKS', 'child-generatepress'); ?></h4>

            <ul>
                <?php
                cg_the_nav_menu_li('footer_other_links', array(
                    array('url' => '#',                    'label' => __('Privacy Policy', 'child-generatepress')),
                    array('url' => home_url('/#contact'),  'label' => __('Contact Us', 'child-generatepress')),
                    array('url' => '#',                    'label' => __('Career', 'child-generatepress')),
                ));
                ?>
            </ul>

        </div>


        <!-- Follow Us -->
        <?php
        $fb = cg_opt('cg_footer_social_facebook');
        $ig = cg_opt('cg_footer_social_instagram');
        $tw = cg_opt('cg_footer_social_twitter');
        $li = cg_opt('cg_footer_social_linkedin');
        $has_social = ($fb || $ig || $tw || $li);
        ?>
        <?php if ($has_social) : ?>
            <div class="footer-column">

                <h4><?php esc_html_e('FOLLOW US', 'child-generatepress'); ?></h4>

                <ul>
                    <?php if ($fb) : ?>
                        <li>
                            <a href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener noreferrer">
                                <?php esc_html_e('Facebook', 'child-generatepress'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($ig) : ?>
                        <li>
                            <a href="<?php echo esc_url($ig); ?>" target="_blank" rel="noopener noreferrer">
                                <?php esc_html_e('Instagram', 'child-generatepress'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($tw) : ?>
                        <li>
                            <a href="<?php echo esc_url($tw); ?>" target="_blank" rel="noopener noreferrer">
                                <?php esc_html_e('Twitter', 'child-generatepress'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($li) : ?>
                        <li>
                            <a href="<?php echo esc_url($li); ?>" target="_blank" rel="noopener noreferrer">
                                <?php esc_html_e('LinkedIn', 'child-generatepress'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

            </div>
        <?php endif; ?>

    </div>


    <!-- Copyright -->

    <div class="footer-bottom">
        <?php
        $copyright = cg_opt('cg_footer_copyright', '© {{year}} Clarke Group. All rights reserved.');
        if ($copyright) :
            $copyright = str_replace('{{year}}', wp_date('Y'), $copyright);
        ?>
            <p>
                <?php cg_e($copyright); ?>
            </p>
        <?php endif; ?>
    </div>

</footer>


<?php wp_footer(); ?>

</body>
</html>