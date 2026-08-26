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
    $jiggy_gallery_title = get_field( 'jiggy_gallery_title', 'option' );
    $jiggy_gallery_images = get_field( 'jiggy_gallery_images', 'option' );

    if ( empty( $jiggy_gallery_images ) ) return;
?>
<section class="gallery" x-data="{ shown: false }" x-intersect:enter.threshold.0.2="shown = true">
    <div class="wrap">
        <div class="gallery-header">
            <?php if ( $jiggy_gallery_sup_title ) : ?>
                <h3 class="heading-5" data-animate="fade-up" :class="shown && 'is-visible'"><?php echo wp_kses_post( $jiggy_gallery_sup_title ); ?></h3>
            <?php endif; ?>
            <?php if ( $jiggy_gallery_title ) : ?>
                <h2 class="heading-2" data-animate="fade-up" data-animate-delay="1" :class="shown && 'is-visible'"><?php echo wp_kses_post( $jiggy_gallery_title ); ?></h2>
            <?php endif; ?>
        </div>

        <div class="gallery-slider splide" data-animate="fade-up" data-animate-delay="2" :class="shown && 'is-visible'">
            <div class="splide__track">
                <div class="splide__list">
                    <?php foreach ( $jiggy_gallery_images as $image ) : ?>
                        <div class="splide__slide gallery-slide">
                            <?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="gallery-controls">
                <button class="gallery-prev" type="button" aria-label="Previous image">&larr;</button>
                <button class="gallery-next" type="button" aria-label="Next image">&rarr;</button>
            </div>
        </div>
    </div>
</section>
