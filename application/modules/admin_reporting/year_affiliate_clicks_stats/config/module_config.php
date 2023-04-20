<?php defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'yearly_affiliate_click_stats';
$config['module_name'] = 'Yearly Affiliate Clicks Stats';
$config['module_description'] = 'Affiliate Click Stats for the Year';
$config['module_model_alias'] = 'year_aff_click';
$config['module_models'] = array('Year_affiliate_clicks_stats_model' => $config['module_model_alias']);
$config['chart_title'] = 'affiliate_clicks_generated_per_month';
$config['x_axis'] = 'amount';

//set the file path for the views template
$config['module_view_path'] = 'admin';

//set the template to use for rendering HTML data
$config['module_admin_view_template'] = 'tpl_admin_yearly_report_details';
$config['module_generate_function'] = 'generate_module';