<?php
/**
 * Template Name: Programmes
 *
 * Reusable class programmes template. Assign to any page that
 * lists class programmes / age groups.
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/template-programmes/programmes.css">

<?php
// Information
$information_title      = get_field( 'information_title' );
$information_text       = get_field( 'information_text' );
$information_age_range  = get_field( 'information_age_range' );
$information_length     = get_field( 'information_length' );
$information_group_size = get_field( 'information_group_size' );

// Content
$content_text     = get_field( 'content_text' );
$content_button_1 = get_field( 'content_button_1' );
$content_button_2 = get_field( 'content_button_2' );
$content_image    = get_field( 'content_image' );
?>

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php
$hero_button   = get_field( 'hero_button_1' );
$hero_button_2 = get_field( 'hero_button_2' );
include locate_template( 'components/hero/index.php' );
?>

<!-- Information Section -->
<!-- ------------------------------------------------- -->
<section class="programmes-information">
    <div class="wrap">
        <?php if ( $information_title ) : ?>
            <h2 class="heading-2"><?php echo wp_kses_post( $information_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $information_text ) : ?>
            <p class="body-medium"><?php echo wp_kses_post( $information_text ); ?></p>
        <?php endif; ?>
        <div class="programmes-information-grid">
            <?php if ( $information_age_range ) : ?>
                <div class="programmes-information-box">
                    <h3 class="heading-6">Age Range</h3>
                    <p class="body-medium"><?php echo wp_kses_post( $information_age_range ); ?></p>
                </div>
            <?php endif; ?>
            <?php if ( $information_length ) : ?>
                <div class="programmes-information-box">
                    <h3 class="heading-6">Class Length</h3>
                    <p class="body-medium"><?php echo wp_kses_post( $information_length ); ?></p>
                </div>
            <?php endif; ?>
            <?php if ( $information_group_size ) : ?>
                <div class="programmes-information-box">
                    <h3 class="heading-6">Group Size</h3>
                    <p class="body-medium"><?php echo wp_kses_post( $information_group_size ); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Content Section -->
<!-- ------------------------------------------------- -->
<?php if ( $content_text ) : ?>
<section class="programmes-content">
    <div class="wrap">
        <div class="programmes-content-text"><?php echo wp_kses_post( $content_text ); ?></div>
        <div class="programmes-content-buttons">
            <?php if ( $content_button_1 ) : ?>
                <a class="button button-purple" href="<?php echo esc_url( $content_button_1['url'] ); ?>"<?php echo ! empty( $content_button_1['target'] ) ? ' target="' . esc_attr( $content_button_1['target'] ) . '"' : ''; ?>>
                    <?php echo esc_html( $content_button_1['title'] ); ?>
                </a>
            <?php endif; ?>
            <?php if ( $content_button_2 ) : ?>
                <a class="button button-pink" href="<?php echo esc_url( $content_button_2['url'] ); ?>"<?php echo ! empty( $content_button_2['target'] ) ? ' target="' . esc_attr( $content_button_2['target'] ) . '"' : ''; ?>>
                    <?php echo esc_html( $content_button_2['title'] ); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if ( $content_image ) : ?>
            <div class="programmes-content-image" data-animate="fade-up" data-animate-delay="2">
                <?php echo wp_get_attachment_image( $content_image['ID'], 'full' ); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
