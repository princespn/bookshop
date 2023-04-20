<?php
/**
 * Module Name: Import Products
 * Description: Mass Import Your Products into the Database
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'products';
$config['module_name'] = 'Import Products';
$config['module_description'] = 'Mass Import Your Products into the Database';
$config['module_models'] = array('Import_products_model'     => $config['module_alias'],
                                 'Products_attributes_model' => 'att',
                                 'Products_categories_model' => 'cat',
                                 'Products_tags_model'       => 'tag',
                                 'Products_model'            => 'prod');
$config['module_config_template'] = 'tpl_admin_data_import_manage'; //template for uploading the file
$config['module_map_fields_template'] = 'tpl_admin_data_import_map_fields'; //template for mapping fields
$config['module_import_tables'] = array(TBL_PRODUCTS,
                                        TBL_PRODUCTS_NAME,
                                        TBL_PRODUCTS_PHOTOS,
                                        TBL_PRODUCTS_TO_AFF_GROUPS,
                                        TBL_PRODUCTS_TO_ATTRIBUTES,
                                        TBL_PRODUCTS_TO_CATEGORIES,
                                        TBL_PRODUCTS_TO_DISC_GROUPS,
                                        TBL_PRODUCTS_TO_PRICING,
                                        TBL_PRODUCTS_TO_TAGS,
);
$config['module_exclude_keys'] = array(TBL_PRODUCTS . '.date_added',
                                       TBL_PRODUCTS . '.date_expires',
                                       TBL_PRODUCTS . '.modified',
                                       TBL_PRODUCTS . '.date_available',
                                       TBL_PRODUCTS . '.product_views',
                                       TBL_PRODUCTS . '.product_page_template',
                                       TBL_PRODUCTS_NAME . '.product_name_id',
                                       TBL_PRODUCTS_NAME . '.product_id',
                                       TBL_PRODUCTS_PHOTOS . '.photo_id',
                                       TBL_PRODUCTS_PHOTOS . '.product_id',
                                       TBL_PRODUCTS_TO_AFF_GROUPS . '.product_id',
                                       TBL_PRODUCTS_TO_ATTRIBUTES . '.prod_att_id',
                                       TBL_PRODUCTS_TO_ATTRIBUTES . '.product_id',
                                       TBL_PRODUCTS_TO_CATEGORIES . '.prod_cat_id',
                                       TBL_PRODUCTS_TO_CATEGORIES . '.product_id',
                                       TBL_PRODUCTS_TO_DISC_GROUPS . '.id',
                                       TBL_PRODUCTS_TO_DISC_GROUPS . '.product_id',
                                       TBL_PRODUCTS_TO_PRICING . '.prod_price_id',
                                       TBL_PRODUCTS_TO_PRICING . '.product_id',
                                       TBL_PRODUCTS_TO_TAGS . '.prod_tag_id',
                                       TBL_PRODUCTS_TO_TAGS . '.product_id',
);
$config['module_import_memory_limit'] = '256M';
$config['module_import_time_limit'] = '360';