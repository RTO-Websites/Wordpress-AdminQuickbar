<?php
/**
 * @var array $filteredPostTypes
 */
?>
<br/>
<select class="admin-quickbar-new-select">
    <?php foreach ( $filteredPostTypes as $postType ): ?>
        <option value="<?php echo $postType->name; ?>"><?php echo $postType->label; ?></option>
    <?php endforeach; ?>
</select>

<a class="add-post-button" href="#"
        onclick="window.location.href='<?php echo admin_url( 'post-new.php' ); ?>?post_type=' + jQuery('.admin-quickbar-new-select').val();return false;"></a>
