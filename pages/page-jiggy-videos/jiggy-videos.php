<?php
/**
 * Template Name: Jiggy Videos
 *
 * Videos and music, with native cookie-consent controls left in charge of embeds.
 *
 * @package Jiggy_Wrigglers
 */

wp_enqueue_style(
    'jiggywrigglers-videos',
    get_theme_file_uri( '/pages/page-jiggy-videos/jiggy-videos.css' ),
    array( 'jiggywrigglers-style' ),
    filemtime( __DIR__ . '/jiggy-videos.css' )
);
get_header();

$hero_title = get_field( 'hero_title' ) ?: __( 'Jiggy Videos & Music', 'jiggywrigglers' );
$hero_text = get_field( 'hero_text' ) ?: __( 'Bring a little Jiggy joy home. Watch our videos, meet the people behind the fun and keep your favourite songs playing.', 'jiggywrigglers' );
$hero_sub_title = get_field( 'hero_sub_title' );
$hero_buttons = get_field( 'hero_buttons' );
$hero_images = get_field( 'hero_images' );
$hero_image = is_array( $hero_images ) && ! empty( $hero_images[0]['ID'] ) ? $hero_images[0]['ID'] : 0;

$videos = array(
    array(
        'id'          => '_XKc5YxcAps',
        'title'       => get_field( 'video_block_2_1_title' ) ?: __( 'Jiggy Wrigglers – Jiggy Mixed', 'jiggywrigglers' ),
        'embed'       => get_field( 'video_block_2_1_embed' ),
        'label'       => __( 'A little look at our classes', 'jiggywrigglers' ),
        'description' => __( 'Take a peek at Jiggy Mixed and the music, movement and smiles that bring our sessions to life.', 'jiggywrigglers' ),
    ),
    array(
        'id'          => 'd1jZ5gefHL8',
        'title'       => get_field( 'video_block_1_title' ) ?: __( 'Jiggy Wrigglers Franchise Conference 2022', 'jiggywrigglers' ),
        'embed'       => get_field( 'video_block_1_embed' ),
        'label'       => __( 'Our Jiggy family', 'jiggywrigglers' ),
        'description' => __( 'Join us for a look back at the Jiggy Wrigglers franchise conference in 2022.', 'jiggywrigglers' ),
    ),
    array(
        'id'          => 'j3W1n26r0o8',
        'title'       => get_field( 'video_block_2_2_title' ) ?: __( 'Interview with Dowley, Jiggy Wrigglers Bournemouth', 'jiggywrigglers' ),
        'embed'       => get_field( 'video_block_2_2_embed' ),
        'label'       => __( 'Meet the people behind the fun', 'jiggywrigglers' ),
        'description' => __( 'Hear from Dowley in Bournemouth and get to know another member of our Jiggy Wrigglers family.', 'jiggywrigglers' ),
    ),
);

/**
 * Add accessible iframe titles without changing consent state, source or visibility.
 * Existing SureCookie wrappers and data-surecookie-* attributes are preserved.
 */
$prepare_embed = static function ( $html, $title ) {
    if ( ! is_string( $html ) || '' === trim( $html ) ) {
        return '<div class="jiggy-video-fallback"><span class="jiggy-video-fallback-icon" aria-hidden="true">&#9654;</span><p>'
            . esc_html__( 'This video is available on YouTube.', 'jiggywrigglers' ) . '</p><p>'
            . esc_html__( 'Use the “Watch on YouTube” link below to open it.', 'jiggywrigglers' ) . '</p></div>';
    }
    if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
        $processor = new WP_HTML_Tag_Processor( $html );
        while ( $processor->next_tag( 'iframe' ) ) {
            if ( ! $processor->get_attribute( 'title' ) ) {
                $processor->set_attribute( 'title', wp_strip_all_tags( $title ) );
            }
            if ( ! $processor->get_attribute( 'loading' ) ) {
                $processor->set_attribute( 'loading', 'lazy' );
            }
        }
        return $processor->get_updated_html();
    }
    return preg_replace_callback(
        '/<iframe\b[^>]*>/i',
        static function ( $match ) use ( $title ) {
            $tag = $match[0];
            if ( ! preg_match( '/\stitle\s*=/i', $tag ) ) {
                $tag = substr( $tag, 0, -1 ) . ' title="' . esc_attr( wp_strip_all_tags( $title ) ) . '">';
            }
            if ( ! preg_match( '/\sloading\s*=/i', $tag ) ) {
                $tag = substr( $tag, 0, -1 ) . ' loading="lazy">';
            }
            return $tag;
        },
        $html
    );
};

