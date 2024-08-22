<?php

namespace AdminQuickbar\Lib;

class Sidebar {
    protected Loader $loader;

    const PARTIAL_DIR = AdminQuickbar_DIR . '/Lib/partials/';


    private array $postTypes = [];
    private array $filteredPostTypes = [];
    private array $categoryList = [];

    private array $cacheList = [];

    private array $cssPosts = [];

    /**
     * List of post-names that should not be displayed and filtered out
     *
     * @var string[]
     */
    private array $filterPosts = [
        'default-kit', // used from elementor for theme-style
    ];

    /**
     * List of post-types that should not be displayed and filtered out
     * @var string[]
     */
    private array $filterPostTypes = [
        'nav_menu_item',
        'revision',
        #'custom_css',
        'customize_changeset',
        'oembed_cache',
        'ocean_modal_window',
        'nxs_qp',
        'wp_global_styles',
        'acf-field',
    ];

    private string $pluginName;

    private string $version;

    public function __construct( string $pluginName, string $version ) {

        $this->pluginName = $pluginName;
        $this->version = $version;

        $this->loader = new Loader();

        $this->defineHooks();

        $this->loader->run();


        foreach ( get_categories() as $category ) {
            $this->categoryList[$category->term_id] = $category;
        }
    }

    private function defineHooks(): void {
        $this->loader->addAction( 'wp_enqueue_scripts', $this, 'enqueueStyles' );
        $this->loader->addAction( 'wp_enqueue_scripts', $this, 'enqueueScripts' );
        $this->loader->addAction( 'admin_enqueue_scripts', $this, 'enqueueStyles' );
        $this->loader->addAction( 'admin_enqueue_scripts', $this, 'enqueueScripts' );
        $this->loader->addAction( 'elementor/editor/before_enqueue_styles', $this, 'enqueueStyles' );
        $this->loader->addAction( 'elementor/editor/before_enqueue_scripts', $this, 'enqueueScripts', 99999 );

        // embed to footer on admin
        $this->loader->addAction( 'admin_print_footer_scripts', $this, 'renderSidebar' );

        // embed to footer on website
        $this->loader->addAction( 'wp_footer', $this, 'renderSidebar' );

        $this->loader->addAction( 'set_current_user', $this, 'fixElementorLanguage', 11 );
        $this->loader->addAction( 'wp_ajax_aqbRenamePost', $this, 'renamePost' );

    }

    private function isWpmlActive(): bool {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        return is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' );
    }

    public function setPostTypes(): void {
        $this->postTypes = get_post_types( [], 'object' );
    }

