<?php
/*
Template Name: Clarke Group Homepage
*/
?>
<?php
get_header();
?>

  <section class="home-hero">

        <div class="home-hero-overlay"></div>

        <div class="home-hero-content">

            <h1>
                Development, Investment and<br>
                Facilities Management
            </h1>

            <a
                href="<?php echo esc_url(home_url('/#properties')); ?>"
                class="home-hero-button"
            >
                VIEW ALL PROPERTIES
                <span>→</span>
            </a>

        </div>

    </section>

<?php
get_footer();
?>