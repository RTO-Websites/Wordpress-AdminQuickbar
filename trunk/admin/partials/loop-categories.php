<?php foreach ( $cats as $catName => $posts ): ?>
    <?php
    if ( empty( $posts ) ) {
        continue;
    }
    ?>
    <?php if ( !$postType->hierarchical ): ?>
        <div class="admin-quickbar-category"><?php echo $catName; ?></div>
    <?php endif; ?>
    <?php include('loop-posts.php'); ?>
<?php endforeach; ?>