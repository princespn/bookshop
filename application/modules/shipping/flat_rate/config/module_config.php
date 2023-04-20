<?php
/**
 * Module Name: Flat Rate Shipping
 * Description: Charge a flat shipping rate for entire purchase
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'flat_rate';
$config['module_name'] = 'Flat Rate Shipping';
$config['module_description'] = 'Charge flat shipping rate for entire purchase';
$config['module_models'] = array('Flat_rate_shipping_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_shipping_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_rates';
$config['module_required_input_fields'] = array('zone_id', 'shipping_description', 'amount');
$config['module_shipping_table'] = 'module_shipping_flat_rate';
