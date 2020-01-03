<?php namespace AdminQuickbar\Pub;

use AdminQuickbar\Lib\Template;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.rto.de
 * @since      1.0.0
 *
 * @package    AdminQuickbar
 */
class AdminQuickbarPublic {
    const PARTIAL_DIR = AdminQuickbar_DIR . '/Admin/partials/';

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
     * @param string $pluginName The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct( $pluginName, $version ) {

        $this->pluginName = $pluginName;
        $this->version = $version;

    }

    /**
     * Render jump-icons
     */
    public function renderJumpIcons() {
        $currentPost = get_the_ID();
        $permalink = get_the_permalink();

        $cssPosts = get_posts( [
            'post_type' => 'elebee-global-css',
            'posts_per_page' => -1,
            'suppress_filters' => false,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ] );

        $template = new Template( self::PARTIAL_DIR . '/jump-icons.php', [
            'currentPost' => $currentPost,
            'permalink' => $permalink,
            'swiftNonce' => wp_create_nonce( 'swift-performance-ajax-nonce' ),
            'hasSwift' => false,
            'inCache' => false,
            'cssPosts' => array_reverse( $cssPosts ),
        ] );
        $template->render();

        $template = new Template( self::PARTIAL_DIR . '/jump-icons-inline-script.php' );
        $template->render();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueueStyles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in ElementorFormBuilderLoader as all of the hooks are defined
         * in that particular class.
         *
         * The ElementorFormBuilderLoader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( 'AdminQuickbar', AdminQuickbar_URL . '/Admin/css/admin-quickbar-admin.min.css', [], AdminQuickbar_VERSION, 'all' );
        wp_enqueue_style( 'dashicons' );
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueueScripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in ElementorFormBuilderLoader as all of the hooks are defined
         * in that particular class.
         *
         * The ElementorFormBuilderLoader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
    }

}
