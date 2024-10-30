<?php
/*
    Plugin Name: Confirmed Shipped for WooCommerce
    Requires Plugins: woocommerce
    description: Adds a new order status "Order Shipped" to the WooCommerce order management system.
    Version: 1.3
    Author: Matteo Enna
    Author URI: https://matteoenna.it/it/wordpress-work/
    Text Domain: confirmed-shipped-for-woocommerce
    License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once (dirname(__FILE__).'/class/confirmed-shipped-for-woocommerce_class.php');

new confirmedShippedForwoocommerce_class();
