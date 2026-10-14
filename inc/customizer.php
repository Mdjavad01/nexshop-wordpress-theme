<?php
/**
 * سفارشی‌ساز قالب — هیرو، فوتر، رنگ‌ها و حالت پیش‌فرض دارک/لایت
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

        /*
         * تصویر هیرو — روش توصیه‌شده: انتخاب از کتابخانه رسانه (Attachment ID).
         * با ID، خروجی <img> با srcset و sizes کامل چاپ می‌شود (بهینه برای موبایل و رتینا).
         */
        $c->add_setting( 'nexshop_hero_image_id', [
                'default'           => 0,
                'sanitize_callback' => 'absint',
        ] );
        $c->add_control( new WP_Customize_Media_Control( $c, 'nexshop_hero_image_id', [
                'label'     => __( 'تصویر هیرو (از کتابخانه رسانه)', 'nexshop' ),
                'section'   => 'nexshop_hero',
                'mime_type' => 'image',
                'description' => __( 'روش پیشنهادی — به‌صورت خودکار اندازه‌های ریسپانسیو (srcset) ساخته می‌شود.', 'nexshop' ),
        ] ) );

        // روش قدیمی (URL مستقیم) — فقط برای سازگاری با تنظیمات قبلی حفظ شده است
        $c->add_setting( 'nexshop_hero_image', [ 'sanitize_callback' => 'esc_url_raw' ] );
        $c->add_control( 'nexshop_hero_image', [
                'label'       => __( 'تصویر هیرو (روش قدیمی — URL)', 'nexshop' ),
                'section'     => 'nexshop_hero',
                'description' => __( 'اگر تصویر بالا از کتابخانه رسانه انتخاب شده باشد، این فیلد نادیده گرفته می‌شود.', 'nexshop' ),
        ] );

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

        // — رنگ‌بندی و حالت پیش‌فرض دارک/لایت
        $c->add_section( 'nexshop_colors', [ 'title' => __( 'رنگ‌بندی و حالت نمایش', 'nexshop' ), 'panel' => 'nexshop_panel' ] );

        $c->add_setting( 'nexshop_default_scheme', [
                'default'           => 'auto',
                'sanitize_callback' => function ( $value ) {
                        return in_array( $value, [ 'auto', 'light', 'dark' ], true ) ? $value : 'auto';
                },
        ] );
        $c->add_control( 'nexshop_default_scheme', [
                'label'       => __( 'حالت رنگی پیش‌فرض', 'nexshop' ),
                'section'     => 'nexshop_colors',
                'type'        => 'select',
                'description' => __( '«خودکار» = تشخیص از تنظیمات سیستم کاربر. کاربران همیشه با دکمهٔ هدر می‌توانند تغییر دهند و انتخابشان ذخیره می‌شود.', 'nexshop' ),
                'choices'     => [
                        'auto'  => __( 'خودکار (سیستم کاربر)', 'nexshop' ),
                        'dark'  => __( 'تیره (دارک‌مود)', 'nexshop' ),
                        'light' => __( 'روشن (لایت‌مود)', 'nexshop' ),
                ],
        ] );

        $c->add_setting( 'nexshop_accent', [ 'default' => '#C8F169', 'sanitize_callback' => 'sanitize_hex_color' ] );
        $c->add_control( new WP_Customize_Color_Control( $c, 'nexshop_accent', [
                'label' => __( 'رنگ اکسنت (دکمه‌ها و لینک‌ها)', 'nexshop' ), 'section' => 'nexshop_colors',
        ] ) );
} );

// نکته: رنگ اکسنت انتخاب‌شده در functions.php با wp_add_inline_style به‌صورت CSS variable تزریق می‌شود.
// نکته: کنتراست متن روی دکمه‌ها همیشه با توکن --nx-on-accent تضمین می‌شود (دارک و لایت).
