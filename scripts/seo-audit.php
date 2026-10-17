<?php
/** Read-only metadata inventory, called only by the fixed Forge CLI task. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! defined( 'ABSPATH' ) ) { exit; }
if ( 'jiggywrigglers.on-forge.com' !== wp_parse_url( home_url(), PHP_URL_HOST ) ) { WP_CLI::error( 'New Forge site only.' ); }
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
	$rows[] = array(
		'id' => $item->ID, 'type' => $item->post_type, 'slug' => $item->post_name,
		'url' => get_permalink( $item ), 'title' => wp_strip_all_tags( $meta['page_title'] ?? get_the_title( $item ) ),
		'description' => wp_strip_all_tags( $meta['page_description'] ?? '' ),
		'canonical' => ! empty( $meta['canonical_url'] ) ? $meta['canonical_url'] : get_permalink( $item ),
		'noindex_override' => $meta['post_no_index'] ?? '',
	);
}
WP_CLI::log( 'JW_SEO_AUDIT ' . wp_json_encode( array(
	'theme' => wp_get_theme()->get( 'Version' ), 'plugins' => $versions,
	'blog_public' => (int) get_option( 'blog_public' ), 'site_language' => get_locale(),
	'pretty_permalinks' => (bool) get_option( 'permalink_structure' ),
	'sitemap_enabled' => is_callable( array( $settings, 'get' ) ) ? $settings::get( 'enable_xml_sitemap' ) : null,
	'rows' => $rows,
) ) );
