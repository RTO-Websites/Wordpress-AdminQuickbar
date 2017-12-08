<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.hennewelt.de
 * @since      1.0.0
 *
 * @package    AdminPostListSidebar
 * @subpackage AdminPostListSidebar/admin/partials
 */
?>
<div class="admin-quickbar">
    <div class="admin-quickbar-inner">
    <?php

    $filerPostTypes = explode( ',', 'nav_menu_item,attachment,revision,custom_css,customize_changeset,'
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
        ) );

        // loop posts of current post-type
        foreach ( $posts as $post ) {
            echo '<div class="admin-quickbar-post">';

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