<table class="form-table dpd">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_fio">
                <?php echo __('Contact person', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
					   name="order[sender][fio]"
					   value="<?php echo esc_html($sender['fio']); ?>"
					   id="dpd_sender_fio"
					   <?php echo disabled($sended); ?>
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_name">
                <?php echo __('Contact name', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
					   name="order[sender][name]"
					   value="<?php echo esc_html($sender['name']); ?>"
					   id="dpd_sender_name"
					   <?php echo disabled($sended); ?>
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_phone">
                <?php echo __('Phone', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
					   name="order[sender][phone]"
					   value="<?php echo esc_html($sender['phone']); ?>"
					   id="dpd_sender_phone"
					   <?php echo disabled($sended); ?>
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_email">
                <?php echo __('Email', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="email"
					   name="order[sender][email]"
					   value="<?php echo esc_html($sender['email']); ?>"
					   id="dpd_sender_email"
					   <?php echo disabled($sended); ?>
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_need_pass">
                <?php echo __('Require Pass', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_sender_need_pass">
					<input class=""
						   type="checkbox" n
						   ame="order[sender][need_pass]"
						   id="dpd_sender_need_pass" value="1"
						   <?php echo checked($sender['need_pass']); ?>
						   <?php echo disabled($sended); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr> 
</table>
<h3><?php echo __('Location', 'woo-dpd'); ?></h3>
<table class="form-table dpd">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_city"><?php echo __('Sender', 'woo-dpd'); ?></label>
        </th>
        <td class="forminp">
            <fieldset>
                <select name="order[sender_idx]" id="dpd_sender">
                    <option value="OTHER">Другой</option>

                    <?php
                        $senderSelected = false;
                    ?>

                    <?php foreach (\DPD\Helper\Sender::getList() as $k => $_sender) { ?>
                        <?php
                            $senderSelected = $senderSelected || $sender['city_id'] == $_sender['city_id'];
                        ?>

                        <option value="<?php echo esc_html($k) ?>"
                                <?php selected($sender['city_id'], $_sender['city_id']) ?>
                                data-value='<?php echo json_encode($_sender) ?>'
                        ><?php echo esc_html($_sender['name']) ?></option>
                    <?php } ?>
                </select>
            </fieldset>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_city"><?php echo __('City', 'woo-dpd'); ?></label>
        </th>
        <td class="forminp">
            <fieldset>
                <input id="dpd_sender_city"
					   class="dpd-city-autocomplete dpd-no-ajax-update"
					   type="text"
					   name="order[sender][city]"
					   value="<?php echo esc_html($sender['city']); ?>"
					   <?php echo wp_readonly($senderSelected); ?>
				>

				<input id="dpd_sender_city_id"
					   type="hidden"
					   name="order[sender][city_id]"
					   value="<?php echo esc_html($sender['city_id']); ?>"
					   <?php echo disabled($senderSelected); ?>
				>
            </fieldset>
        </td>
    </tr>
</table>
<div class="tab-wrapper order">
    <div class="dpd-tab-content-2" id="dpd_door" style="<?php esc_attr($deliveryVariant == 'ТД' || $deliveryVariant == 'ТТ' ? 'display:none' : '') ?>">
        <table class="form-table dpd">
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_sender_street">
                        <?php echo __('Street', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[sender][street]"
							   value="<?php echo esc_html($sender['street']) ?>"
							   id="dpd_sender_street"
							   <?php echo disabled($sended || $senderSelected); ?>
                        >
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_sender_streetabbr">
                        <?php echo __('Street abbreviation', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[sender][streetabbr]"
							   value="<?php echo esc_html($sender['streetabbr']) ?>"
							   id="dpd_sender_streetabbr"
							   <?php echo disabled($sended || $senderSelected); ?>
                        >
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_sender_house">
                        <?php echo __('Housing', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[sender][house]"
							   value="<?php echo esc_html($sender['house']) ?>"
							   id="dpd_sender_korpus"
                               <?php echo disabled($sended || $senderSelected); ?>
                        >
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_sender_korpus">
                        <?php echo __('Korpus', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[sender][korpus]"
							   value="<?php echo esc_html($sender['korpus']) ?>"
							   id="dpd_sender_korpus"
                               <?php echo disabled($sended || $senderSelected); ?>
                        >
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_sender_str">
                        <?php echo __('Structure', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[sender][str]"
							   value="<?php echo esc_html($sender['str']) ?>"
							   id="dpd_sender_str"
                               <?php echo disabled($sended || $senderSelected); ?>
                        >
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_sender_vlad">
                        <?php echo __('Possession', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[sender][vlad]"
							   value="<?php echo esc_html($sender['vlad']) ?>"
							   id="dpd_sender_vlad"
                               <?php echo disabled($sended || $senderSelected); ?>
                        >
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_sender_office">
                        <?php echo __('Office', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[sender][office]"
							   value="<? esc_html($sender['office']) ?>"
							   id="dpd_sender_office"
                               <?php echo disabled($sended || $senderSelected); ?>
                        >
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_sender_flat">
                        <?php echo __('Flat', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[sender][flat]"
							   value="<?php echo esc_html($sender['flat']) ?>"
							   id="dpd_sender_flat"
                               <?php echo disabled($sended || $senderSelected); ?>
                        >
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
    <div class="dpd-tab-content-2" id="dpd_terminal" style="<?php echo esc_attr($deliveryVariant == 'ДТ' || $deliveryVariant == 'ДД' ? 'display: none' : '') ?>">
        <table class="form-table dpd">
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_sender_terminal_code">
                        <?php echo __('Terminal', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <select name="order[sender][terminal_code]"
								id="dpd_sender_terminal_code"
                                <?php echo disabled($sended || $senderSelected); ?>
                        >
                            <?php foreach ($senderTerminals as $terminal) { ?>
                                <option value="<?php echo esc_html($terminal['CODE']); ?>" 
                                    <?php selected($sender['terminal_code'], $terminal['CODE']) ?>
                                >
                                    <?php echo esc_html($terminal['ADDRESS_SHORT']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
</div>

<style>
    .autocomplete-suggestions  {
        z-index: 100052;
    }
</style>