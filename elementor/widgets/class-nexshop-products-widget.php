<?php
/**
 * ویجت المنتور: محصولات نکس‌شاپ
 *
 * خروجی بر پایهٔ شورت‌کدهای رسمی ووکامرس است (کاملاً سازگار با حلقهٔ WC)
 * و از کش هوشمند قالب استفاده می‌کند.
 *
 * @package NexShop
 */

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class NexShop_Products_Widget extends Widget_Base {

        public function get_name() {
                return 'nexshop_products';
        }

        public function get_title() {
                return __( 'محصولات نکس‌شاپ', 'nexshop' );
        }

        public function get_icon() {
                return 'eicon-products';
        }

        public function get_categories() {
                return [ 'nexshop', 'general' ];
        }

        public function get_keywords() {
                return [ 'product', 'woocommerce', 'shop', 'nexshop', 'محصول', 'فروشگاه' ];
        }

        protected function register_controls() {

                $this->start_controls_section(
                        'section_query',
                        [ 'label' => __( 'تنظیمات محصولات', 'nexshop' ) ]
                );

                $this->add_control(
                        'source',
                        [
                                'label'   => __( 'منبع محصولات', 'nexshop' ),
                                'type'    => Controls_Manager::SELECT,
                                'default' => 'recent',
                                'options' => [
                                        'recent'       => __( 'تازه‌ترین محصولات', 'nexshop' ),
                                        'best_selling' => __( 'پرفروش‌ترین‌ها', 'nexshop' ),
                                        'sale'         => __( 'محصولات حراجی', 'nexshop' ),
                                        'featured'     => __( 'محصولات ویژه', 'nexshop' ),
                                ],
                        ]
                );

                $this->add_control(
                        'limit',
                        [
                                'label'   => __( 'تعداد محصولات', 'nexshop' ),
                                'type'    => Controls_Manager::NUMBER,
                                'default' => 8,
                                'min'     => 1,
                                'max'     => 24,
                        ]
                );

                $this->add_control(
                        'columns',
                        [
                                'label'   => __( 'تعداد ستون‌ها', 'nexshop' ),
                                'type'    => Controls_Manager::SELECT,
                                'default' => '4',
                                'options' => [
                                        '2' => '2',
                                        '3' => '3',
                                        '4' => '4',
                                        '5' => '5',
                                ],
                        ]
                );

                $this->add_control(
                        'use_cache',
                        [
                                'label'        => __( 'کش خروجی (پیشنهاد می‌شود)', 'nexshop' ),
                                'type'         => Controls_Manager::SWITCHER,
                                'default'      => 'yes',
                                'return_value' => 'yes',
                        ]
                );

                $this->end_controls_section();
        }

        protected function render() {
                $settings = $this->get_settings_for_display();

                if ( ! class_exists( 'WooCommerce' ) ) {
                        echo '<p class="nx-el-empty">' . esc_html__( 'برای نمایش محصولات، ووکامرس را فعال کنید.', 'nexshop' ) . '</p>';
                        return;
                }

                $limit   = max( 1, min( 24, (int) ( $settings['limit'] ?? 8 ) ) );
                $columns = in_array( (string) ( $settings['columns'] ?? '4' ), [ '2', '3', '4', '5' ], true ) ? (string) $settings['columns'] : '4';

                $map = [
                        'recent'       => '[recent_products limit="%d" columns="%s"]',
                        'best_selling' => '[best_selling_products limit="%d" columns="%s"]',
                        'sale'         => '[sale_products limit="%d" columns="%s"]',
                        'featured'     => '[featured_products limit="%d" columns="%s"]',
                ];
                $source    = isset( $map[ $settings['source'] ?? 'recent' ] ) ? $settings['source'] : 'recent';
                $shortcode = sprintf( $map[ $source ], $limit, $columns );

                if ( ( $settings['use_cache'] ?? 'yes' ) === 'yes' ) {
                        $key = 'el_products_' . $source . '_' . $limit . 'x' . $columns;
                        echo nexshop_cached_shortcode( $key, $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput — خروجی رسمی ووکامرس
                } else {
                        echo do_shortcode( $shortcode );
                }
        }
}
