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
class Products_tags_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'tag_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return bool|false|string
	 */
	public function add_import_tags($id = '', $data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $v)
			{
				$row = add_tag($v);

				// now add the tag to the product
				$row = $this->dbv->create(TBL_PRODUCTS_TO_TAGS, array('tag_id'     => $row['tag_id'],
				                                                      'product_id' => $id));
			}

			$row = array('data'     => $data,
			             'msg_text' => lang('system_updated_successfully'));
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $tag
	 * @return mixed
	 */
	public function add_tag($tag = '')
	{
		$tag = url_title(strtolower(trim($tag)));
		//check if the tag is in the database first
		if (!$q = $this->db->where('tag', $tag)->get(TBL_PRODUCTS_TAGS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
		}
		else
		{
			//add the new tag
			$row = $this->dbv->create(TBL_PRODUCTS_TAGS, array('tag' => $tag));
			$row['tag_id'] = $row['id'];
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $tags
	 * @return array
	 */
	public function add_tags($tags = '')
	{
		$tag = explode(',', $tags);

		$total = 0;
		foreach ($tag as $t)
		{
			if (!empty($t))
			{
				$this->add_tag($t);
				$total++;
			}
		}

		return array('success'  => TRUE,
		             'msg_text' => $total . ' ' . lang('tags_added_successfully'));

	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @return bool|false|string
	 */
	public function ajax_search($term = '')
	{
		$this->db->like('tag', $term);
		$this->db->select('tag_id, tag');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_PRODUCTS_TAGS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return array
	 */
	public function delete($id = '')
	{
		if (!$this->db->where($this->id, $id)->delete(TBL_PRODUCTS_TAGS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array(
			'id'       => $id,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $limit
	 * @return bool|false|string
	 */
	public function get_rows($limit = TAG_CLOUD_LIMIT, $sort_order = '')
	{
		$sort = !empty($sort_order) ? $sort_order : $this->config->item(TBL_PRODUCTS_TAGS, 'db_sort_order');

		$sql = 'SELECT p.*';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ' , (SELECT COUNT(prod_tag_id) 
				 	FROM  ' . $this->db->dbprefix(TBL_PRODUCTS_TO_TAGS) . ' a WHERE p.tag_id = a.tag_id) 
				 	AS products';
		}

			$sql .= ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TAGS) . ' p 
                ORDER BY ' . $sort['column'] . ' ' . $sort['order'] . ' LIMIT 0, ' . $limit;

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $this->dbv->calc_cloud($q->result_array());
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return bool|false|string
	 */
	public function get_tag($str = '')
	{
		//check if the tag is already there
		if (!$q = $this->db->where('tag', $str)->get(TBL_PRODUCTS_TAGS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool|false|string
	 */
	public function load_tags($sort_order = array())
	{
		$cache = __METHOD__;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$row = $this->get_rows(TAG_CLOUD_LIMIT, $sort_order);

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update($id = '', $data = array())
	{
		$tag = url_title(trim(strtolower($data['value'])));

		if (!$this->get_tag($tag) && strlen($tag) > 1)
		{
			if (!$q = $this->db->where('tag_id', $id)->update(TBL_PRODUCTS_TAGS, array('tag' => $tag)))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			return array(
				'id'       => $id,
				'msg_text' => lang('system_updated_successfully'),
				'success'  => TRUE,
				'row'      => $data,
			);
		}
	}
}

/* End of file Products_tags_model.php */
/* Location: ./application/models/Products_tags_model.php */