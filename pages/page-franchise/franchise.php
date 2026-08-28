<?php
/**
 * Template Name: Franchise Opportunity
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-franchise/franchise.css">

<!-- Banner Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php
// Text 3 Images
$text_3_heading = get_field( 'text_3_heading' );
$text_3_title = get_field( 'text_3_title' );
$text_3_text = get_field( 'text_3_text' );
$text_3_image_1 = get_field( 'text_3_image_1' );
$text_3_image_2 = get_field( 'text_3_image_2' );
$text_3_image_3 = get_field( 'text_3_image_3' );

// Video Block 2
$video_1_title = get_field( 'video_1_title' );
$video_1_embed = get_field( 'video_1_embed' );
$video_2_title = get_field( 'video_2_title' );
$video_2_embed = get_field( 'video_2_embed' );

// List Block
$list_intro_heading = get_field( 'list_intro_heading' );
$list_title = get_field( 'list_title' );
$list_items = get_field( 'list_of_items' );
$list_button = get_field( 'list_button' );
$list_image = get_field( 'list_image' );
?>

<!-- Text 3 Images Section -->
<!-- ------------------------------------------------- -->
<?php if ( $text_3_text || $text_3_image_1 || $text_3_image_2 || $text_3_image_3 ) : ?>
<section class="text-with-3-images-block" x-data="{ shown: false }" x-intersect:enter.threshold.0.2="shown = true">
    <div class="wrap">
        <?php if ( $text_3_heading ) : ?>
            <h3 class="heading-5" data-animate="fade-up" :class="shown && 'is-visible'"><?php echo wp_kses_post( $text_3_heading ); ?></h3>
        <?php endif; ?>
        <?php if ( $text_3_title ) : ?>
            <h2 class="heading-2" data-animate="fade-up" data-animate-delay="1" :class="shown && 'is-visible'"><?php echo wp_kses_post( $text_3_title ); ?></h2>
        <?php endif; ?>
        <div class="text-with-images">
            <?php if ( $text_3_text ) : ?>
                <div class="text-section" data-animate="fade-up" data-animate-delay="2" :class="shown && 'is-visible'">
                    <?php echo wp_kses_post( $text_3_text ); ?>
                </div>
            <?php endif; ?>
            <?php if ( $text_3_image_1 || $text_3_image_2 || $text_3_image_3 ) : ?>
                <div class="image-section" data-animate="fade-up" data-animate-delay="2" :class="shown && 'is-visible'">
                    <?php if ( $text_3_image_1 ) : ?>
                        <?php echo wp_get_attachment_image( $text_3_image_1['ID'], 'full' ); ?>
                    <?php endif; ?>
                    <?php if ( $text_3_image_2 ) : ?>
                        <?php echo wp_get_attachment_image( $text_3_image_2['ID'], 'full' ); ?>
                    <?php endif; ?>
                    <?php if ( $text_3_image_3 ) : ?>
                        <?php echo wp_get_attachment_image( $text_3_image_3['ID'], 'full' ); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Video Block 2 Section -->
<!-- ------------------------------------------------- -->
<?php if ( $video_1_embed || $video_2_embed ) : ?>
<section class="video-block-2" x-data="{ shown: false }" x-intersect:enter.threshold.0.2="shown = true">
    <div class="wrap">
        <?php if ( $video_1_embed ) : ?>
            <div class="video-block-2-item">
                <?php if ( $video_1_title ) : ?>
                    <h2 class="heading-3" data-animate="fade-up" :class="shown && 'is-visible'"><?php echo wp_kses_post( $video_1_title ); ?></h2>
                <?php endif; ?>
                <div class="video-block-2-embed"><?php echo $video_1_embed; ?></div>
            </div>
        <?php endif; ?>
        <?php if ( $video_2_embed ) : ?>
            <div class="video-block-2-item">
                <?php if ( $video_2_title ) : ?>
                    <h2 class="heading-3" data-animate="fade-up" data-animate-delay="1" :class="shown && 'is-visible'"><?php echo wp_kses_post( $video_2_title ); ?></h2>
                <?php endif; ?>
                <div class="video-block-2-embed"><?php echo $video_2_embed; ?></div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- List Block Section -->
<!-- ------------------------------------------------- -->
<?php if ( $list_items || $list_image ) : ?>
<section class="list-block" x-data="{ shown: false }" x-intersect:enter.threshold.0.2="shown = true">
    <div class="wrap">
        <div class="list-area">
            <?php if ( $list_intro_heading ) : ?>
                <h3 class="heading-5" data-animate="fade-up" :class="shown && 'is-visible'"><?php echo wp_kses_post( $list_intro_heading ); ?></h3>
            <?php endif; ?>
            <?php if ( $list_title ) : ?>
                <h2 class="heading-2" data-animate="fade-up" data-animate-delay="1" :class="shown && 'is-visible'"><?php echo wp_kses_post( $list_title ); ?></h2>
            <?php endif; ?>
            <?php if ( have_rows( 'list_of_items' ) ) : ?>
                <div class="list-block-items" data-animate="fade-up" data-animate-delay="2" :class="shown && 'is-visible'">
                    <?php
                    while ( have_rows( 'list_of_items' ) ) :
                        the_row();
                        $list_item = get_sub_field( 'item' );
                        if ( empty( $list_item ) ) {
                            continue;
                        }
                        ?>
                        <div class="item"><?php echo $list_item; ?></div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
            <?php if ( $list_button ) : ?>
                <a class="button button-blue" data-animate="fade-up" data-animate-delay="3" :class="shown && 'is-visible'" href="<?php echo esc_url( $list_button['url'] ); ?>"<?php echo ! empty( $list_button['target'] ) ? ' target="' . esc_attr( $list_button['target'] ) . '"' : ''; ?>>
                    <?php echo esc_html( $list_button['title'] ); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if ( $list_image ) : ?>
            <div class="list-block-image" data-animate="fade-up" data-animate-delay="2" :class="shown && 'is-visible'">
                <?php echo wp_get_attachment_image( $list_image['ID'], 'full' ); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/testimonials/index.php' ); ?>

<?php get_footer(); ?>
