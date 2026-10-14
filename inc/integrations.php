<?php
/**
 * اتصالات AI / MCP / اتوماسیون
 *
 * - REST API کاتالوگ محصولات (فقط‌خواندنی، محافظت‌شده با توکن) برای n8n / Make / Zapier / ایجنت‌های AI
 * - وب‌هوک سفارش جدید (امضاشده با HMAC-SHA256) → سرور MCP یا هر endpoint دلخواه
 *
 * @package NexShop
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* -------------------------------------------------------------------------
 * کمکی‌ها
 * ---------------------------------------------------------------------- */

/** تولید توکن/راز امن در صورت نبود */
function nexshop_ensure_secrets() {
        foreach ( [ 'nexshop_api_token', 'nexshop_webhook_secret' ] as $opt ) {
                if ( ! get_option( $opt ) ) {
                        update_option( $opt, wp_generate_password( 40, false, false ), false );
                }
        }
}
add_action( 'after_switch_theme', 'nexshop_ensure_secrets' );
add_action( 'admin_init', 'nexshop_ensure_secrets' );

/** پیکربندی AI برای استفادهٔ افزونه‌ها/اسکریپت‌ها (قابل فیلتر) */
function nexshop_get_ai_config() {
        return apply_filters( 'nexshop_ai_config', [
                'endpoint' => get_option( 'nexshop_ai_endpoint', '' ),
                'api_key'  => get_option( 'nexshop_ai_api_key', '' ),
        ] );
}

/** ثبت کلید ترنزینت برای پاک‌سازی همزمان با کش شورت‌کدها */
function nexshop_remember_transient( $key ) {
        $keys   = (array) get_option( 'nexshop_sc_keys', [] );
        $keys[] = $key;
        update_option( 'nexshop_sc_keys', array_values( array_unique( $keys ) ), false );
}

/* -------------------------------------------------------------------------
 * وب‌هوک سفارش جدید
 *   - بعد از تکمیل ثبت سفارش در تسویه‌حساب (کلاسیک و Blocks) ارسال می‌شود
 *   - امضا: هدر X-NexShop-Signature = sha256=HMAC(body, secret)
 *   - فقط پس از پاسخ موفق (2xx) به‌عنوان «ارسال‌شده» علامت‌گذاری می‌شود
 *   - در شکست، تا ۳ بار با فاصلهٔ افزایشی تلاش مجدد می‌شود (Retry Queue)
 *   - ایمیل مشتری به‌طور پیش‌فرض ارسال نمی‌شود (فیلتر nexshop_webhook_payload)
 * ---------------------------------------------------------------------- */
function nexshop_send_order_webhook( $order, $is_retry = false ) {
        if ( ! $order instanceof WC_Order ) return;

        $endpoint = get_option( 'nexshop_mcp_endpoint' );
        $secret   = get_option( 'nexshop_webhook_secret' );
        if ( ! $endpoint || ! $secret ) return;

        $already_sent = (bool) $order->get_meta( '_nexshop_webhook_sent' );
        if ( $already_sent && ! $is_retry ) return;      // جلوگیری از ارسال تکراری
        if ( $already_sent ) return;                     // تلاش مجدد لازم نیست

        $failures = (int) $order->get_meta( '_nexshop_webhook_failures' );
        if ( $failures >= 3 ) return;                    // سقف تلاش‌ها تمام شده

        $payload = apply_filters( 'nexshop_webhook_payload', [
                'event'    => 'order.created',
                'order_id' => $order->get_id(),
                'total'    => $order->get_total(),
                'currency' => $order->get_currency(),
                'items'    => array_map( fn( $i ) => $i->get_name(), array_values( $order->get_items() ) ),
                'site'     => home_url(),
        ], $order );

        $body = wp_json_encode( $payload, JSON_UNESCAPED_UNICODE );

        // blocking = true با تایم‌اوت کوتاه تا بتوانیم پاسخ را بررسی و Retry واقعی داشته باشیم
        // (رفتار مشابه وب‌هوک‌های هستهٔ ووکامرس؛ با فیلتر زیر قابل غیرفعال‌کردن است)
        $response = wp_safe_remote_post( $endpoint, [
                'timeout'  => 3,
                'blocking' => (bool) apply_filters( 'nexshop_webhook_blocking', true ),
                'headers'  => [
                        'Content-Type'        => 'application/json',
                        'X-NexShop-Event'     => 'order.created',
                        'X-NexShop-Signature' => 'sha256=' . hash_hmac( 'sha256', $body, $secret ),
                ],
                'body'     => $body,
        ] );

        // در حالت غیرمسدودکننده، پاسخی برای بررسی نیست → صبر برای Retry زمان‌بندی‌شده
        $code = is_wp_error( $response ) ? 0 : (int) wp_remote_retrieve_response_code( $response );
        $ok   = $code >= 200 && $code < 300;

        if ( $ok ) {
                $order->update_meta_data( '_nexshop_webhook_sent', time() );
                $order->delete_meta_data( '_nexshop_webhook_failures' );
                $order->add_order_note( __( 'وب‌هوک نکس‌شاپ با موفقیت ارسال شد.', 'nexshop' ) );
        } else {
                $failures++;
                $order->update_meta_data( '_nexshop_webhook_failures', $failures );
                if ( $failures <= 3 ) {
                        // تلاش مجدد با فاصلهٔ افزایشی: ۲، ۵ و ۱۵ دقیقه
                        $delay = [ 2, 5, 15 ][ $failures - 1 ] * MINUTE_IN_SECONDS;
                        wp_schedule_single_event( time() + $delay, 'nexshop_webhook_retry', [ $order->get_id() ] );
                        $order->add_order_note( sprintf(
                                /* translators: %d: attempt number */
                                __( 'ارسال وب‌هوک نکس‌شاپ ناموفق بود (کد %d)؛ تلاش مجدد زمان‌بندی شد.', 'nexshop' ),
                                $code
                        ) );
                } else {
                        $order->add_order_note( __( 'وب‌هوک نکس‌شاپ پس از ۳ تلاش ارسال نشد؛ بررسی کنید.', 'nexshop' ) );
                }
        }

        $order->save_meta_data();
}

