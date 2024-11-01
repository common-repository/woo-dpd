<?php

namespace DPD;

use WC_Order;

use DPD\Model\Terminal as Terminal;
use DPD\Model\Location as Location;

use DPD\Helper\SimpleValidation as SimpleValidation;
use DPD\Helper\PriceRules as PriceRules;
use DPD\Helper\Converter as Converter;
use DPD\Helper\View as View;

use DPD\Factories\Shipment as Shipment;

class Actions {

    protected $config;

    public function __construct(\Ipol\DPD\Config\Config $config)
    {
        $this->config = $config;

        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';

        if (method_exists($this, $action)) {
            if (!$config->isActiveAccount()) {
                throw new \Exception('DPD config empty', 1);
            }

            $restrictedArea = [
                'orderBlock',
                'sendOrder',
                'cancelOrder',
                'printDocs'
            ];

            if (in_array($action, $restrictedArea) && !( current_user_can( 'edit_posts' ) || current_user_can( 'manage_woocommerce' ) )) {
                exit;
            }

            $this->{$action}();
        }
    }


    /**
     * Обновление данных плагина через крон
     * @return
     */
    private function dataUpdate()
    {
        $this->config->set('LOAD_EXTERNAL_DATA_STEP',     get_option('dpd_LOAD_EXTERNAL_DATA_STEP'));
        $this->config->set('LOAD_EXTERNAL_DATA_POSITION', get_option('dpd_LOAD_EXTERNAL_DATA_POSITION'));

        \Ipol\DPD\Agents::loadExternalData($this->config);

        update_option('dpd_LOAD_EXTERNAL_DATA_STEP',     $this->config->get('LOAD_EXTERNAL_DATA_STEP'));
        update_option('dpd_LOAD_EXTERNAL_DATA_POSITION', $this->config->get('LOAD_EXTERNAL_DATA_POSITION'));

        exit;
    }

    /**
     * Обновление данных плагина через крон
     * @return
     */
    private function statusUpdate()
    {
        $orders = \Ipol\DPD\Agents::checkOrderStatus($this->config);

        foreach ($orders as $order) {
            update_post_meta($order->orderId, 'dpd_status', $order->orderStatus);
            update_post_meta($order->orderId, 'dpd_order_num', $order->orderNum);
            update_post_meta($order->orderId, 'dpd_last_status_update', time());

            if (get_option('dpd_status_order_check')) {
              $status = get_option('dpd_sync_order_status_'.$order->orderStatus);

              if ($status) {
                $this->dpd_changeWcStatus($order->orderId, $status);
              }
            }
        }
        exit;
    }

    /**
     * Обновление статуса
     * @param  integer $orderId
     * @param  string $orderStatus
     * @return
     */
    private function dpd_changeWcStatus($orderId, $orderStatus)
    {
        wp_update_post(['ID' => $orderId, 'post_status' => $orderStatus]);
    }


    /**
     * Запуск импорта вручную
     * @return
     */
    private function runImportManually()
    {
        update_option('dpd_run_import_manually', 1, true);

        return wp_safe_redirect(
            admin_url('admin.php?page=wc-settings&tab=shipping&section=dpd')
        );
    }

    /**
     * Импорт данных в базу
     * @return json
     */
    private function import()
    {
        $error  = '';
        $offset = isset($_GET['offset']) ? sanitize_text_field($_GET['offset']) : 0;
        $step   = isset($_GET['step']) ? sanitize_text_field($_GET['step']) : 0;
        
        try {
            $dpdDataImport = new \DPD\DataImport($step, $offset);
            $dpdDataImport->run();
        } catch(\SoapFault $e) {
            $error = $e->getMessage(); 
        } catch(\Exception $e) {
            $error = $e->getMessage(); 
        }

        $pecent = 0;
        
        if ($dpdDataImport->getTotal() && $dpdDataImport->getOffset() != -1) {
            $offset = preg_replace('/(\D)/', '', $dpdDataImport->getOffset());
            $pecent = round(
                (($offset * 100) / $dpdDataImport->getTotal()), 
                3
            );
        }
        
        header('Content-Type: application/json');

        return die (json_encode([
            'step'     => $dpdDataImport->getStep(),
            'stepname' => $dpdDataImport->getStepName(),
            'offset'   => $dpdDataImport->getOffset(),
            'percent'  => $pecent,
            'total'    => $dpdDataImport->getTotal(),
            'error'    => $error ? $error : ''
        ]));
    }


    /**
     * Конец импорта данных
     * @return
     */
    private function importDone()
    {
        update_option('dpd_first_data_import_completed', 1, true);
        update_option('dpd_run_import_manually', 0, true);

        return wp_safe_redirect(
            admin_url('admin.php?page=wc-settings&tab=shipping&section=dpd')
        );
    }

    /**
     * Автозаполнение городов
     * @return json
     */
    private function getCity()
    {
        $q = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
        $q = $q ? $q : '';
        $q = substr($q, 0, 64);
        $countryCode = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
        $location = new Location($this->config);
        $where = 'CITY_NAME LIKE :city';
        $bind = [':city' => $q.'%'];
        if ($countryCode) {
            $where .= ' AND COUNTRY_CODE = :contry_code';
            $bind[':contry_code'] =  $countryCode;
        }
        $location->where(
            $where,
            $bind
        );
        $location->order('IS_CITY DESC');
        $location->limit('0,10');
        $locations = $location->get();
        $result = Location::formAutoCompleteArray($locations, $countryCode);
        header('Content-Type: application/json');
        return die(json_encode($result));
    }

    /**
     * Строит опшены для селектора терминалов по ID города
     * @return json
     */
    private function getTerminalsByCityId()
    {
        $cityId = isset($_GET['city_id']) ? sanitize_text_field($_GET['city_id']) : '';
        $terminal = new Terminal($this->config);
        $terminal->where(
            'LOCATION_ID = :location_id',
            [':location_id' => $cityId]
        );
        $terminals = $terminal->get();
        $result['html'] = Terminal::renderTerminalsOptions($terminals);
        header('Content-Type: application/json');
        return die(json_encode($result));
    }

