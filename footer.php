<?php
/**
 * @package NexShop
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
</main>

<footer class="nx-footer">
	<div class="nx-container nx-footer__grid">
		<div>
			<div class="nx-footer__brand"><?php bloginfo( 'name' ); ?></div>
			<p class="nx-footer__desc"><?php bloginfo( 'description' ); ?></p>
			<?php if ( $trust = get_theme_mod( 'nexshop_trust_text', '' ) ) : ?>
				<p class="nx-footer__trust">🛡 <?php echo esc_html( $trust ); ?></p>
			<?php endif; ?>
		</div>
		<div>
			<h4><?php esc_html_e( 'دسترسی سریع', 'nexshop' ); ?></h4>
			<?php wp_nav_menu( [ 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'nx-footer-menu', 'fallback_cb' => false ] ); ?>
		</div>
		<div>
			<h4><?php esc_html_e( 'پرداخت امن', 'nexshop' ); ?></h4>
			<p class="nx-footer__pay"><?php echo esc_html( get_theme_mod( 'nexshop_gateways_text', 'زرین‌پال • زیبال • پی‌پینگ • Stripe • PayPal' ) ); ?></p>
		</div>
		<div>
			<h4><?php esc_html_e( 'تماس', 'nexshop' ); ?></h4>
			<p class="nx-footer__contact"><?php echo esc_html( get_theme_mod( 'nexshop_contact_text', '' ) ); ?></p>
		</div>
	</div>
	<div class="nx-footer__copy">© <?php echo esc_html( date_i18n( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?> — <?php esc_html_e( 'همه حقوق محفوظ است.', 'nexshop' ); ?></div>
</footer>

<button id="nxToTop" class="nx-totop" aria-label="<?php esc_attr_e( 'بازگشت به بالا', 'nexshop' ); ?>">↑</button>
<?php wp_footer(); ?>
</body>
</html>
