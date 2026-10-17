<?php
/** Standard pages, including native SureCart checkout and customer content. */
get_header();
while ( have_posts() ) :
	the_post();
	$is_commerce = jiggy_wrigglers_is_surecart_content( get_post() );
	$is_legacy = (bool) get_post_meta( get_the_ID(), '_jw_legacy_source', true );
	?>
	<article class="site-content-page<?php echo $is_commerce ? ' site-commerce-page' : ''; ?>">
		<?php if ( ! $is_legacy ) : ?>
			<header class="site-page-heading wrap">
				<p class="eyebrow">Jiggy Wrigglers</p>
				<h1 class="heading-1"><?php the_title(); ?></h1>
			</header>
		<?php endif; ?>
		<div class="<?php echo $is_legacy ? 'legacy-page' : 'wrap prose'; ?>">
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
		</div>
	</article>
	<?php
endwhile;
get_footer();
