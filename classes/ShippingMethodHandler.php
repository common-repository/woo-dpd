<?php

namespace DPD;

use WC_Shipping_Method;

use DPD\Model\Terminal as Terminal;
use DPD\Model\Location as Location;

use DPD\Helper\SimpleValidation as SimpleValidation;
use DPD\Helper\PriceRules as PriceRules;
use DPD\Helper\Converter as Converter;
use DPD\Helper\View as View;

use DPD\Factories\Shipment as Shipment;

class ShippingMethodHandler extends WC_Shipping_Method {

    private $DPDconfig;

    public function __construct()
    {
        $this->id = DPD_SHIPPING_METHOD_ID; 
        $this->method_title = __('DPD Shipping', 'dpd');  
        $this->method_description = __('Express delivery of parcels and goods', 'dpd');
        $this->availability = 'including';
        $this->countries = [
            'RU',
            'BY',
            'KZ'
        ];

        // $this->supports = array(
        //     'shipping-zones'
        // );

        $this->init();

        $this->enabled = 'yes';
        $this->title = isset($this->settings['title']) ?
            $this->settings['title'] : __('DPD', 'dpd');

        if (isset($_SESSION['success'])) {
            unset($_SESSION['success']);
        }

        if (isset($_SESSION['error'])) {
            unset($_SESSION['error']);
        }

        global $DPDconfig;
        $this->DPDconfig = new \Ipol\DPD\Config\Config($DPDconfig);

    }

    public function init()
    {

        //Загрузка настроек API
        //$this->init_form_fields(); 
        $this->init_settings(); 

        //Инициализация обработчика сохранения настроек
        add_action(
            'woocommerce_update_options_shipping_' . $this->id,
            [$this, 'process_admin_options'] 
        );
    }

    public function admin_options()
    {
        ?>
        <h2><?php echo esc_html($this->method_title); ?></h2>
        <p><?php echo esc_html($this->method_description); ?></p>
        <p><a href="https://ipol.ru/spravka/dpd_wp/about_plugin/" target="_blank">
                <?php echo esc_html(__('Instruction for the module', 'dpd')); ?>
            </a></p>
        <?php if (
                    (!get_option('dpd_first_data_import_completed') ||
                        get_option('dpd_run_import_manually')) &&
                        $this->DPDconfig->isActiveAccount()
                ): 
        ?>
            <?php echo View::load('backend/import', [
                'first_run' => true
            ]); ?>
        <?php else: ?>
            <?php $this->generate_settings_html(); ?>
        <?php endif; ?>
        <?php
    }

    /**
     * Генерация страницы настроек
     * @return void
     */
    public function generate_settings_html($form_fields = array(), $echo = true)
    {
        echo View::load('backend/settings/settings');
    }


