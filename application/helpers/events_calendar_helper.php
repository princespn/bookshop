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

// ------------------------------------------------------------------------

/**
 * @return string
 */
function event_date()
{
	$m = uri(5, date('m'));
	$y = uri(4, date('Y'));
	$d = uri(6, date('d'));

	switch (config_option('curdate'))
	{
		case 'mm/dd/yy':

			return $m . '/' . $d . '/' . $y;

			break;

		case 'dd/mm/yy':

			return $d . '/' . $m . '/' . $y;

			break;

		case 'yy/mm/dd':

			return $y . '/' . $m . '/' . $d;

			break;
	}

}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_cell_data($data = array())
{
	foreach ($data as $k => $v)
	{
		if ($v == 0)
		{
			$data[$k] = '';
		}
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 */
function init_calendar($type = 'calendar')
{
	$CI =& get_instance();

	if ($type == 'reports')
	{
		$prefs = array(
			'day_type'       => 'short',
			'show_next_prev' => FALSE,
		);

		$prefs['template'] = '
	   {table_open}<table border="0" cellpadding="4" cellspacing="1" width="100%" class="calendar">{/table_open}
	
	   {heading_row_start}<tr>{/heading_row_start}
	
	   {heading_previous_cell}<th class="text-center"></th>{/heading_previous_cell}
	   {heading_title_cell}<th colspan="{colspan}" class="text-center"><h4>{heading}</h4></th>{/heading_title_cell}
	   {heading_next_cell}<th class="text-center"></th>{/heading_next_cell}
	
	   {heading_row_end}</tr>{/heading_row_end}
	
	   {week_row_start}<tr>{/week_row_start}
	   {week_day_cell}<td class="week"><strong>{week_day}</strong></td>{/week_day_cell}
	   {week_row_end}</tr>{/week_row_end}
	
	   {cal_row_start}<tr>{/cal_row_start}
	   {cal_cell_start}<td>{/cal_cell_start}
	
	   {cal_cell_content}<div class="day">{day}</div><div class="cal-content"><span class="badge badge-pill badge-secondary">{content}</span></div>{/cal_cell_content}
	   {cal_cell_content_today}<div class="day">{day}</div><div class="cal-content"><span class="badge badge-pill badge-primary">{content}</span></div>{/cal_cell_content_today}
	
	   {cal_cell_no_content}<div class="day">{day}</div>{/cal_cell_no_content}
	   {cal_cell_no_content_today}<div class="day">{day}</div>{/cal_cell_no_content_today}
	
	   {cal_cell_blank}&nbsp;{/cal_cell_blank}
	
	   {cal_cell_end}</td>{/cal_cell_end}
	   {cal_row_end}</tr>{/cal_row_end}
	
	   {table_close}</table>{/table_close}';
	}
	else
	{
		$prefs = array(
			'day_type'       => 'short',
			'show_next_prev' => TRUE,
			'next_prev_url'  => admin_url(TBL_EVENTS_CALENDAR . '/view'),
		);

		$prefs['template'] = '
	   {table_open}<table border="0" cellpadding="4" cellspacing="1" width="100%" class="calendar">{/table_open}
	
	   {heading_row_start}<tr>{/heading_row_start}
	
	   {heading_previous_cell}<th class="text-center"><h3><a href="{previous_url}"" class="btn btn-lg btn-default"><i class="fa fa-chevron-left"></i></a></h3></th>{/heading_previous_cell}
	   {heading_title_cell}<th colspan="{colspan}" class="text-center"><h1>{heading}</h1></th>{/heading_title_cell}
	   {heading_next_cell}<th class="text-center"><h3><a href="{next_url}" class="btn btn-lg btn-default"><i class="fa fa-chevron-right"></i></a></h3></th>{/heading_next_cell}
	
	   {heading_row_end}</tr>{/heading_row_end}
	
	   {week_row_start}<tr>{/week_row_start}
	   {week_day_cell}<td class="week"><strong>{week_day}</strong></td>{/week_day_cell}
	   {week_row_end}</tr>{/week_row_end}
	
	   {cal_row_start}<tr>{/cal_row_start}
	   {cal_cell_start}<td>{/cal_cell_start}
	
	   {cal_cell_content}<div class="events"><div class="day" onclick="ViewEvents(\'{day}\')">{day}<i class="fa fa-check-circle-o fa-2x"></i></div>{/cal_cell_content}
	   {cal_cell_content_today}<div class="day" onclick="ViewEvents(\'{day}\')">{day}<i class="fa fa-check-circle-o fa-2x"></i></div>{/cal_cell_content_today}
	
	   {cal_cell_no_content}<div class="day" onclick="ViewEvents(\'{day}\')">{day}</div>{/cal_cell_no_content}
	   {cal_cell_no_content_today}<div class="day">{day}</div>{/cal_cell_no_content_today}
	
	   {cal_cell_blank}&nbsp;{/cal_cell_blank}
	
	   {cal_cell_end}</td>{/cal_cell_end}
	   {cal_row_end}</tr>{/cal_row_end}
	
	   {table_close}</table>{/table_close}';
	}

	$CI->load->library('calendar', $prefs);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_data($data = array())
{
	list($data['start_hour'], $data['start_min'], $data['start_ampm']) = explode(':', date("g:i:a", strtotime($data['start_time'])));
	list($data['end_hour'], $data['end_min'], $data['end_ampm']) = explode(':', date("g:i:a", strtotime($data['end_time'])));

	return $data;
}

/* End of file events_calendar_helper.php */
/* Location: ./application/helpers/events_calendar_helper.php */