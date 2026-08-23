<?php
/**
 * Hero Section
 *
 * A full-width hero with image background, title, text and button.
 * Values can be overridden by setting $hero_* variables before include.
 *
 * @package Jiggy_Wrigglers
 */
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/components/hero/style.css">
<!-- Hero Section -->
<?php
    $hero_post_id = isset( $hero_post_id ) ? $hero_post_id : null;
    $hero_image   = isset( $hero_image ) ? $hero_image : get_field( 'hero_image', $hero_post_id );
    $hero_title   = isset( $hero_title ) ? $hero_title : get_field( 'hero_title', $hero_post_id );
    $hero_text    = isset( $hero_text ) ? $hero_text : get_field( 'hero_text', $hero_post_id );
    $hero_button  = isset( $hero_button ) ? $hero_button : get_field( 'hero_button', $hero_post_id );
?>
<section class="hero">
    <?php if ( $hero_image ) : ?>
        <?php echo wp_get_attachment_image( $hero_image['ID'], 'full' ); ?>
    <?php endif; ?>
    <div class="wrap">
        <?php if ( $hero_text ) : ?>
            <p class="body-medium hero-text"><?php echo wp_kses_post( $hero_text ); ?></p>
        <?php endif; ?>
        <?php if ( $hero_title ) : ?>
            <h1 class="heading-1"><?php echo wp_kses_post( $hero_title ); ?></h1>
        <?php endif; ?>
        <?php if ( $hero_button ) : ?>
            <a class="button button-orange" href="<?php echo esc_url( $hero_button['url'] ); ?>"<?php echo ! empty( $hero_button['target'] ) ? ' target="' . esc_attr( $hero_button['target'] ) . '"' : ''; ?>>
                <?php echo esc_html( $hero_button['title'] ); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
