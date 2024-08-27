<?php declare( strict_types=1 );

namespace AdminQuickbar\Lib\Sidebar;

use AdminQuickbar\Lib\Template;

trait Wpml {
    private $activeLanguages = [];

    public function getRenderedLanguageFlag( $post ): string {
        if ( !$this->isWpmlActive() || empty($post->language_code)) {
            return '';
        }

        /** @var \SitePress $sitepress */
        global $sitepress;
        $languageCode = $post->language_code;
        $flagUrl = $sitepress->get_flag_url( $languageCode );

        $template = new Template( self::PARTIAL_DIR . '/language-flag.php', [
            'flagUrl' => $flagUrl,
            'languageCode' => $languageCode,
        ] );

        return $template->getRendered();
    }

    public function renderAllLanguageFlags(): string {
        $output = '';
        $wpmlLanguages = apply_filters( 'wpml_active_languages', null );
        if ( !$this->isWpmlActive() || empty( $wpmlLanguages ) ) {
            return '';
        }

        foreach ( $wpmlLanguages as $language ) {
            $template = new Template( self::PARTIAL_DIR . '/language-flag.php', [
                'flagUrl' => $language['country_flag_url'],
                'alt' => $language['native_name'],
                'languageCode' => $language['language_code'],
            ] );
            $output .= $template->getRendered();
            $this->activeLanguages[$language['language_code']] = $language;
        }

        return $output;
    }

    private function isWpmlActive(): bool {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        return is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' );
    }

}