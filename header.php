<?php
/**
 * @package NexShop
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'رفتن به محتوا', 'nexshop' ); ?></a>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>
<header class="nx-header" id="nxHeader">
        <div class="nx-container nx-header__inner">
                <div class="nx-header__logo">
                        <?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nx-logo-text"><?php bloginfo( 'name' ); ?></a>
                        <?php endif; ?>
                </div>

                <nav class="nx-nav" id="nxNav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'nexshop' ); ?>">
                        <?php wp_nav_menu( [
                                'theme_location' => 'primary',
                                'container'      => false,
                                'menu_class'     => 'nx-menu',
                                'fallback_cb'    => false,
                        ] ); ?>
                </nav>

                <div class="nx-header__actions">
                        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="nx-icon-btn" aria-label="<?php esc_attr_e( 'حساب کاربری', 'nexshop' ); ?>">👤</a>
                                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="nx-icon-btn nx-cart-btn" aria-label="<?php esc_attr_e( 'سبد خرید', 'nexshop' ); ?>">
                                        🛒 <span class="nx-cart-count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
                                </a>
                        <?php endif; ?>

                        <button type="button" class="nx-icon-btn nx-theme-toggle" id="nxThemeToggle"
                                aria-label="<?php esc_attr_e( 'تغییر حالت روشن/تیره', 'nexshop' ); ?>"
                                data-label-dark="<?php esc_attr_e( 'تغییر به حالت تیره', 'nexshop' ); ?>"
                                data-label-light="<?php esc_attr_e( 'تغییر به حالت روشن', 'nexshop' ); ?>"
                                aria-pressed="false">
                                <span class="nx-icon-sun" aria-hidden="true">☀️</span>
                                <span class="nx-icon-moon" aria-hidden="true">🌙</span>
                        </button>

                        <button type="button" class="nx-icon-btn nx-burger" id="nxBurger" aria-label="<?php esc_attr_e( 'منو', 'nexshop' ); ?>" aria-controls="nxNav" aria-expanded="false">☰</button>
                </div>
        </div>
</header>
<?php endif; ?>

<main id="main" class="nx-main">
