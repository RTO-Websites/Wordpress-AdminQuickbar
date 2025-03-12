<?php
/**
 * @var string $url
 * @var string $class
 */


// phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
?>
<img src="" data-src="<?php echo esc_url($url); ?>" alt="" class="wp-post-image attachment-post-thumbnail <?php echo esc_attr($class); ?>"/>
<?php
// phpcs:enable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage