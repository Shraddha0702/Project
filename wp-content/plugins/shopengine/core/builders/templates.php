<?php

namespace ShopEngine\Core\Builders;

use ShopEngine\Core\PageTemplates\Page_Templates;

class Templates {

	const BODY_CLASS = 'shopengine-template';

	public static function get_template_types(): array {

		return Page_Templates::instance()->getTemplates();
	}

	public static function get_registered_template_data($template_id) {

		$type = self::get_template_type_by_id($template_id);

		return Page_Templates::instance()->getTemplate($type);
	}

	public static function get_template_type_by_id($pid): string {

		$pm = get_post_meta($pid, Action::get_meta_key_for_type(), true);

		return empty($pm) ? 'shop' : $pm;
	}

	public static function get_registered_template_id($template_type) {

		if(!empty($_GET['change_template']) && !empty($_GET['shopengine_template_id']) && !empty($_GET['preview_nonce'])) {
			
			$nonce_status = apply_filters(
				'shopengine/demo/bypass_nonce', 
				(wp_verify_nonce($_GET['preview_nonce'], 'template_preview_' . $_GET['shopengine_template_id']))
			);

			if(1 !== $nonce_status) {
				return 0;
			}

			return (int)$_GET['shopengine_template_id'];
		}

		$saved_id = get_option(Action::PK__SHOPENGINE_TEMPLATE . '__' . $template_type, 0); // this can be string with '-' or int, both.

		global $wpdb;
		$id_by_meta = $wpdb->get_results("select post_id from $wpdb->postmeta where meta_key = 'shopengine_template_uuid' and meta_value = '$saved_id' limit 1", ARRAY_A);

		if(isset($id_by_meta[0])) {
			return $id_by_meta[0];
		}

		return (int)$saved_id;
	}

    public static function has_simple_product($in_status = ['publish', 'draft'])
    {
        global $wpdb;

        $result = $wpdb->get_row("
    SELECT * 
    FROM  $wpdb->posts
        WHERE post_type = 'product'
         AND post_status IN('publish', 'draft')
   ");

        return ! empty($result);
    }

    public static function create_wc_simple_product() {

		$product = new \WC_Product_Simple();

		$product->set_name( 'Shopengine preview product [do not delete it]' );
		$product->set_description( 'This is a shopengine demo preview product' );
		$product->set_short_description( 'This is a shopengine demo preview product' );
		$product->set_status( 'draft' );

		$product->set_regular_price( 100 );
		$product->set_sale_price( 79 );
		$product->set_price( 79 );

		$product->set_sku( 'shopengine-demo-preview-01' );

		$product->set_manage_stock( false );
		$product->set_stock_status( 'instock' );

		$product->set_weight( 11 );
		$product->set_length( 12 );
		$product->set_width( 10 );
		$product->set_height( 9 );

		//$product->set_image_id( 'image_id' );
		//$product->set_gallery_image_ids( [] );

		return $product->save();
	}
}
