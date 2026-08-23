<?php
/**
 * Template Name: Home
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-home/home.css">
<script defer src="<?php echo get_template_directory_uri(); ?>/pages/page-home/home.js"></script>

<?php
// Banner
$banner_images = get_field( 'banner_images' );
$banner_title  = get_field( 'banner_title' );
$banner_text   = get_field( 'banner_text' );

// Introduction
$introduction_sup_title = get_field( 'introduction_sup_title' );
$introduction_title     = get_field( 'introduction_title' );
$introduction_text      = get_field( 'introduction_text' );
$introduction_button    = get_field( 'introduction_button' );

// Groups
$groups_sup_title = get_field( 'groups_sup_title' );
$groups_title     = get_field( 'groups_title' );
$groups_image     = get_field( 'groups_image' );
$groups_repeater  = get_field( 'groups_repeater' );

// Content 1
$content_1_sup_title = get_field( 'content_1_sup_title' );
$content_1_title     = get_field( 'content_1_title' );
$content_1_text      = get_field( 'content_1_text' );
$content_1_button    = get_field( 'content_1_button' );

// Content 2
$content_2_title  = get_field( 'content_2_title' );
$content_2_text   = get_field( 'content_2_text' );
$content_2_button = get_field( 'content_2_button' );

// Testimonials (page-level, passed to the component)
$testimonials_sup_title = get_field( 'testimonials_sup_title' );
$testimonials_title     = get_field( 'testimonials_title' );
$testimonials           = get_field( 'testimonial_repeater' );
?>

<!-- Banner Section -->
<!-- ------------------------------------------------- -->
<section class="home-banner">
    <?php if ( $banner_images ) : ?>
        <div class="home-banner-slider splide">
            <div class="splide__track">
                <div class="splide__list">
                    <?php foreach ( $banner_images as $banner_image ) : ?>
                        <div class="splide__slide">
                            <?php echo wp_get_attachment_image( $banner_image['ID'], 'full' ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="splide__pagination"></div>
        </div>
    <?php endif; ?>
    <div class="wrap">
        <div class="home-banner-content">
            <?php if ( $banner_title ) : ?>
                <h1 class="heading-1"><?php echo wp_kses_post( $banner_title ); ?></h1>
            <?php endif; ?>
            <?php if ( $banner_text ) : ?>
                <p class="body-medium home-banner-text"><?php echo wp_kses_post( $banner_text ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Introduction Section -->
<!-- ------------------------------------------------- -->
<section class="home-introduction">
    <div class="wrap">
        <?php if ( $introduction_sup_title ) : ?>
            <h3 class="heading-5"><?php echo wp_kses_post( $introduction_sup_title ); ?></h3>
        <?php endif; ?>
        <?php if ( $introduction_title ) : ?>
            <h2 class="heading-2"><?php echo wp_kses_post( $introduction_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $introduction_text ) : ?>
            <div class="home-introduction-text"><?php echo wp_kses_post( $introduction_text ); ?></div>
        <?php endif; ?>
        <?php if ( $introduction_button ) : ?>
            <a class="button button-blue" href="<?php echo esc_url( $introduction_button['url'] ); ?>"<?php echo ! empty( $introduction_button['target'] ) ? ' target="' . esc_attr( $introduction_button['target'] ) . '"' : ''; ?>>
                <?php echo esc_html( $introduction_button['title'] ); ?>
            </a>
        <?php endif; ?>
    </div>
</section>

<!-- Groups Section -->
<!-- ------------------------------------------------- -->
<?php if ( $groups_repeater ) : ?>
<section class="home-groups">
    <div class="home-groups-header">
        <?php if ( $groups_sup_title ) : ?>
            <h3 class="heading-5"><?php echo wp_kses_post( $groups_sup_title ); ?></h3>
        <?php endif; ?>
        <?php if ( $groups_title ) : ?>
            <h2 class="heading-2"><?php echo wp_kses_post( $groups_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $groups_image ) : ?>
            <div class="home-groups-header-image">
                <?php echo wp_get_attachment_image( $groups_image['ID'], 'full' ); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="wrap">
        <div class="home-groups-grid">
            <?php foreach ( $groups_repeater as $group ) : ?>
                <div class="home-group">
                    <?php if ( ! empty( $group['image'] ) ) : ?>
                        <div class="home-group-image">
                            <?php echo wp_get_attachment_image( $group['image']['ID'], 'full' ); ?>
                        </div>
                    <?php endif; ?>
                    <div class="home-group-content">
                        <?php if ( ! empty( $group['title'] ) ) : ?>
                            <h4 class="heading-4"><?php echo wp_kses_post( $group['title'] ); ?></h4>
                        <?php endif; ?>
                        <?php if ( ! empty( $group['text'] ) ) : ?>
                            <p class="body-medium"><?php echo wp_kses_post( $group['text'] ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $group['link'] ) ) : ?>
                            <a class="button button-pink" href="<?php echo esc_url( $group['link'] ); ?>">Read More</a>
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $group['logo'] ) ) : ?>
                        <div class="home-group-logo">
                            <?php echo wp_get_attachment_image( $group['logo']['ID'], 'full' ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Content 1 Section -->
<!-- ------------------------------------------------- -->
<?php if ( $content_1_title || $content_1_text ) : ?>
<section class="home-content-1">
    <div class="wrap">
        <?php if ( $content_1_sup_title ) : ?>
            <h3 class="heading-5"><?php echo wp_kses_post( $content_1_sup_title ); ?></h3>
        <?php endif; ?>
        <?php if ( $content_1_title ) : ?>
            <h2 class="heading-2"><?php echo wp_kses_post( $content_1_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $content_1_text ) : ?>
            <p class="body-medium"><?php echo wp_kses_post( $content_1_text ); ?></p>
        <?php endif; ?>
        <?php if ( $content_1_button ) : ?>
            <a class="button button-orange" href="<?php echo esc_url( $content_1_button['url'] ); ?>"<?php echo ! empty( $content_1_button['target'] ) ? ' target="' . esc_attr( $content_1_button['target'] ) . '"' : ''; ?>>
                <?php echo esc_html( $content_1_button['title'] ); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Content 2 Section -->
<!-- ------------------------------------------------- -->
<?php if ( $content_2_title || $content_2_text ) : ?>
<section class="home-content-2">
    <div class="wrap">
        <?php if ( $content_2_title ) : ?>
            <h2 class="heading-2"><?php echo wp_kses_post( $content_2_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $content_2_text ) : ?>
            <p class="body-medium"><?php echo wp_kses_post( $content_2_text ); ?></p>
        <?php endif; ?>
        <?php if ( $content_2_button ) : ?>
            <a class="button button-yellow" href="<?php echo esc_url( $content_2_button['url'] ); ?>"<?php echo ! empty( $content_2_button['target'] ) ? ' target="' . esc_attr( $content_2_button['target'] ) . '"' : ''; ?>>
                <?php echo esc_html( $content_2_button['title'] ); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php include locate_template( 'components/testimonials/index.php' ); ?>

<?php get_footer(); ?>
