<?php
/**
 * Template Name: About
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-about/about.css">

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<!-- Ethos Section (content-journey block, Site Settings) -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/ethos/index.php' ); ?>

<?php
// Awards
$awards_title  = get_field( 'awards_title' );
$awards_images = get_field( 'awards_images' );
?>

<!-- Awards Section -->
<!-- ------------------------------------------------- -->
<?php if ( $awards_images ) : ?>
<section class="about-awards">
    <div class="wrap">
        <?php if ( $awards_title ) : ?>
            <h2 class="heading-2 about-awards-title"><?php echo wp_kses_post( $awards_title ); ?></h2>
        <?php endif; ?>
        <div class="about-awards-grid">
            <?php foreach ( $awards_images as $i => $award ) : ?>
                <div class="about-award" data-animate="scale-up" data-animate-delay="<?php echo esc_attr( ( $i % 4 ) + 1 ); ?>">
                    <?php echo wp_get_attachment_image( $award['ID'], 'full' ); ?>
                    <?php if ( $award['alt'] ) : ?>
                        <h4 class="heading-6"><?php echo esc_html( $award['alt'] ); ?></h4>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/gallery/index.php' ); ?>

<!-- Testimonials Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/testimonials/index.php' ); ?>

<?php get_footer(); ?>
