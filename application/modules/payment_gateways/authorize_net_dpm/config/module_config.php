<?php
/**
 * Module Name: Authorize.net DPM Payment Gateway
 * Description: Authorize.net Direct Post Method (SIM) Payment Gateway
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'authorize.net DPM';
$config['module_name'] = 'Authorize.net DPM Payment Gateway';
$config['module_description'] = 'Authorize.net Direct Post Method (SIM) Payment Gateway';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=25';
$config['module_models'] = array('Authorize_net_dpm_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'authorize_net_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_redirect_type'] = 'onsite'; //offsite or offline
$config['module_gateway_test_url'] = 'https://test.authorize.net/gateway/transact.dll';
$config['module_gateway_production_url'] = 'https://secure2.authorize.net/gateway/transact.dll';

