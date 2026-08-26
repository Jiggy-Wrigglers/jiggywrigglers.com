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
$contact_form_sup_title = get_field( 'contact_form_sup_title' );
$contact_form_title = get_field( 'contact_form_title' );
$contact_form_shortcode = get_field( 'contact_form_shortcode' );
?>

<!-- Contact Form Section -->
<!-- ------------------------------------------------- -->
<section class="contact-form" x-data="{ shown: false }" x-intersect:enter.threshold.0.2="shown = true">
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

<?php get_footer(); ?>