    /**
     * Вовзращает карту ПВЗ
     * @return
     */
    private function getMap()
    {
        global $woocommerce;

        $country = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
        $region = isset($_GET['region']) ? sanitize_text_field($_GET['region']) : '';
        $city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';
        $paymentMethod = isset($_GET['payment_method']) ?
            sanitize_text_field($_GET['payment_method']) : '';

        $converter = new Converter();

        //определяем доставку
        $shipmentFactory = new Shipment(
            $this->config,
            \DPD\Helper\Sender::getDefault()['city'],
            $country,
            $region,
            $city,
            $woocommerce->cart->subtotal
        );
        $shipmentFactory->setItemsByCart($woocommerce->cart->get_cart());
        $shipment = $shipmentFactory->getInstance();
        $shipment->setSelfPickup(get_option('dpd_self_pickup'));
        $shipment->setCurrencyConverter($converter);

        if ($paymentMethod) {
            $shipment->setPaymentMethod(1, sanitize_text_field($_REQUEST['payment_method']));
        }

        $tariffs = [
            'courier' => PriceRules::round($shipment->setSelfDelivery(false)
                ->calculator()->setCurrencyConverter($converter)
                ->calculate(get_option('woocommerce_currency'))),
            'pickup' => PriceRules::round($shipment->setSelfDelivery(true)
                ->calculator()->setCurrencyConverter($converter)
                ->calculate(get_option('woocommerce_currency'))),
        ];

        //получаем терминалы
        $terminal = new Terminal($this->config);
        $where = 'LOCATION_ID = :location AND SCHEDULE_SELF_DELIVERY != ""';
        $bind = [
            ':location' => $shipment->getReceiver()['CITY_ID']
        ];
        if (get_option('dpd_ogd')) {
            $where .= ' AND SERVICES LIKE :ogd';
            $bind['ogd'] = '%|ОЖД_'.get_option('dpd_ogd').'|%';
        }
        if (get_option('dpd_chst')) {
            $where .= ' AND SERVICES LIKE :chst';
            $bind['chst'] = '%|ЧСТ_'. get_option('dpd_chst') .'|%';
        }
        $nppPayment = get_option('dpd_commission_npp_payment');
        if ($nppPayment) {
            $nppPayment = unserialize($nppPayment);
        } else {
            $nppPayment = [];
        }
        if ($nppPayment && $paymentMethod && in_array($paymentMethod, $nppPayment)) {
            $where .= ' AND NPP_AVAILABLE =:npp';
            $bind['npp'] = 'Y';
        }

        $terminal->where($where, $bind);
        $terminals = Terminal::onlyAvaliable($terminal->get(), $shipment);

        die(View::load('frontend/pickup_map', [
            'tariffs' => $tariffs,
            'terminals' => array_values($terminals)
        ]));
    }

