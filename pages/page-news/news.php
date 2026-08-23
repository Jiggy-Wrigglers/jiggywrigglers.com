<?php
/**
 * Template Name: News
 *
 * Single-use news listing page using the default WP query loop
 * with pagination.
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-news/news.css">

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<!-- News Listing Section -->
<!-- ------------------------------------------------- -->
<section class="news-listing">
    <div class="wrap">
        <?php if ( have_posts() ) : ?>
            <div class="news-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article class="news-card">
                        <a href="<?php the_permalink(); ?>" class="news-card-link">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="news-card-image">
                                    <?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'full' ); ?>
                                </div>
                            <?php endif; ?>
                            <div class="news-card-content">
                                <p class="news-card-date"><?php echo esc_html( get_the_date( 'd M Y' ) ); ?></p>
                                <h3 class="heading-5"><?php the_title(); ?></h3>
                                <p class="body-medium"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                                <span class="news-card-read">Read More</span>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                    the_posts_pagination( array(
                        'mid_size'  => 2,
                        'prev_text' => 'Previous',
                        'next_text' => 'Next',
                    ) );
                ?>
            </div>
        <?php else : ?>
            <div class="news-empty">
                <h3 class="heading-5">No news found</h3>
                <p class="body-medium">Please check back soon.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
