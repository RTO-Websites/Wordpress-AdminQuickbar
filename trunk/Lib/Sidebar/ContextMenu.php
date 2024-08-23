<?php declare( strict_types=1 );

namespace AdminQuickbar\Lib\Sidebar;

trait ContextMenu {
    /**
     * Returns data-attributes based on post-type
     *
     * @param \WP_Post_Type $postType
     * @param \WP_Post $post
     * @param array $postTypeInfo
     */
    public function getContextMenuData( $postType, $post, $postTypeInfo, $permalink ): array {
        $data = [
            'favorite' => true,
            'copy' => [
                'id' => $post->ID,
                'wordpress' => $postTypeInfo['link'] . '&action=edit',
                'elementor' => empty( $postTypeInfo['noElementor'] ) ? $postTypeInfo['link'] . '&action=elementor' : '',
                'website' => $permalink,
            ],
        ];

        if ( $this->hasSwift() ) {
            $data['swift'] = [
                'inCache' => in_array( $permalink, $this->cacheList ),
                'permalink' => $permalink,
            ];
        }

        if ( $postType->name !== 'attachment' ) {
            $data['trash'] = [
                'id' => $post->ID,
            ];
            $data['rename'] = [
                'id' => $post->ID,
            ];
        }

        switch ( $postType->name ) {
            case 'elementor_library':
                $data['copy']['shortcode'] = '[elementor-template id=' . $post->ID . ']';
                break;
        }

        return $data;
    }
}