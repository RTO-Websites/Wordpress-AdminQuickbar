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
 * @var string $settings
 * @var string $toolbar
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

        <?php echo $settings; ?>

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

    <?php echo $toolbar; ?>

    <div class="toggle-quickbar-button"></div>
</div>


