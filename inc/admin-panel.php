<?php
/**
 * پنل تنظیمات نکس‌شاپ — اتصالات، APIها، MCP
 * @package NexShop
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', function () {
        add_menu_page(
                __( 'نکس‌شاپ', 'nexshop' ),
                __( 'نکس‌شاپ', 'nexshop' ),
                'manage_options',
                'nexshop',
                'nexshop_admin_page',
                'dashicons-store',
                3
        );
} );

/* -------------------------------------------------------------------------
 * اعتبارسنجی ورودی‌ها
 * ---------------------------------------------------------------------- */

// فقط آدرس https (برای وب‌هوک؛ جلوگیری از ارسال داده روی کانال ناامن)
function nexshop_sanitize_https_url( $value ) {
        $value = trim( (string) $value );
        if ( $value === '' ) return '';
        return esc_url_raw( $value, [ 'https' ] );
}

// کلید AI: اگر فیلد خالی ارسال شد، مقدار قبلی حفظ شود (کلید هرگز در HTML چاپ نمی‌شود)
function nexshop_sanitize_secret_keep( $value ) {
        $value = sanitize_text_field( $value );
        return $value === '' ? get_option( 'nexshop_ai_api_key', '' ) : $value;
}

// تیک «حذف کلید AI»: کلید از دیتابیس پاک می‌شود (امکان حذف کامل، نه فقط تعویض)
function nexshop_sanitize_key_delete( $value ) {
        if ( $value === '1' ) {
                delete_option( 'nexshop_ai_api_key' );
        }
        return ''; // خود این گزینه ذخیره نمی‌شود
}

function nexshop_sanitize_ga4( $value ) {
        $value = strtoupper( trim( (string) $value ) );
        return preg_match( '/^G-[A-Z0-9]{4,}$/', $value ) ? $value : '';
}

function nexshop_sanitize_verify_code( $value ) {
        // اجازه می‌دهد کاربر کل تگ meta را هم بچسباند؛ فقط خود کد استخراج می‌شود
        if ( preg_match( '/content=["\']([^"\']+)["\']/', (string) $value, $m ) ) $value = $m[1];
        return preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $value );
}

function nexshop_sanitize_token( $value ) {
        $value = preg_replace( '/[^A-Za-z0-9]/', '', (string) $value );
        return strlen( $value ) >= 24 ? $value : get_option( 'nexshop_api_token', '' ); // توکن ضعیف پذیرفته نمی‌شود
}

function nexshop_sanitize_secret_hmac( $value ) {
        $value = preg_replace( '/[^A-Za-z0-9]/', '', (string) $value );
        return strlen( $value ) >= 24 ? $value : get_option( 'nexshop_webhook_secret', '' );
}

// کد ویجت: فقط کاربرانی که unfiltered_html دارند می‌توانند اسکریپت ذخیره کنند
function nexshop_sanitize_chat_widget( $value ) {
        return current_user_can( 'unfiltered_html' ) ? (string) $value : wp_kses_post( $value );
}

add_action( 'admin_init', function () {
        foreach ( [
                'nexshop_ai_endpoint'       => 'nexshop_sanitize_https_url',
                'nexshop_ai_api_key'        => 'nexshop_sanitize_secret_keep',
                'nexshop_ai_api_key_delete' => 'nexshop_sanitize_key_delete',
                'nexshop_mcp_endpoint'      => 'nexshop_sanitize_https_url',
                'nexshop_api_token'         => 'nexshop_sanitize_token',
                'nexshop_webhook_secret'    => 'nexshop_sanitize_secret_hmac',
                'nexshop_ga4_id'            => 'nexshop_sanitize_ga4',
                'nexshop_gsc_verify'        => 'nexshop_sanitize_verify_code',
                'nexshop_bing_verify'       => 'nexshop_sanitize_verify_code',
                'nexshop_chat_widget'       => 'nexshop_sanitize_chat_widget',
        ] as $opt => $cb ) {
                register_setting( 'nexshop_settings', $opt, [ 'sanitize_callback' => $cb ] );
        }
} );

/* -------------------------------------------------------------------------
 * صفحهٔ تنظیمات
 * ---------------------------------------------------------------------- */
