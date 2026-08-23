<?php
/**
 * Template Name: Article
 *
 * Reusable article listing template. Assign to any page that needs a
 * post / article grid with pagination.
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/template-article/article.css">
<script defer src="<?php echo get_template_directory_uri(); ?>/pages/template-article/article.js"></script>

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php
// Content
$content_sup_title = get_field( 'content_sup_title' );
$content_title = get_field( 'content_title' );

// Article grid
$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
$articles = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 9,
    'paged'          => $paged,
) );
?>

<!-- Article Listing Section -->
<!-- ------------------------------------------------- -->
<section class="article-listing">
    <div class="wrap">
        <?php if ( $content_sup_title || $content_title ) : ?>
            <div class="article-listing-header">
                <?php if ( $content_sup_title ) : ?>
                    <h3 class="heading-5"><?php echo wp_kses_post( $content_sup_title ); ?></h3>
                <?php endif; ?>
                <?php if ( $content_title ) : ?>
                    <h2 class="heading-2"><?php echo wp_kses_post( $content_title ); ?></h2>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ( $articles->have_posts() ) : ?>
            <div class="article-grid">
                <?php $i = 0; while ( $articles->have_posts() ) : $articles->the_post(); $i++; ?>
                    <article class="article-card" data-animate="fade-up" data-animate-delay="<?php echo esc_attr( ( ( $i - 1 ) % 3 ) + 1 ); ?>">
                        <a href="<?php the_permalink(); ?>" class="article-card-link">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="article-card-image">
                                    <?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'full' ); ?>
                                </div>
                            <?php endif; ?>
                            <div class="article-card-content">
                                <p class="article-card-date"><?php echo esc_html( get_the_date( 'd M Y' ) ); ?></p>
                                <h3 class="heading-5"><?php the_title(); ?></h3>
                                <p class="body-medium"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                                <span class="article-card-read">Read More</span>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                    echo paginate_links( array(
                        'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                        'format'    => '?paged=%#%',
                        'current'   => $paged,
                        'total'     => $articles->max_num_pages,
                        'prev_text' => 'Previous',
                        'next_text' => 'Next',
                    ) );
                    wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            <div class="article-empty">
                <h3 class="heading-5">No articles found</h3>
                <p class="body-medium">Please check back soon.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
