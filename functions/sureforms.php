<?php
/**
 * SureForms Integration
 *
 * The theme disables the block editor everywhere (content is ACF-driven)
 * and hides the editor UI in admin. SureForms builds forms in the block
 * editor, so the sureforms_form post type is exempted from both, the
 * same way SureCart products are.
 *
 * Also keeps wp-block-library CSS on front-end pages that embed a form,
 * since the theme dequeues it for visitors and forms may use core
 * layout blocks.
 *
 * Embed a form via the Contact page's Form Shortcode ACF field:
 * [sureforms id='123']
 *
 * @package Jiggy_Wrigglers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function() {
	add_post_type_support( 'sureforms_form', 'editor' );
	add_post_type_support( 'sureforms_form', 'custom-fields' );
} );

add_filter( 'use_block_editor_for_post_type', function( $use, $post_type ) {
	if ( 'sureforms_form' === $post_type ) {
		return true;
	}
	return $use;
}, 10, 2 );

/**
 * Keep block library CSS when the current page embeds a SureForms form.
 * Runs on 'wp' (query parsed) and re-enqueues after the theme's
 * dequeue (priority 100) at priority 101.
 */
add_action( 'wp', function() {
	if ( is_user_logged_in() ) {
		return;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$has_form = has_shortcode( $post->post_content, 'sureforms' );

	if ( ! $has_form && function_exists( 'get_field' ) ) {
		$shortcode = get_field( 'contact_form_shortcode', $post->ID );
		if ( is_string( $shortcode ) && has_shortcode( $shortcode, 'sureforms' ) ) {
			$has_form = true;
		}
	}

	if ( $has_form ) {
		add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_style( 'wp-block-library' );
		}, 101 );
	}
} );
