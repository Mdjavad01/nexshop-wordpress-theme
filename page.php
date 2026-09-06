<?php
/**
 * @package NexShop
 */
get_header();
echo '<div class="nx-container nx-single">';
while ( have_posts() ) : the_post();
	echo '<article ' . post_class() . '><h1 class="nx-single__title">' . esc_html( get_the_title() ) . '</h1><div class="nx-single__content">';
	the_content();
	echo '</div></article>';
endwhile;
echo '</div>';
get_footer();
