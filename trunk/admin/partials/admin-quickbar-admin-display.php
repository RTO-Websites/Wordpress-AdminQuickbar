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
    <div class="admin-quickbar">
        <div class="admin-quickbar-inner">
            <label for="admin-quickbar-keepopen" class="admin-quickbar-keepopen">
                <input type="checkbox" name="admin-quickbar-keepopen"/>
                <?php _e( 'Keep open', 'admin-quickbar' ); ?>
            </label>
            <label for="admin-quickbar-keepopen" class="admin-quickbar-loadthumbs">
                <input type="checkbox" name="admin-quickbar-keepopen"/>
                <?php _e( 'Show thumbs', 'admin-quickbar' ); ?>
            </label>
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
                        ?>
                        <?php foreach ( $cats as $catName => $posts ): ?>
                            <?php
                            if ( empty( $posts ) ) {
                                continue;
                            }
                            ?>
                            <?php if ( !$postType->hierarchical ): ?>
                                <div class="admin-quickbar-category"><?php echo $catName; ?></div>
                            <?php endif; ?>
                            <?php foreach ( $posts as $post ): ?>
                                <?php
                                $style = $this->getMarginStyle( $post, $postType, $lastParent, $margin );
                                ?>
                                <div class="admin-quickbar-post" <?php echo $style; ?>>
                                    <?php
                                    // echo thumb
                                    $this->renderThumb( $post );

                                    // echo post-title
                                    $this->renderPostTitle( $post );

                                    $postTypeInfo = $this->getPostTypeInfo( $postType, $post );
                                    ?>
                                    <div class="admin-quickbar-post-options">
                                        <a class="dashicons dashicons-edit"
                                                href="<?php echo $link; ?>&action=edit"
                                                title="Go to WP-Editor"></a>


                                        <?php if ( defined( 'ELEMENTOR_VERSION' ) && !$postTypeInfo['noElementor'] ): ?>
                                            <a class="dashicons dashicons-elementor"
                                                    href="<?php echo $link; ?>&action=elementor"
                                                    title="Go to Elementor"></a>
                                        <?php endif; ?>

                                        <?php if ( empty( $postTypeInfo['noView'] ) ): ?>
                                            <a class="dashicons dashicons-visibility"
                                                    href="<?php echo get_permalink( $post->ID ); ?>"
                                                    title="Go to Page"></a>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="toggle-quickbar-button"></div>
    </div>

<?php
$currentPost = filter_input( INPUT_GET, 'post' );
if ( !empty( $currentPost ) ) {
    echo '<div class="admin-quickbar-jumpicons">';
    echo '<a class="dashicons dashicons-visibility" href="' . get_permalink( $currentPost ) . '" title="Go to Page"></a>';
    if ( defined( 'ELEMENTOR_VERSION' ) ) {
        echo '<a class="dashicons dashicons-edit" href="' . admin_url() . 'post.php?post=' . $currentPost . '&action=edit" title="Go to WP-Editor"></a>';
        echo '<a class="dashicons dashicons-elementor" href="' . admin_url() . 'post.php?post=' . $currentPost . '&action=elementor" title="Go to Elementor"></a>';
    }
    echo '</div>';
}
