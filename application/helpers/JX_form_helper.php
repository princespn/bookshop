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
 * @param string $file
 * @return bool
 */
function valid_ext($file = '')
{
	$CI = &get_instance();

	//check extension
	$types = explode('|', $CI->config->item('sts_support_upload_download_types'));

	foreach ($types as $v)
	{
		$a = explode('.', $file);
		$ext = trim(strtolower(end($a)));

		if (in_array($ext, $types))
		{
			return TRUE;
		}
	}

	return FALSE;
}

/**
 * generate validation rules based on database field type
 *
 * This function will generate the form validation rules based
 * on the type of database field it is.  For example, if the
 * field is a varchar, add validation rules that will limit the
 * max_length set for that field type.  If it is an int, set
 * the default valiation rule to validate integers.
 *
 * @param string $type
 * @param string $max_length
 * @return string
 */

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param string $max_length
 * @param bool $clean_text
 * @param string $name
 * @return string
 */
function generate_db_rule($type = '', $max_length = '', $clean_text = FALSE, $name = '')
{
	$rule = '';

	switch ($type)
	{
		case 'int':

			$rule .= '|integer';

			break;

		case 'date':

			$rule .= '|day_to_sql';

			break;

		case 'datetime':

			switch ($name)
			{
				case 'start_date':

					$rule .= '|start_date_to_sql';

					break;

				case 'end_date':
				case 'next_due_date':

					$rule .= '|end_date_to_sql';

					break;

				default:

					$rule .= '|date_to_sql';

					break;
			}

			break;

		case 'decimal':

			$rule .= '|numeric';

			break;

		case 'varchar':

			$rule .= '|max_length[' . $max_length . ']';

			break;

		case 'text':
		case 'mediumtext':
		case 'longtext':

			$rule .= $clean_text == TRUE ? '|xss_clean' : '';

			break;
	}

	return $rule;
}

