<?php
/**
 * Module Name: Member Monthly Click Stats
 * Description: Monthly Click Statistics for a Specific Affiliate Member
 */

defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'monthly_click_stats';
$config['module_name'] = 'Member Monthly Click Stats';
$config['module_description'] = 'Monthly Affiliate Click Statistics';
$config['module_models'] = array('Member_month_click_stats_model' => 'module');

//set the file path for the views template
$config['module_view_path'] = 'members';

//set the template to use for rendering HTML data
$config['module_html_data'] = 'member_month_click_stats';
$config['module_template'] = 'monthly_report_details';
$config['module_generate_function'] = 'generate_module';
