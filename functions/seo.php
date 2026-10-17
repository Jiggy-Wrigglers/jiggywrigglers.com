<?php
/** SEO defaults for the theme's ACF pages and SureCart catalogue. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** A short summary of visible content, without executing feeds or form blocks. */
function jw_seo_summary( $text ) {
	$text = preg_replace( '#<(script|style)\b[^>]*>.*?</\1>#is', '', (string) $text );
	$text = preg_replace( '#</(?:p|div|li|h[1-6])\s*>|<br\s*/?>#i', ' ', $text );
	$text = strip_shortcodes( $text );
	$text = html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES, 'UTF-8' );
	$text = trim( preg_replace( '/\s+/u', ' ', $text ) );
	if ( function_exists( 'mb_strlen' ) && mb_strlen( $text ) > 160 ) {
		$text = mb_substr( $text, 0, 157 );
		$text = preg_replace( '/\s+\S*$/u', '', $text ) . '…';
	} elseif ( ! function_exists( 'mb_strlen' ) && strlen( $text ) > 160 ) {
		$text = wp_trim_words( $text, 24, '…' );
	}
	return $text;
}

function jw_seo_default_description( $post ) {
	if ( ! $post instanceof WP_Post ) { return ''; }
	$title = wp_strip_all_tags( get_the_title( $post ) );
	$utility = array(
		'checkout' => 'Review your Jiggy Wrigglers shop order and complete checkout securely.',
		'customer-dashboard' => 'Manage your Jiggy Wrigglers shop account, orders, invoices and downloads.',
		'thank-you' => 'Your Jiggy Wrigglers enquiry confirmation and next steps.',
		'affiliate-dashboard' => 'Contact Jiggy Wrigglers Head Office for help with an affiliate account, referral links or existing commission.',
	);
	if ( 'page' === $post->post_type && isset( $utility[ $post->post_name ] ) ) { return $utility[ $post->post_name ]; }
	if ( 'page' === $post->post_type && get_post_meta( $post->ID, '_jw_sfs_area', true ) ) {
		$area = trim( preg_replace( '/^Jiggy Wrigglers\s*/i', '', $title ) );
		return jw_seo_summary( 'Find Jiggy Wrigglers classes, venues and birthday parties in ' . $area . '. View local booking links and contact your local team.' );
	}
	if ( $post->post_excerpt ) { return jw_seo_summary( $post->post_excerpt ); }
	if ( 'sc_product' === $post->post_type ) {
		return jw_seo_summary( 'Shop ' . $title . ' from Jiggy Wrigglers. View product details, available options and current pricing.' );
	}
	if ( function_exists( 'get_field' ) ) {
		foreach ( array( 'hero_text', 'content_text', 'introduction_text' ) as $field ) {
			$value = get_field( $field, $post->ID );
			if ( is_string( $value ) && jw_seo_summary( $value ) ) { return jw_seo_summary( $value ); }
		}
	}
	// An imported article already has its title in the hero; start with its prose.
	$body = preg_replace( '#<h[1-6]\b[^>]*>.*?</h[1-6]>#is', ' ', $post->post_content );
	$summary = jw_seo_summary( $body );
	return $summary ?: jw_seo_summary( $title . '. Information from Jiggy Wrigglers.' );
}

