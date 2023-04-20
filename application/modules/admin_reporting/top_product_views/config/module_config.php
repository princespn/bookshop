<?php defined('BASEPATH') OR exit('No direct script access allowed');

//initialize the models required for this module
$config['module_title'] = 'top_product_views';
$config['module_name'] = 'Top Product Views';
$config['module_description'] = 'Top Product Views by Count';
$config['module_model_alias'] = 'top_prod_views';
$config['module_models'] = array('Top_product_views_model' => $config['module_model_alias']);
$config['chart_title'] = 'most_viewed_products';
$config['x_axis'] = 'views';
$config['query_limit'] = '25';

//set the file path for the views template
$config['module_view_path'] = 'admin';

//set the template to use for rendering HTML data
$config['module_admin_view_template'] = 'tpl_admin_top_product_views';
$config['module_generate_function'] = 'generate_module';