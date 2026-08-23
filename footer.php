<?php
/**
 * The template for displaying the footer
 *
 * Footer content is managed via the Site Settings options page
 * (ACF field group: Header & Footer).
 *
 * @package Jiggy_Wrigglers
 */

$footer_1_title = get_field( 'footer_1_title', 'option' );
$footer_1_text = get_field( 'footer_1_text', 'option' );
$footer_2_title = get_field( 'footer_2_title', 'option' );
$footer_3_title = get_field( 'footer_3_title', 'option' );
$footer_3_text = get_field( 'footer_3_text', 'option' );
$footer_4_title = get_field( 'footer_4_title', 'option' );
$footer_4_button_1 = get_field( 'footer_4_button_1', 'option' );
$footer_4_button_2 = get_field( 'footer_4_button_2', 'option' );
$copyright_link = get_field( 'copyright_link', 'option' );
?>

</main>

<!-- Footer -->
<!-- ------------------------------------------------- -->
<footer class="footer" x-data="{ shown: false }" x-intersect:enter.threshold.0.2="shown = true">
    <div class="wrap">
        <div class="footer-grid" data-animate="fade-in" :class="shown && 'is-visible'">

            <div class="footer-section footer-section-1">
                <?php if ( $footer_1_title ) : ?>
                    <h2 class="heading-4"><?php echo esc_html( $footer_1_title ); ?></h2>
                <?php endif; ?>
                <?php if ( $footer_1_text ) : ?>
                    <div class="footer-text"><?php echo wp_kses_post( $footer_1_text ); ?></div>
                <?php endif; ?>
            </div>

            <div class="footer-section footer-section-2">
                <?php if ( $footer_2_title ) : ?>
                    <h2 class="heading-4"><?php echo esc_html( $footer_2_title ); ?></h2>
                <?php endif; ?>
                <?php if ( have_rows( 'footer_2_menu', 'option' ) ) : ?>
                    <ul class="footer-menu">
                        <?php
                        while ( have_rows( 'footer_2_menu', 'option' ) ) :
							the_row();
                            $menu_link = get_sub_field( 'link' );
                            if ( empty( $menu_link ) ) {
                                continue;
                            }
                            ?>
                            <li>
                                <a href="<?php echo esc_url( $menu_link['url'] ); ?>"<?php echo ! empty( $menu_link['target'] ) ? ' target="' . esc_attr( $menu_link['target'] ) . '"' : ''; ?>><?php echo esc_html( $menu_link['title'] ); ?></a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="footer-section footer-section-3">
                <?php if ( $footer_3_title ) : ?>
                    <h2 class="heading-4"><?php echo esc_html( $footer_3_title ); ?></h2>
                <?php endif; ?>
                <?php if ( $footer_3_text ) : ?>
                    <div class="footer-text"><?php echo wp_kses_post( $footer_3_text ); ?></div>
                <?php endif; ?>
            </div>

            <div class="footer-section footer-section-4">
                <?php if ( $footer_4_title ) : ?>
                    <h2 class="heading-4"><?php echo esc_html( $footer_4_title ); ?></h2>
                <?php endif; ?>
                <?php if ( $footer_4_button_1 ) : ?>
                    <a class="button button-orange" href="<?php echo esc_url( $footer_4_button_1['url'] ); ?>"<?php echo ! empty( $footer_4_button_1['target'] ) ? ' target="' . esc_attr( $footer_4_button_1['target'] ) . '"' : ''; ?>><?php echo esc_html( $footer_4_button_1['title'] ); ?></a>
                <?php endif; ?>
                <?php if ( $footer_4_button_2 ) : ?>
                    <a class="button button-purple" href="<?php echo esc_url( $footer_4_button_2['url'] ); ?>"<?php echo ! empty( $footer_4_button_2['target'] ) ? ' target="' . esc_attr( $footer_4_button_2['target'] ) . '"' : ''; ?>><?php echo esc_html( $footer_4_button_2['title'] ); ?></a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</footer>

<div class="footer-copyright">
    <div class="wrap">
        <?php if ( $copyright_link ) : ?>
            <a href="<?php echo esc_url( $copyright_link['url'] ); ?>"<?php echo ! empty( $copyright_link['target'] ) ? ' target="' . esc_attr( $copyright_link['target'] ) . '"' : ''; ?>>&copy; <?php echo esc_html( $copyright_link['title'] ); ?></a>
        <?php else : ?>
            <a href="https://www.modcommslimited.com/privacypolicy.php">&copy; Copyright Jiggy Wrigglers Ltd</a>
        <?php endif; ?>
    </div>
</div>

<?php wp_footer(); ?>

</body>
</html>
