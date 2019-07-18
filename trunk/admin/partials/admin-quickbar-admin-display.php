<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.rto.de
 * @since      1.0.0
 *
 * @package    AdminPostListSidebar
 * @subpackage AdminPostListSidebar/admin/partials
 */
?>
    <div class="admin-quickbar">
        <div class="admin-quickbar-inner">
            <label for="admin-quickbar-keepopen" class="admin-quickbar-keepopen">
                <input type="checkbox" name="admin-quickbar-keepopen"/>
                <?php _e( 'Keep open', 'admin-quickbar' ); ?>
            </label>
            <label for="admin-quickbar-keepopen" class="admin-quickbar-loadthumbs">
                <input type="checkbox" name="admin-quickbar-keepopen"/>
                <?php _e( 'Show thumbs', 'admin-quickbar' ); ?>
            </label>
            <?php include( 'add-new-posts.php' ); ?>
            <?php $this->renderLoopPostTypes(); ?>
        </div>
        <div class="toggle-quickbar-button"></div>
    </div>

<?php
$currentPost = filter_input( INPUT_GET, 'post' );
if ( !empty( $currentPost ) ): ?>
    <div class="admin-quickbar-jumpicons">
        <a class="dashicons dashicons-visibility" href="' . get_permalink( $currentPost ) . '" title="Go to Page"></a>
        <?php if ( defined( 'ELEMENTOR_VERSION' ) ): ?>
            <a class="dashicons dashicons-edit"
                    href="<?php echo admin_url() . 'post.php?post=' . $currentPost; ?>&action=edit"
                    title="Go to WP-Editor"></a>
            <a class="dashicons dashicons-elementor"
                    href="<?php echo admin_url() . 'post.php?post=' . $currentPost; ?>&action=elementor"
                    title="Go to Elementor"></a>
        <?php endif; ?>
    </div>

<?php endif; ?>