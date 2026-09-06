<?php
/**
 * اتصالات AI / MCP / اتوماسیون
 *
 * - REST API آماده ووکامرس (محصولات، سفارش‌ها، مشتری‌ها) برای n8n / Make / Zapier
 * - وب‌هوک سفارش جدید → سرور MCP یا هر endpoint دلخواه
 * - داده‌کاوی محصول برای ایجنت‌های فروش
 *
 * @package NexShop
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// وب‌هوک سفارش جدید به MCP / n8n / هر endpoint
add_action( 'woocommerce_new_order', function ( $order_id ) {
	$endpoint = get_option( 'nexshop_mcp_endpoint' );
	if ( ! $endpoint ) return;
	$order = wc_get_order( $order_id );
	if ( ! $order ) return;
	wp_remote_post( $endpoint, [
		'timeout' => 10,
		'headers' => [ 'Content-Type' => 'application/json' ],
		'body'    => wp_json_encode( [
			'event'     => 'order.created',
			'order_id'  => $order_id,
			'total'     => $order->get_total(),
			'currency'  => $order->get_currency(),
			'email'     => $order->get_billing_email(),
			'items'     => array_map( fn( $i ) => $i->get_name(), array_values( $order->get_items() ) ),
			'site'      => home_url(),
		], JSON_UNESCAPED_UNICODE ),
	] );
} );

// نقطه پایانی سبک برای خواندن کاتالوگ توسط ایجنت‌های AI (فقط‌خواندنی، عمومی)
add_action( 'rest_api_init', function () {
	register_rest_route( 'nexshop/v1', '/catalog', [
		'methods'             => 'GET',
		'permission_callback' => '__return_true',
		'callback'            => function () {
			$products = wc_get_products( [ 'limit' => 50, 'status' => 'publish' ] );
			return array_map( fn( $p ) => [
				'id'        => $p->get_id(),
				'name'      => $p->get_name(),
				'price'     => $p->get_price(),
				'currency'  => get_woocommerce_currency(),
				'in_stock'  => $p->is_in_stock(),
				'permalink' => $p->get_permalink(),
			], $products );
		},
	] );
} );
