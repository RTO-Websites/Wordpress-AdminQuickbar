<?php namespace Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.hennewelt.de
 * @since      1.0.0
 *
 * @package    AdminPostListSidebar
 * @subpackage AdminPostListSidebar/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AdminPostListSidebar
 * @subpackage AdminPostListSidebar/admin
 * @author     Sascha Hennemann <s.hennemann@rto.de>
 */
class AdminQuickbarAdmin {

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
     * @since    1.0.0
     * @param      string $pluginName The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct( $pluginName, $version ) {

        $this->pluginName = $pluginName;
        $this->version = $version;
        //add_action('admin_footer', array($this, 'addSidebar'), 1);
        add_action( 'admin_print_footer_scripts', array( $this, 'addSidebar' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueueStyles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );

        add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'enqueueStyles' ) );
        add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueueScripts' ), 99999 );

    }

    public function addSidebar( $data ) {
        include( 'partials/admin-quickbar-admin-display.php' );

        return $data;
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

        wp_enqueue_style( $this->pluginName, plugin_dir_url( __FILE__ ) . 'css/admin-quickbar-admin.css', array(), $this->version, 'all' );

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

        wp_enqueue_script( $this->pluginName, plugin_dir_url( __FILE__ ) . 'js/admin-quickbar-admin.js', array( 'jquery' ), $this->version, false );

    }

}
