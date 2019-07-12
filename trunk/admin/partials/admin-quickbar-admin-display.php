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
            <?php
            //add_theme_support( 'post-thumbnails' );
            $filterPostTypes = explode( ',', 'nav_menu_item,revision,custom_css,customize_changeset,'
                . 'oembed_cache,ocean_modal_window,nxs_qp' );
            $postTypes = get_post_types( array(), 'object' );

            echo '<br />';
            echo '<select class="admin-quickbar-new-select">';
            // loop all post-types for add new buttons
            foreach ( $postTypes as $postType ) {
                if ( in_array( $postType->name, $filterPostTypes ) ) {
                    continue;
                }
                echo '<option value="' . $postType->name . '">' . $postType->label . '</option>';
            }
            echo '</select>';
            ?>

            <a class="button-secondary add-post-button" href="#"
                    onclick="window.location.href='<?php echo admin_url( 'post-new.php' ); ?>?post_type=' + jQuery('.admin-quickbar-new-select').val();return false;"></a>

            <?php
            $categoryList = get_categories();
            // loop all post-types
            foreach ( $postTypes as $postType ) {
                if ( in_array( $postType->name, $filterPostTypes ) ) {
                    continue;
                }
                $countPostType = 0;

                $cats = [];

                // get posts of current post-type
                $args = [
                    'post_type' => $postType->name,
                    'posts_per_page' => -1,
                    'suppress_filters' => false,
                    'orderby' => $postType->hierarchical ? [ 'parent' => 'ASC', 'menu_order' => 'ASC' ] : 'menu_order',
                    'order' => 'ASC',
                ];
                if ( $postType->hierarchical ) {
                    $posts = get_pages( $args );
                    $cats = [
                        'none' => $posts,
                    ];
                    $countPostType += count( $posts );
                } else {
                    $count = 0;
                    $args = $args + [
                            'post_status' => 'any',
                            // workaround for elementor
                            'meta_key' => 'blub54315321',
                            'meta_compare' => 'NOT EXISTS',
                        ];

                    foreach ( $categoryList as $category ) {
                        $args['category'] = $category->term_id;
                        $cats[$category->name] = get_posts( $args );
                        $count += count( $cats[$category->name] );
                    }
                    $countPostType += $count;

                    if ( !$count ) {
                        unset( $args['category'] );
                        $cats[__( 'Uncategorized' )] = get_posts( $args );
                        $countPostType += count( $cats[__( 'Uncategorized' )] );
                    }
                }

                if ( empty( $cats ) || empty( $countPostType ) ) {
                    continue;
                }
                echo '<div class="admin-quickbar-postlist" data-post-type="' . $postType->name . '">';
                echo '<div class="admin-quickbar-post-type">' . $postType->label
                    . '<a class="dashicons dashicons-plus add-new" href="' . admin_url( 'post-new.php' ) . '?post_type=' . $postType->name . '"></a>'
                    . '</div>';
                echo '<div class="admin-quickbar-postlist-inner">';

                $margin = 0;
                $lastParent = 0;
                // loop posts of current post-type
                foreach ( $cats as $catName => $posts ) {
                    if ( empty( $posts ) ) {
                        continue;
                    }
                    if ( !$postType->hierarchical ) {
                        echo '<div class="admin-quickbar-category">' . $catName . '</div>';
                    }
                    foreach ( $posts as $post ) {
                        $style = '';
                        if ( empty( $post->post_parent ) ) {
                            $margin = 0;
                        } else if ( $post->post_parent === $lastParent ) {
                            // do nothing
                        } else {
                            // has parent, not same as before
                            $margin += 10;
                            $lastParent = $post->post_parent;
                        }

                        if ( !empty( $margin ) && $postType->hierarchical ) {
                            $style = ' style="margin-left:' . $margin . 'px;" ';
                        }

                        echo '<div class="admin-quickbar-post" ' . $style . '>';

                        // echo thumb
                        if ( has_post_thumbnail( $post ) ) {
                            // from post-thumbnail
                            $attachmentId = get_post_thumbnail_id( $post->ID );
                            $path = get_attached_file( $attachmentId );
                            $url = wp_get_attachment_image_src( $attachmentId, 'thumbnail' );
                            $url = !empty( $url ) ? $url[0] : '';
                        } else if ( $post->post_type == 'attachment' ) {
                            // direct from attachment
                            $path = get_attached_file( $post->ID );
                            $url = wp_get_attachment_image_src( $post->ID, 'thumbnail' );
                            $url = !empty( $url ) ? $url[0] : '';
                        }

                        if ( empty( $url ) && class_exists( 'Lib\PostGalleryImageList' ) ) {
                            // from post-gallery
                            $postGalleryImages = Lib\PostGalleryImageList::get( $post->ID );
                            if ( count( $postGalleryImages ) ) {
                                $firstThumb = array_shift( $postGalleryImages );
                                $path = $firstThumb['path'];
                            }
                        }

                        if ( !empty( $path ) && class_exists( 'Lib\Thumb' ) ) {
                            $path = explode( '/wp-content/', $path );
                            $path = '/wp-content/' . array_pop( $path );

                            $thumbInstance = new Lib\Thumb();
                            $thumb = $thumbInstance->getThumb( array(
                                'path' => $path,
                                'width' => '150',
                                'height' => '150',
                                'scale' => '0',
                            ) );

                            if ( !empty( $thumb['url'] ) ) {
                                echo '<img src="" data-src="'
                                    . $thumb['url']
                                    . '" alt="" class="attachment-post-thumbnail' . ' wp-post-image  post-image-from-postgallery" />';
                            }
                        } else if ( !empty( $url ) ) {
                            echo '<img src="" data-src="'
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

                        $noElementor = false;
                        $noView = false;
                        $link = admin_url() . 'post.php?post=' . $post->ID;

                        switch ($postType->name) {
                            case 'wpcf7':
                                $link = admin_url() . 'admin.php?page=wpcf7&post=' . $post->ID;
                                $noElementor = true;
                                break;


                            case 'attachment':
                                $noElementor = true;
                                break;

                            case 'elementor_font':
                            case 'elebee-global-css':
                            case 'postgalleryslider':
                            case 'acf-field-group':
                            case 'attachment':
                                $noElementor = true;
                                $noView = true;
                                break;

                        }

                        echo '<div class="admin-quickbar-post-options">';
                        echo '<a class="dashicons dashicons-edit" href="' . $link . '&action=edit" title="Go to WP-Editor"></a>';

                        // add button for elementor-edit
                        if ( defined( 'ELEMENTOR_VERSION' ) && !$noElementor ) {
                            echo '<a class="dashicons dashicons-elementor" href="' . $link . '&action=elementor" title="Go to Elementor"></a>';
                        }

                        if (empty($noView)) {
                            echo '<a class="dashicons dashicons-visibility" href="' . get_permalink( $post->ID ) . '" title="Go to Page"></a>';
                        }

                        echo '</div>'; // .admin-quickbar-post-options
                        echo '</div>'; // .admin-quickbar-postlist-inner

                    }
                }

                echo '</div>';
                echo '</div>';
            }

            ?>
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
