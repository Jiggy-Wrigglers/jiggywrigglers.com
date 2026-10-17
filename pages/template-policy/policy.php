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

<?php
$policy_content = get_field( 'content_text' );
if ( $policy_content && is_page( 'booking-terms-and-conditions' ) ) {
    // Tidy only this known imported opening; keep the stored legal content intact.
    $policy_content = preg_replace(
        '~\A(\s*<h2\b[^>]*>\s*Booking terms and conditions\s*</h2>)\s*<p>\s*<strong>\s*Booking terms and conditions\s*</strong>\s*</p>~u',
        '$1',
        $policy_content,
        1
    );
    $policy_content = preg_replace_callback(
        '~\A(\s*<h2\b[^>]*>\s*Booking terms and conditions\s*</h2>\s*)<p>\s*(<strong>)\s*•(.*?)</strong>\s*<br\s*/?>\s*(<strong>)\s*•(.*?)\s*•(.*?)</strong>\s*</p>~su',
        static function ( $matches ) {
            $clauses = array( $matches[3], $matches[5], $matches[6] );
            $expected = array(
                'Charges are per child with sibling discounts available',
                'Children outside of your immediate family should not be added to your account, unless you are a working childminder',
                'We require all customers to book online in advance.',
            );
            foreach ( $clauses as $index => $clause ) {
                $plain_text = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $clause ), ENT_QUOTES, 'UTF-8' ) ) );
                if ( $plain_text !== $expected[ $index ] ) {
                    return $matches[0];
                }
            }

            // Retain the original inline markup while replacing typed bullets with list items.
            return $matches[1] . "<ul>\n<li>" . $matches[2] . trim( $clauses[0] ) . "</strong></li>\n<li>"
                . $matches[4] . trim( $clauses[1] ) . "</strong></li>\n<li>"
                . $matches[4] . trim( $clauses[2] ) . "</strong></li>\n</ul>";
        },
        $policy_content,
        1
    );
}
?>

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
