<?php namespace AdminQuickbar\Lib;

use AdminQuickbar\Admin\AdminQuickbarAdmin;
use AdminQuickbar\Pub\AdminQuickbarPublic;


class AdminQuickbar {
    protected Loader $loader;

    protected string $pluginName;

    protected string $version;

    protected ?Sidebar $sidebar = null;

    public function __construct() {

        $this->pluginName = 'admin-quickbar';
        $this->version = AdminQuickbar_VERSION;

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
        $pluginAdmin = new AdminQuickbarAdmin( $this->getAdminQuickbar(), $this->getVersion() );
        $this->loader->addAction( 'plugins_loaded', $this, 'registerSidebar' );
    }

    private function definePublicHooks(): void {
        $pluginPublic = new AdminQuickbarPublic( $this->getAdminQuickbar(), $this->getVersion() );
        $this->loader->addAction( 'plugins_loaded', $this, 'registerSidebar' );
    }

    public function registerSidebar(): void {
        if ( !current_user_can( 'view_admin_quickbar' ) ) {
            return;
        }

        if ( filter_has_var( INPUT_GET, 'elementor-preview' ) ) {
            return;
        }

        $this->sidebar = new Sidebar( $this->getAdminQuickbar(), $this->getVersion() );
    }

    private function addCapatibilites(): void {
        $administratorRole = get_role( 'administrator' );
        $administratorRole->add_cap( 'view_admin_quickbar' );
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
