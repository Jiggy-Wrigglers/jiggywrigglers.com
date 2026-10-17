<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Jiggy_Wrigglers
 */

get_header();
?>
<section class="site-content-page"><div class="wrap prose">
<?php while ( have_posts() ) : the_post(); ?>
	<article>
		<h1 class="heading-1"><?php the_title(); ?></h1>
		<?php the_content(); ?>
	</article>
<?php endwhile; ?>
<?php the_posts_pagination(); ?>
</div></section>
<?php
get_footer();
