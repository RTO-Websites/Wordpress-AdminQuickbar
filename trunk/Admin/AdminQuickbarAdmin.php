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
}
