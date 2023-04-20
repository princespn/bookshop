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

$config['db_sort_order'] = array(
	TBL_ADMIN_USERS                => array(
		'column' => 'admin_id',
		'order'  => 'ASC',
	),
	TBL_AFFILIATE_GROUPS           => array(
		'column' => 'tier',
		'order'  => 'ASC',
	),
	TBL_AFFILIATE_TRAFFIC          => array(
		'column' => 'traffic_id',
		'order'  => 'DESC',
	),
	TBL_AFFILIATE_COMMISSIONS      => array(
		'column' => 'comm_id',
		'order'  => 'DESC',
	),
	TBL_AFFILIATE_COMMISSION_RULES => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_AFFILIATE_PAYMENTS         => array(
		'column' => 'aff_pay_id',
		'order'  => 'DESC',
	),
	TBL_BLOG_COMMENTS              => array(
		'column' => 'id',
		'order'  => 'DESC',
	),
	TBL_BLOG_POSTS                 => array(
		'column' => 'date_published',
		'order'  => 'DESC',
	),
	TBL_BLOG_TAGS                  => array(
		'column' => 'count',
		'order'  => 'DESC',
	),
	TBL_BLOG_CATEGORIES            => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_BRANDS                     => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_BLOG_GROUPS                => array(
		'column' => 'group_name',
		'order'  => 'ASC',
	),
	TBL_COUNTRIES                  => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_CURRENCIES                 => array(
		'column' => 'currency_id',
		'order'  => 'ASC',
	),
	TBL_COUPONS                    => array(
		'column' => 'start_date',
		'order'  => 'DESC',
	),
	TBL_DISCOUNT_GROUPS            => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_EVENTS_CALENDAR            => array(
		'column' => 'date',
		'order'  => 'ASC',
	),
	TBL_EMAIL_TEMPLATES            => array(
		'column' => 'template_name',
		'order'  => 'ASC',
	),
	TBL_EMAIL_MAILING_LISTS        => array(
		'column' => 'list_id',
		'order'  => 'ASC',
	),
	TBL_EMAIL_FOLLOW_UPS           => array(
		'column' => 'sequence',
		'order'  => 'ASC',
	),
	TBL_EMAIL_QUEUE                => array(
		'column' => 'id',
		'order'  => 'DESC',
	),
	TBL_EMAIL_ARCHIVE              => array(
		'column' => 'id',
		'order'  => 'DESC',
	),
	TBL_FORUM_CATEGORIES           => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_FORUM_TOPICS               => array(
		'column' => 'date_modified',
		'order'  => 'DESC',
	),
	TBL_FORUM_TOPICS_REPLIES       => array(
		'column' => 'date',
		'order'  => 'ASC',
	),
	'forum_home_page'              => array(
		'column' => 'topic_id',
		'order'  => 'DESC',
	),

	TBL_FAQ     => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_GALLERY => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),

	TBL_INVOICE_PAYMENTS           => array(
		'column' => 'invoice_payment_id',
		'order'  => 'DESC',
	),
	TBL_INVOICES                   => array(
		'column' => 'invoice_id',
		'order'  => 'DESC',
	),
	TBL_INVOICE_ITEMS              => array(
		'column' => 'invoice_item_id',
		'order'  => 'ASC',
	),
	TBL_KB_ARTICLES                => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_KB_CATEGORIES              => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_LANGUAGES                  => array(
		'column' => 'language_id',
		'order'  => 'ASC',
	),
	TBL_MEMBERS                    => array(
		'column' => 'members.member_id',
		'order'  => 'DESC',
	),
	'member_reporting'             => array(
		'column' => 'module_name',
		'order'  => 'ASC',
	),
	TBL_MEMBERS_CREDITS            => array(
		'column' => 'mcr_id',
		'order'  => 'DESC',
	),
	TBL_MEMBERS_DOWNLOADS          => array(
		'column' => 'd_id',
		'order'  => 'DESC',
	),
	TBL_MEMBERS_EMAIL_MAILING_LIST => array(
		'column' => 'eml_id',
		'order'  => 'DESC',
	),
	TBL_MEMBERS_SUBSCRIPTIONS      => array(
		'column' => 'sub_id',
		'order'  => 'DESC',
	),
	TBL_MODULES                    => array(
		'column' => 'module_sort_order',
		'order'  => 'ASC',
	),
	TBL_ORDERS                     => array(
		'column' => 'order_id',
		'order'  => 'DESC',
	),
	TBL_ORDERS_ITEMS               => array(
		'column' => 'order_item_id',
		'order'  => 'DESC',
	),
	TBL_ORDERS_GIFT_CERTIFICATES   => array(
		'column' => 'cert_id',
		'order'  => 'DESC',
	),
	TBL_ORDERS_ITEM_ATTRIBUTES     => array(
		'column' => 'opaid',
		'order'  => 'DESC',
	),
	'public_blog_comments'         => array(
		'column' => 'date',
		'order'  => 'ASC',
	),
	TBL_PRODUCTS_CATEGORIES        => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_PRODUCTS                   => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),

	'featured_products' => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),

	'latest_products' => array(
		'column' => 'date_added',
		'order'  => 'DESC',
	),

	TBL_PRODUCTS_SPECIFICATIONS => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_PRODUCTS_ATTRIBUTES     => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_PRODUCTS_REVIEWS        => array(
		'column' => 'id',
		'order'  => 'DESC',
	),
	TBL_PRODUCTS_DOWNLOADS      => array(
		'column' => 'download_name',
		'order'  => 'ASC',
	),
	TBL_PROMOTIONAL_RULES       => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_PRODUCTS_TAGS           => array(
		'column' => 'count',
		'order'  => 'DESC',
	),
	TBL_REGIONS                 => array(
		'column' => 'country_name',
		'order'  => 'ASC',
	),
	TBL_REPORT_ARCHIVE          => array(
		'column' => 'report_date',
		'order'  => 'DESC',
	),
	TBL_REWARDS                 => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_REWARDS_HISTORY         => array(
		'column' => 'points_id',
		'order'  => 'DESC',
	),
	TBL_SITE_PAGES              => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_SYSTEM_PAGES            => array(
		'column' => 'p.page_id',
		'order'  => 'ASC',
	),
	TBL_SLIDE_SHOWS             => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_SUPPORT_TICKETS         => array(
		'column' => 'p.ticket_id',
		'order'  => 'DESC',
	),
	TBL_SUPPORT_TICKETS_REPLIES => array(
		'column' => 'date',
		'order'  => 'ASC',
	),
	TBL_SUPPORT_CATEGORIES      => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_TRACKING                => array(
		'column' => 'tracking_id',
		'order'  => 'DESC',
	),
	TBL_TRACKING_REFERRALS      => array(
		'column' => 'id',
		'order'  => 'DESC',
	),
	TBL_TRANSACTIONS            => array(
		'column' => 'id',
		'order'  => 'DESC',
	),
	TBL_SUPPLIERS               => array(
		'column' => 'supplier_name',
		'order'  => 'ASC',
	),
	TBL_WIDGETS                 => array(
		'column' => 'widget_name',
		'order'  => 'ASC',
	),
	TBL_VIDEOS                  => array(
		'column' => 'sort_order',
		'order'  => 'ASC',
	),
	TBL_WISH_LISTS              => array(
		'column' => 'wish_list_id',
		'order'  => 'ASC',
	),
);

/* End of file db_sort.php */
/* Location: ./application/config/db_sort.php */