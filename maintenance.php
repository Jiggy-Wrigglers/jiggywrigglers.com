<?php
/**
 * Custom Maintenance Page
 *
 * Activated via the ACF option: maintenance_mode (True/False).
 * Only shown to non-logged-in visitors. Logged-in users see the live site.
 *
 * Optional ACF fields (defaults provided if empty):
 *   maintenance_title  - Text   (default: "We'll be back soon")
 *   maintenance_text   - Textarea (default: standard message)
 */
header( 'Retry-After: 3600' );

$maintenance_title = get_field( 'maintenance_title', 'option' ) ?: "We'll be back soon";
$maintenance_text  = get_field( 'maintenance_text', 'option' ) ?: 'Jiggy Wrigglers is currently undergoing scheduled maintenance. Please check back shortly.';
$maintenance_logo  = get_field( 'logo_white', 'option' );
$maintenance_image = get_field( 'maintenance_image', 'option' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Maintenance | Jiggy Wrigglers</title>
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #000;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .maintenance-container img.maintenance-image {
            display: flex;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            opacity: 0.6;
        }

        .maintenance-container {
            max-width: 520px;
            text-align: center;
        }

        .maintenance-logo {
            max-width: 200px;
            width: 100%;
            height: auto;
            margin-bottom: 40px;
            filter: brightness(0) invert(1);
        }

        .maintenance-title {
            font-family: 'M PLUS Rounded 1c', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: clamp(24px, 5vw, 34px);
            font-weight: 600;
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .maintenance-text {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <?php if ( $maintenance_image ) : ?>
            <?php echo wp_get_attachment_image( $maintenance_image['ID'], 'full', false, array( 'class' => 'maintenance-image' ) ); ?>
        <?php endif; ?>
        <?php if ( $maintenance_logo ) : ?>
            <?php echo wp_get_attachment_image( $maintenance_logo['ID'], 'full', false, array( 'class' => 'maintenance-logo' ) ); ?>
        <?php endif; ?>
        <h1 class="maintenance-title"><?php echo wp_kses_post( $maintenance_title ); ?></h1>
        <p class="maintenance-text"><?php echo wp_kses_post( $maintenance_text ); ?></p>
    </div>
</body>
</html>
