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
    $hero_title  = get_field( 'hero_title', 'option' ) ?: '404 - Page Not Found';
    $hero_text   = get_field( 'hero_text', 'option' ) ?: "The page you are looking for could not be found. It may have been moved or no longer exists.";
    $hero_button = get_field( 'hero_button_1', 'option' );
    $hero_image  = get_field( 'hero_image', 'option' );

    if ( empty( $hero_button ) ) {
        $hero_button = array(
            'title'  => 'Back to Home',
            'url'    => home_url( '/' ),
            'target' => '',
        );
    }
?>

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php get_footer(); ?>
