<?php
/**
 * Template Name: Shop Item
 *
 * Reusable shop item template. Renders the item's block content
 * (SureCart blocks) inside the theme wrapper.
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/template-shop-item/shop-item.css">

<!-- Shop Item Content Section -->
<!-- ------------------------------------------------- -->
<section class="shop-item-content">
    <div class="wrap">
        <?php
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
</section>

<?php get_footer(); ?>
