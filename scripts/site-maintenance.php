<?php
/** Fixed migration tasks for the new Forge installation; WP-CLI only. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! defined( 'ABSPATH' ) ) { exit; }
if ( 'jiggywrigglers.on-forge.com' !== wp_parse_url( home_url(), PHP_URL_HOST ) ) { WP_CLI::error( 'This task is restricted to the new Forge site.' ); }
$operation = $args[0] ?? 'diagnostics';
if ( ! in_array( $operation, array( 'diagnostics', 'import-media' ), true ) ) { WP_CLI::error( 'Unknown fixed task.' ); }
if ( 'import-media' === $operation ) {
	for ( $batch = 0; $batch < 16; $batch++ ) {
		$before = count( get_option( 'jw_legacy_media_map', array() ) );
		$messages = jw_migration_run_media();
		$after = count( get_option( 'jw_legacy_media_map', array() ) );
		WP_CLI::log( 'Media: ' . $after . ' / ' . count( jw_migration_media_sources() ) );
		if ( $after === $before || $after >= count( jw_migration_media_sources() ) ) { break; }
	}
}
$imported = jw_migration_pages();
$pages = count( array_filter( $imported, function ( $post ) { return 'page' === $post->post_type; } ) );
WP_CLI::log( wp_json_encode( array(
	'theme_version' => wp_get_theme()->get( 'Version' ),
	'pages_imported' => $pages,
	'articles_restored' => count( $imported ) - $pages,
	'media_imported' => count( get_option( 'jw_legacy_media_map', array() ) ),
	'media_expected' => count( jw_migration_media_sources() ),
	'media_failed' => count( get_option( 'jw_legacy_media_failures', array() ) ),
	'custom_css_plugin_removed' => (bool) get_option( 'jw_custom_css_migration_completed' ),
	'published_pages' => (int) wp_count_posts( 'page' )->publish,
	'published_products' => (int) wp_count_posts( 'sc_product' )->publish,
) ) );