$playlist_title = get_field( 'playlist_title' ) ?: __( 'Keep the Jiggy music playing', 'jiggywrigglers' );
$spotify_field = get_field( 'spotify_link' );
$apple_field = get_field( 'apple_music_link' );
$spotify_url = is_array( $spotify_field ) && ! empty( $spotify_field['url'] ) ? $spotify_field['url'] : 'https://open.spotify.com/track/56169h4BUM5KPc3KiPAsxu';
$apple_url = is_array( $apple_field ) && ! empty( $apple_field['url'] ) ? $apple_field['url'] : 'https://music.apple.com/gb/artist/jiggy-wrigglers-uk/1656084414';
$play_icon = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>';
$note_icon = '<svg viewBox="0 0 48 48" fill="none" aria-hidden="true"><path d="M19 35V12l21-5v23M19 19l21-5" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><ellipse cx="12" cy="36" rx="7" ry="5" fill="currentColor"/><ellipse cx="33" cy="31" rx="7" ry="5" fill="currentColor"/></svg>';
?>
<div class="jiggy-video-page">
    <section class="jiggy-video-intro" aria-labelledby="jiggy-video-title">
        <div class="wrap jiggy-video-intro-grid">
            <div class="jiggy-video-intro-copy">
                <p class="jiggy-video-eyebrow"><?php esc_html_e( 'Sing. Dance. Wriggle.', 'jiggywrigglers' ); ?></p>
                <h1 id="jiggy-video-title"><?php echo wp_kses_post( $hero_title ); ?></h1>
                <?php if ( $hero_sub_title ) : ?>
                    <p class="jiggy-video-subtitle"><?php echo wp_kses_post( $hero_sub_title ); ?></p>
                <?php endif; ?>
                <p class="jiggy-video-intro-text"><?php echo wp_kses_post( $hero_text ); ?></p>
                <nav class="jiggy-video-jump-links" aria-label="<?php esc_attr_e( 'Explore videos and music', 'jiggywrigglers' ); ?>">
                    <a class="jiggy-video-button jiggy-video-button-orange" href="#jiggy-videos"><?php echo $play_icon; ?><?php esc_html_e( 'Watch our videos', 'jiggywrigglers' ); ?></a>
                    <a class="jiggy-video-button jiggy-video-button-light" href="#jiggy-music"><?php echo $note_icon; ?><?php esc_html_e( 'Listen to our music', 'jiggywrigglers' ); ?></a>
                </nav>
                <?php if ( is_array( $hero_buttons ) ) : ?>
                    <div class="jiggy-video-extra-links">
                        <?php foreach ( $hero_buttons as $hero_button ) : ?>
                            <?php if ( ! empty( $hero_button['button']['url'] ) ) : ?>
                                <a href="<?php echo esc_url( $hero_button['button']['url'] ); ?>"<?php echo ! empty( $hero_button['button']['target'] ) ? ' target="' . esc_attr( $hero_button['button']['target'] ) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $hero_button['button']['title'] ); ?> <span aria-hidden="true">→</span></a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="jiggy-video-intro-art<?php echo $hero_image ? ' has-photo' : ''; ?>"<?php echo $hero_image ? '' : ' aria-hidden="true"'; ?>>
                <?php if ( $hero_image ) : ?>
                    <?php echo wp_get_attachment_image( $hero_image, 'large', false, array( 'sizes' => '(max-width: 820px) 90vw, 40vw' ) ); ?>
                <?php else : ?>
                    <span class="jiggy-video-note jiggy-video-note-blue"><?php echo $note_icon; ?></span>
                    <span class="jiggy-video-play"><?php echo $play_icon; ?></span>
                    <span class="jiggy-video-note jiggy-video-note-pink"><?php echo $note_icon; ?></span>
                    <span class="jiggy-video-art-dot"></span>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="jiggy-videos" class="jiggy-video-library" aria-labelledby="jiggy-video-library-title">
        <div class="wrap">
            <div class="jiggy-video-section-heading">
                <p class="jiggy-video-eyebrow"><?php esc_html_e( 'Watch & wriggle', 'jiggywrigglers' ); ?></p>
                <h2 id="jiggy-video-library-title"><?php esc_html_e( 'A little look at the Jiggy magic', 'jiggywrigglers' ); ?></h2>
                <p><?php esc_html_e( 'Explore a Jiggy Mixed session, meet our team and share a few moments from our Jiggy Wrigglers family.', 'jiggywrigglers' ); ?></p>
            </div>
            <p class="jiggy-video-consent-note"><?php esc_html_e( 'YouTube videos load after you allow the relevant cookies. You can also open any video directly on YouTube.', 'jiggywrigglers' ); ?></p>
            <div class="jiggy-video-grid">
                <?php foreach ( $videos as $index => $video ) : ?>
                    <?php
                    $video_title = wp_strip_all_tags( $video['title'] );
                    $watch_id = $video['id'];
                    if ( is_string( $video['embed'] ) && preg_match( '~(?:youtube(?:-nocookie)?\.com/embed/|youtu\.be/)([a-zA-Z0-9_-]{11})~', $video['embed'], $video_id_match ) ) {
                        $watch_id = $video_id_match[1];
                    }
                    ?>
                    <article class="jiggy-video-card<?php echo 0 === $index ? ' jiggy-video-card-featured' : ''; ?>" aria-labelledby="jiggy-video-card-<?php echo esc_attr( $index ); ?>">
                        <div class="jiggy-video-frame">
                            <?php echo $prepare_embed( $video['embed'], $video_title ); ?>
                        </div>
                        <div class="jiggy-video-card-copy">
                            <p class="jiggy-video-card-label"><?php echo esc_html( $video['label'] ); ?></p>
                            <h3 id="jiggy-video-card-<?php echo esc_attr( $index ); ?>"><?php echo esc_html( $video_title ); ?></h3>
                            <p><?php echo esc_html( $video['description'] ); ?></p>
                            <a class="jiggy-video-watch-link" href="<?php echo esc_url( 'https://www.youtube.com/watch?v=' . $watch_id ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( __( 'Watch %s on YouTube (opens in a new tab)', 'jiggywrigglers' ), $video_title ) ); ?>"><?php esc_html_e( 'Watch on YouTube', 'jiggywrigglers' ); ?><span aria-hidden="true">↗</span></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="jiggy-video-channel">
                <p><?php esc_html_e( 'There’s more Jiggy fun to discover.', 'jiggywrigglers' ); ?></p>
                <a class="jiggy-video-button jiggy-video-button-purple" href="https://www.youtube.com/@JiggyWrigglers2020" target="_blank" rel="noopener noreferrer"><?php echo $play_icon; ?><?php esc_html_e( 'Visit our YouTube channel', 'jiggywrigglers' ); ?><span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </section>

    <section id="jiggy-music" class="jiggy-music" aria-labelledby="jiggy-music-title">
        <div class="wrap jiggy-music-grid">
            <div class="jiggy-music-copy">
                <span class="jiggy-music-note" aria-hidden="true"><?php echo $note_icon; ?></span>
                <p class="jiggy-video-eyebrow"><?php esc_html_e( 'The soundtrack to your Jiggy day', 'jiggywrigglers' ); ?></p>
                <h2 id="jiggy-music-title"><?php echo wp_kses_post( $playlist_title ); ?></h2>
                <p><?php esc_html_e( 'Your favourite Jiggy songs, ready when you are. Pick a platform and bring a little music and movement to your day.', 'jiggywrigglers' ); ?></p>
            </div>
            <div class="jiggy-music-platforms">
                <a class="jiggy-music-platform" href="<?php echo esc_url( $spotify_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Listen to Jiggy Wrigglers on Spotify (opens in a new tab)', 'jiggywrigglers' ); ?>">
                    <span class="jiggy-music-platform-icon jiggy-music-spotify" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg></span>
                    <span><span class="jiggy-music-platform-small"><?php esc_html_e( 'Listen on', 'jiggywrigglers' ); ?></span><strong>Spotify</strong></span><span class="jiggy-music-platform-arrow" aria-hidden="true">↗</span>
                </a>
                <a class="jiggy-music-platform" href="<?php echo esc_url( $apple_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Listen to Jiggy Wrigglers on Apple Music (opens in a new tab)', 'jiggywrigglers' ); ?>">
                    <span class="jiggy-music-platform-icon jiggy-music-apple" aria-hidden="true"><?php echo $note_icon; ?></span>
                    <span><span class="jiggy-music-platform-small"><?php esc_html_e( 'Listen on', 'jiggywrigglers' ); ?></span><strong>Apple Music</strong></span><span class="jiggy-music-platform-arrow" aria-hidden="true">↗</span>
                </a>
            </div>
        </div>
    </section>
</div>
<?php get_footer(); ?>
