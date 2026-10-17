<?php
/**
 * Live franchise information from the public SFS group API.
 *
 * Retains the legacy [frank_sfs_api area="Bournemouth" key="about"] shortcode.
 * Only this fixed public endpoint is requested; no credentials are required.
 *
 * @package Jiggy_Wrigglers
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Read one SFS group, with request memoization and a last known good response.
 *
 * @param string $area SFS group identifier, which may differ from the page slug.
 * @return array|WP_Error Whitelisted public group data, or a non-sensitive error.
 */
function jiggy_wrigglers_sfs_get_group( $area ) {
    static $request_cache = array();

    if ( ! is_scalar( $area ) ) {
        return new WP_Error( 'jw_sfs_invalid_area', __( 'Invalid franchise area.', 'jiggywrigglers' ) );
    }

    $area = trim( (string) $area );
    if ( ! preg_match( '/\A[\p{L}\p{N} _.-]{1,100}\z/u', $area ) ) {
        return new WP_Error( 'jw_sfs_invalid_area', __( 'Invalid franchise area.', 'jiggywrigglers' ) );
    }

    if ( array_key_exists( $area, $request_cache ) ) {
        return $request_cache[ $area ];
    }

    $cache_key = 'jw_sfs_group_' . md5( $area );
    $fresh = get_transient( $cache_key );
    if ( is_array( $fresh ) && ! empty( $fresh ) ) {
        $request_cache[ $area ] = $fresh;
        return $fresh;
    }

    $response = wp_safe_remote_get(
        add_query_arg( 'group', $area, 'https://my-sfs.com/app.api/v200/api.group.php' ),
        array(
            'timeout'             => 6,
            'redirection'         => 0,
            'limit_response_size' => 262144,
            'headers'             => array( 'Accept' => 'application/json' ),
        )
    );

    $data = null;
    if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
        $decoded = json_decode( wp_remote_retrieve_body( $response ), true );
        $has_identity = is_array( $decoded ) && (
            ( isset( $decoded['name'] ) && is_string( $decoded['name'] ) && '' !== trim( $decoded['name'] ) )
            || ( isset( $decoded['businessname'] ) && is_string( $decoded['businessname'] ) && '' !== trim( $decoded['businessname'] ) )
        );
        if ( $has_identity && empty( $decoded['error'] ) ) {
            $data = array();
            $text_keys = array( 'name', 'businessname', 'about', 'bookingscontact', 'bookingsemail',
                'facebook', 'instagram', 'twitter', 'imagelink', 'bookclasslink', 'bookpartylink', 'bookvouchersextraslink' );
            foreach ( $text_keys as $key ) {
                if ( isset( $decoded[ $key ] ) && is_string( $decoded[ $key ] ) ) {
                    $data[ $key ] = 'about' === $key ? wp_kses_post( $decoded[ $key ] ) : $decoded[ $key ];
                }
            }
            foreach ( array( 'venues', 'services' ) as $key ) {
                $data[ $key ] = array();
                if ( empty( $decoded[ $key ] ) || ! is_array( $decoded[ $key ] ) ) {
                    continue;
                }
                foreach ( array_slice( $decoded[ $key ], 0, 250 ) as $item ) {
                    if ( ! is_array( $item ) ) {
                        continue;
                    }
                    $name = isset( $item['name'] ) && is_string( $item['name'] ) ? $item['name'] : '';
                    $address = isset( $item['address'] ) && is_string( $item['address'] ) ? $item['address'] : '';
                    if ( '' !== $name || '' !== $address ) {
                        $data[ $key ][] = array( 'name' => $name, 'address' => $address );
                    }
                }
            }
        }
    }

    if ( is_array( $data ) && ! empty( $data ) ) {
        set_transient( $cache_key, $data, 10 * MINUTE_IN_SECONDS );
        set_transient( $cache_key . '_last_good', $data, 7 * DAY_IN_SECONDS );
        $request_cache[ $area ] = $data;
        return $data;
    }

    // Do not persist an error or replace the most recent successful response.
    $last_good = get_transient( $cache_key . '_last_good' );
    if ( is_array( $last_good ) && ! empty( $last_good ) ) {
        $request_cache[ $area ] = $last_good;
        return $last_good;
    }

    $error = new WP_Error( 'jw_sfs_unavailable', __( 'Local franchise information is temporarily unavailable.', 'jiggywrigglers' ) );
    $request_cache[ $area ] = $error;
    return $error;
}

