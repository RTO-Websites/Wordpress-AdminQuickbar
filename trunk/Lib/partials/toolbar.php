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
 * @var array $submenuItems
 */


$requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
?>
<div class="admin-quickbar-toolbar">
    <span class="aqb-toolbar-item" data-title="<?php esc_attr_e( 'Plugins', 'adminquickbar' ); ?>">
        <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>">
            <i class="dashicons-before dashicons-admin-plugins"></i>
        </a>
        <span class="aqb-toolbar-submenu">
            <?php do_action( 'aqb-toolbar-submenu-before', 'plugins' ); ?>
            <?php echo $submenuItems['plugins.php']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php do_action( 'aqb-toolbar-submenu-after', 'plugins' ); ?>
        </span>
    </span>

    <?php if ( defined( 'ELEMENTOR_VERSION' ) ): ?>
        <span class="aqb-toolbar-item" data-title="<?php esc_attr_e( 'Templates', 'adminquickbar' ); ?>">
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' ) ); ?>">
                <i class="dashicons-before dashicons-admin-page"></i>
            </a>
            <span class="aqb-toolbar-submenu">
                <?php do_action( 'aqb-toolbar-submenu-before', 'elementor' ); ?>
                <?php echo $submenuItems['elementor']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php do_action( 'aqb-toolbar-submenu-after', 'elementor' ); ?>
            </span>
        </span>
    <?php endif; ?>

    <span class="aqb-toolbar-item" data-title="<?php esc_attr_e( 'Settings', 'adminquickbar' ); ?>">
        <a href="<?php echo esc_url( admin_url( 'options-general.php' ) ); ?>">
            <i class="dashicons-before dashicons-admin-settings"></i>
        </a>
        <span class="aqb-toolbar-submenu">
            <?php do_action( 'aqb-toolbar-submenu-before', 'settings' ); ?>
            <?php echo $submenuItems['options-general.php']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php do_action( 'aqb-toolbar-submenu-after', 'settings' ); ?>
        </span>
    </span>

    <span class="aqb-toolbar-item" data-title="<?php esc_attr_e( 'Customize', 'adminquickbar' ); ?>">
        <a href="<?php echo esc_url( admin_url( 'customize.php?return=' . urlencode(  $requestUri ) ) ); ?>">
            <i class="dashicons-before dashicons-admin-appearance"></i>
        </a>

        <span class="aqb-toolbar-submenu">
            <?php do_action( 'aqb-toolbar-submenu-before', 'customize' ); ?>
            <?php echo $submenuItems['themes.php']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php do_action( 'aqb-toolbar-submenu-after', 'customize' ); ?>
        </span>
    </span>


    <?php if ( $hasSwift ): ?>
        <span class="aqb-toolbar-item" data-title="<?php esc_attr_e( 'Refresh swift cache', 'adminquickbar' ); ?>">
            <a
                    class="admin-quickbar-control-cache  <?php echo $inCache ? ' is-in-cache' : ''; ?>"
                    data-url="<?php echo esc_url( $permalink ); ?>">
                <i class="aqb-icon-swift clear-all dashicons dashicons-update-alt"></i>
            </a>
        </span>
    <?php endif; ?>

    <?php if ( !empty( $cssPosts ) ): ?>
        <span class="aqb-toolbar-item" data-title="CSS">
            <a class="icon-inner"
                    href="<?php echo esc_url( admin_url() . 'post.php?post=' . end( $cssPosts )->ID ); ?>&action=edit">
                <i class="dashicons dashicons-editor-code"></i>
            </a>
            <span class="aqb-toolbar-submenu">
                <?php do_action( 'aqb-toolbar-submenu-before', 'css' ); ?>
                <?php reset( $cssPosts );
                foreach ( $cssPosts as $cssPost ): ?>
                    <span class="aqb-toolbar-subitem aqb-text-align-right">
                        <span class="aqb-toolbar-label">
                            <?php echo esc_attr( $cssPost->post_title ); ?>
                        </span>
                        <span class="aqb-toolbar-actions">
                            <a href="#" class="aqb-icon aqb-icon-external dashicons dashicons-external"></a>
                            <a class="aqb-icon aqb-icon-wordpress dashicons-edit"
                                    href="<?php echo esc_url( admin_url() . 'post.php?post=' . $cssPost->ID ); ?>&action=edit"
                                    title="Edit CSS"></a>
                        </span>
                    </span>
                <?php endforeach; ?>
                <?php do_action( 'aqb-toolbar-submenu-after', 'css' ); ?>
            </span>
        </span>
    <?php endif; ?>


    <span class="aqb-toolbar-indicator"></span>
</div>