    /**
     * Расчет доставки
     *
     * @access public
     * @param mixed $package
     * @return void
     */
    public function calculate_shipping($package = array())
    {
        if (!$this->DPDconfig->isActiveAccount()) {
            return;
        }

        $shipmentFactory = new Shipment(
            $this->DPDconfig,
            \DPD\Helper\Sender::getDefault()['city'],
            (string) $package['destination']['country'],
            (string) $package['destination']['state'],
            (string) $package['destination']['city'],
            (string) $package['cart_subtotal']
        );
        $shipmentFactory->setItemsByCart($package['contents']);
        $shipment = $shipmentFactory->getInstance();

        $converter = new Converter();
        $shipment->setCurrencyConverter($converter);

        $paymentMethod = false;

        if (isset($_REQUEST['payment_method'])) {
            //задаем оплату
            $shipment->setPaymentMethod(1, sanitize_text_field($_REQUEST['payment_method']));
            $paymentMethod = sanitize_text_field($_REQUEST['payment_method']);
        }

        try {
            $ignoreProfiles = get_option('dpd_ignore_profile');
            $ignoreProfiles = unserialize($ignoreProfiles) ?: [];

            if (!in_array('COURIER', $ignoreProfiles)) {
                //получаем способ доставки курьером
                $shipment->setSelfDelivery(false);
                $shipment->setSelfPickup(get_option('dpd_self_pickup'));
                $calc = $shipment->calculator();
                $calc->setCurrencyConverter($converter);
                $tariff = $calc->calculate(get_option('woocommerce_currency'));

                if ($shipment->isPossibileDelivery()) {
                    $tariff = PriceRules::round($tariff);

                    $this->add_rate([
                        'id'    => $this->id.'_'.$tariff['SERVICE_CODE'].'_courier',
                        'label' => $this->method_title.' ('.(__('Courier', 'dpd')).', '. $tariff['DAYS'] .' дн.)',
                        'cost'  => $tariff['COST']
                    ]);
                }
            }

            if (!in_array('PICKUP', $ignoreProfiles)) {
                //получаем способ доставки самовывоз
                $shipment->setSelfDelivery(true);
                $shipment->setSelfPickup(get_option('dpd_self_pickup'));
                
                $terminal = new Terminal($this->DPDconfig);
                $where = 'LOCATION_ID = :location AND SCHEDULE_SELF_DELIVERY != ""';
                $bind = [
                    ':location' => $shipment->getReceiver()['CITY_ID']
                ];

                if (get_option('dpd_ogd')) {
                    $where .= ' AND SERVICES LIKE :ogd';
                    $bind['ogd'] = '%'.get_option('dpd_ogd').'%';
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

                if (sizeof($terminals) > 0) {
                    $calc   = $shipment->calculator();
                    $tariff = $calc->calculate(get_option('woocommerce_currency'));
                
                    if ($shipment->isPossibileSelfDelivery()) {
                        $tariff = PriceRules::round($tariff);

                        $this->add_rate([
                            'id' => $this->id.'_'.$tariff['SERVICE_CODE'].'_pickup',
                            'label' => $this->method_title.' ('.(__('Pickup', 'dpd')).', '. $tariff['DAYS'] .' дн.)',
                            'cost' => $tariff['COST']
                        ]);
                    }
                }
            }
            
        } catch(\SoapFault $e) {
            // echo $e->getMessage();
            // die;
        } catch(\Exception $e) {
            // echo $e->getMessage();
            // die;
        }
    }

    /**
     * Сохранение настроек
     * @return void
     */
    public function process_admin_options()
    {
        $importCompleted = get_option('dpd_first_data_import_completed');
        $location = new Location($this->DPDconfig);
        $terminal = new Terminal($this->DPDconfig);
        $citiesCount = $location->select('count(*) as count')->row()->getArrayCopy();
        $terminalsCount = $terminal->select('count(*) as count')->row()->getArrayCopy();

        //валидация
        $dataForValidation = [
            [
                'type' => $importCompleted ? 'required' : '',
                'value' => isset($_POST['dpd']['sender_fio']) ?
                    sanitize_text_field($_POST['dpd']['sender_fio']) : '',
                'error' => __(
                    'Sender <strong>Contact person</strong> is required',
                    'dpd'
                )
            ],
            [
                'type' => $importCompleted ? 'required' : '',
                'value' => isset($_POST['dpd']['sender_name']) ?
                    sanitize_text_field($_POST['dpd']['sender_name']) : '',
                'error' => __(
                    'Sender <strong>Contact name</strong> is required',
                    'dpd'
                )
            ],
            [
                'type' => $importCompleted ? 'required' : '',
                'value' => isset($_POST['dpd']['sender_phone']) ?
                    sanitize_text_field($_POST['dpd']['sender_phone']) : '',
                'error' => __(
                    'Sender <strong>Phone</strong> is required',
                    'dpd'
                )
            ],
            [
                'type' => ($importCompleted ? 'email|required' : ''),
                'value' => isset($_POST['dpd']['sender_email']) ?
                    sanitize_email($_POST['dpd']['sender_email']) : '',
                'error' => __(
                    'Sender <strong>Email</strong> is required',
                    'dpd'
                )
            ],
            [
                'type' => $importCompleted ? 'required' : '',
                'value' => isset($_POST['dpd']['cargo_category']) ?
                    sanitize_text_field($_POST['dpd']['cargo_category']) : '',
                'error' => __(
                    '<strong>Sending content</strong> is required',
                    'dpd'
                )
            ],
            [
                'type' => 'number'. ($importCompleted ? '|required' : ''),
                'value' => isset($_POST['dpd']['weight_default']) ?
                    sanitize_text_field($_POST['dpd']['weight_default']) : 1000,
                'error' => __(
                    '<strong>Weight default, g</strong> can\'t be empty and must be a number',
                    'dpd'
                )
            ],
            [
                'type' => 'number'. ($importCompleted ? '|required' : ''),
                'value' => isset($_POST['dpd']['length_default']) ?
                    sanitize_text_field($_POST['dpd']['length_default']) : 200,
                'error' => __(
                    '<strong>Length by default, mm</strong> can\'t be empty and  must be a number',
                    'dpd'
                )
            ],
            [
                'type' => 'number'. ($importCompleted ? '|required' : ''),
                'value' => isset($_POST['dpd']['width_default']) ?
                    sanitize_text_field($_POST['dpd']['width_default']) : 100,
                'error' => __(
                    '<strong>Width by default, mm</strong> can\'t be empty and must be a number',
                    'dpd'
                )
            ],
            [
                'type' => 'number'. ($importCompleted ? '|required' : ''),
                'value' => isset($_POST['dpd']['height_default']) ?
                    sanitize_text_field($_POST['dpd']['height_default']) : 200,
                'error' => __(
                    '<strong>Height by default, mm</strong> can\'t be empty and must be a number',
                    'dpd'
                )
            ]
        ];

        if (sanitize_text_field($_POST['dpd']['account_default_country']) == 'RU') {
            $dataForValidation[] = [
                'type' => 'required',
                'value' => sanitize_text_field($_POST['dpd']['client_number_RU']),
                'error' => __('<strong>Client Number</strong> for Russia is required', 'dpd')
            ];

            $dataForValidation[] = [
                'type' => 'required',
                'value' => sanitize_text_field($_POST['dpd']['auth_key_RU']),
                'error' => __('<strong>Authorization key</strong> for Russia is required', 'dpd')
            ];

        } else if (sanitize_text_field($_POST['dpd']['account_default_country']) == 'KZ') {
            $dataForValidation[] = [
                'type' => 'required',
                'value' => sanitize_text_field($_POST['dpd']['client_number_KZ']),
                'error' => __('<strong>Client Number</strong> for Kazakhstan is required', 'dpd')
            ];

            $dataForValidation[] = [
                'type' => 'required',
                'value' => sanitize_text_field($_POST['dpd']['auth_key_KZ']),
                'error' => __('<strong>Authorization key</strong> for Kazakhstan is required', 'dpd')
            ];

        } else if (sanitize_text_field($_POST['dpd']['account_default_country']) == 'BY') {
            $dataForValidation[] = [
                'type' => 'required',
                'value' => sanitize_text_field($_POST['dpd']['client_number_BY']),
                'error' => __('<strong>Client Number</strong> for Belarus is required', 'dpd')
            ];

            $dataForValidation[] = [
                'type' => 'required',
                'value' => sanitize_text_field($_POST['dpd']['auth_key_BY']),
                'error' => __('<strong>Authorization key</strong> for Belarus is required', 'dpd')
            ];

        }

        $errors = SimpleValidation::validate($dataForValidation);

        if ($errors) {
            $this->errors = $errors;
        }

        foreach ($_POST['dpd'] as $key => $value) {
            if ($key == 'sender') {
                $def = false;
                
                foreach ($value as $k => $v) {
                    if (!array_filter($v)) {
                        unset($value[$k]);
                    } elseif ($def) {
                        $value[$k]['default'] = 0;
                    } elseif ($v['default'] > 0) {
                        $def = true;
                    }
                }
            }
            
            if (is_array($value)) {
                $value = serialize($value);
            }
        
            update_option('dpd_'.$key, sanitize_text_field($value), true);
        }
        
        $params = [
            'ignore_tariff',
            'commission_npp_payment'
        ];
        
        foreach ($params as $param) {
            if (!isset($_POST['dpd'][$param])) {
                update_option('dpd_'.$param, '', true);
            }
        }

        return true;

        // return wp_redirect(
        //     admin_url('admin.php?page=wc-settings&tab=shipping&section=dpd'),
        //     301
        // );
    }
}

?>