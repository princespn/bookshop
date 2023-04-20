<?php defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'yearly_invoice_payment_stats';
$config['module_name'] = 'Yearly Invoice Payments Stats';
$config['module_description'] = 'Invoice Payment Stats for the Year';
$config['module_model_alias'] = 'year_inv_pay_stats';
$config['module_models'] = array('Year_invoice_payments_stats_model' => $config['module_model_alias']);
$config['chart_title'] = 'invoice_payments_received_each_month';
$config['x_axis'] = 'amount';

//set the file path for the views template
$config['module_view_path'] = 'admin';

//set the template to use for rendering HTML data
$config['module_admin_view_template'] = 'tpl_admin_yearly_report_details';
$config['module_generate_function'] = 'generate_module';