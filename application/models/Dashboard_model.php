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
class Dashboard_model extends CI_Model
{

	// ------------------------------------------------------------------------

	/**
	 * Load icons
	 *
	 * load dashboard icons from the members dashboard table
	 *
	 * @return array|bool
	 */
	public function load_icons()
	{
		$cache = __METHOD__;

		if (!$row = $this->init->cache($cache, 'settings'))
		{
			if (!$q = $this->db->where('status', '1')
				->order_by('sort_order', 'ASC')
				->get(TBL_MEMBERS_DASHBOARD)
			)
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();
			}
		}

		return empty($row) ? FALSE : $this->init_icons($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Redirect homepage based on admin
	 */
	public function check_homepage()
	{
		if (strlen(sess('admin','admin_home_page'))> '0' && sess('admin','admin_home_page') != 'dashboard')
		{
			redirect(admin_url($this->session->admin['admin_home_page']));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function update_sort_order($data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				if (!$q = $this->db->where('dash_id', $v)
					->update(TBL_MEMBERS_DASHBOARD, array('sort_order' => $k))
				)
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_invoices($id = '')
	{
		if (!$q = $this->db->where('member_id', $id)->order_by('invoice_id', 'DESC')->limit(5)->get(TBL_INVOICES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_tickets($id = '')
	{
		if (!$q = $this->db->where('member_id', $id)->order_by('ticket_id', 'DESC')->limit(5)->get(TBL_SUPPORT_TICKETS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @return array|bool|false|string
	 */
	public function get_widgets($type = '')
	{
		$days = days_in_month(current_date('m'), current_date('Y'));

		switch ($type)
		{
			case 'total_sales':
			case 'daily_sales':

				$sql = 'SELECT SUM( total ) AS ' . $type . '
                  FROM ' . $this->db->dbprefix(TBL_INVOICES);

				if ($type == 'daily_sales')
				{
					$sql .= ' WHERE date_purchased 
                  BETWEEN \'' . current_date('Y') . '-' . current_date('m') . '-' . current_date('d') . ' 00:00:00\'
                    AND \'' . current_date('Y') . '-' . current_date('m') . '-' . current_date('d') . ' 23:59:59\'';
				}

				if (!$q = $this->db->query($sql))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->row_array();
				}

				return array($type => format_amount($row[$type]));

				break;

			case 'total_commissions':
			case 'daily_commissions':

				$sql = 'SELECT SUM( commission_amount ) AS ' . $type . '
                  FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS);

				if ($type == 'daily_commissions')
				{
					$sql .= ' WHERE date 
                  BETWEEN \'' . current_date('Y') . '-' . current_date('m') . '-' . current_date('d') . ' 00:00:00\'
                    AND \'' . current_date('Y') . '-' . current_date('m') . '-' . current_date('d') . ' 23:59:59\'';
				}

				if (!$q = $this->db->query($sql))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->row_array();
				}

				return array($type => format_amount($row[$type]));

				break;

			case 'total_tickets':
			case 'daily_tickets':

				if (config_enabled('sts_support_enable'))
				{
					$sql = 'SELECT COUNT( ticket_id ) AS ' . $type . '
                  FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS);

					if ($type == 'daily_tickets')
					{
						$sql .= ' WHERE date_added 
                  BETWEEN \'' . current_date('Y') . '-' . current_date('m') . '-' . current_date('d') . ' 00:00:00\'
                    AND \'' . current_date('Y') . '-' . current_date('m') . '-' . current_date('d') . ' 23:59:59\'';
					}
				}
				else
				{
					$sql = 'SELECT COUNT( order_id ) AS ' . $type . '
                  FROM ' . $this->db->dbprefix(TBL_ORDERS);

					if ($type == 'daily_tickets')
					{
						$sql .= ' WHERE date_ordered
                  BETWEEN \'' . current_date('Y') . '-' . current_date('m') . '-' . current_date('d') . ' 00:00:00\'
                    AND \'' . current_date('Y') . '-' . current_date('m') . '-' . current_date('d') . ' 23:59:59\'';
					}
				}

				if (!$q = $this->db->query($sql))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->row_array();
				}

				return array($type => $row[$type]);

				break;

			case 'total_users':
			case 'daily_users':

				$sql = 'SELECT COUNT( member_id ) AS ' . $type . '
                  FROM ' . $this->db->dbprefix(TBL_MEMBERS);

				if ($type == 'daily_users')
				{
					$sql .= ' WHERE date 
                  BETWEEN \'' . current_date('Y') . '-' . current_date('m') . '-' . current_date('d') . ' 00:00:00\'
                    AND \'' . current_date('Y') . '-' . current_date('m') . '-' . current_date('d') . ' 23:59:59\'';
				}

				if (!$q = $this->db->query($sql))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->row_array();
				}

				return array($type => $row[$type]);

				break;

			case 'last_30_sales':

				$sql = 'SELECT DAY( date_purchased ) AS day,
                  SUM( total ) AS amount
                  FROM ' . $this->db->dbprefix(TBL_INVOICES) . '
                  WHERE date_purchased 
                  BETWEEN \'' . current_date('Y') . '-' . current_date('m') . '-01 00:00:00\'
                    AND \'' . current_date('Y') . '-' . current_date('m') . '-' . $days . ' 23:59:59\'';

				$sql .= ' GROUP BY day';

				if (!$q = $this->db->query($sql))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->result_array();

					$row = $this->report->init_chart_data($row, $days);

					return json_encode($row);

				}

				break;

			case 'last_30_comm':

				$sql = 'SELECT DAY( date ) AS day,
                  SUM( commission_amount ) AS amount
                  FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . '
                  WHERE date 
                  BETWEEN \'' . current_date('Y') . '-' . current_date('m') . '-01 00:00:00\'
                    AND \'' . current_date('Y') . '-' . current_date('m') . '-' . $days . ' 23:59:59\'';

				$sql .= ' GROUP BY day';

				if (!$q = $this->db->query($sql))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->result_array();

					$row = $this->report->init_chart_data($row, $days);

					return json_encode($row);
				}

				break;

			case 'latest_signups':

				$this->db->limit(8);
				$this->db->order_by($this->db->dbprefix(TBL_MEMBERS) . '.member_id', 'DESC');

				$this->db->join(TBL_MEMBERS_PROFILES,
					$this->db->dbprefix(TBL_MEMBERS_PROFILES) . '.member_id = ' .
					$this->db->dbprefix(TBL_MEMBERS) . '.member_id', 'left');

				if (!$q = $this->db->get(TBL_MEMBERS))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->result_array();
				}

				break;

			case 'latest_commissions':

				$this->db->limit(8);
				$this->db->order_by('comm_id', 'DESC');

				$this->db->join(TBL_MEMBERS,
					$this->db->dbprefix(TBL_MEMBERS) . '.member_id = ' .
					$this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . '.member_id', 'left');

				if (!$q = $this->db->get(TBL_AFFILIATE_COMMISSIONS))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->result_array();
				}

				break;

			case 'latest_invoices':

				$this->db->limit(8);
				$this->db->order_by('invoice_id', 'DESC');

				$this->db->join(TBL_PAYMENT_STATUS,
					$this->db->dbprefix(TBL_PAYMENT_STATUS) . '.payment_status_id = ' .
					$this->db->dbprefix(TBL_INVOICES) . '.payment_status_id', 'left');

				if (!$q = $this->db->get(TBL_INVOICES))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->result_array();
				}

				break;

			case 'latest_tickets':

				$this->db->limit(8);

				if (config_enabled('sts_support_enable'))
				{
					$this->db->order_by('ticket_id', 'DESC');

					$this->db->join(TBL_MEMBERS,
						$this->db->dbprefix(TBL_MEMBERS) . '.member_id = ' .
						$this->db->dbprefix(TBL_SUPPORT_TICKETS) . '.member_id', 'left');

					if (!$q = $this->db->get(TBL_SUPPORT_TICKETS))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
				else
				{
					$this->db->order_by('order_id', 'DESC');

					$this->db->join(TBL_ORDERS_STATUS,
						$this->db->dbprefix(TBL_ORDERS_STATUS) . '.order_status_id = ' .
						$this->db->dbprefix(TBL_ORDERS) . '.order_status_id', 'left');

					if (!$q = $this->db->get(TBL_ORDERS))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->result_array();
				}

				break;
		}

		return !empty($row) ? $row : FALSE;


	}

	// ------------------------------------------------------------------------

	/**
	 * Init icons
	 *
	 * Check if we're going to show the icons on the dashboard
	 *
	 * @param array $data
	 * @return array
	 */
	protected function init_icons($data = array())
	{
		$icons = array();

		foreach ($data as $v)
		{
			$v['url'] = format_url($v['url']);

			switch ($v['show'])
			{
				//show only if the user has been marked as an affiliate
				case 'affiliate':

					if (config_enabled('affiliate_marketing'))
					{
						if ($this->session->is_affiliate)
						{
							array_push($icons, $v);
						}
					}

					break;

				//show only if the user has been marked as as customer
				case 'customer':

					if ($this->session->is_customer)
					{
						array_push($icons, $v);
					}

					break;

				//show always
				default:

					array_push($icons, $v);

					break;
			}
		}

		return $icons;
	}
}