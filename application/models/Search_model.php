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
class Search_model extends CI_Model
{
	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function advanced_search($data = array())
	{
		list($type, $column) = explode('-', $data['column']);

		$sql = 'SELECT * ';

		if (!empty($data['join_table_1']) && !empty($data['join_column_1']) && !empty($data['on_column_1']))
		{
			//check for joins
			list($join_type_1, $join_column_1) = explode('-', $data['join_column_1']);
			list($on_type_1, $on_column_1) = explode('-', $data['on_column_1']);

			$sql .= ', p.' . $on_column_1 . ' AS ' . $on_column_1;
		}

		$sql .= ' FROM ' . $data['table'] . ' p ';

		if (!empty($data['join_table_1']) && !empty($data['join_column_1']) && !empty($data['on_column_1']))
		{
			$sql .= ' LEFT JOIN ' . $data['join_table_1'] . ' d
						ON d.' . $join_column_1 . ' = p.' . $on_column_1;
		}

		$sql .= ' WHERE p.' . $column;

		switch ($data['operator'])
		{
			case 'LIKE':

				$sql .= ' LIKE \'%' . xss_clean($data['value']) . '%\'';

				break;

			case '=':
			case '>':
			case '>=':
			case '<':
			case '<=':

				$sql .= ' ' . $data['operator'] . ' \'' . xss_clean($data['value']) . '\'';

				break;
		}

		$sql .= 'GROUP BY p.' . $column . ' LIMIT ' . $data['limit'];

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'success' => TRUE,
				'total' => count($q->result_array()),
				'fields' => $q->field_data(),
				'values'  => $q->result_array(),
			);

