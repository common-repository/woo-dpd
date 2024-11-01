<table class="form-table">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_fio">
                <?php echo __('Contact person', 'woo-dpd'); ?>
                <span class="required">*</span>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
                    name="dpd[sender_fio]"
                    value="<?php echo esc_html(get_option('dpd_sender_fio')); ?>"
                    id="dpd_sender_fio">
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_name">
                <?php echo __('Contact name', 'woo-dpd'); ?>
                <span class="required">*</span>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
                    name="dpd[sender_name]"
                    value="<?php echo esc_html(get_option('dpd_sender_name')); ?>"
                    id="dpd_sender_name">
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_phone">
                <?php echo __('Phone', 'woo-dpd'); ?>
                <span class="required">*</span>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
                    name="dpd[sender_phone]"
                    value="<?php echo esc_html(get_option('dpd_sender_phone')); ?>"
                    id="dpd_sender_phone">
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_email">
                <?php echo __('Email', 'woo-dpd'); ?>
                <span class="required">*</span>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="email"
                    name="dpd[sender_email]"
                    value="<?php echo esc_html(get_option('dpd_sender_email')); ?>"
                    id="dpd_sender_email">
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="dpd_sender_regular_num">
                <?php echo __('Regular number', 'woo-dpd'); ?>
            </label>
        </th>
        <td class="forminp">
            <fieldset>
                <input type="text"
                    name="dpd[sender_regular_num]"
                    value="<?php echo esc_html(get_option('dpd_sender_regular_num')); ?>"
                    id="dpd_sender_regular_num">
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
                <input type="hidden" name="dpd[sender_need_pass]" value="0">
                <input class="" type="checkbox" name="dpd[sender_need_pass]"
                    id="dpd_sender_need_pass" value="1" 
                    <?php echo checked(get_option('dpd_sender_need_pass')); ?>
                >
                </label>
            </fieldset>
        </td>
    </tr> 
</table>

<h3><?php echo __('Location', 'woo-dpd'); ?></h3>

<?php
    $senders = \DPD\Helper\Sender::getList(true);
?>

<nav class="nav-tab-wrapper woo-nav-tab-wrapper" data-tabs-content-level="2">
    <?php $first = true; foreach ($senders as $k => $sender) { ?>
        <a href="#" class="nav-tab dpd-tab <?php echo esc_attr($first ? 'nav-tab-active' : '') ?>" data-tab-content-id="sender-<?php echo esc_html($k); ?>">
            <?php echo esc_html($sender['name'] ?: 'Новый') ?>
        </a>
    <?php $first = false; } ?>
</nav>

