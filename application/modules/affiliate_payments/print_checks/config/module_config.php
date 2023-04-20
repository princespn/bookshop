<?php
/**
 * Module Name: Print Affiliate Checks
 * Description: Generate affiliate checks for Affiliate Payments
 */

defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_alias'] = 'print_checks';
$config['module_name'] = 'Print Affiliate Checks';
$config['module_description'] = 'Generate affiliate checks';
$config['module_models'] = array('Print_checks_model' => $config['module_alias'] );

//for admin area management of the module
$config['module_admin_view_function'] = 'run_query';
$config['module_admin_view_template'] = 'tpl_admin_payments_view';
$config['module_view_function_sort_order'] = 'ASC';
$config['module_view_function_sort_column'] = 'total_commissions';