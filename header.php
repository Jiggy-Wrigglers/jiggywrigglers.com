<?php
/**
 * The header for our theme
 *
 * Header content is managed via the Site Settings options page
 * (ACF field group: Header Menu tab).
 *
 * @package Jiggy_Wrigglers
 */

$company_logo         = get_field( 'company_logo', 'option' );
$header_menu_items    = get_field( 'header_menu_items', 'option' );
$header_menu_button_1 = get_field( 'header_menu_button_1', 'option' );
$header_menu_button_2 = get_field( 'header_menu_button_2', 'option' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<style>[x-cloak] { display: none !important; }</style>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div x-data="{ open: false }" x-bind:class="{ 'menu-open': open }">

    <!-- Desktop Header -->
    <!-- ------------------------------------------------- -->
    <header class="site-header desktop-header">
        <div class="wrap">
            <a title="Homepage Link and Company Logo" aria-label="Homepage Link and Company Logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header-logo">
                <?php if ( $company_logo ) : ?>
                    <?php echo wp_get_attachment_image( $company_logo['ID'], 'full' ); ?>
                <?php endif; ?>
            </a>

            <?php if ( $header_menu_items ) : ?>
                <nav class="header-menu" aria-label="Primary">
                    <ul>
                        <?php foreach ( $header_menu_items as $menu_item ) : ?>
                            <?php foreach ( $menu_item['item'] as $link ) : ?>
                                <li<?php echo ! empty( $link['sub_menu'] ) ? ' class="has-sub-menu"' : ''; ?>>
                                    <a href="<?php echo esc_url( $link['link']['url'] ); ?>"<?php echo ! empty( $link['link']['target'] ) ? ' target="' . esc_attr( $link['link']['target'] ) . '"' : ''; ?>><?php echo esc_html( $link['link']['title'] ); ?></a>
                                    <?php if ( ! empty( $link['sub_menu'] ) ) : ?>
                                        <ul class="sub-menu">
                                            <?php foreach ( $link['sub_menu'] as $sub_link ) : ?>
                                                <li>
                                                    <a href="<?php echo esc_url( $sub_link['link']['url'] ); ?>"<?php echo ! empty( $sub_link['link']['target'] ) ? ' target="' . esc_attr( $sub_link['link']['target'] ) . '"' : ''; ?>><?php echo esc_html( $sub_link['link']['title'] ); ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <div class="header-buttons">
                <?php if ( $header_menu_button_1 ) : ?>
                    <a class="button button-blue" href="<?php echo esc_url( $header_menu_button_1['url'] ); ?>"<?php echo ! empty( $header_menu_button_1['target'] ) ? ' target="' . esc_attr( $header_menu_button_1['target'] ) . '"' : ''; ?>>
                        <?php echo esc_html( $header_menu_button_1['title'] ); ?>
                    </a>
                <?php endif; ?>
                <?php if ( $header_menu_button_2 ) : ?>
                    <a class="button button-orange" href="<?php echo esc_url( $header_menu_button_2['url'] ); ?>"<?php echo ! empty( $header_menu_button_2['target'] ) ? ' target="' . esc_attr( $header_menu_button_2['target'] ) . '"' : ''; ?>>
                        <?php echo esc_html( $header_menu_button_2['title'] ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Mobile Header -->
    <!-- ------------------------------------------------- -->
    <header class="site-header mobile-header">
        <div class="wrap">
            <a title="Homepage Link and Company Logo" aria-label="Homepage Link and Company Logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header-logo">
                <?php if ( $company_logo ) : ?>
                    <?php echo wp_get_attachment_image( $company_logo['ID'], 'full' ); ?>
                <?php endif; ?>
            </a>
            <button class="header-burger" type="button" aria-label="Toggle menu" @click="open = true">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Mobile Menu Panel -->
    <!-- ------------------------------------------------- -->
    <div class="mobile-menu" x-cloak="true" x-show="open" x-transition.opacity.duration.200ms @click.away="open = false">
        <div class="wrap">
            <button class="mobile-menu-close" type="button" aria-label="Close menu" @click="open = false">&times;</button>
            <?php if ( $header_menu_items ) : ?>
                <nav class="mobile-menu-nav" aria-label="Primary mobile">
                    <ul>
                        <?php foreach ( $header_menu_items as $menu_item ) : ?>
                            <?php foreach ( $menu_item['item'] as $link ) : ?>
                                <li>
                                    <a href="<?php echo esc_url( $link['link']['url'] ); ?>"<?php echo ! empty( $link['link']['target'] ) ? ' target="' . esc_attr( $link['link']['target'] ) . '"' : ''; ?>><?php echo esc_html( $link['link']['title'] ); ?></a>
                                    <?php if ( ! empty( $link['sub_menu'] ) ) : ?>
                                        <ul class="sub-menu">
                                            <?php foreach ( $link['sub_menu'] as $sub_link ) : ?>
                                                <li>
                                                    <a href="<?php echo esc_url( $sub_link['link']['url'] ); ?>"<?php echo ! empty( $sub_link['link']['target'] ) ? ' target="' . esc_attr( $sub_link['link']['target'] ) . '"' : ''; ?>><?php echo esc_html( $sub_link['link']['title'] ); ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>
            <div class="mobile-menu-buttons">
                <?php if ( $header_menu_button_1 ) : ?>
                    <a class="button button-blue" href="<?php echo esc_url( $header_menu_button_1['url'] ); ?>"<?php echo ! empty( $header_menu_button_1['target'] ) ? ' target="' . esc_attr( $header_menu_button_1['target'] ) . '"' : ''; ?>>
                        <?php echo esc_html( $header_menu_button_1['title'] ); ?>
                    </a>
                <?php endif; ?>
                <?php if ( $header_menu_button_2 ) : ?>
                    <a class="button button-orange" href="<?php echo esc_url( $header_menu_button_2['url'] ); ?>"<?php echo ! empty( $header_menu_button_2['target'] ) ? ' target="' . esc_attr( $header_menu_button_2['target'] ) . '"' : ''; ?>>
                        <?php echo esc_html( $header_menu_button_2['title'] ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