/** Preserve editorial SEO settings; fill missing descriptions and local defaults. */
function jw_seo_apply_meta( $meta, $post ) {
	if ( ! is_array( $meta ) || ! $post instanceof WP_Post ) { return $meta; }
	$saved = get_post_meta( $post->ID, 'surerank_settings_general', true );
	$saved = is_array( $saved ) ? $saved : array();
	$is_location = 'page' === $post->post_type && (bool) get_post_meta( $post->ID, '_jw_sfs_area', true );
	$editorial_description = ! empty( $saved['page_description'] ) && ! preg_match( '/%[a-z0-9_.]+%/i', $saved['page_description'] );
	$is_product_default = 'sc_product' === $post->post_type && ! $editorial_description;
	if ( empty( $meta['page_description'] ) || ( $is_location && ! $editorial_description ) || $is_product_default ) {
		if ( $is_product_default && ! empty( $meta['page_description'] ) ) {
			$description = jw_seo_summary( $meta['page_description'] );
			$title = wp_strip_all_tags( get_the_title( $post ) );
			if ( 0 !== stripos( $description, $title ) ) { $description = $title . '. ' . $description; }
			if ( strlen( $description ) < 100 ) { $description .= ' View product details, available options and current pricing in the Jiggy Wrigglers shop.'; }
			$meta['page_description'] = jw_seo_summary( $description );
		} else {
			$meta['page_description'] = jw_seo_default_description( $post );
		}
		foreach ( array( 'facebook_description', 'twitter_description' ) as $key ) {
			if ( empty( $saved[ $key ] ) ) { $meta[ $key ] = $meta['page_description']; }
		}
	}
	return $meta;
}

add_filter( 'surerank_set_meta', function ( $meta ) {
	$meta = is_singular() ? jw_seo_apply_meta( $meta, get_queried_object() ) : $meta;
	if ( is_array( $meta ) && jw_seo_is_shop() ) {
		$meta['canonical_url'] = jw_seo_shop_canonical();
		$page = isset( $_GET['shop_page'] ) && is_scalar( $_GET['shop_page'] ) ? absint( $_GET['shop_page'] ) : 1;
		if ( $page > 1 && ! empty( $meta['page_title'] ) ) { $meta['page_title'] .= ' – Page ' . $page; }
	}
	return $meta;
}, 9 );

function jw_seo_is_shop() {
	return is_page() && 'pages/page-shop/shop.php' === ltrim( (string) get_page_template_slug( get_queried_object_id() ), '/' );
}

/** Transactional/search/placeholder URLs are useful to people, not search results. */
function jw_seo_should_noindex() {
	if ( 'jiggywrigglers.on-forge.com' === wp_parse_url( home_url(), PHP_URL_HOST ) ) { return true; }
	if ( is_search() || is_404() || is_attachment() || is_author() || is_date() ) { return true; }
	if ( is_page( array( 'checkout', 'cart', 'customer-dashboard', 'my-account', 'order-confirmation', 'thank-you', 'affiliate-dashboard' ) ) || is_single( 'test-jiggy-post' ) ) { return true; }
	if ( is_page() && class_exists( 'SureCart' ) ) {
		foreach ( array( 'checkout', 'dashboard', 'order-confirmation' ) as $key ) {
			if ( (int) get_queried_object_id() === (int) SureCart::pages()->getId( $key ) ) { return true; }
		}
	}
	if ( jw_seo_is_shop() ) {
		foreach ( array( 'shop_search', 'shop_collection', 'shop_sort' ) as $key ) {
			if ( isset( $_GET[ $key ] ) && is_string( $_GET[ $key ] ) && '' !== trim( wp_unslash( $_GET[ $key ] ) ) && ( 'shop_sort' !== $key || 'recent' !== $_GET[ $key ] ) ) { return true; }
		}
	}
	return false;
}

add_filter( 'surerank_robots_meta_array', function ( $robots ) {
	if ( ! jw_seo_should_noindex() ) { return $robots; }
	$robots = array_diff( (array) $robots, array( 'index' ) );
	$robots[] = 'noindex';
	return array_values( array_unique( $robots ) );
} );
add_filter( 'wp_robots', function ( $robots ) {
	if ( jw_seo_should_noindex() ) { unset( $robots['index'] ); $robots['noindex'] = true; }
	return $robots;
} );