function nexshop_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        $has_key = (bool) get_option( 'nexshop_ai_api_key' );
        ?>
        <div class="wrap" dir="rtl" style="font-family:Vazirmatn,Tahoma">
                <h1>⚙️ <?php esc_html_e( 'پنل نکس‌شاپ — اتصالات و هوش مصنوعی', 'nexshop' ); ?></h1>
                <form method="post" action="options.php">
                        <?php settings_fields( 'nexshop_settings' ); ?>
                        <table class="form-table">
                                <tr><th colspan="2"><h2>🤖 <?php esc_html_e( 'پلتفرم‌های هوش مصنوعی (OpenAI / Claude / هر endpoint سازگار)', 'nexshop' ); ?></h2></th></tr>
                                <tr><th><?php esc_html_e( 'آدرس API', 'nexshop' ); ?></th>
                                        <td><input type="url" name="nexshop_ai_endpoint" value="<?php echo esc_attr( get_option( 'nexshop_ai_endpoint' ) ); ?>" class="regular-text" placeholder="https://api.openai.com/v1" dir="ltr"></td></tr>
                                <tr><th><?php esc_html_e( 'کلید API', 'nexshop' ); ?></th>
                                        <td><input type="password" name="nexshop_ai_api_key" value="" autocomplete="new-password" class="regular-text" dir="ltr" placeholder="<?php echo $has_key ? '••••••••  (ذخیره شده — برای تغییر، کلید جدید را وارد کنید)' : ''; ?>">
                                        <?php if ( $has_key ) : ?>
                                                <label style="display:block;margin-top:8px">
                                                        <input type="checkbox" name="nexshop_ai_api_key_delete" value="1">
                                                        <?php esc_html_e( 'حذف کامل کلید ذخیره‌شده', 'nexshop' ); ?>
                                                </label>
                                        <?php endif; ?>
                                        <p class="description"><?php esc_html_e( 'کلید در صفحه نمایش داده نمی‌شود. برای افزونه‌ها و اسکریپت‌های سفارشی از تابع nexshop_get_ai_config() قابل‌دسترسی است.', 'nexshop' ); ?></p></td></tr>

                                <tr><th colspan="2"><h2>🔌 MCP / وب‌هوک سفارش</h2></th></tr>
                                <tr><th><?php esc_html_e( 'آدرس وب‌هوک / MCP', 'nexshop' ); ?></th>
                                        <td><input type="url" name="nexshop_mcp_endpoint" value="<?php echo esc_attr( get_option( 'nexshop_mcp_endpoint' ) ); ?>" class="regular-text" placeholder="https://your-n8n.example/webhook/..." dir="ltr">
                                        <p class="description"><?php esc_html_e( 'فقط https. با هر سفارش جدید یک درخواست امضاشده (هدر X-NexShop-Signature) به این آدرس ارسال می‌شود. ایمیل مشتری ارسال نمی‌شود.', 'nexshop' ); ?></p></td></tr>
                                <tr><th><?php esc_html_e( 'راز امضای وب‌هوک', 'nexshop' ); ?></th>
                                        <td><input type="text" name="nexshop_webhook_secret" value="<?php echo esc_attr( get_option( 'nexshop_webhook_secret' ) ); ?>" class="regular-text" dir="ltr" readonly onfocus="this.removeAttribute('readonly');this.select()">
                                        <p class="description"><?php esc_html_e( 'برای تأیید اصالت درخواست در سمت گیرنده (HMAC-SHA256 روی بدنهٔ درخواست).', 'nexshop' ); ?></p></td></tr>
                                <tr><th><?php esc_html_e( 'توکن API کاتالوگ', 'nexshop' ); ?></th>
                                        <td><input type="text" name="nexshop_api_token" value="<?php echo esc_attr( get_option( 'nexshop_api_token' ) ); ?>" class="regular-text" dir="ltr" readonly onfocus="this.removeAttribute('readonly');this.select()">
                                        <p class="description" dir="ltr">GET <?php echo esc_html( rest_url( 'nexshop/v1/catalog' ) ); ?> — Header: <code>X-NexShop-Token</code> or <code>Authorization: Bearer …</code></p></td></tr>

                                <tr><th colspan="2"><h2>📈 <?php esc_html_e( 'سئو و آنالیتیکس', 'nexshop' ); ?></h2></th></tr>
                                <tr><th>Google Analytics 4 ID</th>
                                        <td><input type="text" name="nexshop_ga4_id" value="<?php echo esc_attr( get_option( 'nexshop_ga4_id' ) ); ?>" class="regular-text" placeholder="G-XXXXXXX" dir="ltr">
                                        <p class="description"><?php esc_html_e( 'برای مدیران سایت بارگذاری نمی‌شود. با فیلتر nexshop_load_analytics می‌توانید به سیستم رضایت کاربر (Consent) متصل کنید.', 'nexshop' ); ?></p></td></tr>
                                <tr><th>Google Search Console <?php esc_html_e( 'کد تأیید', 'nexshop' ); ?></th>
                                        <td><input type="text" name="nexshop_gsc_verify" value="<?php echo esc_attr( get_option( 'nexshop_gsc_verify' ) ); ?>" class="regular-text" dir="ltr"></td></tr>
                                <tr><th>Bing Webmaster <?php esc_html_e( 'کد تأیید', 'nexshop' ); ?></th>
                                        <td><input type="text" name="nexshop_bing_verify" value="<?php echo esc_attr( get_option( 'nexshop_bing_verify' ) ); ?>" class="regular-text" dir="ltr"></td></tr>

                                <tr><th colspan="2"><h2>💬 <?php esc_html_e( 'ویجت چت (چت‌بات AI / پشتیبانی)', 'nexshop' ); ?></h2></th></tr>
                                <tr><th><?php esc_html_e( 'کد اسکریپت ویجت', 'nexshop' ); ?></th>
                                        <td><textarea name="nexshop_chat_widget" rows="5" class="large-text code" dir="ltr"><?php echo esc_textarea( get_option( 'nexshop_chat_widget' ) ); ?></textarea>
                                        <p class="description"><?php
                                                echo current_user_can( 'unfiltered_html' )
                                                        ? esc_html__( 'کد چت‌بات AI (مثل Tidio، Chatbase، Crisp) را اینجا بگذارید. فقط کد سرویس‌های مورد اعتماد را وارد کنید.', 'nexshop' )
                                                        : esc_html__( 'حساب شما اجازهٔ ذخیرهٔ اسکریپت ندارد (unfiltered_html)؛ تگ‌های script حذف می‌شوند.', 'nexshop' );
                                        ?></p></td></tr>
                        </table>
                        <?php submit_button( __( 'ذخیره تنظیمات', 'nexshop' ) ); ?>
                </form>
        </div>
        <?php
}

