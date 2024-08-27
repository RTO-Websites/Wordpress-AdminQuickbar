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

}
