<?php
/** Read-only metadata inventory, called only by the fixed Forge CLI task. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! defined( 'ABSPATH' ) ) { exit; }
if ( 'jiggywrigglers.on-forge.com' !== wp_parse_url( home_url(), PHP_URL_HOST ) ) { WP_CLI::error( 'New Forge site only.' ); }
WP_CLI::log( 'JW_SEO_START ' . substr( hash_file( 'sha256', __FILE__ ), 0, 12 ) );
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$versions = array();
foreach ( get_plugins() as $file => $plugin ) {
	if ( in_array( dirname( $file ), array( 'surecart', 'surerank' ), true ) ) { $versions[ dirname( $file ) ] = $plugin['Version']; }
}
$settings = 'SureRank\\Inc\\Functions\\Settings';
$variables = 'SureRank\\Inc\\Functions\\Variables';
$rows = array();
foreach ( get_posts( array( 'post_type' => array( 'page', 'post', 'sc_product' ), 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'ID', 'order' => 'ASC' ) ) as $item ) {
	$meta = is_callable( array( $settings, 'prep_post_meta' ) ) ? $settings::prep_post_meta( $item->ID, $item->post_type, false ) : array();
	if ( is_callable( array( $variables, 'replace' ) ) ) { $meta = $variables::replace( $meta, $item->ID ); }
	$meta = jw_seo_apply_meta( $meta, $item );
	$product = 'sc_product' === $item->post_type ? get_post_meta( $item->ID, 'product', true ) : array();
	$product = is_object( $product ) ? (array) $product : $product;
	$stored_general = get_post_meta( $item->ID, 'surerank_settings_general', true );
	$rows[] = array(
		'id' => $item->ID, 'type' => $item->post_type, 'slug' => $item->post_name,
		'url' => get_permalink( $item ), 'title' => wp_strip_all_tags( $meta['page_title'] ?? get_the_title( $item ) ),
		'description' => wp_strip_all_tags( $meta['page_description'] ?? '' ),
		'canonical' => ! empty( $meta['canonical_url'] ) ? $meta['canonical_url'] : get_permalink( $item ),
		'noindex_override' => $meta['post_no_index'] ?? '',
		'description_storage_type' => gettype( $stored_general ),
		'stored_description' => is_array( $stored_general ) ? ( $stored_general['page_description'] ?? '' ) : '',
		'sku' => is_array( $product ) && isset( $product['sku'] ) && is_scalar( $product['sku'] ) ? (string) $product['sku'] : '',
	);
}
WP_CLI::log( 'JW_SEO_ROWS ' . count( $rows ) );
if ( 'repair-duplicate-descriptions' === ( $operation ?? '' ) ) {
	$counts = array_count_values( array_column( array_filter( $rows, function ( $row ) { return 'sc_product' === $row['type']; } ), 'description' ) );
	$updated = 0;
	foreach ( $rows as $row ) {
		if ( 'sc_product' !== $row['type'] || empty( $row['description'] ) || ( $counts[ $row['description'] ] ?? 0 ) < 2 ) { continue; }
		$old = get_post_meta( $row['id'], 'surerank_settings_general', true );
		if ( ! is_array( $old ) || empty( $old['page_description'] ) || preg_match( '/%[a-z0-9_.]+%/i', $old['page_description'] ) ) { continue; }
		$new = $old;
		$title = html_entity_decode( get_the_title( $row['id'] ), ENT_QUOTES, 'UTF-8' );
		$new['page_description'] = jw_seo_summary( $title . '. ' . $row['description'] );
		foreach ( array( 'facebook_description', 'twitter_description' ) as $key ) {
			if ( ! empty( $new[ $key ] ) && $new[ $key ] === $old['page_description'] ) { $new[ $key ] = $new['page_description']; }
		}
		add_post_meta( $row['id'], '_jw_previous_seo_general', $old, true );
		if ( update_post_meta( $row['id'], 'surerank_settings_general', $new, $old ) ) { $updated++; }
	}
	WP_CLI::log( 'Product-specific descriptions updated: ' . $updated . '. Previous metadata retained per product.' );
	return;
}
$redirects = json_decode( file_get_contents( get_template_directory() . '/data/legacy-product-redirects.json' ), true );
$redirect_missing = array();
foreach ( $redirects['aliases'] ?? array() as $source => $target ) {
	$target_post = get_page_by_path( basename( trim( $target, '/' ) ), OBJECT, 'sc_product' );
	if ( ! $target_post || 'publish' !== $target_post->post_status || $target !== wp_parse_url( get_permalink( $target_post ), PHP_URL_PATH ) ) { $redirect_missing[] = $source; }
}
WP_CLI::log( 'JW_SEO_AUDIT ' . wp_json_encode( array(
	'theme' => wp_get_theme()->get( 'Version' ), 'plugins' => $versions,
	'blog_public' => (int) get_option( 'blog_public' ), 'site_language' => get_locale(),
	'pretty_permalinks' => (bool) get_option( 'permalink_structure' ),
	'sitemap_enabled' => is_callable( array( $settings, 'get' ) ) ? $settings::get( 'enable_xml_sitemap' ) : null,
	'sitemap_excluded_ids' => jw_seo_utility_ids(),
	'verified_product_redirects' => count( $redirects['aliases'] ?? array() ), 'redirect_missing_targets' => $redirect_missing,
	'row_count' => count( $rows ),
) ) );
// One record per line stays below SSH/GitHub log line limits as the site grows.
foreach ( $rows as $row ) { WP_CLI::log( 'JW_SEO_ROW ' . wp_json_encode( $row ) ); }
