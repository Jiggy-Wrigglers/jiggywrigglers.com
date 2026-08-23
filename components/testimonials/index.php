<?php
/**
 * Testimonials Component
 *
 * Splide slider of testimonial quotes. Uses the Site Settings
 * testimonial fields by default; override with $testimonials_sup_title,
 * $testimonials_title and $testimonials before include.
 *
 * @package Jiggy_Wrigglers
 */
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/components/testimonials/style.css">
<script defer src="<?php echo get_template_directory_uri(); ?>/components/testimonials/index.js"></script>
<!-- Testimonials Component -->
<?php
    $testimonials_sup_title = isset( $testimonials_sup_title ) ? $testimonials_sup_title : get_field( 'testimonial_sup_title', 'option' );
    $testimonials_title     = isset( $testimonials_title ) ? $testimonials_title : get_field( 'testimonial_title', 'option' );
    $testimonials           = isset( $testimonials ) ? $testimonials : get_field( 'testimonial_repeater', 'option' );

    if ( empty( $testimonials ) ) return;
?>
<section class="testimonials">
    <div class="wrap">
        <div class="testimonials-header">
            <?php if ( $testimonials_sup_title ) : ?>
                <h3 class="heading-5"><?php echo wp_kses_post( $testimonials_sup_title ); ?></h3>
            <?php endif; ?>
            <?php if ( $testimonials_title ) : ?>
                <h2 class="heading-2"><?php echo wp_kses_post( $testimonials_title ); ?></h2>
            <?php endif; ?>
        </div>

        <div class="testimonials-slider splide">
            <div class="splide__track">
                <div class="splide__list">
                    <?php foreach ( $testimonials as $testimonial ) : ?>
                        <div class="splide__slide testimonials-slide">
                            <div class="testimonials-slide-inner">
                                <?php if ( ! empty( $testimonial['quote'] ) ) : ?>
                                    <p class="body-medium testimonials-quote">&ldquo;<?php echo wp_kses_post( $testimonial['quote'] ); ?>&rdquo;</p>
                                <?php endif; ?>
                                <?php if ( ! empty( $testimonial['name'] ) ) : ?>
                                    <p class="testimonials-name"><?php echo esc_html( $testimonial['name'] ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $testimonial['job'] ) ) : ?>
                                    <p class="body-medium testimonials-job"><?php echo esc_html( $testimonial['job'] ); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="testimonials-controls">
                <button class="testimonials-prev" type="button" aria-label="Previous testimonial">&larr;</button>
                <button class="testimonials-next" type="button" aria-label="Next testimonial">&rarr;</button>
            </div>
        </div>
    </div>
</section>