/* -------------------------------------------------------------------------
 * تزریق کدهای تأیید، آنالیتیکس و ویجت چت
 * ---------------------------------------------------------------------- */
add_action( 'wp_head', function () {
        if ( $gsc = get_option( 'nexshop_gsc_verify' ) )   echo '<meta name="google-site-verification" content="' . esc_attr( $gsc ) . '">' . "\n";
        if ( $bing = get_option( 'nexshop_bing_verify' ) ) echo '<meta name="msvalidate.01" content="' . esc_attr( $bing ) . '">' . "\n";

        $ga = get_option( 'nexshop_ga4_id' );
        if ( $ga && nexshop_analytics_allowed() ) {
                printf(
                        '<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script>' . "\n" .
                        '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag("js",new Date());gtag("config",%2$s);</script>' . "\n",
                        rawurlencode( $ga ),
                        wp_json_encode( $ga )
                );
        }
}, 30 );

/** آنالیتیکس برای مدیران و پیش‌نمایش سفارشی‌ساز بارگذاری نمی‌شود؛ قابل اتصال به سیستم رضایت کاربر */
function nexshop_analytics_allowed() {
        $allowed = ! is_customize_preview() && ! current_user_can( 'manage_options' );
        return (bool) apply_filters( 'nexshop_load_analytics', $allowed );
}

add_action( 'wp_footer', function () {
        if ( is_customize_preview() ) return;
        $w = get_option( 'nexshop_chat_widget' );
        if ( $w ) echo $w; // phpcs:ignore WordPress.Security.EscapeOutput — کد ویجت عمداً خام است؛ فقط توسط کاربران دارای unfiltered_html ذخیره می‌شود
}, 99 );
