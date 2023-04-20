<?php
/**
 * Module Name: Unit Based Shipping
 * Description: Calculate shipping costs based on weight, price, and zone
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'unit_based';
$config['module_name'] = 'Unit Based Shipping';
$config['module_description'] = 'Calculate shipping costs based on weight or price';
$config['module_models'] = array('Unit_based_shipping_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_shipping_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_rates';
$config['module_required_input_fields'] = array('zone_id', 'shipping_description', 'min_amount', 'max_amount', 'amount');
$config['module_shipping_table'] = 'module_shipping_unit_based';