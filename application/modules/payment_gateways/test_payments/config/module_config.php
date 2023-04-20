<?php
/**
 * Module Name: Test Payment Gateway
 * Description: Test Payments Only - No transaction actually happens
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'test';
$config['module_name'] = 'Test Payment Gateway';
$config['module_description'] = 'Test Payments Only - No transaction actually happens';
$config['module_external_url'] = '';
$config['module_models'] = array('Test_payments_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'test_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_redirect_type'] = 'onsite'; //offsite or offline