<?php
/**
 * Template Name: Jiggy Videos
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-jiggy-videos/jiggy-videos.css">

<!-- Banner Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php
// Video Block 1
$video_block_1_title = get_field( 'video_block_1_title' );
$video_block_1_embed = get_field( 'video_block_1_embed' );

// Video Block 2
$video_block_2_1_title = get_field( 'video_block_2_1_title' );
$video_block_2_1_embed = get_field( 'video_block_2_1_embed' );
$video_block_2_2_title = get_field( 'video_block_2_2_title' );
$video_block_2_2_embed = get_field( 'video_block_2_2_embed' );

// Playlist (jiggy-playlight-footer)
$playlist_title = get_field( 'playlist_title' );
$spotify_link = get_field( 'spotify_link' );
$apple_music_link = get_field( 'apple_music_link' );
?>

<!-- Video Block 1 Section -->
<!-- ------------------------------------------------- -->
<?php if ( $video_block_1_embed ) : ?>
<section class="video-block-1" x-data="{ shown: false }" x-intersect:enter.threshold.0.2="shown = true">
    <div class="wrap">
        <?php if ( $video_block_1_title ) : ?>
            <h2 class="heading-3" data-animate="fade-up" :class="shown && 'is-visible'"><?php echo wp_kses_post( $video_block_1_title ); ?></h2>
        <?php endif; ?>
        <div class="video-block-1-embed" data-animate="fade-up" data-animate-delay="1" :class="shown && 'is-visible'">
            <?php echo $video_block_1_embed; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Video Block 2 Section -->
<!-- ------------------------------------------------- -->
<?php if ( $video_block_2_1_embed || $video_block_2_2_embed ) : ?>
<section class="video-block-2" x-data="{ shown: false }" x-intersect:enter.threshold.0.2="shown = true">
    <div class="wrap">
        <?php if ( $video_block_2_1_embed ) : ?>
            <div class="video-block-2-item">
                <?php if ( $video_block_2_1_title ) : ?>
                    <h2 class="heading-3" data-animate="fade-up" :class="shown && 'is-visible'"><?php echo wp_kses_post( $video_block_2_1_title ); ?></h2>
                <?php endif; ?>
                <div class="video-block-2-embed"><?php echo $video_block_2_1_embed; ?></div>
            </div>
        <?php endif; ?>
        <?php if ( $video_block_2_2_embed ) : ?>
            <div class="video-block-2-item">
                <?php if ( $video_block_2_2_title ) : ?>
                    <h2 class="heading-3" data-animate="fade-up" data-animate-delay="1" :class="shown && 'is-visible'"><?php echo wp_kses_post( $video_block_2_2_title ); ?></h2>
                <?php endif; ?>
                <div class="video-block-2-embed"><?php echo $video_block_2_2_embed; ?></div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Playlist Section (jiggy-playlight-footer) -->
<!-- ------------------------------------------------- -->
<?php if ( $spotify_link || $apple_music_link ) : ?>
<section class="jiggy-playlight-footer" x-data="{ shown: false }" x-intersect:enter.threshold.0.2="shown = true">
    <div class="wrap">
        <div class="header-jpl" data-animate="fade-up" :class="shown && 'is-visible'">
            <i class="playlist-note" aria-hidden="true">&#9835;</i>
            <?php if ( $playlist_title ) : ?>
                <h2 class="heading-3 playlist-title"><?php echo wp_kses_post( $playlist_title ); ?></h2>
            <?php endif; ?>
            <i class="playlist-note" aria-hidden="true">&#9835;</i>
        </div>
        <div class="linkd-jpl" data-animate="fade-up" data-animate-delay="1" :class="shown && 'is-visible'">
            <?php if ( $spotify_link ) : ?>
                <a href="<?php echo esc_url( $spotify_link['url'] ); ?>"<?php echo ! empty( $spotify_link['target'] ) ? ' target="' . esc_attr( $spotify_link['target'] ) . '"' : ''; ?>>
                    <?php echo esc_html( $spotify_link['title'] ); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
                </a>
            <?php endif; ?>
            <?php if ( $apple_music_link ) : ?>
                <a href="<?php echo esc_url( $apple_music_link['url'] ); ?>"<?php echo ! empty( $apple_music_link['target'] ) ? ' target="' . esc_attr( $apple_music_link['target'] ) . '"' : ''; ?>>
                    <?php echo esc_html( $apple_music_link['title'] ); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M23.997 6.124c0-.738-.065-1.469-.19-2.185-.327-1.883-1.824-3.38-3.707-3.708C18.767.029 17.435 0 12 0 6.564 0 5.233.029 3.9.231 2.017.559.52 2.056.192 3.939.067 4.655 0 5.386 0 6.124v11.752c0 .738.067 1.469.192 2.185.328 1.883 1.825 3.38 3.708 3.708 1.332.202 2.663.231 8.1.231 5.435 0 6.767-.029 8.1-.231 1.883-.328 3.38-1.825 3.707-3.708.125-.716.19-1.447.19-2.185V6.124zM17.37 14.78c-.45.281-.98.328-1.5.234-1.125-.188-2.25-.422-3.375-.656-.328-.047-.656-.094-.984-.14-.188-.047-.282.093-.235.28.14.516.281 1.031.421 1.547.188.656.094 1.265-.328 1.828-.421.516-1.03.703-1.687.61-.61-.095-1.125-.376-1.64-.704-.188-.14-.375-.28-.562-.421-.094.14-.188.28-.328.421-.328.376-.75.563-1.266.563-.375 0-.703-.14-.984-.375-.187-.141-.328-.375-.515-.563.046-.093.093-.14.14-.187.328-.282.609-.61.843-.985.282-.469.376-.984.282-1.5-.094-.61-.375-1.125-.75-1.594-.14-.187-.281-.375-.375-.562-.516-.89-.985-1.781-1.5-2.672-.047-.094-.094-.14-.14-.234-.375-.703-.188-1.5.421-1.922.563-.375 1.36-.281 1.829.234.328.376.609.797.843 1.219.188.328.329.656.516.984.047.094.14.188.235.188.093 0 .187-.094.234-.188.328-.516.61-1.031.938-1.547.328-.516.797-.844 1.406-.844.703 0 1.312.375 1.64.985.282.516.563 1.078.844 1.64.047.094.094.188.188.282.094-.422.188-.797.281-1.219.14-.703.375-1.36.844-1.922.515-.609 1.125-.844 1.875-.656.703.14 1.125.61 1.359 1.265.14.422.188.844.188 1.266v4.265c0 .61-.188 1.125-.657 1.453z"/></svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
