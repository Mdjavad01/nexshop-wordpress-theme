<form role="search" method="get" class="nx-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <input type="search" name="s" placeholder="<?php esc_attr_e( 'جستجو در فروشگاه…', 'nexshop' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" aria-label="<?php esc_attr_e( 'جستجو', 'nexshop' ); ?>">
        <?php
        /**
         * نوع محتوای جستجو — به‌صورت پیش‌فرض «محصول» (در فروشگاه)؛ قابل تغییر:
         * add_filter( 'nexshop_search_post_type', fn() => '' ); // جستجوی کل سایت
         */
        $post_type = class_exists( 'WooCommerce' ) ? (string) apply_filters( 'nexshop_search_post_type', 'product' ) : '';
        if ( $post_type ) :
        ?>
                <input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>">
        <?php endif; ?>
        <button type="submit" class="nx-btn nx-btn--primary" aria-label="<?php esc_attr_e( 'جستجو', 'nexshop' ); ?>">🔍</button>
</form>
