<?php
/**
 * ویجت المنتور: دسته‌بندی‌های محصول نکس‌شاپ
 *
 * @package NexShop
 */

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class NexShop_Categories_Widget extends Widget_Base {

        public function get_name() {
                return 'nexshop_categories';
        }

        public function get_title() {
                return __( 'دسته‌بندی‌های نکس‌شاپ', 'nexshop' );
        }

        public function get_icon() {
                return 'eicon-product-categories';
        }

        public function get_categories() {
                return [ 'nexshop', 'general' ];
        }

        public function get_keywords() {
                return [ 'category', 'woocommerce', 'shop', 'nexshop', 'دسته', 'فروشگاه' ];
        }

        protected function register_controls() {

                $this->start_controls_section(
                        'section_query',
                        [ 'label' => __( 'تنظیمات دسته‌بندی‌ها', 'nexshop' ) ]
                );

                $this->add_control(
                        'number',
                        [
                                'label'   => __( 'تعداد دسته‌ها', 'nexshop' ),
                                'type'    => Controls_Manager::NUMBER,
                                'default' => 6,
                                'min'     => 1,
                                'max'     => 30,
                        ]
                );

                $this->add_control(
                        'columns',
                        [
                                'label'   => __( 'تعداد ستون‌ها', 'nexshop' ),
                                'type'    => Controls_Manager::SELECT,
                                'default' => '3',
                                'options' => [
                                        '2' => '2',
                                        '3' => '3',
                                        '4' => '4',
                                        '5' => '5',
                                ],
                        ]
                );

                $this->add_control(
                        'hide_empty',
                        [
                                'label'        => __( 'پنهان‌کردن دسته‌های خالی', 'nexshop' ),
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
                        echo '<p class="nx-el-empty">' . esc_html__( 'برای نمایش دسته‌بندی‌ها، ووکامرس را فعال کنید.', 'nexshop' ) . '</p>';
                        return;
                }

                $number  = max( 1, min( 30, (int) ( $settings['number'] ?? 6 ) ) );
                $columns = in_array( (string) ( $settings['columns'] ?? '3' ), [ '2', '3', '4', '5' ], true ) ? (string) $settings['columns'] : '3';
                $hide    = ( $settings['hide_empty'] ?? 'yes' ) === 'yes' ? 'true' : 'false';

                $shortcode = sprintf( '[product_categories number="%d" columns="%s" hide_empty="%s"]', $number, $columns, $hide );
                $key       = 'el_cats_' . $number . 'x' . $columns . ( 'yes' === ( $settings['hide_empty'] ?? 'yes' ) ? '_empty' : '' );

                echo nexshop_cached_shortcode( $key, $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput — خروجی رسمی ووکامرس
        }
}
