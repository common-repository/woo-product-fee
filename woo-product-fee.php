<?php
/**
* Plugin Name: Product Fee for Woocommerce
* Plugin URI: http://quanticedge.co.in
* Description: This plugin allows you to add custom fee per product. You can also decide whether this fee should be visible customer or not.
* Author: QuanticEdge 
* Author URI: http://quanticedge.co.in
* Text Domain: wpf-product-fee
* Domain Path: /languages/
* Version: 1.0
*/ 
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	require_once('classes/wpf_product_fee.php');	
}

?>