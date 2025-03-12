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
<div class="admin-quickbar" data-swift-nonce="<?php echo esc_attr($swiftNonce); ?>" data-current-post="<?php echo esc_attr($currentPost); ?>">
    <div class="admin-quickbar-inner">

        <div class="aqb-tab-button-wrapper">
            <div class="aqb-tab-button active" data-tab="quickbar">
                Quickbar
            </div>
            <div class="aqb-tab-button" data-tab="options">
                <?php esc_attr_e( 'Options', 'adminquickbar' ); ?>
            </div>
        </div>

        <?php
        echo $settings; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>

        <div class="aqb-tab aqb-tab-quickbar active">
            <label class="aqb-search-wrapper">
                <span class="dashicons dashicons-search"></span>
                <input type="search" placeholder="Ctrl + Shift + F" id="aqb-search"/>
            </label>

            <?php if ( !empty( $languageFlags ) ): ?>
                <div class="language-switch">
                    <span data-language-code="all" class="active language-all"><?php esc_attr_e( 'All', 'adminquickbar' ); ?></span>
                    <?php echo wp_kses_post($languageFlags); ?>
                </div>
            <?php endif; ?>

            <div class="admin-quickbar-postlist aqb-recent" data-post-type="aqb-recent">
                <div class="admin-quickbar-post-type"><?php esc_attr_e( 'Recent', 'adminquickbar' ); ?>
                </div>
                <div class="admin-quickbar-postlist-inner"></div>
            </div>

            <div class="admin-quickbar-postlist aqb-favorites" data-post-type="aqb-favorites">
                <div class="admin-quickbar-post-type"><?php esc_attr_e( 'Favorites', 'adminquickbar' ); ?>
                </div>
                <div class="admin-quickbar-postlist-inner"></div>
            </div>

            <?php echo wp_kses_post($postTypeLoop); ?>
        </div>
        <div class="admin-quickbar-contextmenu"></div>
    </div>

    <?php echo wp_kses_post($toolbar); ?>

    <div class="toggle-quickbar-button"></div>
</div>


