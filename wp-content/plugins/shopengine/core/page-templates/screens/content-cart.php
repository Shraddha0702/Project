<?php

use Elementor\Plugin;
use ShopEngine\Core\Page_Templates\Hooks\Base_Content;

defined( 'ABSPATH' ) || exit; ?>

<div class="shopengine">
	<?php
	$editor_mode = Plugin::$instance->editor->is_edit_mode();
	if ( WC()->cart->is_empty() && ! $editor_mode ) {
		?>
        <div class="shopengine shopengine-empty-cart-container">
            <div class="woocommerce-notices-wrapper">

                <article class="page type-page status-publish hentry entry">

                    <header class="entry-header alignwide">
                        <h1 class="entry-title"><?php
							esc_html_e( "Cart", 'shopengine' ) ?></h1>
                    </header>

                    <div class="entry-content">
                        <div class="woocommerce">
                            <div class="woocommerce-notices-wrapper"></div>
							<?php
							do_action( 'woocommerce_cart_is_empty' ); ?>
                            <a class="button wc-backward" href="<?php
							echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect',
								wc_get_page_permalink( 'shop' ) ) ); ?>">
								<?php
								/**
								 * Filter "Return To Shop" text.
								 *
								 * @param  string  $default_text  Default text.
								 *
								 * @since 4.6.0
								 */
								echo esc_html( apply_filters( 'woocommerce_return_to_shop_text',
									__( 'Return to shop', 'shopengine' ) ) );
								?>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </div>

		<?php
	} else {
		while ( have_posts() ): the_post();
			Base_Content::instance()->load_content_designed_from_builder();
		endwhile;
	}
	?>
</div>