    /**
     * Блок заказа в админке
     * @return void
     */
    private function orderBlock()
    {        
        //грузим заказ
        $wcOrderId = isset($_GET['order_id']) ? sanitize_text_field($_GET['order_id']) : 0;
        $order = new WC_Order($wcOrderId);

        if (!$order) {
            return;
        }

        //данные для вьюса
        $status = get_post_meta($order->ID, 'dpd_status', true);
        if (!$status) {
            $status = 'NEW';
        }

        //проверяем был ли отправлен заказ
        $sended = get_post_meta($order->ID, 'dpd_sended', true);

        if ($sended) {
            $orderId            = get_post_meta($order->ID, 'dpd_order_id', true);
            $orderNum           = get_post_meta($order->ID, 'dpd_order_num', true);
            $deliveryType       = get_post_meta($order->ID, 'dpd_delivery_type', true);
            $deliveryVariant    = get_post_meta($order->ID, 'dpd_delivery_variant', true);
            $pickupTimePeriod   = get_post_meta($order->ID, 'dpd_pickup_time_period', true);
            $pickUpDate         = get_post_meta($order->ID, 'dpd_pickup_date', true);
            $deliveryTimePeriod = get_post_meta($order->ID, 'dpd_delivery_time_period', true);
            $shippingWeight     = get_post_meta($order->ID, 'dpd_shipping_weight', true);
            $shippingWidth      = get_post_meta($order->ID, 'dpd_dimensions_width', true);
            $shippingHeight     = get_post_meta($order->ID, 'dpd_dimensions_height', true);
            $shippingLength     = get_post_meta($order->ID, 'dpd_dimensions_length', true);
            $cargoVolume        = get_post_meta($order->ID, 'dpd_cargo_volume', true);
            $cargoNumPack       = get_post_meta($order->ID, 'dpd_cargo_num_pack', true);
            $paymentType        = get_post_meta($order->ID, 'dpd_payment_type', true);
            $contentSubmission  = get_post_meta($order->ID, 'dpd_content_submission', true);

            //отправитель
            $sender = [
                'fio'           => get_post_meta($order->ID, 'dpd_sender_fio', true),
                'name'          => get_post_meta($order->ID, 'dpd_sender_name', true),
                'phone'         => get_post_meta($order->ID, 'dpd_sender_phone', true),
                'email'         => get_post_meta($order->ID, 'dpd_sender_email', true),
                'need_pass'     => get_post_meta($order->ID, 'dpd_sender_need_pass', true),
                'street'        => get_post_meta($order->ID, 'dpd_sender_street', true),
                'streetabbr'    => get_post_meta($order->ID, 'dpd_sender_streetabbr', true),
                'house'         => get_post_meta($order->ID, 'dpd_sender_house', true),
                'korpus'        => get_post_meta($order->ID, 'dpd_sender_korpus', true),
                'str'           => get_post_meta($order->ID, 'dpd_sender_str', true),
                'vlad'          => get_post_meta($order->ID, 'dpd_sender_vlad', true),
                'office'        => get_post_meta($order->ID, 'dpd_sender_office', true),
                'flat'          => get_post_meta($order->ID, 'dpd_sender_flat', true),
                'terminal_code' => get_post_meta($order->ID, 'dpd_sender_terminal_code', true),
                'city_id'       => get_post_meta($order->ID, 'dpd_sender_city_id', true)
            ];

            //получатель
            $recipient = [
                'fio'           => get_post_meta($order->ID, 'dpd_recipient_fio', true),
                'name'          => get_post_meta($order->ID, 'dpd_recipient_name', true),
                'phone'         => get_post_meta($order->ID, 'dpd_recipient_phone', true),
                'email'         => get_post_meta($order->ID, 'dpd_recipient_email', true),
                'need_pass'     => get_post_meta($order->ID, 'dpd_recipient_need_pass', true),
                'street'        => get_post_meta($order->ID, 'dpd_recipient_street', true),
                'streetabbr'    => get_post_meta($order->ID, 'dpd_recipient_streetabbr', true),
                'house'         => get_post_meta($order->ID, 'dpd_recipient_house', true),
                'korpus'        => get_post_meta($order->ID, 'dpd_recipient_korpus', true),
                'str'           => get_post_meta($order->ID, 'dpd_recipient_str', true),
                'vlad'          => get_post_meta($order->ID, 'dpd_recipient_vlad', true),
                'office'        => get_post_meta($order->ID, 'dpd_recipient_office', true),
                'flat'          => get_post_meta($order->ID, 'dpd_recipient_flat', true),
                'terminal_code' => get_post_meta($order->ID, 'dpd_terminal_code', true),
                'city_id'       => get_post_meta($order->ID, 'dpd_recipient_city_id', true),
                'location'      => get_post_meta($order->ID, 'dpd_recipient_city_id', true),
                'comment'       => get_post_meta($order->ID, 'dpd_recipient_comment', true),
            ];

            //оплата
            $useCargoValue = get_post_meta($order->ID, 'dpd_use_cargo_value', true);
            $cargoValue    = get_post_meta($order->ID, 'dpd_cargo_value', true);
            $npp           = get_post_meta($order->ID, 'dpd_npp', true);
            $nppSum        = get_post_meta($order->ID, 'dpd_npp_sum', true);
            $unitLoads     = @unserialize(get_post_meta($order->ID, 'dpd_unit_loads', true));
            $useMarking    = get_post_meta($order->ID, 'dpd_use_marking', true);

            //опции
            $cargoRegistered     = get_post_meta($order->ID, 'cargo_registered', true);
            $dvd                 = get_post_meta($order->ID, 'dpd_dvd', true);
            $trm                 = get_post_meta($order->ID, 'dpd_trm', true);
            $prd                 = get_post_meta($order->ID, 'dpd_prd', true);
            $vdo                 = get_post_meta($order->ID, 'dpd_vdo', true);
            $obr                 = get_post_meta($order->ID, 'dpd_obr', true);
            $ogd                 = get_post_meta($order->ID, 'dpd_ogd', true);
            $esz                 = get_post_meta($order->ID, 'dpd_esz', true);
            $chst                = get_post_meta($order->ID, 'dpd_chst', true);
            $goods_return_amount = get_post_meta($order->ID, 'dpd_goods_return_amount', true);
            $delivery_amount     = get_post_meta($order->ID, 'dpd_delivery_amount', true);
            $sumNpp              = get_post_meta($order->ID, 'dpd_sum_npp', true);
        } else {

            $paymentType        = get_option('dpd_payment_type');
            $shippingWeight     = null;
            $shippingWidth      = null;
            $shippingHeight     = null;
            $shippingLength     = null;
            $cargoVolume        = null;
            $useCargoValue      = get_option('dpd_declared_value');
            $cargoValue         = 0;
            $npp                = 0;
            $sumNpp             = 0;
            $orderId            = '';
            $orderNum           = '';
            $pickupTimePeriod   = '';
            $deliveryTimePeriod = '';
            // $pickUpDate      = date('d.m.Y');
            $pickUpDate         = '';
            $dpdData            = explode('_', get_post_meta($order->ID, 'dpd_data', true));
            $deliveryTo         = isset($dpdData[2]) && $dpdData[2] == 'pickup' ? 'Т' : 'Д';
            $deliveryVariant    = get_option('dpd_self_pickup') ? 'Т'.$deliveryTo : 'Д'.$deliveryTo;
            $deliveryType       = $dpdData[1];
            $cargoNumPack       = get_option('dpd_cargo_num_pack');
            $contentSubmission  = get_option('dpd_cargo_category');
            $unitLoads          = [];
            $useMarking         = false;

            $sender = \DPD\Helper\Sender::getDefault();
            
            //отправитель
            $sender = [
                'fio'           => get_option('dpd_sender_fio'),
                'name'          => get_option('dpd_sender_name'),
                'phone'         => get_option('dpd_sender_phone'),
                'email'         => get_option('dpd_sender_email'),
                'need_pass'     => get_option('dpd_sender_need_pass'),
                'street'        => $sender['street'],
                'streetabbr'    => $sender['streetabbr'],
                'house'         => $sender['house'],
                'korpus'        => $sender['korpus'],
                'str'           => $sender['str'],
                'vlad'          => $sender['vlad'],
                'office'        => $sender['office'],
                'flat'          => $sender['flat'],
                'terminal_code' => $sender['terminal_code'],
                'city'          => $sender['city'],
                'city_id'       => $sender['city_id'],
            ];

            //получатель
            $recipient = [
                'fio'           => $order->get_shipping_first_name()  .' '. $order->get_shipping_last_name(),
                'name'          => $order->get_shipping_first_name() .' '. $order->get_shipping_last_name(),
                'phone'         => $order->get_billing_phone(),
                'email'         => $order->get_billing_email(),
                'need_pass'     => get_option('dpd_recipient_need_pass'),
                'street'        => $order->shipping_address_1,
                'streetabbr'    => '',
                'house'         => '',
                'korpus'        => '',
                'str'           => '',
                'vlad'          => '',
                'office'        => '',
                'flat'          => '',
                'terminal_code' => get_post_meta($order->ID, 'dpd_terminal_code', true),
                'location'      => '',
                'city_id'       => '',
                'comment'       => $order->get_customer_note(),
            ];

            //опции
            $cargoRegistered     = get_option('dpd_cargo_registered');
            $dvd                 = get_option('dpd_dvd');
            $trm                 = get_option('dpd_trm');
            $prd                 = get_option('dpd_prd');
            $vdo                 = get_option('dpd_vdo');
            $obr                 = get_option('dpd_obr');
            $ogd                 = get_option('dpd_ogd');
            $esz                 = get_option('dpd_esz');
            $chst                = get_option('dpd_chst');
            $goods_return_amount = get_option('dpd_goods_return_amount');
            $delivery_amount     = get_option('dpd_delivery_amount');
        }

        $country = $order->get_shipping_country() ?: $order->get_billing_country();
        $state   = $order->get_shipping_state() ?: $order->get_billing_state();
        $city    = $order->get_shipping_city() ?: $order->get_billing_city();

        $recipient['location'] = ($country == 'RU' ? 'Россия' : ($country == 'KZ' ?  'Казахстан' : 'Беларусь'))
            . ', '
            . $state 
            . ', '
            . $city
        ;

        //метод оплаты
        $orderPaymentMethod = $order->get_payment_method();
        $commissionNppPayment = unserialize(get_option('dpd_commission_npp_payment'));
        /*if (is_array($commissionNppPayment) &&
            !in_array($orderPaymentMethod, $commissionNppPayment)) {
            $npp = 0;
        }*/
        if (is_array($commissionNppPayment) &&
            in_array($orderPaymentMethod, $commissionNppPayment)) {
            $npp = 1;
        }


        //изменение данных пользователем
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sender['need_pass'] = '';
            $recipient['need_pass'] = '';
            $paymentType = sanitize_text_field($_POST['order']['payment_type']);
            $deliveryType = sanitize_text_field($_POST['order']['delivery_type']);
            $deliveryVariant = sanitize_text_field($_POST['order']['delivery_variant']);
            if (!$deliveryVariant) {
                $deliveryVariant = 'ДД';
            }
            $pickUpDate = sanitize_text_field($_POST['order']['pickup_date']);
            $pickupTimePeriod = sanitize_text_field($_POST['order']['pickup_time_period']);
            $deliveryTimePeriod = sanitize_text_field($_POST['order']['delivery_time_period']);
            $shippingWeight = sanitize_text_field($_POST['order']['shipping_weight']);
            $shippingWidth = sanitize_text_field($_POST['order']['dimensions_width']);
            $shippingHeight = sanitize_text_field($_POST['order']['dimensions_height']);
            $shippingLength = sanitize_text_field($_POST['order']['dimensions_length']);
            $cargoVolume = sanitize_text_field($_POST['order']['cargo_volume']);

            $cargoNumPack = sanitize_text_field($_POST['order']['cargo_num_pack']);
            $contentSubmission = sanitize_text_field($_POST['order']['content_submission']);

            foreach ($_POST['order']['sender'] as $key => $item) {
                $sender[$key] = sanitize_text_field($item); 
            }

            if (isset($_POST['order']['sender_idx']) && $_POST['order']['sender_idx'] == 'OTHER' && !isset($_POST['order']['sender']['city'])) {
                $sender = array_merge($sender, \DPD\Helper\Sender::makeSender());
            } else {
                $sender = array_merge($sender, \DPD\Helper\Sender::getByIndex(sanitize_text_field($_POST['order']['sender_idx'])) ?: []);
            }

            foreach ($_POST['order']['recipient'] as $key => $item) {
                $recipient[$key] = sanitize_text_field($item); 
            }

            //оплата
            $useCargoValue = sanitize_text_field($_POST['order']['use_cargo_value']) ? 1 : 0;
            $cargoValue = 0;
            $npp = isset($_POST['order']['npp']) ? 1 : 0;
            $useMarking = isset($_POST['order']['use_marking']) ? 1 : 0;
            $sumNpp = 0;

            //опции
            $cargoRegistered = isset($_POST['order']['cargo_registered']) ? 1 : 0;
            $dvd = isset($_POST['order']['dvd']) ? 1 : 0;
            $trm = isset($_POST['order']['trm']) ? 1 : 0;
            $prd = isset($_POST['order']['prd']) ? 1 : 0;
            $vdo = isset($_POST['order']['vdo']) ? 1 : 0;
            $obr = isset($_POST['order']['obr']) ? 1 : 0;
            $ogd = sanitize_text_field($_POST['order']['ogd']);
            $esz = sanitize_text_field($_POST['order']['esz']);

            $chst                = sanitize_text_field($_POST['order']['chst']);
            $goods_return_amount = sanitize_text_field($_POST['order']['goods_return_amount']);
            $delivery_amount     = sanitize_text_field($_POST['order']['delivery_amount']);
        }

