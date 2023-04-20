<?php defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'yearly_support_ticket_stats';
$config['module_name'] = 'Yearly Support Tickets Stats';
$config['module_description'] = 'Support Tickets Stats for the Year';
$config['module_model_alias'] = 'year_support_tickets';
$config['module_models'] = array('Year_support_tickets_stats_model' => $config['module_model_alias']);
$config['chart_title'] = 'support_tickets_submitted_per_month';
$config['x_axis'] = 'amount';

//set the file path for the views template
$config['module_view_path'] = 'admin';

//set the template to use for rendering HTML data
$config['module_admin_view_template'] = 'tpl_admin_yearly_report_details';
$config['module_generate_function'] = 'generate_module';