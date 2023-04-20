<?php
/**
 * Module Name: General Export Module
 * Description: Export data from members, products, orders, invoices, and commissions tables
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$config['module_alias'] = 'general';
$config['module_name'] = 'General Export';
$config['module_description'] = 'Export data from members, products, orders, invoices, and commissions tables';
$config['module_config_template'] = 'tpl_admin_data_export_manage'; //template for uploading the file
$config['module_do_export_template'] = 'tpl_admin_do_export';
$config['module_export_memory_limit'] = '256M';
$config['module_export_file_name'] = 'export-' . date('m-d-Y');
$config['module_export_time_limit'] = '360';
$config['module_column_sort_order'] = 'DESC';

$config['module_models'] = array('General_export_model'        => $config['module_alias'],
                                 'Members_model'               => 'mem',
                                 'Products_attributes_model'   => 'att',
                                 'Products_categories_model'   => 'cat',
                                 'Products_tags_model'         => 'tag',
                                 'Products_model'              => 'prod',
                                 'Invoices_model'              => 'inv',
                                 'Affiliate_commissions_model' => 'comm',
                                 'Affiliate_payments_model'    => 'pay',
);
$config['module_export_sort_column'] = array(TBL_MEMBERS               => 'date',
                                             TBL_PRODUCTS              => 'date_added',
                                             TBL_PRODUCTS_DOWNLOADS    => 'p.download_id',
                                             TBL_INVOICES              => 'p.invoice_id',
                                             TBL_ORDERS                => 'p.order_id',
                                             TBL_AFFILIATE_COMMISSIONS => 'comm_id',
                                             TBL_AFFILIATE_PAYMENTS    => 'payment_date',
                                             TBL_INVOICE_PAYMENTS      => 'date',
);

$config['module_export_exclude_fields'] = array(TBL_MEMBERS               => array('last_login_date',
                                                                                   'id',
                                                                                   'updated_on',
                                                                                   'profile_id',
                                                                                   'alert_id'),
                                                TBL_PRODUCTS_DOWNLOADS    => array('date_modified',
                                                                                   'dw_name_id',
                                                                                   'language_id'),
                                                TBL_PRODUCTS              => array('date_expires',
                                                                                   'modified',
                                                                                   'date_available',
                                                                                   'product_views',
                                                                                   'product_page_template',
                                                                                   'prod_name_id'),
                                                TBL_INVOICES              => array('date_modified',
                                                                                   'tracking_data'),
                                                TBL_ORDERS                => array('tracking_data',
                                                                                   'parent_order',
                                                                                   'coupon_data',
                                                                                   'due_date',
                                                                                   'cart_data',
                                                                                   'currency_id',
                                                                                   'currency_value',
                                                                                   'order_data',
                                                                                   'user_agent',
                                                                                   'shipping_data',
                                                                                   'osid',
                                                                                   'label_url',
                                                                                   'api_id',
                                                                                   'date_modified'),
                                                TBL_AFFILIATE_COMMISSIONS => array('performance_paid',
                                                                                   'tool_type',
                                                                                   'tool_id',
                                                                                   'payment_id',
                                                                                   'email_sent'),
                                                TBL_AFFILIATE_PAYMENTS    => array('transaction_info'),
                                                TBL_INVOICE_PAYMENTS      => array('debug_info'),
);


