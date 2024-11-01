
    <div id="dpd-docs-form">
        <h3><?php echo __('Invoice', 'woo-dpd'); ?></h3>
        <table class="form-table dpd">
            <tr valign="top">
                <th scope="row" class="titledesc">
                </th>
                <td class="forminp">
                    <button class="button button-primary" id="download_invoice_file">
                        <?php echo __('Download invoice file', 'woo-dpd'); ?>
                    </button>
                </td>
            </tr>
        </table>
        <h3><?php echo __('Sticker printing', 'woo-dpd'); ?></h3>
        <table class="form-table dpd">
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_file_format">
                        <?php echo __('Count', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <label for="dpd_label_count">
                    <input name="label_count" id="dpd_label_count"
                        value="<?php echo esc_html(isset($dpdOrder->cargoNumPack) ? $dpdOrder->cargoNumPack : 1); ?>"
                        class="dpd-no-ajax-update"
                    >
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_file_format">
                        <?php echo __('File format', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <select class="dpd-select dpd-no-ajax-update" name="file_format" id="dpd_file_format">
                            <option value="PDF">PDF</option>
                            <option value="FP3">FP3</option>
                        </select>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_print_area_format">
                        <?php echo __('Print area format', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <select class="dpd-select dpd-no-ajax-update" name="print_area_format" id="dpd_print_area_format">
                            <option value="A5">A5</option>
                            <option value="A6">A6</option>
                        </select>
                    </fieldset>
                </td>
            </tr>
            <th scope="row" class="titledesc">
                </th>
                <td class="forminp">
                    <button class="button button-primary" id="download_label_file">
                        <?php echo __('Download label file', 'woo-dpd'); ?>
                    </button>
                </td>
        </table>
    </div>
    
    <div id="dpd-docs-error" style="display: <?php echo esc_attr($dpdCreated ? 'none' : 'block') ?>">
        <div id="message" class="notice notice-info inline">
            <p><?php echo  __('Documents can be printed only for the created order in DPD with the status "Successfully created".', 'woo-dpd'); ?></p>
        </div>
    </div>