/**
 * Keep an unavailable feed from leaving a local page without a next step.
 *
 * @return string Safe, unobtrusive contact guidance.
 */
function jiggy_wrigglers_sfs_fallback() {
    return '<span class="sfs-result sfs-result--unavailable">'
        . esc_html__( 'Please contact your local franchise for the latest information.', 'jiggywrigglers' )
        . ' <a href="' . esc_url( home_url( '/contact-us/' ) ) . '">'
        . esc_html__( 'Contact us', 'jiggywrigglers' ) . '</a></span>';
}

/**
 * Render a legacy SFS shortcode without exposing arbitrary response fields.
 *
 * @param array $atts Shortcode attributes.
 * @param string|null $content Optional original public field HTML used only on outages.
 * @return string Escaped or allowlisted HTML.
 */
function jiggy_wrigglers_sfs_shortcode( $atts = array(), $content = null ) {
    $atts = shortcode_atts(
        array( 'key' => 'about', 'area' => 'Shrewsbury', 'display_type' => 'text' ),
        is_array( $atts ) ? $atts : array(),
        'frank_sfs_api'
    );

    if ( ! is_scalar( $atts['key'] ) || ! is_scalar( $atts['display_type'] ) ) {
        return '';
    }

    $key = sanitize_key( $atts['key'] );
    $allowed_keys = array( 'name', 'businessname', 'about', 'venues', 'services', 'bookingscontact',
        'bookingsemail', 'facebook', 'instagram', 'twitter', 'imagelink', 'bookclasslink', 'bookpartylink', 'bookvouchersextraslink' );
    if ( ! in_array( $key, $allowed_keys, true ) ) {
        return '';
    }

    $data = jiggy_wrigglers_sfs_get_group( $atts['area'] );
    if ( is_wp_error( $data ) ) {
        $fallback = is_string( $content ) ? trim( wp_kses_post( $content ) ) : '';
        if ( '' !== $fallback ) {
            $tag = in_array( $key, array( 'about', 'venues', 'services' ), true ) ? 'div' : 'span';
            return '<' . $tag . ' class="sfs-result sfs-result--' . esc_attr( $key ) . ' sfs-result--fallback">'
                . $fallback . '</' . $tag . '>';
        }
        return jiggy_wrigglers_sfs_fallback();
    }
    if ( ! array_key_exists( $key, $data ) || '' === $data[ $key ] || array() === $data[ $key ] ) {
        return '';
    }

    $value = $data[ $key ];
    $class = 'sfs-result sfs-result--' . $key;
    if ( is_array( $value ) ) {
        $items = '';
        foreach ( $value as $item ) {
            $label = $item['name'] . ( $item['name'] && $item['address'] ? ': ' : '' ) . $item['address'];
            $items .= '<li>' . esc_html( $label ) . '</li>';
        }
        return '<div class="' . esc_attr( $class ) . '"><ul class="sfs-list sfsList">' . $items . '</ul></div>';
    }

    $display_type = sanitize_key( $atts['display_type'] );
    if ( 'email' === $display_type ) {
        $email = sanitize_email( $value );
        if ( ! is_email( $email ) || $email !== trim( $value ) ) {
            return '';
        }
        return '<span class="' . esc_attr( $class ) . '"><a href="' . esc_url( 'mailto:' . $email ) . '">'
            . esc_html( $email ) . '</a></span>';
    }

    if ( 'link' === $display_type ) {
        $url = esc_url_raw( $value, array( 'http', 'https' ) );
        $parts = wp_parse_url( $url );
        if ( ! $parts || empty( $parts['host'] ) || empty( $parts['scheme'] )
            || ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true )
            || isset( $parts['user'] ) || isset( $parts['pass'] ) ) {
            return '';
        }
        return '<span class="' . esc_attr( $class ) . '"><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">'
            . esc_html( $value ) . '</a></span>';
    }

    // About text intentionally supports paragraphs and links supplied by SFS.
    if ( 'about' === $key ) {
        return '<div class="' . esc_attr( $class ) . '">' . wp_kses_post( $value ) . '</div>';
    }
    return '<span class="' . esc_attr( $class ) . '">' . esc_html( $value ) . '</span>';
}

add_shortcode( 'frank_sfs_api', 'jiggy_wrigglers_sfs_shortcode' );
