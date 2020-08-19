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
 * @package    AdminQuickbar
 * @author     RTO GmbH
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
     * @param string $pluginName The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct( $pluginName, $version ) {

        $this->pluginName = $pluginName;
        $this->version = $version;

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
    }

}