<div class="tab-wrapper">
    <?php $first = true; foreach($senders as $k => $sender) { ?>
        <div class="dpd-tab-content-2" id="sender-<?php echo esc_html($k) ?>" <?php echo ($first ? '' : 'style="display:none"') ?>>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="dpd_sender_<?php echo esc_html($k) ?>_default">Использовать по умолчанию</label>
                    </th>

                    <td class="forminp">
                        <fieldset>
                            <input id="dpd_sender_<?php echo esc_html($k) ?>_default" type="hidden"   autocomplete="off" name="dpd[sender][<?php echo esc_html($k); ?>][default]" value="0">
                            <input id="dpd_sender_<?php echo esc_html($k) ?>_default" type="checkbox" autocomplete="off" name="dpd[sender][<?php echo esc_html($k); ?>][default]" value="1" <?php echo checked($sender['default']);?>>
                        </fieldset>
                    </td>
                </tr>
            
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="dpd_sender_<?php echo esc_html($k) ?>_name">Наименование</label>
                    </th>

                    <td class="forminp">
                        <fieldset>
                            <input id="dpd_sender_<?php echo esc_html($k) ?>_name" type="text" autocomplete="off" name="dpd[sender][<?php echo esc_html($k); ?>][name]" value="<?php echo esc_html($sender['name']) ?>">
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="dpd_sender_<?php echo esc_html($k) ?>_city"><?php echo __('City', 'woo-dpd') ?></label>
                    </th>

                    <td class="forminp">
                        <fieldset>
                            <input id="dpd_sender_<?php echo esc_html($k) ?>_city" class="dpd-sender-city" type="text" autocomplete="off" name="dpd[sender][<?php echo esc_html($k); ?>][city]" value="<?php echo esc_html($sender['city']) ?>">
                            <input id="dpd_sender_<?php echo esc_html($k) ?>_city_id" type="hidden"  name="dpd[sender][<?php echo esc_html($k); ?>][city_id]" value="<?php echo esc_html($sender['city_id']) ?>">
                        </fieldset>
                    </td>
                </tr>
            </table>

            <nav class="nav-tab-wrapper woo-nav-tab-wrapper" data-tabs-content-level="3-<?php echo esc_html($k); ?>">
                <a href="#" class="nav-tab dpd-tab nav-tab-active" data-tab-content-id="dpd_sender_<?php echo esc_html($k); ?>_door"><?php echo __('To door', 'woo-dpd'); ?></a>
                <a href="#" class="nav-tab dpd-tab" data-tab-content-id="dpd_sender_<?php echo esc_html($k); ?>_terminal"><?php echo __('Terminal', 'woo-dpd'); ?></a>
            </nav>

            <div class="tab-wrapper">
                <div class="dpd-tab-content-3-<?php echo esc_html($k); ?>" id="dpd_sender_<?php echo esc_html($k); ?>_door">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_sender_<?php echo esc_html($k); ?>_street"><?php echo __('Street', 'woo-dpd'); ?></label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <input id="dpd_sender_<?php echo esc_html($k); ?>_street" type="text" name="dpd[sender][<?php echo esc_html($k); ?>][street]" value="<?php echo esc_html($sender['street']) ?>">
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_sender_<?php echo esc_html($k); ?>_streetabbr"><?php echo __('Street abbreviation', 'woo-dpd'); ?></label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <input id="dpd_sender_<?php echo esc_html($k); ?>_streetabbr" type="text" name="dpd[sender][<?php echo esc_html($k); ?>][streetabbr]" value="<?php echo esc_html($sender['streetabbr']) ?>">
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_sender_<?php echo esc_html($k); ?>_house">
                                    <?php echo __('Housing', 'woo-dpd'); ?>
                                </label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <input id="dpd_sender_<?php echo esc_html($k); ?>_house" type="text" name="dpd[sender][<?php echo esc_html($k); ?>][house]" value="<?php echo esc_html($sender['house']) ?>">
                                </fieldset>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_sender_<?php echo esc_html($k); ?>_korpus"><?php echo __('Korpus', 'woo-dpd'); ?></label>
                            </th>

                            <td class="forminp">
                                <fieldset>
                                    <input id="dpd_sender_<?php echo esc_html($k); ?>_korpus" type="text" name="dpd[sender][<?php echo esc_html($k); ?>][korpus]" value="<?php echo esc_html($sender['korpus']) ?>">
                                </fieldset>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_sender_<?php echo esc_html($k); ?>_str"><?php echo __('Structure', 'woo-dpd'); ?></label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <input id="dpd_sender_<?php echo esc_html($k); ?>_str" type="text" name="dpd[sender][<?php echo esc_html($k); ?>][str]" value="<?php echo esc_html($sender['str']) ?>">
                                </fieldset>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_sender_<?php echo esc_html($k); ?>_vlad">
                                    <?php echo __('Possession', 'woo-dpd'); ?>
                                </label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <input id="dpd_sender_<?php echo esc_html($k); ?>_vlad" type="text" name="dpd[sender][<?php echo esc_html($k); ?>][vlad]" value="<?php echo esc_html($sender['vlad']) ?>">
                                </fieldset>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_sender_<?php echo esc_html($k); ?>_office"><?php echo __('Office', 'woo-dpd'); ?></label>
                            </th>

                            <td class="forminp">
                                <fieldset>
                                    <input id="dpd_sender_<?php echo esc_html($k); ?>_office" type="text" name="dpd[sender][<?php echo esc_html($k); ?>][office]" value="<?php echo esc_html($sender['office']) ?>">
                                </fieldset>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_sender_<?php echo esc_html($k); ?>_flat">
                                    <?php echo __('Flat', 'woo-dpd'); ?>
                                </label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <input id="dpd_sender_<?php echo esc_html($k); ?>_flat" type="text" name="dpd[sender][<?php echo esc_html($k); ?>][flat]" value="<?php echo esc_html($sender['flat']) ?>">
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="dpd-tab-content-3-<?php echo esc_html($k); ?>" id="dpd_sender_<?php echo esc_html($k); ?>_terminal" style="display: none;">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="dpd_sender_<?php echo esc_html($k); ?>_terminal_code">
                                    <?php echo __('Terminal', 'woo-dpd'); ?>
                                </label>
                            </th>
                            <td class="forminp">
                                <fieldset>
                                    <select id="dpd_sender_<?php echo esc_html($k); ?>_terminal_code" name="dpd[sender][<?php echo esc_html($k); ?>][terminal_code]" data-city="<?php echo esc_html($sender['city_id']) ?>" data-value="<?php echo esc_html($sender['terminal_code']) ?>" class="dpd-sender-terminal" disabled="">
                                    </select>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    <?php $first = false; } ?>
</div>