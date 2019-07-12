<div class="admin-quickbar-postlist" data-post-type="<?php echo $postType->name; ?>">
    <div class="admin-quickbar-post-type"><?php echo $postType->label; ?>
        <a class="dashicons dashicons-plus add-new"
                href="<?php echo admin_url( 'post-new.php' ) . '?post_type=' . $postType->name; ?>"></a>
    </div>
    <div class="admin-quickbar-postlist-inner">
        <?php
        // loop categories of current post-type
        $this->renderLoopCategories( $postType, $cats );
        ?>
    </div>
</div>