/** Keep persistent utility pages out of sitemap chunks as well as robots meta. */
function jw_seo_utility_ids() {
	static $ids;
	if ( null !== $ids ) { return $ids; }
	$ids = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'post_name__in' => array( 'checkout', 'cart', 'customer-dashboard', 'my-account', 'order-confirmation', 'thank-you', 'affiliate-dashboard' ) ) );
	$ids = array_merge( $ids, get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'name' => 'test-jiggy-post', 'fields' => 'ids', 'posts_per_page' => 1 ) ) );
	if ( class_exists( 'SureCart' ) ) {
		foreach ( array( 'checkout', 'dashboard', 'order-confirmation' ) as $key ) { $ids[] = (int) SureCart::pages()->getId( $key ); }
	}
	return $ids = array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );
}
add_filter( 'surerank_exclude_posts_from_sitemap', function ( $ids ) {
	return array_values( array_unique( array_merge( wp_parse_id_list( $ids ), jw_seo_utility_ids() ) ) );
} );
add_filter( 'wp_sitemaps_posts_query_args', function ( $args ) {
	$args['post__not_in'] = array_values( array_unique( array_merge( $args['post__not_in'] ?? array(), jw_seo_utility_ids() ) ) );
	return $args;
} );

/** Custom catalogue page 2+ needs its own canonical, not page one's URL. */
function jw_seo_shop_canonical() {
	$url = get_permalink( get_queried_object_id() );
	$args = array();
	foreach ( array( 'shop_search', 'shop_collection', 'shop_sort' ) as $key ) {
		if ( isset( $_GET[ $key ] ) && is_string( $_GET[ $key ] ) && '' !== trim( wp_unslash( $_GET[ $key ] ) ) && ( 'shop_sort' !== $key || 'recent' !== $_GET[ $key ] ) ) {
			$args[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
		}
	}
	$page = isset( $_GET['shop_page'] ) && is_scalar( $_GET['shop_page'] ) ? absint( $_GET['shop_page'] ) : 1;
	if ( $page > 1 ) { $args['shop_page'] = $page; }
	return $args ? add_query_arg( $args, $url ) : $url;
}
add_filter( 'surerank_canonical_url', function ( $url ) {
	return jw_seo_is_shop() ? jw_seo_shop_canonical() : $url;
} );

// SureRank owns social metadata; SureCart retains its native Product schema.
add_filter( 'sc_display_product_seo_meta', function ( $enabled ) {
	return is_singular( 'sc_product' ) && class_exists( 'SureRank\\Inc\\Frontend\\Meta_Data' ) ? false : $enabled;
} );

function jw_seo_has_native_product_schema() {
	return method_exists( 'SureCart\\WordPress\\PostTypes\\ProductSeoService', 'addProductJsonSchema' )
		|| method_exists( 'SureCart\\WordPress\\PostTypes\\ProductPostTypeService', 'addProductJsonSchema' );
}

// SureRank's compatibility layer suppresses native JSON-LD even when its own
// selected graph has no Product. Use SureCart's variant/price/stock-aware graph
// on product detail pages; leave other pages' SureRank graphs intact.
add_action( 'wp', function () {
	if ( ! is_singular( 'sc_product' ) ) { return; }
	if ( class_exists( 'SureRank\\Inc\\Frontend\\Meta_Data' ) && method_exists( 'SureCart\\WordPress\\PostTypes\\ProductPostTypeService', 'disallowPreTitle' ) ) {
		remove_filter( 'pre_get_document_title', array( SureCart::productPost(), 'disallowPreTitle' ), 214748364 );
	}
	if ( ! jw_seo_has_native_product_schema() ) { return; }
	$schemas = 'SureRank\\Inc\\Schema\\Schemas';
	if ( is_callable( array( $schemas, 'get_instance' ) ) ) {
		remove_action( 'surerank_print_meta', array( $schemas::get_instance(), 'print_schema_data' ), 10 );
	}
}, 20 );
add_filter( 'sc_display_product_json_ld_schema', function ( $enabled ) {
	return is_singular( 'sc_product' ) && jw_seo_has_native_product_schema() ? true : $enabled;
}, 99 );
