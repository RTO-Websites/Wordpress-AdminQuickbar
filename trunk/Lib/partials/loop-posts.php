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
<div class="admin-quickbar-post <?php echo esc_attr($postClasses) . ' ' . esc_attr($activeClass); ?>"
        data-postid="<?php echo esc_attr($post->ID); ?>" <?php echo esc_attr($style); ?>
        data-contextmenu='<?php echo esc_attr($contextMenuData); ?>'
        data-trash-url="<?php echo esc_url($trashUrl); ?>"
        data-untrash-url="<?php echo esc_url($unTrashUrl); ?>">
    <?php
    echo wp_kses_post($thumb);
    ?>
    <span class="label">
        <?php echo wp_kses_post($languageFlag); ?>
        <span class="aqb-post-title" data-postid="<?php echo esc_attr($post->ID); ?>"><?php echo esc_attr($postTitle); ?></span>
    </span>
    <div class="admin-quickbar-post-options">
        <a class="aqb-icon aqb-icon-wordpress dashicons-edit"
                href="<?php echo esc_url($postTypeInfo['link']); ?>&action=edit"
                title="Go to WP-Editor"></a>


        <?php if ( !$postTypeInfo['noElementor'] ): ?>
            <a class="aqb-icon aqb-icon-elementor"
                    href="<?php echo esc_url($postTypeInfo['link']); ?>&action=elementor"
                    title="Go to Elementor"></a>
        <?php endif; ?>

        <?php if ( empty( $postTypeInfo['noView'] ) ): ?>
            <a class="aqb-icon aqb-icon-website"
                    href="<?php echo esc_url($permalink); ?>"
                    title="Go to Page"></a>
        <?php endif; ?>
    </div>
</div>