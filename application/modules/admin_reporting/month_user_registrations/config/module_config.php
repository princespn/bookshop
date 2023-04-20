<?php defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'monthly_user_registrations';
$config['module_name'] = 'User Registrations';
$config['module_description'] = 'Shows the number of users who registered on a daily basis';
$config['module_model_alias'] = 'month_user_reg';
$config['module_models'] = array('Month_user_registrations_model' => $config['module_model_alias']);
$config['chart_title'] = 'daily_user_registrations';
$config['x_axis'] = 'users';
$config['query_limit'] = '25';

//set the file path for the views template
$config['module_view_path'] = 'admin';

//set the template to use for rendering HTML data
$config['module_admin_view_template'] = 'tpl_admin_monthly_report_details';
$config['module_generate_function'] = 'generate_module';