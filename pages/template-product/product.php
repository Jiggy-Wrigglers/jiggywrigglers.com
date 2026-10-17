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
get_header();
$shop_url = jiggy_wrigglers_surecart_page_url( 'shop' );
?>

<!-- Product Content Section -->
<!-- ------------------------------------------------- -->
<section class="product-content">
    <div class="wrap">
        <?php if ( $shop_url ) : ?>
            <nav class="product-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'jiggywrigglers' ); ?>">
                <a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop', 'jiggywrigglers' ); ?></a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?php echo esc_html( get_the_title() ); ?></span>
            </nav>
        <?php endif; ?>
        <?php
        while ( have_posts() ) :
            the_post();
            // SureCart's native content filter renders the assigned product
            // template part, including variants, inventory and buy buttons.
            the_content();
        endwhile;
        ?>
    </div>
</section>

<?php get_footer(); ?>
