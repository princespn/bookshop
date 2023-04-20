<?php
/**
 * Module Name: Percentage Based  Shipping
 * Description: Charge a percentage amount for all items purchased
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'percentage';
$config['module_name'] = 'Percentage Based  Shipping';
$config['module_description'] = 'Charge a percentage amount for all items purchased';
$config['module_models'] = array('Percentage_shipping_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_shipping_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_rates';
$config['module_required_input_fields'] = array('zone_id', 'shipping_description', 'amount');
$config['module_shipping_table'] = 'module_shipping_percentage';