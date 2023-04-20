<?php
/**
 * Module Name: Paypal Standard Payment Gateway
 * Description: Use Paypal Standard and Subscribe Buttons
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'paypal_standard';
$config['module_name'] = 'Paypal Standard Payment Gateway';
$config['module_description'] = 'For Paypal Standard and Subcribe Button Payments';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=26';
$config['module_models'] = array('Paypal_standard_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'paypal_standard_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_gateway_refund_testing_url'] = 'https://api-3t.sandbox.paypal.com/nvp';
$config['module_gateway_refund_production_url'] = 'https://api-3t.paypal.com/nvp';
$config['module_gateway_refund_url'] = 'https://api-3t.paypal.com/nvp';
$config['module_redirect_type'] = 'offsite'; //offsite or offline
$config['module_ipn_variable'] = 'custom';
$config['module_ipn_pay_function'] = 'ipn_process';
$config['module_gateway_test_url'] = 'https://www.sandbox.paypal.com';
$config['module_gateway_production_url'] = 'https://www.paypal.com';