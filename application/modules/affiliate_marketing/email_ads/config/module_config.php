<?php
/**
 * Module Name: Email Ads
 * Description: Email ads module for sharing your email ads via affiliate marketing
 */

defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'affiliate_email_ads'; //for the members area

$config['module_name'] = 'Affiliate Email Ads'; //when installing the module
$config['module_description'] = 'Email ads module for sharing your email ads via affiliate marketing';

$config['module_alias'] = 'email ads'; //for running methods
$config['module_models'] = array('Email_ads_model' => $config['module_alias']); //create an alias for running methods
$config['module_table'] = 'module_affiliate_marketing_email_ads'; //table to store the tools

//set the file path for the views template for the public side
$config['module_view_path'] = 'members'; //folder path for rendering the email

//set the template to use for rendering HTML data
$config['module_html_data'] = 'email_ads_view'; //for the email snippet in the members area
$config['module_template'] = 'affiliate_marketing_tools'; //for the members view page (list)

//for admin area management of the module
$config['module_admin_view_template'] = 'tpl_admin_tools_view'; //template for viewing admin side tools
$config['module_view_function_sort_order'] = 'ASC';
$config['module_view_function_sort_column'] = 'sort_order';

//template that shows the admin side tool for configuration
$config['module_admin_create_template'] = 'tpl_admin_tools_manage';
$config['module_admin_update_template'] = 'tpl_admin_tools_manage';

$config['module_admin_required_validation_fields'] =  array('status', 'name', 'email_ad_title', 'email_ad_body');

$config['module_enable_wysiwyg'] = TRUE;