        $location = new Location($this->config);
        $location->where('ORIG_NAME LIKE :location', [':location' => '%'.$recipient['location'].'%']);
        $item = $location->row();

        if ($item) {
            $recipient['city_id'] = $item['CITY_ID'];
        }

        list($country, $region, $city) = array_map('trim', explode(',', $recipient['location']));

        //конвертер
        $converter = new Converter();

        //доставка
        $shipmentFactory = new Shipment(
            $this->config,
            $sender['city'],
            $country,
            $region,
            $city,
            $order->get_subtotal()
        );

        $shipmentFactory->setItemsByOrder($order->get_items());
        $shipment = $shipmentFactory->getInstance();
        $shipment->setCurrencyConverter($converter);

        $recipient['city_id'] = $shipment->getReceiver()['CITY_ID'];

        

        $shipment->setPaymentMethod(1, $orderPaymentMethod);

        if (is_null($shippingWeight)) {
            $shippingWeight = $shipment->getWeight();
        }
        if (is_null($shippingWidth)) {
            $shippingWidth = $shipment->getWidth();
        }
        if (is_null($shippingHeight)) {
            $shippingHeight = $shipment->getHeight();
        }
        if (is_null($shippingLength)) {
            $shippingLength = $shipment->getLength();
        }
        // if (is_null($cargoVolume)) {
        //     $cargoVolume = $shipment->getVolume();
        // }

        //габариты
        $shipment->setDimensions(
           $shippingWidth  ?: 0,
           $shippingHeight ?: 0,
           $shippingLength ?: 0,
           $shippingWeight ?: 0
        );

        $cargoVolume = $shipment->getVolume();

        switch ($deliveryVariant) {

            case 'ДД':
                $shipment->setSelfDelivery(false);
                $shipment->setSelfPickup(false);  
                break;

            case 'ДТ':
                $shipment->setSelfPickup(false);  
                $shipment->setSelfDelivery(true);
                break;

            case 'ТТ':
                $shipment->setSelfDelivery(true);
                $shipment->setSelfPickup(true);  
                break;

            case 'ТД':
                $shipment->setSelfDelivery(false);
                $shipment->setSelfPickup(true);  
            break;

        }
        $calc = $shipment->calculator();
        $calc->setCurrencyConverter($converter);
        $errors = [];
        $notifications = [];
        $tariff = [];

        try {
            $tariff = $calc->calculateWithTariff($deliveryType, get_option('woocommerce_currency'));

            if (!$tariff) {
                throw new \Exception('Не удалось расчитать стоимость доставки');
            }

            if (intval(get_option('dpd_round_to'))) {
                $price = round($tariff['COST'] / get_option('dpd_round_to'))
                    * get_option('dpd_round_to');
            } else {
                $tariff['COST'] = round($tariff['COST'], 2);
            }
            $statusList = \Ipol\DPD\DB\Order\Model::StatusList();
            $status = isset($statusList[$status]) ? $statusList[$status] : 'unknow';
            if (!$sended) {
                $items     = [];
                $unitLoads = [];

                foreach ($order->get_items() as $item) {
                    $product = $item->get_product();

                    $items[] = [
                        'name'           => $product->get_title(),
                        'qty'            => $item->get_quantity(),
                        'declared_value' => $useCargoValue ? $product->get_price() : 0,
                        'npp_amount'     => $npp           ? $product->get_price() : 0,
                        'tax'            => '',
                        'gtin'           => '',
                        'serial'         => '',
                        'article'        => $product->get_sku(),
                    ];
                }
                
                if (isset($_POST['order']['unit_loads'])  && is_array($_POST['order']['unit_loads'])) {
                    $r = [];
                    
                    foreach ($_POST['order']['unit_loads'] as $j => $ul) {
                        foreach ($items as $k => $item) {
                            if ($item['name'] != $ul['name']) {
                                continue;
                            }

                            $ul = array_merge($item, $ul);
                            break;
                        }

                        $r[] = $ul;
                    }

                    $items = $r;
                }

                foreach ($items as $k => $ul) {
                    if ($ul['name'] == __('Delivery', 'dpd')) {
                        $ulDelivery = $ul;

                        continue;
                    }

                    $qty   = $useMarking ? 1 : $ul['qty'];
                    $limit = $useMarking ? $ul['qty'] : 1;

                    for ($i = 1; $i <= $limit; $i++) {
                        $unitLoads[] = $item = array_merge(
                            $items[$k],

                            $useMarking    ? ['qty' => 1] : [],
                            $useCargoValue ? [] : ['declared_value' => 0],
                            $npp           ? [] : ['npp_amount' => 0]
                        );
                        
                        $cargoValue += $item['declared_value'] * $item['qty'];
                        $sumNpp     += $item['npp_amount'] * $item['qty'];
                    }
                }

                if ($paymentType != \Ipol\DPD\Order::PAYMENT_TYPE_OUP) {

                    $deliveryNppCost = /*$npp ? 
                            (*/$ulDelivery && $ulDelivery['npp_amount'] ? 
                                sanitize_text_field($ulDelivery['npp_amount']) :
                                    $tariff['COST']/*) : 0*/;

                    $unitLoads[] = [
                        'name'           => __('Delivery', 'dpd'),
                        'qty'            => $ulDelivery ? sanitize_text_field($ulDelivery['qty']) : 1,
                        'declared_value' => 0,
                        'npp_amount'     => $npp ? $deliveryNppCost : 0,
                        'tax'            => $ulDelivery ? sanitize_text_field($ulDelivery['tax']) : '',
                        'gtin'           => $ulDelivery ? sanitize_text_field($ulDelivery['gtin']) : '',
                        'serial'         => $ulDelivery ? sanitize_text_field($ulDelivery['serial']) : '',
                    ];

                    $sumNpp += $npp ? $deliveryNppCost : 0;
                }
            }
        } catch(\SoapFault $e) {
            $errors[] = $e->getMessage();
        } catch(\Exception $e) {
            $errors[] = $e->getMessage();
        }

