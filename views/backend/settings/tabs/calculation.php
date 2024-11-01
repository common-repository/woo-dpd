<table class="form-table">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_ignore_profile">Укажите профили которые НЕ будут использоваться при расчетах</label>
        </th>

        <td class="forminp">
            <fieldset>
                <?php
                    $dpdProfileOff = get_option('dpd_ignore_profile');
                    $dpdProfileOff = unserialize($dpdProfileOff) ?: [];
                ?>

                <select name="dpd[ignore_profile][]" id="dpd_ignore_profile" multiple="multiple" class="dpd-select">
                    <option value=""></option>
                    <option value="PICKUP" <?php selected(in_array('PICKUP', $dpdProfileOff), true) ?>>До терминала</option>
                    <option value="COURIER" <?php selected(in_array('COURIER', $dpdProfileOff), true) ?>>До двери</option>
                </select>
            </fieldset>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_ignore_tariff">
                <?php echo __('Specify the rates that will NOT be aused in calculations', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                

                <?php
                    $dpdTarifOff = get_option('dpd_ignore_tariff');
                    if ($dpdTarifOff) {
                        $dpdTarifOff = unserialize($dpdTarifOff);
                    }
                ?>
                <select class="dpd-select"
                    type="text" multiple="" name="dpd[ignore_tariff][]"
                    id="dpd_ignore_tariff">
                    <option></option>

					<option value="PCL"
                        <?php selected($dpdTarifOff && in_array('PCL', $dpdTarifOff), true) ?>
					>DPD OPTIMUM</option>

					<option value="CUR"
                        <?php selected($dpdTarifOff && in_array('CUR', $dpdTarifOff), true) ?>
					>DPD CLASSIC</option>

                    <option value="CSM"
                        <?php selected($dpdTarifOff && in_array('CSM', $dpdTarifOff), true) ?>
					>DPD Online Express</option>

                    <option value="ECN"
                        <?php selected($dpdTarifOff && in_array('ECN', $dpdTarifOff), true) ?>
					>DPD ECONOMY</option>

                    <option value="ECU"
                        <?php selected($dpdTarifOff && in_array('ECU', $dpdTarifOff), true) ?>
					>DPD ECONOMY CU</option>

                    <option value="NDY"
                        <?php selected($dpdTarifOff && in_array('NDY', $dpdTarifOff), true) ?>
					>DPD EXPRESS</option>

                    <option value="BZP"
                        <?php selected($dpdTarifOff && in_array('BZP', $dpdTarifOff), true) ?>
					>DPD 18:00</option>

                    <option value="MXO"
                        <?php selected($dpdTarifOff && in_array('MXO', $dpdTarifOff), true) ?>
					>DPD Online Max</option>

                    <option value="MAX"
                        <?php selected($dpdTarifOff && in_array('MAX', $dpdTarifOff), true) ?>
					>DPD MAX domestic</option>

                    <option value="PUP"
                        <?php selected($dpdTarifOff && in_array('PUP', $dpdTarifOff), true) ?>
					>DPD SHOP</option>
                </select>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_tariff_default">
                <?php echo __('Default rate', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <select class="dpd-select"
						type="text" name="dpd[tariff_default]"
						id="dpd_tariff_default"
				>
                    <option value="PCL" <?php selected(get_option('dpd_tariff_default'), 'PCL') ?>>DPD OPTIMUM</option>
                    <option value="CUR" <?php selected(get_option('dpd_tariff_default'), 'CUR') ?>>DPD CLASSIC</option>
                    <option value="CSM" <?php selected(get_option('dpd_tariff_default'), 'CSM') ?>>DPD Online Express</option>
                    <option value="ECN" <?php selected(get_option('dpd_tariff_default'), 'ECN') ?>>DPD ECONOMY</option>
                    <option value="ECU" <?php selected(get_option('dpd_tariff_default'), 'ECU') ?>>DPD ECONOMY CU</option>
                    <option value="NDY" <?php selected(get_option('dpd_tariff_default'), 'NDY') ?>>DPD EXPRESS</option>
                    <option value="BZP" <?php selected(get_option('dpd_tariff_default'), 'BZP') ?>>DPD 18:00</option>
                    <option value="MXO" <?php selected(get_option('dpd_tariff_default'), 'MXO') ?>>DPD Online Max</option>
                    <option value="MAX" <?php selected(get_option('dpd_tariff_default'), 'MAX') ?>>DPD MAX domestic</option>
                    <option value="PUP" <?php selected(get_option('dpd_tariff_default'), 'PUP') ?>>DPD SHOP</option>
                </select>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_default_tariff_treshold">
                <?php echo __('Maximum shipping cost at which the default rate will be applied', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_default_tariff_treshold">
					<input type="text"
						   name="dpd[default_tariff_treshold]"
						   id="dpd_default_tariff_treshold"
						   value="<?php echo esc_html(get_option('dpd_default_tariff_treshold')) ?>"
					>
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_declared_value">
                <?php echo __('Include insurance in the cost of delivery', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="hidden" name="dpd[declared_value]" value="0">
                <label for="dpd_declared_value">
					<input class=""
						   type="checkbox"
						   name="dpd[declared_value]"
						   id="dpd_declared_value"
						   value="1"
						   <?php echo checked(get_option('dpd_declared_value')); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_calculate_by_parcel">
                <?php echo __('Calculate shipping costs by item', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="hidden" name="dpd[calculate_by_parcel]" value="0">
                <label for="dpd_calculate_by_parcel">
					<input class=""
						   type="checkbox"
						   name="dpd[calculate_by_parcel]"
						   id="dpd_calculate_by_parcel"
						   value="1"
						   <?php echo checked(get_option('dpd_calculate_by_parcel')); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_round_to">
                <?php echo __('Round to units', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_round_to">
					<input type="text"
						   name="dpd[round_to]"
						   id="dpd_round_to"
						   value="<?php echo esc_html(get_option('dpd_round_to')); ?>"
					>
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_add_delivery_day">
                <?php echo __('Extend delivery time (days)', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_add_delivery_day">
					<input type="text"
						   name="dpd[add_delivery_day]"
						   id="dpd_add_delivery_day"
						   value="<?php echo esc_html(get_option('dpd_add_delivery_day')); ?>"
					>
                </label>
            </fieldset>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_add_delivery_markup_value">
                Наценка к стоимости доставки
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_add_delivery_markup_value">
                    <input type="text" name="dpd[markup_value]" id="dpd_add_delivery_markup_value" value= "<?php echo esc_html(get_option('dpd_markup_value')); ?>">

                    <select name="dpd[markup_type]" id="dpd_add_delivery_markup_type">
                        <option value="PERCENT" <?php selected(get_option('dpd_markup_type') != 'FIXED', true) ?>>% от стоимости товаров</option>
                        <option value="FIXED" <?php selected(get_option('dpd_markup_type'), 'FIXED') ?>>фиксированная</option>
                    </select>
                </label>
            </fieldset>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_add_delivery_day">Стоимость доставки</label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_add_delivery_day">
					<input type="text"
						   name="dpd[default_price]"
						   id="dpd_add_delivery_day"
						   value="<?php echo esc_html(get_option('dpd_default_price')); ?>"
					>
                </label>
            </fieldset>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_commission_npp_check">
                <?php 
                    echo __('Include cash on delivery fee in the cost of delivery', 'woo-dpd');
                ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="hidden" name="dpd[commission_npp_check]" value="0">
                <label for="dpd_commission_npp_check">
					<input class=""
						   type="checkbox"
						   name="dpd[commission_npp_check]"
						   id="dpd_commission_npp_check"
						   value="1"
						   <?php echo checked(get_option('dpd_commission_npp_check')); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_commission_npp_percent">
                <?php echo __('Commission on the value of goods in the order, %', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_commission_npp_percent">
					<input type="text"
						   name="dpd[commission_npp_percent]"
						   id="dpd_commission_npp_percent"
						   value="<?php echo esc_html(get_option('dpd_commission_npp_percent')); ?>"
					>
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_commission_npp_minsum">
                <?php echo __('The minimum amount of commission in the currency', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_commission_npp_percent">
					<input type="text"
						   name="dpd[commission_npp_minsum]"
						   id="dpd_commission_npp_percent"
						   value="<?php echo esc_html(get_option('dpd_commission_npp_minsum')); ?>"
					>
                </label>
            </fieldset>
        </td>
    </tr>
    <?php
        $gateways = WC()->payment_gateways->get_available_payment_gateways();
        $enabledGateways = [];
        if($gateways) {
            foreach($gateways as $gateway) {
                if($gateway->enabled == 'yes') {
                    $enabledGateways[] = $gateway;
                }
            }
        }
        $сommissionNppPayment = get_option('dpd_commission_npp_payment');
        if ($сommissionNppPayment) {
            $сommissionNppPayment = unserialize($сommissionNppPayment);
        }
    ?>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_commission_npp_payment">
                <?php echo __('Tie payment systems, which means that the payment will be cash on delivery', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <select class="dpd-select"
                    type="text" multiple="" name="dpd[commission_npp_payment][]"
                    id="dpd_commission_npp_payment">
                    <?php foreach($enabledGateways as $gateway): ?>
                        <option value="<?php echo esc_html($gateway->id); ?>"
                                <?php selected(is_array($сommissionNppPayment) && in_array($gateway->id, $сommissionNppPayment), true) ?>
						>
                            <?php echo esc_html($gateway->title); ?>   
                        </option>
                    <?php endforeach; ?>
                </select>
            </fieldset>
        </td>
    </tr>
</table>