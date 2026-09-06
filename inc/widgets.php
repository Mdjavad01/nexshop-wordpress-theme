<?php
/**
 * ابزارک‌های نکس‌شاپ
 * @package NexShop
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'widgets_init', function () {
	register_sidebar( [
		'name'          => __( 'ستون فروشگاه', 'nexshop' ),
		'id'            => 'nexshop-shop',
		'before_widget' => '<div class="nx-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="nx-widget__title">',
		'after_title'   => '</h3>',
	] );
} );
