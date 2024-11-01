<div id="message" class="notice notice-info inline">
    <p><?php echo  __('This group of settings is needed in order to quickly track the status of orders. Once every 10 minutes, information is requested on the status of sent applications. When a response is received, orders will be placed in the specified statuses if they are accepted, or for some reason rejected. It also tracks the status of delivery orders. It is recommended to create two new ones.
order status, to make it easier to track the status of applications.', 'woo-dpd'); ?>
    </p>
    <p>
		Внимание! Для возможности отслеживания статусов заказов DPD необходимо сделать запрос на почту <a href="mailto:integrators@dpd.ru" target="_blank">integrators@dpd.ru</a>
		с просьбой подключения метода getEvents. В запросе обязательно укажите свой клиентский номер.
    </p>
</div>
<?php
    $dpdOrderStatuses = \Ipol\DPD\DB\Order\Model::StatusList();
    $wcOrderStatuses = wc_get_order_statuses();
?>
<table class="form-table">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_set_tracking_number">
                <?php echo __('Place orders for a shipment ID', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_set_tracking_number">
                <input type="hidden" name="dpd[set_tracking_number]" value="0">
                <input class="" type="checkbox" name="dpd[set_tracking_number]"
                    id="dpd_cargo_registered" value="1" 
                    <?php echo checked(get_option('dpd_set_tracking_number'));?>
                >
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_mark_payed">
                <?php echo __('Mark delivered order paid', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_mark_payed">
                <input type="hidden" name="dpd[mark_payed]" value="0">
                <input class="" type="checkbox" name="dpd[mark_payed]"
                    id="dpd_dvd" value="1" 
                    <?php echo checked(get_option('dpd_mark_payed'));?>
                >
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_status_order_check">
                <?php echo __('Track order statuses in DPD', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_status_order_check">
                <input type="hidden" name="dpd[status_order_check]" value="0">
                <input class="" type="checkbox" name="dpd[status_order_check]"
                    id="dpd_status_order_check" value="1" 
                    <?php echo checked(get_option('dpd_status_order_check'));?>
                >
                </label>
            </fieldset>
        </td>
    </tr>
    <?php foreach ($dpdOrderStatuses as $id => $status): ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="dpd_<?php echo esc_html($id); ?>">
                    <?php echo esc_html($status); ?>
                </label>
            </th>
            <td class="forminp">
                <fieldset>
                    <select class="dpd-select status" 
                    <?php echo get_option('dpd_status_order_check') ?
                        '' : 'disabled';
                    ?>
                        type="text" name="dpd[sync_order_status_<?php echo esc_html($id); ?>]"
                        id="dpd_<?php echo esc_html($id); ?>">
                        <option value=""><?php echo __('- No binding -', 'woo-dpd'); ?></option>

                        <?php foreach ($wcOrderStatuses as $key => $wcstatus): ?>
                            <option value="<?php echo esc_html($key); ?>"
                                <?php selected(get_option('dpd_sync_order_status_'.$id), $key) ?>>
                                <?php echo esc_html($wcstatus); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </fieldset>
            </td>
        </tr>
    <?php endforeach; ?>
</table>