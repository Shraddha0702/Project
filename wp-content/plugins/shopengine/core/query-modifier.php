<?php

namespace ShopEngine\Core;

use ShopEngine\Traits\Singleton;

class Query_Modifier
{

	use Singleton;

	public function init() {

		add_action('pre_get_posts', [$this, 'modify_query']);
	}

	public function modify_query($query) {

		if(is_admin() || !$query->is_main_query() || $query->is_single === true) {
			return;
		}

		if(!isset($query->query_vars['wc_query']) || $query->query_vars['wc_query'] != 'product_query') {
			return;
		}

		// query filter begins

		// update query for product per page filter
		if(!empty($_GET['shopengine_products_per_page'])) {
			$query->set('posts_per_page', absint($_GET['shopengine_products_per_page']));
		}

		//update query if query param has color & attribute filter
		foreach($_GET as $key => $value) {

			$color_prefix = 'shopengine_filter_color_';

			$attribute_prefix = 'shopengine_filter_attribute_';

			$image_prefix = 'shopengine_filter_image_';

			$label_prefix = 'shopengine_filter_label_';

			$shipping_prefix = 'shopengine_filter_shipping_';

			$category_prefix = 'shopengine_filter_category';

			$stock_prefix = 'shopengine_filter_stock';

			$sale_prefix ='shopengine_filter_onsale';

			if( $key === $category_prefix ) {

				// The archive category will be removed form query if users filter by category
				$query->query['product_cat'] = '';
				$query->query_vars['product_cat'] = '';
				
				$query = $this->query($key.'product_cat', $category_prefix, $value, $query);

			} elseif( strpos($key, $color_prefix) !== false) {

				$query = $this->query($key, $color_prefix, $value, $query);

			} elseif( strpos($key, $attribute_prefix) !== false ) {

				$query = $this->query($key, $attribute_prefix, $value, $query);

			} elseif( strpos($key, $image_prefix) !== false ) {

				$query = $this->query($key, $image_prefix, $value, $query);

			} elseif( strpos($key, $label_prefix) !== false ) {

				$query = $this->query($key, $label_prefix, $value, $query);

			}  elseif( strpos($key, $shipping_prefix) !== false ) {

				$query = $this->query($key, $shipping_prefix, $value, $query);

			} elseif( $key === $stock_prefix ) {

				$query->set('meta_query',
					[
						[
							'key'		=> '_stock_status',
							'value'		=> $value,
							'compare'	=> 'IN'
						]
					]
				);

			} elseif( $key === $sale_prefix ) {
				
				$s = explode(',', $value);
				$sale_query = ['relation' => 'OR'];

				foreach($s as $v) {
					
					if($v === 'on_sale') {
						$sale_query[] = [
							'key'           => '_sale_price',
							'value'         => 0,
							'compare'       => '>',
							'type'          => 'numeric',
							'operator'		=> 'OR',
						];
					} else {
						$sale_query[] = [
							'key'			=> '_sale_price',
							'compare'		=> 'NOT EXISTS',
							'operator'		=> 'OR',
						];
					}
				}
				$query->set('meta_query', $sale_query);
			}
		}	
	}

	public function query($key, $prefix, $values, $query) {

		$taxonomy = str_replace($prefix, '', $key);
		$query_array = ['relation' => 'AND'];

		if('product_cat' === $taxonomy) {
			$query_array = ['relation' => 'OR'];
		}

		$values = explode(',', $values);

		foreach($values as $value) {
			$query_array[] = [
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $value,
				'operator' => 'IN',
			];
		}

		$query->set('tax_query', $query_array);

		return $query;
	}
}
