<?php
/**
 * Module Name: 2Checkout Payment Gateway
 * Description: 2Checkout.com Payment Gateway Payments API
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = '2checkout';
$config['module_name'] = '2Checkout Payment Gateway';
$config['module_description'] = '2Checkout.com Payment Gateway Payments API';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=23';
$config['module_models'] = array('Two_checkout_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'twocheckout_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_redirect_type'] = 'onsite'; //offsite or offline

$config['module_checkout_header_script'] = array('js' => array('https://www.2checkout.com/checkout/api/2co.min.js'));
