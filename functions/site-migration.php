<?php
/** Fixed, administrator-only migration of the original site's public pages. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function jw_legacy_manifest() {
	static $data;
	if ( null === $data ) {
		$path = get_template_directory() . '/data/legacy-pages.json';
		$data = file_exists( $path ) ? json_decode( file_get_contents( $path ), true ) : array();
	}
	return wp_parse_args( is_array( $data ) ? $data : array(), array( 'pages' => array(), 'posts' => array(), 'aliases' => array(), 'coverage' => array() ) );
}

add_action( 'template_redirect', function () {
	if ( is_admin() || ! in_array( $_SERVER['REQUEST_METHOD'] ?? 'GET', array( 'GET', 'HEAD' ), true ) ) { return; }
	$path = '/' . trim( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ), PHP_URL_PATH ), '/' ) . '/';
	$aliases = jw_legacy_manifest()['aliases'];
	$target = isset( $aliases[ $path ] ) ? get_page_by_path( trim( $aliases[ $path ], '/' ) ) : null;
	if ( $target && 'publish' === $target->post_status ) {
		wp_safe_redirect( home_url( $aliases[ $path ] ), 301 );
		exit;
	}
}, 1 );

function jw_migration_pages() {
	return get_posts( array( 'post_type' => array( 'page', 'post' ), 'post_status' => 'publish', 'posts_per_page' => -1, 'meta_key' => '_jw_legacy_source' ) );
}

function jw_migration_media_sources() {
	$sources = array();
	foreach ( array_merge( jw_legacy_manifest()['pages'], jw_legacy_manifest()['posts'] ) as $page ) {
		preg_match_all( '/<img[^>]+src="([^"]+)"/i', $page['content'], $matches );
		foreach ( $matches[1] as $url ) {
			if ( preg_match( '#^https://jiggywrigglers\.com/wp-content/uploads/[^?]+\.(?:png|jpe?g|webp|avif|gif)$#i', $url ) ) {
				$sources[ $url ] = true;
			}
		}
	}
	return array_keys( $sources );
}

add_action( 'admin_menu', function () {
	add_theme_page( 'Content migration', 'Content migration', 'manage_options', 'jw-content-migration', 'jw_migration_screen' );
} );

function jw_migration_run_pages() {
	$locations = get_page_by_path( 'locations' );
	if ( ! $locations ) { return array( 'Locations page not found; no pages imported.' ); }
	$messages = array();
	foreach ( jw_legacy_manifest()['pages'] as $item ) {
		$path = ( $item['parent'] ? $item['parent'] . '/' : '' ) . $item['slug'];
		$existing = get_page_by_path( $path );
		if ( $existing ) { continue; }
		$content = wp_kses_post( $item['content'] );
		$id = wp_insert_post( wp_slash( array(
			'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $item['slug'],
			'post_title' => $item['title'], 'post_content' => '<!-- wp:html -->' . $content . '<!-- /wp:html -->',
			'post_parent' => $item['parent'] ? $locations->ID : 0,
		) ), true );
		if ( is_wp_error( $id ) ) { $messages[] = $item['slug'] . ': ' . $id->get_error_message(); continue; }
		update_post_meta( $id, '_jw_legacy_source', $item['source_path'] );
		if ( ! empty( $item['sfs_area'] ) ) { update_post_meta( $id, '_jw_sfs_area', $item['sfs_area'] ); }
		update_post_meta( $id, '_wp_page_template', 'default' );
		$messages[] = 'Imported ' . $item['title'];
	}
	add_post_meta( $locations->ID, '_jw_previous_template', get_page_template_slug( $locations->ID ), true );
	update_post_meta( $locations->ID, '_wp_page_template', '/pages/page-locations/locations.php' );
	if ( function_exists( 'update_field' ) ) {
		if ( ! get_field( 'hero_title', $locations->ID ) ) { update_field( 'hero_title', 'Find your local Jiggy Wrigglers', $locations->ID ); }
		if ( ! get_field( 'content_text', $locations->ID ) ) { update_field( 'content_text', '<p>Explore classes, venues and birthday parties in your area. Choose your local page for booking links and contact details.</p>', $locations->ID ); }
	}
	foreach ( jw_legacy_manifest()['posts'] as $item ) {
		$posts = get_posts( array( 'name' => $item['slug'], 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1 ) );
		if ( ! $posts || trim( $posts[0]->post_content ) !== '' ) { continue; }
		$id = $posts[0]->ID;
		$result = wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => wp_kses_post( $item['content'] ) ) ), true );
		if ( is_wp_error( $result ) ) { $messages[] = $item['slug'] . ': ' . $result->get_error_message(); continue; }
		update_post_meta( $id, '_jw_legacy_source', $item['source_path'] );
		$messages[] = 'Restored article: ' . get_the_title( $id );
	}
	$typo = get_page_by_path( 'safegaurding-policy' );
	if ( $typo && ! get_page_by_path( 'safeguarding-policy' ) ) { wp_update_post( array( 'ID' => $typo->ID, 'post_name' => 'safeguarding-policy' ) ); }
	if ( ! get_page_by_path( 'affiliate-dashboard' ) ) {
		wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'affiliate-dashboard', 'post_title' => 'Affiliate Dashboard',
			'post_content' => '<p>For help with your Jiggy Wrigglers affiliate account, referral links or existing commission, please contact Head Office.</p><p><a class="button" href="mailto:enquiries@jiggywrigglers.com?subject=Affiliate%20account%20enquiry">Contact the affiliate team</a></p><p>Existing affiliate records are managed separately from your new shop customer account.</p>' ) );
	}
	update_option( 'jw_content_migration_completed', current_time( 'mysql' ), false );
	return $messages ?: array( 'All migration pages already exist. No existing page content was overwritten.' );
}

function jw_migration_run_media() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$map = get_option( 'jw_legacy_media_map', array() );
	$messages = array();
	$count = 0;
	$failures = get_option( 'jw_legacy_media_failures', array() );
	foreach ( jw_migration_media_sources() as $source ) {
		if ( ! empty( $map[ $source ] ) && wp_get_attachment_url( $map[ $source ] ) ) { continue; }
		if ( isset( $failures[ $source ] ) ) { continue; }
		// Recover a prior upload if its request ended before the mapping was saved.
		$found = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'meta_key' => '_source_url', 'meta_value' => $source, 'fields' => 'ids' ) );
		$id = $found ? $found[0] : media_sideload_image( $source, 0, 'Jiggy Wrigglers local classes', 'id' );
		if ( is_wp_error( $id ) ) { $failures[ $source ] = $id->get_error_message(); update_option( 'jw_legacy_media_failures', $failures, false ); $messages[] = basename( $source ) . ': ' . $id->get_error_message(); }
		else { $map[ $source ] = $id; update_option( 'jw_legacy_media_map', $map, false ); $messages[] = 'Imported ' . basename( $source ); }
		if ( ++$count >= 4 ) { break; }
	}
	foreach ( jw_migration_pages() as $page ) {
		$content = $page->post_content;
		foreach ( $map as $source => $id ) {
			$url = wp_get_attachment_url( $id );
			if ( $url ) { $content = str_replace( $source, esc_url_raw( $url ), $content ); }
		}
		if ( $content !== $page->post_content ) { wp_update_post( wp_slash( array( 'ID' => $page->ID, 'post_content' => $content ) ) ); }
	}
	return $messages ?: array( 'All original page images are now in this site’s media library.' );
}

function jw_migration_screen() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$messages = array();
	if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
		check_admin_referer( 'jw-content-migration' );
		$action = sanitize_key( wp_unslash( $_POST['migration_action'] ?? '' ) );
		if ( 'pages' === $action ) { $messages = jw_migration_run_pages(); }
		if ( 'media' === $action && current_user_can( 'upload_files' ) ) { $messages = jw_migration_run_media(); }
		if ( 'retry-media' === $action && current_user_can( 'upload_files' ) ) { delete_option( 'jw_legacy_media_failures' ); $messages = jw_migration_run_media(); }
	}
	$manifest = jw_legacy_manifest();
	$map = get_option( 'jw_legacy_media_map', array() );
	$media = jw_migration_media_sources();
	echo '<div class="wrap"><h1>Jiggy Wrigglers content migration</h1>';
	echo '<p>Public content captured from the original website on 17 October 2026. Existing pages and store settings are preserved. Imports can be resumed.</p>';
	if ( $messages ) { echo '<div class="notice notice-success"><p>' . esc_html( implode( ' · ', $messages ) ) . '</p></div>'; }
	$imported = jw_migration_pages();
	$pages_count = count( array_filter( $imported, function ( $post ) { return 'page' === $post->post_type; } ) );
	$posts_count = count( $imported ) - $pages_count;
	echo '<h2>Migration status</h2><p id="jw-migration-status">Original pages mapped: ' . count( $manifest['coverage'] ) . '. Imported content pages: ' . $pages_count . ' / ' . count( $manifest['pages'] ) . '. Articles restored: ' . $posts_count . ' / ' . count( $manifest['posts'] ) . '. Images imported: ' . count( array_intersect( array_keys( $map ), $media ) ) . ' / ' . count( $media ) . '.</p>';
	foreach ( array( 'pages' => 'Import missing pages and configure directory', 'media' => 'Import next 4 original images', 'retry-media' => 'Retry failed original images' ) as $action => $label ) {
		echo '<form method="post" style="margin:16px 0">'; wp_nonce_field( 'jw-content-migration' );
		echo '<input type="hidden" name="migration_action" value="' . esc_attr( $action ) . '">'; submit_button( $label, 'primary', 'submit', false ); echo '</form>';
	}
	$failures = get_option( 'jw_legacy_media_failures', array() );
	if ( $failures ) { echo '<h2>Images needing attention</h2><ul>'; foreach ( $failures as $url => $error ) { echo '<li>' . esc_html( basename( $url ) . ': ' . $error ) . '</li>'; } echo '</ul>'; }
	echo '<h2>Original page coverage</h2><table class="widefat striped"><thead><tr><th>Original page</th><th>New route</th><th>Status</th></tr></thead><tbody>';
	foreach ( $manifest['coverage'] as $row ) {
		$page = '/' === $row['target'] ? get_post( (int) get_option( 'page_on_front' ) ) : get_page_by_path( trim( $row['target'], '/' ) );
		echo '<tr><td>' . esc_html( $row['source'] ) . '</td><td><a href="' . esc_url( home_url( $row['target'] ) ) . '">' . esc_html( $row['target'] ) . '</a></td><td>' . ( $page && 'publish' === $page->post_status ? 'Published' : 'Needs attention' ) . '</td></tr>';
	}
	echo '</tbody></table><p>Payment providers, delivery rates, tax settings and a successful test order must be checked in SureCart before launch.</p></div>';
}
