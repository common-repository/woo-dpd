<table class="form-table">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_use_mode">
                <?php echo __('Apply', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <select type="text" name="dpd[use_mode]" id="dpd_use_mode">
                    <option value="ORDER"
                        <?php selected(get_option('dpd_use_mode'), 'ORDER') ?>
                    >
                        <?php echo __('to the entire order', 'woo-dpd'); ?>
                    </option>

                    <option value="ITEM"
                        <?php selected(get_option('dpd_use_mode'), 'ITEM') ?>
                    >
                        <?php echo __('for goods in order', 'woo-dpd'); ?>
                    </option>
                </select>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_weight_default">
                <?php echo __('Weight default, g', 'woo-dpd'); ?>
                <span class="required">*</span>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
					   name="dpd[weight_default]"
					   value="<?php echo esc_html(get_option('dpd_weight_default')); ?>"
                       id="dpd_weight_default"
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_length_default">
                <?php echo __('Length by default, mm', 'woo-dpd'); ?>
                <span class="required">*</span>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
					   name="dpd[length_default]"
					   value="<?php echo esc_html(get_option('dpd_length_default')); ?>"
					   id="dpd_length_default"
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_width_default">
                <?php echo __('Width by default, mm', 'woo-dpd'); ?>
                <span class="required">*</span>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
					   name="dpd[width_default]"
					   value="<?php echo esc_html(get_option('dpd_width_default')); ?>"
                       id="dpd_width_default"
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_height_default">
                <?php echo __('Height by default, mm', 'woo-dpd'); ?>
                <span class="required">*</span>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
					   name="dpd[height_default]"
					   value="<?php echo esc_html(get_option('dpd_height_default')); ?>"
                       id="dpd_height_default"
				>
            </fieldset>
        </td>
    </tr>
</table>