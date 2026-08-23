<?php
/**
 * Register Interest Component
 *
 * Call-to-action band from Site Settings. Hidden entirely
 * when the toggle is off.
 *
 * @package Jiggy_Wrigglers
 */
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/components/register-interest/style.css">
<!-- Register Interest Component -->
<?php
    $register_interest_cta = get_field( 'register_interest_cta', 'option' );

    if ( ! $register_interest_cta ) return;

    $register_heading = get_field( 'register_heading', 'option' );
    $register_title   = get_field( 'register_title', 'option' );
    $register_text    = get_field( 'register_text', 'option' );
    $register_button  = get_field( 'register_button', 'option' );
    $register_image   = get_field( 'register_image', 'option' );
?>
<section class="register-interest">
    <div class="wrap">
        <div class="register-interest-content">
            <?php if ( $register_heading ) : ?>
                <h3 class="heading-5" data-animate="fade-up"><?php echo wp_kses_post( $register_heading ); ?></h3>
            <?php endif; ?>
            <?php if ( $register_title ) : ?>
                <h2 class="heading-2" data-animate="fade-up" data-animate-delay="1"><?php echo wp_kses_post( $register_title ); ?></h2>
            <?php endif; ?>
            <?php if ( $register_text ) : ?>
                <p class="body-medium register-interest-text" data-animate="fade-up" data-animate-delay="2"><?php echo wp_kses_post( $register_text ); ?></p>
            <?php endif; ?>
            <?php if ( $register_button ) : ?>
                <a class="button button-orange" data-animate="fade-up" data-animate-delay="3" href="<?php echo esc_url( $register_button['url'] ); ?>"<?php echo ! empty( $register_button['target'] ) ? ' target="' . esc_attr( $register_button['target'] ) . '"' : ''; ?>>
                    <?php echo esc_html( $register_button['title'] ); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if ( $register_image ) : ?>
            <div class="register-interest-image" data-animate="fade-up" data-animate-delay="2">
                <?php echo wp_get_attachment_image( $register_image['ID'], 'full' ); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
