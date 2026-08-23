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

<?php
// Introduction
$introduction_sup_title = get_field( 'introduction_sup_title' );
$introduction_title     = get_field( 'introduction_title' );
$introduction_text      = get_field( 'introduction_text' );

// Journey
$journey_title    = get_field( 'journey_title' );
$journey_repeater = get_field( 'journey_repeater' );

// Awards
$awards_title  = get_field( 'awards_title' );
$awards_images = get_field( 'awards_images' );

// Content
$content_sup_title = get_field( 'content_sup_title' );
$content_title     = get_field( 'content_title' );
$content_text      = get_field( 'content_text' );
$content_button    = get_field( 'content_button' );
?>

<!-- Introduction Section -->
<!-- ------------------------------------------------- -->
<section class="about-introduction">
    <div class="wrap">
        <?php if ( $introduction_sup_title ) : ?>
            <h3 class="heading-5"><?php echo wp_kses_post( $introduction_sup_title ); ?></h3>
        <?php endif; ?>
        <?php if ( $introduction_title ) : ?>
            <h2 class="heading-2"><?php echo wp_kses_post( $introduction_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $introduction_text ) : ?>
            <div class="about-introduction-text"><?php echo wp_kses_post( $introduction_text ); ?></div>
        <?php endif; ?>
    </div>
</section>

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
            <div class="about-journey-step<?php echo $i % 2 !== 0 ? ' about-journey-step--reversed' : ''; ?>">
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
                        <div class="about-journey-trail">
                            <?php echo wp_get_attachment_image( $journey['journey_trail']['ID'], 'full' ); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="about-journey-image">
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
            <?php foreach ( $awards_images as $award ) : ?>
                <div class="about-award">
                    <img src="<?php echo esc_url( $award['url'] ); ?>" alt="<?php echo esc_attr( $award['alt'] ); ?>" loading="lazy">
                    <?php if ( $award['alt'] ) : ?>
                        <h4 class="heading-6"><?php echo esc_html( $award['alt'] ); ?></h4>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Content Section -->
<!-- ------------------------------------------------- -->
<?php if ( $content_title || $content_text ) : ?>
<section class="about-content">
    <div class="wrap">
        <?php if ( $content_sup_title ) : ?>
            <h3 class="heading-5"><?php echo wp_kses_post( $content_sup_title ); ?></h3>
        <?php endif; ?>
        <?php if ( $content_title ) : ?>
            <h2 class="heading-2"><?php echo wp_kses_post( $content_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $content_text ) : ?>
            <div class="about-content-text"><?php echo wp_kses_post( $content_text ); ?></div>
        <?php endif; ?>
        <?php if ( $content_button ) : ?>
            <a class="button button-purple" href="<?php echo esc_url( $content_button['url'] ); ?>"<?php echo ! empty( $content_button['target'] ) ? ' target="' . esc_attr( $content_button['target'] ) . '"' : ''; ?>>
                <?php echo esc_html( $content_button['title'] ); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
