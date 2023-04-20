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
 * @return bool
 */
function update_timestamp($data = array())
{
	$CI = &get_instance();

	$vars = array($data['field'] => get_time('', TRUE));

	if (!$CI->db->where($data['key'], $data['value'])
		->update($data['table'], $vars)
	)
	{
		get_error(__FILE__, __METHOD__, __LINE__);
	}

	return TRUE;
}

// ------------------------------------------------------------------------

/**
 * @param string $time
 * @param bool $mysql
 * @return false|int|string
 */
function get_time($time = '', $mysql = FALSE, $local = TRUE)
{
	if (empty($time))
	{
		$time = now();
	}

	$dst = config_item('sts_site_use_daylight_savings_time') == '1' ? TRUE : FALSE;

	$t = $local == TRUE ? gmt_to_local($time, config_item('sts_site_default_timezone'), $dst) : $time;

	return !empty($mysql) ? date('Y-m-d H:i:s', $t) : $t;
}

// ------------------------------------------------------------------------

/**
 * @param string $stamp
 * @param bool $unix
 * @return false|string
 */
function display_time($stamp = '', $unix = FALSE)
{
	$a = 'h:i:s A';

	$stamp = $unix == FALSE ? mysql_to_unix($stamp) : $stamp;

	return date($a, $stamp);
}

// ------------------------------------------------------------------------

/**
 * @param string $stamp
 * @param bool $mysql
 * @return false|string
 */
function local_date($stamp = '', $mysql = TRUE)
{
	$stamp = $mysql == TRUE ? mysql_to_unix($stamp) : $stamp;

	$dst = config_item('sts_site_use_daylight_savings_time') == '1' ? TRUE : FALSE;

	$t = gmt_to_local($stamp, config_item('sts_site_default_timezone'), $dst);

	return display_date($t, TRUE, '3', TRUE);
}

// ------------------------------------------------------------------------

/**
 * @param string $stamp
 * @param bool $time
 * @param string $type
 * @param bool $unix
 * @return false|string
 */
