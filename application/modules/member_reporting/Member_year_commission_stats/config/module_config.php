<?php
/**
 * Module Name: Member Yearly Commission Stats
 * Description: Yearly Commission Statistics for a Specific Affiliate Member
 */

defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'yearly_commission_stats';
$config['module_name'] = 'Member Yearly Commission Stats';
$config['module_description'] = 'Yearly Commission Statistics for a Specific Affiliate Member';
$config['module_models'] = array('Member_year_commission_stats_model' => 'module');

//set the file path for the views template
$config['module_view_path'] = 'members';

//set the template to use for rendering HTML data
$config['module_html_data'] = 'member_year_commission_stats';
$config['module_template'] = 'yearly_report_details';
$config['module_generate_function'] = 'generate_module';
