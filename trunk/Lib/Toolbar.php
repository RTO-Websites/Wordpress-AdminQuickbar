<?php
/**
 * @since 1.0.0
 * @author s.hennemann
 * @licence MIT
 */

namespace AdminQuickbar\Lib;


class Toolbar {
    private $submenuItems = [];
    private $templateVars = [];

    const PARTIAL_DIR = AdminQuickbar_DIR . '/Lib/partials/';

    /**
     * Toolbar constructor.
     * @param array $templateVars
     */
    public function __construct( array $templateVars = [] ) {
        $this->initAdminMenuItems();
        $this->templateVars = $templateVars;
        $this->templateVars['submenuItems'] = $this->getRenderedSubmenus();
    }


    /**
     * Returns an array with rendered submenu items
     *
     * @return array
     */
    private function getRenderedSubmenus(): array {
        $result = [];
        foreach ( $this->submenuItems as $key => $item ) {
            $result[$key] = '';
            foreach ( $item as $subitem ) {
                if ( !current_user_can( $subitem[1] ) ) {
                    continue;
                }
                $template = new Template( self::PARTIAL_DIR . '/toolbar-item.php', [
                    'link' => admin_url( $subitem[2] ),
                    'label' => $subitem[0],
                ] );

                $result[$key] .= $template->getRendered();
            }

        }
        return $result;
    }

    /**
     * Returns fully rendered toolbar
     *
     * @return string
     */
    public function getRendered(): string {
        $toolbar = new Template( self::PARTIAL_DIR . '/toolbar.php', $this->templateVars );
        return $toolbar->getRendered();
    }

    /**
     * Returns wordpress admin menu items
     * Copied from wp-admin/menu.php
     *
     * @return array
     */
    private function initAdminMenuItems() {
        $submenu = [];
        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
        $customize_url = add_query_arg( 'return', urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $requestUri ) ) ), 'customize.php' );
        $submenu['themes.php'][6] = [ __( 'Customize', 'adminquickbar' ), 'customize', esc_url( $customize_url ), '', 'hide-if-no-customize' ];

        if ( current_theme_supports( 'menus' ) || current_theme_supports( 'widgets' ) ) {
            $submenu['themes.php'][10] = [ __( 'Menus', 'adminquickbar' ), 'edit_theme_options', 'nav-menus.php' ];
        }

        $submenu['options-general.php'][10] = [ _x( 'General', 'settings screen', 'adminquickbar' ), 'manage_options', 'options-general.php' ];
        $submenu['options-general.php'][15] = [ __( 'Writing', 'adminquickbar' ), 'manage_options', 'options-writing.php' ];
        $submenu['options-general.php'][20] = [ __( 'Reading', 'adminquickbar' ), 'manage_options', 'options-reading.php' ];
        $submenu['options-general.php'][25] = [ __( 'Discussion', 'adminquickbar' ), 'manage_options', 'options-discussion.php' ];
        $submenu['options-general.php'][30] = [ __( 'Media', 'adminquickbar' ), 'manage_options', 'options-media.php' ];
        $submenu['options-general.php'][40] = [ __( 'Permalinks', 'adminquickbar' ), 'manage_options', 'options-permalink.php' ];
        $submenu['options-general.php'][45] = [ __( 'Privacy', 'adminquickbar' ), 'manage_privacy_options', 'options-privacy.php' ];

        $submenu['plugins.php'][5] = [ __( 'Installed Plugins', 'adminquickbar' ), 'activate_plugins', 'plugins.php' ];

        if ( !is_multisite() ) {
            /* translators: Add new plugin. */
            $submenu['plugins.php'][10] = [ _x( 'Add New', 'plugin', 'adminquickbar' ), 'install_plugins', 'plugin-install.php' ];
            $submenu['plugins.php'][15] = [ __( 'Plugin Editor', 'adminquickbar' ), 'edit_plugins', 'plugin-editor.php' ];
        }

        if ( defined( 'ELEMENTOR_VERSION' ) ) {
            $submenu['elementor'][10] = [ _x( 'Templates', '', 'adminquickbar' ), 'edit_posts', 'edit.php?post_type=elementor_library&tabs_group=library' ];
            $submenu['elementor'][15] = [ _x( 'Popups', '', 'adminquickbar' ), 'edit_posts', 'edit.php?post_type=elementor_library&tabs_group=popup&elementor_library_type=popup' ];
            $submenu['elementor'][20] = [ _x( 'Tools', '', 'adminquickbar' ), 'manage_options', 'admin.php?page=elementor-tools' ];
        }


        $this->submenuItems = $submenu;

        return $this->submenuItems;
    }
}