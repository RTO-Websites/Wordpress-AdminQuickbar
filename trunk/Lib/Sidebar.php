<?php

namespace AdminQuickbar\Lib;

use AdminQuickbar\Lib\Sidebar\ContextMenu;
use AdminQuickbar\Lib\Sidebar\Render;
use AdminQuickbar\Lib\Sidebar\Wpml;

class Sidebar {
    use Wpml;
    use Render;
    use ContextMenu;

    protected Loader $loader;

    const PARTIAL_DIR = AdminQuickbar_DIR . '/Lib/partials/';


    private array $postTypes = [];
    private array $filteredPostTypes = [];
    private array $categoryList = [];

    private array $cacheList = [];

    private array $cssPosts = [];

    private array $settings = [];

    private array $transistentThumbnailUrls = [];

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
        'custom_css',
        'customize_changeset',
        'oembed_cache',
        'ocean_modal_window',
        'nxs_qp',
        'wp_global_styles',
        'acf-field',
    ];

    private string $pluginName;

    private string $version;

    public function __construct( string $pluginName, string $version, array $settings ) {

        $this->pluginName = $pluginName;
        $this->version = $version;

        $this->loader = new Loader();
        $this->settings = $settings;

        $this->filterPostTypes = array_merge( $this->filterPostTypes, $this->settings['hiddenPostTypes'] ?? [ 'attachment' ] );

        $this->defineHooks();

        $this->loader->run();


        foreach ( get_categories() as $category ) {
            $this->categoryList[$category->term_id] = $category;
        }

        if ( !empty( $this->settings['loadThumbs'] ) ) {
            $this->transistentThumbnailUrls = get_transient( 'aqb_thumbnails' ) ?: [];
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

        $wpmlSelect = '';
        $wpmlJoin = '';
        if ( $this->isWpmlActive() ) {
            $wpmlSelect = ', language_code';
            $wpmlJoin = " LEFT OUTER JOIN {$wpdb->prefix}icl_translations ON $wpdb->posts.ID = element_id AND element_type = 'post_$postType->name'";
        }

        $categoryCount = [];
        if ( $postType->hierarchical ) {
            $queryString = "
                SELECT 
                    $wpdb->posts.ID,
                    $wpdb->posts.post_title,
                    $wpdb->posts.post_name,
                    $wpdb->posts.post_status,
                    $wpdb->posts.post_type
                    $wpmlSelect
                FROM $wpdb->posts
                    $wpmlJoin
                WHERE $wpdb->posts.post_type = '$postType->name'
                AND $wpdb->posts.post_status NOT IN ('auto-draft')
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
                    $wpdb->posts.post_name,
                    $wpdb->posts.post_status,
                    $wpdb->posts.post_type,
                    GROUP_CONCAT($wpdb->term_relationships.term_taxonomy_id) as post_category
                    $wpmlSelect
                FROM $wpdb->posts
                    LEFT OUTER JOIN $wpdb->term_relationships on $wpdb->posts.ID = $wpdb->term_relationships.object_id
                    $wpmlJoin
                WHERE $wpdb->posts.post_type = '$postType->name'
                AND $wpdb->posts.post_status NOT IN ('auto-draft')
                GROUP BY $wpdb->posts.ID
                ORDER BY menu_order ASC
             ";

            $allPosts = $wpdb->get_results( $queryString, OBJECT );

            $templateTypesByPostId = $this->getTemplateTypesByPostId( $postType, $allPosts );

            foreach ( $allPosts as $post ) {
                $postCategories = explode( ',', $post->post_category ?? '' );
                foreach ( $postCategories as $postCategory ) {
                    if ( $postType->name === 'elementor_library' ) {
                        $categoryName = $templateTypesByPostId[$post->ID];
                    } elseif ( empty( $postCategory ) || empty( $this->categoryList[$postCategory] ) ) {
                        $categoryName = 'none';
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