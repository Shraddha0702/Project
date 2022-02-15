<?php

namespace ShopEngine\Core\Page_Templates\Hooks;

use Elementor\Plugin;
use ShopEngine\Core\Builders\Action;
use ShopEngine\Core\Builders\Templates;
use ShopEngine\Core\Template_Cpt;
use Elementor\Core\Files\CSS\Post as Post_CSS;

defined('ABSPATH') || exit;

abstract class Base
{
	protected $template_loaded = false;
	protected $config;
	protected $template_name = null;
	protected $prod_tpl_id;
	protected $tpl_loaded = false;

	abstract protected function template_include_pre_condition() : bool;
	abstract public function init() : void;

	public function __construct() {


		add_filter('body_class', [$this, 'hook_body_class']);

		$this->prod_tpl_id = $this->get_registered_template_id($this->get_page_type_option_slug());

		$elementor_template_module = \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' );
		if($this->is_template_cpt()){
			$elementor_template_module->set_print_callback(function(){
				include_once $this->get_template_part();
			});
		}

        if(  isset( $_GET['elementor-preview'] ) ){
            /**
             * Remove checkout template extra markup;
             */
            remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);

          }

		if(empty($this->prod_tpl_id) || !$this->is_template_active() || !$this->template_include_pre_condition()) {
			return;
		}

		Base_Content::instance()->set_tpl_data($this->prod_tpl_id, $this->get_page_type_option_slug());

		$this->config = Templates::get_template_types()[$this->page_type];

		add_filter('template_include', [$this, 'redirect_page_template'], 999);

		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 999);

		$this->init();
	}

	public function enqueue_scripts() {
		$css_file = Post_CSS::create( $this->prod_tpl_id );

		if('internal' !== get_option( 'elementor_css_print_method' )){
			$css_file->enqueue();
		}
	}

	public function redirect_page_template($template) {

		$this->tpl_loaded = true;

		// this code is from elementor: /elementor/modules/page-templates/module.php:82
		$elementor_document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( $this->prod_tpl_id );
		$elementor_template_module = \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' );

		if ( $elementor_document && $elementor_document::get_property( 'support_wp_page_templates' ) ) {
			$page_template = $elementor_document->get_meta( '_wp_page_template' );
			$page_template = (in_array($page_template, ['elementor_header_footer', 'elementor_canvas']) ? $page_template : 'elementor_header_footer' );

			$template_path = $elementor_template_module->get_template_path( $page_template );

			if ( 'elementor_theme' !== $page_template && ! $template_path && $elementor_document->is_built_with_elementor() ) {
				$kit_default_template = \Elementor\Plugin::$instance->kits_manager->get_current_settings( 'default_page_template' );
				$template_path = $elementor_template_module->get_template_path( $kit_default_template );
			}

			if ( $template_path ) {
				$template = $template_path;
			}
		}

		$elementor_template_module->set_print_callback(function(){
			include_once $this->get_template_part();
		});

		return $template;

	}

	public function get_registered_template_id($type) {

		return \ShopEngine\Core\Builders\Templates::get_registered_template_id($type);
	}

	protected function get_page_type_option_slug(): string {

		return $this->page_type;
	}

	public function get_template_part() {
		return $this->get_builder_template_dir(). $this->template_part;
	}

	public function hook_body_class($classes) {

		$post_type = get_post_type();

		if($post_type != Template_Cpt::TYPE && !$this->template_include_pre_condition()){
			return $classes;
		}

		$classes[] = 'woocommerce';
		$classes[] = $this->get_body_class();
		$classes[] = Templates::BODY_CLASS;
		$classes[] = (empty($this->get_page_type_option_slug()) ? '' : 'shopengine-'. $this->get_page_type_option_slug());
		$classes[] = ($this->config['css'] ?? '');

		return $classes;
	}

	protected function get_body_class() {
		// child class may include custom classes here.
	}

	protected function get_builder_template_dir() {

		return \ShopEngine::core_dir() . 'page-templates/screens/';
	}

	protected function is_template_cpt() {
		$post_id = get_the_ID();
		$post_type = get_post_type();
		return (Template_Cpt::TYPE === $post_type) && ($this->prod_tpl_id == $post_id);
	}

	protected function is_template_active() {

		$prod_tpl_post_obj = get_post($this->prod_tpl_id);

		if(empty($prod_tpl_post_obj)) return false;

		return $prod_tpl_post_obj->post_status == 'publish';
	}
}
