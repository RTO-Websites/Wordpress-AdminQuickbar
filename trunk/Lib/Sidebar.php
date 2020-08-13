<?php

namespace AdminQuickbar\Lib;

class Sidebar {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    const PARTIAL_DIR = AdminQuickbar_DIR . '/Lib/partials/';
    private $filterPostTypes = [];
    private $postTypes = [];
    private $filteredPostTypes = [];
    private $categoryList = [];

    private $cacheList = [];
    private $hasSwift;

    private $cssPosts = [];

    private $hasWpml = false;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $pluginName The ID of this plugin.
     */
    private $pluginName;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $pluginName The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct( $pluginName, $version ) {

        $this->pluginName = $pluginName;
        $this->version = $version;

        $this->loader = new Loader();

        $this->defineHooks();

        $this->loader->run();

        $this->categoryList = get_categories();
    }

    /**
     * Register all of the hooks related to the sidebar functionality
     */
    private function defineHooks() {
        $this->loader->addAction( 'wp_enqueue_scripts', $this, 'enqueueStyles' );
        $this->loader->addAction( 'wp_enqueue_scripts', $this, 'enqueueScripts' );
        $this->loader->addAction( 'admin_enqueue_scripts', $this, 'enqueueStyles' );
        $this->loader->addAction( 'admin_enqueue_scripts', $this, 'enqueueScripts' );
        $this->loader->addAction( 'elementor/editor/before_enqueue_styles', $this, 'enqueueStyles' );
        $this->loader->addAction( 'elementor/editor/before_enqueue_scripts', $this, 'enqueueScripts', 99999 );

        // embed to footer
        if ( is_admin() ) {
            $this->loader->addAction( 'admin_print_footer_scripts', $this, 'renderSidebar' );
        } else {
            $this->loader->addAction( 'wp_footer', $this, 'renderSidebar' );
        }
        $this->loader->addAction( 'set_current_user', $this, 'fixElementorLanguage', 11 );
        $this->loader->addAction( 'wp_ajax_aqbRenamePost', $this, 'renamePost' );

    }

    /**
     * Checks if wmpl plugin is active
     *
     * @return bool
     */
    private function checkForWpml() {
        $currentLanguage = apply_filters( 'wpml_current_language', null );
        $defaultLanguage = apply_filters( 'wpml_default_language', null );

        if ( !empty( $currentLanguage ) && !empty( $defaultLanguage )
        ) {
            $this->hasWpml = true;
        }

        return $this->hasWpml;
    }

    /**
     * Set post-types and filtered post-types
     */
    public function setPostTypes() {
        $this->postTypes = get_post_types( [], 'object' );

        $this->filterPostTypes = explode( ',', 'nav_menu_item,revision,custom_css,customize_changeset,'
            . 'oembed_cache,ocean_modal_window,nxs_qp' );

        foreach ( $this->postTypes as $postType ) {
            if ( in_array( $postType->name, $this->filterPostTypes ) ) {
                continue;
            }
            $this->filteredPostTypes[] = $postType;
        }
    }

    /**
     * Get cache list from swift and writes to $cacheList
     */
    public function initCacheList() {
        $this->hasSwift = class_exists( 'Swift_Performance' ) || class_exists( 'Swift_Performance_Lite' );
        if ( !$this->hasSwift ) {
            return;
        }

        $this->cacheList = class_exists( 'Swift_Performance' )
            ? Swift_Performance::cache_status()['files']
            : Swift_Performance_Lite::cache_status()['files'];
    }

