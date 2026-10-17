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

/** The six live fields that a local page editor may override independently. */
function jiggy_wrigglers_sfs_override_fields() {
    return array(
        'about'           => array( 'label' => __( 'About your local group', 'jiggywrigglers' ), 'type' => 'html' ),
        'venues'          => array( 'label' => __( 'Venues', 'jiggywrigglers' ), 'type' => 'html' ),
        'bookingscontact' => array( 'label' => __( 'Booking phone number', 'jiggywrigglers' ), 'type' => 'text' ),
        'bookingsemail'   => array( 'label' => __( 'Booking email address', 'jiggywrigglers' ), 'type' => 'email' ),
        'facebook'        => array( 'label' => __( 'Facebook URL', 'jiggywrigglers' ), 'type' => 'url' ),
        'instagram'       => array( 'label' => __( 'Instagram URL', 'jiggywrigglers' ), 'type' => 'url' ),
    );
}

/** Only imported individual local pages have an SFS area; the directory does not. */
function jiggy_wrigglers_sfs_editor_area( $post_id ) {
    if ( 'page' !== get_post_type( $post_id ) ) {
        return '';
    }
    $area = get_post_meta( $post_id, '_jw_sfs_area', true );
    return is_string( $area ) && preg_match( '/\A[\p{L}\p{N} _.-]{1,100}\z/u', $area ) ? $area : '';
}

/** Validate the original value before WordPress can prefix a bare handle. */
function jiggy_wrigglers_sfs_valid_url( $value ) {
    if ( ! is_string( $value ) || ! preg_match( '#^https?://#i', trim( $value ) ) ) {
        return '';
    }
    $url = esc_url_raw( trim( $value ), array( 'http', 'https' ) );
    $parts = wp_parse_url( $url );
    if ( ! $parts || empty( $parts['host'] ) || empty( $parts['scheme'] )
        || ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true )
        || isset( $parts['user'] ) || isset( $parts['pass'] ) ) {
        return '';
    }
    return $url;
}

/**
 * Return null for automatic data, or a string (including empty) for a page override.
 * Overrides never enter the shared API cache and cannot cross area/page boundaries.
 */
function jiggy_wrigglers_sfs_manual_value( $area, $key ) {
    $fields = jiggy_wrigglers_sfs_override_fields();
    if ( ! isset( $fields[ $key ] ) || ! is_string( $area ) ) {
        return null;
    }
    $post_id = get_the_ID();
    if ( ! $post_id || trim( $area ) !== jiggy_wrigglers_sfs_editor_area( $post_id ) ) {
        return null;
    }
    $overrides = get_post_meta( $post_id, '_jw_sfs_overrides', true );
    return is_array( $overrides ) && array_key_exists( $key, $overrides ) && is_string( $overrides[ $key ] )
        ? $overrides[ $key ] : null;
}

/** Remove an empty SFS email-signature image while retaining useful source HTML. */
function jiggy_wrigglers_sfs_clean_html( $html ) {
    $html = wp_kses_post( $html );
    return preg_replace( '~<img\b[^>]*\ssrc\s*=\s*([\x22\x27])\s*(?://)?\s*\1[^>]*>~i', '', $html );
}

/** Render a manually supplied page value, retaining the shortcode's inline/block role. */
function jiggy_wrigglers_sfs_render_manual( $key, $value, $display_type ) {
    $value = trim( $value );
    if ( '' === $value ) {
        return '';
    }
    $class = 'sfs-result sfs-result--' . $key . ' sfs-result--manual';
    if ( in_array( $key, array( 'about', 'venues' ), true ) ) {
        return '<div class="' . esc_attr( $class ) . '">' . jiggy_wrigglers_sfs_clean_html( wpautop( $value ) ) . '</div>';
    }
    if ( 'email' === $display_type ) {
        $email = sanitize_email( $value );
        return is_email( $email ) && $email === $value
            ? '<span class="' . esc_attr( $class ) . '"><a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a></span>' : '';
    }
    if ( 'link' === $display_type ) {
        $url = jiggy_wrigglers_sfs_valid_url( $value );
        return $url ? '<span class="' . esc_attr( $class ) . '"><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $value ) . '</a></span>' : '';
    }
    return '<span class="' . esc_attr( $class ) . '">' . esc_html( $value ) . '</span>';
}

/** Add controls only to individual local pages, without fetching or changing live data. */
function jiggy_wrigglers_sfs_add_editor_box( $post ) {
    if ( ! $post || ! jiggy_wrigglers_sfs_editor_area( $post->ID ) ) {
        return;
    }
    add_meta_box( 'jw-sfs-local-information', __( 'Local information: automatic or manual', 'jiggywrigglers' ), 'jiggy_wrigglers_sfs_editor_box', 'page', 'normal', 'default' );
}
add_action( 'add_meta_boxes_page', 'jiggy_wrigglers_sfs_add_editor_box' );

