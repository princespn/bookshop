<?php defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'monthly_invoice_payments_stats';
$config['module_name'] = 'Monthly invoice payments stats';
$config['module_description'] = 'Invoice payment stats on monthly basis';
$config['module_model_alias'] = 'month_invoice_payments_stats';
$config['module_models'] = array('Month_invoice_payments_stats_model' => $config['module_model_alias']);
$config['chart_title'] = 'payments_received_per_day';
$config['x_axis'] = 'amount';

//set the file path for the views template
$config['module_view_path'] = 'admin';

//set the template to use for rendering HTML data
$config['module_admin_view_template'] = 'tpl_admin_monthly_report_details';
$config['module_generate_function'] = 'generate_module';