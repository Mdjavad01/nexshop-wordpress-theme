<?php
/**
 * سفارشی‌ساز قالب — هیرو، فوتر، رنگ‌ها
 * @package NexShop
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', function ( WP_Customize_Manager $c ) {

	$c->add_panel( 'nexshop_panel', [
		'title'    => __( '⚙️ تنظیمات نکس‌شاپ', 'nexshop' ),
		'priority' => 10,
	] );

	// — هیرو
	$c->add_section( 'nexshop_hero', [ 'title' => __( 'بخش هیرو', 'nexshop' ), 'panel' => 'nexshop_panel' ] );

	foreach ( [
		'nexshop_hero_chip'  => [ __( 'برچسب کوچک بالای تیتر', 'nexshop' ), '⚡ فروشگاه آنلاین' ],
		'nexshop_hero_title' => [ __( 'تیتر اصلی', 'nexshop' ), '' ],
		'nexshop_hero_sub'   => [ __( 'زیرتیتر', 'nexshop' ), '' ],
	] as $id => $f ) {
		$c->add_setting( $id, [ 'default' => $f[1], 'sanitize_callback' => 'sanitize_text_field' ] );
		$c->add_control( $id, [ 'label' => $f[0], 'section' => 'nexshop_hero', 'type' => 'text' ] );
	}

	$c->add_setting( 'nexshop_hero_image', [ 'sanitize_callback' => 'esc_url_raw' ] );
	$c->add_control( new WP_Customize_Image_Control( $c, 'nexshop_hero_image', [
		'label' => __( 'تصویر هیرو', 'nexshop' ), 'section' => 'nexshop_hero',
	] ) );

	// — فوتر و اعتماد
	$c->add_section( 'nexshop_footer', [ 'title' => __( 'فوتر و اعتماد', 'nexshop' ), 'panel' => 'nexshop_panel' ] );
	foreach ( [
		'nexshop_trust_text'    => [ __( 'متن نماد اعتماد', 'nexshop' ), '' ],
		'nexshop_gateways_text' => [ __( 'نام درگاه‌های پرداخت', 'nexshop' ), 'زرین‌پال • زیبال • پی‌پینگ • Stripe • PayPal' ],
		'nexshop_contact_text'  => [ __( 'اطلاعات تماس', 'nexshop' ), '' ],
	] as $id => $f ) {
		$c->add_setting( $id, [ 'default' => $f[1], 'sanitize_callback' => 'sanitize_text_field' ] );
		$c->add_control( $id, [ 'label' => $f[0], 'section' => 'nexshop_footer', 'type' => 'text' ] );
	}

	// — رنگ اکسنت
	$c->add_section( 'nexshop_colors', [ 'title' => __( 'رنگ‌بندی', 'nexshop' ), 'panel' => 'nexshop_panel' ] );
	$c->add_setting( 'nexshop_accent', [ 'default' => '#C8F169', 'sanitize_callback' => 'sanitize_hex_color' ] );
	$c->add_control( new WP_Customize_Color_Control( $c, 'nexshop_accent', [
		'label' => __( 'رنگ اکسنت (دکمه‌ها و لینک‌ها)', 'nexshop' ), 'section' => 'nexshop_colors',
	] ) );
} );

// تزریق رنگ اکسنت انتخاب‌شده به صورت CSS variable
add_action( 'wp_head', function () {
	$accent = get_theme_mod( 'nexshop_accent', '#C8F169' );
	echo '<style>:root{--nx-accent:' . esc_attr( $accent ) . '}</style>';
}, 20 );
