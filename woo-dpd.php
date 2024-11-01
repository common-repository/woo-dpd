<?php
/**
 * Plugin Name: DPD in Russia - Shipping for WooCommerce
 * Plugin URI: https://www.dpd.ru/
 * Description: Интеграция Woocommerce с сервисом доставки DPD
 * Version: 1.0.83
 * Author: Ipol
 * Author URI: http://ipolh.com/
 * Text Domain: dpd
 * Domain Path: /languages
 */

if (!defined('WPINC')) {
    die;
}

if (!defined('DPD_PLUGIN_URI')) {
    define('DPD_PLUGIN_URI', plugin_dir_url(__FILE__));
}

if (!defined('DPD_PLUGIN_PATH')) {
    define('DPD_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
}

define('DPD_SHIPPING_METHOD_ID', 'dpd');

define('DPD_CACHE_FOLDER', DPD_PLUGIN_PATH.'cache/');

//Проверяем наличие woocommerce
if (in_array(
        'woocommerce/woocommerce.php',
        apply_filters('active_plugins', get_option('active_plugins'))
    )) {

    //Подключаем главный класс плагина
    if (!class_exists('\DPD\Kernel')) {
        include_once dirname( __FILE__ ).'/classes/Kernel.php';
    }

    $DPDconfig = include 'config.php';
    new \DPD\Kernel();
}