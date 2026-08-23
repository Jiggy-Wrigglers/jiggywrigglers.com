<?php
/**
 * Ethos Component
 *
 * Grid of ethos cards (text, button, image) from Site Settings.
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
    <div class="wrap">
        <?php if ( $ethos_title ) : ?>
            <h2 class="heading-2 ethos-title"><?php echo wp_kses_post( $ethos_title ); ?></h2>
        <?php endif; ?>

        <div class="ethos-grid">
            <?php foreach ( $ethos_repeater as $ethos ) : ?>
                <div class="ethos-card">
                    <?php if ( ! empty( $ethos['image'] ) ) : ?>
                        <div class="ethos-card-image">
                            <?php echo wp_get_attachment_image( $ethos['image']['ID'], 'full' ); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $ethos['text'] ) ) : ?>
                        <p class="body-medium ethos-card-text"><?php echo wp_kses_post( $ethos['text'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( ! empty( $ethos['button'] ) ) : ?>
                        <a class="button button-purple" href="<?php echo esc_url( $ethos['button']['url'] ); ?>"<?php echo ! empty( $ethos['button']['target'] ) ? ' target="' . esc_attr( $ethos['button']['target'] ) . '"' : ''; ?>>
                            <?php echo esc_html( $ethos['button']['title'] ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
