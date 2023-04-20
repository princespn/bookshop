<?php defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'weekly_affiliate_clicks_stats';
$config['module_name'] = 'Weekly affiliate clicks stats';
$config['module_description'] = 'Affiliate click stats on weekly basis';
$config['module_model_alias'] = 'week_aff_clicks';
$config['module_models'] = array('week_affiliate_clicks_stats_model' => $config['module_model_alias']);
$config['chart_title'] = 'affiliate_clicks_generated_per_day';
$config['x_axis'] = 'amount';

//set the file path for the views template
$config['module_view_path'] = 'admin';

//set the template to use for rendering HTML data
$config['module_admin_view_template'] = 'tpl_admin_weekly_report_details';
$config['module_generate_function'] = 'generate_module';