			return sc($row);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @param string $table
	 * @param string $id
	 * @return string
	 */
	public function check_search_link($str = '', $table = '', $id = '')
	{
		if (in_array($id, config_item('link_search_ids')))
		{
			$table = str_replace($this->db->dbprefix, '', $table);

			return anchor(admin_url($table . '/update/' . $str), $str);
		}

		return $str;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function generate_download($data = array())
	{
		if (count($data) > 0)
		{
			$row['list_fields'] = array();
			$row['result_array'] = array();

			$i = 0;
			foreach ($data as $v)
			{
				if ($i == 0)
				{
					foreach ($v as $k => $c)
					{
						array_push($row['list_fields'], $k);
					}
				}

				array_push($row['result_array'], $v);

				$i++;
			}


			$data = csv_from_result($row, ADVANCED_SEARCH_FILE_DELIMITER, "\r\n");

			$row = array('success'   => TRUE,
			             'file_name' => lang('advanced_search_results') . '-' . time() . '.' . ADVANCED_SEARCH_FILE_EXTENSION,
			             'data'      => $data,
			             'msg_text'  => lang('file_exported_successfully'));

		}

		return !empty($row) ? sc($row) : FALSE;

	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return mixed
	 */
	public function get_query_total($str = '')
	{
		$sql = 'SELECT COUNT(*) AS total FROM (' . $str . ') AS p';

		if (!$a = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$b = $a->row_array();

		return $b['total'];
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $str
	 * @param int $lang_id
	 * @return false|string
	 */
	public function site_search($options = '', $str = '', $lang_id = 1)
	{
		$sql = '';

		if (config_enabled('sts_store_enable'))
		{
			$sql .= 'SELECT
					  p.product_id AS id,
					  (SELECT AVG(ratings)
                            FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' r
						    WHERE p.product_id = r.product_id
                            AND r.status = \'1\') AS reviews,
					  \'products\' AS \'table\',
					  p.date_added AS date,
					  s.product_name AS url,
					  s.product_name AS title,
					  s.product_overview AS overview,
					  s.product_description AS body,
					  h.photo_file_name AS image,
					  p.product_views AS views
					FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' s
						ON p.product_id = s.product_id 
						AND s.language_id = ' . (int)$lang_id . '
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_PHOTOS) . '  h
                        ON p.product_id = h.product_id
                        AND h.product_default = \'1\'	
					WHERE p.product_status = \'1\' 
						AND p.hidden_product = \'0\' 
						AND p.date_expires > NOW() 
						AND s.product_name LIKE \'%' . strip_tags($str) . '%\' 
						OR s.product_overview LIKE \'%' . strip_tags($str) . '%\'
						OR s.product_description LIKE \'%' . strip_tags($str) . '%\'';
		}

		if (config_enabled('sts_blog_enable'))
		{
			$sql .= !empty($sql) ? 'UNION' : '';
			$sql .= ' SELECT b.blog_id AS id,
						 (SELECT COUNT(id)
                            FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' c
						    WHERE b.blog_id = c.blog_id
							AND c.status = \'1\') AS reviews,
					  \'blog\' AS \'table\',
					  b.date_published AS date,
					  b.url AS url,
					  n.title AS title,
					  n.overview AS overview,
					  n.body AS body,
					  b.overview_image AS image, 
					  b.views AS views
					FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' b
					LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' n
						ON b.blog_id = n.blog_id 
						AND n.language_id = ' . (int)$lang_id . '
					WHERE status = \'1\' 
						AND date_published < NOW() 
						AND drip_feed = \'0\' 
						AND require_registration = \'0\' 
						AND n.title LIKE \'%' . strip_tags($str) . '%\' 
						OR n.overview LIKE \'%' . strip_tags($str) . '%\'
						OR n.body LIKE \'%' . strip_tags($str) . '%\'';
		}

		if (config_enabled('sts_kb_enable'))
		{
			$sql .= !empty($sql) ? 'UNION' : '';
			$sql .= ' SELECT k.kb_id AS id,
						\'0\' AS reviews,
					  \'kb\' AS \'table\',
					  k.date_modified AS date,
					  k.url AS url,
					  m.kb_title AS title,
					  m.kb_body AS overview,
					  m.kb_body AS body,
					  \'images/no-photo.jpg\' AS image, 
					  k.views AS views
					FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' k
					LEFT JOIN ' . $this->db->dbprefix(TBL_KB_ARTICLES_NAME) . ' m
						ON k.kb_id = m.kb_id
						AND m.language_id = ' . (int)$lang_id . '
					WHERE k.status = \'1\' 
						AND m.kb_title LIKE \'%' . strip_tags($str) . '%\' 
						OR m.kb_body LIKE \'%' . strip_tags($str) . '%\'';
		}

		$sql .= !empty($sql) ? 'UNION' : '';
		$sql .= ' SELECT f.page_id AS id,
						\'0\' AS reviews,
					  \'site_pages\' AS \'table\',
					  f.date_modified AS date,
					  f.url AS url,
					  f.title AS title,
					  g.page_content AS overview,
					  g.page_content AS body,
					  \'images/no-photo.jpg\' AS image, 
					  f.views AS views
					FROM ' . $this->db->dbprefix(TBL_SITE_PAGES) . ' f	
					LEFT JOIN ' . $this->db->dbprefix(TBL_SITE_PAGES_NAME) . ' g
						ON f.page_id = g.page_id
						AND g.language_id = ' . (int)$lang_id . '
					WHERE f.status = \'1\' 
						AND f.title LIKE \'%' . strip_tags($str) . '%\' 
						OR g.page_content LIKE \'%' . strip_tags($str) . '%\'';


		$limit = ' ORDER BY id LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		if (!$q = $this->db->query($sql . $limit))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values' => $q->result_array(),
				'total'  => $this->get_query_total($sql),
			);

			return sc($row);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Select search
	 *
	 * search option for select dropdowns
	 *
	 * @param string $term
	 * @param string $table
	 * @param string $field
	 * @param string $key
	 * @param string $status
	 * @param int $limit
	 * @return bool|string
	 */
	public function select_search($term = '', $table = '', $field = '', $key = '', $status = '', $limit = TPL_AJAX_LIMIT)
	{
		//search for select2 dropdowns
		$this->db->like($field, $term);

		$this->db->select($key . ', ' . $field);

		if (!empty($status))
		{
			$this->db->where($status, '1');
		}

		$this->db->limit($limit);

		if (!$q = $this->db->get($table))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}
}

/* End of file Search_model.php */
/* Location: ./application/models/Search_model.php */