function default_custom_form_fields($id = '', $type = '')
{
	$data = array('form_id'           => $id,
	              'show_public'       => '1',
	              'show_account'      => '0',
	              'field_type'        => 'text',
	              'custom'            => '1',
	              'form_field'        => $type,
	              'field_required'    => '1',
	              'field_options'     => '',
	              'field_value'       => '',
	              'field_name'        => $type,
	              'field_description' => $type,
	              'field_validation'  => 'trim',
	              'sub_form'          => '',
	              'sort_order'        => '999',
	);

	switch ($type)
	{
		case 'fname':

			$data['field_name'] = lang('first_name');
			$data['field_description'] = lang('first_name');
			break;

		case 'lname':
			$data['field_name'] = lang('last_name');
			$data['field_description'] = lang('last_name');

			break;

		case 'primary_email':

			$data['field_name'] = lang('email_address');
			$data['field_description'] = lang('email_address');
			$data['field_validation'] .= '|valid_email';
			break;
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param array $lang
 * @return array
 */
function set_default_create_data($data = array(), $lang = array())
{
	foreach ($lang as $k => $v)
	{
		foreach ($data as $a => $c)
		{
			if ($a == 'language_id')
			{
				continue;
			}
			$lang[$k][$a] = $c;
		}
	}

	return $lang;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function set_default_form_values($data = array())
{

	$vars = list_fields($data);

	foreach ($data as $v)
	{
		switch ($v)
		{
			case TBL_AFFILIATE_COMMISSION_RULES:

				$a = array('action'             => 'issue_bonus_commission',
				           'end_date_formatted' => display_date(get_time(), FALSE, 2, TRUE),
				);

				break;

			case TBL_AFFILIATE_COMMISSIONS:

				$a = array('date_formatted'      => display_date(get_time(), FALSE, 2, TRUE),
				           'date_paid_formatted' => display_date(get_time(), FALSE, 2, TRUE),
				           'invoice_id'          => (int)uri(4),
				           'username'            => '',
				);

				break;

			case TBL_PROMOTIONAL_RULES:

				$a = array('promo_amount'         => '1',
				           'discount_type'        => 'percent',
				           'start_date_formatted' => display_date(get_time() - 2592000, FALSE, 2, TRUE),
				           'end_date_formatted'   => display_date(get_time() + 31536000, FALSE, 2, TRUE),
				);

				break;

			case TBL_TRACKING:

				$a = array('url'         => site_url(),
				           'expired_url' => site_url(),
				           'end_date'    => display_date(get_time() + 31536000, FALSE, 2, TRUE),
				);

				break;

			case TBL_EMAIL_TEMPLATES:

				$a = array('html'       => '1',
				           'type'       => 'custom',
				           'from_name'  => config_option('sts_site_name'),
				           'from_email' => config_option('sts_site_email'),
				);

				break;
		}
	}

	//add any custom fields to the default...
	if (!empty($a))
	{
		foreach ($a as $k => $v)
		{
			$vars[$k] = $v;
		}
	}

	//set default values based on table field
	foreach ($vars as $k => $v)
	{
		switch ($k)
		{
			case 'sort_order':

				$vars[$k] = 1;

				break;

			case 'status':

				$vars[$k] = 0;

				break;

			case 'start_date':

				$vars[$k] = display_date(get_time(), FALSE, 2, TRUE);

				break;

			case 'end_date':

				$vars[$k] = display_date(get_time() + 604800, FALSE, 2, TRUE);

				break;
		}
	}

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @return string
 */
function set_csrf()
{
	$CI = &get_instance();

	return form_hidden($CI->config->item('csrf_token'), $CI->config->item('csrf_value'));
}

// ------------------------------------------------------------------------

/**
 * @param string $field
 * @return string
 */
function css_error($field = '')
{
	if (form_error($field))
	{
		return 'error';
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param bool $keys
 * @return array
 */
function list_fields($data = array(), $keys = TRUE)
{
	$CI = &get_instance();

	$row = array();
	foreach ($data as $v)
	{
		$keys = $CI->db->list_fields($v);

		$row = array_merge($row, $keys);

		if ($keys == FALSE)
		{
			return $row;
		}
	}

	return (array_fill_keys($row, ''));
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @return array
 */
function merge_fields($type = '', $data = array())
{
	$fields = array('site_url'     => 'site_url',
	                'login_url'    => 'login_url',
	                'site_name'    => 'site_name',
	                'current_time' => 'current_time',
	                'current_date' => 'current_date',
	);

	$include = array(); //included tags as part of the merge fields
	$remove = array(); //remove tags from merge fields

	//remove any variables not needed...
	switch ($type)
	{
		case TBL_MEMBERS:

			$remove = array(
				'status', 'email_confirmed', 'is_affiliate', 'enable_custom_url', 'profile_id',
				'updated_on', 'updated_by', 'is_customer', 'id', 'original_sponsor_id', 'profile_photo',
				'profile_background', 'prev', 'next', 'html_body', 'mailing_lists', 'addresses', 'subject');

			break;

		case 'follow_ups':
		case 'affiliate_marketing':

			$include = array(
				'affiliate_url', 'affiliate_url_text', 'member_id', 'fname', 'lname', 'company', 'home_phone', 'work_phone',
				'mobile_phone', 'fax', 'primary_email', 'paypal_id', 'skrill_id', 'payza_id', 'dwolla_id', 'position',
				'coinbase_id', 'custom_id', 'bank_transfer_info', 'payment_preference_amount', 'custom_url_link',
				'website', 'facebook_id', 'twitter_id', 'youtube_id', 'linked_in_id', 'instagram_id', 'pinterest_id',
				'tumblr_id', 'points', 'sponsor_fname', 'sponsor_lname', 'sponsor_username', 'sponsor_primary_email',
				'username', 'unsubscribe_url', 'unsubscribe_url_text');
			break;

		case EMAIL_MEMBER_DOWNLOAD_ACCESS:

			$include = array(
				'fname', 'lname', 'username', 'text_download_links', 'html_download_links', 'html_download_links_list',
			);

			break;

		case EMAIL_ADMIN_RESET_PASSWORD:

			$include = array(
				'fname', 'lname', 'username', 'primary_email', 'reset_admin_password_link', 'reset_admin_password_link_text',
				'admin_login_url',
			);

			break;

		case EMAIL_ADMIN_ALERT_COMMENT_MODERATION:

			$include = array(
				'url', 'name', 'email', 'comment', 'blog_id', 'title',
			);

			break;

		case EMAIL_ADMIN_ALERT_PRODUCT_REVIEW_TEMPLATE:

			$include = array(
				'product_name', 'product_url', 'title', 'comment', 'member_fname', 'member_lname', 'username',
			);

			break;

		case EMAIL_ADMIN_ALERT_CONTACT_US:

			$include = array(
				'name', 'primary_email', 'contact_subject', 'contact_data',
			);

			break;

		case EMAIL_MEMBER_GIFT_CERTIFICATE_DETAILS:

			$include = array(
				'code', 'from_name', 'from_email', 'to_name', 'to_email', 'message', 'amount', 'description',
			);


			break;

		case EMAIL_ADMIN_FAILED_LOGIN:

			$include = array(
				'username', 'password', 'ip_address', 'admin_login_url',
			);

			break;

		case EMAIL_ADMIN_FORUM_REPLY_ALERT:
		case EMAIL_MEMBER_FORUM_REPLY_ALERT:
		case EMAIL_ADMIN_FORUM_TOPIC_ALERT:

			$include = array(
				'admin_fname', 'admin_lname', 'member_username', 'member_fname', 'member_lname', 'ip_address', 'admin_login_url',
				'category_name', 'category_url', 'topic_url', 'title', 'reply_content', 'topic_id', 'reply_id', 'primary_email',
			);

			break;

		case EMAIL_ADMIN_PRODUCT_INVENTORY_ALERT:

			$include = array(
				'admin_fname', 'admin_lname', 'product_name', 'product_id', 'inventory_alert_level',
				'product_sku', 'current_inventory',
			);

			break;

		case EMAIL_ADMIN_ALERT_NEW_ORDER:

			$include = array(
				'order_id', 'order_number', 'payment_status_code', 'order_status_code', 'member_id', 'order_name',
				'order_company', 'order_address_1', 'order_address_2', 'order_city', 'order_postal_code',
				'order_state_name', 'order_country_name', 'order_telephone', 'order_primary_email', 'shipping_name',
				'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_postal_code',
				'shipping_state_name', 'shipping_country_name', 'date_ordered', 'due_date', 'order_total', 'ip_address',
				'order_notes', 'order_items_html', 'carrier', 'service', 'rate', 'rate_id', 'tracking_id',
				'invoice_id', 'invoice_number', 'admin_fname', 'admin_lname', 'total', 'ip_address', 'invoice_notes',
				'invoice_items_html', 'invoice_payments_html', 'tax', 'points', 'sub_total', 'shipping', 'amount', 'fee',
				'transaction_id', 'method', 'description',
			);

			break;

		case EMAIL_ADMIN_PRODUCT_ATTRIBUTE_INVENTORY_ALERT:

			$include = array(
				'product_id', 'product_sku', 'product_name', 'current_inventory', 'attribute_name', 'option_name',
				'inventory_alert_level',
			);

			break;

		case EMAIL_ADMIN_PRODUCT_INVENTORY_ALERT:

			$include = array(
				'product_id', 'product_sku', 'product_name', 'current_inventory', 'inventory_alert_level',
			);

			break;

		case EMAIL_ADMIN_AFFILIATE_MARKETING_ACTIVATION:

			$include = array(
				'admin_fname', 'admin_lname', 'member_fname', 'member_lname', 'member_username',
			);

			break;

		case EMAIL_ADMIN_ALERT_SUPPLIER:

			$include = array(
				'supplier_id', 'supplier_name', 'image', 'supplier_email', 'supplier_address', 'supplier_city', 'supplier_state',
				'supplier_country', 'supplier_zip', 'supplier_phone', 'supplier_notes', 'order_id', 'order_number',
				'payment_status_code', 'order_status_code', 'member_id', 'order_name', 'order_company', 'order_address_1',
				'order_address_2', 'order_city', 'order_postal_code', 'order_state_name', 'order_country_name',
				'order_telephone', 'order_primary_email', 'shipping_name', 'shipping_company', 'shipping_address_1',
				'shipping_address_2', 'shipping_city', 'shipping_postal_code', 'shipping_state_name',
				'shipping_country_name', 'product_name', 'product_sku', 'quantity',
			);

			break;

		case EMAIL_ADMIN_ALERT_NEW_SIGNUP:

			$include = array(
				'admin_fname', 'admin_lname', 'member_fname', 'member_lname', 'member_username', 'work_phone',
				'last_login_ip', 'member_id', 'company', 'position', 'home_phone', 'tumblr_id',
				'mobile_phone', 'fax', 'primary_email', 'paypal_id', 'skrill_id', 'payza_id', 'dwolla_id',
				'coinbase_id', 'custom_id', 'bank_transfer_info', 'payment_preference_amount', 'custom_url_link',
				'website', 'facebook_id', 'twitter_id', 'youtube_id', 'linked_in_id', 'instagram_id', 'pinterest_id',
			);

			break;

		case 'admin_create_support_ticket_template':

			$include = array(
				'fname', 'lname',
			);

			break;

		case 'member_support_ticket_reply_template':
		case 'member_create_support_ticket_template':

			$include = array(
				'fname', 'lname', 'ip_address', 'ticket_id', 'ticket_status', 'ticket_priority',
				'ticket_category', 'ticket_subject', 'ticket_message',
			);

			break;

		case EMAIL_MEMBER_LOGIN_DETAILS:

			$include = array(
				'member_id', 'fname', 'lname', 'username', 'password', 'company', 'position', 'home_phone', 'work_phone',
				'mobile_phone', 'fax', 'primary_email', 'paypal_id', 'skrill_id', 'payza_id', 'dwolla_id',
				'coinbase_id', 'custom_id', 'bank_transfer_info', 'payment_preference_amount', 'custom_url_link',
				'website', 'facebook_id', 'twitter_id', 'youtube_id', 'linked_in_id', 'instagram_id', 'pinterest_id',
				'tumblr_id', 'points');

			break;

		case 'mass_email':

			$include = array(
				'member_id', 'fname', 'lname', 'username');

			break;

		case 'member_affiliate_performance_group_upgrade_template':

			$include = array(
				'member_fname', 'member_lname', 'ip_address', 'upgrade_affiliate_group',
			);

			break;

		case 'member_affiliate_performance_bonus_amount_template':

			$include = array(
				'member_fname', 'member_lname', 'ip_address', 'bonus_amount',
			);

			break;

		case 'member_affiliate_commission_generated_template':

			$include = array(
				'comm_id', 'member_id', 'invoice_id', 'comm_status', 'commission_amount', 'sale_amount', 'fee',
				'commission_level', 'referrer', 'trans_id', 'ip_address', 'commission_notes',
				'member_username', 'member_fname', 'member_lname',
			);

			break;

		case 'member_payment_invoice_template':

			$include = array(
				'invoice.data.invoice_number',
				'invoice.data.customer_name',
				'invoice_id', 'order_id', 'payment_status_id', 'member_id',
				'customer_company', 'customer_address_1', 'customer_address_2', 'customer_city', 'customer_postal_code',
				'customer_state_name', 'customer_country_name', 'customer_telephone', 'customer_fax',
				'customer_primary_email', 'shipping_name', 'shipping_company', 'shipping_address_1',
				'shipping_address_2', 'shipping_city', 'shipping_postal_code', 'shipping_state_name',
				'shipping_country_name', 'date_purchased', 'due_date', 'total', 'ip_address', 'invoice_notes',
				'invoice_items_html', 'invoice_payments_html', 'tax', 'points', 'sub_total', 'shipping', 'amount', 'fee',
				'transaction_id', 'method', 'description',
			);

			break;

		case 'member_affiliate_payment_sent_template':

			$include = array(
				'member_id', 'member_fname', 'member_lname', 'payment_name', 'payment_date', 'payment_type',
				'payment_amount', 'payment_notes',
			);

			break;

		case 'member_order_details_template':

			$include = array(
				'order_id', 'order_number', 'payment_status_code', 'order_status_code', 'member_id', 'order_name',
				'order_company', 'order_address_1', 'order_address_2', 'order_city', 'order_postal_code',
				'order_state_name', 'order_country_name', 'order_telephone', 'order_primary_email', 'shipping_name',
				'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_postal_code',
				'shipping_state_name', 'shipping_country_name', 'date_ordered', 'due_date', 'order_total', 'ip_address',
				'order_notes', 'order_items_html', 'carrier', 'service', 'rate', 'rate_id', 'tracking_id',
			);

			break;

		case EMAIL_MEMBER_EMAIL_CONFIRMATION:

			$include = array(
				'fname', 'lname', 'username', 'primary_email', 'confirm_url', 'confirm_url_text',
			);

			break;

		case EMAIL_MEMBER_AFFILIATE_DOWNLINE_SIGNUP:

			$include = array(
				'affiliate_fname', 'affiliate_lname', 'affiliate_username', 'referral_fname', 'referral_lname', 'referral_username',
				'referral_email',
			);

			break;

		case EMAIL_MEMBER_ALERT_SIGNUP_BONUS:

			$include = array(
				'fname', 'lname', 'username', 'commission_amount',
			);

			break;

		case EMAIL_MEMBER_AFFILIATE_REFERRAL_SIGNUP_BONUS:

			$include = array(
				'fname', 'lname', 'username', 'commission_amount', 'referral_fname', 'referral_lname', 'referral_username',
				'referral_email', 'comm_status',
			);

			break;

		case EMAIL_MEMBER_RESET_PASSWORD:

			$include = array(
				'fname', 'lname', 'username', 'primary_email', 'reset_member_password_link', 'reset_member_password_link_text',
			);

			break;

		case 'member_order_status_change_template':

			$include = array(
				'fname', 'lname', 'order_status_code', 'invoice_number',
			);

			break;

		case 'member_affiliate_send_downline_email':

			$include = array(
				'member_fname', 'member_lname', 'member_username', 'sponsor_fname', 'sponsor_lname', 'sponsor_username',
				'sponsor_email', 'html_message', 'message',
			);

			break;

		case 'member_affiliate_marketing_approval_template':

			$include = array(
				'member_fname', 'member_lname', 'member_username',
			);

			break;
	}

	if (!empty($data))
	{
		foreach ($data as $k => $v)
		{
			if (is_array($v))
			{
				continue;
			}

			if (in_array($k, $remove))
			{
				continue;
			}

			$fields[$k] = $k;
		}
	}

	if (!empty($include))
	{
		foreach ($include as $k)
		{
			$fields[$k] = $k;
		}
	}

	$row = array('' => lang('allowed_merge_fields'));

	foreach ($fields as $k => $v)
	{
		$row['{{' . $k . '}}'] = ucfirst(lang($k)) . ' - {{' . $k . '}}';
	}

	asort($row);

	return $row;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @param array $data
 * @return mixed
 */
function map_field($str = '', $data = array())
{
	foreach ($data as $v)
	{
		if ($v != 'none')
		{
			$a = explode('.', $v);

			if (!empty($a[1]))
			{
				if ($str == $a[1])
				{
					return $v;
				}
			}
		}
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @return string
 */
function generate_form_link($id = '')
{
	//generate the links to each form for the site, including checkout, registration and contact forms
	$forms = config_option('default_form_ids');

	return !empty($forms[$id]) ? site_url($forms[$id]) : site_url('form/id/' . $id);
}

/**
 * options array
 *
 * generates all the different dropdown options for form fields
 *
 * @param string $array
 * @param string $default
 * @param string $use_array
 * @return array
 */

// ------------------------------------------------------------------------

/**
 * @param string $array
 * @param string $default
 * @param string $use_array
 * @return array|bool|false
 */
function options($array = '', $default = '', $use_array = '')
{
	$CI = &get_instance();

	$lang_id = !sess('default_lang_id') ? 1 : sess('default_lang_id');
	//load options file for dropdowns
	$CI->load->config('options');

	if (sess('default_lang_id'))
	{
		$lang_id = sess('default_lang_id');
	}

	$a = array();

	switch ($array)
	{
		case 'active':
		case 'address_type':
		case 'admin_status':
		case 'affiliate_link_type':
		case 'affiliate_link_username_id':
		case 'affiliate_performance_bonus':
		case 'affiliate_performance_bonus_required':
		case 'ampm':
		case 'approve':
		case 'asc_desc':
		case 'backup_option':
		case 'backup_path':
		case 'bill_ship_address':
		case 'blog_comments_module':
		case 'boolean':
		case 'cart_commissions':
		case 'cc_auth_type':
		case 'comment_reply_to':
		case 'commission_refund_option':
		case 'commission_type':
		case 'date_format':
		case 'forced_matrix_spillover':
		case 'full_column':
		case 'get_post':
		case 'grid_list':
		case 'image_library':
		case 'mailer_type':
		case 'mark_ticket_priority':
		case 'mass_payment':
		case 'menu_link_type':
		case 'numbers_5':
		case 'numbers_50':
		case 'operator':
		case 'paid_options':
		case 'paper_orientation':
		case 'paper_size':
		case 'payment':
		case 'pending_commission':
		case 'plus_minus':
		case 'position':
		case 'process_commissions':
		case 'production_sandbox':
		case 'products':
		case 'published':
		case 'record_limit':
		case 'sidebar':
		case 'ssl_tls':
		case 'video_control_bar':
		case 'yes_no':
		case 'use_saved_billing':

			$a = config_option($array);

			break;

		case 'admin_home_page_redirect':

			foreach (config_option('admin_home_page_redirect') as $v)
			{
				$a[$v] = lang($v);
			}

			break;

		case 'form_processor':

			foreach (config_option('form_processor') as $v)
			{
				$a[$v] = lang('form_processor_send_to_' . $v);
			}

			break;

		case 'cc_type':

			$q = $CI->db->get(TBL_CC_TYPES);
			$a = format_array($q->result_array(), 'cc_type', 'cc_type');

			break;

		case 'db_tables':

			$tables = $CI->db->list_tables();

			foreach ($tables as $t)
			{
				$a[$t] = str_replace($CI->db->dbprefix, '', $t);
			}
			break;

		case 'list_modules':

			$row = $CI->mod->get_modules('mailing_lists');

			$a = format_array($row, 'module_folder', 'module_folder');

			$a['internal'] = lang('internal');

			break;

		case 'widget_categories':

			foreach ($CI->config->item('widget_categories') as $k => $v)
			{

				$a[$k] = lang($v);
			}

			break;

		case 'payment_methods':

			$row = $CI->mod->get_modules('payment_gateways');

			$a = format_array($row, 'module_folder', 'module_folder');

			$a['refund'] = lang('refund');
			$a['manual'] = lang('manual');
			$a['credit'] = lang('member_credit');

			break;

		case 'payment_gateways':

			$row = $CI->mod->get_modules('payment_gateways');

			$a = format_array($row, 'module_folder', 'module_folder');

			break;

		case 'order_statuses':

			$a = $CI->orders->get_statuses(TRUE);

			break;

		case 'commission_levels':

			$levels = $CI->config->item('max_commission_levels') ? $CI->config->item('max_commission_levels') : 1;
			for ($i = 1; $i <= $levels; $i++)
			{
				$a[$i] = $i;
			}

			break;

		case 'age_limit':

			for ($i = MIN_AGE_LIMIT; $i <= MAX_AGE_LIMIT; $i++)
			{
				$a[$i] = $i;
			}

			break;

		case 'commission_levels_any':

			$a['0'] = lang('any');

			for ($i = 1; $i <= $CI->config->item('sts_affiliate_commission_levels'); $i++)
			{
				$a[$i] = $i;
			}

			break;

		case 'payment_statuses':

			$a = $CI->invoices->get_payment_statuses(TRUE);

			break;

		case 'content_layout':

			$a = array('default'    => lang('default_template'),
			           'store_grid' => lang('store_grid'),
			           'store_list' => lang('store_list'),
			           'blog_grid'  => lang('blog_grid'),
			           'blog_list'  => lang('blog_list'),
			);

			if (check_section(SITE_BUILDER))
			{
				$a['builder'] = lang('site_builder');
			}

			break;

		case 'comm_statuses':

			foreach ($CI->config->item('affiliate_commission_statuses') as $v)
			{
				$a[$v] = lang($v);
			}

			break;

		case 'commissions':

			$a = array('1'       => lang('approved'),
			           '0'       => lang('unapproved'),
			           'pending' => lang('pending'),
			           'unpaid'  => lang('unpaid'),
			);

			break;

		case 'sub_form':

			foreach ($CI->config->item('sub_forms') as $v)
			{

				$a[$v] = empty($v) ? lang('none') : lang($v);
			}

			break;

		case 'menus':

			$a = $CI->menus->get_rows(TRUE);

			break;

		case 'members':

			foreach ($CI->config->item('options_mass_update_members') as $v)
			{
				if ($v == 'set_affiliate_group' && !config_enabled('affiliate_marketing'))
				{
					continue;
				}

				$a[$v] = lang($v);
			}

			break;

		case 'interval_types':

			foreach ($CI->config->item('recurring_interval_types') as $v)
			{
				$a[$v] = lang($v);
			}

			break;

		case 'ticket_status':

			foreach ($CI->config->item('ticket_status_options') as $v)
			{
				$a[$v] = lang($v);
			}

			break;

		case 'ticket_priority':

			foreach ($CI->config->item('ticket_priorities') as $v)
			{
				$a[$v] = lang($v);
			}

			break;

		case 'ticket_categories':

			$a = $CI->cat->get_categories(sess('default_lang_id'), TRUE);

			break;

		case 'forum_categories':

			$lang = sess('default_lang_id') ? sess('default_lang_id') : sess('default_lang_id');

			$a = $CI->cat->get_categories($lang, TRUE);

			break;

		case 'cc_months':

			$a = generate_cc_months();

			break;

		case 'cc_years':

			$a = generate_cc_years($default);

			break;

		case 'attribute_types':

			foreach ($CI->config->item('attribute_types') as $v)
			{
				$a[$v] = lang($v);
			}

			break;

		case 'input_types':

			foreach ($CI->config->item('input_types') as $v)
			{
				$a[$v] = lang($v);
			}

			break;

		case 'countries':

			$a = $CI->country->load_countries_array(FALSE, TRUE);

			break;

		case 'countries_iso_code_3':

			$a = $CI->country->load_countries_array(FALSE, TRUE, FALSE, 'country_iso_code_3');

			break;


		case 'affiliate_groups':

			$a = $CI->aff_group->get_affiliate_groups();

			break;

		case 'enable':

			$a = array(0 => lang('disable'), 1 => lang('enable'));

			break;

		case 'zone_calc':

			$a = array('price' => lang('price'), 'weight' => lang('weight'));

			break;

		case 'numbers_5':

			for ($i = 1; $i <= 5; $i++)
			{
				$a[$i] = $i;
			}

			break;

		case 'numbers_10':

			for ($i = 1; $i <= 10; $i++)
			{
				$a[$i] = $i;
			}

			break;

		case 'matrix_width':

			for ($i = 1; $i <= 5; $i++)
			{
				$a[$i] = $i;
			}

			break;

		case 'numbers_30':

			for ($i = 1; $i <= 30; $i++)
			{
				$a[$i] = $i;
			}

			break;

		case 'site_addresses':

			$a = $CI->set->get_site_addresses(TRUE);

			break;

		case 'username_generator':

			foreach ($CI->config->item('random_username_types') as $f)
			{
				$a[$f] = lang($f);
			}

			break;

		case 'timezone_menu':

			$CI->lang->load('date');

			foreach (timezones() as $key => $val)
			{
				$a[$key] = $CI->lang->line($key);
			}

			break;

		case 'order_status':

			$q = $CI->db->get(TBL_ORDERS_STATUS);
			$a = format_array($q->result_array(), 'order_status_id', 'order_status');

			break;

		case 'mailing_lists':

			$q = $CI->db->get(TBL_EMAIL_MAILING_LISTS);
			$a = format_array($q->result_array(), 'list_id', 'list_name', TRUE, 'none');

			break;

		case 'username_length':

			for ($i = 4; $i < 10; $i++)
			{
				$a[$i] = $i;
			}

			break;

		case 'commission_levels':

			if ($CI->config->item('max_commission_levels'))
			{
				for ($i = 1; $i <= $CI->config->item('max_commission_levels'); $i++)
				{
					$a[$i] = $i;
				}
			}
			else
			{
				$a[1] = 1;
			}

			break;

		case 'affiliate_group':

			$q = $CI->db->get('affiliate_groups');
			$a = format_array($q->result_array(), 'group_id', 'aff_group_name');

			break;

		case 'discount_group':

			$q = $CI->db->get('discount_groups');
			$a = format_array($q->result_array(), 'group_id', 'group_name');

			break;

		case 'blog_group':

			$q = $CI->db->get('blog_groups');
			$a = format_array($q->result_array(), 'group_id', 'group_name');

			break;

		case 'countries':

			$CI->load->helper('country');

			$a = load_countries_dropdown($countries = $CI->country->load_countries_array());

			break;

		case 'currencies':

			$q = $CI->db->get(TBL_CURRENCIES);
			$a = format_array($q->result_array(), 'code', 'title');

			break;

		case 'language':
			$a = get_languages();

			break;

		case 'mailing_list':

			$q = $CI->db->get('email_mailing_lists');
			$a = format_array($q->result_array(), 'list_id', 'list_name', TRUE, 'none');

			break;

		case 'theme':

			$q = $CI->db->get('layout_themes');
			$a = format_array($q->result_array(), 'template_id', 'template_name');

			break;

		case 'weight':

			$a = $CI->weight->get_weight_options(TRUE);

			break;

		case 'tax_classes':

			$q = $CI->db->get(TBL_TAX_CLASSES);

			$a = format_array($q->result_array(), 'tax_class_id', 'class_name');

			break;

		case 'hours':

			$a = array();
			for ($h = 1; $h <= 23; $h++)
			{
				$i = date('H', mktime($h, 0, 0, date('m'), date('d'), date('Y')));

				$a[$i] = $i;
			}

			break;

		case 'minutes':

			$j = '0';

			$a = array();

			while ($j <= '55')
			{
				if ($j < 10)
				{
					$j = '0' . $j;
				}

				$a[$j] = $j;

				$j = $j + 5;
			}

			break;

		default:

			if ($CI->config->item($array))
			{
				foreach ($CI->config->item($array) as $f)
				{
					$a[$f] = lang($f);
				}
			}

			break;
	}

	//use a cutom array if specified
	if (!empty($use_array))
	{
		$a = $use_array;
	}

	if ($default == 'none')
	{
		$a['0'] = lang('none');
	}

	if ($CI->sec->check_admin_permissions(CONTROLLER_CLASS, 'delete') == TRUE)
	{
		if ($default == 'delete')
		{
			$a['delete'] = lang('deleted');
		}
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @return bool
 */
function check_the_box($type = '', $data = array())
{
	switch ($type)
	{
		case 'subscribe':

			//see if we need to check for checkbox confirmation
			if (config_enabled('sts_form_enable_list_subscribe_checkbox'))
			{
				if (empty($data[$type]))
				{
					return FALSE;
				}
			}

			break;
	}

	return TRUE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $field
 * @param string $type
 * @return mixed|string
 */
function check_order_field($data = array(), $field = '', $type = 'billing')
{
	$a = '';

	switch ($field)
	{
		case 'name':

			$a = $data['fname'] . ' ' . $data['lname'];
			if (!empty($data[$type . '_fname']) && !empty($data[$type . '_lname']))
			{
				$a = $data[$type . '_fname'] . ' ' . $data[$type . '_lname'];
			}

			break;

		case 'billing_address_name':

			$a = $data['fname'] . ' ' . $data['lname'];
			if (!empty($data['billing_address_fname']) && !empty($data['billing_address_lname']))
			{
				$a = $data[$type . '_fname'] . ' ' . $data[$type . '_lname'];
			}

			break;

		default:
			if (!empty($data[$type . '_' . $field]))
			{
				$a = $data[$type . '_' . $field];
			}
			elseif (!empty($data[$field]))
			{
				$a = $data[$field];
			}

			break;
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $value
 * @param string $attributes
 * @return array|string
 */
function generate_custom_field($data = array(), $value = '', $attributes = '')
{
	//set the field name
	$field = 'custom-' . $data['custom_field_id'];

	switch ($data['field_type'])
	{
		case 'textarea':

			return form_textarea($field, $value, $attributes);

			break;

		case 'select':

			$a = explode("\n", $data['field_options']);

			$options = array();

			foreach ($a as $v)
			{
				$options[$v] = lang($v);
			}

			return form_dropdown($field, $options, $value, $attributes);

			break;

		case 'radio':

			$a = explode("\n", $data['field_options']);

			$options = '';

			foreach ($a as $v)
			{
				$checked = $v == $value ? TRUE : FALSE;
				$options .= '<div class="radio_field">' . form_radio($field, $v, $checked, $attributes) . ' ' . form_label(lang($v), $v) . '</div>';
			}

			return $options;

			break;

		case 'checkbox':

			$a = explode("\n", $data['field_options']);

			$options = '';

			foreach ($a as $v)
			{
				$checked = $v == $value ? TRUE : FALSE;
				$options .= '<div class="checkbox_field">' . form_checkbox($field, $v, $checked, $attributes) . ' ' . form_label(lang($v), $v) . '</div>';
			}

			return $options;

			break;

		case 'file':

			$a = form_upload($field, $value, 'class="btn btn-default"');
			if (!empty($value))
			{
				$a = anchor($value, i('fa fa-download'), 'class="btn btn-primary pull-right"') . $a;
			}

			return $a;

			break;

		default: //text

			return form_input($data['form_field'], $value, $attributes);

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $options
 * @return array
 */
function format_custom_options($options = '')
{
	$del = "\n";

	$a = explode($del, $options);
	$opts = array();
	foreach ($a as $v)
	{
		$opts[$v] = lang($v);
	}

	return $opts;
}

// ------------------------------------------------------------------------

/**
 * @param string $a
 * @param string $b
 * @param string $c
 * @return array
 */
function bool($a = '', $b = '', $c = '')
{
	$options = array('1' => lang($a), '0' => lang($b));

	if (!empty($c))
	{
		$options['2'] = $c;
	}

	return $options;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $id
 * @param int $level
 * @param int $parent_id
 * @return string
 */
function select_children($data = array(), $id = '0', $level = 1, $parent_id = 0)
{
	$array = array_match_values($data, 'parent_id', $id);

	$indent = (!empty($id)) ? str_repeat('&nbsp;', $level * 2) : '&nbsp;';

	$tree = '';

	if (count($array) > 0)
	{
		foreach ($array as $value)
		{
			if ($parent_id == $value['category_id'])
			{
				$tree .= "<option value='" . $value['category_id'] . "' selected='selected'>" . $indent . $value['category_name'] . "</option>\n";
			}
			else
			{
				$tree .= "<option value='" . $value['category_id'] . "'>" . $indent . $value['category_name'] . "</option>\n";
			}

			$tree .= select_children($data, $value['category_id'], $level + 1, $parent_id);
		}
	}

	return $tree;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param string $field
 * @param array $data
 * @return bool|mixed
 */
function set_form_default($type = '', $field = '', $data = array())
{

	if (!empty($data['addresses']))
	{
		$field = str_replace($type . '_', '', $field);

		foreach ($data['addresses'] as $v)
		{
			if ($v[$type . '_default'] == 1)
			{
				if (!empty($v[$field]))
				{
					return $v[$field];
				}
			}
		}
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $f
 * @param array $data
 * @return bool|mixed
 */
function show_user_field($f = '', $data = array(), $field = 'field')
{
	foreach ($data as $k => $v)
	{
		if ($f == $v['form_field'])
		{
			return $v[$field];
		}
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $row
 * @param array $data
 * @param string $form_type
 * @return array
 */
function format_user_fields($row = array(), $data = array(), $form_type = '')
{
	foreach ($row as $k => $v)
	{
		//check required value
		$required = $v['field_required'] == 1 ? $form_type == 'admin' ? '' : 'required' : '';

		//check for default value
		if (!empty($data[$v['form_field']]))
		{
			$v['field_value'] = $data[$v['form_field']];
			$row[$k]['field_value'] = $data[$v['form_field']];
		}

		if ($form_type == 'admin') //make sure we can see it in the admin area?
		{
			if ($v['field_type'] == 'password' || $v['field_type'] == 'hidden')
			{
				$v['field_type'] = 'text';
			}
		}

		//set the field
		$field_id = $v['form_field'];
		$field_name = $form_type == 'admin' ? 'form_field[' . $v['field_id'] . '][field_value]' : $v['form_field'];

		switch ($v['field_type'])
		{
			case 'select':

				if ($v['field_validation'] == 'country')
				{
					$id = str_replace('_country', '', $field_id) . '_state';
					$row[$k]['field_value_name'] = get_country_name($v['field_value'], 'country_name');
					$row[$k]['field'] = form_dropdown($field_name, array($v['field_value'] => $row[$k]['field_value_name']), '', 'onchange="updateregion(\'' . $id . '\' , \'' . $v['sub_form'] . '\')" id="' . $field_id . '" class="' . $v['sub_form'] . '  country_id form-control ' . $required . '"');
				}
				elseif ($v['field_validation'] == 'region')
				{
					$r = get_region_name($v['field_value']);
					$row[$k]['field_value_name'] = $r['region_name'];
					$row[$k]['field'] = form_dropdown($field_name, load_regions($r['region_country_id']), '', 'id="' . $field_id . '" class="' . $v['sub_form'] . ' form-control ' . $required . '"');
				}
				else
				{
					$val_array = format_custom_options($v['field_options']);
					$row[$k]['field'] = form_dropdown($field_name, $val_array, $v['field_value'], 'class="' . $v['sub_form'] . ' form-control ' . $required . '" id="' . $field_id . '"');
				}

				break;

			case 'radio':

				$val_array = format_custom_options($v['field_options']);
				$row[$k]['field'] = '';
				foreach ($val_array as $a)
				{
					$checked = !empty($v['field_value']) ? TRUE : FALSE;

					$row[$k]['field'] .= '<div class="form-check">' . form_radio($field_name, $a, $checked, 'id="' . $field_id . '" class="' . $v['sub_form'] . ' form-check-input ' . $required . '"') . ' ' . form_label(lang($a), $a, 'class="form-check-label"') . '</div>';
				}

				break;

			case 'checkbox':

				$checked = strlen($v['field_value'] > 0) ? TRUE : FALSE;

				$row[$k]['field'] = '<div class="form-check">' . form_checkbox($field_name, $v['field_value'], $checked, 'id="' . $field_id . '" class="' . $v['sub_form'] . ' form-check-input ' . $required . '"') . ' ' . form_label(lang($field_id), $field_id, 'class="form-check-label"') . '</div>';

				break;

			case 'textarea':

				$row[$k]['field'] = form_textarea($field_name, $v['field_value'], 'placeholder="' . lang($v['form_field']) . '" id="' . $field_id . '" class="' . $v['sub_form'] . ' form-control ' . $required . '"');

				break;

			case 'password':

				$row[$k]['field'] = form_password($field_name, $v['field_value'], 'placeholder="' . lang($v['form_field']) . '" id="' . $field_id . '" class="' . $v['sub_form'] . ' form-control ' . $required . '"');

				break;

			case 'hidden':

				$row[$k]['field'] = form_hidden($field_name, $v['field_value'], 'id="' . $v['form_field'] . '" class="' . $v['sub_form'] . ' form-control ' . $required . '"');

				break;

			case 'file':

				$row[$k]['field'] = '<label class="file">' . form_upload($field_name, '', 'id="' . $v['form_field'] . '" class="' . $v['sub_form'] . ' file' . $required . '"') . '<span class="file-custom"></span></label>';

				break;

			case 'date':

				if ($form_type == 'admin')
				{
					$row[$k]['field'] = form_input($field_name, $v['field_value'], 'placeholder="' . lang($v['form_field']) . '" id="' . $field_id . '" class="' . $v['sub_form'] . ' form-control ' . $required . ' ' . $v['field_validation'] . '"');
				}
				else
				{
					$row[$k]['field'] = '<div class="input-group">' . form_input($field_name, $v['field_value'], 'placeholder="' . lang($v['form_field']) . '" id="' . $field_id . '" class="' . $v['sub_form'] . ' datepicker-input form-control ' . $required . ' ' . $v['field_validation'] . '" ' . FIELD_READ_ONLY) . '<div class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div></div>';
				}

				break;

			default: //text field

				$row[$k]['field'] = form_input($field_name, $v['field_value'], 'placeholder="' . $v['field_description'] . '" id="' . $field_id . '" class="' . $v['sub_form'] . ' form-control ' . $required . ' ' . $v['field_validation'] . '"');

				break;
		}
	}

	return $row;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @param array $merge
 * @return array
 */
function order_member_data($type = 'account', $data = array(), $merge = array())
{

	switch ($type)
	{
		case 'account':

			$vars = array(
				'member_id'     => sess('member_id'),
				'fname'         => $data['fname'],
				'lname'         => $data['lname'],
				'primary_email' => $data['primary_email'],
			);

			break;

		case 'shipping':

			if (!empty($data))
			{
				//get country data
				$country = get_country_name($data['country']);
				$region = get_region_name($data['state']);

				$vars = array(

					'fname'                       => $data['fname'],
					'lname'                       => $data['lname'],
					'shipping_address_1'          => $data['address_1'],
					'shipping_address_2'          => $data['address_2'],
					'shipping_city'               => $data['city'],
					'shipping_state'              => $data['state'],
					'shipping_state_name'         => $region['region_name'],
					'shipping_state_code'         => $region['region_code'],
					'shipping_country'            => $data['country'],
					'shipping_country_name'       => $country['country_name'],
					'shipping_country_iso_code_2' => $country['country_iso_code_2'],
					'shipping_country_iso_code_3' => $country['country_iso_code_3'],
					'shipping_postal_code'        => $data['postal_code'],
				);
			}
			else
			{
				$vars = $merge;

				//get country data
				$country = get_country_name($vars['shipping_country']);
				$region = get_region_name($vars['shipping_state']);

				$vars['shipping_state_name'] = $region['region_name'];
				$vars['shipping_state_code'] = $region['region_code'];
				$vars['shipping_country_name'] = $country['country_name'];
				$vars['shipping_country_iso_code_2'] = $country['country_iso_code_2'];
				$vars['shipping_country_iso_code_3'] = $country['country_iso_code_3'];
			}

			break;

		case 'billing':

			if (!empty($data))
			{
				//get country data
				$country = get_country_name($data['country']);
				$region = get_region_name($data['state']);

				$vars = array(

					'billing_address_id'         => !empty($data['id']) ? $data['id'] : '0',
					'billing_fname'              => is_var($data, 'fname'),
					'billing_lname'              => is_var($data, 'lname'),
					'billing_address_1'          => is_var($data, 'address_1'),
					'billing_address_2'          => is_var($data, 'address_2'),
					'billing_city'               => is_var($data, 'city'),
					'billing_state'              => is_var($data, 'state'),
					'billing_state_name'         => is_var($region, 'region_name'),
					'billing_state_code'         => is_var($region, 'region_code'),
					'billing_country'            => is_var($data, 'country'),
					'billing_country_name'       => is_var($country, 'country_name'),
					'billing_country_iso_code_2' => is_var($country, 'country_iso_code_2'),
					'billing_country_iso_code_3' => is_var($country, 'country_iso_code_3'),
					'billing_postal_code'        => is_var($data, 'postal_code'),
				);
			}
			else
			{
				$vars = $merge;

				//get country data
				$country = get_country_name($vars['billing_country']);
				$region = get_region_name($vars['billing_state']);

				$vars['billing_state_name'] = is_var($region, 'region_name');
				$vars['billing_state_code'] = is_var($region, 'region_code');
				$vars['billing_country_name'] = is_var($country, 'country_name');
				$vars['billing_country_iso_code_2'] = is_var($country, 'country_iso_code_2');
				$vars['billing_country_iso_code_3'] = is_var($country, 'country_iso_code_3');
			}

			break;
	}

	return !empty($merge) ? array_merge($merge, $vars) : $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @return string
 */
function form_select_address($data = array(), $type = 'shipping')
{
	$vars = array();
	foreach ($data as $v)
	{
		$vars[$v['id']] = $v['fname'] . ' ' . $v['lname'] . ' - ' . $v['address_1'] . ' ' . $v['address_2'] . ' ' . $v['city'] . ' ' . $v['region_code'] . ' ' . $v['postal_code'];
	}

	$vars[0] = lang('add_new_' . $type . '_address');

	return form_dropdown($type . '_address_id', $vars, '', 'class="form-control" id="' . $type . '_address_id"');
}

/**
 * Format errors
 *
 * Format error messages into HTML format using the
 * standard error prefix and suffix for Codeigniter
 *
 * @param array $data
 *
 * @return string
 */

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function format_errors($data = array())
{
	$CI = &get_instance();

	$msg = '';

	foreach ($data as $v)
	{
		$msg .= $CI->config->item('error_prefix') . $v . $CI->config->item('error_suffix');
	}

	return $msg;
}

// ------------------------------------------------------------------------

/**
 * @param string $data
 * @return array
 */
function generate_error_fields($data = '')
{
	$CI = &get_instance();

	$vars = !empty($data) ? $data : $CI->input->post();

	$row = array();

	if ($vars)
	{
		foreach ($vars as $k => $v)
		{
			if (strlen(form_error($k)) > 0)
			{
				$row[$k] = form_error($k);
			}
		}
	}

	return $row;
}

/**
 * Initialize sub forms
 *
 * Initialize form fields into different sub forms for
 * account, billing, shipping, and payment
 *
 * @param array $data
 *
 * @return array
 */

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function init_sub_forms($data = array())
{
	$forms = array('account'  => array(),
	               'billing'  => array(),
	               'shipping' => array(),
	               'payment'  => array(),
	);

	if (!empty($data['values']))
	{
		//add each form field to the correct sub form array
		foreach ($data['values'] as $v)
		{
			if (empty($v['sub_form']))
			{
				array_push($forms['account'], $v);
			}
			else
			{
				array_push($forms[$v['sub_form']], $v);
			}
		}
	}

	return $forms;
}

/* End of file JX_form_helper.php */
/* Location: ./application/helpers/JX_form_helper.php */