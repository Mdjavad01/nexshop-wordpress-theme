<?php
/**
 * @package NexShop
 */
get_header();
if ( function_exists( 'woocommerce_content' ) && ( is_shop() || is_product_category() || is_product_tag() || is_product() || is_cart() || is_checkout() || is_account_page() ) ) {
	echo '<div class="nx-container nx-wc">';
	woocommerce_content();
	echo '</div>';
} elseif ( have_posts() ) :
	echo '<div class="nx-container nx-grid-posts">';
	while ( have_posts() ) : the_post();
		echo '<article ' . post_class( 'nx-card' ) . '>';
		if ( has_post_thumbnail() ) echo '<a href="' . esc_url( get_permalink() ) . '" class="nx-card__img">' . get_the_post_thumbnail( null, 'nexshop-card' ) . '</a>';
		echo '<div class="nx-card__body"><h2 class="nx-card__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h2>';
		echo '<p class="nx-card__excerpt">' . esc_html( wp_trim_words( get_the_excerpt(), 20 ) ) . '</p></div></article>';
	endwhile;
	echo '</div><div class="nx-container nx-pagination">' . wp_kses_post( paginate_links() ) . '</div>';
else :
	echo '<div class="nx-container"><p>' . esc_html__( 'چیزی پیدا نشد.', 'nexshop' ) . '</p></div>';
endif;
get_footer();