add_action( 'nexshop_webhook_retry', function ( $order_id ) {
        if ( ! function_exists( 'wc_get_order' ) ) return;
        $order = wc_get_order( $order_id );
        if ( $order ) nexshop_send_order_webhook( $order, true );
} );

add_action( 'woocommerce_checkout_order_processed', function ( $order_id ) {
        if ( function_exists( 'wc_get_order' ) ) nexshop_send_order_webhook( wc_get_order( $order_id ) );
}, 20 );
add_action( 'woocommerce_store_api_checkout_order_processed', 'nexshop_send_order_webhook', 20 );

/* -------------------------------------------------------------------------
 * REST: کاتالوگ محصولات برای ایجنت‌های AI (فقط‌خواندنی، نیازمند توکن)
 *   GET /wp-json/nexshop/v1/catalog?per_page=50&page=1
 *   هدر: X-NexShop-Token: <token>   یا   Authorization: Bearer <token>
 *   (کاربر واردشده با دسترسی manage_woocommerce هم مجاز است)
 * ---------------------------------------------------------------------- */
function nexshop_catalog_permission( WP_REST_Request $request ) {
        if ( current_user_can( 'manage_woocommerce' ) ) return true;

        $token = get_option( 'nexshop_api_token' );
        if ( ! $token ) {
                return new WP_Error( 'nexshop_disabled', __( 'کاتالوگ API فعال نیست.', 'nexshop' ), [ 'status' => 403 ] );
        }

        $given = (string) $request->get_header( 'x-nexshop-token' );
        if ( ! $given ) {
                $auth = (string) $request->get_header( 'authorization' );
                if ( stripos( $auth, 'Bearer ' ) === 0 ) $given = trim( substr( $auth, 7 ) );
        }

        if ( $given && hash_equals( $token, $given ) ) return true;

        return new WP_Error( 'nexshop_forbidden', __( 'توکن نامعتبر است.', 'nexshop' ), [ 'status' => 401 ] );
}

add_action( 'rest_api_init', function () {
        if ( ! class_exists( 'WooCommerce' ) ) return;

        register_rest_route( 'nexshop/v1', '/catalog', [
                'methods'             => 'GET',
                'permission_callback' => 'nexshop_catalog_permission',
                'args'                => [
                        'per_page' => [ 'default' => 50, 'sanitize_callback' => 'absint' ],
                        'page'     => [ 'default' => 1,  'sanitize_callback' => 'absint' ],
                ],
                'callback'            => function ( WP_REST_Request $request ) {
                        $per_page = min( 100, max( 1, (int) $request['per_page'] ) );
                        $page     = max( 1, (int) $request['page'] );

                        $cache_key = 'nexshop_catalog_' . $per_page . '_' . $page;
                        $data      = get_transient( $cache_key );

                        if ( false === $data ) {
                                $products = wc_get_products( [
                                        'limit'  => $per_page,
                                        'page'   => $page,
                                        'status' => 'publish',
                                ] );
                                $data = array_map( fn( $p ) => [
                                        'id'        => $p->get_id(),
                                        'name'      => $p->get_name(),
                                        'price'     => $p->get_price(),
                                        'currency'  => get_woocommerce_currency(),
                                        'in_stock'  => $p->is_in_stock(),
                                        'permalink' => $p->get_permalink(),
                                ], $products );
                                set_transient( $cache_key, $data, 5 * MINUTE_IN_SECONDS );
                                nexshop_remember_transient( $cache_key );
                        }

                        return rest_ensure_response( $data );
                },
        ] );
} );
