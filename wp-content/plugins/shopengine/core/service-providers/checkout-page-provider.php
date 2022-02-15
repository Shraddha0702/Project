<?php

namespace ShopEngine\Core\Service_Providers;


class Checkout_Page_Provider
{


    public function init()
    {
        // remove thumbnail image from mini cart template
        add_action('woocommerce_cart_item_thumbnail', function ( $product_image, $cart_item, $cart_item_key){
            if(is_cart()) {
                return $product_image;
            }
            
            return '' ;
        }, 10, 4);

       add_action( 'woocommerce_cart_item_name', [ $this, 'add_product_thumbnail' ], 10, 4 );
       add_action( 'woocommerce_cart_item_class', [ $this, 'add_css_class_to_product_tr' ], 10, 3 );

    }

    private function is_applicable(){
        return !is_cart();
    }

    public function add_css_class_to_product_tr($css_class, $cart_item, $cart_item_key)
    {
        if($this->is_applicable()){
            return  $css_class . ' shopengine-order-review-product';
        }
        
        return  $css_class;
    }

    public function add_product_thumbnail($product_name, $cart_item, $cart_item_key)
    {
        if($this->is_applicable()) {

            $product_id = isset($cart_item['variation_id']) && $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];

            $image_url = wp_get_attachment_image(get_post_thumbnail_id($product_id), 'single-post-thumbnail');

            return $image_url  . $product_name;
        }

        return $product_name;
    }
}
