 <?php
    $activeTab = isset($_GET['active_dpd_tab']) ? sanitize_text_field($_GET['active_dpd_tab']) : 'main';
    
    $tabs = [
        'main' => __('Main settings', 'woo-dpd'),
    ];
    
    if (get_option('dpd_first_data_import_completed')) {
        $tabs['dimensions']           = __('Dimensions', 'woo-dpd');
        $tabs['calculation']          = __('Delivery calculation', 'woo-dpd');
        $tabs['sender']               = __('Sender', 'woo-dpd');
        $tabs['recipient']            = __('Recipient', 'woo-dpd');
        $tabs['shipping_description'] = __('Shipping Description', 'woo-dpd');
        $tabs['status']               = __('Status', 'woo-dpd');
        $tabs['import']               = __('Import data', 'woo-dpd');
    }

    $tabs['faq'] = 'FAQ';

    ini_set('default_socket_timeout', 600);

    $checker = \Ipol\DPD\SettingChecker::getInstance();
    
    $showSettingChecker = get_option('dpd_show_setting_checker');
    $showFaqNotice      = get_option('dpd_show_faq_notice');
?>
    <? if (!$checker->itOkay() || !$showSettingChecker) { ?>
        <? update_option('dpd_show_setting_checker', 1); ?>

        <div class="notice notice-info inline">
            <p>
                <b>Внимание:</b>
                Ваша система должна соответствовать обязательным параметрам. Если какой-либо
                из этих параметров выделен красным цветом, то вам необходимо исправить его.
                В противном случае работоспособность модуля не гарантируется.
            </p>
        </div>

        <?= $checker->print() ?>

        <div class="" style="text-align: center">
            <input type="button" 
                class="button-primary" 
                value="<?= !$checker->itOkay() ? 'Проверить снова' : 'Продолжить' ?>"
                onclick="document.location.href = document.location.href"
            >
        </div>

        <style>
            .woocommerce-save-button {
                display: none !important;
            }
        </style>
    
    <? } else { ?>

        <? if (!$showFaqNotice) { ?>
            <? update_option('dpd_show_faq_notice', 1) ?>

            <a href="#TB_inline?width=200&height=100&inlineId=modal-faq" id="modal-faq-link" class="thickbox"></a>

            <div id="modal-faq" style="display:none;">
                <? require __DIR__ .'/tabs/faq.php' ?>
            </div>

            <script>
                jQuery(function() {
                    setTimeout(function() {
                        jQuery('#modal-faq-link').trigger('click')
                    }, 500)
                })
            </script>

        <? } ?>

        <nav class="nav-tab-wrapper woo-nav-tab-wrapper" data-tabs-content-level="1">
            <?php foreach ($tabs as $id => $tabname): ?>
                <a href="#"
                class="nav-tab dpd-tab <?php echo esc_attr($id == $activeTab ? 'nav-tab-active' : ''); ?>"
                data-tab-content-id="<?php echo esc_html($id); ?>"
                >
                    <?php echo esc_html($tabname); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="tab-wrapper">
            <?php foreach ($tabs as $id => $tabname): ?>
                <div class="dpd-tab-content-1"
                    id="<?php echo esc_html($id); ?>"
                    <?php echo ($id == $activeTab ? '' : 'style="display:none;"'); ?>
                >
                    <?php echo \DPD\Helper\View::load('backend/settings/tabs/'.$id); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <? } ?>