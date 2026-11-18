<?php
/**
 * Project Section - Why Invest Spotlight
 * Layout name: why_invest_spotlight
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
 * Existing fields
 */
$variant = get_sub_field('variant') ?: 'normal';
$title = get_sub_field('title');
$text = get_sub_field('text');
$image = get_sub_field('image');
$cta_text = get_sub_field('cta_text');
$cta_link = get_sub_field('cta_link');
$reasons = get_sub_field('reasons');

/*
 * New media fields
 */
$media_type = get_sub_field('media_type') ?: 'image';
$video_url = get_sub_field('video_url');

if (!$title && !$image && !$video_url) {
    return;
}
?>

<section class="cg-why-invest">

    <div class="cg-why-invest__row<?php echo ('reverse' === $variant) ? ' cg-why-invest__row--reverse' : ''; ?>">

        <!-- =====================================================
             CONTENT
        ====================================================== -->
        <div class="cg-why-invest__content">

            <?php if ($title): ?>
                <h2 class="cg-why-invest__title">
                    <?php echo esc_html($title); ?>
                </h2>
            <?php endif; ?>


            <?php if ($text): ?>
                <div class="cg-why-invest__text">
                    <?php echo wp_kses_post($text); ?>
                </div>
            <?php endif; ?>


            <?php if ($cta_text && $cta_link && !empty($cta_link['url'])): ?>

                <a
                    class="button cg-why-invest__cta"
                    href="<?php echo esc_url($cta_link['url']); ?>"
                    <?php if (!empty($cta_link['target'])): ?>
                        target="<?php echo esc_attr($cta_link['target']); ?>"
                    <?php endif; ?>
                >
                    <?php echo esc_html($cta_text); ?>

                    <span class="button__arrow">→</span>
                </a>

            <?php endif; ?>

        </div>


        <!-- =====================================================
             MEDIA
        ====================================================== -->
        <?php if ($image || ($media_type === 'video' && $video_url)): ?>

            <div class="cg-why-invest__media">

                <?php if ($media_type === 'video' && $video_url): ?>

                    <?php
                    /*
                     * Detect direct video files.
                     */
                    $is_direct_video = preg_match(
                        '/\.(mp4|webm|ogg)(\?.*)?$/i',
                        $video_url
                    );
                    ?>

                    <?php if ($is_direct_video): ?>

                        <video
                            class="cg-why-invest__video"
                            controls
                            playsinline
                            preload="metadata"
                            <?php if ($image && !empty($image['url'])): ?>
                                poster="<?php echo esc_url($image['url']); ?>"
                            <?php endif; ?>
                        >

                            <source
                                src="<?php echo esc_url($video_url); ?>"
                            >

                            <?php esc_html_e(
                                'Your browser does not support the video tag.',
                                'child-generatepress'
                            ); ?>

                        </video>

                    <?php else: ?>

                        <div class="cg-why-invest__video-wrapper">

                            <iframe
                                class="cg-why-invest__video"
                                src="<?php echo esc_url($video_url); ?>"
                                title="<?php echo esc_attr($title ?: 'Project video'); ?>"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                            ></iframe>

                        </div>

                    <?php endif; ?>

                <?php elseif ($image): ?>

                    <img
                        src="<?php echo esc_url($image['url']); ?>"
                        alt="<?php echo esc_attr($image['alt']); ?>"
                        loading="lazy"
                    >

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>


    <!-- =========================================================
         REASONS
    ========================================================== -->

    <?php if (!empty($reasons)): ?>

        <div class="cg-why-invest__reasons">

            <?php foreach ($reasons as $i => $reason): ?>

                <?php
                /*
                 * Existing number field.
                 *
                 * If no custom number is entered,
                 * automatically generate 01, 02, 03...
                 */
                $number = trim((string) ($reason['number'] ?? ''));

                if ($number === '') {
                    $number = str_pad(
                        $i + 1,
                        2,
                        '0',
                        STR_PAD_LEFT
                    );
                }


                /*
                 * New Show Number field.
                 *
                 * Important:
                 * If this field doesn't exist on an old saved
                 * reason, default to TRUE so existing content
                 * continues displaying numbers.
                 */
                $show_number = array_key_exists(
                    'show_number',
                    $reason
                )
                    ? (bool) $reason['show_number']
                    : true;
                ?>

                <div class="cg-why-invest__reason">

                    <?php if ($show_number): ?>

                        <span class="cg-why-invest__reason-number">
                            <?php echo esc_html($number); ?>
                        </span>

                    <?php endif; ?>


                    <?php if (!empty($reason['title'])): ?>

                        <h3 class="cg-why-invest__reason-title">
                            <?php echo esc_html($reason['title']); ?>
                        </h3>

                    <?php endif; ?>


                    


                    <?php if (!empty($reason['text'])): ?>

                        <p class="cg-why-invest__reason-text">
                            <?php echo esc_html($reason['text']); ?>
                        </p>

                    <?php endif; ?>


                    <?php if (!empty($reason['description'])): ?>

                        <p class="cg-why-invest__reason-description">
                            <?php echo esc_html($reason['description']); ?>
                        </p>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>