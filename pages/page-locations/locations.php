<?php
/**
 * Template Name: Locations
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/pages/page-locations/locations.css?ver=<?php echo esc_attr( _S_VERSION ); ?>">

<?php
$hero_title = get_field( 'hero_title' ) ?: get_the_title();
include locate_template( 'components/hero/index.php' );

$content_sup_title = get_field( 'content_sup_title' );
$content_title = get_field( 'content_title' );
$content_text = get_field( 'content_text' );
$franchises_repeater = get_field( 'franchises_repeater' );
$franchises = array();
$location_pages = get_posts( array(
    'post_type'      => 'page',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'post_parent'   => get_queried_object_id(),
    'orderby'       => 'title',
    'order'         => 'ASC',
) );

// Keep the original ACF directory on this site once its child pages exist.
$published_location_paths = array();
foreach ( $location_pages as $location_page ) {
    $location_path = wp_parse_url( get_permalink( $location_page ), PHP_URL_PATH );
    if ( is_string( $location_path ) ) {
        $published_location_paths[ trim( $location_path, '/' ) ] = true;
    }
}
$local_location_url = static function ( $url ) use ( $published_location_paths ) {
    $parts = wp_parse_url( $url );
    if ( ! is_array( $parts ) || empty( $parts['host'] )
        || 'jiggywrigglers.com' !== strtolower( $parts['host'] )
        || ! empty( $parts['user'] ) || ! empty( $parts['pass'] ) ) {
        return $url;
    }
    $path = isset( $parts['path'] ) ? trim( $parts['path'], '/' ) : '';
    if ( ! isset( $published_location_paths[ $path ] ) ) {
        return $url;
    }
    $url = home_url( '/' . $path . '/' );
    if ( isset( $parts['query'] ) && '' !== $parts['query'] ) {
        $url .= '?' . $parts['query'];
    }
    if ( isset( $parts['fragment'] ) && '' !== $parts['fragment'] ) {
        $url .= '#' . $parts['fragment'];
    }
    return $url;
};
if ( is_string( $content_text ) && '' !== $content_text ) {
    if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
        $directory_html = new WP_HTML_Tag_Processor( $content_text );
        while ( $directory_html->next_tag( 'a' ) ) {
            $href = $directory_html->get_attribute( 'href' );
            if ( is_string( $href ) ) {
                $local_href = $local_location_url( $href );
                if ( $local_href !== $href ) {
                    $directory_html->set_attribute( 'href', $local_href );
                }
            }
        }
        $content_text = $directory_html->get_updated_html();
    } else {
        $content_text = preg_replace_callback(
            '/(<a\b[^>]*?\s)href\s*=\s*([\'\"])(.*?)\2/is',
            static function ( $match ) use ( $local_location_url ) {
                $href = html_entity_decode( $match[3], ENT_QUOTES, 'UTF-8' );
                $local_href = $local_location_url( $href );
                return $local_href === $href ? $match[0] : $match[1] . 'href=' . $match[2] . esc_url( $local_href ) . $match[2];
            },
            $content_text
        );
    }
}

// ACF link fields return arrays; only their URL belongs in a link attribute.
foreach ( is_array( $franchises_repeater ) ? $franchises_repeater : array() as $franchise ) {
    $calendar = isset( $franchise['franchise_calendar'] ) ? $franchise['franchise_calendar'] : '';
    $franchises[] = array(
        'name'     => isset( $franchise['franchise_name'] ) ? $franchise['franchise_name'] : '',
        'area'     => isset( $franchise['franchise_area'] ) ? $franchise['franchise_area'] : '',
        'email'    => sanitize_email( isset( $franchise['franchise_email'] ) ? $franchise['franchise_email'] : '' ),
        'calendar' => esc_url_raw( is_array( $calendar ) ? ( isset( $calendar['url'] ) ? $calendar['url'] : '' ) : $calendar ),
    );
}
?>

<?php if ( $content_sup_title || $content_title || $content_text ) : ?>
<section class="locations-content">
    <div class="wrap">
        <?php if ( $content_sup_title ) : ?>
            <p class="heading-5"><?php echo wp_kses_post( $content_sup_title ); ?></p>
        <?php endif; ?>
        <?php if ( $content_title ) : ?>
            <h2 class="heading-2"><?php echo wp_kses_post( $content_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $content_text ) : ?>
            <?php if ( $location_pages ) : ?>
                <details class="locations-regions">
                    <summary><?php esc_html_e( 'Browse locations by region', 'jiggywrigglers' ); ?></summary>
                    <div class="locations-content-text"><?php echo wp_kses_post( $content_text ); ?></div>
                </details>
            <?php else : ?>
                <div class="locations-content-text"><?php echo wp_kses_post( $content_text ); ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ( $franchises || $location_pages ) : ?>
<section class="locations-franchises" aria-labelledby="locations-franchises-title">
    <div class="wrap">
        <div class="locations-franchises-search">
            <h2 id="locations-franchises-title" class="heading-3"><?php esc_html_e( 'Find your local Jiggy Wrigglers', 'jiggywrigglers' ); ?></h2>
            <?php if ( $franchises ) : ?>
            <p id="locations-area-help" class="body-medium"><?php esc_html_e( 'Choose your area to view party calendars or contact your local franchise. You can also browse all areas below.', 'jiggywrigglers' ); ?></p>
            <div class="locations-franchises-select">
                <label for="locations-area"><?php esc_html_e( 'Your area', 'jiggywrigglers' ); ?></label>
                <select id="locations-area" class="locations-franchises-toggle" aria-describedby="locations-area-help">
                    <option value=""><?php esc_html_e( 'Select your area', 'jiggywrigglers' ); ?></option>
                    <?php foreach ( $franchises as $i => $franchise ) : ?>
                        <option value="<?php echo esc_attr( $i ); ?>" data-calendar="<?php echo esc_url( $franchise['calendar'] ); ?>" data-email="<?php echo esc_attr( $franchise['email'] ); ?>">
                            <?php echo esc_html( $franchise['area'] ?: $franchise['name'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="locations-franchises-actions" id="locations-selected-actions" hidden>
                <a class="button button-blue" id="locations-calendar" target="_blank" rel="noopener noreferrer" hidden><?php esc_html_e( 'View party calendar', 'jiggywrigglers' ); ?></a>
                <a class="button button-purple" id="locations-email" hidden><?php esc_html_e( 'Email franchise', 'jiggywrigglers' ); ?></a>
            </div>
            <p class="locations-selection-status" id="locations-selection-status" role="status" aria-live="polite"></p>
            <?php endif; ?>

            <div class="locations-directory-search">
                <label for="locations-search"><?php esc_html_e( 'Search by town or area', 'jiggywrigglers' ); ?></label>
                <input id="locations-search" type="search" placeholder="<?php echo esc_attr__( 'Start typing your town or area…', 'jiggywrigglers' ); ?>" aria-controls="locations-directory" autocomplete="off">
            </div>
            <p id="locations-search-status" class="body-medium" role="status" aria-live="polite"></p>
        </div>

        <div class="locations-franchises-grid" id="locations-directory">
            <?php if ( $location_pages ) : ?>
                <?php foreach ( $location_pages as $location_page ) : ?>
                    <article class="locations-franchise" data-location-search="<?php echo esc_attr( get_the_title( $location_page ) ); ?>">
                        <h3 class="heading-5"><a href="<?php echo esc_url( get_permalink( $location_page ) ); ?>"><?php echo esc_html( get_the_title( $location_page ) ); ?></a></h3>
                        <p class="body-medium"><?php esc_html_e( 'Explore local classes, venues and birthday parties.', 'jiggywrigglers' ); ?></p>
                        <a class="locations-franchise-link" href="<?php echo esc_url( get_permalink( $location_page ) ); ?>"><?php esc_html_e( 'View local classes and details', 'jiggywrigglers' ); ?><span aria-hidden="true"> →</span></a>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
            <?php foreach ( $franchises as $franchise ) : ?>
                <article class="locations-franchise" data-location-search="<?php echo esc_attr( $franchise['area'] . ' ' . $franchise['name'] ); ?>">
                    <h3 class="heading-5"><?php echo esc_html( $franchise['area'] ?: $franchise['name'] ); ?></h3>
                    <?php if ( $franchise['name'] && $franchise['name'] !== $franchise['area'] ) : ?>
                        <p class="body-medium"><?php echo esc_html( $franchise['name'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( $franchise['email'] ) : ?>
                        <a class="body-medium" href="<?php echo esc_url( 'mailto:' . $franchise['email'] ); ?>"><?php echo esc_html( $franchise['email'] ); ?></a>
                    <?php endif; ?>
                    <?php if ( $franchise['calendar'] ) : ?>
                        <a href="<?php echo esc_url( $franchise['calendar'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View party calendar', 'jiggywrigglers' ); ?></a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<script>
(function () {
    const select = document.getElementById('locations-area');
    const actions = document.getElementById('locations-selected-actions');
    const calendar = document.getElementById('locations-calendar');
    const email = document.getElementById('locations-email');
    const status = document.getElementById('locations-selection-status');
    function updateSelection() {
        const option = select.options[select.selectedIndex];
        const hasSelection = select.value !== '';
        const calendarUrl = hasSelection ? option.dataset.calendar : '';
        const emailAddress = hasSelection ? option.dataset.email : '';
        calendar.hidden = !calendarUrl;
        email.hidden = !emailAddress;
        actions.hidden = !calendarUrl && !emailAddress;
        if (calendarUrl) calendar.href = calendarUrl;
        else calendar.removeAttribute('href');
        if (emailAddress) email.href = 'mailto:' + emailAddress;
        else email.removeAttribute('href');
        status.textContent = hasSelection ? option.textContent.trim() : '';
    }
    if (select && actions && calendar && email && status) {
        select.addEventListener('change', updateSelection);
        updateSelection();
    }

    const search = document.getElementById('locations-search');
    const searchStatus = document.getElementById('locations-search-status');
    const cards = Array.from(document.querySelectorAll('#locations-directory [data-location-search]'));
    if (!search || !searchStatus) return;

    function filterLocations() {
        const query = search.value.trim().toLocaleLowerCase();
        let visible = 0;
        cards.forEach(function (card) {
            const matches = card.dataset.locationSearch.toLocaleLowerCase().includes(query);
            card.hidden = !matches;
            if (matches) visible++;
        });
        searchStatus.textContent = visible ? visible + ' / ' + cards.length : <?php echo wp_json_encode( __( 'No areas found. Try a nearby town or browse all areas by clearing your search.', 'jiggywrigglers' ) ); ?>;
    }
    search.addEventListener('input', filterLocations);
    filterLocations();
}());
</script>
<?php endif; ?>

<?php get_footer(); ?>