    /**
     * Adds the sidebar to footer
     *
     * @param string $data
     * @return string $data
     * @throws \ImagickException
     */
    public function renderSidebar( $data ) {
        $this->checkForWpml();
        $this->initCacheList();
        $this->setPostTypes();
        $postTypeLoop = $this->getLoopPostTypes();
        if ( is_admin() ) {
            $currentPost = filter_input( INPUT_GET, 'post' );
        } else {
            $currentPost = get_the_ID();
        }

        if ( empty( $currentPost ) ) {
            $permalink = get_bloginfo( 'wpurl' );
        } else {
            $permalink = get_permalink( $currentPost );
        }

        $template = new Template( self::PARTIAL_DIR . '/sidebar.php', [
            'postTypeLoop' => $postTypeLoop,
            'filteredPostTypes' => $this->filteredPostTypes,
            'currentPost' => $currentPost,
            'permalink' => $permalink,
            'swiftNonce' => wp_create_nonce( 'swift-performance-ajax-nonce' ),
            'hasSwift' => $this->hasSwift,
            'inCache' => in_array( $permalink, $this->cacheList ),
            'languageFlags' => $this->renderAllLanguageFlags(),
        ] );
        $template->render();

        $template = new Template( self::PARTIAL_DIR . '/jump-icons.php', [
            'currentPost' => $currentPost,
            'permalink' => $permalink,
            'swiftNonce' => wp_create_nonce( 'swift-performance-ajax-nonce' ),
            'hasSwift' => $this->hasSwift && !empty( $currentPost ),
            'inCache' => in_array( $permalink, $this->cacheList ),
            'cssPosts' => array_reverse( $this->cssPosts ),
        ] );
        $template->render();

        return $data;
    }

    /**
     * Gets rendered post-type loop
     *
     * @return string
     * @throws \ImagickException
     */
    public function getLoopPostTypes() {
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

            $postsByCategory = $this->getLoopCategories( $postType, $categories );

            $template = new Template( self::PARTIAL_DIR . '/loop-post-types.php', [
                'postType' => $postType,
                'postsByCategory' => $postsByCategory,
                'createNewUrl' => $createNewUrl,
            ] );
            $output .= $template->getRendered();
        }

