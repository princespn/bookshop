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
 * @package      eCommerce Suite
 * @author       JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link         https://www.jrox.com
 * @filesource
 */

$config['admin_pagination_links'] = 4;
$config['member_pagination_links'] = 4;
$config['member_marketing_tool_ext'] = 'png';

//set template cache path
$config['tpl_cache_path'] = APPPATH . 'cache/tpl';
$config['tpl_strict_variables'] = FALSE;
$config['tpl_enable_autoescape'] = FALSE;

//set the cache driver type; backup is always 'file'
$config['cache_driver_type'] = 'file';

//template tags
$config['template_tag_left'] = '{{';
$config['template_tag_right'] = '}}';

//enable transactions
$config['enable_innodb_transactions'] = TRUE;

//disable table counts
$config['disable_sql_category_count'] = FALSE;

$config['enable_db_debugging'] = FALSE; //set to TRUE to enable profiler

//username password field
//minimum username set by $sts_affiliate_min_username_length
$config['max_member_username_length'] = 30;
$config['min_member_password_length'] = 7;
$config['default_member_password_length'] = 8;
$config['max_member_password_length'] = 30;

$config['min_admin_username_length'] = 6;
$config['max_admin_username_length'] = 30;
$config['min_admin_password_length'] = 6;
$config['max_admin_password_length'] = 30;

$config['members_area'] = MEMBERS_ROUTE;

$config['encrypt_cart_uploads'] = FALSE;
$config['encrypt_content_upload_images'] = FALSE;

$config['default_set_ticket_status'] = 'answered';

$config['allow_programming_codes_in_text'] = FALSE;

$config['enable_ajax_security'] = TRUE;

//affiliate
$config['enable_no_follow_links'] = FALSE; //se to false if you don't want rel="nofollow" on your affiliate links

//send order emails immediately after payment
$config['send_checkout_emails_immediately'] = TRUE;

$config['import_field_separator'] = ','; //for separating specific column values in each import field
$config['blog_related_posts_limit'] = 4;
$config['is_affiliate_icon'] = 'A';
$config['default_registration_discount_group'] = 1;
$config['default_kb_category_id'] = 1;
$config['default_forum_category_id'] = 1;
$config['default_support_category_id'] = 1;
$config['default_supplier_id'] = 1;
$config['default_admin_group_id'] = 1;
$config['default_product_group_id'] = 1;
$config['default_blog_category_id'] = 1;
$config['default_site_address'] = 1;
$config['default_layout_menu'] = 1;
$config['default_total_cross_sells'] = 6;
$config['default_product_grid_size'] = 4;

$config['default_order_attribute_info'] = array('option_id','option_sku', 'attribute_type', 'weight', 'points');
$config['stop_word_filter'] = array('if', 'of', 'and', 'the', 'or', 'is', 'are');

$config['chart_bg_color'] = '#FFFFFF';
$config['chart_graph_colors'] = '#6d94bf';
$config['chart_graph_colors2'] = '#777777';

$config['lazy_load_images'] = 0;

$config['currency_rate_server'] = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

$config['max_backup_files'] = '30';
$config['default_time_format'] = 'm-d-Y h:i:s A';

$config['timer_modal_cookie'] = 'modalTimerCheck';

$config['dbr_total_report_years'] = '3';

//set the default uri segment offset for db
$config['db_segment'] = 4;
$config['site_db_segment'] = 4;

$config['product_types'] = array('general',
                                 'third_party',
);
//axis range fo reports
$config['dbr_y_axis_range'] = '50';

//data input to be filtered
$config['dbi_filter'] = array('passconf', 'redir_button');

//to remove certain elements in the content
$config['dbi_content_filter'] = array('&nbsp;' => '');

//arrays not to run through xss filterk
$config['dbi_arrays'] = array();

$config['db_disable_cache_pages'] = array('admin'  => array('Admin_users::view', 'Admin_groups::view'),
                                          'public' => array('view'),
);

$config['default_lang_files'] = array('admin',
                                      'affiliate',
                                      'content',
                                      'email',
                                      'global',
                                      'install',
                                      'layout',
                                      'localization',
                                      'members',
                                      'orders',
                                      'products',
                                      'promotions',
                                      'settings',
                                      'support',
                                      'system');

$config['dashboard_latest_data'] = array('latest_signups', 'latest_commissions', 'latest_invoices', 'latest_tickets');

