<?php
/**
 * Module Name: Payeezy.js Payment Gateway
 * Description: Payeezy.js Payment Gateway by First Data
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'payeezy_js';
$config['module_name'] = 'Payeezy.js Payment Gateway';
$config['module_description'] = 'Payeezy.js Payment Gateway by First Data';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=29';
$config['module_models'] = array('Payeezy_js_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'payeezy_js_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_redirect_type'] = 'onsite'; //offsite or offline
$config['module_gateway_test_url'] = 'https://api-cert.payeezy.com/v1/transactions';
$config['module_gateway_production_url'] = 'https://api.payeezy.com/v1/transactions';
$config['module_checkout_header_script'] = array('js' => array(base_url('js/payeezy/js/payeezy_us_v5.1.js')));
