<?php defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'monthly_top_affiliate_sales_stats';
$config['module_name'] = 'Top Affiliates By Sales';
$config['module_description'] = 'Top Affiliate Earners By Sales Per Month';
$config['module_model_alias'] = 'month_top_aff_earners';
$config['module_models'] = array('Month_top_affiliate_sales_model' => $config['module_model_alias']);
$config['chart_title'] = 'top_sale_earners';
$config['x_axis'] = 'amount';
$config['query_limit'] = '25';

//set the file path for the views template
$config['module_view_path'] = 'admin';

//set the template to use for rendering HTML data
$config['module_admin_view_template'] = 'tpl_admin_monthly_top_report_details';
$config['module_generate_function'] = 'generate_module';