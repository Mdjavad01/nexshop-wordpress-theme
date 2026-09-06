<?php
/**
 * پنل تنظیمات نکس‌شاپ — اتصالات، APIها، MCP
 * @package NexShop
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', function () {
	add_menu_page(
		__( 'نکس‌شاپ', 'nexshop' ),
		__( 'نکس‌شاپ', 'nexshop' ),
		'manage_options',
		'nexshop',
		'nexshop_admin_page',
		'dashicons-store',
		3
	);
} );

add_action( 'admin_init', function () {
	foreach ( [
		'nexshop_ai_endpoint'   => 'esc_url_raw',
		'nexshop_ai_api_key'    => 'sanitize_text_field',
		'nexshop_mcp_endpoint'  => 'esc_url_raw',
		'nexshop_ga4_id'        => 'sanitize_text_field',
		'nexshop_gsc_verify'    => 'sanitize_text_field',
		'nexshop_bing_verify'   => 'sanitize_text_field',
		'nexshop_chat_widget'   => 'wp_kses_post',
	] as $opt => $cb ) {
		register_setting( 'nexshop_settings', $opt, [ 'sanitize_callback' => $cb ] );
	}
} );

function nexshop_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) return;
	?>
	<div class="wrap" dir="rtl" style="font-family:Vazirmatn,Tahoma">
		<h1>⚙️ <?php esc_html_e( 'پنل نکس‌شاپ — اتصالات و هوش مصنوعی', 'nexshop' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'nexshop_settings' ); ?>
			<table class="form-table">
				<tr><th colspan="2"><h2>🤖 <?php esc_html_e( 'پلتفرم‌های هوش مصنوعی (OpenAI / Claude / هر endpoint سازگار)', 'nexshop' ); ?></h2></th></tr>
				<tr><th><?php esc_html_e( 'آدرس API', 'nexshop' ); ?></th>
					<td><input type="url" name="nexshop_ai_endpoint" value="<?php echo esc_attr( get_option( 'nexshop_ai_endpoint' ) ); ?>" class="regular-text" placeholder="https://api.openai.com/v1" dir="ltr"></td></tr>
				<tr><th><?php esc_html_e( 'کلید API', 'nexshop' ); ?></th>
					<td><input type="password" name="nexshop_ai_api_key" value="<?php echo esc_attr( get_option( 'nexshop_ai_api_key' ) ); ?>" class="regular-text" dir="ltr"></td></tr>

				<tr><th colspan="2"><h2>🔌 MCP (Model Context Protocol)</h2></th></tr>
				<tr><th><?php esc_html_e( 'آدرس MCP Server', 'nexshop' ); ?></th>
					<td><input type="url" name="nexshop_mcp_endpoint" value="<?php echo esc_attr( get_option( 'nexshop_mcp_endpoint' ) ); ?>" class="regular-text" placeholder="https://your-mcp-server.example/sse" dir="ltr">
					<p class="description"><?php esc_html_e( 'برای اتصال ایجنت‌های AI به کاتالوگ محصولات، سفارش‌ها و موجودی فروشگاه.', 'nexshop' ); ?></p></td></tr>

				<tr><th colspan="2"><h2>📈 <?php esc_html_e( 'سئو و آنالیتیکس', 'nexshop' ); ?></h2></th></tr>
				<tr><th>Google Analytics 4 ID</th>
					<td><input type="text" name="nexshop_ga4_id" value="<?php echo esc_attr( get_option( 'nexshop_ga4_id' ) ); ?>" class="regular-text" placeholder="G-XXXXXXX" dir="ltr"></td></tr>
				<tr><th>Google Search Console <?php esc_html_e( 'کد تأیید', 'nexshop' ); ?></th>
					<td><input type="text" name="nexshop_gsc_verify" value="<?php echo esc_attr( get_option( 'nexshop_gsc_verify' ) ); ?>" class="regular-text" dir="ltr"></td></tr>
				<tr><th>Bing Webmaster <?php esc_html_e( 'کد تأیید', 'nexshop' ); ?></th>
					<td><input type="text" name="nexshop_bing_verify" value="<?php echo esc_attr( get_option( 'nexshop_bing_verify' ) ); ?>" class="regular-text" dir="ltr"></td></tr>

				<tr><th colspan="2"><h2>💬 <?php esc_html_e( 'ویجت چت (چت‌بات AI / پشتیبانی)', 'nexshop' ); ?></h2></th></tr>
				<tr><th><?php esc_html_e( 'کد اسکریپت ویجت', 'nexshop' ); ?></th>
					<td><textarea name="nexshop_chat_widget" rows="5" class="large-text code" dir="ltr"><?php echo esc_textarea( get_option( 'nexshop_chat_widget' ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'کد چت‌بات AI (مثل Tidio AI، Chatbase، Crisp یا ایجنت اختصاصی) را اینجا بگذارید.', 'nexshop' ); ?></p></td></tr>
			</table>
			<?php submit_button( __( 'ذخیره تنظیمات', 'nexshop' ) ); ?>
		</form>
	</div>
	<?php
}

// تزریق کدهای تأیید و آنالیتیکس و ویجت چت به سایت
add_action( 'wp_head', function () {
	if ( $gsc = get_option( 'nexshop_gsc_verify' ) )  echo '<meta name="google-site-verification" content="' . esc_attr( $gsc ) . '">' . "\n";
	if ( $bing = get_option( 'nexshop_bing_verify' ) ) echo '<meta name="msvalidate.01" content="' . esc_attr( $bing ) . '">' . "\n";
	if ( $ga = get_option( 'nexshop_ga4_id' ) ) {
		printf(
			'<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag("js",new Date());gtag("config","%1$s");</script>',
			esc_js( $ga )
		);
	}
}, 30 );

add_action( 'wp_footer', function () {
	$w = get_option( 'nexshop_chat_widget' );
	if ( $w ) echo $w; // phpcs:ignore — intentionally raw embed code
}, 99 );
