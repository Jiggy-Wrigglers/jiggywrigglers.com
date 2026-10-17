<?php
/**
 * Template Name: Article
 *
 * Displays the current article with its ACF hero and content headings.
 * Also loaded by single.php for individual blog posts.
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/pages/template-article/article.css?ver=<?php echo esc_attr( _S_VERSION ); ?>">

<?php while ( have_posts() ) : the_post(); ?>

<?php
// Read the fields from the opened article, without querying other posts.
$hero_post_id = get_the_ID();
$hero_title = get_field( 'hero_title', $hero_post_id ) ?: get_the_title();
$content_sup_title = get_field( 'content_sup_title', $hero_post_id );
$content_title = get_field( 'content_title', $hero_post_id );
?>

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<!-- Article Content Section -->
<!-- ------------------------------------------------- -->
<article class="article-content">
    <div class="wrap">
        <?php if ( $content_sup_title || $content_title ) : ?>
            <div class="article-content-header">
                <?php if ( $content_sup_title ) : ?>
                    <h3 class="heading-5"><?php echo wp_kses_post( $content_sup_title ); ?></h3>
                <?php endif; ?>
                <?php if ( $content_title ) : ?>
                    <h2 class="heading-2"><?php echo wp_kses_post( $content_title ); ?></h2>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="article-content-text">
            <?php
                // The Article ACF group has no body field; use the saved post content.
                the_content();
                wp_link_pages( array(
                    'before' => '<nav class="article-content-pages" aria-label="' . esc_attr__( 'Article pages', 'jiggywrigglers' ) . '">',
                    'after'  => '</nav>',
                ) );
            ?>
        </div>
    </div>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