        $senderTerminals = [];
        $recipientTerminals = [];
        
        //проверяем терминалы на доступность для получателя и для отправителя
        $terminal = new Terminal($this->config);

        if ($deliveryVariant == 'ТТ' || $deliveryVariant == 'ТД') {    
            //отправитель
            $terminal->where(
                'LOCATION_ID = :location_id AND SCHEDULE_SELF_PICKUP != ""',
                [':location_id' => $sender['city_id']]);
            $senderTerminals = $terminal->get();

            $senderTerminals = Terminal::onlyAvaliableByDimessions($senderTerminals, $shipment);
            if (!count($senderTerminals)) {
                $errors[] = __('Sender terminals for settings order not found', 'dpd');
            } else {
                if (!Terminal::checkSelectedTerminalOnExists(
                    $senderTerminals,
                    $sender['terminal_code'])
                ) {
                    $notifications[] = __('Sender terminal was changed', 'dpd');
                }
            }
        }

        if ($deliveryVariant == 'ДТ' || $deliveryVariant == 'ТТ') {
            //получатель
            $where = 'LOCATION_ID = :location_id AND SCHEDULE_SELF_DELIVERY != ""';
            $bind = [':location_id' => $recipient['city_id']];
            if ($ogd) {
                $where .= ' AND SERVICES LIKE :ogd';
                $bind['ogd'] = '%'.$ogd.'%';
            }

            $terminal->where($where, $bind);
            $recipientTerminals = $terminal->get();
            $recipientTerminals = Terminal::onlyAvaliable($recipientTerminals, $shipment);

            if ($sumNpp) {
                $recipientTerminals = Terminal::onlyAvaliableNpp(
                    $recipientTerminals,
                    $shipment,
                    $sumNpp
                );
            }
            if (!count($recipientTerminals)) {
                $errors[] = __('Recipient terminals for settings order not found', 'dpd');
            } else {
                if (!Terminal::checkSelectedTerminalOnExists(
                    $recipientTerminals,
                    $recipient['terminal_code'])
                ) {
                    $notifications[] = __('Recipient terminal was changed', 'dpd');
                }
            }
        }

        //проверяем был ли успешно создан заказ на стороне DPD
        $orderTabel = \Ipol\DPD\DB\Connection::getInstance($this->config)->getTable('order');
        $dpdOrder = $orderTabel->getByOrderId($order->ID);
        $dpdCreated = false;

        if ($dpdOrder) {
            $dpdCreated = $dpdOrder->isDpdCreated();
            $sended     = $dpdOrder->isCreated();
        }

