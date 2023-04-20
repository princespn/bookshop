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

//List of API models and config for use in API calls.

$config['api_class_alias'] = array(
	'members'               => 'mem',
	'products'              => 'prod',
	'invoices'              => 'invoices',
	'orders'                => 'orders',
	'affiliate_commissions' => 'comm',
	'affiliate_payments'    => 'pay',
	'brands'                => 'brands',
	'product_categories'    => 'cat',
	'kb_articles'           => 'kb',
	'blog_posts'            => 'blog',
	'faq'                   => 'faq',
);

$config['api_load_models'] = array(
	'members'               => array(
		'members'             => 'mem',
		'affiliate_groups'    => 'aff_group',
		'discount_groups'     => 'disc_group',
		'blog_groups'         => 'blog_group',
		'email_mailing_lists' => 'lists',
		'regions'             => 'regions',
		'forms'               => 'form',
	),
	'products'              => array(
		'products'                => 'prod',
		'affiliate_groups'        => 'aff_group',
		'discount_groups'         => 'disc_group',
		'products_attributes'     => 'att',
		'products_categories'     => 'cat',
		'products_specifications' => 'specs',
		'tax_classes'             => 'tax',
	),
	'invoices'              => array(
		'invoices'              => 'invoices',
		'members'               => 'mem',
		'affiliate_commissions' => 'comm',
		'email_mailing_lists'   => 'lists',
	),
	'orders'                => array(
		'products'                => 'prod',
		'affiliate_groups'        => 'aff_group',
		'discount_groups'         => 'disc_group',
		'products_attributes'     => 'att',
		'products_categories'     => 'cat',
		'products_specifications' => 'specs',
		'tax_classes'             => 'tax',
		'orders'                  => 'orders',
		'members'                 => 'mem',
		'cart'                    => 'cart',
		'uploads'                 => 'uploads',
		'shipping'                => 'ship',
		'coupons'                 => 'coupon',
		'email_mailing_lists'     => 'lists',
		'forms'                   => 'form',
		'modules'                 => 'mod',
		'invoices'                => 'invoices',
		'affiliate_downline'      => 'downline',
		'affiliate_commissions'   => 'comm',
	),
	'affiliate_commissions' => array(
		'invoices'              => 'invoices',
		'affiliate_downline'    => 'downline',
		'members'               => 'mem',
		'affiliate_groups'      => 'aff_group',
		'discount_groups'       => 'disc_group',
		'email_mailing_lists'   => 'lists',
		'affiliate_commissions' => 'comm',
	),
	'brands'                => array(
		'brands' => 'brands',
	),
	'products_categories'   => array(
		'products_categories' => 'cat',
	),
	'kb_articles'           => array(
		'kb_articles'   => 'kb',
		'kb_categories' => 'cat',
	),
	'blog_posts'            => array(
		'blog_posts'      => 'blog',
		'blog_categories' => 'cat',
	),
	'faq'                   => array(
		'faq' => 'faq',
	),
	'affiliate_payments'    => array(
		'affiliate_payments' => 'pay',
	),
	'affiliate'             => array(
		'affiliate_marketing' => 'aff',
		'affiliate_groups'    => 'aff_group',
	),
);

$config['api_load_helpers'] = array(
	'products'            => array('string', 'text'),
	'orders'              => array('string', 'text'),
	'brands'              => array('products'),
	'products_categories' => array('products'),
	'blog_posts'          => array('text'),
	'kb_articles'         => array('text'),
	'faq'                 => array('text'),
);

/* End of file api.php */
/* Location: ./application/config/api.php */