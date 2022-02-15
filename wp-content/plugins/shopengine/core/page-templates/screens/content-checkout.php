<?php

defined('ABSPATH') || exit; ?>

<div class="shopengine-quick-checkout-content-warper">
    <div class="woocommerce shopengine-woocommerce-checkout">

		<?php if($this->get_page_type_option_slug() == 'quick_checkout'): ?>
            <style>
                #wpadminbar {
                    display: none!important;
                }
                html{
                    margin: 0!important;
                }
            </style>
		<?php endif; ?>

		<?php
		$checkout = WC()->checkout();

		global $wp;

		while(have_posts()) : the_post();

			do_action('woocommerce_before_checkout_form', $checkout);

			$skip_content = false;

			if(!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {


				?>

				<style>
					.woocommerce.shopengine-woocommerce-checkout {
						margin: 0 auto;
						width: 600px;
						padding: 20px 0;
					}

					.shopengine-quick-checkout-content-warper .woocommerce-info {
						margin-bottom: 15px;
					}

					@media(max-width: 768px) {
					.woocommerce.shopengine-woocommerce-checkout {
						width: 90%;
					}
					}
				</style>

                <div class="woocommerce-form-login-toggle">
					<?php wc_print_notice( apply_filters( 'woocommerce_checkout_login_message', esc_html__( 'Returning customer?', 'shopengine' ) ) . ' <a href="#" class="showlogin">' . esc_html__( 'Click here to login', 'shopengine' ) . '</a>', 'notice' ); ?>
                </div>
				<?php

				woocommerce_login_form(
					array(
						'message'  => esc_html__( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'shopengine' ),
						'redirect' => wc_get_checkout_url(),
						'hidden'   => true,
					)
				);

				echo esc_html(
					apply_filters(
						'woocommerce_checkout_must_be_logged_in_message',
						esc_html__('You must be logged in to checkout.', 'shopengine')
					)
				);

				$skip_content = true;
			}

			if(!\ShopEngine\Utils\Helper::is_guest_checkout_allowed() && !is_user_logged_in()) {
				$skip_content = true;
			}

			if(!$skip_content):
				if ( isset( $wp->query_vars['order-pay'] ) ) : ?>
                    <div class="shopengine-order-pay-container">
						<?php \ShopEngine\Core\Page_Templates\Hooks\Base_Content::instance()->load_content_designed_from_builder(); ?>
                    </div>
				<?php else: ?>
                    <form name="checkout"
                          method="post"
                          class="checkout woocommerce-checkout shopengine-woocommerce-checkout-form"
                          action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
                          enctype="multipart/form-data">

						<?php \ShopEngine\Core\Page_Templates\Hooks\Base_Content::instance()->load_content_designed_from_builder(); ?>

                    </form>
				<?php
				endif;
			endif;
		endwhile; ?>
    </div>
</div>
