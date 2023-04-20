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
class Events_calendar_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'id';

	// ------------------------------------------------------------------------

	/**
	 * Events_calendar_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('events_calendar');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $public
	 * @param bool $get_cache
	 * @return bool|false|string
	 */
	public function get_details($id = '', $public = FALSE, $get_cache = TRUE)
	{
		$sql = 'SELECT *,
                 DATE_FORMAT(p.date,\'' . $this->config->item('sql_date_format') . '\')
                    AS date ';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_EVENTS_CALENDAR) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                                AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_EVENTS_CALENDAR) . ' p
                            WHERE p.' . $this->id . '  > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                                AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_EVENTS_CALENDAR) . ' p
                        WHERE p.' . $this->id . ' = ' . (int)$id;

		//check if we have a cache file and return that instead
		$cache = __METHOD__ . md5($sql);
		$cache_type = $public == TRUE ? 'public_db_query' : 'db_query';

		if ($row = $this->init->cache($cache, $cache_type))
		{
			if ($get_cache == TRUE)
			{
				return sc($row);
			}
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		if ($q->num_rows() > 0)
		{
			$row = format_data($q->row_array());

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $m
	 * @param string $y
	 * @return bool|false|string
	 */
	public function get_rows($m = '', $y = '')
	{

		$start = date('Y-m-d', mktime(0, 0, 0, $m, 1, $y));
		$end = date('Y-m-d', mktime(12, 0, 0, $m, date('t', $m), $y));

		$sql = 'SELECT DATE_FORMAT(date, \'%e\') AS date, 
					COUNT(id) AS total 
					FROM ' . $this->db->dbprefix(TBL_EVENTS_CALENDAR) . ' 
					WHERE date >= \'' . $start . '\'
					AND date <= \'' . $end . '\'
					GROUP BY DAYOFMONTH(date)';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array();

			foreach ($q->result_array() as $v)
			{
				$row[ $v['date'] ] = $v['total'];
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $m
	 * @param string $y
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_events($m = '', $y = '', $public = FALSE)
	{

		$start = date('Y-m-d', mktime(0, 0, 0, $m, 1, $y));
		$end = date('Y-m-d', mktime(12, 0, 0, $m, date('t', $m), $y));

		$sql = 'SELECT *, DATE_FORMAT(date, \'%e\') AS day, 
		TIME_FORMAT(start_time, \'' . SQL_TIME_FORMAT . '\' ) as start_time,
		TIME_FORMAT(end_time, \'' . SQL_TIME_FORMAT . '\' ) as end_time,
				DATE_FORMAT(date,\'' . config_option('sql_date_format') . '\')
                        AS formatted_date
					FROM ' . $this->db->dbprefix(TBL_EVENTS_CALENDAR) . ' 
					WHERE date >= \'' . $start . '\'
					AND date <= \'' . $end . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND status = \'1\' 
			ORDER BY date ASC ';
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $y
	 * @param string $m
	 * @param string $d
	 * @return bool|false|string
	 */
	public function get_daily_events($y = '', $m = '', $d = '')
	{
		$start = date('Y-m-d', mktime(0, 0, 0, $m, $d, $y));

		$sql = 'SELECT *
                  FROM ' . $this->db->dbprefix(TBL_EVENTS_CALENDAR) . '
				    WHERE date = \'' . $start . '\'
				  ORDER BY start_time ASC';

		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
			foreach ($row as $k => $v)
			{
				$row[ $k ]['start_time'] = date('h:i A', strtotime($v['start_time']));
				$row[ $k ]['end_time'] = date('h:i A', strtotime($v['end_time']));
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$data = $this->dbv->clean($data, TBL_EVENTS_CALENDAR);

		if (!$this->db->insert(TBL_EVENTS_CALENDAR, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array('id' => $this->db->insert_id(),
						'success'  => TRUE,
		                 'data'     => $data,
		                 'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$data = $this->dbv->clean($data, TBL_EVENTS_CALENDAR);

		if (!$q = $this->db->where($this->id, valid_id($data[ $this->id ]))->update(TBL_EVENTS_CALENDAR, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'data'     => $data,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		$error = '';

		//check if the start and end time are correct
		$data['start_time'] = ($data['start_hour'] . ':' . $data['start_min'] . ':00 ');
		$data['end_time'] = ($data['end_hour'] . ':' . $data['end_min'] . ':00 ');

		if (strtotime($data['start_time']) > strtotime($data['end_time']))
		{
			$error .= 	'<div class="error_prefix">' . lang('start_time_must_be_earlier_than_end_time') . '</div>';
		}
		
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item(TBL_EVENTS_CALENDAR, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_EVENTS_CALENDAR);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean';

			//if this field is a required field, let's set that
			if (is_array($required) && in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			switch ($f->name)
			{
				case 'date':

					$rule .= '|day_to_sql';

					break;

				case 'start_hour':
				case 'end_hour':

					$rule .= '|integer|in_list[' . implode(',', config_option('hours')) . ']';

					break;

				case 'start_min':
				case 'end_min':

					$rule .= '|integer|in_list[' . implode(',', config_option('minutes')) . ']';

					break;

				case 'start_ampm':
				case 'end_ampm':

					$rule .= '|in_list[' . implode(',', config_option('ampm')) . ']';

					break;

				default:

					$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

					break;
			}

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if (!$this->form_validation->run())
		{
			$error .= validation_errors();
		}

		if (empty($error))
		{
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data, TRUE),
			);
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => $error,
			);
		}

		return $row;
	}
}

/* End of file Events_calendar_model.php */
/* Location: ./application/models/Events_calendar_model.php */