    public function setFilteredPostTypes(): void {
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
    public function initCacheList(): void {
        if ( !$this->hasSwift() ) {
            return;
        }

        $this->cacheList = class_exists( 'Swift_Performance' )
            ? \Swift_Performance::cache_status()['files']
            : \Swift_Performance_Lite::cache_status()['files'];
    }

    private function hasSwift(): bool {
        return class_exists( 'Swift_Performance' ) || class_exists( 'Swift_Performance_Lite' );
    }

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


        $settings = new Settings( [
            'filteredPostTypes' => $this->filteredPostTypes,
        ] );
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
            $permalink = get_permalink( $post->ID );
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
                'contextMenuData' => json_encode( $this->getContextMenuData( $postType, $post, $postTypeInfo ) ),
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

    public function getRenderedLanguageFlag( $post ): string {
        if ( !$this->isWpmlActive() ) {
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

    public function renderAllLanguageFlags(): string {
        $output = '';
        $wpmlLanguages = apply_filters( 'wpml_active_languages', null );
        if ( !$this->isWpmlActive() || empty( $wpmlLanguages ) ) {
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
     * @param \WP_Post $post
     * @throws \ImagickException
     */
    public function getRenderedPostThumbnail( $post ): string {
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
     * @param \WP_Post $post
     */
    public function getPostTitle( $post ): string {
        if ( !empty( $post->post_title ) ) {
            return $post->post_title;
        }
        if ( !empty( $post->post_name ) ) {
            return $post->post_name;
        }
        return $post->ID;
    }

    /**
     * @param \WP_Post $post
     * @param \WP_Post_Type $postType
     * @param int $lastParent
     * @param int $margin
     */
    public function getMarginStyle( $post, $postType, &$lastParent, &$margin = 0 ): string {
        $style = '';

        if ( empty( $post->post_parent ) ) {
            $margin = 0;
        } else if ( $post->post_parent !== $lastParent ) {
            $margin += 10;
            $lastParent = $post->post_parent;
        }

        if ( !empty( $margin ) && $postType->hierarchical ) {
            $style = ' style="margin-left:' . $margin . 'px;" ';
        }

        return $style;
    }

    public function getPostsByPostType( $postType ): array {
        global $wpdb;
        $countPostType = 0;
        $categories = [];

        if ( $this->isWpmlActive() ) {
            do_action( 'wpml_switch_language', 'all' );
        }

        $categoryCount = [];
        if ( $postType->hierarchical ) {
            $queryString = "
                SELECT 
                    $wpdb->posts.ID,
                    $wpdb->posts.post_title,
                    $wpdb->posts.post_status,
                    $wpdb->posts.post_type
                FROM $wpdb->posts
                WHERE $wpdb->posts.post_type = '$postType->name'
                ORDER BY `post_parent` ASC, menu_order ASC
             ";

            $pages = $wpdb->get_results( $queryString, OBJECT );
            $categories = [
                'none' => $pages,
            ];
            $countPostType += count( $pages );
        } else {
            $queryString = "
                SELECT 
                    $wpdb->posts.ID,
                    $wpdb->posts.post_title,
                    $wpdb->posts.post_status,
                    $wpdb->posts.post_type,
                    GROUP_CONCAT($wpdb->term_relationships.term_taxonomy_id) as post_category
                FROM $wpdb->posts
                    JOIN wp_term_relationships on $wpdb->posts.ID = $wpdb->term_relationships.object_id
                WHERE $wpdb->posts.post_type = '$postType->name'
                GROUP BY $wpdb->posts.ID
                ORDER BY menu_order ASC
             ";

            $allPosts = $wpdb->get_results( $queryString, OBJECT );

            $templateTypesByPostId = $this->getTemplateTypesByPostId( $postType, $allPosts );

            foreach ( $allPosts as $post ) {
                $postCategories = explode( ',', $post->post_category );
                foreach ( $postCategories as $postCategory ) {
                    if ( $postType->name === 'elementor_library' ) {
                        $categoryName = $templateTypesByPostId[$post->ID];
                    } else {
                        $categoryName = $this->categoryList[$postCategory]->name;
                    }
                    $categories[$categoryName][] = $post;
                    ksort( $categories );
                    $categoryCount[$categoryName] = !empty( $categoryCount[$categoryName] )
                        ? $categoryCount[$categoryName] + 1
                        : 1;
                }
                $countPostType += 1;
            }
        }

        return [
            'count' => $countPostType,
            'categories' => $categories,
            'categoryCount' => $categoryCount,
        ];
    }

    private function getTemplateTypesByPostId( $postType, $allPosts ): array {
        global $wpdb;
        if ( $postType->name !== 'elementor_library' || empty( $allPosts ) ) {
            return [];
        }

        $postIds = array_map( function ( $post ) {
            return $post->ID;
        }, $allPosts );

        $queryString = "SELECT $wpdb->postmeta.post_id, `meta_value` 
                    FROM $wpdb->postmeta 
                    WHERE post_id IN (" . implode( ',', $postIds ) . ") 
                        AND meta_key = '_elementor_template_type'";
        $templateTypes = $wpdb->get_results( $queryString, OBJECT );
        $templateTypesByPostId = [];
        foreach ( $templateTypes as $templateType ) {
            $templateTypesByPostId[$templateType->post_id] = $templateType->meta_value;
        }
        return $templateTypesByPostId;
    }

    /**
     * Returns post-edit-link, and info if elementor is available
     *
     * @param \WP_Post_Type $postType
     * @param \WP_Post $post
     */
    public function getPostTypeInfo( $postType, $post ): array {
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
            case 'custom-css':
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
     */
    public function getContextMenuData( $postType, $post, $postTypeInfo ): array {
        $data = [
            'favorite' => true,
            'copy' => [
                'id' => $post->ID,
                'wordpress' => $postTypeInfo['link'] . '&action=edit',
                'elementor' => empty( $postTypeInfo['noElementor'] ) ? $postTypeInfo['link'] . '&action=elementor' : '',
                'website' => get_permalink( $post->ID ),
            ],
        ];

        if ( $this->hasSwift() ) {
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
    public function renamePost(): void {
        $postid = filter_input( INPUT_POST, 'postid' );
        $title = filter_input( INPUT_POST, 'title' );

        $result = [
            'ID' => $postid,
            'post_title' => $title,
        ];

        if ( empty( $postid ) || empty( $title ) ) {
            wp_die( json_encode( $result ) );
        }

        wp_update_post( $result );
        wp_die( json_encode( $result ) );
    }


    /**
     * Fix wrong language in elementor
     */
    public function fixElementorLanguage() {
        global $current_user;
        $userLocale = get_user_meta( get_current_user_id(), 'locale', true );
        $current_user->locale = $userLocale;
    }

    public function enqueueStyles() {
        wp_enqueue_style( $this->pluginName, AdminQuickbar_URL . '/Admin/css/admin-quickbar-admin.min.css', [], $this->version, 'all' );
        if ( !is_admin() ) {
            wp_enqueue_style( 'dashicons' );
        }
    }

    public function enqueueScripts() {
        wp_enqueue_script( $this->pluginName, AdminQuickbar_URL . '/Admin/js/build.min.js', [ 'jquery' ], $this->version, true );

        wp_localize_script( $this->pluginName, 'aqbLocalize',
            [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] );
    }
}