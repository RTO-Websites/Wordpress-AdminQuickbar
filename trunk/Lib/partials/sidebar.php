<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.rto.de
 * @since      1.0.0
 *
 * @package    AdminQuickbar
 */

/**
 * @var string $swiftNonce
 * @var string $addNewPosts
 * @var string $postTypeLoop
 * @var string $languageFlags
 * @var int $currentPost
 * @var array $filteredPostTypes
 */
?>
<div class="admin-quickbar" data-swift-nonce="<?php echo $swiftNonce; ?>" data-current-post="<?php echo $currentPost; ?>">
    <div class="admin-quickbar-inner">

        <div class="aqb-tab-button-wrapper">
            <div class="aqb-tab-button active" data-tab="quickbar">
                Quickbar
            </div>
            <div class="aqb-tab-button" data-tab="options">
                <?php echo __( 'Options' ); ?>
            </div>
        </div>

        <div class="aqb-options aqb-tab aqb-tab-options">
            <h2 class="aqb-settings-headline">
                <?php _e( 'Visibility' ); ?>
            </h2>
            <label class="admin-quickbar-hide-posttypes">
                <?php _e( 'Hide main container (PostTypes)', 'admin-quickbar' ); ?>
                <span class="sublabel">[<?php _e( 'Ctrl+Click', 'admin-quickbar' ); ?>]</span>
                <br/>
                <select class="aqm-hide-posttypes"
                    multiple
                    rows="<?php echo count( $filteredPostTypes ); ?>">
                    <option value="aqb-recent"><?php echo __( 'Recent' ); ?></option>
                    <option value="aqb-favorites"><?php echo __( 'Favorites' ); ?></option>
                    <?php foreach ( $filteredPostTypes as $postType ): ?>
                        <option value="<?php echo $postType->name; ?>"><?php echo $postType->label; ?></option>
                    <?php endforeach; ?>
                </select>

            </label>

            <label class="admin-quickbar-loadthumbs">
                <input type="checkbox" name="admin-quickbar-loadthumbs"/>
                <?php _e( 'Show thumbs', 'admin-quickbar' ); ?>
            </label>

            <label class="admin-quickbar-show-trash-option">
                <input type="checkbox" name="admin-quickbar-show-trash"/>
                <?php _e( 'Show trashed posts', 'admin-quickbar' ); ?>
            </label>

            <label class="admin-quickbar-max-recent">
                <?php _e( 'Max. Recent', 'admin-quickbar' ); ?>
                <br/>
                <input type="number" min="0" value="4" name="admin-quickbar-max-recent"/>
            </label>

            <label class="admin-quickbar-hide-on-website">
                <input type="checkbox" name="admin-quickbar-hide-on-website"/>
                <?php _e( 'Hide quickbar on website', 'admin-quickbar' ); ?>
            </label>


            <h2 class="aqb-settings-headline">
                <?php _e( 'Quickbar behavior', 'admin-quickbar' ); ?>
            </h2>
            <label class="admin-quickbar-keepopen">
                <input type="checkbox" name="admin-quickbar-keepopen"/>
                <?php _e( 'Keep open when switching page', 'admin-quickbar' ); ?>
            </label>


            <label class="admin-quickbar-overlap">
                <input type="checkbox" name="admin-quickbar-overlap"/>
                <?php _e( 'Overlap', 'admin-quickbar' ); ?>
            </label>


            <h2 class="aqb-settings-headline">
                <?php _e( 'Quickbar Color-Theme', 'admin-quickbar' ); ?>
            </h2>
            <label class="admin-quickbar-theme">
                <select name="admin-quickbar-theme">
                    <option value="auto"><?php _e( 'Auto detect', 'admin-quickbar' ); ?></option>
                    <option value="dark"><?php _e( 'Dark', 'admin-quickbar' ); ?></option>
                    <option value="light"><?php _e( 'Light', 'admin-quickbar' ); ?></option>
                </select>
            </label>


        </div>

        <div class="aqb-tab aqb-tab-quickbar active">
            <label class="aqb-search-wrapper">
                <span class="dashicons dashicons-search"></span>
                <input type="search" placeholder="Ctrl + Shift + F" id="aqb-search"/>
            </label>

            <?php if ( !empty( $languageFlags ) ): ?>
                <div class="language-switch">
                    <span data-language-code="all" class="active language-all"><?php _e( 'All' ); ?></span>
                    <?php echo $languageFlags; ?>
                </div>
            <?php endif; ?>

            <div class="admin-quickbar-postlist aqb-recent" data-post-type="aqb-recent">
                <div class="admin-quickbar-post-type"><?php echo __( 'Recent' ); ?>
                </div>
                <div class="admin-quickbar-postlist-inner"></div>
            </div>

            <div class="admin-quickbar-postlist aqb-favorites" data-post-type="aqb-favorites">
                <div class="admin-quickbar-post-type"><?php echo __( 'Favorites' ); ?>
                </div>
                <div class="admin-quickbar-postlist-inner"></div>
            </div>

            <?php echo $postTypeLoop; ?>
        </div>
        <div class="admin-quickbar-contextmenu"></div>
    </div>

    <div class="admin-quickbar-toolbar">
        <span class="aqb-toolbar-item" data-title="<?php _e('Plugins'); ?>">
            <a href="<?php echo admin_url( 'plugins.php' ); ?>">
                <i class="dashicons-before dashicons-admin-plugins"></i>
            </a>
            <span class="aqb-toolbar-submenu">
                <a href="<?php echo admin_url( 'plugins.php' ); ?>"><?php _e('Installed Plugins'); ?></a>
                <a href="<?php echo admin_url( 'plugin-install.php' ); ?>"><?php _e('Add New'); ?></a>
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


        <span class="aqb-toolbar-indicator">Plugins</span>
    </div>

    <div class="toggle-quickbar-button"></div>
</div>


