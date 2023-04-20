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
class Cron_model extends CI_Model
{
	// ------------------------------------------------------------------------

	/**
	 * @param bool $public
	 * @return bool
	 */
	public function get_timers($public = FALSE)
	{
		if ($public == TRUE)
		{
			$this->db->where('status', '1');
		}

		if (!$q = $this->db->order_by('id', 'ASC')->get(TBL_TIMERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check cron security
	 */
	public function check_security()
	{
		if (uri(3) != config_item('sts_cron_password_key'))
		{
			show_error(lang('invalid_cron_access'));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function format_cron_report($data = array())
	{
		$vars = array();
		foreach ($data as $v)
		{
			$vars['cron_job'] = $v['msg_text'];
		}

		return $vars;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $table
	 * @param string $interval
	 * @param string $col
	 * @return array
	 */
	public function prune_table($table = '', $interval = '0', $col = 'date')
	{
		$sql = 'DELETE FROM ' . $this->db->dbprefix($table) .
			' WHERE ' . $col . ' < (CURDATE() - INTERVAL ' . (int)$interval . ' DAY);';

		if (!$this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$rows = !$this->db->affected_rows() ? 0 : $this->db->affected_rows();
		$row = array(
			'msg_text' => $table . ' - ' . $rows . ' ' . lang('rows_pruned_successfully'),
			'success'  => TRUE,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @return bool
	 */
	public function update_timer($type = '')
	{
		$vars = array('timestamp' => get_time() + config_item('timer_' . $type));

		if (!$this->db->where('name', $type)->update(TBL_TIMERS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}
}

/* End of file Cron_model.php */
/* Location: ./application/models/Cron_model.php */

