<?php
/**
 * 404 Template
 *
 * Self-contained 404 page. Content managed via Site Settings
 * (ACF group: Page Not Found).
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>

<?php
    $hero_title   = get_field( 'hero_title', 'option' ) ?: '404 - Page Not Found';
    $hero_text    = get_field( 'hero_text', 'option' ) ?: "The page you are looking for could not be found. It may have been moved or no longer exists.";
    $hero_buttons = get_field( 'hero_buttons', 'option' );
    $hero_images  = get_field( 'hero_images', 'option' );

    if ( empty( $hero_buttons ) ) {
        $hero_buttons = array(
            array(
                'button' => array(
                    'title'  => 'Back to Home',
                    'url'    => home_url( '/' ),
                    'target' => '',
                ),
                'colour' => 'orange',
            ),
        );
    }
?>

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php get_footer(); ?>
