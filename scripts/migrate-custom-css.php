<?php
/** Run once with WP-CLI after the stylesheet has been deployed in the theme. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! defined( 'ABSPATH' ) ) { exit; }
if ( 'jiggywrigglers.on-forge.com' !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
	WP_CLI::warning( 'Custom CSS migration is restricted to the Forge staging site.' );
	return;
}
if ( get_option( 'jw_custom_css_migration_completed' ) ) { return; }
$plugin = 'custom-css-js/custom-css-js.php';
if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) { return; }
$posts = get_posts( array( 'post_type' => 'custom-css-js', 'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ), 'posts_per_page' => -1 ) );
$stylesheet = get_template_directory() . '/css/surecart.css';
$normalize = function ( $value ) { return trim( str_replace( array( "\r\n", "\r" ), "\n", $value ) ); };
if ( 1 !== count( $posts ) || ! file_exists( $stylesheet ) || $normalize( $posts[0]->post_content ) !== $normalize( file_get_contents( $stylesheet ) ) ) {
	WP_CLI::warning( 'Custom code differs from the verified theme stylesheet. Plugin preserved for review.' );
	return;
}
update_option( 'jw_custom_css_migration_backup', array( 'id' => $posts[0]->ID, 'title' => $posts[0]->post_title, 'content' => $posts[0]->post_content ), false );
require_once ABSPATH . 'wp-admin/includes/plugin.php';
deactivate_plugins( $plugin );
$result = delete_plugins( array( $plugin ) );
if ( is_wp_error( $result ) ) {
	WP_CLI::warning( 'Styles migrated and plugin deactivated, but removal failed: ' . $result->get_error_message() );
	return;
}
update_option( 'jw_custom_css_migration_completed', current_time( 'mysql' ), false );
WP_CLI::success( 'Custom CSS moved into the theme. Simple Custom CSS and JS removed.' );
