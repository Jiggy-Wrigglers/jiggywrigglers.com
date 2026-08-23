# Jiggy Wrigglers WordPress Theme

A modern, clean, performant, and secure WordPress theme built with a **component-based architecture** that aligns with contemporary frameworks like **Laravel** and **Next.js**, not the WordPress block editor.

## Why This Approach Over Block-Led Themes?

Most modern WordPress themes (like those using ACF Blocks) rely heavily on the WordPress block editor (Gutenberg) for content management. While this works for content editors, it creates several issues for developers:

- **Lock-in to WordPress admin** - Components are tied to the block editor UI
- **Limited portability** - Blocks can't easily be reused outside WordPress
- **Performance overhead** - Block editor adds significant bloat
- **Developer experience** - Building blocks requires React knowledge and WordPress-specific tooling

**This theme takes a different approach**: one that will feel familiar if you've worked with Laravel Blade components or Next.js file-based routing:

| Feature | Block-Led Themes | This Theme |
|---------|------------------|------------|
| **Architecture** | Tied to Gutenberg | Framework-agnostic components |
| **Templating** | Block editor UI | PHP includes with args |
| **Styling** | Inline styles or global CSS | Component-scoped CSS |
| **JavaScript** | React-based blocks | Vanilla JS or Alpine.js |
| **Portability** | WordPress-only | Concepts transfer to any framework |
| **Learning curve** | WordPress-specific | Standard web development |

### Laravel/Next.js Developers Will Feel at Home

If you're coming from Laravel or Next.js, this architecture will feel immediately familiar:

**Laravel Blade Components:**
```blade
<x-hero title="Welcome" :background="$image" />
```

**This Theme:**
```php
<?php
$args = array(
    'title' => 'Welcome',
    'background' => $image,
);
include get_template_directory() . '/components/hero/index.php';
?>
```

**Next.js Components:**
```jsx
<Hero title="Welcome" background={image} />
```

All three follow the same principle: **self-contained components with props/arguments**.

### Key Architectural Benefits

1. **Component Isolation** - Each component manages its own CSS, JS, and PHP in a single folder
2. **Explicit Dependencies** - Components load their own assets; no global enqueue confusion
3. **File-Based Discovery** - Page templates are automatically discovered from the `/pages` directory
4. **No Build Step Required** - Works without Webpack, Vite, or complex tooling
5. **Framework-Agnostic Skills** - Patterns learned here transfer directly to Laravel, Next.js, or any modern framework

## About Jiggy Wrigglers

At Jiggy Wrigglers, we're more than just a marketing agency; We are a team driven by core values that directly enhance our client relationships and project outcomes. Our foundational values of **Respect**, **Collaboration**, **Drive**, and of course **Fun** are integral to delivering exceptional results.

Founded in 2019, with just a team of two, Jiggy Wrigglers has evolved to encompass four key departments - **Design**, **Social**, **Digital**, and **Video** - and now has a team of over twenty.

## JavaScript Libraries

The theme loads two libraries globally via `wp_enqueue_script` with `defer`, available on every page:

