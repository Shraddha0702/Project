<?php defined('ABSPATH') || exit;

\ShopEngine\Widgets\Widget_Helper::instance()->wc_template_filter();

\ShopEngine\Widgets\Widget_Helper::instance()->wc_template_part_filter_by_match('woocommerce/content-product.php', 'templates/content-product.php');

$upsell_ids = $product->get_upsell_ids();

$upsells = array_filter(array_map('wc_get_product', $upsell_ids), 'wc_products_array_filter_visible');

$editor_mode = (\Elementor\Plugin::$instance->editor->is_edit_mode() || is_preview());

if($editor_mode) {

	global $wp_query, $post;;
	$main_query = clone $wp_query;
	$main_post = clone $post;

	$wp_query = new \WP_Query([]);

	$args = [
		'type'  => ['simple'],
		'limit' => $shopengine_up_sells_product_to_show,
	];

	$upsells = wc_get_products($args);

	unset($args, $upsell_ids);
}

if(empty($upsells)) {

	return;
}

$limit		= $shopengine_up_sells_product_to_show;
$orderby	= $shopengine_up_sells_product_orderby;
$order		= $shopengine_up_sells_product_order;
$columns	= $is_slider_enable ? $shopengine_up_sells_product_slider_perview : $shopengine_up_sells_product_columns;

?>

<div class="shopengine-up-sells <?php echo ($is_slider_enable ? 'slider-enabled' : 'slider-disabled'); ?>" data-controls="<?php echo esc_attr($encode_slider_options); ?>">
	<?php
	if($post_type == \ShopEngine\Core\Template_Cpt::TYPE) {
		wc_get_template(
			'single-product/up-sells.php',
			[
				'upsells'        => $upsells,
				'posts_per_page' => $limit,
				'orderby'        => $orderby,
				'columns'        => $columns,
			]
		);
	} else {
		woocommerce_upsell_display($limit, $columns, $orderby, $order);
	}

	if($is_slider_enable && $shopengine_up_sells_product_slider_show_dots) {
		echo '<div class="swiper-pagination" style="width: 100%;"></div>';
	}

	if($is_slider_enable && $shopengine_up_sells_product_slider_show_arrows) {
		echo sprintf(
			'<div class="swiper-button-prev">%1$s</div><div class="swiper-button-next">%2$s</div>',
			$this->get_icon_html($shopengine_up_sells_product_slider_left_arrow_icon),
			$this->get_icon_html($shopengine_up_sells_product_slider_right_arrow_icon)
		);
	}
	?>
</div>

<?php

if($editor_mode) {

	global $wp_query, $post;;

	$wp_query = $main_query;
	$post = $main_post;
	wp_reset_query();
	wp_reset_postdata();

	unset($main_query, $main_post);
}
