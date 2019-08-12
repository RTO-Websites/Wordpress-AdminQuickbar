<div class="admin-quickbar-post <?php echo $activeClass; ?>" data-postid="<?php echo $post->ID; ?>" <?php echo $style; ?> data-contextmenu='<?php echo $contextMenuData; ?>'>
    <?php
    echo $thumb;
    ?>
    <span class="label"><?php echo $postTitle; ?></span>
    <div class="admin-quickbar-post-options">
        <a class="dashicons dashicons-wordpress-alt"
                href="<?php echo $postTypeInfo['link']; ?>&action=edit"
                title="Go to WP-Editor"></a>


        <?php if ( !$postTypeInfo['noElementor'] ): ?>
            <a class="dashicons dashicons-elementor"
                    href="<?php echo $postTypeInfo['link']; ?>&action=elementor"
                    title="Go to Elementor"></a>
        <?php endif; ?>

        <?php if ( empty( $postTypeInfo['noView'] ) ): ?>
            <a class="dashicons dashicons-visibility"
                    href="<?php echo $permalink; ?>"
                    title="Go to Page"></a>
        <?php endif; ?>
    </div>
</div>