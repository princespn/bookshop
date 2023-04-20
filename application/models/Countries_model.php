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
class Countries_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'country_id';

	// ------------------------------------------------------------------------

	/**
	 * Countries_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('country');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @param string $col
	 * @return bool
	 */
	public function check_country($str = '', $col = 'country_id')
	{
		$this->db->where('country_name', ucfirst(trim($str)));
		$this->db->or_where('country_iso_code_2', strtoupper(trim($str)));
		$this->db->or_where('country_iso_code_3', strtoupper(trim($str)));
		$this->db->or_where('country_id', (int)$str);
		$this->db->limit(1);

		if (!$q = $this->db->get(TBL_COUNTRIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
		}

		return empty($row) ? config_option('sts_site_default_country') : $row[$col];
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function mass_update($data = array())
	{
		if (!empty($data['country']))
		{
			foreach ($data['country'] as $k => $v)
			{
				$v[ $this->id ] = $k;

				//check for status
				if (!empty($v['update']))
				{
					$v['status'] = $data['change-status'];
				}

				$vars = $this->dbv->clean($v, TBL_COUNTRIES);

				if (!$this->dbv->update(TBL_COUNTRIES, $this->id, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		$row = array(
			'data'     => $data,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $status
	 * @param bool $format
	 * @param bool $all_countries
	 * @param string $key
	 * @return bool|false|string
	 */
	public function load_countries_array($status = FALSE, $format = FALSE, $all_countries = FALSE, $key = 'country_id')
	{
		$cache = __METHOD__ . $status . $format;
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if ($status == TRUE)
			{
				$this->db->where('status', '1');
			}

			$this->db->order_by('sort_order', 'ASC');

			if (!$q = $this->db->get(TBL_COUNTRIES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array();
			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();

				if ($format == TRUE)
				{
					$row = format_array($row, $key, 'country_name');


				}
			}

			if ($all_countries == TRUE)
			{
				$row = show_country_regions($row);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function check_country_id($id = '')
	{
		$this->db->where($this->id, $id);

		if (!$q = $this->db->get(TBL_COUNTRIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? TRUE : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @param bool $all_countries
	 * @param bool $public
	 * @return array|bool
	 */
	public function ajax_search($term = '', $all_countries = FALSE, $public = FALSE)
	{
		$this->db->like('country_name', $term);
		$this->db->select('country_id, country_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if ($public == TRUE)
		{
			$this->db->where('status', '1');
		}

		if (!$q = $this->db->get(TBL_COUNTRIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array();
		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		if ($all_countries == TRUE)
		{
			$row = show_country_regions($row, TRUE, 'all_countries');
		}

		return empty($row) ? FALSE : $row;
	}
}
/* End of file Countries_model.php */
/* Location: ./application/models/Countries_model.php */