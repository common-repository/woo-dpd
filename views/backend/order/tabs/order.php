<table class="form-table dpd">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('Status', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <span id="dpd_status"><?php echo esc_html($status); ?></span>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('Woocommerce ID', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <?php echo esc_html($order->ID); ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('DPD ID', 'woo-dpd'); ?></span>
        </th>
        <td class="forminp">
            <span id="dpd_id"><?php echo esc_html($orderNum); ?></span>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('Payment type', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <select name="order[payment_type]" <?php echo disabled($sended); ?> >
                <option 
                    <?php if (!$paymentType): ?>
                        selected=""
                    <?php endif; ?>   
                    value="">
                    <?php echo __('Cashless payments', 'woo-dpd'); ?>
                </option>

                <option value="<?php echo esc_html(\Ipol\DPD\Order::PAYMENT_TYPE_OUP); ?>"
						<?php selected($paymentType, \Ipol\DPD\Order::PAYMENT_TYPE_OUP) ?>
				>
                    <?php echo __('Payment at the recipient in cash', 'woo-dpd'); ?>
                </option>
                <option value="<?php echo esc_html(\Ipol\DPD\Order::PAYMENT_TYPE_OUO); ?>"
						<? selected($paymentType, \Ipol\DPD\Order::PAYMENT_TYPE_OUO) ?>
				>
                    <?php echo __('Payment at the sender in cash', 'woo-dpd'); ?>
                </option>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_delivery_type">
                <?php echo __('Delivery type', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <select class="dpd-select"
					type="text" name="order[delivery_type]"
					id="dpd_delivery_type"
				    <?php echo disabled($sended); ?>
			>
                <?php foreach (\Ipol\DPD\Calculator::TariffList() as $code => $name) { ?>
                    <option value="<?php echo esc_html($code) ?>"
							<?php selected($deliveryType, $code) ?>
					>
						<?php echo esc_html($name) ?>
					</option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('Delivery variant', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <select name="order[delivery_variant]" <?php echo disabled( $sended); ?> >
                <option value="ДД"
						<?php selected($deliveryVariant, 'ДД') ?>
				>
                    <?php echo __('Door - Door', 'woo-dpd'); ?>
                </option>

				<option value="ДТ"
						<?php selected($deliveryVariant, 'ДТ') ?>
				>
                    <?php echo __('Door - Terminal', 'woo-dpd'); ?>
                </option>

				<option value="ТД"
						<?php selected($deliveryVariant, 'ТД') ?>
				>
                    <?php echo __('Terminal - Door', 'woo-dpd'); ?>
                </option>

                <option value="ТТ"
						<?php selected($deliveryVariant, 'ТТ') ?>
				>
                    <?php echo __('Terminal - Terminal', 'woo-dpd'); ?>
                </option>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('Date of shipment to DPD', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <input type="text"
				   name="order[pickup_date]"
				   class="datepicker"
				   value="<?php echo esc_html($pickUpDate); ?>"
				   <?php echo disabled( $sended);?>
				   readonly="readonly"
			>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('DPD Transit Time Interval', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <select name="order[pickup_time_period]" <?php echo disabled( $sended); ?>>
                <option value="9-18"
						<?php selected($pickupTimePeriod, '9-18') ?>
                >
                    <?php echo __('any time from 09:00 to 18:00', 'woo-dpd'); ?>
                </option>

                <option value="9-13"
						<?php selected($pickupTimePeriod, '9-13') ?>
                >
                    <?php echo __('from 09:00 to 13:00', 'woo-dpd'); ?>
                </option>

                <option value="13-18"
						<?php selected($pickupTimePeriod, '13-18') ?>
                >
                    <?php echo __('13:00 to 18:00', 'woo-dpd'); ?>
                </option>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_delivery_time_period">
                <?php echo __('Delivery time interval', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <select class="dpd-select"
						name="order[delivery_time_period]"
						id="dpd_delivery_time_period"
					    <?php echo disabled( $sended); ?>
				>

                    <option value="9-18"
							<?php selected($deliveryTimePeriod, '9-18') ?>
                    >
                        <?php echo __('any time from 09:00 to 18:00', 'woo-dpd'); ?>
                    </option>

                    <option value="9-14"
							<?php selected($deliveryTimePeriod, '9-14') ?>
                    >
                        <?php echo __('from 09:00 to 14:00', 'woo-dpd'); ?>
                    </option>

                    <option value="13-18"
							<?php selected($deliveryTimePeriod, '13-18') ?>
                    >
                        <?php echo __('13:00 to 18:00', 'woo-dpd'); ?>
                    </option>

                    <option value="18-22"
							<?php selected($deliveryTimePeriod, '18-22') ?>
                    >
                        <?php echo __('18:00 to 22:00 (extra charge)', 'woo-dpd'); ?>
                    </option>
                </select>
            </fieldset>
        </td>
    </tr>
</table>
<h3><?php echo __('Order detail', 'woo-dpd'); ?></h3>
<table class="form-table dpd">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('Shipping weight, kg', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <input type="text"
				   name="order[shipping_weight]"
				   value="<?php echo esc_html($shippingWeight); ?>"
                   <?php echo disabled($sended); ?>
			>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('Dimensions', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <input type="text"
				   name="order[dimensions_width]"
				   class="dimensions-input"
				   value="<?php echo esc_html($shippingWidth); ?>"
				   id="dpd_dimensions_width"
                   <?php echo disabled( $sended); ?>
			> x

			<input type="text"
				   name="order[dimensions_height]"
				   class="dimensions-input"
				   value="<?php echo esc_html($shippingHeight); ?>"
				   id="dpd_dimensions_height"
                   <?php echo disabled( $sended); ?>
			> x

			<input type="text"
				   name="order[dimensions_length]"
				   class="dimensions-input"
				   value="<?php echo esc_html($shippingLength); ?>"
				   id="dpd_dimensions_length"
                   <?php echo disabled( $sended); ?>
			>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('Volume, m<sup>3</sup>', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <input type="text"
				   name="order[cargo_volume]"
				   value="<?php echo esc_html($cargoVolume); ?>"
				   id="dpd_cargo_volume"
				   readonly=""
				   <?php echo disabled($sended); ?>
			>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('Number of cargo spaces (parcels)', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <input type="text"
				   name="order[cargo_num_pack]"
				   value="<?php echo esc_html($cargoNumPack); ?>"
				   <?php echo disabled($sended); ?>
			>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <?php echo __('Content Submission', 'woo-dpd'); ?>
        </th>
        <td class="forminp">
            <input type="text"
				   name="order[content_submission]"
				   value="<?php echo esc_html($contentSubmission); ?>"
                   <?php echo disabled( $sended); ?>
			>
        </td>
    </tr>
</table>
<script>
    jQuery('.datepicker').datepicker({
        minDate: new Date(),
        dateFormat: 'dd.mm.yy',
    });
    // jQuery('#dpd_dimensions_width,'+
    //     '#dpd_dimensions_height, #dpd_dimensions_length').keyup(function() {
    //     jQuery('#dpd_cargo_volume').val((
    //             (parseFloat(jQuery('#dpd_dimensions_width').val()))
    //             * (parseFloat(jQuery('#dpd_dimensions_height').val()))
    //             * (parseFloat(jQuery('#dpd_dimensions_length').val()))
    //             / 1000000).toFixed(6));
    // });
</script>