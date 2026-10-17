<?php
/**
 * Template Name: Shop
 *
 * Server-rendered SureCart catalogue with accessible search and pagination.
 *
 * @package Jiggy_Wrigglers
 */
get_header();
include locate_template( 'components/hero/index.php' );

$content_title = get_field( 'content_title' );
$content_text  = get_field( 'content_text' );
$shop_url      = get_permalink( get_queried_object_id() );
$search        = isset( $_GET['shop_search'] ) && is_string( $_GET['shop_search'] ) ? sanitize_text_field( wp_unslash( $_GET['shop_search'] ) ) : '';
$sort          = isset( $_GET['shop_sort'] ) && is_string( $_GET['shop_sort'] ) ? sanitize_key( wp_unslash( $_GET['shop_sort'] ) ) : 'recent';
$collection    = isset( $_GET['shop_collection'] ) && is_string( $_GET['shop_collection'] ) ? sanitize_title( wp_unslash( $_GET['shop_collection'] ) ) : '';
$current_page  = isset( $_GET['shop_page'] ) && is_scalar( $_GET['shop_page'] ) ? max( 1, absint( $_GET['shop_page'] ) ) : 1;
$sorts         = array(
	'recent'     => __( 'Newest arrivals', 'jiggywrigglers' ),
	'name-asc'   => __( 'Name: A–Z', 'jiggywrigglers' ),
	'name-desc'  => __( 'Name: Z–A', 'jiggywrigglers' ),
	'price-low'  => __( 'Price: Low to high', 'jiggywrigglers' ),
	'price-high' => __( 'Price: High to low', 'jiggywrigglers' ),
);
$sort = isset( $sorts[ $sort ] ) ? $sort : 'recent';
$query_args = array(
	'post_type'           => 'sc_product',
	'post_status'         => 'publish',
	'posts_per_page'      => 12,
	'paged'               => $current_page,
	'ignore_sticky_posts' => true,
	'orderby'             => 'date',
	'order'               => 'DESC',
	's'                   => $search,
);
if ( in_array( $sort, array( 'name-asc', 'name-desc' ), true ) ) {
	$query_args['orderby'] = array( 'title' => 'name-asc' === $sort ? 'ASC' : 'DESC', 'ID' => 'ASC' );
} elseif ( in_array( $sort, array( 'price-low', 'price-high' ), true ) ) {
	// SureCart maintains numeric metadata that includes variant price ranges.
	$query_args['meta_key'] = 'min_price_amount';
	$query_args['orderby']  = array( 'meta_value_num' => 'price-low' === $sort ? 'ASC' : 'DESC', 'ID' => 'ASC' );
}
$collections = taxonomy_exists( 'sc_collection' ) ? get_terms( array( 'taxonomy' => 'sc_collection', 'hide_empty' => true ) ) : array();
if ( is_wp_error( $collections ) ) {
	$collections = array();
}
if ( $collection && taxonomy_exists( 'sc_collection' ) ) {
	$query_args['tax_query'] = array(
		array( 'taxonomy' => 'sc_collection', 'field' => 'slug', 'terms' => $collection ),
	);
}
$products = post_type_exists( 'sc_product' ) ? new WP_Query( $query_args ) : null;
$total    = $products ? (int) $products->found_posts : 0;
if ( $total && $current_page > (int) $products->max_num_pages ) {
	$current_page        = (int) $products->max_num_pages;
	$query_args['paged'] = $current_page;
	$products            = new WP_Query( $query_args );
}
$filtered = '' !== $search || '' !== $collection;
?>
<section class="shop-listing" aria-labelledby="shop-heading">
	<div class="wrap">
		<div class="shop-listing-header">
			<div>
				<p class="shop-eyebrow"><?php esc_html_e( 'A little Jiggy joy', 'jiggywrigglers' ); ?></p>
				<h2 class="heading-2" id="shop-heading"><?php echo $content_title ? wp_kses_post( $content_title ) : esc_html__( 'Bring the fun home', 'jiggywrigglers' ); ?></h2>
				<?php if ( $content_text ) : ?>
					<div class="body-medium shop-intro"><?php echo wp_kses_post( $content_text ); ?></div>
				<?php endif; ?>
			</div>
			<div class="shop-account-links">
				<?php if ( jiggy_wrigglers_surecart_page_url( 'dashboard' ) ) : ?>
					<a href="<?php echo esc_url( jiggy_wrigglers_surecart_page_url( 'dashboard' ) ); ?>"><?php esc_html_e( 'My orders & account', 'jiggywrigglers' ); ?> <span aria-hidden="true">↗</span></a>
				<?php endif; ?>
				<?php if ( shortcode_exists( 'sc_cart_menu_icon' ) ) : ?>
					<?php echo do_shortcode( '[sc_cart_menu_icon cart_icon="shopping-bag" cart_menu_always_shown="1"]' ); ?>
				<?php endif; ?>
			</div>
		</div>
		<form class="shop-listing-search-row" method="get" action="<?php echo esc_url( $shop_url ); ?>" role="search" aria-label="<?php esc_attr_e( 'Search the shop', 'jiggywrigglers' ); ?>">
			<?php if ( ! get_option( 'permalink_structure' ) ) : ?>
				<input type="hidden" name="page_id" value="<?php echo esc_attr( get_queried_object_id() ); ?>">
			<?php endif; ?>
			<div class="shop-listing-search">
				<label for="shop-search"><?php esc_html_e( 'Find something fun', 'jiggywrigglers' ); ?></label>
				<input id="shop-search" type="search" name="shop_search" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search products…', 'jiggywrigglers' ); ?>">
			</div>
			<?php if ( $collections ) : ?>
				<div class="shop-listing-sort">
					<label for="shop-collection"><?php esc_html_e( 'Collection', 'jiggywrigglers' ); ?></label>
					<select id="shop-collection" name="shop_collection">
						<option value=""><?php esc_html_e( 'All collections', 'jiggywrigglers' ); ?></option>
						<?php foreach ( $collections as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $collection, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>
			<div class="shop-listing-sort">
				<label for="shop-sort"><?php esc_html_e( 'Sort by', 'jiggywrigglers' ); ?></label>
				<select id="shop-sort" name="shop_sort">
					<?php foreach ( $sorts as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $sort, $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<button class="button button-purple shop-filter-button" type="submit"><?php esc_html_e( 'Explore', 'jiggywrigglers' ); ?> <span aria-hidden="true">→</span></button>
		</form>
		<div class="shop-listing-summary">
			<p class="shop-listing-count">
				<?php
				/* translators: %s: number of matching products. */
				printf( esc_html( _n( '%s product', '%s products', $total, 'jiggywrigglers' ) ), esc_html( number_format_i18n( $total ) ) );
				?>
			</p>
			<?php if ( $filtered || 'recent' !== $sort ) : ?>
				<a class="shop-reset" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Clear filters', 'jiggywrigglers' ); ?></a>
			<?php endif; ?>
		</div>
		<?php if ( $products && $products->have_posts() ) : ?>
			<div class="shop-listing-grid">
				<?php
				while ( $products->have_posts() ) :
					$products->the_post();
					$product = function_exists( 'sc_get_product' ) ? sc_get_product( get_the_ID() ) : null;
					$price   = $product && ! is_wp_error( $product ) ? ( $product->range_display_amount ?: $product->display_amount ) : '';
					$image   = $product && ! is_wp_error( $product ) ? $product->featured_image : null;
					?>
					<a href="<?php the_permalink(); ?>" class="shop-listing-card">
						<div class="shop-listing-card-image">
							<?php if ( $image && is_callable( array( $image, 'html' ) ) ) : ?>
								<?php echo $image->html( 'medium_large', array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SureCart native image renderer. ?>
							<?php elseif ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium_large', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<span class="shop-listing-card-placeholder" aria-hidden="true">Jiggy joy</span>
							<?php endif; ?>
						</div>
						<div class="shop-listing-card-content">
							<h3 class="heading-5"><?php echo esc_html( get_the_title() ); ?></h3>
							<?php if ( '' !== $price ) : ?>
								<p class="shop-listing-card-price"><?php echo esc_html( wp_strip_all_tags( $price ) ); ?></p>
							<?php endif; ?>
							<span class="button button-purple shop-listing-card-button"><?php esc_html_e( 'Take a look', 'jiggywrigglers' ); ?> <span aria-hidden="true">→</span></span>
						</div>
					</a>
				<?php endwhile; ?>
			</div>
			<?php if ( $products->max_num_pages > 1 ) : ?>
				<nav class="shop-listing-pagination" aria-label="<?php esc_attr_e( 'Shop pages', 'jiggywrigglers' ); ?>">
					<?php
					$pagination_url = add_query_arg(
						array_filter( array( 'shop_search' => $search, 'shop_sort' => $sort, 'shop_collection' => $collection ), function( $value ) { return '' !== $value; } ),
						$shop_url
					);
					echo wp_kses_post( paginate_links( array(
						// paginate_links() combines query arguments, then escapes each final URL.
						'base'      => str_replace( '999999999', '%#%', add_query_arg( 'shop_page', 999999999, $pagination_url ) ),
						'format'    => '',
						'current'   => $current_page,
						'total'     => (int) $products->max_num_pages,
						'type'      => 'list',
						'prev_text' => __( '← Previous', 'jiggywrigglers' ),
						'next_text' => __( 'Next →', 'jiggywrigglers' ),
						'add_args'  => false,
					) ) );
					?>
				</nav>
			<?php endif; ?>
		<?php else : ?>
			<div class="shop-listing-empty">
				<span class="shop-empty-icon" aria-hidden="true">✦</span>
				<h3 class="heading-4"><?php echo $filtered ? esc_html__( 'No matches just yet', 'jiggywrigglers' ) : esc_html__( 'More Jiggy joy is on its way', 'jiggywrigglers' ); ?></h3>
				<p><?php echo $filtered ? esc_html__( 'Try another search or explore all our products.', 'jiggywrigglers' ) : esc_html__( 'Please check back soon, or get in touch with our friendly team.', 'jiggywrigglers' ); ?></p>
				<a class="button button-purple" href="<?php echo esc_url( $filtered ? $shop_url : home_url( '/contact-us/' ) ); ?>"><?php echo $filtered ? esc_html__( 'View all products', 'jiggywrigglers' ) : esc_html__( 'Get in touch', 'jiggywrigglers' ); ?></a>
			</div>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
<?php get_footer(); ?>
