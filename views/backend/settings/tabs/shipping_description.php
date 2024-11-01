<table class="form-table">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_self_pickup">
                <?php echo __('Sending method', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <?php
                    $dpdSelfPickup = 
                        get_option('dpd_self_pickup');
                ?>
                <select class="dpd-select"
                    type="text" name="dpd[self_pickup]"
                    id="dpd_self_pickup">
                    <option value="1"
                        <?php selected($dpdSelfPickup, '1') ?>
                    >
                        <?php echo __('We will carry orders ourselves', 'woo-dpd'); ?>
                    </option>
                    <option value="0"
                        <?php selected($dpdSelfPickup, '0') ?>
                    >
                        <?php echo __('We want to call the fence automatically', 'woo-dpd'); ?>
                    </option>
                </select>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_payment_type">
                <?php echo __('Payment method delivery', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <?php $dpdPaymentType = get_option('dpd_payment_type');
                ?>
                <select class="dpd-select"
                    type="text" name="dpd[payment_type]"
                    id="dpd_payment_type">
                    <option value=""
                        <?php selected($dpdPaymentType, ''); ?>
                    >
                        <?php echo __('By sender by wire transfer', 'woo-dpd'); ?>
                    </option>

                    <option value="<?php echo esc_attr(\Ipol\DPD\Order::PAYMENT_TYPE_OUP); ?>"
                        <?php selected($dpdPaymentType, \Ipol\DPD\Order::PAYMENT_TYPE_OUP) ?>
                    >
                        <?php echo __('Payment at the recipient in cash', 'woo-dpd'); ?>
                    </option>

					<option value="<?php echo esc_attr(\Ipol\DPD\Order::PAYMENT_TYPE_OUO); ?>"
                        <?php selected($dpdPaymentType, \Ipol\DPD\Order::PAYMENT_TYPE_OUO) ?>
                    >
                        <?php echo __('Payment at the sender in cash', 'woo-dpd'); ?>
                    </option>
                </select>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_pickup_time_period">
                <?php echo __('DPD Transit Time Interval', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <?php
                    $dpdPickupTimePeriod = 
                        get_option('dpd_pickup_time_period');
                ?>
                <select class="dpd-select"
                    type="text" name="dpd[pickup_time_period]"
                    id="dpd_pickup_time_period"
				>
                    <option value="9-18"
                        <?php selected($dpdPickupTimePeriod, '9-18') ?>
                    >
                        <?php echo __('any time from 09:00 to 18:00', 'woo-dpd'); ?>
                    </option>

                    <option value="9-13"
                        <?php selected($dpdPickupTimePeriod, '9-13') ?>
                    >
                        <?php echo __('from 09:00 to 13:00', 'woo-dpd'); ?>
                    </option>

                    <option value="13-18"
                        <?php selected($dpdPickupTimePeriod == '13-18') ?>
                    >
                        <?php echo __('13:00 to 18:00', 'woo-dpd'); ?>
                    </option>
                </select>
            </fieldset>
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
                <?php $dpdDeliveryTimePeriod = get_option('dpd_delivery_time_period'); ?>
                <select class="dpd-select"
                    type="text" name="dpd[delivery_time_period]"
                    id="dpd_delivery_time_period"
				>
                    <option value="9-18"
                        <?php selected($dpdDeliveryTimePeriod, '9-18') ?>
                    >
                        <?php echo __('any time from 09:00 to 18:00', 'woo-dpd'); ?>
                    </option>

                    <option value="9-14"
                        <?php selected($dpdDeliveryTimePeriod, '9-14') ?>
                    >
                        <?php echo __('from 09:00 to 14:00', 'woo-dpd'); ?>
                    </option>

                    <option value="13-18"
                        <?php selected($dpdDeliveryTimePeriod, '13-18') ?>
                    >
                        <?php echo __('13:00 to 18:00', 'woo-dpd'); ?>
                    </option>

                    <option value="18-22"
                        <?php selected($dpdDeliveryTimePeriod, '18-22') ?>
                    >
                        <?php echo __('18:00 to 22:00 (extra charge)', 'woo-dpd'); ?>
                    </option>
                </select>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_cargo_num_pack">
                <?php echo __('Number of cargo spaces (parcels)', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_cargo_num_pack">
					<input type="text"
						   name="dpd[cargo_num_pack]"
						   id="dpd_cargo_num_pack"
						   value="<?php echo esc_html(get_option('dpd_cargo_num_pack')); ?>"
					>
                </label>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_cargo_category">
                <?php echo __('Sending content', 'woo-dpd'); ?>
                <span class="required">*</span>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_cargo_category">
					<input type="text"
						   name="dpd[cargo_category]"
						   id="dpd_cargo_category"
						   value="<?php echo esc_html(get_option('dpd_cargo_category')); ?>"
					>
                </label>
            </fieldset>
        </td>
    </tr>
</table>
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
					<input type="hidden" name="dpd[cargo_registered]" value="0">
					<input class=""
						   type="checkbox"
						   name="dpd[cargo_registered]"
						   id="dpd_cargo_registered"
						   value="1"
						   <?php echo checked(get_option('dpd_cargo_registered')); ?>
					>
                </label>
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
					<input type="hidden" name="dpd[dvd]" value="0">
					<input class=""
						   type="checkbox"
						   name="dpd[dvd]"
						   id="dpd_dvd"
						   value="1"
						  <?php	echo checked(get_option('dpd_dvd')); ?>
					>
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
					<input type="hidden" name="dpd[trm]" value="0">
					<input class=""
						   type="checkbox"
						   name="dpd[trm]"
						   id="dpd_trm"
						   value="1"
						   <?php echo checked(get_option('dpd_trm')); ?>
					>
                </label>
                <small>
                    <br>Поддержание температуры окружающей среды на уровне не ниже +5°C для сохранности груза.
                    <br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?>
                </small>
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
					<input type="hidden" name="dpd[prd]" value="">
					<input class=""
						   type="checkbox"
						   name="dpd[prd]"
						   id="dpd_prd"
						   value="1"
						   <?php echo checked(get_option('dpd_prd')); ?>
					>
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
					<input type="hidden" name="dpd[vdo]" value="0">
					<input class=""
						   type="checkbox"
						   name="dpd[vdo]"
						   id="dpd_vdo"
						   value="1"
						   <?php echo checked(get_option('dpd_vdo')); ?>
                >
                </label>
                <small><br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?></small>
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_obr">Обрешетка</label>
        </th>
        <td class="forminp">
            <fieldset>
                <label for="dpd_obr">
					<input type="hidden" name="dpd[obr]" value="0">
					<input class=""
						   type="checkbox"
						   name="dpd[obr]"
						   id="dpd_obr"
						   value="1"
						   <?php echo checked(get_option('dpd_obr')); ?>
                >
                </label>
                <small>
                    <br>Жесткая упаковка грузового места/посылки, представляющая деревянный каркас и предназначающаяся для перевозки хрупких и нестандартных грузов.
                    <br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?>
                </small>
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
                <?php $dpdOGD = get_option('dpd_ogd'); ?>
                <select class="dpd-select"
                    type="text" name="dpd[ogd]"
                    id="dpd_ogd"
				>
                    <option value="0">
                        <?php echo __('- Not selected -', 'woo-dpd'); ?>
                    </option>

                    <option value="ПРИМ"
                        <?php selected($dpdOGD, 'ПРИМ') ?>
                    >
                        <?php echo __('Fitting', 'woo-dpd'); ?>
                    </option>

                    <option value="ПРОС"
                        <?php selected($dpdOGD, 'ПРОС') ?>
                    >
                        <?php echo __('Simple', 'woo-dpd'); ?>
                    </option>

                    <option value="РАБТ"
                        <?php selected($dpdOGD, 'РАБТ') ?>
                    >
                        <?php echo __('Health check', 'woo-dpd'); ?>
                    </option>
                </select>
                <small>
                    <br>Возможность примерки, проверки работоспособности и комплектности товара в пункте выдачи или при доставке до адреса Получателя (физическое лицо).
                    <br>Время ожидания курьера на адресе получателя - 20 минут. 
                    <br>В случае необходимости, курьер заберет весь заказ обратно. Возврат части отправки в рамках этой опции недоступен.
                    <br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?>
                </small>
            </fieldset>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_chst">
                Частичный выкуп
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <?php $dpdCHST = get_option('dpd_chst'); ?>
                <select class="dpd-select"
                    type="text" name="dpd[chst]"
                    id="dpd_chst"
				>
                    <option value="0">
                        <?php echo __('- Not selected -', 'woo-dpd'); ?>
                    </option>

                    <option value="ПРИМ" <?php selected($dpdCHST, 'ПРИМ') ?>>
                        <?php echo __('Fitting', 'woo-dpd'); ?>
                    </option>

                    <option value="ПРОС" <?php selected($dpdCHST, 'ПРОС') ?>>
                        <?php echo __('Simple', 'woo-dpd'); ?>
                    </option>

                    <option value="РАБТ" <?php selected($dpdCHST, 'РАБТ') ?>>
                        <?php echo __('Health check', 'woo-dpd'); ?>
                    </option>
                </select>
                <small>
                    <br>Опция «Частичный выкуп» позволяет получателю отказаться от части вложений заказа при доставке (взять не всю отправку, а только часть товаров из нее). Можно установить минимальную сумму выкупа, при которой получатель не будет оплачивать услуги доставки.
                    <br><?php echo __('option paid, check with the manager', 'woo-dpd'); ?>
                </small>
            </fieldset>
        </td>
    </tr>

    <tr valign="top">
        <?php $goods_return_amount = get_option('dpd_goods_return_amount') ?>
        <th scope="row" class="titledesc">
            <label for="dpd_goods_return_amount">Минимальная сумма выкупа, при достижении которой доставка будет бесплатной</label>
        </th>
        <td class="forminp">
            <fieldset>
                <input
                    id="dpd_goods_return_amount"
                    type="text"
                    name="dpd[goods_return_amount]"
                    value="<?=$goods_return_amount?>"
                    <?php echo disabled($sended); ?>
                >
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <?php $delivery_amount = get_option('dpd_delivery_amount') ?>
        <th scope="row" class="titledesc">
            <label for="dpd_delivery_amount">Сумма за доставку</label>
        </th>
        <td class="forminp">
            <fieldset>
                <input
                    id="dpd_delivery_amount"
                    type="text"
                    name="dpd[delivery_amount]"
                    value="<?=$delivery_amount?>"
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
						   name="dpd[esz]"
						   id="dpd_esz"
						   value="<?php echo get_option('dpd_esz'); ?>">
                </label>
            </fieldset>
        </td>
    </tr>
</table>