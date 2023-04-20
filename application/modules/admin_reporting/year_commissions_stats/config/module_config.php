<?php defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'yearly_commission_stats';
$config['module_name'] = 'Yearly Commission Stats';
$config['module_description'] = 'Commission Stats for Year';
$config['module_model_alias'] = 'year_comm_stats';
$config['module_models'] = array('Year_commissions_stats_model' => $config['module_model_alias']);
$config['chart_title'] = 'commissions_generated_per_month';
$config['x_axis'] = 'amount';

//set the file path for the views template
$config['module_view_path'] = 'admin';

//set the template to use for rendering HTML data
$config['module_admin_view_template'] = 'tpl_admin_yearly_report_details';
$config['module_generate_function'] = 'generate_module';