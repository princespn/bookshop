<?php
/**
 * Module Name: Payza Payment Gateway
 * Description: Accept Payza based payments
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'payza';
$config['module_name'] = 'Payza Payment Gateway';
$config['module_description'] = 'Accept Payza based payments';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=24';
$config['module_models'] = array('Payza_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'payza_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_redirect_type'] = 'offsite'; //offsite or offline
$config['module_ipn_variable'] = 'custom';
$config['module_ipn_pay_function'] = 'ipn_process';
$config['module_gateway_production_url'] = 'https://secure.payza.com/checkout';
$config['module_gateway_production_ipn_handler'] = 'https://secure.payza.com/ipn2.ashx';
