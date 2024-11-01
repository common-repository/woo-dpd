<?php

$dpdTarifOff = get_option('dpd_ignore_tariff');
if ($dpdTarifOff) {
    $dpdTarifOff = unserialize($dpdTarifOff);
}
$nppPayment = get_option('dpd_commission_npp_payment');
if ($nppPayment) {
    $nppPayment = unserialize($nppPayment);
} else {
    $nppPayment = [];
}
$nppPercent = get_option('dpd_commission_npp_percent');
$nppMinSum = get_option('dpd_commission_npp_minsum');

return array_merge(
    [
        'KLIENT_NUMBER'            => get_option('dpd_client_number_RU'),
        'KLIENT_KEY'               => get_option('dpd_auth_key_RU'),

        'KLIENT_NUMBER_TEST'            => get_option('dpd_client_number_test_RU'),
        'KLIENT_KEY_TEST'               => get_option('dpd_auth_key_test_RU'),
        
        'KLIENT_CURRENCY'          => get_option('dpd_currency_RU'),

        'KLIENT_NUMBER_KZ'         => get_option('dpd_client_number_KZ'),
        'KLIENT_KEY_KZ'            => get_option('dpd_auth_key_KZ'),
        'KLIENT_NUMBER_TEST_KZ'    => get_option('dpd_client_number_test_KZ'),
        'KLIENT_KEY_TEST_KZ'       => get_option('dpd_auth_key_test_KZ'),
        'KLIENT_CURRENCY_KZ'       => get_option('dpd_currency_KZ'),

        'KLIENT_NUMBER_BY'         => get_option('dpd_client_number_BY'),
        'KLIENT_KEY_BY'            => get_option('dpd_auth_key_BY'),
        'KLIENT_NUMBER_TEST_BY'    => get_option('dpd_client_number_test_BY'),
        'KLIENT_KEY_TEST_BY'       => get_option('dpd_auth_key_test_BY'),
        'KLIENT_CURRENCY_BY'       => get_option('dpd_currency_BY'),
        'API_DEF_COUNTRY'          => get_option('dpd_account_default_country'),
        'IS_TEST'                  => get_option('dpd_test_mode'),
        'WEIGHT'                   => get_option('dpd_weight_default'),
        'LENGTH'                   => get_option('dpd_length_default'),
        'WIDTH'                    => get_option('dpd_width_default'),
        'HEIGHT'                   => get_option('dpd_height_default'),
        'TARIFF_OFF'               => $dpdTarifOff,
        'TARIFF_OFF'               => $dpdTarifOff,
        'USE_MODE'                 => get_option('dpd_use_mode'),
        'DEFAULT_TARIFF_CODE'      => get_option('dpd_tariff_default'),
        'DEFAULT_TARIFF_THRESHOLD' => get_option('dpd_default_tariff_treshold'),
        'DEFAULT_PRICE'            => get_option('dpd_default_price'),
        'DECLARED_VALUE'           => get_option('dpd_declared_value'),
        'CURRENCY'                 => [
            'RU' => get_option('dpd_currency_RU'),
            'BY' => get_option('dpd_currency_BY'),
            'KZ' => get_option('dpd_currency_KZ'),
            'KG' => get_option('dpd_currency_KG'),
            'AM' => get_option('dpd_currency_AM'),
        ],
        'COMMISSION_NPP_CHECK'     => [
            1 => get_option('dpd_commission_npp_check') ? 1 : 0,
            2 => false
        ],
        'COMMISSION_NPP_PERCENT' => [
            1 => $nppPercent ? $nppPercent : 0,
            2 => 0
        ],
        'COMMISSION_NPP_MINSUM' => [
            1 => $nppMinSum ? $nppMinSum : 0,
            2 => 0
        ],
        'COMMISSION_NPP_PAYMENT' => [
            1 => $nppPayment,
            2 => []
        ],
        'SOURCE_NAME' => 'WordPress',
        'SENDER_REGULAR_NUM' => get_option('dpd_sender_regular_num'),
        'MARKUP' => [
            'VALUE' => get_option('dpd_markup_value'),
            'TYPE'  => get_option('dpd_markup_type'),
        ]
    ],

    extension_loaded('pdo_mysql') ? [
        'DB' => [
            'DSN'      => 'mysql:host='. DB_HOST .';dbname='. DB_NAME,
            'USERNAME' => DB_USER,
            'PASSWORD' => DB_PASSWORD,
            'DRIVER'   => null,
            'PDO'      => null,
        ]
    ] : [],

    []
);