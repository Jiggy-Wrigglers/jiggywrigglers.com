<?php
/**
 * Template Name: Locations
 *
 * @package Jiggy_Wrigglers
 */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-locations/locations.css">

<!-- Hero Section -->
<!-- ------------------------------------------------- -->
<?php include locate_template( 'components/hero/index.php' ); ?>

<?php
// Content
$content_sup_title = get_field( 'content_sup_title' );
$content_title     = get_field( 'content_title' );
$content_text      = get_field( 'content_text' );

// Franchises
$franchises_repeater = get_field( 'franchises_repeater' );
?>

<!-- Content Section -->
<!-- ------------------------------------------------- -->
<section class="locations-content">
    <div class="wrap">
        <?php if ( $content_sup_title ) : ?>
            <h3 class="heading-5"><?php echo wp_kses_post( $content_sup_title ); ?></h3>
        <?php endif; ?>
        <?php if ( $content_title ) : ?>
            <h2 class="heading-2"><?php echo wp_kses_post( $content_title ); ?></h2>
        <?php endif; ?>
        <?php if ( $content_text ) : ?>
            <div class="locations-content-text"><?php echo wp_kses_post( $content_text ); ?></div>
        <?php endif; ?>
    </div>
</section>

<!-- Franchises Section -->
<!-- ------------------------------------------------- -->
<?php if ( $franchises_repeater ) : ?>
<section class="locations-franchises" x-data="locationsApp()">
    <div class="wrap">
        <div class="locations-franchises-search" @click.away="open = false">
            <p class="body-medium">Select your area to view party calendars or contact your local franchise.</p>
            <div class="locations-franchises-select">
                <button class="locations-franchises-toggle" type="button" @click="open = !open">
                    <span x-text="selected ? selected.franchise_area : 'Select Your Area'"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="locations-franchises-dropdown" x-show="open" x-transition.opacity.duration.200ms>
                    <?php foreach ( $franchises_repeater as $franchise ) : ?>
                        <button type="button"
                                @click="select(<?php echo esc_attr( wp_json_encode( array(
                                    'area'     => $franchise['franchise_area'],
                                    'email'    => $franchise['franchise_email'],
                                    'calendar' => ! empty( $franchise['franchise_calendar'] ) ? $franchise['franchise_calendar'] : '',
                                ) ) ) ?>)">
                            <?php echo esc_html( $franchise['franchise_area'] ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="locations-franchises-actions">
                <a class="button button-blue" :href="selected ? selected.calendar : '#'" :target="selected && selected.calendar ? '_blank' : '_self'" :class="{ 'is-disabled': !selected }" @click.prevent="!selected">
                    View Party Calendar
                </a>
                <a class="button button-purple" :href="selected ? 'mailto:' + selected.email : '#'" :class="{ 'is-disabled': !selected }" @click.prevent="!selected">
                    Email Franchise
                </a>
            </div>
        </div>

        <div class="locations-franchises-grid">
            <?php foreach ( $franchises_repeater as $i => $franchise ) : ?>
                <div class="locations-franchise" data-animate="fade-up" data-animate-delay="<?php echo esc_attr( ( $i % 3 ) + 1 ); ?>">
                    <h3 class="heading-5"><?php echo esc_html( $franchise['franchise_name'] ); ?></h3>
                    <p class="body-medium"><?php echo esc_html( $franchise['franchise_area'] ); ?></p>
                    <?php if ( ! empty( $franchise['franchise_email'] ) ) : ?>
                        <a class="body-medium" href="mailto:<?php echo esc_attr( $franchise['franchise_email'] ); ?>"><?php echo esc_html( $franchise['franchise_email'] ); ?></a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
function locationsApp() {
    return {
        open: false,
        selected: null,
        select(franchise) {
            this.selected = franchise;
            this.open = false;
        },
    };
}
</script>

<?php get_footer(); ?>