function display_date($stamp = '', $time = FALSE, $type = '', $unix = FALSE)
{
	$CI = &get_instance();

	$stamp = (empty($stamp)) ? get_time() : $stamp;
	$stamp = $unix == FALSE ? mysql_to_unix($stamp) : $stamp;

	switch ($type)
	{
		case '1':

			$a = $CI->config->item('format_date'); //mm/dd/yyyy

			break;

		case '2':

			$a = $CI->config->item('format_date2'); //m/d/Y

			break;

		default:

			$a = $CI->config->item('format_date3'); //M d Y

			//return lang(date('M', $stamp)) . ' ' . date('d Y', $stamp);

			break;
	}

	if ($time == TRUE)
	{
		$a .= ' h:i:s A';
	}

	return date($a, $stamp);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @return false|string
 */
function get_next_due_date($data = array(), $type = 'add')
{
	//pr($data);
	if ($type == 'add' && !empty($data['enable_initial_amount']))
	{
		$int = $data['initial_interval'];
		$type = $data['initial_interval_type'];
	}
	else
	{
		$int = $data['interval_amount'];
		$type = $data['interval_type'];
	}

	$t = !empty($data['next_due_date'])? mysql_to_unix($data['next_due_date']) : get_time();

	$date = get_time();

	switch ($type)
	{
		case 'day':

			$date =   mktime(23, 59, 59, date('m', $t), date('d', $t) + ($int), date('Y', $t));

			break;

		case 'week':

			$date =   mktime(23, 59, 59, date('m', $t), date('d', $t) + ($int * 7), date('Y', $t));

			break;

		case 'month':

			$date =   mktime(23, 59, 59, date('m', $t) + $int, date('d', $t), date('Y', $t));

			break;

		case 'year':

			$date =   mktime(23, 59, 59, date('m', $t), date('d', $t), date('Y', $t) + $int);

			break;
	}

	return date('Y-m-d h:i:s A', $date);
}

// ------------------------------------------------------------------------

/**
 * @param string $format
 * @param string $m
 * @param string $d
 * @param string $y
 * @return false|string
 */
function current_date($format = 'M d Y', $m = '', $d = '', $y = '')
{
	//set the month
	if (empty($m))
	{
		$m = !empty($_GET['month']) ? (int)$_GET['month'] : date('m', get_time());
	}

	//set the day
	if (empty($d))
	{
		$d = date('d', get_time());
	}

	//set the year
	if (empty($y))
	{
		$y = !empty($_GET['year']) ? (int)$_GET['year'] : date('Y', get_time());
	}

	return date($format, mktime(12, 0, 0, $m, $d, $y));
}

// ------------------------------------------------------------------------

/**
 * @param string $select
 * @param string $class
 * @param string $onchange
 * @return string
 */
function generate_month_dropdown($select = '', $class = 'form-control', $onchange = 'updateReportDate(this)')
{
	$CI =& get_instance();

	$options = array();

	$total_years = $CI->config->item('dbr_total_report_years');

	$current_year = date('Y');

	$last_year = $current_year - $total_years;

	for ($i = $current_year; $i > $last_year; $i = $i - '1')
	{
		for ($j = 12; $j >= 1; $j = $j - '1')
		{
			$a = date('m', mktime(0, 0, 0, $j, '1', $i));
			$b = date('Y', mktime(0, 0, 0, $j, '1', $i));
			$c = date('F Y', mktime(0, 0, 0, $j, '1', $i));

			$opt = '?month=' . $a . '&year=' . $b;
			$options[ $opt ] = $c;
		}

		$j = 1;
	}

	$selected = '?month=' . current_date('m') . '&year=' . current_date('Y');

	return form_dropdown('change_status', $options, $selected, 'class="' . $class . '" onchange="' . $onchange . '"');
}

// ------------------------------------------------------------------------

/**
 * @param string $select
 * @param string $class
 * @param string $onchange
 * @return string
 */
function generate_year_dropdown($select = '', $class = 'form-control', $onchange = 'updateReportDate(this)')
{
	$CI =& get_instance();

	$options = array();

	$total_years = $CI->config->item('dbr_total_report_years');

	$current_year = date('Y');

	$last_year = $current_year - $total_years;

	for ($i = $current_year; $i > $last_year; $i = $i - '1')
	{
		$b = date('Y', mktime(0, 0, 0, 1, '1', $i));

		$opt = '?year=' . $b;
		$options[ $opt ] = $b;
	}

	$selected = '?year=' . current_date('Y');

	return form_dropdown('change_status', $options, $selected, 'class="' . $class . '" onchange="' . $onchange . '"');
}

// ------------------------------------------------------------------------

/**
 * @param string $time
 * @param bool $mysql
 * @return string
 */
function calculate_time($time = '', $mysql = FALSE)
{

	$now = get_time();

	$time = $mysql == TRUE ? strtotime($time) : $time;

	$a = $now - $time;

	//now check if time is in minutes, hours or days
	if ($a < 60)
	{
		$b = round($a);
		$c = $b < 2 ? lang('second') : lang('seconds');
	}
	elseif ($a < 3600)
	{
		//minutes
		$b = $a / 60;
		$b = round($b);
		$c = $b < 2 ? lang('minute') : lang('minutes');
	}
	elseif ($a < 86400)
	{
		//hours
		$b = $a / 3600;
		$b = round($b);
		$c = $b < 2 ? lang('hour') : lang('hours');
	}
	else
	{
		//days
		$b = $a / 86400;
		$b = round($b);
		$c = $b < 2 ? lang('day') : lang('days');
	}

	return '<span>' . $b . ' ' . $c . ' ' . lang('ago') . '</span>';
}

// ------------------------------------------------------------------------

/**
 * @param string $start
 * @param string $end
 * @param string $format
 * @return array
 * @throws Exception
 */
function date_range($start = '', $end = '', $format = 'Y-m-d')
{

	$datetime1 = new DateTime($start);
	$datetime2 = new DateTime($end);
	$interval = $datetime1->diff($datetime2);
	$days = $interval->format('%a');

	$dates = array();
	for ($i = 1; $i <= $days; $i++)
	{
		$dates[date('m-d', strtotime("+ $i day", strtotime($start)))] = date($format, strtotime("+ $i day", strtotime($start)));
	}

	return $dates;
}

// ------------------------------------------------------------------------

/**
 * @param string $format
 * @return string
 */
function local_time($format = '')
{
	return 'CURDATE()';
}

// ------------------------------------------------------------------------

/**
 * @param bool $format
 * @return false|float|int|string
 */
function default_due_date($format = FALSE)
{
	$CI = &get_instance();

	$time = get_time() + 60 * 60 * 24 * $CI->config->item('sts_invoice_due_date_days');

	return $format == TRUE ? display_date($time) : $time;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return false|string
 */
function check_import_date($str = '')
{
	$a = str_replace(array('-', '.'), '/', $str);

	return day_to_sql($a);
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return false|string
 */
function check_import_start_time($str = '')
{
	$a = str_replace(array('-', '.'), '/', $str);

	return start_date_to_sql($a);
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return false|string
 */
function check_import_end_time($str = '')
{
	$a = str_replace(array('-', '.'), '/', $str);

	return end_date_to_sql($a);
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function generate_cc_months()
{
	$data = array();
	for ($i = 1; $i <= 12; $i++)
	{
		$m = date('m', mktime(0, 0, 0, $i, 10, date('Y')));
		$data[ $m ] = date('M (m)', mktime(0, 0, 0, $i, 10, date('Y')));
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param bool $two_digits
 * @return array
 */
function generate_cc_years($two_digits = FALSE)
{
	$data = array();

	for ($i = date('Y'); $i < (date('Y') + 20); $i++)
	{
		if ($two_digits == TRUE)
		{
			$j = $i - '2000';
		}
		else
		{
			$j = $i;
		}
		$data[ $j ] = $i;
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return false|string
 */
function day_to_sql($str = '')
{
	//return sql formatted year, day and month only, no time.
	return date_to_sql($str, '', TRUE);
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return false|string
 */
function start_date_to_sql($str = '')
{
	return date_to_sql($str, '00:00:00');
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return false|string
 */
function end_date_to_sql($str = '')
{
	return date_to_sql($str, '23:59:59');
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @param string $time
 * @param bool $date_only
 * @return false|string
 */
function date_to_sql($str = '', $time = '', $date_only = FALSE)
{
	switch (config_option('curdate'))
	{
		case 'mm/dd/yy':
			list($m, $d, $y) = explode('/', $str);
			break;

		case 'dd/mm/yy':
			list($d, $m, $y) = explode('/', $str);
			break;

		case 'yy/mm/dd':
			list($y, $d, $m) = explode('/', $str);
			break;
	}

	$d = $y . '-' . $m . '-' . $d;

	if ($date_only == TRUE) //returns date only, no time
	{
		return $d;
	}

	if (empty($time))
	{
		$time = date('H:i:s', now());
	}

	list($h, $i, $s) = explode(':', $time);

	$d .= ' ' . $h . ':' . $i . ':' . $s;

	$a = date($d, strtotime($d));

	return $a;
}

/* End of file JX_date_helper.php */
/* Location: ./application/helpers/JX_date_helper.php */