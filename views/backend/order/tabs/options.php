<h3><?php echo __('Options', 'woo-dpd'); ?></h3>
<div id="message" class="notice notice-info inline">
    <p><?php echo  __('The cost of the options is not taken into account when calculating the delivery.', 'woo-dpd'); ?></p>
</div>
<table class="form-table">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_cargo_registered">
                <?php echo __('Valuable cargo', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_cargo_registered">
                <input class="" type="checkbox" name="order[cargo_registered]"
                    id="dpd_cargo_registered" value="1" 
                    <?php echo checked($cargoRegistered); ?>
                    <?php echo disabled($sended); ?>>
                </label>
                <small><br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?></small>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_dvd">
                <?php echo __('Weekend delivery', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_dvd">
                <input class="" type="checkbox" name="order[dvd]"
                    id="dpd_dvd" value="1" 
                    <?php echo checked($dvd); ?>
                    <?php echo disabled($sended); ?>>
                </label>
                <small><br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?></small>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_trm">
                <?php echo __('Temperature conditions', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_trm">
                <input class="" type="checkbox" name="order[trm]"
                    id="dpd_trm" value="1" 
                    <?php echo checked($trm); ?>
                    <?php echo disabled($sended); ?>>
                </label>
                <small><br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?></small>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_prd">
                <?php echo __('Loading and unloading during delivery', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_prd">
                <input class="" type="checkbox" name="order[prd]"
                    id="dpd_prd" value="1" 
                    <?php echo checked($prd); ?>
                    <?php echo disabled($sended); ?>>
                </label>
                <small><br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?></small>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_vdo">
                <?php echo __('Return documents to the sender', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_vdo">
                <input class="" type="checkbox" name="order[vdo]"
                    id="dpd_vdo" value="1" 
                    <?php echo checked($vdo); ?>
                    <?php echo disabled($sended); ?>>
                </label>
                <small><br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?></small>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_obr">
                Обрешетка
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_obr">
                <input class="" type="checkbox" name="order[obr]"
                    id="dpd_obr" value="1" 
                    <?php echo checked($obr); ?>
                    <?php echo disabled($sended); ?>>
                </label>
                <small><br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?></small>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_ogd">
                <?php echo __('Waiting on the address', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <select class="dpd-select"
                    type="text" name="order[ogd]"
                    id="dpd_ogd"
					<?php echo disabled($sended); ?>
				>
                    <option value=""><?php echo __('- Not selected -', 'woo-dpd'); ?></option>

                    <option value="ПРИМ"
						    <?php selected($ogd, 'ПРИМ') ?>
					><?php echo __('Fitting', 'woo-dpd'); ?></option>

					<option value="ПРОС"
							<?php selected($ogd, 'ПРОС') ?>
                    ><?php echo __('Simple', 'woo-dpd'); ?></option>

					<option value="РАБТ"
							<?php selected($ogd, 'РАБТ') ?>
                    ><?php echo __('Health check', 'woo-dpd'); ?></option>
                </select>

                <small><br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?></small>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_ogd">Частичный выкуп</label>
        </th>
        <td class="forminp">
            <fieldset>
                <select class="dpd-select"
                    type="text" name="order[chst]"
                    id="dpd_chst"
					<?php echo disabled($sended); ?>
				>
                    <option value=""><?php echo __('- Not selected -', 'woo-dpd'); ?></option>

                    <option value="ПРИМ" <?php selected($chst, 'ПРИМ') ?>><?php echo __('Fitting', 'woo-dpd'); ?></option>
					<option value="ПРОС" <?php selected($chst, 'ПРОС') ?>><?php echo __('Simple', 'woo-dpd'); ?></option>
					<option value="РАБТ" <?php selected($chst, 'РАБТ') ?>><?php echo __('Health check', 'woo-dpd'); ?></option>
                </select>

                <small><br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?></small>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_goods_return_amount">Минимальная сумма выкупа, при достижении которой доставка будет бесплатной</label>
        </th>
        <td class="forminp">
            <fieldset>
                <input
                    id="dpd_goods_return_amount"
                    type="text"
                    name="order[goods_return_amount]"
                    value="<?=$goods_return_amount?>"
                    <?php echo disabled($sended); ?>
                >
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_delivery_amount">Сумма за доставку</label>
        </th>
        <td class="forminp">
            <fieldset>
                <input
                    id="dpd_delivery_amount"
                    type="text"
                    name="order[delivery_amount]"
                    value="<?=$delivery_amount?>"
                    <?php echo disabled($sended); ?>
                >
            </fieldset>
        </td>
    </tr>
</table>
<h3><?php echo __('Notifications', 'woo-dpd'); ?></h3>
<table class="form-table">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_esz">
                <?php echo __('Order Received Email', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_esz">
                    <input type="text"
						   name="order[esz]"
						   id="dpd_esz"
						   value="<?php echo esc_attr($esz); ?>"
						   <?php echo disabled($sended); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr>
</table>