        return $output;
    }

    /**
     * Get rendered category loop
     *
     * @param $postType
     * @param $categories
     * @return array
     * @throws \ImagickException
     */
    public function getLoopCategories( $postType, $categories ) {
        $output = [];

        foreach ( $categories as $categoryName => $posts ) {
            if ( empty( $posts ) ) {
                continue;
            }
            if ( $postType->name === 'custom-css' ) {
                $this->cssPosts = $posts;
            }

            $output[$categoryName] = $this->getLoopPosts( $postType, $posts );
        }

        return $output;
    }

    /**
     * Get rendered posts loop
     *
     * @param $postType
     * @param $posts
     * @return string
     * @throws \ImagickException
     */
    public function getLoopPosts( $postType, $posts ) {
        $output = '';
        foreach ( $posts as $post ) {
            if ( $post->post_name === 'default-kit' ) {
                continue;
            }
            $style = $this->getMarginStyle( $post, $postType, $lastParent, $margin );
            $postTypeInfo = $this->getPostTypeInfo( $postType, $post );
            $permalink = get_permalink( $post->ID );
            $activeClass = filter_input( INPUT_GET, 'post' ) == $post->ID ? ' is-active' : '';
            $trashUrl = admin_url() . wp_nonce_url( "post.php?action=trash&amp;post=$post->ID", 'trash-post_' . $post->ID );
            $unTrasUrl = admin_url() . wp_nonce_url( "post.php?action=untrash&amp;post=$post->ID", 'untrash-post_' . $post->ID );

            $postClasses = ' post-status-' . $post->post_status;
            if ( !empty( $post->post_password ) ) {
                $postClasses .= ' has-password';
            }
            $languageFlag = $this->getLanguageFlag( $post );

            $template = new Template( self::PARTIAL_DIR . '/loop-posts.php', [
                'post' => $post,
                'postTypeInfo' => $postTypeInfo,
                'contextMenuData' => json_encode( $this->getContextMenuData( $postType, $post, $postTypeInfo ) ),
                'style' => $style,
                'thumb' => $this->getThumb( $post ),
                'postTitle' => $this->getPostTitle( $post ),
                'inCache' => in_array( $permalink, $this->cacheList ),
                'permalink' => $permalink,
                'hasSwift' => $this->hasSwift,
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

    /**
     * @param $post
     * @return string
     */
    public function getLanguageFlag( $post ) {
        if ( !$this->hasWpml ) {
            return '';
        }

        global $sitepress;
        $wpmlLanguageInfo = apply_filters( 'wpml_post_language_details', null, $post->ID );
        $languageCode = $wpmlLanguageInfo['language_code'];
        $flagUrl = $sitepress->get_flag_url( $languageCode );

        $template = new Template( self::PARTIAL_DIR . '/language-flag.php', [
            'flagUrl' => $flagUrl,
            'alt' => $wpmlLanguageInfo['display_name'],
            'languageCode' => $languageCode,
        ] );

        return $template->getRendered();
    }

    /**
     * Renders all wpml language-flags
     *
     * @return string
     */
    public function renderAllLanguageFlags() {
        $output = '';

        if ( !$this->hasWpml || empty( $wpmlLanguages = apply_filters( 'wpml_active_languages', null ) ) ) {
            return '';
        }

        foreach ( $wpmlLanguages as $language ) {
            $template = new Template( self::PARTIAL_DIR . '/language-flag.php', [
                'flagUrl' => $language['country_flag_url'],
                'alt' => $language['native_name'],
                'languageCode' => $language['language_code'],
            ] );
            $output .= $template->getRendered();
        }

        return $output;
    }

    /**
     * Returns post-thumb html
     *
     * @param \WP_Post $post
     * @return string
     * @throws \ImagickException
     */
    public function getThumb( $post ) {
        $class = '';
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
            $postGalleryImages = \Lib\PostGalleryImageList::get( $post->ID );
            if ( count( $postGalleryImages ) ) {
                $firstThumb = array_shift( $postGalleryImages );
                $path = $firstThumb['path'];
            }
        }

        if ( !empty( $path ) && class_exists( 'Lib\Thumb' ) ) {
            $path = explode( '/wp-content/', $path );
            $path = '/wp-content/' . array_pop( $path );

            $thumbInstance = new \Lib\Thumb();
            $thumb = $thumbInstance->getThumb( [
                'path' => $path,
                'width' => '150',
                'height' => '150',
                'scale' => '0',
            ] );

            if ( !empty( $thumb['url'] ) ) {
                $url = $thumb['url'];
                $class .= '  post-image-from-postgallery';
            }
        }

        if ( empty( $url ) ) {
            return '';
        }

        $template = new Template( self::PARTIAL_DIR . '/thumbnail.php', [
            'url' => $url,
            'class' => $class,
        ] );

        return $template->getRendered();
    }

    /**
     * Get post-title, or post-name/post-id if empty
     *
     * @param \WP_Post $post
     * @return string
     */
    public function getPostTitle( $post ) {
        if ( !empty( $post->post_title ) ) {
            $output = $post->post_title;
        } else if ( !empty( $post->post_name ) ) {
            $output = $post->post_name;
        } else {
            $output = $post->ID;
        }

        return $output;
    }

    /**
     * @param \WP_Post $post
     * @param \WP_Post_Type $postType
     * @param int $lastParent
     * @param int $margin
     * @return string
     */
    public function getMarginStyle( $post, $postType, &$lastParent, &$margin = 0 ) {
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

        return $style;
    }

    /**
     * Get posts by post-type
     *
     * @param $postType
     * @return array
     */
    public function getPostsByPostType( $postType ) {
        $countPostType = 0;
        $categories = [];

        if ( $this->hasWpml ) {
            do_action( 'wpml_switch_language', 'all' );
        }
        // get posts of current post-type
        $args = [
            'post_type' => $postType->name,
            'posts_per_page' => -1,
            'suppress_filters' => false,
            'post_status' => get_post_stati(),
            'orderby' => $postType->hierarchical ? [ 'parent' => 'ASC', 'menu_order' => 'ASC' ] : 'menu_order',
            'order' => 'ASC',
        ];
        if ( $postType->hierarchical ) {
            $posts = get_pages( $args );
            $categories = [
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

            foreach ( $this->categoryList as $category ) {
                $args['category'] = $category->term_id;
                $categories[$category->name] = get_posts( $args );
                $count += count( $categories[$category->name] );
            }
            $countPostType += $count;

            if ( !$count ) {
                unset( $args['category'] );
                $categories[__( 'Uncategorized' )] = get_posts( $args );
                $countPostType += count( $categories[__( 'Uncategorized' )] );
            }
        }

        return [
            'count' => $countPostType,
            'categories' => $categories,
        ];
    }

    /**
     * Returns post-edit-link, and info if elementor is available
     *
     * @param \WP_Post_Type $postType
     * @param \WP_Post $post
     * @return array
     */
    public function getPostTypeInfo( $postType, $post ) {
        $noElementor = false;
        $noView = false;
        $link = admin_url() . 'post.php?post=' . $post->ID;

        switch ( $postType->name ) {
            case 'wpcf7':
            case 'wpcf7_contact_form':
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
                $noElementor = true;
                $noView = true;
                break;

        }

        if ( !defined( 'ELEMENTOR_VERSION' ) ) {
            $noElementor = true;
        }

        return [
            'link' => $link,
            'noElementor' => $noElementor,
            'noView' => $noView,
        ];
    }

    /**
     * Returns data-attributes based on post-type
     *
     * @param \WP_Post_Type $postType
     * @param \WP_Post $post
     * @param array $postTypeInfo
     * @return array
     */
    public function getContextMenuData( $postType, $post, $postTypeInfo ) {
        $data = [
            'favorite' => true,
            'copy' => [
                'id' => $post->ID,
                'wordpress' => $postTypeInfo['link'] . '&action=edit',
                'elementor' => empty( $postTypeInfo['noElementor'] ) ? $postTypeInfo['link'] . '&action=elementor' : '',
                'website' => get_permalink( $post->ID ),
            ],
        ];

        if ( $this->hasSwift ) {
            $permalink = get_the_permalink( $post->ID );
            $data['swift'] = [
                'inCache' => in_array( $permalink, $this->cacheList ),
                'permalink' => $permalink,
            ];
        }

        if ( $postType->name !== 'attachment' ) {
            $data['trash'] = [
                'id' => $post->ID,
            ];
            $data['rename'] = [
                'id' => $post->ID,
            ];
        }

        switch ( $postType->name ) {
            case 'elementor_library':
                $data['copy']['shortcode'] = '[elementor-template id=' . $post->ID . ']';
                break;
        }

        return $data;
    }

    /**
     * Rename a post
     *  Typically called via admin-ajax
     */
    public function renamePost() {
        $postid = filter_input( INPUT_POST, 'postid' );
        $title = filter_input( INPUT_POST, 'title' );

        if ( empty( $postid ) || empty( $title ) ) {
            var_dump( $postid );
            var_dump( $title );
            wp_die();
        }

        wp_update_post( [
            'ID' => $postid,
            'post_title' => $title,
        ] );
        wp_die();
    }


    /**
     * Fix wrong language in elementor
     */
    public function fixElementorLanguage() {
        global $current_user;
        $userLocale = get_user_meta( get_current_user_id(), 'locale', true );
        $current_user->locale = $userLocale;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueueStyles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in AdminPostListSidebarLoader as all of the hooks are defined
         * in that particular class.
         *
         * The AdminPostListSidebarLoader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->pluginName, AdminQuickbar_URL . '/Admin/css/admin-quickbar-admin.min.css', [], $this->version, 'all' );
        if ( !is_admin() ) {
            wp_enqueue_style( 'dashicons' );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueueScripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in AdminPostListSidebarLoader as all of the hooks are defined
         * in that particular class.
         *
         * The AdminPostListSidebarLoader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( $this->pluginName, AdminQuickbar_URL . '/Admin/js/build.min.js', [ 'jquery' ], $this->version, true );

        wp_localize_script( $this->pluginName, 'aqbLocalize',
            [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] );
    }
}