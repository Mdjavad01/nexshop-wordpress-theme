<?php
/**
 * درگاه‌های پرداخت — راهنمای اتصال و فیلترها
 *
 * نکس‌شاپ با ووکامرس استاندارد کار می‌کند؛ یعنی هر افزونه درگاهی که
 * برای ووکامرس نوشته شده باشد بدون تغییر روی این قالب کار می‌کند.
 *
 * ایرانی:  زرین‌پال، زیبال، پی‌پینگ، آیدی‌پی، نکست‌پی، سداد (به‌پرداخت)
 * خارجی:   Stripe، PayPal، Square، Razorpay، 2Checkout
 *
 * @package NexShop
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// افزودن واحدهای پول ایرانی به ووکامرس
add_filter( 'woocommerce_currencies', function ( $currencies ) {
        $currencies['IRT'] = __( 'تومان ایران', 'nexshop' );
        $currencies['IRR'] = __( 'ریال ایران', 'nexshop' );
        return $currencies;
} );

add_filter( 'woocommerce_currency_symbol', function ( $symbol, $currency ) {
        if ( $currency === 'IRT' ) $symbol = __( 'تومان', 'nexshop' );
        if ( $currency === 'IRR' ) $symbol = __( 'ریال', 'nexshop' );
        return $symbol;
}, 10, 2 );

// صفحه راهنمای درگاه‌ها داخل پیشخوان
add_action( 'admin_menu', function () {
        add_submenu_page(
                'nexshop',
                __( 'درگاه‌های پرداخت', 'nexshop' ),
                __( '💳 درگاه‌ها', 'nexshop' ),
                'manage_options',
                'nexshop-gateways',
                'nexshop_gateways_page'
        );
} );

function nexshop_gateways_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;

        $rows = [
                [ __( 'زرین‌پال', 'nexshop' ), '🇮🇷', 'zarinpal.com', __( 'افزونه ZarinPal for WooCommerce', 'nexshop' ) ],
                [ __( 'زیبال', 'nexshop' ),    '🇮🇷', 'zibal.ir',    __( 'افزونه Zibal for WooCommerce', 'nexshop' ) ],
                [ __( 'پی‌پینگ', 'nexshop' ),  '🇮🇷', 'payping.ir',  __( 'افزونه PayPing for WooCommerce', 'nexshop' ) ],
                [ __( 'آیدی‌پی', 'nexshop' ),  '🇮🇷', 'idpay.ir',    __( 'افزونه IDPay for WooCommerce', 'nexshop' ) ],
                [ 'Stripe',                    '🌐', 'stripe.com',  __( 'افزونه WooCommerce Stripe Gateway (رسمی)', 'nexshop' ) ],
                [ 'PayPal',                    '🌐', 'paypal.com',  __( 'افزونه WooCommerce PayPal Payments (رسمی)', 'nexshop' ) ],
        ];

        echo '<div class="wrap" dir="rtl" style="font-family:Vazirmatn,Tahoma"><h1>💳 ' . esc_html__( 'درگاه‌های پرداخت', 'nexshop' ) . '</h1>';
        echo '<p>' . wp_kses_post( __( 'این قالب با همه افزونه‌های درگاه استاندارد ووکامرس سازگار است. افزونه موردنظر را نصب و در <code>ووکامرس ← تنظیمات ← پرداخت</code> فعال کنید:', 'nexshop' ) ) . '</p>';
        echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'درگاه', 'nexshop' ) . '</th><th>' . esc_html__( 'منطقه', 'nexshop' ) . '</th><th>' . esc_html__( 'وب‌سایت', 'nexshop' ) . '</th><th>' . esc_html__( 'افزونه پیشنهادی', 'nexshop' ) . '</th></tr></thead><tbody>';
        foreach ( $rows as $r ) {
                printf( '<tr><td><strong>%s</strong></td><td>%s</td><td dir="ltr">%s</td><td>%s</td></tr>', esc_html( $r[0] ), esc_html( $r[1] ), esc_html( $r[2] ), esc_html( $r[3] ) );
        }
        echo '</tbody></table></div>';
}
