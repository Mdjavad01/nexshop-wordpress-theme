<?php
/**
 * @package NexShop
 */
get_header();
echo '<div class="nx-container nx-single"><h1 class="nx-single__title">' . esc_html__( '۴۰۴ — صفحه پیدا نشد', 'nexshop' ) . '</h1>';
echo '<p>' . esc_html__( 'صفحه‌ای که دنبالشی اینجا نیست. از جستجو استفاده کن:', 'nexshop' ) . '</p>';
get_search_form();
echo '<p><a class="nx-btn nx-btn--primary" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'بازگشت به فروشگاه', 'nexshop' ) . '</a></p></div>';
get_footer();
