<?php
/**
 * Template Name: Jiggy Videos
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-jiggy-videos/jiggy-videos.css">

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php
// Videos
$video_1_title = get_field( 'video_1_title' );
$video_1_embed = get_field( 'video_1_embed' );
$video_2_title = get_field( 'video_2_title' );
$video_2_embed = get_field( 'video_2_embed' );

// YouTube
$youtube_text   = get_field( 'youtube_text' );
$youtube_button = get_field( 'youtube_button' );
?>

<!-- Video 1 Section -->
<!-- ------------------------------------------------- -->
<?php if ( $video_1_embed ) : ?>
<section class="jiggy-videos">
    <div class="wrap">
        <div class="jiggy-video" data-animate="fade-up">
            <?php if ( $video_1_title ) : ?>
                <h2 class="heading-4"><?php echo wp_kses_post( $video_1_title ); ?></h2>
            <?php endif; ?>
            <div class="jiggy-video-embed"><?php echo wp_kses_post( $video_1_embed ); ?></div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Video 2 Section -->
<!-- ------------------------------------------------- -->
<?php if ( $video_2_embed ) : ?>
<section class="jiggy-videos">
    <div class="wrap">
        <div class="jiggy-video" data-animate="fade-up">
            <?php if ( $video_2_title ) : ?>
                <h2 class="heading-4"><?php echo wp_kses_post( $video_2_title ); ?></h2>
            <?php endif; ?>
            <div class="jiggy-video-embed"><?php echo wp_kses_post( $video_2_embed ); ?></div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- YouTube Section -->
<!-- ------------------------------------------------- -->
<?php if ( $youtube_text || $youtube_button ) : ?>
<section class="jiggy-videos-youtube">
    <div class="wrap">
        <?php if ( $youtube_text ) : ?>
            <h2 class="heading-3"><?php echo wp_kses_post( $youtube_text ); ?></h2>
        <?php endif; ?>
        <?php if ( $youtube_button ) : ?>
            <a class="button button-orange" href="<?php echo esc_url( $youtube_button['url'] ); ?>"<?php echo ! empty( $youtube_button['target'] ) ? ' target="' . esc_attr( $youtube_button['target'] ) . '"' : ''; ?>>
                <?php echo esc_html( $youtube_button['title'] ); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Playlist Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/playlist/index.php' ); ?>

<?php get_footer(); ?>
