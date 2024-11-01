<?php

namespace DPD\Helper;

 class Converter implements \Ipol\DPD\Currency\ConverterInterface {

    public function convert($amount, $currencyFrom, $currencyTo, $actualDate = false)
    {
        if (!$currencyFrom || !$currencyTo) {
            return false;
        }
        
        if ($currencyFrom == $currencyTo) {
            return $amount;
        }
        
        if ($actualDate) {
            $actualDate = date('d/m/Y', strtotime($actualDate));
        }

        $cachePath          = md5(DPD_CACHE_FOLDER.'currency'.$actualDate);
        $currencyRatesData  = [];
        $forOneCurrencyUnit = 1;
        
        if (file_exists($cachePath)) {
            $currencyRatesData = simplexml_load_string(file_get_contents($cachePath));
        }
        
        if (empty($currencyRatesData) || (time() - filemtime($cachePath)) > 86400) { //сутки кэш
            $xml = @file_get_contents(
                'http://www.cbr.ru/scripts/XML_daily.asp'.
                ($actualDate ? '?date_req='.$actualDate : '')
            );
            $currencyRatesData = simplexml_load_string($xml);
            file_put_contents($cachePath, $xml);
        }
        
        if ($currencyRatesData) {
            $from = ['currency' => $currencyFrom, 'nominal' => 1, 'value' => 1];
            $to   = ['currency' => $currencyTo,   'nominal' => 1, 'value' => 1];

            foreach ($currencyRatesData->Valute as $item) {
                if ($item->CharCode == $currencyFrom) {
                    $from['nominal'] = (float) str_replace(',', '.', $item->Nominal);
                    $from['value']   = (float) str_replace(',', '.', $item->Value);
                }
                
                if ($item->CharCode == $currencyTo) {
                    $to['nominal'] = (float) str_replace(',', '.', $item->Nominal);
                    $to['value']   = (float) str_replace(',', '.', $item->Value);
                }
            }

            $forOneCurrencyUnit = round((
                ($from['nominal'] / $from['value']) / ($to['nominal'] / $to['value'])
            ), 4);
        }

        return round($amount / $forOneCurrencyUnit, 2);
    }

 }