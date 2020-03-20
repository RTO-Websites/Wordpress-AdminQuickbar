<?php
/**
 * @var object $postType
 * @var array $postsByCategory
 * @var string $createNewUrl
 */
?>

<div class="admin-quickbar-postlist" data-post-type="<?php echo $postType->name; ?>">
    <div class="admin-quickbar-post-type"><?php echo $postType->label; ?>
        <a class="dashicons dashicons-plus add-new"
                href="<?php echo $createNewUrl; ?>"></a>
    </div>
    <div class="admin-quickbar-postlist-inner">
        <?php foreach ($postsByCategory as $categoryName => $renderedPosts) : ?>
            <?php if ( !$postType->hierarchical ): ?>
                <div class="admin-quickbar-category"><?php echo $categoryName; ?></div>
            <?php endif; ?>
            <?php
            echo $renderedPosts;
            ?>
        <?php endforeach; ?>
    </div>
</div>