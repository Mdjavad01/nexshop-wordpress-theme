<?php
/**
 * NexShop Theme Functions
 * نکس‌شاپ — قالب فروشگاهی وردپرس (فول المنتور + دارک/لایت مود)
 *
 * @package NexShop
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'NEXSHOP_VERSION', '2.0.0' );
define( 'NEXSHOP_DIR', get_template_directory() );
define( 'NEXSHOP_URI', get_template_directory_uri() );

/* -------------------------------------------------------------------------
 * 1. راه‌اندازی قالب
 * ---------------------------------------------------------------------- */
add_action( 'after_setup_theme', function () {

        load_theme_textdomain( 'nexshop', NEXSHOP_DIR . '/languages' );

        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'align-wide' );
        add_theme_support( 'wp-block-styles' );
        add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

        add_theme_support( 'custom-logo', [
                'height'      => 80,
                'width'       => 200,
                'flex-height' => true,
                'flex-width'  => true,
        ] );

        // پشتیبانی کامل ووکامرس
        add_theme_support( 'woocommerce' );
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );

        register_nav_menus( [
                'primary' => __( 'منوی اصلی', 'nexshop' ),
                'footer'  => __( 'منوی فوتر', 'nexshop' ),
        ] );

        // سایزهای تصویر فروشگاهی
        add_image_size( 'nexshop-hero',   1920, 900, true );
        add_image_size( 'nexshop-card',    600, 600, true );
        add_image_size( 'nexshop-banner', 1200, 500, true );

        // عرض محتوا برای oEmbed و بلوک‌ها
        if ( ! isset( $GLOBALS['content_width'] ) ) {
                $GLOBALS['content_width'] = (int) apply_filters( 'nexshop_content_width', 860 );
        }

        // استایل ادیتور وردپرس هماهنگ با ظاهر قالب
        add_editor_style( 'assets/css/editor-style.css' );
} );

/* -------------------------------------------------------------------------
 * 2. بارگذاری استایل و اسکریپت
 *    فونت وزیرمتن به‌صورت محلی (خودمیزبان) بارگذاری می‌شود؛ هیچ درخواست
 *    خارجی (CDN) برای فونت وجود ندارد.
 * ---------------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', function () {

        wp_enqueue_style( 'nexshop-main', NEXSHOP_URI . '/assets/css/main.css', [], NEXSHOP_VERSION );
        wp_enqueue_style( 'nexshop-style', get_stylesheet_uri(), [ 'nexshop-main' ], NEXSHOP_VERSION );

        // رنگ اکسنت به‌صورت CSS inline متصل به استایل اصلی (قابل کش و بدون تگ جداگانه)
        // نکته: برای هر دو حالت دارک/لایت تزریق می‌شود چون سلکتور data-theme="light" اولویت بیشتری دارد.
        $accent = sanitize_hex_color( get_theme_mod( 'nexshop_accent', '#C8F169' ) );
        if ( $accent && strtoupper( $accent ) !== '#C8F169' ) {
                wp_add_inline_style( 'nexshop-main',
                        ':root{--nx-accent:' . $accent . '}:root[data-theme="light"]{--nx-accent:' . $accent . '}'
                );
        }

        wp_enqueue_script( 'nexshop-main', NEXSHOP_URI . '/assets/js/main.js', [], NEXSHOP_VERSION, true );
        wp_enqueue_script( 'nexshop-dark-mode', NEXSHOP_URI . '/assets/js/dark-mode.js', [], NEXSHOP_VERSION, true );
} );

/**
 * اسکریپت بوت دارک/لایت — قبل از اولین رنگ‌آمیزی صفحه اجرا می‌شود
 * (اولویت 0 یعنی قبل از چاپ استایل‌ها) تا هیچ‌وقت فلش دارک→لایت نداشته باشیم.
 *
 * ترتیب اولویت حالت رنگی:
 *   1. انتخاب دستی کاربر (localStorage)
 *   2. حالت پیش‌فرض انتخابی مدیر در سفارشی‌ساز (خودکار = تشخیص سیستم)
 */
add_action( 'wp_head', function () {
        $scheme = get_theme_mod( 'nexshop_default_scheme', 'auto' );
        if ( $scheme === 'dark' ) {
                $default = '"dark"';
        } elseif ( $scheme === 'light' ) {
                $default = '"light"';
        } else { // خودکار: تشخیص سیستم کاربر
                $default = '(window.matchMedia&&matchMedia("(prefers-color-scheme: light)").matches?"light":"dark")';
        }
        echo '<script>(function(){var d=' . $default . ';try{var s=localStorage.getItem("nexshop_scheme");if(s!=="light"&&s!=="dark")s=d;document.documentElement.setAttribute("data-theme",s);}catch(e){document.documentElement.setAttribute("data-theme",d||"dark");}})();</script>' . "\n";
}, 0 );

// پیش‌بارگذاری فونت اصلی + علامت‌گذاری وجود JS (برای انیمیشن‌های امن)
add_action( 'wp_head', function () {
        printf(
                '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
                esc_url( NEXSHOP_URI . '/assets/fonts/Vazirmatn-Variable.woff2' )
        );
        echo '<script>document.documentElement.classList.add("nx-js");</script>' . "\n";
}, 1 );

