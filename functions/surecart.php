<?php
/**
 * SureCart integration for the classic theme.
 *
 * Payment processing and customer accounts stay in SureCart. This module
 * supplies the surrounding layout without creating or changing a store.
 *
 * @package Jiggy_Wrigglers
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a post needs the SureCart editor and block styles.
 *
 * @param WP_Post|int|null $post Post to inspect, or the current post.
 * @return bool
 */
function jiggy_wrigglers_is_surecart_content( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}
	if ( 0 === strpos( $post->post_type, 'sc_' ) ) {
		return true;
	}
	foreach ( array( 'shop', 'checkout', 'dashboard', 'order-confirmation' ) as $key ) {
		if ( (int) $post->ID === (int) get_option( 'surecart_' . $key . '_page_id' ) ) {
			return true;
		}
	}
	$template = (string) get_page_template_slug( $post );
	return false !== strpos( $template, 'surecart' )
		|| false !== strpos( $post->post_content, '<!-- wp:surecart/' )
		|| (bool) preg_match( '/\[\/?sc_[a-z_]+(?:\s|\]|\/)/i', $post->post_content );
}

/**
 * Get a configured published store page without guessing its slug.
 *
 * @param string $key SureCart page key, e.g. shop or dashboard.
 * @return string
 */
function jiggy_wrigglers_surecart_page_url( $key ) {
	if ( ! class_exists( 'SureCart' ) ) {
		return '';
	}
	$page = SureCart::pages()->get( $key );
	return $page && 'publish' === get_post_status( $page ) ? get_permalink( $page ) : '';
}

add_action( 'after_setup_theme', function() {
	add_theme_support( 'block-template-parts' );
}, 999 );

add_action( 'wp_enqueue_scripts', function() {
	if ( is_singular( 'sc_product' ) ) {
		wp_enqueue_style( 'jiggywrigglers-product', get_theme_file_uri( '/pages/template-product/product.css' ), array( 'jiggywrigglers-style' ), _S_VERSION );
	}
	if ( 'pages/page-shop/shop.php' === ltrim( (string) get_page_template_slug(), '/' ) ) {
		wp_enqueue_style( 'jiggywrigglers-shop', get_theme_file_uri( '/pages/page-shop/shop.css' ), array( 'jiggywrigglers-style' ), _S_VERSION );
	}
	$checkout_id = class_exists( 'SureCart' ) ? SureCart::pages()->getId( 'checkout' ) : 0;
	if ( $checkout_id && is_page( $checkout_id ) ) {
		wp_enqueue_style( 'jiggywrigglers-checkout', get_theme_file_uri( '/css/surecart-checkout.css' ), array( 'theme-refresh' ), _S_VERSION );
	}
}, 20 );

add_filter( 'template_include', function( $template ) {
	if ( ! is_singular( 'sc_product' ) || wp_is_block_theme() ) {
		return $template;
	}
	// Respect deliberately selected page-builder and blank SureCart layouts.
	if ( ! in_array( basename( $template ), array( 'single-sc_product.php', 'template-surecart-product.php' ), true ) ) {
		return $template;
	}
	$custom = get_theme_file_path( '/pages/template-product/product.php' );
	return file_exists( $custom ) ? $custom : $template;
}, 20 );

// SureCart selects its blank page layout at priority 9. Wrap only the
// configured checkout in the site shell, retaining every saved content block.
// The native dashboard keeps its controller, authentication and navigation.
add_filter( 'template_include', function( $template ) {
	if ( ! class_exists( 'SureCart' ) || 'template-surecart-blank.php' !== basename( $template ) ) {
		return $template;
	}
	$checkout_id = SureCart::pages()->getId( 'checkout' );
	if ( ! $checkout_id || ! is_page( $checkout_id ) ) {
		return $template;
	}
	$page_template = get_theme_file_path( '/page.php' );
	return file_exists( $page_template ) ? $page_template : $template;
}, 30 );