        die(View::load('backend/order/order', [
            'order' => $order,
            'errors' => $errors,
            'notifications' => $notifications,
            'shipment' => $shipment,
            'status' => $status,
            'orderId' => $orderId,
            'orderNum' => $orderNum,
            'paymentType' => $paymentType,
            'deliveryType' => $deliveryType,
            'deliveryVariant' => $deliveryVariant,
            'pickUpDate' => $pickUpDate,
            'pickupTimePeriod' => $pickupTimePeriod,
            'deliveryTimePeriod' => $deliveryTimePeriod,
            'shippingWeight' => $shippingWeight,
            'shippingWidth' => $shippingWidth,
            'shippingHeight' => $shippingHeight,
            'shippingLength' => $shippingLength,
            'cargoVolume' => $cargoVolume,
            'cargoNumPack' => $cargoNumPack,
            'contentSubmission' => $contentSubmission,
            'sender' => $sender,
            'recipient' => $recipient,
            'unitLoads' => $unitLoads,
            'cargoRegistered' => $cargoRegistered,
            'dvd' => $dvd,
            'trm' => $trm,
            'prd' => $prd,
            'vdo' => $vdo,
            'obr' => $obr,
            'ogd' => $ogd,
            'esz' => $esz,
            'chst' => $chst,
            'goods_return_amount' => $goods_return_amount,
            'delivery_amount' => $delivery_amount,
            'tariff' => $tariff,
            'sended' => $sended,
            'useCargoValue' => $useCargoValue,
            'cargoValue' => $cargoValue,
            'npp' => $npp,
            'useMarking' => $useMarking,
            'sumNpp' => $sumNpp,
            'senderTerminals' => $senderTerminals,
            'recipientTerminals' => $recipientTerminals,
            'dpdCreated' => $dpdCreated,
            'dpdOrder' => $dpdOrder
        ]));
    }

    /**
     * Отправка заказа в DPD
     * @return
     */
    private function sendOrder()
    {
        //грузим заказ
        $wcorder = new WC_Order(sanitize_text_field($_POST['order']['id']));

        if (!$wcorder) {
            return;
        }

        $result = [];

        //чистим данные от мусора по просьбе маркета вордпресс
        
        //order
        
        $orderPickUpDate = isset($_POST['order']['pickup_date']) ?
            sanitize_text_field($_POST['order']['pickup_date']) : null;

        $orderShippingWeight = isset($_POST['order']['shipping_weight']) ?
            sanitize_text_field($_POST['order']['shipping_weight']) : null;

        $orderDimensionsWidth = isset($_POST['order']['dimensions_width']) ?
            sanitize_text_field($_POST['order']['dimensions_width']) : null;

        $orderDimensionsHeight = isset($_POST['order']['dimensions_height']) ?
            sanitize_text_field($_POST['order']['dimensions_height']) : null;

        $orderDimensionsLength = isset($_POST['order']['dimensions_length']) ?
            sanitize_text_field($_POST['order']['dimensions_length']) : null;

        $orderCargoNumPack = isset($_POST['order']['cargo_num_pack']) ?
            sanitize_text_field($_POST['order']['cargo_num_pack']) : null;

        $orderContentSubmission = isset($_POST['order']['content_submission']) ?
            sanitize_text_field($_POST['order']['content_submission']) : null;

        $orderDeliveryVariant = isset($_POST['order']['delivery_variant']) ?
            sanitize_text_field($_POST['order']['delivery_variant']) : null;

        //sender

        if (isset($_POST['order']['sender_idx']) && $_POST['order']['sender_idx'] == 'OTHER' && !isset($_POST['order']['sender']['city'])) {
            $sender = array_merge((array) $sender, \DPD\Helper\Sender::makeSender());
        } else {
            $sender = array_merge((array) $sender, \DPD\Helper\Sender::getByIndex(sanitize_text_field($_POST['order']['sender_idx'])) ?: []);
        }
        
        $orderSenderFio          = isset($_POST['order']['sender']['fio']) ? sanitize_text_field($_POST['order']['sender']['fio']) : null;
        $orderSenderName         = isset($_POST['order']['sender']['name']) ? sanitize_text_field($_POST['order']['sender']['name']) : null;
        $orderSenderPhone        = isset($_POST['order']['sender']['phone']) ? sanitize_text_field($_POST['order']['sender']['phone']) : null;
        $orderSenderEmail        = isset($_POST['order']['sender']['email']) ? sanitize_email($_POST['order']['sender']['email']) : null;
        $orderSenderStreet       = isset($_POST['order']['sender']['street']) ? sanitize_text_field($_POST['order']['sender']['street']) : null;
        $orderSenderStreet       = $orderSenderStreet ?: $sender['street'];
        $orderSenderTerminalCode = isset($_POST['order']['sender']['terminal_code']) ? sanitize_text_field($_POST['order']['sender']['terminal_code']) : null;
        $orderSenderTerminalCode = $orderSenderTerminalCode ?: $sender['terminal_code'];

        //recipient
        $orderRecipientFio          = isset($_POST['order']['recipient']['fio']) ?  sanitize_text_field($_POST['order']['recipient']['fio'])                   : null;
        $orderRecipientName         = isset($_POST['order']['recipient']['name']) ? sanitize_text_field($_POST['order']['recipient']['name'])                  : null;
        $orderRecipientPhone        = isset($_POST['order']['recipient']['phone']) ? sanitize_text_field($_POST['order']['recipient']['phone'])                : null;
        $orderRecipientEmail        = isset($_POST['order']['recipient']['email']) ? sanitize_email($_POST['order']['recipient']['email'])                     : null;
        $orderRecipientStreet       = isset($_POST['order']['recipient']['street']) ? sanitize_text_field($_POST['order']['recipient']['street'])              : null;
        $orderRecipientTerminalCode = isset($_POST['order']['recipient']['terminal_code']) ? sanitize_text_field($_POST['order']['recipient']['terminal_code']): null;
        $orderRecipientComment      = isset($_POST['order']['recipient']['comment']) ? sanitize_text_field($_POST['order']['recipient']['comment']) : null;

        //валидация
        $dataForValidation = [
            [
                'type' => 'date|required',
                'value' => $orderPickUpDate,
                'error' => __('<strong>Date of shipment to DPD</strong> is required', 'dpd')
            ],
            [
                'type' => 'number|required',
                'value' => $orderShippingWeight,
                'error' => __('<strong>Shipping weight, kg</strong> is required and must be a number',
                    'dpd')
            ],
            [
                'type' => 'number|required',
                'value' => $orderDimensionsWidth,
                'error' => __('<strong>Width</strong> is required and must be a number', 'dpd')
            ],
            [
                'type' => 'number|required',
                'value' => $orderDimensionsHeight,
                'error' => __('<strong>Height</strong> is required and must be a number', 'dpd')
            ],
            [
                'type' => 'number|required',
                'value' => $orderDimensionsLength,
                'error' => __('<strong>Length</strong> is required and must be a number', 'dpd')
            ],
            [
                'type' => 'number|required',
                'value' => $orderCargoNumPack,
                'error' => __(
                    '<strong>Number of cargo spaces</strong> is required and must be a number'.
                        ' and  greater then 0',
                    'dpd'
                )
            ],
            [
                'type' => 'required',
                'value' => $orderContentSubmission,
                'error' => __(
                    '<strong>Content Submission</strong> is required',
                    'dpd'
                )
            ],
            [
                'type' => 'required',
                'value' =>$orderSenderFio,
                'error' => __(
                    'Sender <strong>Contact person</strong> is required',
                    'dpd'
                )
            ],
            [
                'type' => 'required',
                'value' => $orderSenderName,
                'error' => __(
                    'Sender <strong>Contact name</strong> is required',
                    'dpd'
                )
            ],
            [
                'type' => 'required',
                'value' => $orderSenderPhone,
                'error' => __(
                    'Sender <strong>Phone</strong> is required',
                    'dpd'
                )
            ],
            // [
            //     'type' => 'email',
            //     'value' => $orderSenderEmail,
            //     'error' => __(
            //         'Sender <strong>Email</strong> is required',
            //         'dpd'
            //     )
            // ],
            [
                'type' => 'required',
                'value' => $orderRecipientFio,
                'error' => __(
                    'Recipient <strong>Contact person</strong> is required',
                    'dpd'
                )
            ],
            [
                'type' => 'required',
                'value' => $orderRecipientName,
                'error' => __(
                    'Recipient <strong>Contact name</strong> is required',
                    'dpd'
                )
            ],
            [
                'type' => 'required',
                'value' => $orderRecipientPhone,
                'error' => __(
                    'Recipient <strong>Phone</strong> is required',
                    'dpd'
                )
            ],
            // [
            //     'type' => 'email',
            //     'value' => $orderRecipientEmail,
            //     'error' => __(
            //         'Recipient <strong>Email</strong> is required',
            //         'dpd'
            //     )
            // ]
        ];

        if ($orderDeliveryVariant == 'ДД' || $orderDeliveryVariant == 'ДТ') {
            $dataForValidation[] = [
                'type' => 'required',
                'value' => $orderSenderStreet,
                'error' => __(
                    'Sender <strong>Street</strong> is required',
                    'dpd'
                )
            ];
        }
        if ($orderDeliveryVariant == 'ДД' || $orderDeliveryVariant == 'ТД') {
            $dataForValidation[] = [
                'type' => 'required',
                'value' => $orderRecipientStreet,
                'error' => __(
                    'Recipient <strong>Street</strong> is required',
                    'dpd'
                )
            ];
        }
        if ($orderDeliveryVariant == 'ТТ' || $orderDeliveryVariant == 'ТД') {
            $dataForValidation[] = [
                'type' => 'required',
                'value' => $orderSenderTerminalCode,
                'error' => __(
                    'Sender <strong>Terminal</strong> is required',
                    'dpd'
                )
            ];
        }
        if ($orderDeliveryVariant == 'ТТ' || $orderDeliveryVariant == 'ДТ') {
            $dataForValidation[] = [
                'type' => 'required',
                'value' => $orderRecipientTerminalCode,
                'error' => __(
                    'Recipient <strong>Terminal</strong> is required',
                    'dpd'
                )
            ];
        }

        $errors = SimpleValidation::validate($dataForValidation);

        
        if (!$errors) {

            //конвертер
            $converter = new Converter();

            $recipient['location'] = ($country == 'RU' ? 'Россия' : ($country == 'KZ' ?  'Казахстан' : 'Беларусь'))
                . ', '
                . $state 
                . ', '
                . $city
            ;

            //доставка
            $shipmentFactory = new Shipment(
                $this->config,
                sanitize_text_field($_POST['order']['sender']['city']) ?: $sender['city'],
                $wcorder->get_shipping_country() ?: $wcorder->get_billing_country(),
                $wcorder->get_shipping_state() ?: $wcorder->get_billing_state(),
                $wcorder->get_shipping_city() ?: $wcorder->get_billing_city(),
                $wcorder->get_subtotal()
            );
            $shipmentFactory->setItemsByOrder($wcorder->get_items());
            $shipment = $shipmentFactory->getInstance();

            $shipment->setDimensions(
               $orderDimensionsWidth,
               $orderDimensionsHeight,
               $orderDimensionsLength,
               $orderShippingWeight
            );

            switch ($orderDeliveryVariant) {
                case 'ДД':
                    $shipment->setSelfDelivery(false);
                    $shipment->setSelfPickup(false);  
                break;
                case 'ДТ':
                    $shipment->setSelfPickup(false);  
                    $shipment->setSelfDelivery(true);
                break;
                case 'ТТ':
                    $shipment->setSelfDelivery(true);
                    $shipment->setSelfPickup(true);  
                break;
                case 'ТД':
                    $shipment->setSelfDelivery(false);
                    $shipment->setSelfPickup(true);  
                break;
            }

            $shipment->setPaymentMethod(1, $wcorder->get_payment_method());


            $order = \Ipol\DPD\DB\Connection::getInstance($this->config)->getTable('order')->getByOrderId($wcorder->ID, true);

            $order->orderId = $wcorder->ID;
            $order->currency = get_option('woocommerce_currency');

            $order->serviceCode = sanitize_text_field($_POST['order']['delivery_type']);
            $order->cargoNumPack = $orderCargoNumPack;
            $order->cargoCategory = $orderContentSubmission;

            $order->senderName = $orderSenderName;
            $order->senderFio = $orderSenderFio;
            $order->senderEmail = $orderSenderEmail;
            $order->senderPhone = $orderSenderPhone;
            $order->senderNeedPass = isset($_POST['order']['sender']['need_pass']) ? 'Y' : 'N';
            $order->pickupDate = date('Y-m-d', strtotime(sanitize_text_field($_POST['order']['pickup_date'])));
            $order->pickupTimePeriod = sanitize_text_field($_POST['order']['pickup_time_period']);

            if ($orderDeliveryVariant == 'ТД' || $orderDeliveryVariant == 'ТТ') {
                $order->senderTerminalCode = $orderSenderTerminalCode;
            } else {
                $order->senderStreet     = $orderSenderStreet;
                $order->senderStreetabbr = sanitize_text_field($_POST['order']['sender']['streetabbr']) ?: $sender['streetabbr'];
                $order->senderKorpus     = sanitize_text_field($_POST['order']['sender']['korpus'])     ?: $sender['korpus'];
                $order->senderHouse      = sanitize_text_field($_POST['order']['sender']['house'])      ?: $sender['house'];
                $order->senderStr        = sanitize_text_field($_POST['order']['sender']['str'])        ?: $sender['str'];
                $order->senderVlad       = sanitize_text_field($_POST['order']['sender']['vlad'])       ?: $sender['vlad'];
                $order->senderOffice     = sanitize_text_field($_POST['order']['sender']['office'])     ?: $sender['office'];
                $order->senderFlat       = sanitize_text_field($_POST['order']['sender']['flat'])       ?: $sender['flat'];
            }

            $order->receiverName       = $orderRecipientName;
            $order->receiverFio        = $orderRecipientFio;
            $order->receiverPhone      = $orderRecipientPhone;
            $order->receiverEmail      = $orderRecipientEmail;
            $order->receiverComment    = $orderRecipientComment;
            $order->receiverNeedPass   = isset($_POST['order']['recipient']['need_pass']) ? 'Y' : 'N';
            $order->deliveryTimePeriod = sanitize_text_field($_POST['order']['delivery_time_period']);

            if ($orderDeliveryVariant == 'ДТ' || $orderDeliveryVariant == 'ТТ') {
                $order->receiverTerminalCode = $orderRecipientTerminalCode;
            } else {
                $order->receiverStreet     = $orderRecipientStreet;
                $order->receiverStreetabbr = sanitize_text_field($_POST['order']['recipient']['streetabbr']);
                $order->receiverKorpus     = sanitize_text_field($_POST['order']['recipient']['korpus']);
                $order->receiverHouse      = sanitize_text_field($_POST['order']['recipient']['house']);
                $order->receiverStr        = sanitize_text_field($_POST['order']['recipient']['str']);
                $order->receiverVlad       = sanitize_text_field($_POST['order']['recipient']['vlad']);
                $order->receiverOffice     = sanitize_text_field($_POST['order']['recipient']['office']);
                $order->receiverFlat       = sanitize_text_field($_POST['order']['recipient']['flat']);
            }

            $order->useMarking          = isset($_POST['order']['use_marking']) ? 'Y'                         : 'N';
            $order->npp                 = isset($_POST['order']['npp']) ? 'Y'                                 : 'N';
            $order->cargoRegistered     = isset($_POST['order']['cargo_registered']) ? 'Y'                    : 'N';
            $order->dvd                 = isset($_POST['order']['dvd']) ? 'Y'                                 : 'N';
            $order->trm                 = isset($_POST['order']['trm']) ? 'Y'                                 : 'N';
            $order->prd                 = isset($_POST['order']['prd']) ? 'Y'                                 : 'N';
            $order->vdo                 = isset($_POST['order']['vdo']) ? 'Y'                                 : 'N';
            $order->obr                 = isset($_POST['order']['obr']) ? 'Y'                                 : 'N';
            $order->ogd                 = $_POST['order']['ogd'] ? sanitize_text_field($_POST['order']['ogd']): '';
            $order->esz                 = sanitize_text_field($_POST['order']['esz']);
            $order->chst                = sanitize_text_field($_POST['order']['chst']);
            $order->goods_return_amount = sanitize_text_field($_POST['order']['goods_return_amount']);
            $order->delivery_amount     = sanitize_text_field($_POST['order']['delivery_amount']);
            $order->paymentType         = sanitize_text_field($_POST['order']['payment_type']);


            $unitLoads = [];
            foreach ($_POST['order']['unit_loads'] as $key => $ul) {
                $index = crc32( sanitize_text_field($ul['name']) );

                if ($ul['name'] == __('Delivery', 'dpd')) {
                    $index = 'DELIVERY';
                } else {
                    $index .= '_'. sanitize_text_field($key);
                }

                $unitLoads[$index] = [
                    'ID'       => $index,
                    'NAME'     => sanitize_text_field($ul['name']),
                    'QUANTITY' => sanitize_text_field($ul['qty']),
                    'CARGO'    => isset($ul['declared_value']) ? sanitize_text_field($ul['declared_value']) : 0,
                    'NPP'      => isset($ul['npp_amount']) ? sanitize_text_field($ul['npp_amount']) : 0,
                    'VAT'      => sanitize_text_field($ul['tax']),
                    'GTIN'     => sanitize_text_field($ul['gtin']),
                    'SERIAL'   => sanitize_text_field($ul['serial']),
                    'ARTICLE'  => sanitize_text_field($ul['article']),
                ];
                if (isset($ul['declared_value'])) {
                    $order->useCargoValue = 'Y';
                }
                if (isset($ul['npp_amount'])) {
                    $order->npp = 'Y';
                }
            }

            try {
                $order->setShipment($shipment);
                $order->unitLoads = array_values($unitLoads);

                $response = $order->dpd()
                    ->setCurrencyConverter($converter)
                    ->create();
            }  catch (\SoapFault $e) {
                update_post_meta($wcorder->ID, 'dpd_order_send_error_flag', 1);
                $errors[] = $e->getMessage();
            } catch (\Exception $e) {
                update_post_meta($wcorder->ID, 'dpd_order_send_error_flag', 1);
                $errors[] = $e->getMessage();
            }

            if (isset($response) && !$response->isSuccess()) {
                $errors = $response->getErrorMessages();
                update_post_meta($wcorder->ID, 'dpd_order_send_error_flag', 1);
            } else {
                update_post_meta($wcorder->ID, 'dpd_order_send_error_flag', 0);
                foreach ($_POST['order'] as $key => $value) {
                    $key = sanitize_text_field($key);
                    
                    if (is_array($value) && $key != 'unit_loads') {
                        foreach ($value as $vkey => $vvalue) {
                            $vkey = sanitize_text_field($vkey);
                            $vvalue = sanitize_text_field($vvalue);
                            update_post_meta($wcorder->ID, 'dpd_'.$key.'_'.$vkey, $vvalue);
                        }
                    } elseif ($key == 'unit_loads') {
                        update_post_meta($wcorder->ID, 'dpd_'.$key, serialize($value));
                    } elseif ($key == 'cargo_volume'){
                        update_post_meta($wcorder->ID, 'dpd_'.$key, $shipment->getVolume());
                    }
                    else {
                        $value = sanitize_text_field($value);
                        update_post_meta($wcorder->ID, 'dpd_'.$key, $value);
                    }
                }
                update_post_meta($wcorder->ID, 'dpd_order_id', $order->id);
                update_post_meta($wcorder->ID, 'dpd_order_num', $order->orderNum);
                update_post_meta($wcorder->ID, 'dpd_status', $order->orderStatus);
                update_post_meta($wcorder->ID, 'dpd_sended', true);
                update_post_meta($wcorder->ID, 'dpd_last_status_update', 0);
                $statusList = \Ipol\DPD\DB\Order\Model::StatusList();
                $status = isset($statusList[$order->orderStatus]) ? $statusList[$order->orderStatus] : 'unknow';
                $result['success'] = __('Order sended', 'dpd');
                $result['data'] = [
                    'id' => $order->orderNum,
                    'status' => $status
                ];
            }
        }

        if ($errors) {
            $result['error'] = SimpleValidation::errorsHtml($errors);
        }

        header('Content-Type: application/json');
        return die(json_encode($result));
    }


    /**
     * Отмена заказа
     * @return
     */
    private function cancelOrder()
    {
        $orderId = isset($_GET['order_id']) ? sanitize_text_field($_GET['order_id']) : 0;
        $errors = [];
        $result = [];
        if (!$orderId) {
            $errors[] = __('Order ID is required', 'dpd');
        } else {
            $order = \Ipol\DPD\DB\Connection::getInstance($this->config)
                ->getTable('order')
                ->getByOrderId($orderId);
            try {
                $response = $order->dpd()->cancel();
                update_post_meta($orderId, 'dpd_order_id', '');
                update_post_meta($orderId, 'dpd_status', 'NEW');
                update_post_meta($orderId, 'dpd_sended', false);
            }  catch (\SoapFault $e) {
                $errors[] = $e->getMessage();
            } catch(\Exception $e) {
                $errors[] = $error->getMessage();
            }
        }
        if (isset($response) && !$response->isSuccess()) {
            $errors = $response->getErrorMessages();
        }
        if ($errors) {
            $result['error'] = SimpleValidation::errorsHtml($errors);
        } else {
            $result['success'] = __('Order canceled', 'dpd');
            $statusList = \Ipol\DPD\DB\Order\Model::StatusList();
            $result['data'] = [
                'id' => '',
                'status' => $statusList['NEW']
            ];
        }
        header('Content-Type: application/json');
        return die(json_encode($result));
    }

    /**
     * Печать документов
     * @return
     */
    private function printDocs()
    {
        $orderId = isset($_GET['order_id']) ? sanitize_text_field($_GET['order_id']) : 0;
        $type = $_GET['type'] == 'invoice' ? 'invoice' : 'label';
        $errors = [];
        $result = [];
        if (!$orderId) {
            $errors[] = __('Order ID is required', 'dpd');
        } else {
            $order = \Ipol\DPD\DB\Connection::getInstance($this->config)
                ->getTable('order')
                ->getByOrderId($orderId);

            try {
                if (!$order) {
                    throw new \Exception('Order not found');
                }


                switch ($type) {
                    case 'invoice':
                        $response = $order->dpd()->getInvoiceFile();
                    break;

                    case 'label':
                        $labelCount = sanitize_text_field($_GET['label_count']);
                        $fileFormat = sanitize_text_field($_GET['file_format']);
                        $printAreaFormat = sanitize_text_field($_GET['print_area_format']);
                        $response = $order->dpd()
                            ->getLabelFile($labelCount, $fileFormat, $printAreaFormat);
                    break;

                    default:
                        $result['error'] = __('Unknow action', 'dpd');
                    break;
                }
            }  catch (\SoapFault $e) {
                $errors[] = $e->getMessage();
            } catch(\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        if (isset($response) && !$response->isSuccess()) {
            $errors = $response->getErrorMessages();
        }
        if ($errors) {
            $result['error'] = SimpleValidation::errorsHtml($errors);
        } else {
            $result['file'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $response->getData()['file']);
        }
        header('Content-Type: application/json');
        return die(json_encode($result));
    }
}