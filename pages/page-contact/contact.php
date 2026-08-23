<?php
/**
 * Template Name: Contact
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-contact/contact.css">

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php
// Contact form
$contact_form_sup_title  = get_field( 'contact_form_sup_title' );
$contact_form_title      = get_field( 'contact_form_title' );
$contact_form_shortcode  = get_field( 'contact_form_shortcode' );

// Contact content
$contact_content_title = get_field( 'contact_content_title' );
$contact_content_image = get_field( 'contact_content_image' );
?>

<!-- Contact Form Section -->
<!-- ------------------------------------------------- -->
<section class="contact-form">
    <div class="wrap">
        <?php if ( $contact_form_sup_title ) : ?>
            <h3 class="heading-5"><?php echo wp_kses_post( $contact_form_sup_title ); ?></h3>
        <?php endif; ?>
        <?php if ( $contact_form_title ) : ?>
            <h2 class="heading-2"><?php echo wp_kses_post( $contact_form_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $contact_form_shortcode ) : ?>
            <div class="contact-form-shortcode">
                <?php echo do_shortcode( $contact_form_shortcode ); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Contact Content Section -->
<!-- ------------------------------------------------- -->
<?php if ( $contact_content_title || $contact_content_image ) : ?>
<section class="contact-content">
    <div class="wrap">
        <div class="contact-content-text">
            <?php if ( $contact_content_title ) : ?>
                <h2 class="heading-3"><?php echo wp_kses_post( $contact_content_title ); ?></h2>
            <?php endif; ?>
            <a class="button button-blue" href="mailto:enquiries@jiggywrigglers.com">enquiries@jiggywrigglers.com</a>
        </div>
        <?php if ( $contact_content_image ) : ?>
            <div class="contact-content-image">
                <?php echo wp_get_attachment_image( $contact_content_image['ID'], 'full' ); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
