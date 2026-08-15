<?php
/**
 * The header for our theme
 *
 * @package Jiggy_Wrigglers
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<style>[x-cloak] { display: none !important; }</style>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>