<?php

namespace DPD\Helper;

class PriceRules {

    /**
     * Округление цены
     * @param array $tariff
     * @return $tariff
     */
    public static function round($tariff)
    {
        if (intval(get_option('dpd_round_to'))) {
            $tariff['COST'] = ceil($tariff['COST'] / get_option('dpd_round_to')) * get_option('dpd_round_to');
        } else {
            $tariff['COST'] = round($tariff['COST'], 2);
        }

        if (intval(get_option('dpd_add_delivery_day'))) {
            $tariff['DAYS'] = $tariff['DAYS'] + intval(get_option('dpd_add_delivery_day'));
        }

        return $tariff;
    }
}