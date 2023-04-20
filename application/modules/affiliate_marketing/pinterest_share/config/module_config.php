<?php
/**
 * Module Name: Pinterest Affiliate Link Sharing
 * Description: Pinterest module for sharing affiliate links and images via Pinterest
 */

defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'pinterest_share'; //for the members area

$config['module_name'] = 'Pinterest Affiliate Link Sharing'; //when installing the module
$config['module_description'] = 'Pinterest module for sharing affiliate links and images via Pinterest'; //installs

$config['module_alias'] = 'fb'; //for running methods
$config['module_models'] = array('Pinterest_share_model' => $config['module_alias']); //create an alias for running methods
$config['module_table'] = 'module_affiliate_marketing_pinterest_share'; //table to store the tools

//set the file path for the views template for the public side
$config['module_view_path'] = 'members'; //folder path for rendering the html

//set the template to use for rendering HTML data
$config['module_html_data'] = 'pinterest_share_view'; //for the html snippet in the members area
$config['module_template'] = 'affiliate_marketing_tools'; //for the members view page (list)

//for admin area management of the module
$config['module_admin_view_template'] = 'tpl_admin_tools_view'; //template for viewing admin side tools
$config['module_view_function_sort_order'] = 'ASC';
$config['module_view_function_sort_column'] = 'sort_order';

//template that shows the admin side tool for configuration
$config['module_admin_create_template'] = 'tpl_admin_tools_manage';
$config['module_admin_update_template'] = 'tpl_admin_tools_manage';
$config['module_admin_settings_template'] = 'tpl_admin_tools_settings';

$config['module_admin_required_validation_fields'] = array('status', 'name', 'link_text', 'image_file_name');
