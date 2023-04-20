<?php
/**
 * Module Name: Stripe Payment Gateway
 * Description: Stripe.com Credit Card Payments
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'stripe';
$config['module_name'] = 'Stripe Payment Gateway';
$config['module_description'] = 'Stripe.com Credit Card Payments';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=27';
$config['module_models'] = array('Stripe_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'stripe_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_redirect_type'] = 'onsite'; //offsite or offline
$config['module_checkout_header_script'] = array('js' => array('https://js.stripe.com/v3/'));
