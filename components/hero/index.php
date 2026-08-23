<?php
/**
 * Hero Section
 *
 * Full-height page banner matching the homepage banner, using a gallery
 * of images (first image shown) instead of the slider. Values can be
 * overridden by setting $hero_* variables before include.
 *
 * @package Jiggy_Wrigglers
 */
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/components/hero/style.css">
<!-- Hero Section -->
<?php
    $hero_post_id = isset( $hero_post_id ) ? $hero_post_id : null;
    $hero_images = isset( $hero_images ) ? $hero_images : get_field( 'hero_images', $hero_post_id );
    $hero_title = isset( $hero_title ) ? $hero_title : get_field( 'hero_title', $hero_post_id );
    $hero_sub_title = isset( $hero_sub_title ) ? $hero_sub_title : get_field( 'hero_sub_title', $hero_post_id );
    $hero_text = isset( $hero_text ) ? $hero_text : get_field( 'hero_text', $hero_post_id );
    $hero_buttons = isset( $hero_buttons ) ? $hero_buttons : get_field( 'hero_buttons', $hero_post_id );

    $hero_image = is_array( $hero_images ) && ! empty( $hero_images ) ? $hero_images[0] : null;
?>
<section class="hero">
    <?php if ( $hero_image ) : ?>
        <?php echo wp_get_attachment_image( $hero_image['ID'], 'full' ); ?>
    <?php endif; ?>
    <div class="wrap">
        <?php if ( $hero_title ) : ?>
            <h1 class="heading-1" data-animate="fade-up"><?php echo wp_kses_post( $hero_title ); ?></h1>
        <?php endif; ?>
        <?php if ( $hero_text ) : ?>
            <p class="body-medium hero-text" data-animate="fade-up" data-animate-delay="1"><?php echo wp_kses_post( $hero_text ); ?></p>
        <?php endif; ?>
        <?php if ( $hero_sub_title ) : ?>
            <h2 class="heading-4 hero-sub-title" data-animate="fade-up" data-animate-delay="2"><?php echo wp_kses_post( $hero_sub_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $hero_buttons ) : ?>
            <div class="hero-buttons" data-animate="fade-up" data-animate-delay="3">
                <?php foreach ( $hero_buttons as $hero_button ) : ?>
                    <?php if ( ! empty( $hero_button['button'] ) ) :
                        $colour = ! empty( $hero_button['colour'] ) ? $hero_button['colour'] : 'orange'; ?>
                        <a class="button button-<?php echo esc_attr( $colour ); ?>" href="<?php echo esc_url( $hero_button['button']['url'] ); ?>"<?php echo ! empty( $hero_button['button']['target'] ) ? ' target="' . esc_attr( $hero_button['button']['target'] ) . '"' : ''; ?>>
                            <?php echo esc_html( $hero_button['button']['title'] ); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
