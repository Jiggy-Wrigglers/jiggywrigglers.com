<?php
/**
 * Template Name: Policy
 *
 * Reusable policy template. Assign to any page that displays static
 * policy content, such as privacy, terms, or cookie policy pages.
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/template-policy/policy.css">

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php $policy_content = get_field( 'content_text' ); ?>

<!-- Policy Content Section -->
<!-- ------------------------------------------------- -->
<section class="policy-content">
    <div class="wrap">
        <?php if ( $policy_content ) : ?>
            <div class="policy-text"><?php echo wp_kses_post( $policy_content ); ?></div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
