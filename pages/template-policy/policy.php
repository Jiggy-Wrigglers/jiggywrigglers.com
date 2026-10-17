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
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/pages/template-policy/policy.css?ver=<?php echo esc_attr( _S_VERSION ); ?>">

<?php while ( have_posts() ) : the_post(); ?>

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php
$hero_title = get_field( 'hero_title' ) ?: get_the_title();
$hero_compact = true;
include locate_template( 'components/hero/index.php' );
?>

<?php $policy_content = get_field( 'content_text' ); ?>

<!-- Policy Content Section -->
<!-- ------------------------------------------------- -->
<section class="policy-content">
    <div class="wrap">
        <div class="policy-text">
            <?php
            if ( $policy_content ) {
                echo wp_kses_post( $policy_content );
            } else {
                // Preserve native policy content when this page has not been migrated to ACF.
                the_content();
                wp_link_pages();
            }
            ?>
        </div>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
