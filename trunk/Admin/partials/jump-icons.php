<div class="admin-quickbar-jumpicons" data-swift-nonce="<?php echo $swiftNonce; ?>">
    <?php if ( $hasSwift ): ?>
        <a title="Refresh swift cache"
                class="aqb-icon aqb-icon-swift admin-quickbar-control-cache  <?php echo $inCache ? ' is-in-cache' : ''; ?>"
                data-url="<?php echo $permalink; ?>"></a>
    <?php endif; ?>

    <?php if ( !empty( $cssPosts ) ): ?>
        <span class="aqb-icon aqb-icon-css"
                href="<?php echo $permalink; ?>"
                title="Go to Page">CSS
            <span class="aqb-css-list">
                <?php foreach ( $cssPosts as $cssPost ): ?>
                    <span class="item has-sub">
                        <span class="label"><?php echo $cssPost->post_title; ?></span>
                        <a href="#" class="subitem aqb-icon aqb-icon-external dashicons dashicons-external"></a>
                        <a class="subitem aqb-icon aqb-icon-wordpress dashicons-edit"
                                href="<?php echo admin_url() . 'post.php?post=' . $cssPost->ID; ?>&action=edit"
                                title="Edit CSS"></a>
                </span>

                <?php endforeach; ?>
            </span>
        </span>
    <?php endif; ?>

    <?php if ( defined( 'ELEMENTOR_VERSION' ) ): ?>
        <a class="aqb-icon aqb-icon-wordpress"
                href="<?php echo admin_url() . 'post.php?post=' . $currentPost; ?>&action=edit"
                title="Go to WP-Editor"></a>
        <a class="aqb-icon aqb-icon-elementor"
                href="<?php echo admin_url() . 'post.php?post=' . $currentPost; ?>&action=elementor"
                title="Go to Elementor"></a>
    <?php endif; ?>

    <a class="aqb-icon aqb-icon-website"
            href="<?php echo $permalink; ?>"
            title="Go to Page"></a>
</div>