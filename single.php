<?php
/**
 * @package NexShop
 */
get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) :
	echo '<div class="nx-container nx-single">';
	while ( have_posts() ) : the_post();
		echo '<article class="' . esc_attr( implode( ' ', get_post_class() ) ) . '">';
		if ( has_post_thumbnail() ) the_post_thumbnail( 'nexshop-banner', [ 'class' => 'nx-single__img' ] );
		echo '<h1 class="nx-single__title">' . esc_html( get_the_title() ) . '</h1>';
		echo '<div class="nx-single__content">';
		the_content();
		echo '</div></article>';
		if ( comments_open() ) comments_template();
	endwhile;
	echo '</div>';
endif;

get_footer();
