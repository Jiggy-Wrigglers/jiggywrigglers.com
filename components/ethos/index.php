<?php
/**
 * Ethos Component
 *
 * The old site's content-journey block: purple section, centred title,
 * alternating text/image rows with a trail image dangling below the text.
 * Fields come from Site Settings (Ethos tab).
 *
 * @package Jiggy_Wrigglers
 */
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/components/ethos/style.css">
<!-- Ethos Component -->
<?php
    $ethos_title    = get_field( 'ethos_title', 'option' );
    $ethos_repeater = get_field( 'ethos_repeater', 'option' );

    if ( empty( $ethos_repeater ) ) return;
?>
<section class="ethos">
    <?php if ( $ethos_title ) : ?>
        <h2 class="heading-2 ethos-title" data-animate="fade-up"><?php echo wp_kses_post( $ethos_title ); ?></h2>
    <?php endif; ?>
    <div class="wrap">
        <?php foreach ( $ethos_repeater as $i => $ethos ) : ?>
            <div class="single-journey<?php echo $i % 2 !== 0 ? ' journey-reversed' : ''; ?>" data-animate="fade-up">
                <div class="journey-text">
                    <?php if ( ! empty( $ethos['text'] ) ) : ?>
                        <p class="body-medium"><?php echo wp_kses_post( $ethos['text'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( ! empty( $ethos['button'] ) ) : ?>
                        <a class="button button-orange" href="<?php echo esc_url( $ethos['button']['url'] ); ?>"<?php echo ! empty( $ethos['button']['target'] ) ? ' target="' . esc_attr( $ethos['button']['target'] ) . '"' : ''; ?>>
                            <?php echo esc_html( $ethos['button']['title'] ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $ethos['journey_trail'] ) ) : ?>
                        <div class="content-trail before-trail" aria-hidden="true">
                            <?php echo wp_get_attachment_image( $ethos['journey_trail']['ID'], 'full' ); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="journey-image-area" data-animate="fade-up" data-animate-delay="1">
                    <?php if ( ! empty( $ethos['image'] ) ) : ?>
                        <?php echo wp_get_attachment_image( $ethos['image']['ID'], 'full' ); ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
