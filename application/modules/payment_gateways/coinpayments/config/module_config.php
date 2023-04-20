<?php
/**
 * Module Name: Coinpayments Payment Gateway
 * Description: Coinpayments Payments
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'coinpayments';
$config['module_name'] = 'Coinpayments Payment Gateway';
$config['module_description'] = 'Coinpayments Payments';
$config['module_external_url'] = 'https://my.jrox.com/link.php?id=39';
$config['module_models'] = array('coinpayments_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_gateway_form'] = 'coinpayments_gateway_form';
$config['module_one_off_pay_function'] = 'generate_payment';
$config['module_ipn_pay_function'] = 'ipn_process';
$config['module_gateway_production_url'] = 'https://www.coinpayments.net/index.php';
$config['module_redirect_type'] = 'offsite'; //offsite or offline
