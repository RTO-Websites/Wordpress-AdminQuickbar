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
            <?php _e( 'Keep open', $this->textdomain ); ?>
        </label>
        <?php
        //add_theme_support( 'post-thumbnails' );
        $filerPostTypes = explode( ',', 'nav_menu_item,revision,custom_css,customize_changeset,'
            . 'oembed_cache,ocean_modal_window,nxs_qp,elementor_library' );
        $postTypes = get_post_types( array(), 'object' );


        // loop all post-types
        foreach ( $postTypes as $postType ) {
            if ( in_array( $postType->name, $filerPostTypes ) ) {
                continue;
            }
            /*echo '<pre>';
            var_dump( $postType );
            echo '</pre>';*/

            echo '<div class="admin-quickbar-postlist" data-post-type="' . $postType->name . '">';
            echo '<div class="admin-quickbar-post-type">' . $postType->label . '</div>';
            echo '<div class="admin-quickbar-postlist-inner">';

            // get posts of current post-type
            $posts = get_posts( array(
                'post_type' => $postType->name,
                'posts_per_page' => -1,
                'post_status' => 'any',
                'suppress_filters' => false,
            ) );

            // loop posts of current post-type
            foreach ( $posts as $post ) {
                echo '<div class="admin-quickbar-post">';

                // echo thumb
                if ( has_post_thumbnail( $post ) ) {
                    // from post-thumbnail
                    $attachmentId = get_post_thumbnail_id( $post->ID );
                    $path = get_attached_file( $attachmentId );
                    $url = wp_get_attachment_url( $attachmentId );
                } else if ( $post->post_type == 'attachment' ) {
                    // direct from attachment
                    $path = get_attached_file( $post->ID );
                    $url = wp_get_attachment_url( $post->ID );
                } else if ( class_exists( 'Inc\PostGallery' ) ) {
                    // from post-gallery
                    $postGalleryImages = Inc\PostGallery::getImages( $post->ID );
                    if ( count( $postGalleryImages ) ) {
                        $firstThumb = array_shift( $postGalleryImages );
                        $path = $firstThumb['path'];
                    }
                }

                if ( !empty( $path ) && class_exists( 'Inc\PostGallery' ) ) {
                    $path = explode( '/wp-content/', $path );
                    $path = '/wp-content/' . array_pop( $path );

                    $thumbInstance = new Thumb\Thumb();
                    $thumb = $thumbInstance->getThumb( array(
                        'path' => $path,
                        'width' => '150',
                        'height' => '150',
                        'scale' => '0',
                    ) );

                    echo '<img src="'
                        . $thumb['url']
                        . '" alt="" class="attachment-post-thumbnail' . ' wp-post-image  post-image-from-postgallery" />';
                } else if ( !empty( $url ) ) {
                    echo '<img src="'
                        . $url
                        . '" alt="" class="attachment-post-thumbnail' . ' wp-post-image" />';
                }

                // reset vars
                $thumb = null;
                $path = null;
                $url = null;

                // echo post-title
                if ( !empty( $post->post_title ) ) {
                    echo $post->post_title;
                } else if ( !empty( $post->post_name ) ) {
                    echo $post->post_name;
                } else {
                    echo $post->ID;
                }


                echo '<div class="admin-quickbar-post-options">';
                echo '<a class="dashicons dashicons-edit" href="' . admin_url() . 'post.php?post=' . $post->ID . '&action=edit"></a>';

                // add button for elementor-edit
                if ( defined( 'ELEMENTOR_VERSION' ) ) {
                    echo '<a class="dashicons dashicons-elementor" href="' . admin_url() . 'post.php?post=' . $post->ID . '&action=elementor"></a>';
                }

                echo '</div>'; // .admin-quickbar-post-options
                echo '</div>'; // .admin-quickbar-postlist-inner

            }

            echo '</div>';
            echo '</div>';
        }

        ?>
    </div>

    <div class="toggle-quickbar-button"></div>
</div>