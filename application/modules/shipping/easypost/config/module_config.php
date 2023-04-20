<?php
/**
 * Module Name: Easypost - Fedex, USPS, and UPS Shipping Quotes
 * Description: Fedex, USPS, and UPS Shipping Quotes via EasyPost API
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'easypost';
$config['module_name'] = 'Fedex, USPS, and UPS Shipping Quotes';
$config['module_description'] = 'Fedex, USPS, and UPS Shipping Quotes via EasyPost API';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=35';
$config['module_models'] = array('Easypost_shipping_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_shipping_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_rates';
$config['module_required_input_fields'] = array('zone_id', 'shipping_description');
$config['module_shipping_table'] = 'module_shipping_easypost';
