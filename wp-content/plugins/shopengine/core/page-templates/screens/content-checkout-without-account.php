<?php defined( 'ABSPATH' ) || exit; ?>

<div class="shopengine">
    <?php
    while ( have_posts() ): the_post();
    \ShopEngine\Core\Page_Templates\Hooks\Base_Content::instance()->load_content_designed_from_builder();
    endwhile;
    ?>
</div>