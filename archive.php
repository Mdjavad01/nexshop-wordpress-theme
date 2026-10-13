<?php
/**
 * @package NexShop
 */
get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) :
	echo '<div class="nx-container nx-single"><h1 class="nx-single__title">';
	if ( is_search() ) printf( esc_html__( 'نتایج جستجو برای: %s', 'nexshop' ), '<span class="nx-accent">' . esc_html( get_search_query() ) . '</span>' );
	elseif ( is_archive() ) the_archive_title();
	echo '</h1>';
	if ( have_posts() ) {
		echo '<div class="nx-grid-posts">';
		while ( have_posts() ) : the_post();
			echo '<article class="' . esc_attr( implode( ' ', get_post_class( 'nx-card' ) ) ) . '"><div class="nx-card__body"><h2 class="nx-card__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h2></div></article>';
		endwhile;
		echo '</div><div class="nx-pagination">' . wp_kses_post( paginate_links() ) . '</div>';
	} else {
		echo '<p>' . esc_html__( 'نتیجه‌ای پیدا نشد.', 'nexshop' ) . '</p>';
	}
	echo '</div>';
endif;

get_footer();
