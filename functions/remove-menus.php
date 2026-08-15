<?php
/**
 * Remove Admin Menus
 *
 * @package Jiggy_Wrigglers
 */

function jiggy_wrigglers_remove_customiser_settings( $wp_customize ) {
	$wp_customize->remove_section( 'colors' );
	$wp_customize->remove_section( 'background_image' );
	$wp_customize->remove_section( 'static_front_page' );
	$wp_customize->remove_section( 'custom_css' );
	$wp_customize->remove_section( 'header_image' );
}
add_action( 'customize_register', 'jiggy_wrigglers_remove_customiser_settings', 20 );

function jiggy_wrigglers_remove_admin_menus() {
	remove_menu_page( 'edit-comments.php' );
	remove_menu_page( 'tools.php' );
}
add_action( 'admin_menu', 'jiggy_wrigglers_remove_admin_menus', 999 );

function jiggy_wrigglers_remove_appearance_submenus() {
	remove_submenu_page( 'themes.php', 'site-editor.php?p=/pattern' );
	remove_submenu_page( 'themes.php', 'site-editor.php' );

	global $submenu;

	if ( isset( $submenu['themes.php'] ) ) {
		foreach ( $submenu['themes.php'] as $key => $item ) {
			$remove = array( 'Header', 'Background', 'Widgets', 'Theme File Editor', 'Editor', 'Pattern Editor', 'Menus', 'Customise', 'Customizer' );
			if ( in_array( $item[0], $remove, true ) ) {
				unset( $submenu['themes.php'][ $key ] );
			}
		}
	}
}
add_action( 'admin_menu', 'jiggy_wrigglers_remove_appearance_submenus', 999 );

function jiggy_wrigglers_remove_settings_submenus() {
	remove_submenu_page( 'options-general.php', 'options-writing.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}
add_action( 'admin_menu', 'jiggy_wrigglers_remove_settings_submenus', 999 );
