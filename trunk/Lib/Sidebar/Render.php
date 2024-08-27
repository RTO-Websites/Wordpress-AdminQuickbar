<?php declare( strict_types=1 );

namespace AdminQuickbar\Lib\Sidebar;

use AdminQuickbar\Lib\Settings;
use AdminQuickbar\Lib\Template;
use AdminQuickbar\Lib\Toolbar;

trait Render {

    /**
     * Adds the sidebar to footer
     *
     * @param string $data
     * @throws \ImagickException
     */
    public function renderSidebar( $data ): string {
        $this->initCacheList();
        $this->setPostTypes();
        $this->setFilteredPostTypes();
        $postTypeLoop = $this->getRenderedPostTypeList();

        $currentPost = is_admin()
            ? filter_input( INPUT_GET, 'post' )
            : get_the_ID();

        $permalink = empty( $currentPost )
            ? get_bloginfo( 'wpurl' )
            : get_permalink( $currentPost );


        $templateVars = [
            'postTypeLoop' => $postTypeLoop,
            'filteredPostTypes' => $this->filteredPostTypes,
            'currentPost' => $currentPost,
            'permalink' => $permalink,
            'swiftNonce' => wp_create_nonce( 'swift-performance-ajax-nonce' ),
            'hasSwift' => $this->hasSwift(),
            'inCache' => in_array( $permalink, $this->cacheList ),
            'languageFlags' => $this->renderAllLanguageFlags(),
            'cssPosts' => array_reverse( $this->cssPosts ),
        ];


        $settings = new Settings( $this->settings );
        $toolbar = new Toolbar( $templateVars );

        $templateVars['settings'] = $settings->getRendered();
        $templateVars['toolbar'] = $toolbar->getRendered();

        $template = new Template( self::PARTIAL_DIR . '/sidebar.php', $templateVars );
        $template->render();

        return $data;
    }


    /**
     * @throws \ImagickException
     */
    public function getRenderedPostTypeList(): string {
        $output = '';
        foreach ( $this->postTypes as $postType ) {
            if ( in_array( $postType->name, $this->filterPostTypes ) ) {
                continue;
            }

            $posts = $this->getPostsByPostType( $postType );

            switch ( $postType->name ) {
                case 'elementor_library':
                    $createNewUrl = admin_url( 'edit.php' ) . '?post_type=elementor_library';
                    break;
                default:
                    $createNewUrl = admin_url( 'post-new.php' ) . '?post_type=' . $postType->name;
                    break;
            }

            $countPostType = $posts['count'];
            $categories = $posts['categories'];


            if ( empty( $categories ) || empty( $countPostType ) ) {
                continue;
            }

            $postsByCategory = $this->getRenderedCategoriesAsArray( $postType, $categories );

            $template = new Template( self::PARTIAL_DIR . '/loop-post-types.php', [
                'postType' => $postType,
                'postTypeCount' => $countPostType,
                'postsByCategory' => $postsByCategory,
                'createNewUrl' => $createNewUrl,
                'categoriesCount' => $posts['categoryCount'],
            ] );
            $output .= $template->getRendered();
        }

        return $output;
    }

    /**
     * @throws \ImagickException
     */
    public function getRenderedCategoriesAsArray( $postType, array $categories ): array {
        $output = [];

        foreach ( $categories as $categoryName => $posts ) {
            if ( empty( $posts ) ) {
                continue;
            }
            if ( $postType->name === 'custom-css' ) {
                $this->cssPosts = $posts;
            }

            $output[$categoryName] = $this->getRenderedPostsList( $postType, $posts );
        }

        return $output;
    }

    /**
     * @throws \ImagickException
     */
    public function getRenderedPostsList( $postType, array $posts ): string {
        $output = '';
        foreach ( $posts as $post ) {
            if ( in_array( $post->post_name, $this->filterPosts ) ) {
                continue;
            }
            $style = $this->getMarginStyle( $post, $postType, $lastParent, $margin );
            $postTypeInfo = $this->getPostTypeInfo( $postType, $post );
            $permalink = get_permalink( $post );
            $activeClass = filter_input( INPUT_GET, 'post' ) == $post->ID ? ' is-active' : '';
            $trashUrl = admin_url() . wp_nonce_url( "post.php?action=trash&amp;post=$post->ID", 'trash-post_' . $post->ID );
            $unTrasUrl = admin_url() . wp_nonce_url( "post.php?action=untrash&amp;post=$post->ID", 'untrash-post_' . $post->ID );

            $postClasses = ' post-status-' . $post->post_status;
            if ( !empty( $post->post_password ) ) {
                $postClasses .= ' has-password';
            }
            $languageFlag = $this->getRenderedLanguageFlag( $post );

            $template = new Template( self::PARTIAL_DIR . '/loop-posts.php', [
                'post' => $post,
                'postTypeInfo' => $postTypeInfo,
                'contextMenuData' => json_encode( $this->getContextMenuData( $postType, $post, $postTypeInfo, $permalink ) ),
                'style' => $style,
                'thumb' => $this->getRenderedPostThumbnail( $post ),
                'postTitle' => $this->getPostTitle( $post ),
                'inCache' => in_array( $permalink, $this->cacheList ),
                'permalink' => $permalink,
                'hasSwift' => $this->hasSwift(),
                'activeClass' => $activeClass,
                'languageFlag' => $languageFlag,
                'postClasses' => $postClasses,
                'trashUrl' => $trashUrl,
                'unTrashUrl' => $unTrasUrl,
            ] );
            $output .= $template->getRendered();
        }

        return $output;
    }


    public function getPostThumbnailUrlCached( $post ): string {
        if ( !empty( $this->transistentThumbnailUrls[$post->ID] ) ) {
            return $this->transistentThumbnailUrls[$post->ID];
        }

        if ( has_post_thumbnail( $post ) ) {
            // from post-thumbnail
            $attachmentId = get_post_thumbnail_id( $post );
            $url = wp_get_attachment_image_src( $attachmentId, 'thumbnail' );
            $url = !empty( $url ) ? $url[0] : '';
        } else if ( $post->post_type == 'attachment' ) {
            // direct from attachment
            $url = wp_get_attachment_image_src( $post->ID, 'thumbnail' );
            $url = !empty( $url ) ? $url[0] : '';
        }

        $this->transistentThumbnailUrls[$post->ID] = empty($url) ? 'none' : $url;
        set_transient( 'aqb_thumbnails', $this->transistentThumbnailUrls, 3600 );

        return !is_string( $url ) ? '' : $url;
    }


    /**
     * @param \WP_Post $post
     * @throws \ImagickException
     */
    public function getRenderedPostThumbnail( $post ): string {
        if ( empty( $this->settings['loadThumbs'] ) ) {
            return '';
        }

        $class = '';

        $url = $this->getPostThumbnailUrlCached( $post );

        if ( empty( $url ) || $url === 'none' ) {
            return '';
        }

        $template = new Template( self::PARTIAL_DIR . '/thumbnail.php', [
            'url' => $url,
            'class' => $class,
        ] );

        return $template->getRendered();
    }

}