- **[Alpine.js](https://alpinejs.dev/)** - Lightweight reactive framework for dropdowns, modals, tabs, and simple interactivity
- **[Splide.js](https://splidejs.com/)** - Flexible, accessible slider/carousel for hero sliders, testimonials, and galleries

Use `[x-cloak]` on Alpine elements to prevent flash of unstyled content. The required `[x-cloak] { display: none !important; }` style is included in `<head>` via `header.php`.

## Theme Structure

```
jiggywrigglers/
├── components/                 # Reusable UI components
│   └── hero/
│       ├── index.php
│       ├── style.css
│       └── index.js
├── css/                        # Global styles (enqueued in functions.php)
│   ├── header.css
│   └── footer.css
├── pages/                      # Page templates (auto-discovered)
│   ├── page-home/              # Single page: Home
│   ├── page-news/              # Single page: News listing
│   ├── page-shop/              # Single page: Shop listing (SureCart products)
│   ├── template-article/       # Reusable template: Article grid
│   ├── template-product/       # SureCart single product wrapper
│   └── template-policy/        # Reusable template: Policy pages
├── js/                         # Global JavaScript
│   ├── navigation.js
│   ├── customizer.js
│   └── index.js                # Lenis smooth scroll, parallax, focus trap
├── inc/                        # WordPress includes
│   ├── customizer.php
│   ├── template-functions.php
│   ├── template-tags.php
│   └── jetpack.php
├── functions/                  # Theme modules
│   ├── duplication.php
│   ├── remove-menus.php
│   ├── surerank.php
│   └── custom-functions.php
├── languages/                  # Translation files
├── style.css                   # Theme header, variables, global styles
├── functions.php               # Setup, enqueues, security
├── header.php                  # <head> and <body> open
├── footer.php                  </body> and </html> close
├── index.php                   # Main template fallback
├── single.php                  # Single post template
├── archive.php                 # Archive template
├── search.php                  # Search results template
└── 404.php                     # 404 template
```

## Development Guidelines

### Page Templates

Page templates live in the `pages/` directory and are automatically discovered by scanning for `Template Name:` headers. Use a consistent naming convention:

| Prefix | Purpose | Example | Usage |
|--------|---------|---------|-------|
| `page-` | **One-page template**: used on a single specific page | `page-home/`, `page-news/`, `page-contact/` | One page only |
| `template-` | **Reusable template**: used on multiple pages sharing a layout | `template-article/`, `template-policy/` | Multiple pages |

**`page-` = one page. `template-` = many pages.**

Each template folder contains three files:

```
pages/
└── page-home/
    ├── home.php        # Template (requires Template Name header)
    ├── home.css        # Page styles
    └── home.js         # Page JavaScript
```

The PHP file **must** include a `Template Name:` header for WordPress to discover it:

```php
<?php
/**
 * Template Name: Home
 */

get_header();
?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/pages/page-home/home.css">
<script defer src="<?php echo get_template_directory_uri(); ?>/pages/page-home/home.js"></script>

<?php include get_template_directory() . '/components/hero/index.php'; ?>

<?php get_footer(); ?>
```

#### Current Templates

| Folder | Template Name | Type | Description |
|--------|--------------|------|-------------|
| `page-about/` | About | Single page | About page: hero, introduction, journey, awards, content |
| `page-contact/` | Contact | Single page | Contact page: hero, form shortcode, contact content |
| `page-home/` | Home | Single page | Homepage: banner slider, introduction, groups, content bands, testimonials |
| `page-jiggy-videos/` | Jiggy Videos | Single page | Video embeds page with YouTube CTA |
| `page-locations/` | Locations | Single page | Franchise locations with area selector (calendar / email) |
| `page-news/` | News | Single page | News listing using the default WP query loop with pagination |
| `page-shop/` | Shop | Single page | SureCart product listing with client-side search, sorting, and pagination |
| `page-thank-you/` | Thank You | Single page | Post-submission thank-you page |
| `template-article/` | Article | Reusable | Article grid using a custom `WP_Query` with pagination. Assign to any page that needs a post listing |
| `template-policy/` | Policy | Reusable | Static policy content layout. Assign to any policy page (privacy, terms, cookies) |
| `template-product/` | n/a | Auto | SureCart single product wrapper. Not selectable; loaded via `functions/surecart.php` for any `sc_product` single |
| `template-programmes/` | Programmes | Reusable | Class programme pages: hero, key info boxes, content |

### Components

Components are reusable UI elements included across multiple pages. Each component is self-contained:

```
components/
└── your-component/
    ├── index.php       # Component template
    ├── style.css       # Component styles
    └── index.js        # Component JavaScript
```

#### Including a Component

```php
<?php include get_template_directory() . '/components/hero/index.php'; ?>
```

#### Passing Arguments

```php
<?php
$args = array(
    'title' => 'Custom Title',
    'subtitle' => 'Custom subtitle text',
    'background' => get_template_directory_uri() . '/images/hero-bg.jpg',
);
include get_template_directory() . '/components/hero/index.php';
?>
```

Components access arguments via the `$args` array, falling back to ACF fields when no arguments are passed.

### Styles

- **`style.css`** - CSS custom properties and global styles (enqueued by WordPress automatically)
- **`css/header.css`** and **`css/footer.css`** - Global layout styles (enqueued in `functions.php`)
- **`components/*/style.css`** - Component-specific styles (loaded by the component)
- **`pages/*/*.css`** - Page-specific styles (loaded by the page template)

### JavaScript

- **Alpine.js** and **Splide.js** - Loaded globally via `wp_enqueue_script` with `defer` in `<head>` (handled in `functions.php`)
- **`components/*/index.js`** - Component-specific JavaScript (loaded by the component)
- **`pages/*/*.js`** - Page-specific JavaScript (loaded by the page template, use `defer`)

### Includes

| File | Purpose |
|------|---------|
| `inc/customizer.php` | WordPress Customiser integration |
| `inc/template-functions.php` | Body classes and theme hooks |
| `inc/template-tags.php` | Template tags (posted on, posted by, entry footer, thumbnails) |
| `inc/jetpack.php` | Jetpack compatibility (loaded only when Jetpack is active) |

### Functions

| File | Purpose |
|------|---------|
| `functions/duplication.php` | Post/page duplication for all public post types |
| `functions/remove-menus.php` | Admin menu cleanup and Customiser streamlining |
| `functions/custom-functions.php` | Add your own custom functions here |
| `functions/surerank.php` | SureRank SEO analyser ACF bridge: auto-detects headings from templates |
| `functions/surecart.php` | SureCart integration: routes `sc_product` singles to `pages/template-product/product.php`, enables block-template-parts support |

## Features

### Security

- **File editing disabled** via `DISALLOW_FILE_EDIT`: only defines the constant if not already set in `wp-config.php`
- **Security headers** on every request type: frontend, admin, login, and REST API (X-Content-Type-Options, X-Frame-Options, Referrer-Policy)
- **WordPress version hidden** from `<head>` output
- **Pingbacks disabled** to prevent DDoS via XML-RPC
- **Comments disabled** completely across all post types (supports, frontend, and counts)
- **Gutenberg disabled**: classic editor only, editor area hidden (designed for ACF-driven content)

### Performance

- **Alpine.js and Splide.js** loaded via `wp_enqueue_script` with `defer` for non-blocking rendering
- **Block library CSS** dequeued for non-logged-in visitors
- **Minimal dependencies**: no build step, no bundler, no React runtime

### Post/Page Duplication

Duplicate any post, page, or custom post type from two locations:

- **Row actions**: hover over any item in the list table and click "Duplicate"
- **Edit screen**: "Duplicate this" button in the Publish meta box

The duplicate opens as a draft with a success notice. Copies taxonomies, post meta, and featured image. All public post types are supported automatically. Two filters are available:

- `jiggy_wrigglers_duplicable_post_types`: exclude specific post types
- `jiggy_wrigglers_duplicate_skip_meta_keys`: control which meta keys are skipped

### Admin Cleanup

 - Comments and Tools menus removed
 - Appearance submenus trimmed (Header, Background, Widgets, Pattern Editor)
 - Customiser sections removed (Colors, Background Image, Static Front Page, Custom CSS, Header Image)
 - Login page logo links to `jiggywrigglers.co.uk`

### SureRank SEO Compatibility

This theme includes a deep integration with [SureRank](https://surerank.com/) (`functions/surerank.php`) that makes the SEO analyzer fully compatible with ACF-driven content.

**The problem:** SureRank's page analyser reads from `$post->post_content` by default. Since this theme stores all content in ACF fields and never calls `the_content()`, the analyser sees an empty string, so headings, images, links, and keyword density checks all fail.

**The solution:** The `surerank_post_analyzer_content` filter is used to feed ACF field content into the analyser as proper HTML markup. This happens automatically with zero configuration:

1. **Template scanning**: when the SEO panel opens, the bridge resolves which PHP template serves the current post, follows `locate_template()` and `include` paths to discover all component files
2. **Auto heading detection**: each template file is scanned line-by-line for `<h1>` to `<h6>` tags wrapping ACF field variables. The bridge builds a `field_name` to `heading_tag` map from the actual markup with no manual configuration needed
3. **Sub-field detection**: repeater sub-fields are mapped by scanning `get_sub_field()` calls and finding their wrapping heading tags
4. **Field type handling**: all ACF field types are converted to proper HTML:
   - `wysiwyg` → passed through as-is (already HTML)
   - `image` / `gallery` → `<img src="" alt="" />`
   - `link` → `<a href="">title</a>`
   - `text` / `textarea` → wrapped in `<p>` (or the auto-detected heading tag)
   - `repeater` → each row walked recursively
5. **Caching**: results are cached in a transient for 1 hour so templates are only parsed once per analysis cycle

**Example of auto-detection in action:**

Given this template markup:
```php
<?php $hero_title = get_field('hero_title'); ?>
<h1><?php echo wp_kses_post($hero_title); ?></h1>
```

The bridge automatically detects that `hero_title` renders inside an `<h1>` tag and feeds it to SureRank as `<h1>Your Title</h1>` with no manual mapping required.

**Filters for overrides:**

| Filter | Purpose |
|--------|---------|
| `jiggy_wrigglers_surerank_heading_map` | Override auto-detected top-level field → heading mappings |
| `jiggy_wrigglers_surerank_sub_heading_map` | Override auto-detected repeater sub-field → heading mappings |

## Coding Standards

- **Clean Architecture** - Separation of concerns with dedicated folders
- **Performance Optimised** - Minimal dependencies, deferred scripts, dequeued bloat
- **Security Conscious** - Proper escaping, sanitisation, and nonce verification
- **WordPress Standards** - Uses WordPress APIs (`get_post_meta`, `wp_enqueue_script`, `wp_verify_nonce`) over raw SQL and hardcoded tags
- **PHP 7.4+** required

### PHPCS Configuration

```bash
vendor/bin/phpcs
vendor/bin/phpcbf
```

## Translation

The theme uses the `jiggywrigglers` text domain:

- Translation files are in `/languages/`
- Use `esc_html__()`, `esc_html_e()`, etc. with `'jiggywrigglers'`
- Generate POT file: `wp i18n make-pot . languages/jiggywrigglers.pot`