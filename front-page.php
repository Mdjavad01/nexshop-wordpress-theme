<?php
/**
 * صفحه اصلی فروشگاه — هیرو، دسته‌ها، محصولات، مزیت‌ها
 * @package NexShop
 */
get_header();

// اگر صفحهٔ اصلی با المنتور ساخته شده باشد، محتوای المنتور به‌صورت تمام‌عرض نمایش داده می‌شود.
if ( 'page' === get_option( 'show_on_front' ) && nexshop_is_elementor_page( (int) get_option( 'page_on_front' ) ) ) {
        while ( have_posts() ) : the_post();
                echo '<div class="nx-elementor-page">';
                the_content();
                echo '</div>';
        endwhile;
        get_footer();
        return;
}
?>
<section class="nx-hero">
        <div class="nx-container nx-hero__inner">
                <div class="nx-hero__text">
                        <span class="nx-chip"><?php echo esc_html( get_theme_mod( 'nexshop_hero_chip', '⚡ فروشگاه آنلاین' ) ); ?></span>
                        <h1><?php echo esc_html( get_theme_mod( 'nexshop_hero_title', get_bloginfo( 'name' ) ) ); ?></h1>
                        <p><?php echo esc_html( get_theme_mod( 'nexshop_hero_sub', get_bloginfo( 'description' ) ) ); ?></p>
                        <div class="nx-hero__cta">
                                <a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="nx-btn nx-btn--primary"><?php esc_html_e( 'مشاهده محصولات ←', 'nexshop' ); ?></a>
                                <a href="#features" class="nx-btn nx-btn--ghost"><?php esc_html_e( 'چرا ما؟', 'nexshop' ); ?></a>
                        </div>
                </div>
                <div class="nx-hero__media">
                        <?php
                        /**
                         * تصویر هیرو با srcset کامل:
                         *  - روش جدید: انتخاب از کتابخانه رسانه (Attachment ID) → خروجی wp_get_attachment_image
                         *    با srcset و sizes برای همهٔ اندازه‌ها (ریتینا و موبایل بهینه)
                         *  - سازگاری با روش قدیمی: اگر فقط URL ذخیره شده باشد، ID آن از کش استخراج می‌شود
                         */
                        $hero_url = get_theme_mod( 'nexshop_hero_image', '' );
                        $hero_id  = (int) get_theme_mod( 'nexshop_hero_image_id', 0 );

                        // سازگاری رو به عقب: URL قدیمی → ID (با کش تا کوئری تکراری نشود)
                        if ( ! $hero_id && $hero_url ) {
                                $cache_key = 'nexshop_heroid_' . md5( $hero_url );
                                $hero_id   = (int) get_transient( $cache_key );
                                if ( ! $hero_id ) {
                                        $hero_id = (int) attachment_url_to_postid( $hero_url );
                                        set_transient( $cache_key, $hero_id, DAY_IN_SECONDS );
                                }
                        }

                        $hero_alt = get_theme_mod( 'nexshop_hero_title', get_bloginfo( 'name' ) );

                        if ( $hero_id ) {
                                echo wp_get_attachment_image( $hero_id, 'nexshop-hero', false, [
                                        'class'         => 'nx-hero__img',
                                        'alt'           => esc_attr( $hero_alt ),
                                        'loading'       => 'eager',
                                        'fetchpriority' => 'high',
                                        'decoding'      => 'async',
                                ] );
                        } elseif ( $hero_url ) {
                                ?>
                                <img src="<?php echo esc_url( $hero_url ); ?>" alt="<?php echo esc_attr( $hero_alt ); ?>" loading="eager" fetchpriority="high" decoding="async">
                                <?php
                        } else {
                                ?>
                                <div class="nx-hero__placeholder">🛍</div>
                                <?php
                        } ?>
                </div>
        </div>
</section>

<?php if ( class_exists( 'WooCommerce' ) ) : ?>
<section class="nx-section nx-container">
        <h2 class="nx-section__title nx-reveal"><?php esc_html_e( 'پرفروش‌ترین‌ها', 'nexshop' ); ?><span>.</span></h2>
        <div class="nx-products nx-reveal">
                <?php echo nexshop_cached_shortcode( 'best', '[best_selling_products limit="8" columns="4"]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        </div>
</section>

<section class="nx-section nx-container">
        <h2 class="nx-section__title nx-reveal"><?php esc_html_e( 'تازه‌رسیده‌ها', 'nexshop' ); ?><span>.</span></h2>
        <div class="nx-products nx-reveal">
                <?php echo nexshop_cached_shortcode( 'recent', '[recent_products limit="8" columns="4"]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        </div>
</section>

<section class="nx-section nx-container">
        <h2 class="nx-section__title nx-reveal"><?php esc_html_e( 'دسته‌بندی‌ها', 'nexshop' ); ?><span>.</span></h2>
        <div class="nx-reveal"><?php echo nexshop_cached_shortcode( 'cats', '[product_categories number="6" columns="3"]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
</section>
<?php endif; ?>

<section id="features" class="nx-section nx-container nx-features">
        <?php
        $features = [
                [ '🚚', __( 'ارسال سریع', 'nexshop' ), __( 'به سراسر ایران و جهان', 'nexshop' ) ],
                [ '💳', __( 'پرداخت امن', 'nexshop' ), __( 'درگاه‌های ایرانی و بین‌المللی', 'nexshop' ) ],
                [ '↩️', __( 'ضمانت بازگشت', 'nexshop' ), __( '۷ روز مهلت بازگشت کالا', 'nexshop' ) ],
                [ '🎧', __( 'پشتیبانی ۲۴/۷', 'nexshop' ), __( 'پاسخ‌گویی با AI و انسان', 'nexshop' ) ],
        ];
        foreach ( $features as $f ) : ?>
                <div class="nx-card nx-feature nx-reveal">
                        <div class="nx-feature__icon"><?php echo esc_html( $f[0] ); ?></div>
                        <h3><?php echo esc_html( $f[1] ); ?></h3>
                        <p><?php echo esc_html( $f[2] ); ?></p>
                </div>
        <?php endforeach; ?>
</section>

<?php get_footer(); ?>
