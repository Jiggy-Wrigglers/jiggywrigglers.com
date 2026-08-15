<?php
/**
 * Post/Page Duplication Functions
 *
 * @package Jiggy_Wrigglers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jiggy_wrigglers_duplicate_post() {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

	if ( ! $post_id ) {
		wp_die( esc_html__( 'No post to duplicate has been supplied.', 'jiggywrigglers' ) );
	}

	$nonce = isset( $_GET['duplicate_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['duplicate_nonce'] ) ) : '';
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'jiggy_wrigglers_duplicate_post_' . $post_id ) ) {
		wp_die( esc_html__( 'Security check failed. Please try again.', 'jiggywrigglers' ) );
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		wp_die( esc_html__( 'You do not have permission to duplicate this item.', 'jiggywrigglers' ) );
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		wp_die( esc_html__( 'Could not find the original post.', 'jiggywrigglers' ) );
	}

	$duplicable_types = apply_filters( 'jiggy_wrigglers_duplicable_post_types', get_post_types( array( 'public' => true ) ) );
	if ( ! in_array( $post->post_type, $duplicable_types, true ) ) {
		wp_die( esc_html__( 'This post type cannot be duplicated.', 'jiggywrigglers' ) );
	}

	$args = array(
		'comment_status' => $post->comment_status,
		'ping_status'    => $post->ping_status,
		'post_author'    => get_current_user_id(),
		'post_content'   => $post->post_content,
		'post_excerpt'   => $post->post_excerpt,
		'post_name'      => $post->post_name . '-copy',
		'post_parent'    => $post->post_parent,
		'post_password'  => $post->post_password,
		'post_status'    => 'draft',
		'post_title'     => $post->post_title . ' (Copy)',
		'post_type'      => $post->post_type,
		'menu_order'     => $post->menu_order,
	);

	$new_post_id = wp_insert_post( $args );

	if ( is_wp_error( $new_post_id ) ) {
		wp_die( esc_html__( 'Duplication failed:', 'jiggywrigglers' ) . ' ' . $new_post_id->get_error_message() );
	}

	$taxonomies = get_object_taxonomies( $post->post_type );
	foreach ( $taxonomies as $taxonomy ) {
		$term_ids = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
		if ( ! is_wp_error( $term_ids ) && ! empty( $term_ids ) ) {
			wp_set_object_terms( $new_post_id, array_map( 'intval', $term_ids ), $taxonomy );
		}
	}

	jiggy_wrigglers_duplicate_post_meta( $post_id, $new_post_id );

	$thumbnail_id = get_post_thumbnail_id( $post_id );
	if ( $thumbnail_id ) {
		set_post_thumbnail( $new_post_id, $thumbnail_id );
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'action'          => 'edit',
				'post'            => $new_post_id,
				'jiggy_wrigglers_duplicated' => 1,
			),
			admin_url( 'post.php' )
		)
	);
	exit;
}
add_action( 'admin_action_jiggy_wrigglers_duplicate_post', 'jiggy_wrigglers_duplicate_post' );

function jiggy_wrigglers_duplicate_post_meta( $from_id, $to_id ) {
	$meta = get_post_meta( $from_id );
	if ( ! $meta ) {
		return;
	}

	$skip_keys = apply_filters( 'jiggy_wrigglers_duplicate_skip_meta_keys', array(
		'_wp_old_slug',
		'_edit_lock',
		'_edit_last',
		'_thumbnail_id',
	) );

	foreach ( $meta as $key => $values ) {
		if ( in_array( $key, $skip_keys, true ) ) {
			continue;
		}

		if ( 0 === strpos( $key, '_wp_' ) ) {
			continue;
		}

		foreach ( $values as $value ) {
			add_post_meta( $to_id, $key, $value );
		}
	}
}

function jiggy_wrigglers_duplicate_admin_notice() {
	if ( ! isset( $_GET['jiggy_wrigglers_duplicated'] ) ) {
		return;
	}

	$post_type_obj = get_post_type_object( get_post_type() );
	$label = $post_type_obj ? $post_type_obj->labels->singular_name : __( 'Item', 'jiggywrigglers' );

	printf(
		'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
		sprintf(
			/* translators: %s: post type singular name */
			esc_html__( '%s duplicated as a draft. Edit and publish when ready.', 'jiggywrigglers' ),
			esc_html( $label )
		)
	);
}
add_action( 'admin_notices', 'jiggy_wrigglers_duplicate_admin_notice' );

function jiggy_wrigglers_add_duplicate_link( $actions, $post ) {
	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return $actions;
	}

	$duplicable_types = apply_filters( 'jiggy_wrigglers_duplicable_post_types', get_post_types( array( 'public' => true ) ) );
	if ( ! in_array( $post->post_type, $duplicable_types, true ) ) {
		return $actions;
	}

	$url = wp_nonce_url(
		admin_url( 'admin.php?action=jiggy_wrigglers_duplicate_post&post=' . $post->ID ),
		'jiggy_wrigglers_duplicate_post_' . $post->ID,
		'duplicate_nonce'
	);

	$actions['duplicate'] = sprintf(
		'<a href="%s" title="%s">%s</a>',
		esc_url( $url ),
		esc_attr__( 'Duplicate this item', 'jiggywrigglers' ),
		esc_html__( 'Duplicate', 'jiggywrigglers' )
	);

	return $actions;
}

function jiggy_wrigglers_duplicate_register_row_filters() {
	$post_types = get_post_types( array( 'public' => true ) );
	foreach ( $post_types as $post_type ) {
		add_filter( "{$post_type}_row_actions", 'jiggy_wrigglers_add_duplicate_link', 10, 2 );
	}
}
add_action( 'admin_init', 'jiggy_wrigglers_duplicate_register_row_filters' );

function jiggy_wrigglers_duplicate_post_button() {
	global $post;

	if ( ! $post || ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}

	$duplicable_types = apply_filters( 'jiggy_wrigglers_duplicable_post_types', get_post_types( array( 'public' => true ) ) );
	if ( ! in_array( $post->post_type, $duplicable_types, true ) ) {
		return;
	}

	$url = wp_nonce_url(
		admin_url( 'admin.php?action=jiggy_wrigglers_duplicate_post&post=' . $post->ID ),
		'jiggy_wrigglers_duplicate_post_' . $post->ID,
		'duplicate_nonce'
	);

	printf(
		'<div class="misc-pub-section"><a href="%s" class="button">%s</a></div>',
		esc_url( $url ),
		esc_html__( 'Duplicate this', 'jiggywrigglers' )
	);
}
add_action( 'post_submitbox_misc_actions', 'jiggy_wrigglers_duplicate_post_button' );