$config['default_image_types'] = array('jpg', 'jpeg', 'png', 'gif');

$config['disable_require_user_login'] = array('register', 'login', 'site_map');

$config['name_tables'] = array('blog_categories', 'blog_posts', 'blog_posts_revisions', 'brands', 'email_follow_ups',
                               'email_templates', 'faq', 'form_fields', 'forum_categories', 'kb_articles', 'kb_categories',
                               'products_attribute_options', 'products_attributes', 'products_categories',
                               'products_downloads', 'products', 'products_specifications',
                               'products_to_specifications', 'site_pages', 'site_menus_links', 'site_menus',
                               'slide_shows', 'support_categories', 'system_pages');

$config['templates_not_for_editing'] = array('default_site_builder.tpl',
                                             'site_builder.tpl',
                                             'sample.tpl',
                                             'blocked.tpl',
                                             'site_map_index.tpl',
                                             'site_map_xml.tpl',
                                             'cp.tpl',
                                             'privacy.tpl',
                                             'bootstrap.tpl',
                                             'test_payments/test_gateway_form.tpl');

$config['name_ids'] = array('brand_name_id', 'att_option_name_id', 'fu_name_id', 'template_name_id', 'field_name_id',
                            'att_name_id', 'att_option_name_id', 'cat_name_id', 'dw_name_id', 'spec_name_id', 'prod_spec_id',
                            'link_name_id', 'menu_name_id', 'slide_name_id', 'page_name_id', 'product_name_id', 'name_page_id');

$config['link_search_ids'] = array('admin_id', 'comm_id', 'member_id', 'aff_pay_id', 'product_id', 'blog_id', 'kb_id');

$config['exclude_fields'] = array('apassword', 'password');

$config['section_sitebuilder_basic_js'] = array(1,2,3,51);

$config['restricted_usernames'] = array(ADMIN_LOGIN, ADMIN_ROUTE, SITE_BUILDER, 'store', 'members', 'webmaster', 'checkout' );

$config['regular_fonts'] = array('System UI', 'Arial', 'Courier', 'Georgia', 'MonoSpace', 'Sans Serif', 'Serif');

$config['google_fonts'] = array('Abel', 'Abril Fatface', 'Advent Pro', 'Aladin', 'Allerta Stencil', 'Allura', 'Architects Daughter',
                                'Aubrey', 'Anton', 'Bevan', 'Bowlby One SC', 'Chewy', ' Cinzel' ,'Comfortaa', 'Diplomata SC', 'Encode Sans',
                                'Forum', 'Gruppo', 'Happy Monkey', 'Iceland', 'Julee', 'Junge', 'Kaushan Script', 'Kite One', 'Lato',
                                'Lobster', 'Landrian Shadow', 'Macondo', 'Martel', 'Maven Pro', 'Merriweather', 'Monoton', 'Montserrat',
                                'Muli', 'Neuton', 'Nixie One', 'Open Sans', 'Oswald', 'Oxygen', 'Passion One', 'Pathway Gothic One',
                                'Petit Formal Script', 'Philosopher', 'Playfair Display', 'Poiret One', 'Poppins', 'PT Serif',
                                'Quattrocento Sans', 'Quicksand', 'Raleway', 'Ribeye Marrow', 'Righteous', 'Roboto', 'Rouge Script',
                                'Sacrement', 'Sanchez', 'Seymour One', 'Shadows Into Light Two', 'Source Code Pro', 'Source Sans Pro',
                                'Special Elite', 'Squada One', 'Stint Ultra Expanded', 'Tenor Sans', 'Ubuntu Mono', 'Vast Shadow',
                                'Viga', 'Voltaire'
);

$config['default_form_ids'] = array('1' => 'register',
                                    '2' => 'checkout/cart',
                                    '3' => 'contact',
);

$config['default_required_fields'] = array('fname',
                                           'primary_email');

$config['db_select_page_rows'] = array(20, 50, 100, 250);

//array for graph colors
$config['report_graph_colors'] = array('#DB887F', '#3F5C9A');

$config['query_type_filter'] = array('order',
                                     'column',
                                     'table',
                                     'type_id',
                                     'group_id',
                                     'q',
                                     'preview',
                                     'mass_edit');

$config['recurring_interval_types'] = array('month',
                                            'day',
                                            'week',
                                            'year',
);

