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


$config['use_saved_billing'] = array('0'     => lang('select_credit_card_option'),
                                     'new'   => lang('enter_new_credit_card_details'),
                                     'saved' => lang('use_current_credit_card_details'));

$config['position'] = $a = array('left'   => lang('left'),
                                 'center' => lang('center'),
                                 'right'  => lang('right'));

$config['backup_path'] = array(APPPATH => lang('application_folder'),
                               PUBPATH => lang('root_folder'));

$config['operator'] = array('='    => lang('equals'),
                            '>'    => lang('greater_than'),
                            '>='   => lang('greater_than_or_equal'),
                            '<'    => lang('less_than'),
                            '<='   => lang('less_than_or_equal'),
                            'LIKE' => lang('like'));

$config['process_commissions'] = array('0' => lang('no_do_not_process_referral_commissions'),
                                       '1' => lang('yes_process_associated_referral_commissions'));

$config['admin_status'] = array('active'   => lang('active'),
                                'inactive' => lang('inactive'));

$config['production_sandbox'] = array('production' => lang('production'),
                                      'sandbox'    => lang('sandbox'));
$config['blog_comments_module'] = array('0' => lang('no'),
                                        '1' => lang('use_internal_system'),
                                        '2' => lang('use_disqus_comments_plugin'),
                                        '3' => lang('use_facebook_comments_plugin'));

$config['comment_reply_to'] = array('thread' => lang('comment_thread'),
                                    'parent' => lang('user_response'));
$config['mass_payment'] = array('generate_file' => lang('included_in_mass_payment'),
                                'mark_as_paid'  => lang('paid_and_archive_commissions'));

$config['paid_options'] = array('1' => lang('paid'),
                                '0' => lang('unpaid'));

$config['grid_list'] = array('grid' => lang('grid'),
                             'list' => lang('list'));

$config['full_column'] = array('full'   => lang('full'),
                               'column' => lang('column'));

$config['sidebar'] = array('left'  => lang('left'),
                           'right' => lang('right'),
                           'none'  => lang('none'));

$config['admin_home_page_redirect'] = array('dashboard',
                                            'invoices',
                                            'orders',
                                            'blog_posts',
                                            'products',
                                            'members',
                                            'events_calendar',
                                            'forum_topics',
                                            'support_tickets');

$config['payment'] = array('0' => lang('unpaid'),
                           '1' => lang('paid'),
);

$config['active'] = array('1' => lang('active'),
                          '0' => lang('inactive'),
);

$config['published'] = array('0' => lang('draft'),
                             '1' => lang('published'),
);

$config['plus_minus'] = array('+' => '+',
                              '-' => '-',
);

$config['cart_bag'] = array('cart','bag','basket');

$config['approve'] = array('1' => lang('approved'),
                           '0' => lang('disapproved'),
);

$config['products'] = array('active'          => lang('active'),
                            'inactive'        => lang('inactive'),
                            'delete'          => lang('deleted'),
                            'add_featured'    => lang('add_featured'),
                            'remove_featured' => lang('remove_featured'),
                            'add_category'    => lang('add_to_category'),
                            'remove_category' => lang('remove_from_category'),
                            'add_brand'         => lang('add_brand'),
                            'remove_brand'      => lang('remove_brand'),
                            'add_tag'         => lang('add_product_tag'),
                            'remove_tag'      => lang('remove_product_tag'),
);

$config['mark_ticket_priority'] = array('0'      => lang('open'),
                                        '1'      => lang('closed'),
                                        'high'   => lang('high_priority'),
                                        'normal' => lang('normal_priority'),
                                        'low'    => lang('low_priority'),
                                        'admin_id' => lang('assign_to'),
                                        'delete' => lang('deleted'),
);

$config['yes_no'] = array('0' => lang('no'),
                          '1' => lang('yes'),
);

$config['bill_ship_address'] = array('shipping' => lang('shipping_address'),
                                     'billing'  => lang('billing_address'),
);

$config['get_post'] = array('get'  => 'GET',
                            'post' => 'POST',
);

$config['record_limit'] = array(25 => 25, 50 => 50, 100 => 100, 250 => 250);

$config['numbers_50'] = array(50 => 50, 100 => 100, 250 => 250, 500 => 500, 1000 => 1000);

$config['cc_auth_type'] = array('AUTH_CAPTURE' => 'Authorization and Capture',
                                'AUTH_ONLY'    => 'Authorization Only');

$config['forced_matrix_spillover'] = array('none'     => lang('no_sponsor'),
                                           'random'   => lang('random_affiliate'),
                                           'specific' => lang('specific_affiliate'),
);

