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
            'aqb-recent' => __( 'Recent' ),
            'aqb-favorites' => __( 'Favorites' ),
        ];
        foreach ( get_post_types( [], 'object' ) as $postType ) {
            if ( in_array( $postType->name, $this->filterPostTypes ) ) {
                continue;
            }
            $hidePostTypes[$postType->name] = $postType->label;
        }

        $this->fieldGroups = [
            [
                'label' => __( 'Visibility' ),
                'fields' => [
                    'hide-posttypes' => [
                        'type' => 'select',
                        'multiple' => true,
                        'label' => __( 'Hide main container (PostTypes)', 'admin-quickbar' ),
                        'sublabel' => '[' . __( 'Ctrl+Click', 'admin-quickbar' ) . ']',
                        'rows' => count( $hidePostTypes ),
                        'options' => $hidePostTypes,
                        'selected' => $this->settings['hiddenPostTypes'] ?? ['attachment'],
                    ],
                    'loadthumbs' => [
                        'type' => 'checkbox',
                        'label' => __( 'Show thumbs', 'admin-quickbar' ),
                        'checked' => $this->settings['loadThumbs'] ?? false,
                    ],
                    'show-trash-option' => [
                        'type' => 'checkbox',
                        'label' => __( 'Show trashed posts', 'admin-quickbar' ),
                    ],
                    'max-recent' => [
                        'type' => 'number',
                        'label' => __( 'Max. Recent', 'admin-quickbar' ),
                        'min' => 0,
                    ],
                    'show-postids' => [
                        'type' => 'checkbox',
                        'label' => __( 'Show post-ids', 'admin-quickbar' ),
                    ],
                    'hide-on-website' => [
                        'type' => 'checkbox',
                        'label' => __( 'Hide quickbar on website', 'admin-quickbar' ),
                        'checked' => $this->settings['hideOnWebsite'] ?? false,
                    ],
                ],
            ],
            [
                'label' => __( 'Quickbar behavior', 'admin-quickbar' ),
                'fields' => [
                    'keepopen' => [
                        'type' => 'checkbox',
                        'label' => __( 'Keep open when switching page', 'admin-quickbar' ),
                    ],
                    'overlap' => [
                        'type' => 'checkbox',
                        'label' => __( 'Overlap', 'admin-quickbar' ),
                    ],
                ],
            ],
            [
                'label' => __( 'Quickbar Color-Theme' ),
                'fields' => [
                    'theme' => [
                        'type' => 'select',
                        'label' => '',
                        'options' => [
                            'auto' => __( 'Auto detect', 'admin-quickbar' ),
                            'dark' => __( 'Dark', 'admin-quickbar' ),
                            'light' => __( 'Light', 'admin-quickbar' ),
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