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

// Match only the evidenced source shapes; preserve unrelated editorial SVGs.
$svg_path_signature = static function ( $svg ) {
    if ( ! is_string( $svg ) || ! preg_match( '/^\s*<svg\b/i', $svg ) || ! preg_match( '#</svg>\s*$#i', $svg )
        || preg_match( '/\b(?:on[\w-]+|href|style)\s*=/i', $svg ) ) { return ''; }
    $markup = preg_replace( '/<!--.*?-->/s', '', $svg );
    preg_match_all( '#</?([a-z][a-z0-9:-]*)\b#i', $markup, $tags );
    foreach ( $tags[1] as $tag ) {
        if ( ! in_array( strtolower( $tag ), array( 'svg', 'path', 'g', 'title', 'desc' ), true ) ) { return ''; }
    }
    if ( 1 !== preg_match_all( '/<path\b[^>]*\bd\s*=\s*([\x22\x27])(.*?)\1/is', $markup, $paths ) ) { return ''; }
    return hash( 'sha256', preg_replace( '/\s+/', '', $paths[2][0] ) );
};
$babies = get_page_by_path( 'jiggy-babies-groups' );
$people_icon = $babies ? get_field( 'key_info_3_icon', $babies->ID, false ) : '';
if ( 'b9dc8ea5bf046676a793b75e17f5c351abfb73be9e696fb894cccf5e95911740' === $svg_path_signature( $people_icon ) ) {
    foreach ( array( 'jiggy-tots-groups', 'jiggy-mixed-groups' ) as $slug ) {
        $page = get_page_by_path( $slug );
        $current_icon = $page ? get_field( 'key_info_3_icon', $page->ID, false ) : '';
        $label = $page ? wp_strip_all_tags( (string) get_field( 'key_info_3_title', $page->ID ) ) : '';
        if ( ! $page || false === stripos( $label, 'Group Size:' )
            || '27418138225558a39fe0462dc856cc7bba554a9185905817c622785cc00ca7ce' !== $svg_path_signature( $current_icon )
            || $current_icon !== get_field( 'key_info_3_icon', $page->ID, false ) ) { continue; }
        $remember( $slug . '_key_info_3_icon', $current_icon );
        if ( ! update_field( 'key_info_3_icon', $people_icon, $page->ID ) ) { WP_CLI::error( 'Programme group-size icon was not updated.' ); }
        $changes[] = 'Use the existing people icon for ' . $slug . ' group size';
    }
}

$normalise_text = static function ( $value ) {
    return trim( preg_replace( '/\s+/u', ' ', str_replace( array( '’', '‘' ), "'", html_entity_decode( wp_strip_all_tags( (string) $value ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) ) );
};
$vintage = get_page_by_path( 'jiggy-vintage-groups' );
$vintage_intro = 'Jiggy Vintage is an interactive music and movement programme designed to entertain elderly people living in residential accommodation.';
$generic_intro = "Jiggy Wrigglers is all about fun! Our groups are NOT classes, they're social occasions! We offer upbeat, funky music and movement groups for babies, toddlers and preschoolers at an affordable!";
if ( $vintage ) {
    $current_intro = get_field( 'information_text', $vintage->ID, false );
    $known_generic_intros = array( $normalise_text( $generic_intro ), $normalise_text( str_replace( 'at an affordable!', 'at an affordable price.', $generic_intro ) ) );
    if ( in_array( $normalise_text( $current_intro ), $known_generic_intros, true )
        && false !== strpos( $normalise_text( get_field( 'content_text', $vintage->ID, false ) ), $normalise_text( $vintage_intro ) )
        && $current_intro === get_field( 'information_text', $vintage->ID, false ) ) {
        $remember( 'vintage_information_text', $current_intro );
        if ( ! update_field( 'information_text', $vintage_intro, $vintage->ID ) ) { WP_CLI::error( 'Vintage introduction was not updated.' ); }
        $changes[] = 'Use the existing Vintage introduction rather than infant-programme copy';
    }
    $vintage_images = get_field( 'hero_images', $vintage->ID, false );
    $replacement_id = is_array( $home_images ) && ! empty( $home_images[0] ) ? (int) $home_images[0] : 0;
    $replacement_name = basename( (string) wp_get_attachment_url( $replacement_id ) );
    $replacement_meta = $replacement_id ? wp_get_attachment_metadata( $replacement_id ) : array();
    // The original Vintage hero used Homepage-Hero-Image.jpg. These existing
    // converted originals are source-approved; do not substitute arbitrary media.
    if ( is_array( $vintage_images ) && 1 === count( $vintage_images ) && ! empty( $vintage_images[0] )
        && 'jiggyvintage-groups.avif' === basename( (string) wp_get_attachment_url( (int) $vintage_images[0] ) )
        && in_array( $replacement_name, array( 'Homepage-Hero-Image.avif', 'Homepage-Hero-Image@2x-scaled-1.avif' ), true )
        && (int) ( $replacement_meta['width'] ?? 0 ) >= 1200 && (int) ( $replacement_meta['height'] ?? 0 ) >= 500
        && $vintage_images === get_field( 'hero_images', $vintage->ID, false ) ) {
        $remember( 'vintage_hero_images', $vintage_images );
        if ( ! update_field( 'hero_images', array( $replacement_id ), $vintage->ID ) ) { WP_CLI::error( 'Vintage hero photograph was not updated.' ); }
        $changes[] = 'Restore the original Vintage hero photograph using an existing high-resolution attachment';
    }
}
// Keep the separate 240 x 320 Sara content photograph: no evidenced replacement
// for that editorial image has been selected.
WP_CLI::log( 'JW_UI_CONTENT ' . wp_json_encode( array( 'changes' => $changes, 'backup' => 'jw_ui_introduction_backup' ) ) );
