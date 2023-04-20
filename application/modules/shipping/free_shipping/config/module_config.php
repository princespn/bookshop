<?php
/**
 * Module Name: Free Shipping
 * Description: Charge free shipping when a specific amount has been reached
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'free_shipping';
$config['module_name'] = 'Free Shipping';
$config['module_description'] = 'Charge free shipping when a specific amount has been reached';
$config['module_models'] = array('Free_shipping_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_shipping_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_rates';
$config['module_required_input_fields'] = array('zone_id', 'shipping_description', 'amount');
$config['module_shipping_table'] = 'module_shipping_free_shipping';