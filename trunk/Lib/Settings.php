<?php
/**
 * @since 1.0.0
 * @author s.hennemann
 * @licence MIT
 */

namespace AdminQuickbar\Lib;


class Settings {
    private array $fieldGroups = [];

    private array $settings = [];

    /**
     * List of post-types that should not be displayed and filtered out
     * @var string[]
     */
    private array $filterPostTypes = [
        'nav_menu_item',
        'revision',
        'custom_css',
        'customize_changeset',
        'oembed_cache',
        'ocean_modal_window',
        'nxs_qp',
        'wp_global_styles',
        'acf-field',
    ];

    const PARTIAL_DIR = AdminQuickbar_DIR . '/Lib/partials/';


    public function __construct( array $settings = [] ) {
        $this->settings = $settings;
        $this->initFieldGroups();
    }

    private function initFieldGroups() {
        $hidePostTypes = [
            'aqb-recent' => __( 'Recent', 'adminquickbar' ),
            'aqb-favorites' => __( 'Favorites', 'adminquickbar' ),
        ];
        foreach ( get_post_types( [], 'object' ) as $postType ) {
            if ( in_array( $postType->name, $this->filterPostTypes ) ) {
                continue;
            }
            $hidePostTypes[$postType->name] = $postType->label;
        }

        $this->fieldGroups = [
            [
                'label' => __( 'Visibility', 'adminquickbar' ),
                'fields' => [
                    'hide-posttypes' => [
                        'type' => 'select',
                        'multiple' => true,
                        'label' => __( 'Hide main container (PostTypes)', 'adminquickbar' ),
                        'sublabel' => '[' . __( 'Ctrl+Click', 'adminquickbar' ) . ']',
                        'rows' => count( $hidePostTypes ),
                        'options' => $hidePostTypes,
                        'selected' => $this->settings['hiddenPostTypes'] ?? ['attachment'],
                    ],
                    'loadthumbs' => [
                        'type' => 'checkbox',
                        'label' => __( 'Show thumbs', 'adminquickbar' ),
                        'checked' => $this->settings['loadThumbs'] ?? false,
                    ],
                    'show-trash-option' => [
                        'type' => 'checkbox',
                        'label' => __( 'Show trashed posts', 'adminquickbar' ),
                    ],
                    'max-recent' => [
                        'type' => 'number',
                        'label' => __( 'Max. Recent', 'adminquickbar' ),
                        'min' => 0,
                    ],
                    'show-postids' => [
                        'type' => 'checkbox',
                        'label' => __( 'Show post-ids', 'adminquickbar' ),
                    ],
                    'hide-on-website' => [
                        'type' => 'checkbox',
                        'label' => __( 'Hide quickbar on website', 'adminquickbar' ),
                        'checked' => $this->settings['hideOnWebsite'] ?? false,
                    ],
                ],
            ],
            [
                'label' => __( 'Quickbar behavior', 'adminquickbar' ),
                'fields' => [
                    'keepopen' => [
                        'type' => 'checkbox',
                        'label' => __( 'Keep open when switching page', 'adminquickbar' ),
                    ],
                    'overlap' => [
                        'type' => 'checkbox',
                        'label' => __( 'Overlap', 'adminquickbar' ),
                    ],
                ],
            ],
            [
                'label' => __( 'Quickbar Color-Theme', 'adminquickbar' ),
                'fields' => [
                    'theme' => [
                        'type' => 'select',
                        'label' => '',
                        'options' => [
                            'auto' => __( 'Auto detect', 'adminquickbar' ),
                            'dark' => __( 'Dark', 'adminquickbar' ),
                            'light' => __( 'Light', 'adminquickbar' ),
                        ],
                        'selected' => [],
                    ],
                ],
            ],
        ];
        $this->fieldGroups = json_decode( json_encode( $this->fieldGroups ) );
    }

    public function getRendered(): string {
        $template = new Template( self::PARTIAL_DIR . '/settings.php', [
            'fieldGroups' => $this->fieldGroups,
        ] );
        return $template->getRendered();
    }
}