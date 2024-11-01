<?php

namespace DPD;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

class Kernel {

    public function __construct()
    {
        // session_start();
        $this->includes();
        $this->init_hooks();
        $this->init_filters();
        $this->checkNotice();
    }

    /**
     * Инклюды
     * @return void
     */
    public function includes()
    {
        //SDK
        require_once DPD_PLUGIN_PATH.'lib/dpd-sdk/src/autoload.php';

        //Helpers
        require_once 'Helpers/View.php';
        require_once 'Helpers/SimpleValidation.php';
        require_once 'Helpers/Converter.php';
        require_once 'Helpers/PriceRules.php';
        require_once 'Helpers/Sender.php';

        //other classes
        require_once 'DataImport.php';
        require_once 'Actions.php';

        //Factories
        require_once 'Factories/Shipment.php';

        //Models
        require_once 'Models/Model.php';
        require_once 'Models/Terminal.php';
        require_once 'Models/Location.php';
    }

    /**
     * Привязка к хукам
     * @return void
     */
    private function init_hooks()
    {
        add_action('plugins_loaded', [
            $this,
            'loadPluginTextdomain'
        ]);


        //подключение класса доставки
        add_action('woocommerce_shipping_init', [
            $this,
            'includeShippingMethodClass'
        ]);

        //подключение скриптов и стилей в админку
        add_action('admin_enqueue_scripts', [
            $this,
            'addBackendAssets'
        ]);

        //подключение скриптов и стилей в пользовательскую часть
        add_action('wp_enqueue_scripts', [
            $this,
            'addFrontedAssets'
        ]);

        //инициализация экшенов плагина
        add_action('init', [
            $this,
            'initActions'
        ]);

        //добавляем поля
        add_action('woocommerce_after_order_notes', [
            $this,
            'addExtraFields'
        ]);

        //добавляем валидацию полей
        add_action('woocommerce_after_checkout_validation', [
            $this,
            'addExtraFieldsValidation'
        ], 20, 2);

        //добавляем сохранение полей
        add_action('woocommerce_checkout_update_order_meta', [
            $this,
            'saveExtraFields'
        ], 10, 2);

        //добавляем JS константы
        add_action('wp_head', [
            $this,
            'addJsConstants'
        ]);

        //добавляем блок управление заказом
        add_action('add_meta_boxes', [
            $this,
            'addMetaBox'
        ]);
    }

    /**
     * Загрузка перевода плагина
     * @return
     */
    public function loadPluginTextdomain()
    {
        load_textdomain(
            'dpd',
            DPD_PLUGIN_PATH.'lang/dpd-'.determine_locale().'.mo'
        );
    }

    /**
     * Расширение данных с помощью фильтров
     * @return void
     */
    private function init_filters()
    {
        //включение в список доставок
        add_filter('woocommerce_shipping_methods', [
            $this,
            'addShippingMethod'
        ]);
    }

    /**
     * Подключение обработчика способа доставки
     * @return void
     */
    public function includeShippingMethodClass()
    {
        if (!class_exists('\DPD\ShippingMethodHandler')) {
            require_once 'ShippingMethodHandler.php';
        }
    }

    /**
     * Добавление способа доставки в список способов в магазине
     * @param array $methods
     */
    public function addShippingMethod($methods)
    {
        $methods[ DPD_SHIPPING_METHOD_ID ] = '\DPD\ShippingMethodHandler';
        
        return $methods;
    }

    /**
     * Добавление ассетов на страницу настроек в админке
     * @param string $hook
     * @return void
     */
    public function addBackendAssets($hook) {
        wp_enqueue_style(
            'dpd-autocomplete-css', 
            DPD_PLUGIN_URI.'assets/js/jquery-autocomplete/jquery.auto-complete.css'
        );
        wp_enqueue_script(
            'dpd-autocomplete',
            DPD_PLUGIN_URI.'assets/js/jquery-autocomplete/jquery.auto-complete.min.js',
            ['jquery']
        );
        wp_enqueue_style(
            'dpd-admin-css', 
            DPD_PLUGIN_URI.'assets/css/dpd-admin.css'
        );
        wp_enqueue_script(
            'admin',
            DPD_PLUGIN_URI.'assets/js/admin.js?'. time(),
            ['jquery']
        );
        add_thickbox();
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui');
        return;
    }

    /**
     * Добавление ассетов в пользовательскую часть сайта
     * @param string $hook
     * @return void
     */
    public function addFrontedAssets($hook) {
        wp_enqueue_style(
            'dpd-autocomplete-css', 
            DPD_PLUGIN_URI.'assets/js/jquery-autocomplete/jquery.auto-complete.css'
        );
        wp_enqueue_script(
            'dpd-autocomplete',
            DPD_PLUGIN_URI.'assets/js/jquery-autocomplete/jquery.auto-complete.min.js',
            ['jquery']
        );
        wp_enqueue_style(
            'dpd-pickpoint-widget-css',
            DPD_PLUGIN_URI.'assets/js/dpd-pickpoint-widget/css/dpd-pickpoint-widget.css?v5'
        );
        wp_enqueue_style(
            'dpd-pickpoint-widgets-css',
            DPD_PLUGIN_URI.'assets/js/dpd.widgets.map/src/css/style.css'
        );
        // wp_enqueue_style(
        //     'dpd-pickpoint-widgets-css-iframe',
        //     DPD_PLUGIN_URI.'assets/js/dpd-pickpoint-widget/css/iframe.css'
        // );
        wp_enqueue_script(
            'dpd-pickpoint-widget-js',
            DPD_PLUGIN_URI.'assets/js/dpd-pickpoint-widget/js/dpd-pickpoint-widget.js?v5',
            ['jquery']
        );
        wp_enqueue_script(
            'front',
            DPD_PLUGIN_URI.'assets/js/front.js',
            ['jquery']
        );
        wp_enqueue_script(
            'front',
            DPD_PLUGIN_URI .'assets/js/dpd.widgets.map/src/js/jquery.dpd.map.js',
            ['jquery']
        );
    }

