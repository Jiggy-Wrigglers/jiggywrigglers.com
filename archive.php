<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<section class="site-content-page"><div class="wrap prose">
    <h1 class="heading-1"><?php the_archive_title(); ?></h1>
    <?php the_archive_description( '<div>', '</div>' ); ?>
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article>
                <h2 class="heading-3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <p><?php echo esc_html( jw_seo_default_description( get_post() ) ); ?></p>
            </article>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e( 'There are no articles in this archive yet.', 'jiggywrigglers' ); ?></p>
    <?php endif; ?>
</div></section>
<?php get_footer();
