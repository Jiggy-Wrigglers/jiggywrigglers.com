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
<?php get_footer(); ?>