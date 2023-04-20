<?php
/**
 * Module Name: Bank Transfer Payment Gateway
 * Description: Allow payments by direct bank or wire transfer
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'bank_transfer';
$config['module_name'] = 'Bank Transfer Payment Gateway';
$config['module_description'] = 'Allow payments by direct bank or wire transfer';
$config['module_models'] = array('Bank_transfer_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'bank_transfer_gateway_form';
$config['module_redirect_type'] = 'offline'; //offsite or online