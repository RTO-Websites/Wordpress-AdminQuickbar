<?php
/**
 * @var array $fieldGroups
 */
?>
<div class="aqb-options aqb-tab aqb-tab-options">
    <?php foreach ( $fieldGroups as $group ): ?>
        <h2 class="aqb-settings-headline">
            <?php echo $group->label; ?>
        </h2>
        <?php foreach ( $group->fields as $fieldKey => $field ): ?>
            <label class="admin-quickbar-<?php echo $fieldKey; ?>">

                <?php switch ( $field->type ):
                    case 'select': ?>
                        <span class="aqb-field-label"><?php echo $field->label; ?></span>
                        <?php if ( !empty( $field->sublabel ) ): ?>
                            <span class="sublabel"><?php echo $field->sublabel; ?></span>
                        <?php endif; ?>
                        <select class="aqb-input-<?php echo $fieldKey; ?>"
                            <?php echo !empty( $field->multiple ) ? 'multiple' : ''; ?>
                        >
                            <?php foreach ( $field->options as $optionKey => $optionLabel ): ?>
                                <option value="<?php echo $optionKey; ?>" <?php echo in_array($optionKey, $field->selected) ? ' selected' : ''; ?>>
                                    <?php echo $optionLabel; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php break;
                    case 'checkbox': ?>
                        <input type="checkbox" name="admin-quickbar-<?php echo $fieldKey; ?>" <?php echo !empty($field->checked) ? ' checked' : ''; ?> />
                        <span class="aqb-field-label"><?php echo $field->label; ?></span>
                        <?php break;
                    case 'number': ?>
                        <span class="aqb-field-label"><?php echo $field->label; ?></span>
                        <input type="number" name="admin-quickbar-<?php echo $fieldKey; ?>>"
                            <?php echo !empty( $field->max ) ? ' max=' . $field->max : ''; ?>
                            <?php echo !empty( $field->min ) ? ' min=' . $field->min : ''; ?>
                        />
                        <?php break; ?>
                    <?php endswitch; ?>

            </label>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>