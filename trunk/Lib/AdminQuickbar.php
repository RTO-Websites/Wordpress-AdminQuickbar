<?php namespace AdminQuickbar\Lib;

use AdminQuickbar\Admin\AdminQuickbarAdmin;
use AdminQuickbar\Pub\AdminQuickbarPublic;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.rto.de
 * @since      1.0.0
 *
 * @package    AdminQuickbar
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    AdminQuickbar
 */
class AdminQuickbar {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $pluginName The string used to uniquely identify this plugin.
     */
    protected $pluginName;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->pluginName = 'admin-quickbar';
        $this->version = AdminQuickbar_VERSION;

        $this->loadDependencies();

        if ( filter_has_var( INPUT_GET, 'noaqb' ) ) {
            return;
        }

        if ( is_admin() ) {
            $this->defineAdminHooks();
            $this->setLocale();
        } else {
            $this->definePublicHooks();
        }
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - AdminQuickbarLoader. Orchestrates the hooks of the plugin.
     * - AdminQuickbarI18n. Defines internationalization functionality.
     * - AdminQuickbarAdmin. Defines all hooks for the admin area.
     * - AdminQuickbarPublic. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function loadDependencies() {

        $this->loader = new Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the AdminQuickbarI18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function setLocale() {
        $pluginI18n = new I18N();
        $pluginI18n->setDomain( $this->getAdminQuickbar() );

        $this->loader->addAction( 'plugins_loaded', $pluginI18n, 'loadPluginTextdomain' );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineAdminHooks() {
        /*if ( !current_user_can( 'administrator' ) ) {
            return;
        }*/
        $pluginAdmin = new AdminQuickbarAdmin( $this->getAdminQuickbar(), $this->getVersion() );

        $this->loader->addAction( 'admin_enqueue_scripts', $pluginAdmin, 'enqueueStyles' );
        $this->loader->addAction( 'admin_enqueue_scripts', $pluginAdmin, 'enqueueScripts' );

        // admin_footer
        $this->loader->addAction( 'admin_print_footer_scripts', $pluginAdmin, 'renderSidebar' );

        $this->loader->addAction( 'elementor/editor/before_enqueue_styles', $pluginAdmin, 'enqueueStyles' );
        $this->loader->addAction( 'elementor/editor/before_enqueue_scripts', $pluginAdmin, 'enqueueScripts', 99999 );

        $this->loader->addAction( 'set_current_user', $pluginAdmin, 'fixElementorLanguage', 11 );

        $this->loader->addAction( 'wp_ajax_aqbRenamePost', $pluginAdmin, 'renamePost');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function definePublicHooks() {
        $this->loader->addAction( 'plugins_loaded', $this, 'checkLoginOnWebsite' );
    }

    /**
     * Checks if user is logged in and register actions for public-jumpicons
     */
    public function checkLoginOnWebsite() {
        if ( !current_user_can( 'administrator' ) ) {
            return;
        }

        if ( filter_has_var( INPUT_GET, 'elementor-preview' ) ) {
            return;
        }

        $pluginPublic = new AdminQuickbarPublic( $this->getAdminQuickbar(), $this->getVersion() );

        // can´t use loader->addAction here, cause it is nested in plugins_loaded
        add_action( 'wp_footer', [ $pluginPublic, 'renderJumpIcons' ] );
        add_action( 'wp_enqueue_scripts', [ $pluginPublic, 'enqueueStyles' ] );



        // TODO: Move sidebar to own class
        $pluginAdmin = new AdminQuickbarAdmin( $this->getAdminQuickbar(), $this->getVersion() );
        add_action( 'wp_enqueue_scripts', [$pluginAdmin, 'enqueueStyles'] );
        add_action( 'wp_enqueue_scripts', [$pluginAdmin, 'enqueueScripts'] );

        // admin_footer
        add_action( 'wp_footer', [$pluginAdmin, 'renderSidebar'] );
        add_action( 'set_current_user', [$pluginAdmin, 'fixElementorLanguage'], 11 );
        add_action( 'wp_ajax_aqbRenamePost', [$pluginAdmin, 'renamePost']);
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function getAdminQuickbar() {
        return $this->pluginName;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function getLoader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public static function run() {
        $plugin = new self();
        $plugin->loader->run();
    }

}
