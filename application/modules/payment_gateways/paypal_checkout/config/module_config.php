<?php
/**
 * Module Name: Paypal Checkout Payment Gateway
 * Description: Accept Paypal Checkout Payments
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'paypal_checkout';
$config['module_name'] = 'Paypal Checkout Payment Gateway';
$config['module_description'] = 'Accept Paypal Checkout Payments';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=26';
$config['module_models'] = array('Paypal_checkout_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'paypal_checkout_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_redirect_type'] = 'onsite'; //offsite or offline
