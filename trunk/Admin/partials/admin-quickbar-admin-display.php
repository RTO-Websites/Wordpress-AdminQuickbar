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
<div class="admin-quickbar" data-swift-nonce="<?php echo $swiftNonce; ?>">
    <div class="admin-quickbar-inner">
        <label for="admin-quickbar-keepopen" class="admin-quickbar-keepopen">
            <input type="checkbox" name="admin-quickbar-keepopen"/>
            <?php _e( 'Keep open', 'admin-quickbar' ); ?>
        </label>
        <label for="admin-quickbar-loadthumbs" class="admin-quickbar-loadthumbs">
            <input type="checkbox" name="admin-quickbar-loadthumbs"/>
            <?php _e( 'Show thumbs', 'admin-quickbar' ); ?>
        </label>
        <label for="admin-quickbar-overlap" class="admin-quickbar-overlap">
            <input type="checkbox" name="admin-quickbar-overlap"/>
            <?php _e( 'Overlap', 'admin-quickbar' ); ?>
        </label>
        <?php echo $addNewPosts; ?>

        <div class="admin-quickbar-postlist aqb-favorites" data-post-type="aqb-favorites">
            <div class="admin-quickbar-post-type"><?php echo __('Favorites'); ?>
            </div>
            <div class="admin-quickbar-postlist-inner"></div>
        </div>

        <?php echo $postTypeLoop; ?>
    </div>
    <div class="toggle-quickbar-button"></div>
</div>

<?php if ( !empty( $currentPost ) ): ?>
    <div class="admin-quickbar-jumpicons" data-swift-nonce="<?php echo $swiftNonce; ?>">
        <?php if ( $hasSwift ): ?>
            <a title="Refresh swift cache"
                    class="dashicons dashicons-update-alt admin-quickbar-control-cache  <?php echo $inCache ? ' is-in-cache' : ''; ?>"
                    data-url="<?php echo $permalink; ?>"></a>
        <?php endif; ?>
        <a class="dashicons dashicons-visibility"
                href="<?php echo get_permalink( $currentPost ); ?>"
                title="Go to Page"></a>
        <?php if ( defined( 'ELEMENTOR_VERSION' ) ): ?>
            <a class="dashicons dashicons-wordpress"
                    href="<?php echo admin_url() . 'post.php?post=' . $currentPost; ?>&action=edit"
                    title="Go to WP-Editor"></a>
            <a class="dashicons dashicons-elementor"
                    href="<?php echo admin_url() . 'post.php?post=' . $currentPost; ?>&action=elementor"
                    title="Go to Elementor"></a>
        <?php endif; ?>
    </div>

<?php endif; ?>


<style>
    .admin-quickbar-post {
        position: relative;
    }

    .admin-quickbar-post.is-active {
        border-left: 3px solid #777;
    }

    .admin-quickbar-jumpicons .dashicons.dashicons-wordpress,
    .admin-quickbar-post-options .dashicons.dashicons-wordpress {
        font-size: 16px;
        padding-top: 2px;
    }

    .admin-quickbar-post.is-favorite::before {
        content: '\f154';
        font-family: Dashicons;
        font-size: 12px;
        display: inline-block;
        position: absolute;
        left: -3px;
        top: 12px;
    }

    .admin-quickbar-contextmenu {
        display: none;
        position: fixed;
        width: 280px;
        color: #6d7882;
        border-radius: 5px;
        background: #f3f3f3;
        box-shadow: 3px 3px 3px #1b1c1d21;

        z-index: 999999999;
    }

    .admin-quickbar-contextmenu.open {
        display: block;
    }


    .admin-quickbar-contextmenu > .item {
        display: block;
        position: relative;
        padding: 0 15px;
        height: 30px;
        margin: 7px 0;
        cursor: default;
    }

    .admin-quickbar-contextmenu .item:not(.has-sub):hover {
        background-color: #e6e9ec;
    }

    .admin-quickbar-contextmenu .item.has-sub {
        text-align: right;
    }

    .admin-quickbar-contextmenu .item.has-sub .label {
        display: block;
        position: absolute;
        text-align: left;
        top: 14px;
    }

    .admin-quickbar-contextmenu .subitem {
        position: relative;
        display: inline-block;
        vertical-align: middle;
        width: 30px;
        height: 30px;
        text-align: center;
        font-family: Dashicons;
        background-color: rgba(0, 0, 0, 0.1);
        margin: 0 2px;
    }

    .admin-quickbar-contextmenu .subitem::after {
        display: inline-block;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
    }

    .admin-quickbar-contextmenu .item.item-copy .subitem {
        cursor: copy;
    }

    .admin-quickbar-contextmenu .item.item-favorite .subitem {
        cursor: pointer;
    }

    .admin-quickbar-contextmenu .subitem.item-permalink::after {
        content: "\f177";
    }

    .admin-quickbar-contextmenu .subitem.item-edit::after {
        content: "\f120";
        font-size: 16px;
    }

    .admin-quickbar-contextmenu .subitem.item-id::after {
        content: "\f336";
    }

    .admin-quickbar-contextmenu .subitem.item-elementor::after {
        content: "\e812";
        font-family: eicons;
        font-size: 13px;
        top: calc(50% - 3px);
    }

    .admin-quickbar-contextmenu .subitem.item-shortcode::after {
        content: "\e883";
        font-family: eicons;
        font-size: 13px;
        top: calc(50% - 3px);
    }

    .admin-quickbar-contextmenu .subitem.item-add-favorite::after {
        content: "\f154";
    }

    .admin-quickbar-contextmenu .subitem.item-remove-favorite::after {
        content: "\f155";
    }

    .admin-quickbar-contextmenu .item input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
</style>
<div class="admin-quickbar-contextmenu">

</div>
