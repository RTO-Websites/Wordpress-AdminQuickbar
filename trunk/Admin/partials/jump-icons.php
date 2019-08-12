
<div class="admin-quickbar-jumpicons" data-swift-nonce="<?php echo $swiftNonce; ?>">
    <?php if ( $hasSwift ): ?>
        <a title="Refresh swift cache"
            class="dashicons dashicons-update-alt admin-quickbar-control-cache  <?php echo $inCache ? ' is-in-cache' : ''; ?>"
            data-url="<?php echo $permalink; ?>"></a>
    <?php endif; ?>

    <?php if ( defined( 'ELEMENTOR_VERSION' ) ): ?>
        <a class="dashicons dashicons-wordpress-alt"
            href="<?php echo admin_url() . 'post.php?post=' . $currentPost; ?>&action=edit"
            title="Go to WP-Editor"></a>
        <a class="dashicons dashicons-elementor"
            href="<?php echo admin_url() . 'post.php?post=' . $currentPost; ?>&action=elementor"
            title="Go to Elementor"></a>
    <?php endif; ?>

    <a class="dashicons dashicons-visibility"
        href="<?php echo $permalink; ?>"
        title="Go to Page"></a>
</div>