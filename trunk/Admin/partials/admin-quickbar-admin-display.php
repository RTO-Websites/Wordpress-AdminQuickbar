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
 * @var array $filteredPostTypes
 */
?>
<div class="admin-quickbar" data-swift-nonce="<?php echo $swiftNonce; ?>">
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
            <label class="admin-quickbar-keepopen">
                <input type="checkbox" name="admin-quickbar-keepopen"/>
                <?php _e( 'Keep open', 'admin-quickbar' ); ?>
            </label>
            <label class="admin-quickbar-loadthumbs">
                <input type="checkbox" name="admin-quickbar-loadthumbs"/>
                <?php _e( 'Show thumbs', 'admin-quickbar' ); ?>
            </label>
            <label class="admin-quickbar-overlap">
                <input type="checkbox" name="admin-quickbar-overlap"/>
                <?php _e( 'Overlap', 'admin-quickbar' ); ?>
            </label>
            <label class="admin-quickbar-darkmode">
                <input type="checkbox" name="admin-quickbar-darkmode"/>
                <?php _e( 'Darkmode', 'admin-quickbar' ); ?>
            </label>
            <label class="admin-quickbar-hide-on-website">
                <input type="checkbox" name="admin-quickbar-hide-on-website"/>
                <?php _e( 'Hide on website', 'admin-quickbar' ); ?>
            </label>


            <label class="admin-quickbar-hide-posttypes">
                <?php _e( 'Hide PostTypes', 'admin-quickbar' ); ?><br/>
                <select class="aqm-hide-posttypes" multiple>
                    <option value="aqb-favorites"><?php echo __( 'Favorites' ); ?></option>
                    <?php foreach ( $filteredPostTypes as $postType ): ?>
                        <option value="<?php echo $postType->name; ?>"><?php echo $postType->label; ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div class="aqb-tab aqb-tab-quickbar active">
            <?php echo $addNewPosts; ?>

            <?php if ( !empty( $languageFlags ) ): ?>
                <div class="language-switch">
                    <span data-language-code="all" class="active language-all"><?php _e( 'All' ); ?></span>
                    <?php echo $languageFlags; ?>
                </div>
            <?php endif; ?>

            <div class="admin-quickbar-postlist aqb-favorites" data-post-type="aqb-favorites">
                <div class="admin-quickbar-post-type"><?php echo __( 'Favorites' ); ?>
                </div>
                <div class="admin-quickbar-postlist-inner"></div>
            </div>

            <?php echo $postTypeLoop; ?>
        </div>
        <div class="admin-quickbar-contextmenu"></div>
    </div>
    <div class="toggle-quickbar-button"></div>
</div>


