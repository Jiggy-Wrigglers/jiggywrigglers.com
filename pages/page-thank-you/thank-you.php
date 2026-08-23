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

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php get_footer(); ?>
