<?php
/**
 * Module Name: WorldPay Payment Gateway
 * Description: Accept WorldPay Credit Card Payments
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'worldpay';
$config['module_name'] = 'Worldpay Payments';
$config['module_description'] = 'Worldpay Payments';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=28';
$config['module_models'] = array('Worldpay_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'worldpay_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_redirect_type'] = 'onsite'; //offsite or offline

$config['module_checkout_header_script'] = array('js' => array('https://cdn.worldpay.com/v1/worldpay.js'));
