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
$logo_white           = get_field( 'logo_white', 'option' );
$header_menu_items    = get_field( 'header_menu_items', 'option' );
$header_menu_button_1 = get_field( 'header_menu_button_1', 'option' );
$header_menu_button_2 = get_field( 'header_menu_button_2', 'option' );

$header_logo = $logo_white ? $logo_white : $company_logo;
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
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content">Skip to content</a>
<!-- Header -->
<div class="header-shell" x-data="{ menuOpen: false }">
<header class="header" :class="{ 'menu-open': menuOpen }">
	<div class="wrap">
		<div class="header-wrapper">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header-logo" title="Homepage Link and Company Logo" aria-label="Homepage Link and Company Logo">
				<?php if ( $header_logo ) : ?>
					<?php echo wp_get_attachment_image( $header_logo['ID'], 'full' ); ?>
				<?php endif; ?>
			</a>
			<div class="header-right">
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
				<button class="header-menu-toggle" @click="
							menuOpen = !menuOpen;
							if (menuOpen) {
								$nextTick(() => {
									const firstFocusable = document.querySelector('#header-menu a[href]');
									firstFocusable?.focus();
								});
							}
						"
					aria-label="Toggle menu" :aria-expanded="menuOpen">
					<span class="body-small">MENU</span>
					<div class="nav-icon" :class="{ open: menuOpen }">
						<span></span>
						<span></span>
						<span></span>
					</div>
				</button>
			</div>
		</div>
	</div>
</header>
<aside
	id="header-menu"
	class="header-menu"
	:class="{ open: menuOpen }"
	x-show="menuOpen"
	x-data="headerMenuFocusTrap()"
	@keydown.escape.prevent="
		menuOpen = false;
		$nextTick(() => {
			document.querySelector('.header-menu-toggle')?.focus();
		});
	"
	@keydown.tab="trapFocus($event)"
	:inert="!menuOpen"
>
	<div class="wrap">
		<nav class="header-menu-nav" aria-label="Primary">
			<?php if ( $header_menu_items ) : ?>
				<ul>
					<?php foreach ( $header_menu_items as $menu_item ) : ?>
						<li x-data="{ subOpen: false }">
							<div class="header-menu-item-header">
								<a class="header-menu-link" href="<?php echo esc_url( $menu_item['link']['url'] ); ?>"<?php echo ! empty( $menu_item['link']['target'] ) ? ' target="' . esc_attr( $menu_item['link']['target'] ) . '"' : ''; ?>>
									<?php echo wp_kses_post( $menu_item['link']['title'] ); ?>
								</a>
								<?php if ( ! empty( $menu_item['sub_menu'] ) ) : ?>
									<button class="header-menu-arrow" :class="{ 'is-open': subOpen }" @click="subOpen = !subOpen" aria-label="Toggle sub menu" :aria-expanded="subOpen" type="button">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( '#662483' ); ?>" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
									</button>
								<?php endif; ?>
							</div>
							<?php if ( ! empty( $menu_item['sub_menu'] ) ) : ?>
								<ul class="header-menu-sub" x-show="subOpen" x-collapse.duration.300ms>
									<?php foreach ( $menu_item['sub_menu'] as $sub_link ) : ?>
										<li>
											<a href="<?php echo esc_url( $sub_link['link']['url'] ); ?>"<?php echo ! empty( $sub_link['link']['target'] ) ? ' target="' . esc_attr( $sub_link['link']['target'] ) . '"' : ''; ?>>
												<?php echo wp_kses_post( $sub_link['link']['title'] ); ?>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</nav>
		<div class="header-menu-buttons">
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
</aside>
</div>
<main id="main-content" tabindex="-1">
