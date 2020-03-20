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
 * @var string $deleteUrl
 * @var string $unDeleteUrl
 */
?>
<div class="admin-quickbar-post <?php echo $postClasses . ' ' . $activeClass; ?>"
        data-postid="<?php echo $post->ID; ?>" <?php echo $style; ?>
        data-contextmenu='<?php echo $contextMenuData; ?>'
        data-delete-url="<?php echo $deleteUrl; ?>"
        data-undelete-url="<?php echo $unDeleteUrl; ?>">
    <?php
    echo $thumb;
    ?>
    <span class="label">
        <?php echo $languageFlag; ?>
        <?php echo $postTitle; ?>
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