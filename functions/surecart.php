<?php
/**
 * SureCart Template Overrides
 *
 * Intercepts template loading to provide the theme's own product
 * wrapper while still rendering SureCart's block template parts for
 * product data (prices, buy button, etc).
 *
 * Also ensures block-template-parts support is active so the
 * Appearance > Template Parts admin menu stays visible.
 *
 * @package Jiggy_Wrigglers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', function() {
	add_theme_support( 'block-template-parts' );
}, 999 );

add_filter( 'template_include', function( $template ) {
	if ( ! is_singular( 'sc_product' ) || wp_is_block_theme() ) {
		return $template;
	}

	$custom = get_template_directory() . '/pages/template-product/product.php';
	if ( file_exists( $custom ) ) {
		return $custom;
	}

	return $template;
}, 20 );
