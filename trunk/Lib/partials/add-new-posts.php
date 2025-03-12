<?php
/**
 * @var array $filteredPostTypes
 */
?>
<br/>
<select class="admin-quickbar-new-select">
    <?php foreach ( $filteredPostTypes as $postType ): ?>
        <option value="<?php echo esc_attr($postType->name); ?>"><?php echo esc_attr($postType->label); ?></option>
    <?php endforeach; ?>
</select>

<a class="add-post-button" href="#"
        onclick="window.location.href='<?php echo esc_url(admin_url( 'post-new.php' )); ?>?post_type=' + jQuery('.admin-quickbar-new-select').val();return false;"></a>
