<?php
/**
 * Template Name: Shop
 *
 * Single-use shop listing page. Lists all SureCart products with
 * client-side search, sorting, and pagination via Alpine.js.
 *
 * @package Jiggy_Wrigglers
 */
get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-shop/shop.css">

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<!-- Shop Listing Section -->
<!-- ------------------------------------------------- -->
<?php
$content_title = get_field('content_title');
$content_text = get_field('content_text');

$products = array();
$query = new WP_Query(array(
    'post_type' => 'sc_product',
    'posts_per_page' => -1,
    'post_status' => 'publish',
));

if ($query->have_posts()) {
    foreach ($query->posts as $post_obj) {
        $product_data = array(
            'id' => $post_obj->ID,
            'title' => get_the_title($post_obj->ID),
            'url' => get_permalink($post_obj->ID),
            'image' => '',
            'excerpt' => wp_strip_all_tags(get_the_excerpt($post_obj->ID)),
            'price' => '',
        );

        $thumb = get_the_post_thumbnail_url($post_obj->ID, 'large');
        if ($thumb) {
            $product_data['image'] = $thumb;
        }

        if (function_exists('sc_get_product')) {
            $sc_product = sc_get_product($post_obj->ID);
            if ($sc_product && !is_wp_error($sc_product)) {
                if (!empty($sc_product->range_display_amount)) {
                    $product_data['price'] = $sc_product->range_display_amount;
                } elseif (!empty($sc_product->display_amount)) {
                    $product_data['price'] = $sc_product->display_amount;
                }
            }
        }

        $products[] = $product_data;
    }
}
wp_reset_postdata();
?>

<section class="shop-listing" x-data="shopApp()" x-init="init()">
    <div class="wrap">
        <div class="shop-listing-header">
            <?php if ($content_title) : ?>
                <h2 class="heading-2"><?php echo wp_kses_post($content_title); ?></h2>
            <?php endif; ?>
            <?php if ($content_text) : ?>
                <p class="body-medium"><?php echo wp_kses_post($content_text); ?></p>
            <?php endif; ?>

            <div class="shop-listing-search-row">
                <div class="shop-listing-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M14.0391 3.45502C12.3152 3.45502 10.6619 4.13984 9.44287 5.35882C8.22388 6.57781 7.53906 8.23111 7.53906 9.95502C7.53906 11.575 8.12906 13.055 9.10906 14.185L3.45906 19.835L4.16906 20.545L9.81906 14.895C10.9938 15.9031 12.4911 16.4566 14.0391 16.455C15.763 16.455 17.4163 15.7702 18.6353 14.5512C19.8542 13.3322 20.5391 11.6789 20.5391 9.95502C20.5391 8.23111 19.8542 6.57781 18.6353 5.35882C17.4163 4.13984 15.763 3.45502 14.0391 3.45502ZM14.0391 4.45502C15.4978 4.45502 16.8967 5.03448 17.9281 6.06593C18.9596 7.09738 19.5391 8.49633 19.5391 9.95502C19.5391 11.4137 18.9596 12.8127 17.9281 13.8441C16.8967 14.8756 15.4978 15.455 14.0391 15.455C13.3168 15.455 12.6016 15.3128 11.9343 15.0364C11.267 14.76 10.6607 14.3548 10.15 13.8441C9.63925 13.3334 9.23413 12.7271 8.95773 12.0598C8.68132 11.3925 8.53906 10.6773 8.53906 9.95502C8.53906 9.23275 8.68132 8.51755 8.95773 7.85026C9.23413 7.18297 9.63925 6.57665 10.15 6.06593C10.6607 5.55521 11.267 5.15008 11.9343 4.87368C12.6016 4.59728 13.3168 4.45502 14.0391 4.45502Z" fill="#6E6E6E"/>
                    </svg>
                    <input type="text" x-model="filters.search" @input.debounce.300ms="applyFilters()" placeholder="Search products...">
                </div>
                <div class="shop-listing-sort">
                    <select x-model="filters.sort" @change="applyFilters()">
                        <option value="recent">Most Recent</option>
                        <option value="name-asc">Name: A–Z</option>
                        <option value="name-desc">Name: Z–A</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="shop-listing-count">
            <span x-text="filteredProducts.length"></span> products available
        </div>

        <div class="shop-listing-grid" x-show="paginatedProducts.length > 0">
            <template x-for="product in paginatedProducts" :key="product.id">
                <a :href="product.url" class="shop-listing-card">
                    <div class="shop-listing-card-image">
                        <template x-if="product.image">
                            <img :src="product.image" :alt="product.title" loading="lazy">
                        </template>
                        <template x-if="!product.image">
                            <div class="shop-listing-card-placeholder">No Image</div>
                        </template>
                    </div>
                    <div class="shop-listing-card-content">
                        <h3 class="heading-5" x-text="product.title"></h3>
                        <template x-if="product.price">
                            <p class="body-medium shop-listing-card-price" x-text="product.price"></p>
                        </template>
                        <span class="shop-listing-card-button">
                            VIEW
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                                <path d="M1.42059e-08 6.77966L13.8418 6.77966L7.9096 0.847458L8.65537 -1.81362e-08L16 7.34463L8.65537 14.6893L7.9096 13.8418L13.8418 7.9096L1.65735e-08 7.9096L1.42059e-08 6.77966Z" fill="#000000"/>
                            </svg>
                        </span>
                    </div>
                </a>
            </template>
        </div>

        <div class="shop-listing-empty" x-show="filteredProducts.length === 0">
            <h3 class="heading-5">No products found</h3>
            <p class="body-medium">Try adjusting your search terms.</p>
        </div>

        <div class="shop-listing-pagination" x-show="totalPages > 1">
            <div class="shop-listing-pagination-nav">
                <button class="shop-listing-pagination-btn" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
                <div class="shop-listing-pagination-pages">
                    <template x-for="page in getVisiblePages()" :key="page">
                        <button class="shop-listing-pagination-page" :class="{ 'is-active': page === currentPage }" @click="goToPage(page)" x-text="page"></button>
                    </template>
                </div>
                <button class="shop-listing-pagination-btn" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages">Next</button>
            </div>
            <p class="shop-listing-pagination-info">
                Showing <span x-text="((currentPage - 1) * perPage) + 1"></span> to <span x-text="Math.min(currentPage * perPage, filteredProducts.length)"></span> of <span x-text="filteredProducts.length"></span>
            </p>
        </div>
    </div>
</section>

<script>
function shopApp() {
    return {
        allProducts: <?php echo json_encode($products); ?>,
        filteredProducts: [],
        paginatedProducts: [],
        currentPage: 1,
        perPage: 9,
        totalPages: 0,
        filters: {
            search: '',
            sort: 'recent'
        },

        init() {
            this.loadFromUrl();
            this.applyFilters();
            window.addEventListener('popstate', () => {
                this.loadFromUrl();
                this.applyFilters();
            });
        },

        loadFromUrl() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('search')) this.filters.search = params.get('search');
            if (params.get('sort')) this.filters.sort = params.get('sort');
            const page = parseInt(params.get('page'));
            if (page > 0) this.currentPage = page;
        },

        updateUrl() {
            const params = new URLSearchParams();
            if (this.filters.search) params.set('search', this.filters.search);
            if (this.filters.sort !== 'recent') params.set('sort', this.filters.sort);
            if (this.currentPage > 1) params.set('page', this.currentPage);
            const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
            window.history.pushState({ filters: JSON.parse(JSON.stringify(this.filters)), page: this.currentPage }, '', newUrl);
        },

        parsePrice(str) {
            if (!str) return 0;
            const match = str.match(/[\d,.]+/);
            return match ? parseFloat(match[0].replace(/,/g, '')) : 0;
        },

        applyFilters() {
            let results = [...this.allProducts];
            if (this.filters.search && this.filters.search.trim()) {
                const s = this.filters.search.toLowerCase().trim();
                results = results.filter(product => {
                    const fields = [product.title || '', product.excerpt || '', product.price || ''].join(' ').toLowerCase();
                    return fields.includes(s);
                });
            }
            switch (this.filters.sort) {
                case 'name-asc': results.sort((a, b) => (a.title || '').localeCompare(b.title || '')); break;
                case 'name-desc': results.sort((a, b) => (b.title || '').localeCompare(a.title || '')); break;
                case 'price-low': results.sort((a, b) => this.parsePrice(a.price) - this.parsePrice(b.price)); break;
                case 'price-high': results.sort((a, b) => this.parsePrice(b.price) - this.parsePrice(a.price)); break;
                default: break;
            }
            this.filteredProducts = results;
            this.totalPages = Math.ceil(results.length / this.perPage);
            if (this.currentPage > this.totalPages || this.currentPage < 1) this.currentPage = 1;
            this.updatePagination();
            this.updateUrl();
        },

        updatePagination() {
            const start = (this.currentPage - 1) * this.perPage;
            this.paginatedProducts = this.filteredProducts.slice(start, start + this.perPage);
        },

        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                this.updatePagination();
                this.updateUrl();
                document.querySelector('.shop-listing').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        },

        getVisiblePages() {
            const pages = [];
            const max = 5;
            if (this.totalPages <= max) {
                for (let i = 1; i <= this.totalPages; i++) pages.push(i);
            } else {
                let start = Math.max(1, this.currentPage - Math.floor(max / 2));
                let end = Math.min(this.totalPages, start + max - 1);
                if (end - start < max - 1) start = Math.max(1, end - max + 1);
                for (let i = start; i <= end; i++) pages.push(i);
            }
            return pages;
        }
    };
}
</script>

<?php get_footer(); ?>
