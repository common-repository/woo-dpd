<?php

namespace DPD\Factories;

use DPD\Helper\Converter as Converter;

class Shipment {

    protected $shipment;
    protected $subtotal;
    
    public function __construct(
        \Ipol\DPD\Config\Config $config,
        $senderCityOption,
        $recipientCountryCode,
        $recipientState,
        $recipientCity,
        $subtotal
    ) {
        $this->shipment = new \Ipol\DPD\Shipment($config);
        $this->shipment->setCurrencyConverter(new Converter());
        $this->setSender($senderCityOption);
        $this->setReceiver($recipientCountryCode, $recipientState, $recipientCity);
        $this->subtotal = $subtotal;
    }

    protected function setSender($senderCityOption)
    {
        $senderCity = explode(',', $senderCityOption);
        $this->shipment->setSender(
            trim($senderCity[0]),
            trim($senderCity[1]),
            trim($senderCity[2])
        );
    }

    protected function setReceiver($recipientCountryCode, $recipientState, $recipientCity)
    {
        $countryArr = [
            'RU' => 'Россия',
            'BY' => 'Беларусь',
            'KZ' => 'Казахстан'
        ];

        if (in_array($recipientCountryCode, $countryArr)) {
            $recipientCountryCode = array_search($recipientCountryCode, $countryArr);
        }

        $this->shipment->setReceiver(
            isset($countryArr[$recipientCountryCode]) ? $countryArr[$recipientCountryCode] : '',
            $recipientState,
            $recipientCity
        );
    }


    public function setItemsByCart(array $cart)
    {
        $items = [];

        foreach ($cart as $item) {
            $items[] = $this->prepareItem([
                'NAME'       => $item['data']->get_title(),
                'QUANTITY'   => $item['quantity'],
                'PRICE'      => $item['line_total'] / $item['quantity'],
                'VAT_RATE'   => $item['line_tax'],
                'WEIGHT'     => (float) $item['data']->get_weight() ?: 0,
                'DIMENSIONS' => [
                    'LENGTH' => (float) $item['data']->get_length() ?: 0,
                    'WIDTH'  => (float) $item['data']->get_width() ? : 0,
                    'HEIGHT' => (float) $item['data']->get_height() ?: 0
                ],
            ]);
        }

        $this->shipment->setItems($items, $this->subtotal);
        $this->shipment->setCurrency(get_option('woocommerce_currency'));
    }

    public function setItemsByOrder(array $orderItems)
    {
        $items = [];
        foreach ($orderItems as $item) {
            $product = $item->get_product();
            $items[] = $this->prepareItem([
                'NAME' => $product->get_title(),
                'QUANTITY' => $item->get_quantity(),
                'PRICE' => $product->get_price(),
                'VAT_RATE' => $item->get_total_tax(),
                'WEIGHT' => (float)$product->get_weight() ? : 0,
                'DIMENSIONS' => [
                    'LENGTH' => (float)$product->get_length() ? : 0,
                    'WIDTH' => (float)$product->get_width() ? : 0,
                    'HEIGHT' => (float)$product->get_height() ? : 0
                ]
            ]);
        }

        $this->shipment->setItems($items, $this->subtotal);
        $this->shipment->setCurrency(get_option('woocommerce_currency'));
    }


    public function getInstance()
    {
        return $this->shipment;
    }

    protected function prepareItem($item)
    {
        $weight_units = [
            'g'   => 1,
            'oz'  => 28.3495,
            'lbs' => 453.592,
            'kg'  => 1000,
        ];
        
        $dimensions_units = [
            'mm' => 1,
            'cm' => 10,
            'in' => 25.4,
            'yd' => 914,
            'm'  => 1000,
        ];

        $weight_unit     = get_option( 'woocommerce_weight_unit' );
        $dimensions_unit = get_option( 'woocommerce_dimension_unit' );

        $item['WEIGHT'] =               floatval($item['WEIGHT']) * ($weight_units[$weight_unit] ?? 1);
        $item['DIMENSIONS']['LENGTH'] = floatval($item['DIMENSIONS']['LENGTH']) * ($dimensions_units[$dimensions_unit] ?? 1);
        $item['DIMENSIONS']['WIDTH']  = floatval($item['DIMENSIONS']['WIDTH'])  * ($dimensions_units[$dimensions_unit] ?? 1);
        $item['DIMENSIONS']['HEIGHT'] = floatval($item['DIMENSIONS']['HEIGHT']) * ($dimensions_units[$dimensions_unit] ?? 1);

        return $item;
    }
}