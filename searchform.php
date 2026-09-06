<form role="search" method="get" class="nx-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" name="s" placeholder="<?php esc_attr_e( 'جستجو در فروشگاه…', 'nexshop' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
	<?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
	<button type="submit" class="nx-btn nx-btn--primary">🔍</button>
</form>
