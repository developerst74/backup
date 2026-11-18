<?php
/*
Template Name: About
*/

get_header();

$rendered_any = false;

// --- Flexible Content: Page Sections ---
if (function_exists('get_field') && have_rows('page_sections')) :
    while (have_rows('page_sections')) : the_row();
        $rendered_any = true;
        switch (get_row_layout()) :
            case 'page_hero_layout' :
                get_template_part('template-parts/sections/page-hero');
                break;
            case 'about_intro_layout' :
                get_template_part('template-parts/sections/about-intro');
                break;
            case 'experience_stats_layout' :
                get_template_part('template-parts/sections/experience');
                break;
            case 'process_layout' :
                get_template_part('template-parts/sections/process');
                break;
            case 'team_layout' :
                get_template_part('template-parts/sections/team');
                break;
            case 'trusted_by_layout' :
                get_template_part('template-parts/sections/trusted-by');
                break;
            case 'client_logos_layout' :
                get_template_part('template-parts/sections/client-logos');
                break;
            case 'testimonials_layout' :
                get_template_part('template-parts/sections/testimonials');
                break;
            case 'cta_layout' :
                get_template_part('template-parts/sections/cta');
                break;
            case 'contact_section_layout' :
                get_template_part('template-parts/sections/contact');
                break;
            case 'faq_layout' :
                get_template_part('template-parts/sections/faq');
                break;
            case 'locations_layout' :
                get_template_part('template-parts/sections/locations');
                break;
            case 'bottom_cta_layout' :
                get_template_part('template-parts/sections/bottom-cta');
                break;
            case 'hero_slider_layout' :
                get_template_part('template-parts/sections/hero');
                break;
            case 'services_grid_layout' :
                get_template_part('template-parts/sections/services');
                break;
            case 'builder_features_layout' :
                get_template_part('template-parts/sections/builder-features');
                break;
        endswitch;
    endwhile;
endif;

// --- BACKWARD COMPAT FALLBACK ---
if (!$rendered_any) :
    get_template_part('template-parts/sections/page-hero');
    get_template_part('template-parts/sections/about-intro');
    get_template_part('template-parts/sections/experience');
    get_template_part('template-parts/sections/team');
    get_template_part('template-parts/sections/testimonials');
    get_template_part('template-parts/sections/contact');
endif;

get_footer();
