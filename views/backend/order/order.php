<?php

    $activeTab = isset($_GET['dpd_active_tab']) ? sanitize_text_field($_GET['dpd_active_tab']) : 'order';
    $tabs = [
        'order' => __('Order', 'woo-dpd'),
        'sender' => __('Sender', 'woo-dpd'),
        'recipient' => __('Recipient', 'woo-dpd'),
        'payment' => __('Payment', 'woo-dpd'),
        'options' => __('Options', 'woo-dpd'),
        'docs' => __('Docs', 'woo-dpd')
    ];
?>
<form id="dpd_order" method="post">
    <div class="order-content">
        <input type="hidden" name="order[id]" value="<?php echo esc_html($order->ID); ?>">
        <div class="notifications">
            <?php if (!empty($errors)): ?>
                <div class="notice notice-error inline">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo esc_html($error); ?></p>
                    <?php endforeach; ?>  
                </div>
            <?php endif; ?>
            <?php if (!empty($notifications)): ?>
                <div class="notice notice-info inline">
                    <?php foreach ($notifications as $notification): ?>
                        <p><?php echo esc_html($notification); ?></p>
                    <?php endforeach; ?>  
                </div>
            <?php endif; ?>
        </div>
        <?php if (isset($tariff['COST'])): ?>
            <div class="order-delivery-price">
                <div id="message" class="notice notice-info inline">
                    <p>
                        <strong><?php echo  __('Calculated delivery price', 'woo-dpd'); ?>:</strong>
                         <?php echo esc_html($tariff['COST']); ?> <?php echo  get_woocommerce_currency_symbol(get_option('woocommerce_currency')); ?>
                    </p>
                    <p>
                        <strong><?php echo  __('Delivery time', 'woo-dpd'); ?>:</strong>
                         <?php echo esc_html($tariff['DAYS']); ?>  <?php echo  __('d', 'woo-dpd'); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        <nav class="nav-tab-wrapper woo-nav-tab-wrapper" data-tabs-content-level="1">
            <?php foreach ($tabs as $id => $tabname): ?>
                <a href="javascript:void(0);"
				   class="nav-tab dpd-tab
				   <?php  echo esc_attr($id == $activeTab ? 'nav-tab-active' : ''); ?>"
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
                     <?php echo esc_attr($id == $activeTab ? '' : 'style="display:none;"'); ?>
                >
                	<?php include(DPD_PLUGIN_PATH.'views/backend/order/tabs/'.$id.'.php');
                ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="order-buttons">
        <button class="button send-order button-primary"
            name="send" value="<?php echo __('Send Order', 'woo-dpd');?>"
            <?php if ($sended): ?>
                style="display:none";
            <?php endif; ?>
        >
            <?php echo __('Send Order', 'woo-dpd');?>
        </button>
        <button class="button cancel-order button-primary"
            name="save" value="<?php echo __('Cancel Order', 'woo-dpd');?>"
            <?php if (!$sended): ?>
                style="display:none";
            <?php endif; ?>
        >
            <?php echo __('Cancel Order', 'woo-dpd');?>
        </button>
        <button class="button cancel-button button-primary" onclick="return tb_remove();"
            name="save" value="<?php echo __('Close', 'woo-dpd');?>">
            <?php echo __('Close', 'woo-dpd');?>
        </button>
    </div>
</form>