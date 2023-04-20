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
class Blog_tags_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'tag_id';

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

		if (!$q = $this->db->get(TBL_BLOG_TAGS))
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
		if (!$this->db->where($this->id, $id)->delete(TBL_BLOG_TAGS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return  array(
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
	public function get_rows($limit = TAG_CLOUD_LIMIT)
	{
		$sort = $this->config->item(TBL_BLOG_TAGS, 'db_sort_order');

		$this->db->limit($limit);
		$this->db->order_by( $sort[ 'column' ], $sort[ 'order' ]);

		if (!$q = $this->db->get(TBL_BLOG_TAGS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $this->dbv->calc_cloud($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return bool|false|string
	 */
	public function get_tag($str = '')
	{
		//check if the tag is already there
		if (!$q = $this->db->where('tag', $str)->get(TBL_BLOG_TAGS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool|false|string
	 */
	public function load_tags()
	{
		$cache = __METHOD__;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$row = $this->get_rows();

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
		$tag = strip_tags(trim(strtolower($data[ 'value' ])));

		if (!$this->get_tag($tag) && strlen($tag) > 1)
		{
			if (!$q = $this->db->where('tag_id', $id)->update(TBL_BLOG_TAGS, array( 'tag' => $tag )))
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

/* End of file Blog_tags_model.php */
/* Location: ./application/models/Blog_tags_model.php */