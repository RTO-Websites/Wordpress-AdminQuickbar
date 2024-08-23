<?php namespace AdminQuickbar\Admin;

use AdminQuickbar\Lib\Template;
use Swift_Performance;
use Swift_Performance_Lite;

class AdminQuickbarAdmin {

    private string $pluginName;

    private string $version;

    public function __construct( string $pluginName, string $version ) {

        $this->pluginName = $pluginName;
        $this->version = $version;

    }

    public function enqueueStyles(): void {
    }


    public function enqueueScripts(): void {
    }

    public function saveSettings() {
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }
        $settings = filter_input( INPUT_POST, 'aqbSettings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        $settings['loadThumbs'] = filter_var( $settings['loadThumbs'], FILTER_VALIDATE_BOOLEAN );

        set_transient( 'aqb_settings', $settings, 0 );

        wp_send_json_success(get_transient( 'aqb_settings' ));

        die();
    }

}
