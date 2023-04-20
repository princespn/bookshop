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
class Affiliate_downline_model extends CI_Model
{
	/**
	 * @param string $mem
	 * @param bool $admin
	 * @param string $api
	 * @return string
	 */
	public function check_downline_details($mem = '', $admin = FALSE, $api = '')
	{
		$details = '';
		$link = 'javascript:void(0)';

		if ($admin == TRUE)
		{
			$link = admin_url('affiliate_downline/view/' . $mem['member_id']);
		}
		else
		{
			if (config_enabled('sts_affiliate_show_downline_email'))
			{
				$details .= '<br /><span class="downline-email">' . $mem['primary_email'] . '</span>';
			}
		}

		$data = '<br />' . i('fa fa-arrow-down') . '<br /><a href="' . $link . '">' . i('fa fa-user fa-4x') . '</a>';
		$data .= '<br /><a href="' . $link . '"><small>' . $this->check_show_name($mem) . '</a>' . $details . '</small>';

		return $data;

	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function check_upline($data = array())
	{
		return $this->get_upline($data['member_id']);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return array
	 */
	private function get_upline($id = '')
	{
		$rows = array();
		$current_sponsor = $id;

		if ($current_sponsor != '0')
		{
			$sql = 'SELECT *, m.member_id AS member_id
					 	FROM ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . ' m
						LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_AFFILIATE_GROUPS) . ' a
						    ON m.member_id = a.member_id
						LEFT JOIN ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . ' g
						    ON a.group_id = g.group_id
						LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' p
						    ON m.member_id = p.member_id
						LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_ALERTS) . ' q
						    ON m.member_id = q.member_id
						WHERE m.member_id = \'' . $current_sponsor . '\'
							AND p.is_affiliate = \'1\'
						GROUP BY m.member_id';


			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();

				//add the member data to the array
				$rows[1] = $row;

				//set the new affiliate
				$current_sponsor = $row['sponsor_id'];
			}
		}

		return $rows;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $admin
	 * @param string $type
	 * @return mixed
	 */
	public function generate_downline($id = '0', $admin = FALSE, $type = 'table')
	{
		//set the cache file
		$cache = __METHOD__ . $id . $admin . $type;
		if (!$sdata = $this->init->cache($cache, 'downline_db_query'))
		{
			//lets get the first row of referrals first for level 1 and level 2

			$sdata['results'] = $type == 'table' ? '' : array();
			$sdata['levels'] = '';
			$total = '0';

			$sql = 'SELECT m.*,
                  c.sponsor_id
                  FROM ' . $this->db->dbprefix('members') . ' m
                  LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . ' c
                  ON m.member_id = c.member_id
				    WHERE sponsor_id = \'' . $id . '\'
				    AND m.is_affiliate != \'0\'';

			if ($admin == FALSE && $this->config->item('sts_affiliate_show_active_downline_users') == '1')
			{
				$sql .= ' AND m.status = \'1\'';
			}

			$first_row = $this->db->query($sql);

			if ($first_row->num_rows() > 0)
			{
				$total = $first_row->num_rows();

				$sdata['levels'] = $this->config->item('sts_affiliate_commission_levels');

				//generate the table for commission levels less than 3
				$this->original_array = $first_row->result_array();

				if ($type == 'table') //return a formatted table
				{
					foreach ($first_row->result_array() as $value)
					{
						$sdata['results'] .= '<td align="center" class="downline_top"><table class="table">
										  <tr>
											<td align="center"><div class="downline-box">' .
							$this->check_downline_details($value, $admin)
							. '</div></td>
										  </tr>
										</table></td>';
					}
				}
				else //just return an array
				{
					$sdata['results'] = $first_row->result_array();
				}
			}
			else
			{
				if ($type == 'table')
				{
					$sdata['results'] = '<td align="center">' . $this->lang->line('no_downline_members_found') . '</td>';
				}
			}

			$sdata['total_users'] = $total;

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $sdata, 'downline_db_query');
		}

		return $sdata;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $sponsor
	 * @return string
	 */
	public function get_downline_sponsor($sponsor = '')
	{
		return $sponsor;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $sponsor
	 * @return array
	 */
	public function get_direct_downline($sponsor = '')
	{
		$this->db->select('member_id as mid');
		$this->db->where('sponsor_id', $sponsor);

		if (!$q = $this->db->get(TBL_MEMBERS_SPONSORS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			return $q->result_array();
		}

		return array();
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $mem_array
	 * @return mixed|string
	 */
	protected function check_show_name($mem_array = array())
	{
		if ($this->config->item('show_view_downline_usernames'))
		{
			$id = $this->config->item('show_view_downline_usernames');

			return $mem_array[$id];
		}

		return $mem_array['fname'] . ' ' . $mem_array['lname'];
	}
}



/* End of file Downline_model.php */
/* Location: ./application/models/Downline_model.php */