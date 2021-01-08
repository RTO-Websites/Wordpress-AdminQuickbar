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