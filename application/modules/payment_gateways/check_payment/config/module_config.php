<?php
/**
 * Module Name: Checks Payment Gateway
 * Description: Payment option via check
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'Mobile Money';
$config['module_name'] = 'Mobile Money Payment Gateway';
$config['module_description'] = 'Payment option via Mobile Money';
$config['module_models'] = array('Check_payment_model' => $config['module_alias']);
$config['module_template'] = 'tpl_admin_payment_gateway_manage'; //specify a custom template to use here
$config['module_generate_function'] = 'generate_form';
$config['module_gateway_form'] = 'check_payment_gateway_form';
$config['module_redirect_type'] = 'offline'; //offsite or online