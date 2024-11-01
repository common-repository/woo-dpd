<?php
    $subTabActive = isset($_GET['dpd-subtab']) ? sanitize_text_field($_GET['dpd-subtab']) : 'RU';
    $tabs = [
        'RU' => __('Russia', 'woo-dpd'),
        'KZ' => __('Kazakhstan', 'woo-dpd'),
        'BY' => __('Belarus', 'woo-dpd'),
        'KG' => __('Киргызстан', 'woo-dpd'),
        'AM' => __('Армения', 'woo-dpd'),
    ];
    $dpdAccountDefaultCountry = get_option('dpd_account_default_country') ?
        get_option('dpd_account_default_country') : 'RU';
?>
<div id="message" class="notice notice-info inline">
    <p><?php echo  __('You can get the integration key by following the link <a href="http://www.dpd.ru/ols/order/personal/integrationkey.do2" target="_blank">http://www.dpd.ru/ols/order/personal/integrationkey.do2</a> in your MyDPD account or by contacting the support service by email integrators@dpd.ru', 'woo-dpd'); ?></p>
</div>
<nav class="nav-tab-wrapper woo-nav-tab-wrapper" data-tabs-content-level="2">
    <?php foreach($tabs as $id => $tabname): ?>
        <a href="#"
		   class="nav-tab dpd-tab <?php echo esc_attr($id == $subTabActive ? 'nav-tab-active' : ''); ?>"
		   data-tab-content-id="dpd_<?php echo esc_html($id); ?>"
		>
            <?php echo esc_html($tabname); ?>
        </a>
    <?php endforeach; ?>
</nav>
<div class="tab-wrapper">
    <?php foreach($tabs as $id => $tabname): ?>
        <div class="dpd-tab-content-2"
			 id="dpd_<?php echo esc_html($id); ?>"
            <?php echo ($id == $subTabActive ? '' : 'style="display:none;"'); ?>
        >
            <div class="dpd-tab-content-border">
                <table class="form-table">
                    <? if (in_array($id, ['RU', 'BY', 'KZ'])) { ?>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_client_number_<?php echo esc_html($id); ?>">
                                    <?php echo __('Client Number', 'woo-dpd'); ?>
                                </label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <input type="text"
                                        name="dpd[client_number_<?php echo esc_html($id); ?>]"
                                        value="<?php echo get_option('dpd_client_number_'.$id); ?>"
                                        id="dpd_client_number_<?php echo esc_html($id); ?>"
                                    >
                                </fieldset>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_auth_key_<?php echo esc_html($id); ?>">
                                    <?php echo __('Authorization key', 'woo-dpd'); ?>
                                </label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <input type="text"
                                        name="dpd[auth_key_<?php echo esc_html($id); ?>]"
                                        value="<?php echo get_option('dpd_auth_key_'.$id); ?>"
                                        id="dpd_auth_key_<?php echo esc_html($id); ?>"
                                    >
                                </fieldset>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_client_number_test_<?php echo esc_html($id); ?>">
                                    <?php echo __('Client Number Test', 'woo-dpd'); ?>
                                </label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <input type="text"
                                        name="dpd[client_number_test_<?php echo esc_html($id); ?>]"
                                        value="<?php echo get_option('dpd_client_number_test_'.$id); ?>"
                                        id="dpd_client_number_test_<?php echo esc_html($id); ?>"
                                    >
                                </fieldset>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_auth_key_test_<?php echo esc_html($id); ?>">
                                    <?php echo __('Authorization key test', 'woo-dpd'); ?>
                                </label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <input type="text"
                                        name="dpd[auth_key_test_<?php echo esc_html($id); ?>]"
                                        value="<?php echo get_option('dpd_auth_key_test_'.$id); ?>"
                                        id="dpd_auth_key_test_<?php echo esc_html($id); ?>"
                                    >
                                </fieldset>
                            </td>
                        </tr>
                    <? } ?>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="dpd_currency_<?php echo esc_html($id); ?>">
                                <?php echo __('Account currency', 'woo-dpd'); ?>
                            </label>
                        </th>
                        <td class="forminp">
                            <?php
                                $currency = get_option('dpd_currency_'.$id);
                            ?>
                            <fieldset>
                                <select id="dpd_currency_<?php echo esc_html($id); ?>"
										name="dpd[currency_<?php echo esc_html($id); ?>]"
								>
                                    <option value="KZT"
                                        <?php selected($currency == 'KZT') ? 'selected="selected"' : ''; ?>
                                    ><?php echo __('Tenge', 'woo-dpd'); ?></option>

                                    <option value="RUB"
                                        <?php selected($currency == 'RUB' || !$currency, true) ?>
                                    ><?php echo __('Russian Ruble', 'woo-dpd'); ?></option>

                                    <option value="USD"
                                        <?php selected($currency == 'USD') ?>
                                    ><?php echo __('US Dollar', 'woo-dpd'); ?></option>

									<option value="EUR"
                                        <?php selected($currency == 'EUR') ?>
                                    ><?php echo __('Euro', 'woo-dpd'); ?></option>

									<option value="UAH"
                                        <?php selected($currency == 'UAH') ?>
                                    ><?php echo __('Hryvnia', 'woo-dpd'); ?></option>

									<option value="BYN"
                                        <?php selected($currency == 'BYN') ?>
                                    ><?php echo __('Belarusian ruble', 'woo-dpd'); ?></option>

                                    <option value="KGS"
                                        <?php selected($currency == 'KGS') ?>
                                    ><?php echo __('Киргизский сом', 'woo-dpd'); ?></option>

                                    <option value="AMD"
                                        <?php selected($currency == 'AMD') ?>
                                    ><?php echo __('Армянский драм', 'woo-dpd'); ?></option>
                                </select>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<table class="form-table">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_account_default_country">
                <?php echo __('Default account', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <select name="dpd[account_default_country]"
                    id="dpd_account_default_country">
                    <option value="RU"
						    <?php echo selected($dpdAccountDefaultCountry, 'RU') ?>
                    >
                        <?php echo __('Russia', 'woo-dpd'); ?>
                    </option>

					<option value="KZ"
                            <?php echo selected($dpdAccountDefaultCountry, 'KZ') ?>
                    >
                        <?php echo __('Kazakhstan', 'woo-dpd'); ?>
					</option>

                    <option value="BY"
                            <?php echo selected($dpdAccountDefaultCountry, 'BY') ?>
                    >
                        <?php echo __('Belarus', 'woo-dpd'); ?>
                    </option>
                </select>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_test_mode">
                <?php echo __('Test mode', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_test_mode">
					<input type="hidden" name="dpd[test_mode]" value="0">
					<input class=""
						   type="checkbox"
						   name="dpd[test_mode]"
						   id="dpd_test_mode" value="1"
						   <?php echo checked(get_option('dpd_test_mode')); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_required_pickpoint_selection">
                <?php echo __('Pick pount selection required', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_required_pickpoint_selection">
					<input type="hidden" name="dpd[required_pickpoint_selection]" value="0">
					<input class=""
						   type="checkbox"
						   name="dpd[required_pickpoint_selection]"
						   id="dpd_required_pickpoint_selection" value="1"
						   <?php echo checked(get_option('dpd_required_pickpoint_selection')); ?>
					>
                </label>
            </fieldset>
        </td>
    </tr>
</table>