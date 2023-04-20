<?php
/**
 * Module Name: Dwolla Mass Payment
 * Description: Generate Dwolla Mass Payment file for Affiliate Payments
 */

defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_alias'] = 'dwolla_mass_payment';
$config['module_name'] = 'Dwolla Mass Payment';
$config['module_description'] = 'Generate dwolla mass payment file';
$config['module_models'] = array('Dwolla_model' => $config['module_alias']);
$config['module_libraries'] = array('Dwolla/DwollaRestClient');

//for admin area management of the module
$config['module_admin_view_function'] = 'run_query';
$config['module_admin_view_template'] = 'tpl_admin_payments_view';
$config['module_view_function_sort_order'] = 'ASC';
$config['module_view_function_sort_column'] = 'total_commissions';
$config['module_download_file_extension'] = 'txt'; //csv or txt

//module specific
$config['module_permisssions'] = array("send", "transactions", "balance", "request", "contacts", "accountinfofull", "funding");