    /**
     * Добавляет поле пвз код в чекаут
     * @param string $checkout
     * @return void
     */
    public function addExtraFields($checkout)
    {
        echo '<input type="hidden" name="dpd_data" value="">';
        echo '<input type="hidden" name="dpd_delivery_type" value="">';
        echo '<input type="hidden" name="dpd_terminal_code" value="">';



        return $checkout;
    }

    /**
     * Валидация поля
     * @param $data
     * @param $errors
     * @return void
     */
    public function addExtraFieldsValidation($data, $errors)
    {
        if (empty($_POST['dpd_terminal_code']) 
            && sanitize_text_field($_POST['dpd_delivery_type']) == 'pickup' 
            && get_option('dpd_required_pickpoint_selection')
        ) {
            $errors->add('dpd_terminal_code',  __( 'Please, select pickpoint.', 'dpd'), 'error');
        }
    }

    /**
     * Сохранение доп полей чекаута
     * @param integer $orderId
     * @param array $posted
     * @return void
     */
    public function saveExtraFields($orderId, $posted)
    {
        if(isset($_POST['dpd_terminal_code'])) {
            update_post_meta(
                $orderId,
                'dpd_terminal_code',
                sanitize_text_field($_POST['dpd_terminal_code'])
            );
        }
        if(isset($_POST['dpd_delivery_type'])) {
            update_post_meta(
                $orderId,
                'dpd_delivery_type',
                sanitize_text_field($_POST['dpd_delivery_type'])
            );
        }
        if(isset($_POST['dpd_data']) && $_POST['dpd_data']) {
            update_post_meta(
                $orderId,
                'dpd_data',
                sanitize_text_field($_POST['dpd_data'])
            );
            $note = __('DPD delevery shipping method', 'dpd').': '.
                ($_POST['dpd_delivery_type'] == 'pickup' ? __('pickup', 'dpd') : 
                    __('courier', 'dpd'))."\n";
            if ($_POST['dpd_delivery_type'] == 'pickup') {
                $note .= __('DPD pickup code', 'dpd').': '.
                    sanitize_text_field($_POST['dpd_terminal_code']);
            }
            $order = wc_get_order($orderId);
            $order->add_order_note($note);
            $order->save();
        }
    }
    

    /**
     * Инициализация экшенов плагина
     * @return DPDActions
     */
    public function initActions()
    {
        global $DPDconfig;

        return new \DPD\Actions(new \Ipol\DPD\Config\Config($DPDconfig));
    }

    /**
     * Добавляет JS константы
     * @return void
     */
    public function addJsConstants()
    {
        echo '<script>'.
            'const DPD_SELECT_PICKPOINT_TEXT = "'.__('Select pickpoint', 'dpd').'";'.
            'const DPD_PLUGIN_URI = "'.DPD_PLUGIN_URI.'";'.
            'const DPD_HOME_URI = "'. get_home_url() .'";'.
        '</script>';
    }

    /**
     * Добавляем блок управления заказом
     * @return void
     */
    public function addMetaBox()
    {
        $screen = class_exists( CustomOrdersTableController::class ) && wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
            ? wc_get_page_screen_id( 'shop-order' )
            : 'shop_order'
        ;

        add_meta_box(
            'dpd-metabox', 
            __('DPD','dpd'),
            [
                $this,
                'addFieldsToMetabox'
            ],
            $screen,
            'side',
            'core'
        );
    }

    /**
     * Добавляем поля
     * @return void
     */
    public function addFieldsToMetabox()
    {
        global $post;

        $postId = get_the_ID() ?: $_REQUEST['id'];
        

        $dpdData = get_post_meta($postId, 'dpd_data', true);
        if ($dpdData) {
            $color = 'blue';
            if (get_post_meta($postId, 'dpd_order_send_error_flag', true) && 
                !get_post_meta($postId, 'dpd_sended', true)) {
                $color = 'red';
            } else if(!get_post_meta($postId, 'dpd_order_send_error_flag', true) &&
                get_post_meta($postId, 'dpd_sended', true)) {
                $color = 'green';
            }
            echo '<a href="?height=600&width=753&action=orderBlock&order_id='.
                $postId .'" class="thickbox dpd button '.$color.'" name="'.__('Edit data for DPD','dpd').'">'.__('Order Controll','dpd').'</a>';
        } else {
            echo __('Another delivery method was selected','dpd');
        }
    }

    /**
     * Проверяем есть ли уведомления от страницы настроек
     * @return
     */
    public function checkNotice()
    {
        // if (isset($_SESSION['dpd_settings_notice'])) {
        //     add_action('admin_notices', [
        //         $this,
        //         'showNotice'
        //     ]);
        // }
    }

    /**
     * Показываем уведомление от страницы настроек
     * @return
     */
    public function showNotice()
    {
        // if (isset($_GET['page'], $_GET['tab'], $_GET['section']) &&
        //     $_GET['page'] == 'wc-settings' && $_GET['tab'] == 'shipping' && 
        //     $_GET['section'] == 'dpd') {
        //     // echo $_SESSION['dpd_settings_notice'];
        //     // unset($_SESSION['dpd_settings_notice']);
        // }
    }

}