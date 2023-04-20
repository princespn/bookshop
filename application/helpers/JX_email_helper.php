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

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_admin_failed_login_email($data = array())
{
	$data['username'] = $data[config_item('admin_login_username_field')];
	$data['password'] = $data[config_item('admin_login_password_field')];

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $member
 * @param array $admin
 * @return mixed
 */
function format_affiliate_activation_email($member = array(), $admin = array())
{
	foreach ($member as $k => $v)
	{
		$data['member_' . $k] = $v;
	}

	foreach ($admin as $k => $v)
	{
		$data['admin_' . $k] = $v;
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @param array $post
 * @return array
 */
function format_forum_alert_email($type = 'topic', $data = array(), $post = array())
{
	switch ($type)
	{
		case 'topic':

			$data['member_fname'] = sess('fname');
			$data['member_lname'] = sess('lname');
			$data['topic_url'] = site_url(config_item('forum_uri') . '/topic/' . $post['url']);

			$data = array_merge($data, $post);

			break;

		case 'reply':
			$data['reply_id'] = $post['reply_id'];
			$data['reply_content'] = $post['reply_content'];
			$data['topic_url'] = site_url(config_item('forum_uri') . '/topic/' . $data['url']);

			break;
	}


	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return false|string
 */
function format_email_download_links($data = array())
{
	$data['text_download_links'] = '';
	$data['html_download_links'] = '<ul>';
	$data['html_download_links_list'] = '';
	$data['download_link_url'] = site_url(('product/download/'));

	$i = 1;
	foreach ($data['downloads'] as $v)
	{
		$data['text_download_links'] .= site_url('product/download/' . $v['code']) . "\n\n\n";
		$data['html_download_links'] .= '<li>' . anchor(site_url('product/download/' . $v['code']), $v['filename']) . '</li>';
		$data['html_download_links_list'] .= nl2br(site_url('product/download/' . $v['code']) . "\n\n");

		$data['download_link_filename_' . $i] = $v['filename'];
		$data['download_link_code_' . $i] = $v['code'];

		$i++;
	}

	$data['html_download_links'] .= '</ul>';

	return sc($data);
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @return array
 */
function format_product_email($type = '', $data = array())
{
	switch ($type)
	{
		case 'product_review':

			$data['product_url'] = page_url('product', $data);

			return $data;

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $body
 * @param string $s
 */
function send_debug_email($body = array(), $s = 'debug email')
{
	$CI = &get_instance();

	$m = print_r($body, TRUE);

	$data['subject'] = $s;
	$data['event'] = nl2br($m);
	$CI->mail->send_email_events($data);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param array $post
 * @return array
 */
function format_gift_certificate_template($data = array(), $post = array())
{
	$vars = array(
		'cert_id'     => $data['cert_id'],
		'description' => $data['description'],
		'code'        => $data['code'],
		'from_name'   => $data['from_name'],
		'from_email'  => $data['from_email'],
		'to_name'     => $post['to_name'],
		'to_email'    => $post['to_email'],
		'amount'      => $data['amount'],
		'message'     => strip_tags($post['message']),
	);

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param string $email
 * @return string
 */
function encode_email($email = '')
{
	$output = '';
	for ($i = 0; $i < strlen($email); $i++)
	{
		$output .= '&#' . ord($email[$i]) . ';';
	}

	return $output;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param string $email
 * @param array $data
 * @param string $lang_id
 * @return array
 */
function format_mailing_list_member_data($id = '', $email = '', $data = array(), $lang_id = '1')
{
	$vars = array(
		'list_id'       => $id,
		'member_id'     => is_var($data, 'member_id'),
		'language_id'   => $lang_id,
		'email_address' => $email,
		'sequence_id'   => 1,
		'send_date'     => get_time(now(), TRUE),
		'sub_data'      => format_sub_data($data),
	);

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function format_sub_data($data = array())
{
	$CI = &get_instance();
	if (!empty($data))
	{
		$vars = $CI->dbv->clean($data, TBL_MEMBERS);

		return serialize($vars);
	}

	return '';
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return bool
 */
function valid_email($str = '')
{
	return (bool)filter_var($str, FILTER_VALIDATE_EMAIL);
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @return array
 */
function format_registration_email($type = '', $data = array())
{
	switch ($type)
	{
		case EMAIL_ADMIN_ALERT_NEW_SIGNUP:

			$data['admin_fname'] = is_var($data, 'admin_fname');
			$data['admin_lname'] = is_var($data, 'admin_lname');
			$data['member_fname'] = is_var($data, 'fname');
			$data['member_lname'] = is_var($data, 'lname');
			$data['fname'] = is_var($data, 'admin_fname');
			$data['lname'] = is_var($data, 'admin_lname');
			$data['member_username'] = is_var($data, 'username');
			$data['billing_state'] = !empty($data['billing_state']) ? get_region_name($data['billing_state'], 'region_name') : '';
			$data['billing_country'] = !empty($data['billing_country']) ? get_country_name($data['billing_country'], 'country_name') : '';
			$data['payment_state'] = !empty($data['payment_state']) ? get_region_name($data['payment_state'], 'region_name') : '';
			$data['payment_country'] = !empty($data['payment_country']) ? get_country_name($data['payment_country'], 'country_name') : '';

			return $data;

			break;

		case EMAIL_MEMBER_LOGIN_DETAILS:

			$data['billing_state'] = !empty($data['billing_state']) ? get_region_name($data['billing_state'], 'region_name') : '';
			$data['billing_country'] = !empty($data['billing_country']) ? get_country_name($data['billing_country'], 'country_name') : '';
			$data['payment_state'] = !empty($data['payment_state']) ? get_region_name($data['payment_state'], 'region_name') : '';
			$data['payment_country'] = !empty($data['payment_country']) ? get_country_name($data['payment_country'], 'country_name') : '';

			return $data;

			break;

		case EMAIL_MEMBER_EMAIL_CONFIRMATION:

			$data['confirm_url_text'] = generate_confirmation_url($data['confirm_id']);
			$data['confirm_url'] = anchor(generate_confirmation_url($data['confirm_id']));

			return $data;

			break;

		case EMAIL_MEMBER_AFFILIATE_DOWNLINE_SIGNUP:

			$user = $data;
			$user['affiliate_fname'] = is_var($data['sponsor_data'], 'fname');
			$user['affiliate_lname'] = is_var($data['sponsor_data'], 'lname');
			$user['affiliate_username'] = is_var($data['sponsor_data'], 'username');

			$user['referral_fname'] = $data['fname'];
			$user['referral_lname'] = $data['lname'];
			$user['fname'] = is_var($data['sponsor_data'], 'fname');
			$user['lname'] = is_var($data['sponsor_data'], 'lname');
			$user['referral_username'] = $data['username'];
			$user['referral_email'] = $data['primary_email'];

			return $user;

			break;

		case EMAIL_MEMBER_ALERT_SIGNUP_BONUS:

			$user = $data['signup_bonus'];
			$user['fname'] = $data['fname'];
			$user['lname'] = $data['lname'];
			$user['username'] = $data['username'];

			return $user;

			break;

		case EMAIL_MEMBER_AFFILIATE_REFERRAL_SIGNUP_BONUS:

			$user = array_merge($data['referral_bonus'], $data['sponsor_data']);
			$user['fname'] = $data['fname'];
			$user['lname'] = $data['lname'];
			$user['username'] = $data['username'];

			return $user;

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_mass_downline_email($data = array())
{
	$user = array('member_fname'     => $data['fname'],
	              'member_lname'     => $data['lname'],
	              'member_username'  => $data['username'],
	              'primary_email'    => $data['primary_email'],
	              'sponsor_fname'    => sess('fname'),
	              'sponsor_lname'    => sess('lname'),
	              'sponsor_username' => sess('username'),
	              'sponsor_email'    => sess('primary_email'),
	              'html_message'        => nl2br($data['message']),
	              'message'        => $data['message'],

	);

	return $user;
}

// ------------------------------------------------------------------------

/**
 * @param string $email
 * @param array $data
 * @param string $type
 * @return array|mixed
 */
function format_checkout_email($email = '', $data = array(), $type = 'checkout')
{
	switch ($email)
	{
		case 'commission':

			$data['sale_amount'] = format_amount($data['sale_amount']);
			$data['commission_amount'] = format_amount($data['commission_amount']);
			$data['date'] = display_date($data['date']);

			return $data;

			break;

		case 'invoice':

			if ($type == 'invoice')
			{
				$invoice = $data['invoice'];
				$invoice['payments'] = !empty($data['payment']) ? array($data['payment']) : '';
				$invoice['date_purchased'] = display_date($data['invoice']['date_purchased']);
				$invoice['due_date'] = display_date($data['invoice']['due_date']);

				if (!empty($data['payment']['paid']))
				{
					$data['invoice']['payment_status_id'] = '2';
				}

				$invoice['payment_status'] = get_payment_status($data['invoice']['payment_status_id']);
				$invoice['customer_state'] = get_region_name($data['invoice']['customer_state'], 'region_name');
				$invoice['customer_country'] = get_country_name($data['invoice']['customer_country'], 'country_name');

				$invoice['shipping_state'] = get_region_name($data['invoice']['shipping_state'], 'region_name');
				$invoice['shipping_country'] = get_country_name($data['invoice']['shipping_country'], 'country_name');
			}
			else
			{
				$invoice = $data['invoice']['data'];
				$invoice['payments'] = !empty($data['payment']) ? array($data['payment']) : '';
				$invoice['date_purchased'] = display_date($data['invoice']['data']['date_purchased']);
				$invoice['due_date'] = display_date($data['invoice']['data']['due_date']);

				if (!empty($data['payment']['paid']))
				{
					$data['invoice']['data']['payment_status_id'] = '2';
				}

				$invoice['payment_status'] = get_payment_status($data['invoice']['data']['payment_status_id']);
				$invoice['customer_state'] = get_region_name($data['invoice']['data']['customer_state'], 'region_name');
				$invoice['customer_country'] = get_country_name($data['invoice']['data']['customer_country'], 'country_name');

				$invoice['shipping_state'] = get_region_name($data['invoice']['data']['shipping_state'], 'region_name');
				$invoice['shipping_country'] = get_country_name($data['invoice']['data']['shipping_country'], 'country_name');
			}

			return $invoice;

			break;

		case 'invoice_admin':

			$data['date_purchased'] = display_date($data['date_purchased']);
			$data['due_date'] = display_date($data['due_date']);

			$data['payment_status'] = get_payment_status($data['payment_status_id']);
			$data['customer_state'] = get_region_name($data['customer_state'], 'region_name');
			$data['customer_country'] = get_country_name($data['customer_country'], 'country_name');

			$data['shipping_state'] = get_region_name($data['shipping_state'], 'region_name');
			$data['shipping_country'] = get_country_name($data['shipping_country'], 'country_name');

			return $data;

			break;

		case 'order':

			$order = $data['order'];
			$order['order_items'] = $data['order']['items'];
			$order['date_ordered'] = display_date($data['order']['date_ordered']);
			$order['due_date'] = display_date($data['order']['due_date']);
			$order['order_status'] = get_order_status($data['order']['order_status_id']);
			$order['order_state'] = get_region_name($data['order']['order_state'], 'region_name');
			$order['order_country'] = get_country_name($data['order']['order_country'], 'country_name');

			if (!empty($data['order']['shipping_data']))
			{
				$order['shipping_state'] = get_region_name($data['order']['shipping_state'], 'region_name');
				$order['shipping_country'] = get_country_name($data['order']['shipping_country'], 'country_name');
				$order['shipping'] = unserialize($data['order']['shipping_data']);
				$order['shipping']['shipping_amount'] = format_amount($order['shipping']['shipping_total']);
			}

			return $order;

			break;

		case 'order_admin':

			$order = $data;
			$order['date_ordered'] = display_date($data['date_ordered']);

			$order['order_status'] = get_order_status($data['order_status_id']);
			$order['order_state'] = get_region_name($data['order_state'], 'region_name');
			$order['order_country'] = get_country_name($data['order_country'], 'country_name');

			$order['shipping_state'] = get_region_name($data['shipping_state'], 'region_name');
			$order['shipping_country'] = get_country_name($data['shipping_country'], 'country_name');
			$order['order_items'] = $order['items'];

			return $order;

			break;

		case 'member_order_email':

			$order = $data;
			$order['order_items'] = $data['items'];
			$order['date_ordered'] = display_date($data['date_ordered']);
			$order['due_date'] = display_date($data['due_date']);
			$order['order_status'] = get_order_status($data['order_status_id']);
			$order['order_state'] = $data['order_state_name'];
			$order['order_country'] = $data['order_country_name'];

			if (!empty($data['shipping']))
			{
				$order['shipping_state'] = $data['shipping_state_name'];
				$order['shipping_country'] = $data['shipping_country_name'];
				$order['shipping'] = $data['shipping'];
				$order['shipping']['shipping_amount'] = format_amount($data['shipping']['shipping_amount']);
			}

			return $order;

			break;

		case 'supplier':

			$data['fname'] = $data['supplier_name'];
			$data['primary_email'] = $data['supplier_email'];

			if (!empty($data['shipping_data']))
			{
				$data['shipping_state_name'] = get_region_name($data['shipping_state'], 'region_name');
				$data['shipping_country_name'] = get_country_name($data['shipping_country'], 'country_name');
			}

			$data['attributes'] = '';

			if (!empty($data['attribute_data']))
			{
				foreach (unserialize($data['attribute_data']) as $k => $v)
				{
					$data['attributes'] .= order_attributes($k, $v) . "\n";
				}

				$data['attributes_text'] = strip_tags($data['attributes']);
			}

			return $data;

			break;

		case 'support_admin':

			$user = array('admin_fname'     => is_var($data, 'admin_fname'),
			              'admin_lname'     => is_var($data, 'admin_lname'),
			              'member_fname'    => is_var($data, 'fname'),
			              'member_lname'    => is_var($data, 'lname'),
			              'primary_email'   => is_var($data, 'primary_email'),
			              'ip_address'      => is_var($data, 'ip_address'),
			              'ticket_id'       => is_var($data, 'ticket_id'),
			              'ticket_status'   => is_var($data, 'ticket_status'),
			              'ticket_priority' => is_var($data, 'priority'),
			              'ticket_category' => !empty($data['category_id']) ? get_support_category($data['category_id'], sess('default_lang_id')) : '',
			              'ticket_subject'  => is_var($data, 'ticket_subject'),
			              'ticket_message'  => nl2br(is_var($data, 'reply_content')),
			);

			return $user;

			break;

		case 'support_member':

			$user = array('fname'           => is_var($data, 'fname'),
			              'lname'           => is_var($data, 'lname'),
			              'primary_email'   => is_var($data, 'primary_email'),
			              'ticket_id'       => is_var($data, 'ticket_id'),
			              'ticket_status'   => is_var($data, 'ticket_status'),
			              'ticket_priority' => is_var($data, 'priority'),
			              'ticket_category' => !empty($data['category_id']) ? get_support_category($data['category_id'], sess('default_lang_id')) : '',
			              'ticket_subject'  => is_var($data, 'ticket_subject'),
			              'ticket_message'  => nl2br(is_var($data, 'reply_content')),
			);

			return $user;

			break;

		case 'bonus':

			$user = $data['signup_bonus'];
			$user['fname'] = $data['fname'];
			$user['lname'] = $data['lname'];
			$user['username'] = $data['username'];

			return $user;

			break;

		case 'referral_bonus':

			$user = array_merge($data['referral_bonus'], $data['sponsor_data']);
			$user['fname'] = $data['fname'];
			$user['lname'] = $data['lname'];
			$user['username'] = $data['username'];

			return $user;

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_custom_form_fields($data = array())
{
	unset($data['g-recaptcha-response']);

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param string $email
 * @return bool
 */
function check_free_email_accounts($email = '')
{
	if (config_enabled('sts_form_site_enable_block_free_email_accounts'))
	{
		//get domains
		$blocked = config_item('sts_form_site_block_free_email_accounts');

		if (!empty($blocked) && !empty($email))
		{
			$domains = explode("\n", config_item('sts_form_site_block_free_email_accounts'));

			list($user, $domain) = explode('@', $email);

			foreach ($domains as $v)
			{
				if ($domain == trim(strtolower($v)))
				{
					return FALSE;
				}
			}
		}
	}

	return TRUE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_mass_email($data = array())
{
	foreach ($data as $k => $v)
	{
		switch ($k)
		{
			case 'email_address':

				$data['primary_email'] = $v;

				break;
		}
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return string
 */
function format_template_name($str = '')
{
	return url_title($str, 'underscore');
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return mixed|string
 */
function format_recipient_name($data = array())
{
	$name = !empty($data['fname']) ? $data['fname'] : '';

	if (!empty($data['lname']))
	{
		$name .= ' ' . $data['lname'];
	}
	elseif (!empty($data['customer_name']))
	{
		$name = $data['customer_name'];
	}
	elseif (!empty($data['order_name']))
	{
		$name = $data['order_name'];
	}

	return $name;
}

/* End of file JX_email_helper.php */
/* Location: ./application/helpers/JX_email_helper.php */