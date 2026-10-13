<?php
/**
 * قالب صفحه — اگر صفحه با المنتور ساخته شده باشد، تمام‌عرض و بدون عنوان/کانتینر قالب نمایش داده می‌شود.
 *
 * @package NexShop
 */
get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) :
	while ( have_posts() ) : the_post();
		if ( nexshop_is_elementor_page( get_the_ID() ) ) {
			echo '<article class="' . esc_attr( implode( ' ', get_post_class( 'nx-elementor-page' ) ) ) . '">';
			the_content();
			echo '</article>';
		} else {
			echo '<div class="nx-container nx-single">';
			echo '<article class="' . esc_attr( implode( ' ', get_post_class() ) ) . '"><h1 class="nx-single__title">' . esc_html( get_the_title() ) . '</h1><div class="nx-single__content">';
			the_content();
			echo '</div></article>';
			echo '</div>';
		}
	endwhile;
endif;

get_footer();
