<?php
/**
 * Template Name: Thank You
 *
 * Single-use thank-you page shown after form submission.
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-thank-you/thank-you.css">

<?php
$hero_title  = get_field( 'hero_title' );
$hero_text   = get_field( 'hero_text' );
$hero_button = get_field( 'hero_button_1' );
$hero_image  = get_field( 'hero_image' );
?>

<!-- Thank You Section -->
<!-- ------------------------------------------------- -->
<section class="thank-you">
    <div class="wrap">
        <div class="thank-you-content">
            <?php if ( $hero_title ) : ?>
                <h1 class="heading-1"><?php echo wp_kses_post( $hero_title ); ?></h1>
            <?php endif; ?>
            <?php if ( $hero_text ) : ?>
                <p class="body-medium"><?php echo wp_kses_post( $hero_text ); ?></p>
            <?php endif; ?>
            <?php if ( $hero_button ) : ?>
                <a class="button button-orange" href="<?php echo esc_url( $hero_button['url'] ); ?>"<?php echo ! empty( $hero_button['target'] ) ? ' target="' . esc_attr( $hero_button['target'] ) . '"' : ''; ?>>
                    <?php echo esc_html( $hero_button['title'] ); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if ( $hero_image ) : ?>
            <div class="thank-you-image">
                <?php echo wp_get_attachment_image( $hero_image['ID'], 'full' ); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
