
<?php foreach ( $this->postTypes as $postType ): ?>
    <?php
    if ( in_array( $postType->name, $this->filterPostTypes ) ) {
        continue;
    }

    $posts = $this->getPostsByPostType( $postType );
    $countPostType = $posts['count'];
    $cats = $posts['cats'];


    if ( empty( $cats ) || empty( $countPostType ) ) {
        continue;
    }
    ?>
    <div class="admin-quickbar-postlist" data-post-type="<?php echo $postType->name; ?>">
        <div class="admin-quickbar-post-type"><?php echo $postType->label; ?>
            <a class="dashicons dashicons-plus add-new"
                href="<?php echo admin_url( 'post-new.php' ) . '?post_type=' . $postType->name; ?>"></a>
        </div>
        <div class="admin-quickbar-postlist-inner">
            <?php
            $margin = 0;
            $lastParent = 0;
            // loop posts of current post-type
            include('loop-categories.php');
            ?>
        </div>
    </div>
<?php endforeach; ?>