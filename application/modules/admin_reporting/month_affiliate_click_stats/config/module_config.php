<?php defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'monthly_affiliate_clicks_stats';
$config['module_name'] = 'Monthly affiliate clicks stats';
$config['module_description'] = 'Affiliate Click Stats for the month';
$config['module_model_alias'] = 'month_click_stats';
$config['module_models'] = array('Month_affiliate_clicks_stats_model' => $config['module_model_alias']);
$config['chart_title'] = 'traffic_generated_per_day';
$config['x_axis'] = 'clicks';

//set the file path for the views template
$config['module_view_path'] = 'admin';

//set the template to use for rendering HTML data

$config['module_admin_view_template'] = 'tpl_admin_monthly_report_details';
$config['module_generate_function'] = 'generate_module';