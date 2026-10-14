<?php
/**
 * یکپارچگی کامل با المنتور
 *
 * - ثبت کامل لوکیشن‌های Theme Builder (هدر، فوتر، تک، آرشیو، …)
 * - ویجت‌های اختصاصی نکس‌شاپ (محصولات و دسته‌بندی‌ها)
 * - سازگاری با قالب‌های Canvas و Full Width خود المنتور
 * - اعلان راهنما برای نصب المنتور
 * - استایل ادیتور المنتور هماهنگ با قالب
 *
 * @package NexShop
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* -------------------------------------------------------------------------
 * 1. لوکیشن‌های Theme Builder
 *    با ثبت این لوکیشن‌ها، هدر/فوتر/تک/آرشیو ساخته‌شده در المنتور Pro
 *    به‌صورت خودکار جایگزین هدر/فوتر/قالب‌های قالب می‌شوند.
 * ---------------------------------------------------------------------- */
add_action( 'elementor/theme/register_locations', function ( $manager ) {
        $manager->register_all_core_location();
} );

/**
 * آیا صفحهٔ جاری با المنتور ساخته شده است؟
 */
function nexshop_is_elementor_page( $post_id = 0 ) {
        if ( ! class_exists( '\Elementor\Plugin' ) ) return false;
        $post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
        if ( ! $post_id ) return false;
        $doc = \Elementor\Plugin::$instance->documents->get( $post_id );
        return $doc && $doc->is_built_with_elementor();
}

// کلاس بدنه برای صفحه‌های المنتوری (حذف کانتینر و عنوان پیش‌فرض قالب)
add_filter( 'body_class', function ( $classes ) {
        if ( nexshop_is_elementor_page() ) $classes[] = 'nx-elementor';
        return $classes;
} );

/* -------------------------------------------------------------------------
 * 2. ویجت‌های اختصاصی نکس‌شاپ
 * ---------------------------------------------------------------------- */
add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
        require_once NEXSHOP_DIR . '/elementor/widgets/class-nexshop-products-widget.php';
        require_once NEXSHOP_DIR . '/elementor/widgets/class-nexshop-categories-widget.php';

        if ( class_exists( 'NexShop_Products_Widget' ) ) {
                $widgets_manager->register( new NexShop_Products_Widget() );
        }
        if ( class_exists( 'NexShop_Categories_Widget' ) ) {
                $widgets_manager->register( new NexShop_Categories_Widget() );
        }
} );

// سازگاری با نسخه‌های قدیمی‌تر المنتور (قبل از 3.5)
add_action( 'elementor/widgets/widgets_registered', function () {
        if ( did_action( 'elementor/widgets/register' ) ) return; // نسخهٔ جدید قبلاً ثبت کرده
        if ( ! class_exists( 'NexShop_Products_Widget' ) ) {
                require_once NEXSHOP_DIR . '/elementor/widgets/class-nexshop-products-widget.php';
                require_once NEXSHOP_DIR . '/elementor/widgets/class-nexshop-categories-widget.php';
        }
        if ( \Elementor\Plugin::$instance->widgets_manager && class_exists( 'NexShop_Products_Widget' ) ) {
                \Elementor\Plugin::$instance->widgets_manager->register_widget_type( new NexShop_Products_Widget() );
                \Elementor\Plugin::$instance->widgets_manager->register_widget_type( new NexShop_Categories_Widget() );
        }
}, 50 );

/* -------------------------------------------------------------------------
 * 3. اعلان نصب المنتور (اگر فعال نباشد)
 * ---------------------------------------------------------------------- */
add_action( 'admin_notices', function () {
        if ( did_action( 'elementor/loaded' ) ) return;
        if ( ! current_user_can( 'install_plugins' ) ) return;
        if ( get_option( 'nexshop_elementor_notice_dismissed' ) ) return;
        if ( get_current_screen() && get_current_screen()->is_block_editor() ) return;

        $install_url = wp_nonce_url(
                self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ),
                'install-plugin_elementor'
        );
        $dismiss_url = wp_nonce_url( add_query_arg( 'nexshop_dismiss_elementor', '1' ), 'nexshop_dismiss_elementor' );
        ?>
        <div class="notice notice-info is-dismissible" style="padding:12px">
                <strong>🧩 <?php esc_html_e( 'قالب نکس‌شاپ — آمادهٔ کار با المنتور', 'nexshop' ); ?></strong>
                <p>
                        <?php esc_html_e( 'برای استفاده از تمام قابلیت‌ها (صفحه‌ساز، Theme Builder و ویجت‌های اختصاصی نکس‌شاپ) افزونهٔ المنتور را نصب کنید.', 'nexshop' ); ?>
                        <a href="<?php echo esc_url( $install_url ); ?>" class="button button-primary" style="margin-inline-start:8px">
                                <?php esc_html_e( 'نصب المنتور', 'nexshop' ); ?>
                        </a>
                        <a href="<?php echo esc_url( $dismiss_url ); ?>" class="button-link" style="margin-inline-start:8px">
                                <?php esc_html_e( 'دیگر نشان نده', 'nexshop' ); ?>
                        </a>
                </p>
        </div>
        <?php
} );

// بستن اعلان المنتور
add_action( 'admin_init', function () {
        if ( isset( $_GET['nexshop_dismiss_elementor'] ) && check_admin_referer( 'nexshop_dismiss_elementor' ) ) {
                update_option( 'nexshop_elementor_notice_dismissed', 1 );
        }
} );

/* -------------------------------------------------------------------------
 * 4. استایل ادیتور المنتور — پیش‌نمایش هماهنگ با قالب (فونت + رنگ‌ها)
 * ---------------------------------------------------------------------- */
add_action( 'elementor/editor/after_enqueue_styles', function () {
        wp_enqueue_style( 'nexshop-elementor-editor', NEXSHOP_URI . '/assets/css/elementor-editor.css', [], NEXSHOP_VERSION );
} );

/* -------------------------------------------------------------------------
 * 5. پیش‌نمایش سفارشی‌ساز: اعمال فوری حالت رنگی پیش‌فرض (بدون رفرش کامل)
 * ---------------------------------------------------------------------- */
add_action( 'customize_preview_init', function () {
        $scheme = get_theme_mod( 'nexshop_default_scheme', 'auto' );
        // «خودکار» در پیش‌نمایش به‌صورت تیره نمایش داده می‌شود؛ انتخاب صریح روشن/تیره فوری اعمال می‌شود
        $value  = ( $scheme === 'light' ) ? 'light' : 'dark';
        wp_add_inline_script(
                'nexshop-dark-mode',
                "document.documentElement.setAttribute('data-theme'," . wp_json_encode( $value ) . ');'
        );
} );
