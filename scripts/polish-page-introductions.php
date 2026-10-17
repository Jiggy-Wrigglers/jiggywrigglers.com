<?php
/** Narrow, repeatable corrections to imported presentation fields on staging. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! defined( 'ABSPATH' ) ) { exit; }
if ( 'jiggywrigglers.on-forge.com' !== wp_parse_url( home_url(), PHP_URL_HOST ) ) { WP_CLI::error( 'New Forge site only.' ); }
if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) { WP_CLI::error( 'ACF is required.' ); }
$backup = get_option( 'jw_ui_introduction_backup', array() );
$changes = array();
$remember = static function ( $key, $value ) use ( &$backup ) {
    if ( ! array_key_exists( $key, $backup ) ) {
        $backup[ $key ] = $value;
        update_option( 'jw_ui_introduction_backup', $backup, false );
    }
};
$contact = get_page_by_path( 'contact-us' );
if ( $contact && 'Contact | Enquiries & Franchise Opportunities |' === trim( (string) get_field( 'hero_title', $contact->ID ) ) ) {
    $remember( 'contact_hero_title', get_field( 'hero_title', $contact->ID, false ) );
    if ( ! update_field( 'hero_title', 'Contact Jiggy Wrigglers', $contact->ID ) ) { WP_CLI::error( 'Contact heading was not updated.' ); }
    $changes[] = 'Clean contact heading';
}
$front_id = (int) get_option( 'page_on_front' );
$groups = get_field( 'groups_repeater', $front_id );
foreach ( is_array( $groups ) ? $groups : array() as $index => $group ) {
    $link = is_array( $group['link'] ?? null ) ? ( $group['link']['url'] ?? '' ) : ( $group['link'] ?? '' );
    if ( 'Jiggy Wrigglers' !== trim( (string) ( $group['title'] ?? '' ) ) || '/about-jiggy-wrigglers/' !== wp_parse_url( $link, PHP_URL_PATH ) ) { continue; }
    $remember( 'home_about_card', array( 'row' => $index + 1, 'title' => $group['title'], 'text' => $group['text'] ?? '' ) );
    if ( ! update_sub_field( array( 'groups_repeater', $index + 1, 'title' ), 'About Jiggy Wrigglers', $front_id )
        || ! update_sub_field( array( 'groups_repeater', $index + 1, 'text' ), 'Discover our story and the ethos behind our music, movement and sensory classes for babies, toddlers and preschoolers.', $front_id ) ) { WP_CLI::error( 'About card was not fully updated.' ); }
    $changes[] = 'Match About card copy to its destination';
}
$locations = get_page_by_path( 'locations' );
$location_images = $locations ? get_field( 'hero_images', $locations->ID, false ) : array();
$home_images = get_field( 'banner_images', $front_id, false );
if ( $locations && is_array( $location_images ) && ! empty( $location_images[0] ) && is_array( $home_images ) && ! empty( $home_images[0] )
    && 'Jiggy-Stock-18-1.avif' === basename( (string) wp_get_attachment_url( (int) $location_images[0] ) ) ) {
    $remember( 'locations_hero_images', $location_images );
    if ( ! update_field( 'hero_images', array( (int) $home_images[0] ), $locations->ID ) ) { WP_CLI::error( 'Location photograph was not updated.' ); }
    $changes[] = 'Use an existing class photograph for the location finder';
}
WP_CLI::log( 'JW_UI_CONTENT ' . wp_json_encode( array( 'changes' => $changes, 'backup' => 'jw_ui_introduction_backup' ) ) );
