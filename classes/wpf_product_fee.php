<?php

/*
* @author QuanticEdge
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
} 

/*
* required action & filters.
*/

/*
* This Function will be add the tab in woocommerce product for additional fee.
* Date: 07-08-2019
* Author: Shweta
*/

add_action( 'woocommerce_product_write_panel_tabs', 'wpf_product_tab');
function wpf_product_tab() {
 ?><li class="product_tab">
      <a href="#product_panel">
        <span><?php esc_html_e( 'Product-Fee', 'wpf-product-fee' ); ?></span>
      </a>
  </li><?php
}
add_action( 'woocommerce_product_data_panels', 'wpf_panel_tab' );
function wpf_panel_tab() {
  global $post;
  ?><div id="product_panel" class="panel woocommerce_options_panel">
        <div class="options_group"><?php  
            woocommerce_wp_checkbox (
               array( 
                    'id'            => '_wpf_additionl_fee', 
                    'label'         => esc_html(__('Additional Fee', 'wpf-product-fee' )), 
                    'description'   => esc_html(__( 'Visible on Product, Cart,Checkout,Order receipt and Email. ', 'wpf-product-fee' )),
                )
            );
            woocommerce_wp_text_input(
              array(
                  'id' => '_wpf_product_label',
                  'label' => esc_html(__( 'Product Fee', 'wpf-product-fee' )),
              )
            );
            woocommerce_wp_text_input( 
               array(
                    'id' => '_wpf_amount_product_price',
                    'label' => esc_html(__('Amount','wpf-product-fee')),
                )
            ); 
        ?></div>
  </div><?php
}
 
/* 
* This Function will be add post meta of product
* Date : 07-08-2019
* Author : Shweta
*/

add_action( 'woocommerce_process_product_meta', 'wpf_save_product_field',10,1 );
function wpf_save_product_field($post_id) {
  $product_field_value = sanitize_text_field($_POST['_wpf_product_label']);
  if(isset($product_field_value) && !empty($product_field_value)) {
    update_post_meta($post_id, '_wpf_product_label',sanitize_text_field($product_field_value));
  }
  $product_value = sanitize_text_field($_POST['_wpf_amount_product_price']);
  if(isset($product_value) && !empty($product_value)) {
    update_post_meta($post_id,'_wpf_amount_product_price', sanitize_text_field($product_value)); 
  }
  $checkbox_value = sanitize_text_field($_POST['_wpf_additionl_fee']);
  if(isset($checkbox_value) || empty($checkbox_value)) {
   update_post_meta($post_id,'_wpf_additionl_fee', sanitize_text_field($checkbox_value)); 
  }
}

/*
* This function will add the price using product fee field on front-end
* Date : 07-08-2019
* Author : Shweta
*/

add_action('woocommerce_before_calculate_totals','wpf_add_product_price',10,1);
function wpf_add_product_price($cart_obj) {
  foreach ($cart_obj->get_cart() as $key => $value) {
      $orgPrice = floatval( $value['data']->get_price() );
      $product = $value['product_id'];
      if( isset( $product) && !empty($product)) {
        $wpf_field = floatval(get_post_meta($product,'_wpf_amount_product_price',true));
        $product_price = $orgPrice + sanitize_text_field($wpf_field);    
        $value['data']->set_price(($product_price));
      }
  }
}

/*
* This Function will add the product fee on Product page .
* Date: 07-08-2019
* Author: Shweta
*/

add_action('woocommerce_before_add_to_cart_form','wpf_add_additional_fee');
function wpf_add_additional_fee() {
  global $post;
  $wpf_check = get_post_meta($post->ID,'_wpf_additionl_fee',true);
  $wpf_checked = sanitize_text_field($wpf_check);
  $product_field = get_post_meta($post->ID,'_wpf_product_label',true);
  $product_price = get_post_meta($post->ID,'_wpf_amount_product_price',true);
  if(isset($wpf_checked) && !empty($wpf_checked) && $wpf_checked == 'yes') {
    echo sanitize_text_field($product_field).':'.'&nbsp;'.'<strong>'.sanitize_text_field(wc_price($product_price)).'</strong>';
  }
} 

/*
* This Function will be show the additional fee on front end.
* Date: 07-08-2019
* Author: Shweta
*/

add_filter('woocommerce_cart_item_name','wpf_add_cart_item_name',11,2); 
function wpf_add_cart_item_name($product_name, $cart_item ) {
  $wpf_check = get_post_meta($cart_item['product_id'],'_wpf_additionl_fee',true);
  $wpf_checked = sanitize_text_field($wpf_check);
  $product_field = get_post_meta($cart_item['product_id'],'_wpf_product_label',true);
  $product_price = get_post_meta($cart_item['product_id'],'_wpf_amount_product_price',true);
  if(isset($wpf_check) && !empty($wpf_check) && $wpf_checked == 'yes') {

    $product_name .= '<br>'.sanitize_text_field(__($product_field).':'.'&nbsp;'.'<strong>'.sanitize_text_field(wc_price($product_price))).'</strong>';
  }
  return $product_name;
}

/*
* This Function will add the product fee on thank you page and email reciept.
* Date: 07-08-2019
* Author: Shweta
*/

add_action('woocommerce_add_order_item_meta','wpf_add_new_order_item_name',20,3);
function wpf_add_new_order_item_name($item_id, $cart_item ) {
  $wpf_check = get_post_meta($cart_item['product_id'],'_wpf_additionl_fee',true);
  $wpf_checked = sanitize_text_field($wpf_check);
  if(isset($wpf_checked) && !empty($wpf_checked) && $wpf_checked == 'yes') {
    $product_field = get_post_meta($cart_item['product_id'],'_wpf_product_label',true);
    $product_price = get_post_meta($cart_item['product_id'],'_wpf_amount_product_price',true);
  }
   wc_update_order_item_meta($item_id,sanitize_text_field($product_field), sanitize_text_field(wc_price($product_price)));
}