/* -------------------------------------------------------------------------
 * 3. سئو (فقط وقتی افزونهٔ سئو فعال نیست)
 * ---------------------------------------------------------------------- */

/**
 * آیا افزونهٔ سئوی معتبری فعال است؟ (جلوگیری از اسکیما و OG تکراری)
 */
function nexshop_has_seo_plugin() {
        return defined( 'WPSEO_VERSION' )          // Yoast
                || class_exists( 'RankMath' )          // Rank Math
                || defined( 'AIOSEO_VERSION' )         // All in One SEO
                || defined( 'SEOPRESS_VERSION' )       // SEOPress
                || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

add_action( 'wp_head', function () {
        if ( nexshop_has_seo_plugin() ) return;

        // اسکیما Organization فقط در صفحهٔ اصلی
        if ( is_front_page() ) {
                $schema = [
                        '@context' => 'https://schema.org',
                        '@type'    => 'Organization',
                        'name'     => get_bloginfo( 'name' ),
                        'url'      => home_url(),
                ];
                $logo = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
                if ( $logo ) $schema['logo'] = $logo;
                echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
        }

        // Open Graph پایه
        $title = is_singular() ? single_post_title( '', false ) : get_bloginfo( 'name' );
        $url   = is_singular() ? get_permalink() : home_url( '/' );
        echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
        echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
        echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
        if ( is_singular() && has_post_thumbnail() ) {
                echo '<meta property="og:image" content="' . esc_url( get_the_post_thumbnail_url( null, 'nexshop-banner' ) ) . '">' . "\n";
        }
}, 5 );

// نکته: اسکیمای Product را خود ووکامرس چاپ می‌کند؛ قالب دیگر نسخهٔ تکراری اضافه نمی‌کند.

/* -------------------------------------------------------------------------
 * 4. بهینه‌سازی سرعت
 * ---------------------------------------------------------------------- */
add_action( 'init', function () {
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'rsd_link' );
} );

/**
 * خروجی HTML یک شورت‌کد را کش می‌کند (مثلاً بخش‌های صفحهٔ اصلی).
 * با تغییر محصول، ثبت سفارش یا اجرای فروش‌های زمان‌بندی‌شده، کش پاک می‌شود.
 */
function nexshop_cached_shortcode( $key, $shortcode, $ttl = 6 * HOUR_IN_SECONDS ) {
        $key  = 'nexshop_sc_' . md5( $key . $shortcode . get_locale() );
        $html = get_transient( $key );
        if ( false === $html ) {
                $html = do_shortcode( $shortcode );
                set_transient( $key, $html, $ttl );
                nexshop_remember_transient( $key );
        }
        return $html;
}

function nexshop_flush_shortcode_cache() {
        foreach ( (array) get_option( 'nexshop_sc_keys', [] ) as $key ) {
                delete_transient( $key );
        }
        delete_option( 'nexshop_sc_keys' );
}
add_action( 'save_post_product', 'nexshop_flush_shortcode_cache' );
add_action( 'woocommerce_update_product', 'nexshop_flush_shortcode_cache' );
add_action( 'woocommerce_new_order', 'nexshop_flush_shortcode_cache' );      // تغییر پرفروش‌ها
add_action( 'woocommerce_order_status_changed', 'nexshop_flush_shortcode_cache' );
add_action( 'created_product_cat', 'nexshop_flush_shortcode_cache' );
add_action( 'edited_product_cat', 'nexshop_flush_shortcode_cache' );
add_action( 'woocommerce_scheduled_sales', 'nexshop_flush_shortcode_cache' ); // شروع/پایان فروش‌های زمان‌بندی‌شده → قیمت‌ها همیشه تازه
add_action( 'switch_theme', 'nexshop_flush_shortcode_cache' );

// به‌روزرسانی تعداد سبد خرید در هدر بعد از افزودن به سبد (AJAX)
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
        ob_start();
        echo '<span class="nx-cart-count">' . esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) . '</span>';
        $fragments['span.nx-cart-count'] = ob_get_clean();
        return $fragments;
} );

/* -------------------------------------------------------------------------
 * 5. فایل‌های داخلی قالب
 * ---------------------------------------------------------------------- */
require_once NEXSHOP_DIR . '/inc/customizer.php';      // تنظیمات سفارشی‌سازی (+ حالت رنگی پیش‌فرض)
require_once NEXSHOP_DIR . '/inc/elementor.php';       // یکپارچگی کامل المنتور (Theme Builder + ویجت‌ها)
require_once NEXSHOP_DIR . '/inc/admin-panel.php';     // پنل تنظیمات نکس‌شاپ
require_once NEXSHOP_DIR . '/inc/gateways.php';        // راهنما و هوک‌های درگاه پرداخت
require_once NEXSHOP_DIR . '/inc/integrations.php';    // اتصالات AI / سئو / MCP (+ وب‌هوک با retry)
require_once NEXSHOP_DIR . '/inc/widgets.php';         // ابزارک‌ها
