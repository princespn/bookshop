<?php
/**
 * Module Name: Cash On Delivery Payment Gateway
 * Description: Payment option via cash on delivery (COD)
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'cash_on_delivery';
$config['module_name'] = 'Payment Voucher';
$config['module_description'] = 'Pay via Payment Voucher';
$config['module_models'] = array('Cash_on_delivery_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'cash_on_delivery_gateway_form';
$config['module_payment_gateway_table'] = 'module_payment_gateway_cash_on_delivery';
$config['module_redirect_type'] = 'offline'; //offsite or online