$config['paper_size'] = array('letter' => 'letter',
                              'legal'  => 'legal',
                              '11x17'  => '11x17');

$config['paper_orientation'] = array('landscape' => 'landscape',
                                     'portrait'  => 'portrait');

$config['numbers_5'] = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5);

$config['affiliate_performance_bonus_required'] = array('commission_amount' => lang('commission_amount'),
                                                        'sales_amount'      => lang('sales_amount'));

$config['cart_commissions'] = array('total_sale'  => lang('total_sale'),
                                    'per_product' => lang('per_product'));

$config['commission_refund_option'] = array('none'    => lang('do_nothing'),
                                            'delete'  => lang('delete'),
                                            'pending' => lang('pending'),
);

$config['affiliate_link_type'] = array('regular'   => lang('regular'),
                                       'subdomain' => lang('subdomain'),
                                       'custom'    => lang('custom'));

$config['affiliate_link_username_id'] = array('username' => lang('username'), 'id' => lang('id'));

$config['video_control_bar'] = array('bottom' => 'bottom', 'over' => 'over', 'none' => 'none');

$config['affiliate_performance_bonus'] = array('group_upgrade'  => lang('group_upgrade'),
                                               'payment_amount' => lang('payment_amount'));

$config['backup_option'] = array('daily'   => lang('daily'), 'weekly' => lang('weekly'),
                                 'monthly' => lang('monthly'));

$config['boolean'] = array('1' => lang('yes'), '0' => lang('no'));

$config['commission_type'] = array('flat' => lang('flat'), 'percent' => lang('percent'));

$config['date_format'] = array('mm/dd/yyyy:m/d/Y:M d Y:%m/%d/%Y' => 'mm/dd/yyyy',
                               'dd/mm/yyyy:d/m/Y:d M Y:%d/%m/%Y' => 'dd/mm/yyyy',
                               'yyyy/mm/dd:Y/m/d:Y d M:%Y/%m/%d' => 'yyyy/dd/mm',
                               //'yyyy/mm/dd' => 'yyyy/mm/dd',
);

$config['image_library'] = array('GD'     => 'GD',
                                 'GD2' => ' GD2',
                                 'ImageMagick' => 'ImageMagick',
                                 'NetPBM' => 'NetPBM');

$config['mailer_type'] = array('php' => 'php', 'smtp' => 'smtp');

$config['ssl_tls'] = array('none' => 'none', 'ssl' => 'ssl', 'tls' => 'tls');

$config['pending_commission'] = array('no_pending'    => lang('pending_no_email'),
                                      'alert_pending' => lang('pending_send_email'),
                                      'no_unpaid'     => lang('unpaid_no_email'),
                                      'alert_unpaid'  => lang('unpaid_send_email'));

$config['address_type'] = array('billing'  => lang('billing_address'),
                                'shipping' => lang('shipping_address'), 'store' => lang('store_address'));

$config['asc_desc'] = array('ASC'  => lang('asc'),
                            'DESC' => lang('desc'),
);

$config['ampm'] = array('am' => lang('AM'), 'pm' => lang('PM'));

$config['menu_link_type'] = array(
	'link'     => lang('link'),
	'dropdown' => lang('dropdown'),
	//'mega'     => lang('mega_menu'), //todo
);

$config['bbcode_tags'] = array( '' => 'allowed_bbcodes',
                                'b' => '[b]...[/b]' . ' - ' . lang('bold'),
                                'i' => '[i]...[/i]' . ' - ' . lang('italicize'),
                                'u' => '[u]...[/u]' . ' - ' . lang('underline'),
                                'quote' => '[quote]...[/quote]' . ' - ' . lang('quote'),
                                'size=' => '[size=16]...[/size]' . ' - ' . lang('text_size'),
                                'color=' => '[color=red]...[/color]' . ' - ' . lang('text_color'),
                                'url' => '[url]...[/url]' . ' - ' . lang('website_url'),
                                'img' => '[img]...[/img]' . ' - ' . lang('image_link'),
								);

$config['module_export_tables'] = array(TBL_MEMBERS,
                                        TBL_PRODUCTS,
                                        TBL_PRODUCTS_DOWNLOADS,
                                        TBL_INVOICES,
                                        TBL_INVOICE_PAYMENTS,
                                        TBL_ORDERS,
                                        TBL_AFFILIATE_COMMISSIONS,
                                        TBL_AFFILIATE_PAYMENTS,
);



//load custom options if needed
if (file_exists(APPPATH . 'config/custom_options.php'))
{
	require_once(APPPATH . 'config/custom_options.php');
}

/* End of file options.php */
/* Location: ./application/config/options.php */