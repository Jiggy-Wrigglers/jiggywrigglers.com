<?php
/**
 * Jiggy Wrigglers functions and definitions
 *
 * @package Jiggy_Wrigglers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '2.2.2' );
}

if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';

if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

include get_template_directory() . '/functions/duplication.php';
include get_template_directory() . '/functions/remove-menus.php';
include get_template_directory() . '/functions/custom-functions.php';
include get_template_directory() . '/functions/surerank.php';
include get_template_directory() . '/functions/surecart.php';

function jiggy_wrigglers_setup() {
	load_theme_textdomain( 'jiggywrigglers', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'jiggywrigglers' ),
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Block editor only for SureCart products (product content is block-based). Everything else is ACF-driven.
	add_filter( 'use_block_editor_for_post', function( $use, $post ) {
		if ( $post && 'sc_product' === $post->post_type ) {
			return true;
		}
		return false;
	}, 10, 2 );

	add_filter( 'theme_page_templates', function( $templates ) {
		unset( $templates['functions.php'] );
		return $templates;
	} );
}
add_action( 'after_setup_theme', 'jiggy_wrigglers_setup' );

function jiggy_wrigglers_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'jiggy_wrigglers_content_width', 640 );
}
add_action( 'after_setup_theme', 'jiggy_wrigglers_content_width', 0 );

function jiggy_wrigglers_scripts() {
	wp_enqueue_style( 'jiggywrigglers-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'jiggywrigglers-style', 'rtl', 'replace' );

	wp_enqueue_style( 'theme-header', get_template_directory_uri() . '/css/header.css', array(), _S_VERSION );
	wp_enqueue_style( 'theme-footer', get_template_directory_uri() . '/css/footer.css', array(), _S_VERSION );

	wp_enqueue_style( 'splide-css', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css', array(), null );
	wp_enqueue_script( 'splide', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js', array(), null, false );

	wp_enqueue_style( 'lenis-css', 'https://unpkg.com/lenis@1.3.23/dist/lenis.css', array(), null );
	wp_enqueue_script( 'lenis', 'https://unpkg.com/lenis@1.3.23/dist/lenis.min.js', array(), null, false );
	wp_enqueue_script( 'theme-js', get_template_directory_uri() . '/js/index.js', array( 'lenis' ), _S_VERSION, false );

	wp_enqueue_script( 'alpinejs-intersect', 'https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.14.1/dist/cdn.min.js', array(), null, false );
	wp_enqueue_script( 'alpinejs-collapse', 'https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js', array(), null, false );
	wp_enqueue_script( 'alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js', array( 'alpinejs-intersect', 'alpinejs-collapse', 'theme-js' ), null, false );
}
add_action( 'wp_enqueue_scripts', 'jiggy_wrigglers_scripts' );

function jiggy_wrigglers_defer_scripts( $tag, $handle ) {
	$defer = array( 'alpinejs', 'alpinejs-intersect', 'alpinejs-collapse', 'splide', 'lenis', 'theme-js' );
	if ( in_array( $handle, $defer, true ) ) {
		return str_replace( ' src=', ' defer src=', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'jiggy_wrigglers_defer_scripts', 10, 2 );

function jiggy_wrigglers_custom_admin_css() {
	echo '<style>#postdivrich, #wp-content-wrap, div#authordiv, div#slugdiv { display: none !important; }</style>';
}
add_action( 'admin_head', 'jiggy_wrigglers_custom_admin_css' );

function jiggy_wrigglers_disable_block_library_css() {
	if ( ! is_user_logged_in() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ), true ) ) {
		wp_dequeue_style( 'wp-block-library' );
	}
}
add_action( 'wp_enqueue_scripts', 'jiggy_wrigglers_disable_block_library_css', 100 );

add_filter( 'login_headerurl', function() {
	return 'https://jiggywrigglers.co.uk/';
} );

add_filter( 'theme_page_templates', 'jiggy_wrigglers_register_page_templates' );

function jiggy_wrigglers_register_page_templates( $templates ) {
	$new_templates = array();
	$dir     = get_template_directory() . '/pages';
	$flags   = FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;
	$files   = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, $flags ) );

	foreach ( $files as $file ) {
		if ( $file->isDir() || $file->getExtension() !== 'php' ) {
			continue;
		}

		$path     = $file->getPathname();
		$template = str_replace( get_template_directory(), '', $path );

		$handle  = fopen( $path, 'r' );
		$content = fread( $handle, 8192 );
		fclose( $handle );

		if ( preg_match( '/^\s*\*\s*Template Name:\s*(.+)$/mi', $content, $header ) ) {
			$new_templates[ $template ] = _cleanup_header_comment( $header[1] );
		}
	}

	asort( $new_templates );

	return array_merge( $templates, $new_templates );
}

function jiggy_wrigglers_add_security_headers() {
	if ( headers_sent() ) {
		return;
	}

	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
}
add_action( 'send_headers', 'jiggy_wrigglers_add_security_headers' );
add_action( 'admin_init', 'jiggy_wrigglers_add_security_headers' );
add_action( 'login_init', 'jiggy_wrigglers_add_security_headers' );
add_action( 'rest_api_init', 'jiggy_wrigglers_add_security_headers' );

remove_action( 'wp_head', 'wp_generator' );

add_filter( 'xmlrpc_methods', function( $methods ) {
	unset( $methods['pingback.ping'] );
	unset( $methods['pingback.extensions.getPingbacks'] );
	return $methods;
} );

function jiggy_wrigglers_disable_comments() {
	$post_types = get_post_types();
	foreach ( $post_types as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
}
add_action( 'admin_init', 'jiggy_wrigglers_disable_comments' );

add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'get_comments_number', '__return_zero' );

/**
 * Sort an array of posts by an ACF numeric order field.
 *
 * Posts without a value (or <= 0 when $zero_is_unordered) sink to the end
 * while keeping their original relative order.
 *
 * @param WP_Post[] $posts             Posts to sort.
 * @param string    $field             ACF field name. Default 'order'.
 * @param bool      $zero_is_unordered Treat 0 as unordered. Default false.
 * @return WP_Post[]
 */
function jiggy_wrigglers_sort_by_order( $posts, $field = 'order', $zero_is_unordered = false ) {
	$indexed = array();
	foreach ( $posts as $i => $post ) {
		$order = get_field( $field, $post->ID );
		if ( $order === '' || $order === null || $order === false || ( $zero_is_unordered && (float) $order <= 0 ) ) {
			$order = PHP_FLOAT_MAX;
		} else {
			$order = (float) $order;
		}
		$indexed[] = array( 'post' => $post, 'order' => $order, 'i' => $i );
	}
	usort( $indexed, function ( $a, $b ) {
		if ( $a['order'] === $b['order'] ) {
			return $a['i'] <=> $b['i'];
		}
		return $a['order'] <=> $b['order'];
	} );
	return array_column( $indexed, 'post' );
}

/**
 * Maintenance mode. Activated via the ACF option: maintenance_mode (True/False).
 * Only shown to non-logged-in visitors. Logged-in users see the live site.
 */
add_action( 'template_redirect', function () {
	if ( get_field( 'maintenance_mode', 'option' ) && ! is_user_logged_in() ) {
		http_response_code( 503 );
		include get_template_directory() . '/maintenance.php';
		die();
	}
} );