$config['random_username_types'] = array('alpha_numeric',
                                         'random_numeric',
                                         'sequential_numeric',
);

$config['ticket_status_options'] = array('new',
                                         'answered',
                                         'client_reply',
                                         'on_hold',
                                         'in_progress',
);

$config['ticket_priorities'] = array('low',
                                     'normal',
                                     'high',
);

$config['affiliate_commission_statuses'] = array('pending',
                                                 'unpaid',
                                                 'paid',
);



$config['attribute_types'] = array('text',
                                   'textarea',
                                   'select',
                                   'radio',
                                   'checkbox',
                                   'file',
                                   'image',
);

$config['input_types'] = array('text',
                               'textarea',
                               'select',
                               'radio',
                               'checkbox',
                               'file',
);

$config['form_field_types'] = array('text',
                                    'textarea',
                                    'select',
                                    'radio',
                                    'checkbox',
                                    'date',
                                    'hidden',
                                    'password',
);

$config['sub_forms'] = array('billing',
                             'shipping',
                             'payment',
);

$config['email_providers'] = array('phpmailer',
                                   'sendgrid',
);

$config['tax_types'] = array('sales',
                             'shipping',
);

$config['widget_categories'] = array('51'  => 'modules',
                                     '2'  => 'content_only',
                                     '3'  => 'images_with_content',
                                     '4'  => 'images_only',
                                     '5'  => 'list',
                                     '6'  => 'separators',
                                     '7'  => 'video',
                                     '8'  => 'dynamic_data',
                                     '9'  => 'background_image_content',
                                     '10' => 'form_content',
);

$config['options_mass_update_members'] = array('active',
                                               'inactive',
                                               'delete',
                                               'set_blog_group',
                                               'set_discount_group',
                                               'set_affiliate_group',
                                               'activate_affiliate',
                                               'deactivate_affiliate',
                                               'add_mailing_list',
                                               'remove_mailing_list',
);

$config['form_processor'] = array('email',
                                  'page',
                                  //'plugin', //todo
                                  //'module'
);

$config['rule_sale_types'] = array('amount_of_commission',
                                   'amount_of_sale',
                                   'total_amount_of_commissions',
                                   'total_amount_of_sales',
                                   'total_amount_of_referrals',
                                   'total_amount_of_clicks',
);

$config['search_display_output'] = array('web_page', 'csv_file');

$config['rule_time_limit'] = array('all_time',
                                   'current_month',
                                   'current_year',
                                   'last_month',
                                   'last_year',
);

$config['rule_operator'] = array('greater_than',
                                 'less_than',
                                 'equal_to',
);

$config['promo_rules'] = array('per_item',
                               //'cart_amount', //todo
);

$config['promo_rule_types'] = array('item_quantity',
                                    'total_item_price',
);
$config['promo_rule_actions'] = array('special_offer',
                                      'quantity_discount',
);

$config['flat_percent'] = array('flat',
                                'percent',
);

$config['rule_actions'] = array('issue_bonus_commission',
                                'assign_affiliate_group',
                                'issue_reward_points',
);

$config['reward_types'] = array('reward_user_account_registration',
                                'reward_user_birthday',
                                'reward_product_review',
                                'reward_blog_comment',
                                'reward_wish_list',
);

$config['site_maps'] = array('site_map_index',
                             'product',
                             'product_categories',
                             'blog',
                             'pages',
                             'kb',
);

$config['file_delimiters'] = array('comma',
                                   'tab',
                                   'semicolon',
                                   'pipe');

$config['dashboard_templates'] = array('dashboard_default',
                                       'dashboard_no_columns');



$config['dashboard_items_total'] = array('total_users', 'total_commissions', 'total_sales', 'total_tickets',
                                         'daily_users', 'daily_commissions', 'daily_sales', 'daily_tickets');

//for replacing certain words in the language file automatically
/*
$config['dbi_lang_filter'] = array('affiliate' => 'consultant',
									'contact' => 'consultant',
								);
*/

//$config['chart_graph_colors'] = array('#ee5f5b', '#1F6AAA');
//$config['chart_graph_colors2'] = array('#cccccc', '#dddddd');
//$config['chart_bg_color'] = 'transparent';
//$config['chart_height'] = '300';
//$config['chart_width'] = '100%';

/* End of file custom.php */
/* Location: ./application/config/custom.php */