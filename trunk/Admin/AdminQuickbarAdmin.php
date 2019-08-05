<?php namespace AdminQuickbar\Admin;

use AdminQuickbar\Lib\Template;
use Swift_Performance;
use Swift_Performance_Lite;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.rto.de
 * @since      1.0.0
 *
 * @package    AdminQuickbar
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AdminPostListSidebar
 * @subpackage AdminPostListSidebar/admin
 * @author     RTO GmbH
 */
class AdminQuickbarAdmin {

    const PartialDir = AdminQuickbar_DIR . '/Admin/partials/';
    public $filterPostTypes = [];
    public $postTypes = [];
    public $filteredPostTypes = [];
    public $categoryList = [];

    public $cacheList = [];
    private $hasSwift;

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

        $this->categoryList = get_categories();

        add_action( 'admin_print_footer_scripts', [ $this, 'renderSidebar' ] );

        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueStyles' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueScripts' ] );

        add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'enqueueStyles' ] );
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueueScripts' ], 99999 );

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

    public function getCacheList() {
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
        $this->getCacheList();
        $this->setPostTypes();
        $postTypeLoop = $this->getLoopPostTypes();
        $currentPost = filter_input( INPUT_GET, 'post' );
        $permalink = get_permalink( $currentPost );

        $addNewPosts = new Template( self::PartialDir . '/add-new-posts.php', [
            'filteredPostTypes' => $this->filteredPostTypes,
        ] );

        $template = new Template( self::PartialDir . '/admin-quickbar-admin-display.php', [
            'postTypeLoop' => $postTypeLoop,
            'currentPost' => $currentPost,
            'addNewPosts' => $addNewPosts->getRendered(),
            'swiftNonce' => wp_create_nonce( 'swift-performance-ajax-nonce' ),
            'hasSwift' => $this->hasSwift,
            'inCache' => in_array( $permalink, $this->cacheList ),
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
            $countPostType = $posts['count'];
            $categories = $posts['categories'];


            if ( empty( $categories ) || empty( $countPostType ) ) {
                continue;
            }

            $postsByCategory = $this->getLoopCategories( $postType, $categories );

            $template = new Template( self::PartialDir . '/loop-post-types.php', [
                'postType' => $postType,
                'postsByCategory' => $postsByCategory,
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
            $style = $this->getMarginStyle( $post, $postType, $lastParent, $margin );
            $postTypeInfo = $this->getPostTypeInfo( $postType, $post );
            $permalink = get_permalink( $post->ID );

            $template = new Template( self::PartialDir . '/loop-posts.php', [
                'post' => $post,
                'postTypeInfo' => $postTypeInfo,
                'style' => $style,
                'thumb' => $this->getThumb( $post ),
                'postTitle' => $this->getPostTitle( $post ),
                'inCache' => in_array( $permalink, $this->cacheList ),
                'permalink' => $permalink,
                'hasSwift' => $this->hasSwift,
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
            $thumb = $thumbInstance->getThumb( array(
                'path' => $path,
                'width' => '150',
                'height' => '150',
                'scale' => '0',
            ) );

            if ( !empty( $thumb['url'] ) ) {
                $url = $thumb['url'];
                $class .= '  post-image-from-postgallery';
            }
        }

        if ( empty( $url ) ) {
            return '';
        }

        $template = new Template( self::PartialDir . '/thumbnail.php', [
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
        $output = '';
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

        return [
            'link' => $link,
            'noElementor' => $noElementor,
            'noView' => $noView,
        ];
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

        wp_enqueue_script( $this->pluginName, AdminQuickbar_URL . '/Admin/js/admin-quickbar-admin.js', [ 'jquery' ], $this->version, false );

    }

}
