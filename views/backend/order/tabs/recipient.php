<table class="form-table dpd">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_recipient_fio">
                <?php echo __('Contact person', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
					   name="order[recipient][fio]"
					   value="<?php echo esc_html($recipient['fio']); ?>"
					   id="dpd_recipient_fio" <?php echo disabled($sended); ?>
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_recipient_name">
                <?php echo __('Contact name', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
					   name="order[recipient][name]"
					   value="<?php echo esc_html($recipient['name']); ?>"
					   id="dpd_recipient_name" <?php echo disabled($sended); ?>
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_recipient_phone">
                <?php echo __('Phone', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
					   name="order[recipient][phone]"
					   value="<?php echo esc_html($recipient['phone']); ?>"
					   id="dpd_recipient_phone" <?php echo disabled($sended); ?>
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_recipient_email">
                <?php echo __('Email', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="email"
					   name="order[recipient][email]"
					   value="<?php echo esc_html($recipient['email']); ?>"
					   id="dpd_recipient_email" <?php echo disabled($sended); ?>
				>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_recipient_need_pass">
                <?php echo __('Require Pass', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_recipient_need_pass">
					<input class=""
						   type="checkbox"
						   name="order[recipient][need_pass]"
						   id="dpd_recipient_need_pass" value="1"
						   <?php echo checked($recipient['need_pass']); ?>
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
            <label for="dpd_recipient_city">
                <?php echo __('City', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input id="dpd_recipient_city"
					   class="dpd-city-autocomplete dpd-no-ajax-update"
					   type="text"
					   name="order[recipient][location]"
					   value="<?php echo esc_html($recipient['location']); ?>"
					   <?php echo wp_readonly($sended); ?>
				>

                <input id="dpd_recipient_city_id"
					   type="hidden"
					   name="order[recipient][city_id]"
					   value="<?php echo esc_html($recipient['city_id']); ?>"
					   <?php echo disabled($sended); ?>
				>
            </fieldset>
        </td>
    </tr>
</table>
<div class="tab-wrapper order">
    <div class="dpd-tab-content-2" id="dpd_door" 
         style="<?php echo esc_attr($deliveryVariant == 'ДТ' || $deliveryVariant == 'ТТ' ? 'display:none' : ''); ?>"
    >
        <table class="form-table dpd">
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_street">
                        <?php echo __('Street', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[recipient][street]"
							   value="<?php echo esc_html($recipient['street']); ?>"
							   id="dpd_recipient_street" <?php echo disabled($sended); ?>
						>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_streetabbr">
                        <?php echo __('Street abbreviation', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[recipient][streetabbr]"
							   value="<?php echo esc_html($recipient['streetabbr']); ?>"
							   id="dpd_recipient_streetabbr"
                               <?php echo disabled($sended); ?>
						>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_house">
                        <?php echo __('Housing', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[recipient][house]"
							   value="<?php echo esc_html($recipient['house']); ?>"
							   id="dpd_recipient_korpus"
							   <?php echo disabled($sended); ?>
						>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_korpus">
                        <?php echo __('Korpus', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[recipient][korpus]"
							   value="<?php echo esc_html($recipient['korpus']); ?>"
							   id="dpd_recipient_korpus"
							   <?php echo disabled($sended); ?>
						>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_str">
                        <?php echo __('Structure', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[recipient][str]"
							   value="<?php echo esc_html($recipient['str']); ?>"
							   id="dpd_recipient_str"
							   <?php echo disabled($sended); ?>
						>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_vlad">
                        <?php echo __('Possession', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[recipient][vlad]"
							   value="<?php echo esc_html($recipient['vlad']); ?>"
							   id="dpd_recipient_vlad"
							   <?php echo disabled($sended); ?>
						>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_office">
                        <?php echo __('Office', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[recipient][office]"
							   value="<?php echo esc_html($recipient['office']); ?>"
							   id="dpd_recipient_office"
							   <?php echo disabled($sended); ?>
						>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_flat">
                        <?php echo __('Flat', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <input type="text"
							   name="order[recipient][flat]"
							   value="<?php echo esc_html($recipient['flat']); ?>"
							   id="dpd_recipient_flat"
							   <?php echo disabled($sended); ?>
						>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_comment"><?php echo __('Recipient Comment', 'woo-dpd')?></label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <textarea name="order[recipient][comment]" id="dpd_recipient_comment" style="width: 100%; height: 50px;"><?php echo esc_html($recipient['comment']) ?></textarea>
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
    <div class="dpd-tab-content-2" id="dpd_terminal"
         style="<?php echo esc_attr($deliveryVariant == 'ТД' || $deliveryVariant == 'ДД' ? 'display:none' : ''); ?>"
    >
        <table class="form-table dpd">
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_terminal_code">
                        <?php echo __('Terminal', 'woo-dpd'); ?>
                    </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <select name="order[recipient][terminal_code]"
								id="dpd_recipient_terminal_code"
                                <?php echo disabled($sended); ?>
						>
                            <?php foreach ($recipientTerminals as $terminal): ?>
                                <option value="<?php echo $terminal['CODE']; ?>" 
                                    <?php selected($recipient['terminal_code'], $terminal['CODE']) ?>
								>
                                    <?php echo esc_html($terminal['ADDRESS_SHORT']); ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="dpd_recipient_comment"><?php echo __('Recipient Comment', 'woo-dpd')?></label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <textarea name="order[recipient][comment]" id="dpd_recipient_comment" style="width: 100%; height: 50px;"><?php echo esc_html($recipient['comment']) ?></textarea>
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
</div>