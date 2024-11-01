<h3><?php echo __('Order content', 'woo-dpd'); ?></h3>

<table class="form-table dpd">
    <tr valign="top">
        <th scope="row" class="titledesc">
            Указать маркировку
        </th>
        <td class="forminp">
            <input type="checkbox"
				   name="order[use_marking]"
				   value="1"
                   <?php echo disabled($sended); ?>
                   <?php echo checked($useMarking);?>
            >
        </td>
    </tr>
</table>

<table class="form-table pay-tab-table">
    <tr>
        <th><?php echo __('Name', 'woo-dpd'); ?></th>
        <th><?php echo __('Quantity', 'woo-dpd'); ?></th>
        <th><?php echo __('Declared cost', 'woo-dpd'); ?></th>
        <th><?php echo __('Npp amount', 'woo-dpd'); ?></th>
        <th><?php echo __('Tax', 'woo-dpd'); ?></th>
    </tr>
    <?php foreach($unitLoads as $key => $ul): ?>
        <tr>
            <td>
                <?php echo esc_html($ul['name']); ?>

                <?php if ($ul['name'] != __('Delivery', 'dpd')) { ?>
                    <input type="text"
                        class="order-pay-tab-input"
                        name="order[unit_loads][<?php echo esc_html($key) ?>][article]"
                        value="<?php echo esc_html($ul['article']) ?>"
                        placeholder="Артикул"
                        <?php echo disabled($sended); ?>
                    >
                <?php } ?>

                <?php if ($useMarking) { ?>
                    <input type="text"
                           class="order-pay-tab-input"
                           name="order[unit_loads][<?php echo esc_html($key) ?>][gtin]"
                           value="<?php echo esc_html($ul['gtin']) ?>"
                           placeholder="Идентификационный номер"
                           <?php echo disabled($sended); ?>
                    >
                    
                    <input type="text"
                           class="order-pay-tab-input"
                           name="order[unit_loads][<?php echo esc_html($key) ?>][serial]"
                           value="<?php echo esc_html($ul['serial']) ?>"
                           placeholder="Серийный номер"
                           <?php echo disabled($sended); ?>
                    >
                <?php } ?>

                <input type="hidden"
					   class="order-pay-tab-input"
					   name="order[unit_loads][<?php echo esc_html($key) ?>][name]"
					   value="<?php echo esc_html($ul['name']) ?>"
	                   <?php echo disabled($sended); ?>
				>
            </td>
            <td>
                <input type="text"
					   class="order-pay-tab-input"
					   name="order[unit_loads][<?php echo esc_html($key) ?>][qty]"
					   value="<?php echo esc_html($ul['qty']) ?>"
	                   <?php echo disabled($sended); ?>
				>
            </td>
            <td>
				<input type="text"
					   class="order-pay-tab-input"
					   name="order[unit_loads][<?php echo esc_html($key) ?>][declared_value]"
					   value="<?php echo isset($ul['declared_value']) ? esc_html($ul['declared_value']) : 0 ?>"
				       <?php echo disabled(!$useCargoValue); ?>
                       <?php echo disabled($sended); ?>
				   >
            </td>
            <td>
				<input type="text"
					   class="order-pay-tab-input"
					   name="order[unit_loads][<?php echo esc_html($key) ?>][npp_amount]"
					   value="<?php echo isset($ul['npp_amount']) ? esc_html($ul['npp_amount']) : 0 ?>"
					   <?php echo disabled(!$npp); ?>
					   <?php echo disabled($sended); ?>
				>
            </td>
            <td>
                <select name="order[unit_loads][<?php echo esc_html($key) ?>][tax]"
                    <?php echo disabled($sended); ?>>

					<option value=""
                            <?php selected($ul['tax'], '') ?>
					>
                        <?php echo __('Whithout tax', 'woo-dpd'); ?>
                    </option>

                    <option value="0" 
						    <?php selected($ul['tax'], '0') ?>
					>
                        <?php echo __('0%', 'woo-dpd'); ?>
                    </option>

                    <option value="10"
						    <?php selected($ul['tax'], '10') ?>
	                >
                        <?php echo __('10%', 'woo-dpd'); ?>
                    </option>

                    <option value="20"
	                        <?php selected($ul['tax'], '20') ?>
					>
                        <?php echo __('20%', 'woo-dpd'); ?>
                    </option>
                </select>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h3><?php echo __('Declared value', 'woo-dpd'); ?></h3>
<table class="form-table dpd">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_recipient_fio">
                <?php echo __('Indicate the declared value', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_use_cargo_value">
                    <input class=""
						   type="checkbox"
						   name="order[use_cargo_value]"
                           id="dpd_use_cargo_value"
                           <?php echo checked($useCargoValue);?>
                           <?php echo disabled($sended); ?>
                    >
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_cargo_value">
                <?php echo __('Cargo value', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_cargo_value">
					<input class=""
						   type="text"
						   name="order[cargo_value]"
						   readonly=""
						   id="dpd_cargo_value"
						   value="<?php  echo esc_html($cargoValue); ?>"
						   <?php echo disabled($sended); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr>
</table>
<h3><?php echo __('C.O.D', 'woo-dpd'); ?></h3>
<table class="form-table dpd">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_npp">
                <?php echo __('Use npp', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_npp">
					<input class=""
						   type="checkbox"
						   name="order[npp]"
						   id="dpd_npp"
						   <?php echo checked($npp); ?>
						   <?php echo disabled($sended); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sum_npp">
                <?php echo __('Npp sum', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_sum_npp">
					<input class=""
						   type="text"
						   name="order[sum_npp]"
						   readonly=""
						   id="dpd_sum_npp"
						   value="<?php echo esc_html($sumNpp); ?>"
						   <?php echo disabled($sended); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr>
</table>
