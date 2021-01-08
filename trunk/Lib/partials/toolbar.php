<?php
/**
 * @var string $swiftNonce
 * @var string $addNewPosts
 * @var string $postTypeLoop
 * @var string $languageFlags
 * @var int $currentPost
 * @var array $filteredPostTypes
 * @var bool $hasSwift
 * @var string $permalink
 * @var bool $inCache
 */
?>
<div class="admin-quickbar-toolbar">
        <span class="aqb-toolbar-item" data-title="<?php _e('Plugins'); ?>">
            <a href="<?php echo admin_url( 'plugins.php' ); ?>">
                <i class="dashicons-before dashicons-admin-plugins"></i>
            </a>
            <span class="aqb-toolbar-submenu">
                <a class="aqb-toolbar-subitem" href="<?php echo admin_url( 'plugins.php' ); ?>">
                    <?php _e('Installed Plugins'); ?>
                </a>
                <a class="aqb-toolbar-subitem" href="<?php echo admin_url( 'plugin-install.php' ); ?>">
                    <?php _e('Add New'); ?>
                </a>
            </span>
        </span>

    <?php if ( defined( 'ELEMENTOR_VERSION' ) ): ?>
        <span class="aqb-toolbar-item"  data-title="<?php _e('Templates'); ?>">
                <a href="<?php echo admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' ); ?>">
                    <i class="dashicons-before dashicons-admin-page"></i>
                </a>
            </span>
    <?php endif; ?>

    <span class="aqb-toolbar-item"  data-title="<?php _e('Settings'); ?>">
            <a href="<?php echo admin_url( 'options-general.php' ); ?>">
                <i class="dashicons-before dashicons-admin-settings"></i>
            </a>
        </span>

    <span class="aqb-toolbar-item"  data-title="<?php _e('Customize'); ?>">
            <a href="<?php echo admin_url( 'customize.php?return=' . urlencode( $_SERVER['REQUEST_URI'] ) ); ?>">
                <i class="dashicons-before dashicons-admin-appearance"></i>
            </a>
        </span>


    <?php if ( $hasSwift ): ?>
        <span class="aqb-toolbar-item"  data-title="<?php _e('Refresh swift cache'); ?>">
                <a
                    class="admin-quickbar-control-cache  <?php echo $inCache ? ' is-in-cache' : ''; ?>"
                    data-url="<?php echo $permalink; ?>">
                    <i class="aqb-icon-swift clear-all dashicons dashicons-update-alt"></i>
                </a>
            </span>
    <?php endif; ?>

    <?php if ( !empty( $cssPosts ) ): ?>
        <span class="aqb-toolbar-item" data-title="CSS">
                <a class="icon-inner"
                    href="<?php echo admin_url() . 'post.php?post=' . end( $cssPosts )->ID; ?>&action=edit">
                    <i class="dashicons dashicons-editor-code"></i>
                </a>
            <span class="aqb-toolbar-submenu">
                <?php reset( $cssPosts );
                foreach ( $cssPosts as $cssPost ): ?>
                    <span class="aqb-toolbar-subitem aqb-text-align-right">
                        <span class="aqb-toolbar-label">
                            <?php echo $cssPost->post_title; ?>
                        </span>
                        <span class="aqb-toolbar-actions">
                            <a href="#" class="aqb-icon aqb-icon-external dashicons dashicons-external"></a>
                            <a class="aqb-icon aqb-icon-wordpress dashicons-edit"
                                href="<?php echo admin_url() . 'post.php?post=' . $cssPost->ID; ?>&action=edit"
                                title="Edit CSS"></a>
                        </span>
                    </span>

                <?php endforeach; ?>
            </span>
        </span>
    <?php endif; ?>


    <span class="aqb-toolbar-indicator">Plugins</span>
</div>