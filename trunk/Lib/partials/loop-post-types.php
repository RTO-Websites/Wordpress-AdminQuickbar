<?php
/**
 * @var object $postType
 * @var array $postsByCategory
 * @var string $createNewUrl
 * @var string $postTypeCount
 * @var array $categoriesCount
 */
?>

<div class="admin-quickbar-postlist" data-post-type="<?php echo esc_attr($postType->name); ?>">
    <div class="admin-quickbar-post-type"><?php echo esc_attr($postType->label); ?>
        <span class="aqb-post-type-count"><?php echo esc_attr($postTypeCount); ?></span>
        <a class="dashicons dashicons-plus add-new"
                href="<?php echo esc_url($createNewUrl); ?>"></a>
    </div>
    <div class="admin-quickbar-postlist-inner">
        <?php foreach ($postsByCategory as $categoryName => $renderedPosts) : ?>
            <?php if ( !$postType->hierarchical ): ?>
                <div class="admin-quickbar-category">
                    <?php echo esc_attr($categoryName); ?>
                    <span class="aqb-category-count"><?php echo esc_attr($categoriesCount[$categoryName]); ?></span>
                </div>
            <?php endif; ?>
            <?php
            echo wp_kses_post($renderedPosts);
            ?>
        <?php endforeach; ?>
    </div>
</div>