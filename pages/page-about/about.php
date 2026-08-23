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

<!-- Ethos Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/ethos/index.php' ); ?>

<?php
// Journey
$journey_title    = get_field( 'journey_title' );
$journey_repeater = get_field( 'journey_repeater' );

// Awards
$awards_title  = get_field( 'awards_title' );
$awards_images = get_field( 'awards_images' );
?>

<!-- Journey Section -->
<!-- ------------------------------------------------- -->
<?php if ( $journey_repeater ) : ?>
<section class="about-journey">
    <?php if ( $journey_title ) : ?>
        <div class="wrap">
            <h2 class="heading-2 about-journey-title"><?php echo wp_kses_post( $journey_title ); ?></h2>
        </div>
    <?php endif; ?>
    <div class="wrap">
        <?php foreach ( $journey_repeater as $i => $journey ) : ?>
            <div class="about-journey-step<?php echo $i % 2 !== 0 ? ' about-journey-step--reversed' : ''; ?>" data-animate="fade-up">
                <div class="about-journey-text">
                    <?php if ( ! empty( $journey['text'] ) ) : ?>
                        <p class="body-medium"><?php echo wp_kses_post( $journey['text'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( ! empty( $journey['button'] ) ) : ?>
                        <a class="button button-orange" href="<?php echo esc_url( $journey['button']['url'] ); ?>"<?php echo ! empty( $journey['button']['target'] ) ? ' target="' . esc_attr( $journey['button']['target'] ) . '"' : ''; ?>>
                            <?php echo esc_html( $journey['button']['title'] ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $journey['journey_trail'] ) ) : ?>
                        <div class="about-journey-trail" data-animate="fade-in" data-animate-delay="2">
                            <?php echo wp_get_attachment_image( $journey['journey_trail']['ID'], 'full' ); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="about-journey-image" data-animate="fade-up" data-animate-delay="1">
                    <?php if ( ! empty( $journey['image'] ) ) : ?>
                        <?php echo wp_get_attachment_image( $journey['image']['ID'], 'full' ); ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

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
