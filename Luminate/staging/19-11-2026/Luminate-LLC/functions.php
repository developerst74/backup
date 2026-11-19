<?php

/*
 * Creating a function to create our CPT
 */


function custom_post_type()
{

    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x('Services', 'Post Type General Name', 'twentytwentyone'),
        'singular_name' => _x('Service', 'Post Type Singular Name', 'twentytwentyone'),
        'menu_name' => __('Services', 'twentytwentyone'),
        'parent_item_colon' => __('Parent Service', 'twentytwentyone'),
        'all_items' => __('All Services', 'twentytwentyone'),
        'view_item' => __('View Service', 'twentytwentyone'),
        'add_new_item' => __('Add New Service', 'twentytwentyone'),
        'add_new' => __('Add New', 'twentytwentyone'),
        'edit_item' => __('Edit Service', 'twentytwentyone'),
        'update_item' => __('Update Service', 'twentytwentyone'),
        'search_items' => __('Search Service', 'twentytwentyone'),
        'not_found' => __('Not Found', 'twentytwentyone'),
        'not_found_in_trash' => __('Not found in Trash', 'twentytwentyone'),
    );

    // Set other options for Custom Post Type

    $args = array(
        'label' => __('services', 'twentytwentyone'),
        'description' => __('Service news and reviews', 'twentytwentyone'),
        'labels' => $labels,
        // Features this CPT supports in Post Editor
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies' => array('genres'),
        /* A hierarchical CPT is like Pages and can have
         * Parent and child items. A non-hierarchical CPT
         * is like Posts.
         */
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'show_in_rest' => true,

    );

    // Registering your Custom Post Type
    register_post_type('services', $args);

}

/* Hook into the 'init' action so that the function
 * Containing our post type registration is not 
 * unnecessarily executed. 
 */

add_action('init', 'custom_post_type', 0);
add_action("wp_footer", "custom_feture_image_url");
function custom_feture_image_url()
{
    ?>
    <?php
    if (is_page('news-media')) {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                // Check if 'et_fb' is present in the URL
                if (window.location.href.indexOf('et_fb') === -1) {
                    setInterval(function () {
                        $('article.et_pb_post').each(function () {
                            var $article = $(this);

                            // Check if .et_pb_image_container exists inside the article
                            if ($article.find('.entry-featured-image-url').length === 0) {

                                // Get the article link title from <h2> -> <a>
                                var articleLink = $article.find('h2.entry-title a');
                                var linkHref = articleLink.attr('href');
                                var linkTitle = articleLink.text();

                                // Default image URL
                                var defaultImg = window.location.origin + '/wp-content/uploads/2025/02/placeholder.jpg';

                                // Create the new div structure
                                var newDiv = $(`<a href="${linkHref}" class="entry-featured-image-url"></a>`);
                                var newAnchor = $('<a></a>').attr('href', linkHref);
                                var newImg = $('<img>').attr({
                                    'src': defaultImg,
                                    'alt': linkTitle,
                                    'width': '400',
                                    'height': '250'
                                });

                                // Append image inside anchor, and anchor inside div
                                // newAnchor.append(newImg);
                                newDiv.append(newImg);

                                // Prepend the newly created div to the article
                                $article.prepend(newDiv);
                            }
                        });
                    }, 1000); // 5000ms = 5 seconds

                }
            });

        </script>
        <?php
        return;
    }
    ?>
    <script>
        jQuery(document).ready(function ($) {
            // Check if 'et_fb' is present in the URL
            if (window.location.href.indexOf('et_fb') === -1) {
                if (jQuery(".home").length > 0) {
                    $('article.et_pb_post').each(function () {
                        var $article = $(this);


                        // Check if .et_pb_image_container exists inside the article
                        if ($article.find('.et_pb_image_container').length === 0) {

                            // Get the article link title from <h2> -> <a>
                            var articleLink = $article.find('h2.entry-title a');
                            var linkHref = articleLink.attr('href');
                            var linkTitle = articleLink.text();

                            // Default image URL
                            var defaultImg = window.location.origin + '/wp-content/uploads/2025/02/placeholder.jpg';

                            // Create the new div structure
                            var newDiv = $('<div class="et_pb_image_container"></div>');
                            var newAnchor = $('<a></a>').attr('href', linkHref);
                            var newImg = $('<img>').attr({
                                'src': defaultImg,
                                'alt': linkTitle,
                                'width': '400',
                                'height': '250'
                            });

                            // Append image inside anchor, and anchor inside div
                            newAnchor.append(newImg);
                            newDiv.append(newAnchor);

                            // Prepend the newly created div to the article
                            $article.prepend(newDiv);
                        }
                    });
                }

            }
        });

    </script>
    <?php
}
require_once get_stylesheet_directory() . '/lm-news-media.php';

