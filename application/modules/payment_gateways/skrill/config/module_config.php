<?php
/**
 * Module Name: Skrill Payment Gateway
 * Description: Accept Skrill / Moneybookers based payments
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'skrill';
$config['module_name'] = 'Skrill Payment Gateways';
$config['module_description'] = 'Accept Skrill / Moneybookers based payments';
$config['module_external_url'] = 'https://www.skrill.com';
$config['module_models'] = array('Skrill_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'skrill_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_redirect_type'] = 'offsite'; //offsite or offline
$config['module_ipn_variable'] = 'custom';
$config['module_ipn_pay_function'] = 'ipn_process';
$config['module_gateway_production_url'] = 'https://pay.skrill.com';
$config['module_gateway_production_ipn_handler'] = 'https://secure.skrill.com/ipn2.ashx';
