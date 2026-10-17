<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<section class="site-content-page"><div class="wrap prose">
    <h1 class="heading-1"><?php printf( esc_html__( 'Search results for: %s', 'jiggywrigglers' ), esc_html( get_search_query() ) ); ?></h1>
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article>
                <h2 class="heading-3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <p><?php echo esc_html( jw_seo_default_description( get_post() ) ); ?></p>
            </article>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e( 'No results found. Try another search or explore our locations.', 'jiggywrigglers' ); ?></p>
        <a class="button button-purple" href="<?php echo esc_url( home_url( '/locations/' ) ); ?>"><?php esc_html_e( 'Find your local class', 'jiggywrigglers' ); ?></a>
    <?php endif; ?>
    <?php get_search_form(); ?>
</div></section>
<?php get_footer();
