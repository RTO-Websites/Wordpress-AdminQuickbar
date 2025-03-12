<?php
/**
 * @var array $fieldGroups
 */
?>
<div class="aqb-options aqb-tab aqb-tab-options">
    <?php foreach ( $fieldGroups as $group ): ?>
        <h2 class="aqb-settings-headline">
            <?php echo esc_attr($group->label); ?>
        </h2>
        <?php foreach ( $group->fields as $fieldKey => $field ): ?>
            <label class="admin-quickbar-<?php echo esc_attr($fieldKey); ?>">

                <?php switch ( $field->type ):
                    case 'select': ?>
                        <span class="aqb-field-label"><?php echo esc_attr($field->label); ?></span>
                        <?php if ( !empty( $field->sublabel ) ): ?>
                            <span class="sublabel"><?php echo esc_attr($field->sublabel); ?></span>
                        <?php endif; ?>
                        <select class="aqb-input-<?php echo esc_attr($fieldKey); ?>"
                            <?php echo !empty( $field->multiple ) ? 'multiple' : ''; ?>
                        >
                            <?php foreach ( $field->options as $optionKey => $optionLabel ): ?>
                                <option value="<?php echo esc_attr($optionKey); ?>" <?php echo in_array($optionKey, $field->selected) ? ' selected' : ''; ?>>
                                    <?php echo esc_attr($optionLabel); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php break;
                    case 'checkbox': ?>
                        <input type="checkbox" name="admin-quickbar-<?php echo esc_attr($fieldKey); ?>" <?php echo !empty($field->checked) ? ' checked' : ''; ?> />
                        <span class="aqb-field-label"><?php echo esc_attr($field->label); ?></span>
                        <?php break;
                    case 'number': ?>
                        <span class="aqb-field-label"><?php echo esc_attr($field->label); ?></span>
                        <input type="number" name="admin-quickbar-<?php echo esc_attr($fieldKey); ?>>"
                            <?php echo !empty( $field->max ) ? ' max=' . esc_attr($field->max) : ''; ?>
                            <?php echo !empty( $field->min ) ? ' min=' . esc_attr($field->min) : ''; ?>
                        />
                        <?php break; ?>
                    <?php endswitch; ?>

            </label>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>