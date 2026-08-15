<?php
/**
 * SureCart Product Template
 *
 * Single shop item page. Renders the product's block content
 * (SureCart blocks for title, price, media, buy button) inside
 * the theme wrapper.
 *
 * Routing: functions/surecart.php template_include filter.
 * Fallback: single-sc_product.php.
 *
 * @package Jiggy_Wrigglers
 */
get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/template-product/product.css">

<!-- Product Content Section -->
<!-- ------------------------------------------------- -->
<section class="product-content">
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
