<?php
/**
 * صفحه اصلی فروشگاه — هیرو، دسته‌ها، محصولات، مزیت‌ها
 * @package NexShop
 */
get_header();
?>

<section class="nx-hero">
	<div class="nx-container nx-hero__inner">
		<div class="nx-hero__text nx-reveal">
			<span class="nx-chip"><?php echo esc_html( get_theme_mod( 'nexshop_hero_chip', '⚡ فروشگاه آنلاین' ) ); ?></span>
			<h1><?php echo esc_html( get_theme_mod( 'nexshop_hero_title', get_bloginfo( 'name' ) ) ); ?></h1>
			<p><?php echo esc_html( get_theme_mod( 'nexshop_hero_sub', get_bloginfo( 'description' ) ) ); ?></p>
			<div class="nx-hero__cta">
				<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="nx-btn nx-btn--primary"><?php esc_html_e( 'مشاهده محصولات ←', 'nexshop' ); ?></a>
				<a href="#features" class="nx-btn nx-btn--ghost"><?php esc_html_e( 'چرا ما؟', 'nexshop' ); ?></a>
			</div>
		</div>
		<div class="nx-hero__media nx-reveal">
			<?php $hero_img = get_theme_mod( 'nexshop_hero_image' );
			if ( $hero_img ) : ?>
				<img src="<?php echo esc_url( $hero_img ); ?>" alt="" loading="eager">
			<?php else : ?>
				<div class="nx-hero__placeholder">🛍</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( class_exists( 'WooCommerce' ) ) : ?>
<section class="nx-section nx-container">
	<h2 class="nx-section__title nx-reveal"><?php esc_html_e( 'پرفروش‌ترین‌ها', 'nexshop' ); ?><span>.</span></h2>
	<div class="nx-products nx-reveal">
		<?php echo do_shortcode( '[best_selling_products limit="8" columns="4"]' ); ?>
	</div>
</section>

<section class="nx-section nx-container">
	<h2 class="nx-section__title nx-reveal"><?php esc_html_e( 'تازه‌رسیده‌ها', 'nexshop' ); ?><span>.</span></h2>
	<div class="nx-products nx-reveal">
		<?php echo do_shortcode( '[recent_products limit="8" columns="4"]' ); ?>
	</div>
</section>

<section class="nx-section nx-container">
	<h2 class="nx-section__title nx-reveal"><?php esc_html_e( 'دسته‌بندی‌ها', 'nexshop' ); ?><span>.</span></h2>
	<div class="nx-reveal"><?php echo do_shortcode( '[product_categories number="6" columns="3"]' ); ?></div>
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
			<div class="nx-feature__icon"><?php echo $f[0]; ?></div>
			<h3><?php echo esc_html( $f[1] ); ?></h3>
			<p><?php echo esc_html( $f[2] ); ?></p>
		</div>
	<?php endforeach; ?>
</section>

<?php get_footer(); ?>
