<?php
/**
 * @var object $postType
 * @var array $postsByCategory
 * @var string $createNewUrl
 * @var string $postTypeCount
 * @var array $categoriesCount
 */
?>

<div class="admin-quickbar-postlist" data-post-type="<?php echo $postType->name; ?>">
    <div class="admin-quickbar-post-type"><?php echo $postType->label; ?>
        <span class="aqb-post-type-count"><?php echo $postTypeCount; ?></span>
        <a class="dashicons dashicons-plus add-new"
                href="<?php echo $createNewUrl; ?>"></a>
    </div>
    <div class="admin-quickbar-postlist-inner">
        <?php foreach ($postsByCategory as $categoryName => $renderedPosts) : ?>
            <?php if ( !$postType->hierarchical ): ?>
                <div class="admin-quickbar-category">
                    <?php echo $categoryName; ?>
                    <span class="aqb-category-count"><?php echo $categoriesCount[$categoryName]; ?></span>
                </div>
            <?php endif; ?>
            <?php
            echo $renderedPosts;
            ?>
        <?php endforeach; ?>
    </div>
</div>