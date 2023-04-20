<?php
/**
 * Module Name: Import Product Downloads
 * Description: Mass Import Your Downloadable Files into the Database
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'download';
$config['module_name'] = 'Import Downloads';
$config['module_description'] = 'Mass Import Your Downloadable Files into the Database';
$config['module_models'] = array('Import_product_downloads_model' => $config['module_alias'],
                                 'Products_downloads_model'       => 'prod');
$config['module_config_template'] = 'tpl_admin_data_import_manage'; //template for uploading the file
$config['module_map_fields_template'] = 'tpl_admin_data_import_map_fields'; //template for mapping fields
$config['module_import_tables'] = array(TBL_PRODUCTS_DOWNLOADS,
                                        TBL_PRODUCTS_DOWNLOADS_NAME,
);
$config['module_exclude_keys'] = array(TBL_PRODUCTS . '.date_modified');
$config['module_import_memory_limit'] = '256M';
$config['module_import_time_limit'] = '360';