<?php
/**
 * Gallery Component
 *
 * Splide slider of images from Site Settings (Jiggy Gallery tab).
 *
 * @package Jiggy_Wrigglers
 */
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/components/gallery/style.css">
<script defer src="<?php echo get_template_directory_uri(); ?>/components/gallery/index.js"></script>
<!-- Gallery Component -->
<?php
    $jiggy_gallery_sup_title = get_field( 'jiggy_gallery_sup_title', 'option' );
    $jiggy_gallery_title     = get_field( 'jiggy_gallery_title', 'option' );
    $jiggy_gallery_images    = get_field( 'jiggy_gallery_images', 'option' );

    if ( empty( $jiggy_gallery_images ) ) return;
?>
<section class="gallery">
    <div class="wrap">
        <div class="gallery-header">
            <?php if ( $jiggy_gallery_sup_title ) : ?>
                <h3 class="heading-5"><?php echo wp_kses_post( $jiggy_gallery_sup_title ); ?></h3>
            <?php endif; ?>
            <?php if ( $jiggy_gallery_title ) : ?>
                <h2 class="heading-2"><?php echo wp_kses_post( $jiggy_gallery_title ); ?></h2>
            <?php endif; ?>
        </div>

        <div class="gallery-slider splide">
            <div class="splide__track">
                <div class="splide__list">
                    <?php foreach ( $jiggy_gallery_images as $image ) : ?>
                        <div class="splide__slide gallery-slide">
                            <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="splide__pagination"></div>
        </div>
    </div>
</section>
