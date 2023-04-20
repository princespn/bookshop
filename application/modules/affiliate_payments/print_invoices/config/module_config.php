<?php
/**
 * Module Name: Print Affiliate Invoices
 * Description: Generate affiliate Invoices for Affiliate Payments
 */

defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_alias'] = 'print_invoices';
$config['module_name'] = 'Print Affiliate Invoices';
$config['module_description'] = 'Generate affiliate Invoices for Affiliate Payments';
$config['module_models'] = array('Print_invoices_model' => $config['module_alias'] );

//for admin area management of the module
$config['module_admin_view_function'] = 'run_query';
$config['module_admin_view_template'] = 'tpl_admin_payments_view';
$config['module_view_function_sort_order'] = 'ASC';
$config['module_view_function_sort_column'] = 'total_commissions';