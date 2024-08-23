<?php namespace AdminQuickbar\Lib;

use AdminQuickbar\Admin\AdminQuickbarAdmin;
use AdminQuickbar\Pub\AdminQuickbarPublic;


class AdminQuickbar {
    protected Loader $loader;

    protected string $pluginName;

    protected string $version;

    protected ?Sidebar $sidebar = null;
    private array $settings = [];

    public function __construct() {

        $this->pluginName = 'admin-quickbar';
        $this->version = AdminQuickbar_VERSION;
        $this->settings = get_transient( 'aqb_settings' ) ?: [];

        $this->loadDependencies();

        if ( filter_has_var( INPUT_GET, 'noaqb' ) ) {
            return;
        }
        $this->addCapatibilites();
        $this->setLocale();

        if ( is_admin() ) {
            $this->defineAdminHooks();
        } else {
            $this->definePublicHooks();
        }
    }

    private function loadDependencies(): void {

        $this->loader = new Loader();

    }

    private function setLocale(): void {
        $pluginI18n = new I18N();
        $pluginI18n->setDomain( $this->getAdminQuickbar() );

        $this->loader->addAction( 'plugins_loaded', $pluginI18n, 'loadPluginTextdomain' );

    }

    private function defineAdminHooks(): void {
        #$pluginAdmin = new AdminQuickbarAdmin( $this->getAdminQuickbar(), $this->getVersion() );
        $this->loader->addAction( 'plugins_loaded', $this, 'registerSidebar' );

        // Register ajax
        $this->loader->addAction( 'wp_ajax_aqb_save_settings', $this, 'saveSettings' );
    }

    private function definePublicHooks(): void {
        #$pluginPublic = new AdminQuickbarPublic( $this->getAdminQuickbar(), $this->getVersion() );
        if ( !empty( $this->settings['hideOnWebsite'] ) ) {
            return;
        }
        $this->loader->addAction( 'plugins_loaded', $this, 'registerSidebar' );
        // Register ajax
        $this->loader->addAction( 'wp_ajax_aqb_save_settings', $this, 'saveSettings' );
    }

    public function registerSidebar(): void {
        if ( !current_user_can( 'view_admin_quickbar' ) ) {
            return;
        }

        if ( filter_has_var( INPUT_GET, 'elementor-preview' ) ) {
            return;
        }

        $this->sidebar = new Sidebar( $this->getAdminQuickbar(), $this->getVersion(), $this->settings );
    }

    private function addCapatibilites(): void {
        $administratorRole = get_role( 'administrator' );
        $administratorRole->add_cap( 'view_admin_quickbar' );
    }


    public function saveSettings() {
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }
        $settings = filter_input( INPUT_POST, 'aqbSettings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        $settings['loadThumbs'] = filter_var( $settings['loadThumbs'], FILTER_VALIDATE_BOOLEAN );
        $settings['hideOnWebsite'] = filter_var( $settings['hideOnWebsite'], FILTER_VALIDATE_BOOLEAN );

        set_transient( 'aqb_settings', $settings, 0 );

        wp_send_json_success(get_transient( 'aqb_settings' ));

        die();
    }

    public function getAdminQuickbar(): string {
        return $this->pluginName;
    }

    public function getLoader(): Loader {
        return $this->loader;
    }

    public function getVersion(): string {
        return $this->version;
    }

    public static function run(): void {
        $plugin = new self();
        $plugin->loader->run();
    }

}
