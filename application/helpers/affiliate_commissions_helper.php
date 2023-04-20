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
 * @param int $amount
 * @param string $rate
 * @param string $type
 * @return float|int|string
 */
function calculate_commission($amount = 0, $rate = '0', $type = 'flat')
{
	if ($type == 'flat')
	{
		return $rate;
	}

	return $amount * show_percent($rate);
}

// ------------------------------------------------------------------------

/**
 * @return string
 */
function check_default_comm_status()
{
	//set the commission status
	switch (config_option('sts_affiliate_new_commission'))
	{
		case 'no_unpaid':
		case 'alert_unpaid':

			return 'unpaid';

			break;

		default:

			return 'pending';

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $amount
 * @param array $data
 * @return float|int|mixed
 */
function calculate_comm_fee($amount = '0', $data = array())
{
	$fee = 0;

	if (defined('AFFILIATE_MARKETING_CHARGE_FEES'))
	{
		if ($data['enable_fees'] == 1)
		{
			$fee = $data['fee_type'] == 'percent' ? $amount * show_percent($data['fee_amount']) : $data['fee_amount'];
		}
	}

	return $fee;
}

// ------------------------------------------------------------------------

/**
 * @return bool
 */
function check_upline_config()
{
	if (config_option('max_commission_levels')) //network config
	{
		if (config_option('sts_affiliate_commission_levels') > 1)
		{
			return TRUE;
		}
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @return array
 */
function format_signup_bonus($data = array(), $type = '')
{
	$CI = &get_instance();

	$vars = array(
		'member_id'         => $type == 'referral_' ? $data['original_sponsor_id'] : $data['member_id'],
		'invoice_id'        => '',
		'comm_status'       => check_default_comm_status(),
		'approved'          => '0',
		'date'              => get_time(now(), TRUE),
		'commission_amount' => config_item('sts_affiliate_enable_' . $type . 'signup_bonus_amount'),
		'sale_amount'       => 0,
		'fee'               => 0,
		'commission_level'  => 1,
		'referrer'          => '',
		'trans_id'          => lang($type . 'signup_bonus_amount'),
		'ip_address'        => $CI->input->ip_address(),
		'date_paid'         => '0000-00-00 00:00:00',
		'commission_notes'  => '',
		'performance_paid'  => '0',
		'tool_type'         => '',
		'tool_id'           => '0',
	);

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param string $level
 * @param string $amount
 * @param string $member
 * @param array $data
 * @return array
 */
function format_commission_data($level = '1', $amount = '0', $member = '', $data = array())
{
	$CI = &get_instance();


	$vars = array(
		'member_id'         => $member['member_id'],
		'invoice_id'        => check_invoice($data),
		'comm_status'       => check_default_comm_status(),
		'approved'          => '0',
		'date'              => get_time(now(), TRUE),
		'commission_amount' => calculate_commission($amount, $member['commission_level_' . $level], $member['commission_type']),
		'sale_amount'       => $amount,
		'fee'               => calculate_comm_fee($amount, $member),
		'commission_level'  => $level,
		'referrer'          => '',
		'trans_id'          => check_trans_id($data),
		'ip_address'        => $CI->input->ip_address(),
		'date_paid'         => '0000-00-00 00:00:00',
		'commission_notes'  => '',
		'performance_paid'  => '0',
		'tool_type'         => '',
		'tool_id'           => '0',
	);

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return mixed|string
 */
function check_invoice($data = array())
{
	$id = '';

	if (!empty($data['invoice']['id']))
	{
		$id = $data['invoice']['id'];
	}
	elseif (!empty($data['invoice_id']))
	{
		$id = $data['invoice_id'];
	}

	return $id;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return mixed|string
 */
function check_trans_id($data = array())
{
	$id = '';

	if (!empty($data['transaction']['transaction_id']))
	{
		$id = $data['transaction']['transaction_id'];
	}
	elseif (!empty($data['trans_id']))
	{
		$id = $data['trans_id'];
	}

	return $id;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param array $rule
 * @return array
 */
function format_rule_comm($data = array(), $rule = array())
{
	//format the new commission to be added by the rule
	$CI = &get_instance();

	$vars = array(
		'member_id'         => is_var($data, 'member_id'),
		'invoice_id'        => '0',
		'comm_status'       => check_default_comm_status(),
		'approved'          => '0',
		'date'              => get_time(now(), TRUE),
		'commission_amount' => $rule['bonus_amount'],
		'sale_amount'       => '0',
		'fee'               => is_var($data, 'fee'),
		'commission_level'  => is_var($data, 'commission_level'),
		'referrer'          => '',
		'trans_id'          => lang('commission_bonus_amount'),
		'ip_address'        => $CI->input->ip_address(),
		'date_paid'         => '0000-00-00 00:00:00',
		'commission_notes'  => is_var($data, 'trans_id'),
		'performance_paid'  => '0',
		'tool_type'         => '',
		'tool_id'           => '0',
	);

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function get_comm_rule($data = array())
{
	$CI = &get_instance();

	$rule = '<div class="text-capitalize">';

	switch ($data['sale_type'])
	{
		case 'total_amount_of_commissions':
		case 'total_amount_of_sales':
		case 'total_amount_of_referrals':

			$rule .= lang('if') . ' <span class="rewardLinks">' . lang($data['sale_type']) . '</span> ' . lang('is') . ' <span class="rewardLinks">' . lang($data['operator']) . ' ' . $data['sale_amount'] . '</span> ' . lang('for') . ' <span class="rewardLinks">' . lang($data['time_limit']) . '</span> ' . lang('then') . ' <span class="rewardLinks">' . lang($data['action']) . ' ';

			break;

		default:

			$rule .= lang('if') . ' <span class="rewardLinks">' . lang($data['sale_type']) . ' ' . lang('is') . ' ' . lang($data['operator']) . ' ' . $data['sale_amount'] . '</span> ' . lang('then') . ' <span class="rewardLinks">' . lang($data['action']) . ' ';

			break;
	}

	if ($data['action'] == 'assign_affiliate_group')
	{
		$row = $CI->aff_group->get_details($data['group_id']);
		$rule .= $row['aff_group_name'];
	}
	else
	{
		$rule .= $data['bonus_amount'];
	}

	$rule .= '</div>';

	return $rule;
}

/* End of file affiliate_commissions_helper.php */
/* Location: ./application/helpers/affiliate_commissions_helper.php */