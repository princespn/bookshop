<?php
/**
 * Module Name: Per Item Shipping
 * Description: Charge shipping rate for each item purchased
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'per_item';
$config['module_name'] = 'Per Item Shipping';
$config['module_description'] = 'Charge shipping rate for each item purchased';
$config['module_models'] = array('Per_item_shipping_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_shipping_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_rates';
$config['module_required_input_fields'] = array('zone_id', 'shipping_description');
$config['module_shipping_table'] = 'module_shipping_per_item';