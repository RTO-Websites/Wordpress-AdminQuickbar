<?php
/**
 * @var string $activeClass
 * @var object $post
 * @var string $style
 * @var string $contextMenuData
 * @var string $thumb
 * @var string $languageFlag
 * @var string $postTitle
 * @var array $postTypeInfo
 * @var string $postClasses
 * @var string $permalink
 * @var string $trashUrl
 * @var string $unTrashUrl
 */
?>
<div class="admin-quickbar-post <?php echo $postClasses . ' ' . $activeClass; ?>"
        data-postid="<?php echo $post->ID; ?>" <?php echo $style; ?>
        data-contextmenu='<?php echo $contextMenuData; ?>'
        data-trash-url="<?php echo $trashUrl; ?>"
        data-untrash-url="<?php echo $unTrashUrl; ?>">
    <?php
    echo $thumb;
    ?>
    <span class="label">
        <?php echo $languageFlag; ?>
        <span class="aqb-post-title" data-postid="<?php echo $post->ID; ?>"><?php echo $postTitle; ?></span>
    </span>
    <div class="admin-quickbar-post-options">
        <a class="aqb-icon aqb-icon-wordpress dashicons-edit"
                href="<?php echo $postTypeInfo['link']; ?>&action=edit"
                title="Go to WP-Editor"></a>


        <?php if ( !$postTypeInfo['noElementor'] ): ?>
            <a class="aqb-icon aqb-icon-elementor"
                    href="<?php echo $postTypeInfo['link']; ?>&action=elementor"
                    title="Go to Elementor"></a>
        <?php endif; ?>

        <?php if ( empty( $postTypeInfo['noView'] ) ): ?>
            <a class="aqb-icon aqb-icon-website"
                    href="<?php echo $permalink; ?>"
                    title="Go to Page"></a>
        <?php endif; ?>
    </div>
</div>