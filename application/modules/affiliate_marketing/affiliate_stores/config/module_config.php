<?php
/**
 * Module Name: Affiliate Stores
 * Description: Allow Affiliates to create their own affiliate stores
 */

defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'affiliate_stores'; //for the members area

$config['module_name'] = 'Affiliate Stores'; //when installing the module
$config['module_description'] = 'Allow Affiliates to create their own affiliate stores'; //installs

$config['module_alias'] = 'affiliate_stores'; //for running methods
$config['module_models'] = array('Affiliate_stores_model' => $config['module_alias']); //create an alias for running methods
$config['module_table'] = 'module_affiliate_marketing_affiliate_stores'; //table to store the tools
$config['module_products_table'] = 'module_affiliate_marketing_affiliate_stores_products';

//set the file path for the views template for the public side
$config['module_view_path'] = 'members'; //folder path for rendering the html

//set the template to use for rendering HTML data
$config['module_html_data'] = 'affiliate_stores_view'; //for the html snippet in the members area
$config['module_template'] = 'affiliate_marketing_tools'; //for the members view page (list)

//for admin area management of the module
$config['module_admin_view_template'] = 'tpl_admin_tools_view'; //template for viewing admin side tools
$config['module_view_function_sort_order'] = 'DESC';
$config['module_view_function_sort_column'] = 'id';

//template that shows the admin side tool for configuration
$config['module_admin_create_template'] = 'tpl_admin_tools_manage';
$config['module_admin_update_template'] = 'tpl_admin_tools_manage';
$config['module_admin_settings_template'] = 'tpl_admin_tools_settings';

$config['module_admin_required_validation_fields'] =   array('member_id', 'name');

$config['module_enable_wysiwyg'] = TRUE;