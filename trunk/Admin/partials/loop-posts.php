<div class="admin-quickbar-post" <?php echo $style; ?>>
    <?php if ( $inCache ): ?>
        <a title="Cached (Click to clear)"
                class="dashicons dashicons-yes-alt admin-quickbar-clear-cache"
                data-url="<?php echo $permalink; ?>"></a>
    <?php endif; ?>
    <?php
    echo $thumb;
    echo $postTitle;
    ?>
    <div class="admin-quickbar-post-options">
        <a class="dashicons dashicons-edit"
                href="<?php echo $postTypeInfo['link']; ?>&action=edit"
                title="Go to WP-Editor"></a>


        <?php if ( defined( 'ELEMENTOR_VERSION' ) && !$postTypeInfo['noElementor'] ): ?>
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