function jiggy_wrigglers_sfs_editor_box( $post ) {
    $overrides = get_post_meta( $post->ID, '_jw_sfs_overrides', true );
    $overrides = is_array( $overrides ) ? $overrides : array();
    wp_nonce_field( 'jw_sfs_local_information_' . $post->ID, 'jw_sfs_local_information_nonce' );
    echo '<p>' . esc_html__( 'By default these fields update automatically from SFS. Tick a field to use this page’s manual value instead. A ticked field with an empty value is hidden. Untick it and update the page to return to automatic information.', 'jiggywrigglers' ) . '</p>';
    echo '<p>' . esc_html__( 'The original text inside the page’s SFS shortcodes remains an outage fallback. Other page text, images and booking buttons can still be edited in the main page editor.', 'jiggywrigglers' ) . '</p>';
    echo '<p><strong>' . esc_html__( 'Connected SFS group:', 'jiggywrigglers' ) . '</strong> ' . esc_html( jiggy_wrigglers_sfs_editor_area( $post->ID ) ) . '</p>';
    foreach ( jiggy_wrigglers_sfs_override_fields() as $key => $field ) {
        $enabled = array_key_exists( $key, $overrides );
        $value = $enabled && is_string( $overrides[ $key ] ) ? $overrides[ $key ] : '';
        $id = 'jw-sfs-' . $key;
        echo '<fieldset style="margin:20px 0;padding:12px;border:1px solid #c3c4c7"><legend><strong>' . esc_html( $field['label'] ) . '</strong></legend>';
        echo '<p><label><input type="checkbox" name="jw_sfs_manual[' . esc_attr( $key ) . ']" value="1"' . ( $enabled ? ' checked' : '' ) . '> ' . esc_html__( 'Use this page’s manual value', 'jiggywrigglers' ) . '</label></p>';
        echo '<label for="' . esc_attr( $id ) . '">' . esc_html__( 'Manual value', 'jiggywrigglers' ) . '</label>';
        if ( 'html' === $field['type'] ) {
            echo '<textarea class="widefat" rows="5" id="' . esc_attr( $id ) . '" name="jw_sfs_value[' . esc_attr( $key ) . ']" aria-describedby="' . esc_attr( $id ) . '-help">' . esc_textarea( $value ) . '</textarea>';
            echo '<p class="description" id="' . esc_attr( $id ) . '-help">' . esc_html__( 'Plain text or safe paragraph, list and link HTML. Line breaks are supported. This is used only when the manual option above is ticked.', 'jiggywrigglers' ) . '</p>';
        } else {
            echo '<input class="widefat" type="' . esc_attr( $field['type'] ) . '" id="' . esc_attr( $id ) . '" name="jw_sfs_value[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '"' . ( 'url' === $field['type'] ? ' placeholder="https://"' : '' ) . '>';
        }
        echo '</fieldset>';
    }
}

/** Save explicit page overrides; normal, preview and automatic requests cannot mutate them. */
function jiggy_wrigglers_sfs_save_editor_box( $post_id ) {
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id )
        || ! current_user_can( 'edit_post', $post_id ) || ! jiggy_wrigglers_sfs_editor_area( $post_id ) ) {
        return;
    }
    $nonce = isset( $_POST['jw_sfs_local_information_nonce'] ) && is_string( $_POST['jw_sfs_local_information_nonce'] )
        ? wp_unslash( $_POST['jw_sfs_local_information_nonce'] ) : '';
    if ( ! wp_verify_nonce( $nonce, 'jw_sfs_local_information_' . $post_id ) ) {
        return;
    }
    $enabled = isset( $_POST['jw_sfs_manual'] ) && is_array( $_POST['jw_sfs_manual'] ) ? wp_unslash( $_POST['jw_sfs_manual'] ) : array();
    $values = isset( $_POST['jw_sfs_value'] ) && is_array( $_POST['jw_sfs_value'] ) ? wp_unslash( $_POST['jw_sfs_value'] ) : array();
    $overrides = array();
    foreach ( jiggy_wrigglers_sfs_override_fields() as $key => $field ) {
        if ( ! isset( $enabled[ $key ] ) || '1' !== $enabled[ $key ] ) {
            continue;
        }
        $value = isset( $values[ $key ] ) && is_string( $values[ $key ] ) ? trim( $values[ $key ] ) : '';
        if ( 'html' === $field['type'] ) {
            $value = wp_kses_post( $value );
        } elseif ( 'url' === $field['type'] ) {
            $value = jiggy_wrigglers_sfs_valid_url( $value );
        } elseif ( 'email' === $field['type'] ) {
            $value = is_email( $value ) ? sanitize_email( $value ) : '';
        } else {
            $value = sanitize_text_field( $value );
        }
        $overrides[ $key ] = $value;
    }
    if ( $overrides ) {
        update_post_meta( $post_id, '_jw_sfs_overrides', wp_slash( $overrides ) );
    } else {
        delete_post_meta( $post_id, '_jw_sfs_overrides' );
    }
}
add_action( 'save_post_page', 'jiggy_wrigglers_sfs_save_editor_box' );

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

    $manual_value = jiggy_wrigglers_sfs_manual_value( $atts['area'], $key );
    if ( null !== $manual_value ) {
        return jiggy_wrigglers_sfs_render_manual( $key, $manual_value, sanitize_key( $atts['display_type'] ) );
    }

    $data = jiggy_wrigglers_sfs_get_group( $atts['area'] );
    if ( is_wp_error( $data ) ) {
        $fallback = is_string( $content ) ? trim( jiggy_wrigglers_sfs_clean_html( $content ) ) : '';
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
        $value = trim( $value );
        $url = jiggy_wrigglers_sfs_valid_url( $value );
        if ( ! $url ) {
            return '';
        }
        return '<span class="' . esc_attr( $class ) . '"><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">'
            . esc_html( $value ) . '</a></span>';
    }

    // About text intentionally supports paragraphs and links supplied by SFS.
    if ( 'about' === $key ) {
        return '<div class="' . esc_attr( $class ) . '">' . jiggy_wrigglers_sfs_clean_html( $value ) . '</div>';
    }
    return '<span class="' . esc_attr( $class ) . '">' . esc_html( $value ) . '</span>';
}

add_shortcode( 'frank_sfs_api', 'jiggy_wrigglers_sfs_shortcode' );
