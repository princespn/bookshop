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
class Gallery_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'gallery_id';

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function create($data = array())
	{
		$data = $this->dbv->clean($data, TBL_GALLERY);

		if (!$this->db->insert(TBL_GALLERY, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_created_successfully'),
			'data'     => $data,
			'success'  => TRUE,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_details($id = '', $public = FALSE)
	{
		$sql = 'SELECT * ';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_GALLERY) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_GALLERY) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next';
		}

		$sql .= '   FROM ' . $this->db->dbprefix(TBL_GALLERY) . ' p
                    WHERE p.' . $this->id . ' = \'' . (int)$id . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND p.gallery_status = \'1\'';
		}

		$cache = __METHOD__ . md5($sql);
		$cache_type = $public == TRUE ? 'public_db_query' : 'db_query';
		if (!$row = $this->init->cache($cache, $cache_type))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool|false|string
	 */
	public function load_gallery()
	{
		$this->db->order_by('sort_order', 'ASC');
		if (!$q = $this->db->where('gallery_status', '1')->get(TBL_GALLERY))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_GALLERY);

		if (!$q = $this->db->where($this->id, $data[$this->id])->update(TBL_GALLERY, $vars))
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
	 */
	public function update_sort_order($data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				if (!$q = $this->db->where($this->id, $v)
					->update(TBL_GALLERY, array('sort_order' => $k))
				)
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}
	}
}

/* End of file Gallery_model.php */
/* Location: ./application/models/Gallery_model.php */