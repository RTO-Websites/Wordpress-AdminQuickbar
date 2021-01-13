<?php
/**
 * @since 1.0.0
 * @author s.hennemann
 * @licence MIT
 */

namespace AdminQuickbar\Lib;


class Settings {
    private $fieldGroups = [];

    const PARTIAL_DIR = AdminQuickbar_DIR . '/Lib/partials/';

    /**
     * Settings constructor.
     * @param array $args
     */
    public function __construct( array $args = [] ) {
        $this->initFieldGroups( $args );
    }

    /**
     * @param array $args
     */
    private function initFieldGroups( array $args = [] ) {
        $hidePostTypes = [
            'aqb-recent' => __( 'Recent' ),
            'aqb-favorites' => __( 'Favorites' ),
        ];
        foreach ( $args['filteredPostTypes'] as $postType ) {
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
                        'rows' => count( $args['filteredPostTypes'] ),
                        'options' => $hidePostTypes,
                    ],
                    'loadthumbs' => [
                        'type' => 'checkbox',
                        'label' => __( 'Show thumbs', 'admin-quickbar' ),
                    ],
                    'show-trash' => [
                        'type' => 'checkbox',
                        'label' => __( 'Show trashed posts', 'admin-quickbar' ),
                    ],
                    'max-recent' => [
                        'type' => 'number',
                        'label' => __( 'Max. Recent', 'admin-quickbar' ),
                        'min' => 0,
                    ],
                    'hide-on-website' => [
                        'type' => 'checkbox',
                        'label' => __( 'Hide quickbar on website', 'admin-quickbar' ),
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
                    ],
                ],
            ],
        ];
        $this->fieldGroups = json_decode( json_encode( $this->fieldGroups ) );
    }

    /**
     * Returns fully rendered settings
     *
     * @return string
     */
    public function getRendered(): string {
        $template = new Template( self::PARTIAL_DIR . '/settings.php', [
            'fieldGroups' => $this->fieldGroups,
        ] );
        return $template->getRendered();
    }
}