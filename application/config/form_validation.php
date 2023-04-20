<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 * Copyright (c) 2007-2020, JROX Technologies, Inc.
 *
 * This script may be only used and modified in accordance to the license
 * agreement attached (license.txt) except where expressly noted within
 * commented areas of the code body. This copyright notice and the
 * comments above and below must remain intact at all times.  By using this
 * code you agree to indemnify JROX Technologies, Inc, its corporate agents
 * and affiliates from any liability that might arise from its use.
 *
 * Selling the code for this program without prior written consent is
 * expressly forbidden and in violation of Domestic and International
 * copyright laws.
 *
 * @package    eCommerce Suite
 * @author    JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link    https://www.jrox.com
 * @filesource
 */
$config['error_prefix'] = '<div class="error_prefix">';
$config['error_suffix'] = '</div>';

$config['required_input_fields'] = array(
	'affiliate_commissions'         => array('member_id', 'comm_status', 'date', 'sale_amount', 'trans_id'),
	'affiliate_commission_rules'    => array('status', 'end_date', 'sale_type', 'time_limit', 'operator',
	                                         'sale_amount', 'action'),
	'coupon_codes'                  => array('coupon_type', 'status', 'coupon_code', 'coupon_amount', 'start_date',
	                                         'expire_data'),
	'countries'                     => array('country_name', 'country_iso_code_2', 'country_iso_code_3', 'status',
	                                         'sort_order'),
	'currencies'                    => array('name', 'code', 'value', 'symbol_left', 'decimal_places'),
	'dashboard'                     => array('title', 'description', 'icon', 'url', 'sort_order'),
	'discount_groups'               => array('group_name', 'group_description', 'discount_type', 'group_amount',
	                                         'sort_order'),
	'email_follow_ups'              => array('follow_up_name', 'from_name', 'from_email'),
	'email_follow_ups_name'         => array('subject', 'html_body'),
	'email_mailing_lists'           => array('list_name', 'description'),
	'email_templates'               => array('from_name', 'from_email'),
	'email_templates_name'          => array('subject', 'html_body'),
	'events_calendar'               => array('status', 'date', 'start_time', 'end_time', 'start_hour', 'end_hour',
	                                         'start_min', 'end_min', 'start_ampm', 'end_ampm', 'title', 'location'),
	'forms'                         => array('form_id', 'form_name', 'form_description', 'form_method', 'form_processor'),
	'form_fields'                   => array('field_id', 'form_field', 'field_type'),
	'forum_topics_create'           => array('title', 'topic'),
	'forum_add_topic'               => array('title', 'topic', 'member_id'),
	'forum_update_topic'            => array('title', 'topic', 'member_id', 'topic_id'),
	'forum_add_reply'               => array('reply_content', 'topic_id', 'member_id'),
	'forum_update_reply'            => array('reply_content', 'reply_id', 'member_id'),
	'forum_topics_replies'          => array('reply_content'),
	'form_fields_name'              => array('field_name', 'field_description'),
	'gallery'                       => array('gallery_status', 'gallery_name', 'gallery_photo', 'sort_order'),
	'gift_certificates'             => array('status', 'description', 'code', 'from_name', 'from_email', 'to_name',
	                                         'to_email', 'message', 'amount'),
	'invoices_create'               => array('date_purchased', 'due_date', 'customers_primary_email',
	                                         'payment_status_id', 'items', 'billing_address_id', 'member_id'),
	'invoices_items'                => array('invoice_item_name', 'quantity', 'unit_price'),
	'invoices_update'               => array('date_purchased', 'due_date', 'customers_primary_email', 'payment_status_id',
	                                         'items', 'customer_name', 'customer_address_1', 'customer_city',
	                                         'customer_state', 'customer_country', 'customer_postal_code',
	                                         'invoice_number', 'totals', 'invoice_id'),
	'invoice_payments'              => array('method', 'date', 'transaction_id', 'invoice_id', 'amount',
	                                         'description'),
	'languages'                     => array('name', 'code', 'image'),
	'member_credits'                => array('member_id', 'date', 'amount', 'transaction_id'),
	'members_addresses'             => array('fname', 'lname', 'address_1', 'city', 'state', 'country',
	                                         'postal_code'),
	'subscriptions'                 => array('member_id', 'product_id', 'start_date', 'next_due_date', 'product_price',
	                                         'payment_type'),
	'modules'                       => array('module_status', 'module_name', 'module_description'),
	'page_templates'                => array('template_name', 'template_category', 'template_data'),
	'products_create'               => array('product_type'),
	'products_update'               => array('product_id', 'product_sku', 'product_name'),
	'products_name'                 => array('product_name', 'product_overview'),
	'products_downloads'            => array('file_name'),
	'products_downloads_name'       => array('language_id', 'download_name'),
	'products_filters'              => array('filter_name', 'filter_description'),
	'products_specs'                => array('spec_value'),
	'products_attributes'           => array('required', 'prod_att_id', 'attribute_id'),
	'products_discount_groups'      => array('group_id', 'group_amount', 'quantity', 'discount_type', 'points',
	                                         'start_date', 'end_date', 'priority'),
	'products_affiliate_groups'     => array('id'),
	'products_pricing_subscription' => array('amount', 'interval', 'interval_type'),
	'products_attributes_values'    => array('status', 'option_sku', 'price_add', 'price', 'weight_add', 'weight',
	                                         'points_add', 'points', 'inventory'),
	'product_reviews'               => array('product_id', 'member_id', 'ratings', 'title',
	                                         'comment', 'sort_order'),
	'promotion_rules'               => array('rule', 'item_id', 'type', 'operator', 'amount', 'action',
	                                         'start_date', 'end_date', 'promo_amount'),
	'regions_to_zones'              => array('country_id', 'region_id', 'priority'),
	'rewards'                       => array('rule', 'status', 'points', 'start_date', 'end_date'),
	'site_menus'                    => array('menu_link_status', 'menu_link_type', 'menu_link'),
	'site_pages'                    => array('page_content', 'meta_title', 'meta_description', 'meta_keywords'),
	'slide_shows'                   => array('name', 'start_date', 'end_date'),
	'slide_shows_name'              => array('slide_shows'),
	'support_ticket_create'         => array('priority', 'category_id', 'ticket_subject', 'ticket_status'),
	'support_ticket_reply'          => array('ticket_id', 'reply_type', 'reply_content'),
	'support_predefined_replies'    => array('title', 'ticket_subject', 'reply_content'),
	'syatem_pages'                  => array('page_content', 'title'),
	'tracking'                      => array('status', 'name', 'url', 'end_date'),
	'tax_classes'                   => array('class_name', 'class_description'),
	'tax_rates'                     => array('zone_id', 'tax_type', 'tax_rate_name', 'amount_type', 'tax_amount'),
	'tax_rate_rules'                => array('calculation', 'priority', 'tax_rate_id'),
	'suppliers'                     => array('supplier_name', 'supplier_email'),
	'videos'                        => array('video_name', 'video_code'),
	'widgets'                       => array('widget_name', 'widget_type', 'template_code'),
	'zones'                         => array('zone_name', 'zone_description'),
);