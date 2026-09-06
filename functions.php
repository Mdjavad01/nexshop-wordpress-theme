<?php
/**
 * NexShop Theme Functions
 * نکس‌شاپ — قالب فروشگاهی وردپرس
 *
 * @package NexShop
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'NEXSHOP_VERSION', '1.0.0' );
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
} );

/* -------------------------------------------------------------------------
 * 2. بارگذاری استایل و اسکریپت
 * ---------------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', function () {

	// فونت وزیرمتن (فارسی) + Inter (لاتین)
	wp_enqueue_style(
		'nexshop-fonts',
		'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css',
		[], null
	);

	wp_enqueue_style( 'nexshop-main', NEXSHOP_URI . '/assets/css/main.css', [ 'nexshop-fonts' ], NEXSHOP_VERSION );
	wp_enqueue_style( 'nexshop-style', get_stylesheet_uri(), [ 'nexshop-main' ], NEXSHOP_VERSION );

	wp_enqueue_script( 'nexshop-main', NEXSHOP_URI . '/assets/js/main.js', [], NEXSHOP_VERSION, true );

	wp_localize_script( 'nexshop-main', 'nexshopData', [
		'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
		'cartUrl'  => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
		'nonce'    => wp_create_nonce( 'nexshop-nonce' ),
		'i18n'     => [
			'addedToCart' => __( 'به سبد خرید اضافه شد ✓', 'nexshop' ),
		],
	] );
} );

/* -------------------------------------------------------------------------
 * 3. سئو و آماده‌سازی برای AI/MCP
 * ---------------------------------------------------------------------- */
add_action( 'wp_head', function () {
	// اسکیما پایه Organization برای موتورهای AI و جستجو
	if ( is_front_page() ) {
		$schema = [
			'@context' => 'https://schema.org',
			'@type'    => 'Organization',
			'name'     => get_bloginfo( 'name' ),
			'url'      => home_url(),
			'logo'     => wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ),
		];
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}
	// Open Graph پایه
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:locale" content="fa_IR">' . "\n";
}, 5 );

// فیلدهای سئو + AI در هر محصول ووکامرس (meta خلاصه هوشمند)
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( ! $product ) return;
	$schema = [
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => $product->get_name(),
		'description' => wp_strip_all_tags( $product->get_short_description() ),
		'sku'         => $product->get_sku(),
		'offers'      => [
			'@type'         => 'Offer',
			'price'         => $product->get_price(),
			'priceCurrency' => get_woocommerce_currency(),
			'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
		],
	];
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
}, 99 );

/* -------------------------------------------------------------------------
 * 4. بهینه‌سازی سرعت
 * ---------------------------------------------------------------------- */
add_action( 'init', function () {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
} );

add_filter( 'wp_lazy_loading_enabled', '__return_true' );

/* -------------------------------------------------------------------------
 * 5. فایل‌های داخلی قالب
 * ---------------------------------------------------------------------- */
require_once NEXSHOP_DIR . '/inc/customizer.php';      // تنظیمات سفارشی‌سازی
require_once NEXSHOP_DIR . '/inc/admin-panel.php';     // پنل تنظیمات نکس‌شاپ
require_once NEXSHOP_DIR . '/inc/gateways.php';        // راهنما و هوک‌های درگاه پرداخت
require_once NEXSHOP_DIR . '/inc/integrations.php';    // اتصالات AI / سئو / MCP
require_once NEXSHOP_DIR . '/inc/widgets.php';         // ابزارک‌ها
