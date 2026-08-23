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

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php
// Key Info
$information_title = get_field( 'information_title' );
$information_text = get_field( 'information_text' );
$key_info_boxes = array(
    array( 'icon' => get_field( 'key_info_1_icon' ), 'title' => get_field( 'key_info_1_title' ) ),
    array( 'icon' => get_field( 'key_info_2_icon' ), 'title' => get_field( 'key_info_2_title' ) ),
    array( 'icon' => get_field( 'key_info_3_icon' ), 'title' => get_field( 'key_info_3_title' ) ),
);

// Content Columns
$content_text = get_field( 'content_text' );
$content_button_1 = get_field( 'content_button_1' );
$content_button_2 = get_field( 'content_button_2' );
$content_image = get_field( 'content_image' );
?>

<!-- Key Info Text Section -->
<!-- ------------------------------------------------- -->
<section class="key-info-text-block">
    <?php if ( $information_title ) : ?>
        <h2 class="heading-3 key-info-title" data-animate="fade-up"><?php echo wp_kses_post( $information_title ); ?></h2>
    <?php endif; ?>

    <?php if ( $information_text || $key_info_boxes ) : ?>
        <div class="wrap key-info-strip" data-animate="fade-up" data-animate-delay="1">
            <?php if ( $information_text ) : ?>
                <div class="key-info-box key-info-intro">
                    <p><?php echo wp_kses_post( $information_text ); ?></p>
                </div>
            <?php endif; ?>
            <?php foreach ( $key_info_boxes as $box ) : ?>
                <?php if ( $box['icon'] || $box['title'] ) : ?>
                    <div class="key-info-box">
                        <?php if ( $box['icon'] ) : ?>
                            <div class="key-info-icon"><?php echo $box['icon']; ?></div>
                        <?php endif; ?>
                        <?php if ( $box['title'] ) : ?>
                            <h3 class="heading-5"><?php echo wp_kses_post( $box['title'] ); ?></h3>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ( $content_text || $content_image ) : ?>
        <div class="wrap content-columns">
            <div class="content-columns-1" data-animate="fade-up">
                <?php if ( $content_text ) : ?>
                    <div class="content-columns-text"><?php echo wp_kses_post( $content_text ); ?></div>
                <?php endif; ?>
                <?php if ( $content_button_1 || $content_button_2 ) : ?>
                    <div class="content-columns-button">
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
                <?php endif; ?>
            </div>
            <?php if ( $content_image ) : ?>
                <div class="content-columns-2" data-animate="fade-up" data-animate-delay="1">
                    <div class="image">
                        <?php echo wp_get_attachment_image( $content_image['ID'], 'full' ); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Gallery Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/gallery/index.php' ); ?>

<!-- Ethos Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/ethos/index.php' ); ?>

<!-- Testimonials Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/testimonials/index.php' ); ?>

<?php get_footer(); ?>
