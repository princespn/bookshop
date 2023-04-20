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
class Regions_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'region_id';

	// ------------------------------------------------------------------------

	/**
	 * Regions_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('regions');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @param bool $all_regions
	 * @return array|bool
	 */
	public function ajax_search($term = '', $all_regions = FALSE)
	{
		$this->db->like('region_name', $term);
		$this->db->select('region_id, region_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get($this->name_table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array();
		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		if ($all_regions == TRUE)
		{
			$row = show_country_regions($row, TRUE, $all_regions);
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @param string $col
	 * @return string
	 */
	public function check_region($str = '', $col = 'region_id')
	{
		$this->db->where('region_name', ucfirst(trim($str)));
		$this->db->or_where('region_code', strtoupper(trim($str)));
		$this->db->or_where('region_id', (int)$str);
		$this->db->limit(1);

		if (!$q = $this->db->get(TBL_REGIONS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
		}

		return empty($row) ? '0' : $row[$col];
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_REGIONS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT p.*, c.country_name, c.country_iso_code_2, c.country_id
                    FROM ' . $this->db->dbprefix(TBL_REGIONS) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' c ON (p.region_country_id= c.country_id)';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_REGIONS, TBL_COUNTRIES), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$query = $this->db->query($sql);
		

		if ($query->num_rows() > 0)
		{
			$row = array(
				'values'         => $query->result_array(),
				'total'          => $this->get_table_totals($options),
				'success'        => TRUE,
			);

			return sc($row);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_details($id = '')
	{
		$sql = 'SELECT p.*, c.country_iso_code_2, c.country_name,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_REGIONS) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_REGIONS) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next
				    FROM ' . $this->db->dbprefix(TBL_REGIONS) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' c ON (p.region_country_id= c.country_id)
                    WHERE p.' . $this->id . '= ' . (int)$id . '';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return mixed
	 */
	public function get_table_totals($options = '')
	{
		$sql = 'SELECT COUNT(*) as total FROM ' . $this->db->dbprefix(TBL_REGIONS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' c ON (p.region_country_id= c.country_id)';

		if (!empty($options['query']))
		{
			$sql .= $options['where_string'];
		}

		$query = $this->db->query($sql);

		$q = $query->row();

		return $q->total;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $format
	 * @param bool $all_regions
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function load_country_regions($id = '', $format = FALSE, $all_regions = FALSE, $public = FALSE)
	{
		$cache = __METHOD__ . $id . $format;
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			$this->db->where('region_country_id', $id);
			$this->db->order_by('region_name', 'ASC');

			if ($public == TRUE)
			{
				$this->db->where('status', '1');
			}

			if (!$q = $this->db->get(TBL_REGIONS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array();
			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();

				if ($format == TRUE)
				{
					$row = format_array($row, 'region_id', 'region_name');
				}
			}

			if ($all_regions == TRUE)
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
	 * @param array $data
	 * @return false|string
	 */
	public function mass_update($data = array())
	{
		if (!empty($data['region']))
		{
			foreach ($data['region'] as $k => $v)
			{
				$v[ $this->id ] = $k;

				//check for status
				if (!empty($v['update']))
				{
					$v['status'] = $data['change-status'];
				}

				$vars = $this->dbv->clean($v, TBL_REGIONS);

				$this->dbv->update(TBL_REGIONS, $this->id, $vars);

			}
		}

		$row = array(
			'data'     => $data,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}
}

/* End of file Regions_model.php */
/* Location: ./application/models/Regions_model.php */