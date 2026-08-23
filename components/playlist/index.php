<?php
/**
 * Playlist Component
 *
 * Spotify / Apple Music call-to-action band from Site Settings.
 * Hidden entirely when the toggle is off.
 *
 * @package Jiggy_Wrigglers
 */
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/components/playlist/style.css">
<!-- Playlist Component -->
<?php
    $show_jiggy_playlist = get_field( 'show_jiggy_playlist', 'option' );
    $playlist_title      = get_field( 'playlist_title', 'option' );
    $spotify_link        = get_field( 'spotify_link', 'option' );
    $apple_music_link    = get_field( 'apple_music_link', 'option' );

    if ( ! $show_jiggy_playlist ) return;
?>
<section class="playlist">
    <div class="wrap">
        <?php if ( $playlist_title ) : ?>
            <h2 class="heading-3 playlist-title"><?php echo wp_kses_post( $playlist_title ); ?></h2>
        <?php endif; ?>
        <div class="playlist-links">
            <?php if ( $spotify_link ) : ?>
                <a class="button button-blue" href="<?php echo esc_url( $spotify_link['url'] ); ?>"<?php echo ! empty( $spotify_link['target'] ) ? ' target="' . esc_attr( $spotify_link['target'] ) . '"' : ''; ?>>
                    <?php echo esc_html( $spotify_link['title'] ); ?>
                </a>
            <?php endif; ?>
            <?php if ( $apple_music_link ) : ?>
                <a class="button button-pink" href="<?php echo esc_url( $apple_music_link['url'] ); ?>"<?php echo ! empty( $apple_music_link['target'] ) ? ' target="' . esc_attr( $apple_music_link['target'] ) . '"' : ''; ?>>
                    <?php echo esc_html( $apple_music_link['title'] ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
