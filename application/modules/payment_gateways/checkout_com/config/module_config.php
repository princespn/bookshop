<?php
/**
 * Module Name: Checkout.com Payment Gateway
 * Description: Checkout.com Credit Card Payments
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'checkout_com';
$config['module_name'] = 'Checkout.com Payment Gateway';
$config['module_description'] = 'Checkout.com Credit Card Payments';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=38';
$config['module_models'] = array('Checkout_com_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'checkout_com_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_gateway_test_url'] = 'https://api.sandbox.checkout.com/payments';
$config['module_gateway_production_url'] = 'https://api.checkout.com/payments';
$config['module_redirect_type'] = 'onsite'; //offsite or offline
$config['module_checkout_header_script'] = array('css' => array(base_url('themes/modules/checkout_com.css')),
                                                 'js'  => array('https://cdn.checkout.com/js/framesv2.min.js'));
