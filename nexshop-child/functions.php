<?php
/**
 * NexShop Child Theme Functions
 * چایلد تم نکس‌شاپ — سفارشی‌سازی امن بدون از دست رفتن تغییرات در آپدیت‌ها
 *
 * نکته: نیازی به انکو کردن استایل قالب والد نیست؛ functions.php والد به‌صورت
 * خودکار main.css، فونت‌ها و اسکریپت‌ها را بارگذاری می‌کند و style.css این
 * چایلد (هندل nexshop-style) بعد از آن اعمال می‌شود.
 *
 * @package NexShopChild
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* نمونه: افزودن اسکریپت یا استایل سفارشی
add_action( 'wp_enqueue_scripts', function () {
        wp_enqueue_style(
                'nexshop-child-extra',
                get_stylesheet_directory_uri() . '/assets/css/extra.css',
                [ 'nexshop-style' ], // بعد از استایل والد
                '1.0.0'
        );
} );
*/

/* نمونه: لودر فایل‌های PHP سفارشی از پوشهٔ inc چایلد
foreach ( glob( get_stylesheet_directory() . '/inc/*.php' ) as $file ) {
        require_once $file;
}
*/
