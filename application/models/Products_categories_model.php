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
class Products_categories_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'category_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @param int $lang_id
	 * @param bool $no_parent
	 * @return bool|false|string
	 */
	public function ajax_search($term = '', $lang_id = 1, $no_parent = FALSE)
	{
		$sql = 'SELECT *
					FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' c
						ON p.' . $this->id . ' =  c.' . $this->id . '
						AND `language_id` = \'' . $lang_id . '\'
					WHERE c.category_name
					LIKE \'%' . xss_clean($term) . '%\'
						ESCAPE \'!\'
					LIMIT ' . TPL_AJAX_LIMIT . '';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array();

		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $v)
			{
				$c = $this->get_cat_path($v, $lang_id);

				if (!empty($c['path']))
				{
					$v['category_name'] = $c['path'] . ' / ' . $v['category_name'];
				}

				array_push($row, $v);
			}
		}

		if ($no_parent == TRUE)
		{
			array_push($row, array(
				'category_id'   => '0',
				'category_name' => lang('none'),
				'parent_path'   => '',
			));
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
		$vars = $this->dbv->clean($data, TBL_PRODUCTS_CATEGORIES);

		if (!$q = $this->db->insert(TBL_PRODUCTS_CATEGORIES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$data['category_id'] = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data['lang'] as $k => $v)
		{
			$vars = array(
				$this->id          => $data['category_id'],
				'language_id'      => $k,
				'category_name'    => $v['category_name'],
				'description'      => $v['description'],
				'meta_title'       => empty($v['meta_title']) ? $v['category_name'] : $v['meta_title'],
				'meta_description' => empty($v['meta_title']) ? $v['category_name'] : $v['meta_description'],
				'meta_keywords'    => empty($v['meta_title']) ? $v['category_name'] : $v['meta_keywords'],
			);

			if (!$q = $this->db->insert(TBL_PRODUCTS_CATEGORIES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$this->update_category_path(0, 1);

		return sc(array('success'  => TRUE,
		                'data'     => $data,
		                'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete($id = '')
	{
		if (!$this->db->where($this->id, $id)->delete(TBL_PRODUCTS_CATEGORIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array('success'  => TRUE,
		                'id'       => $id,
		                'msg_text' => lang('record_deleted_successfully')));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $status
	 * @param int $lang_id
	 * @return mixed
	 */
	public function get_all_product_categories($status = FALSE, $lang_id = 1)
	{
		$sort = $this->config->item(TBL_PRODUCTS_CATEGORIES, 'db_sort_order');

		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' p
                LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' n
                    ON (p.' . $this->id . ' = n.' . $this->id . ')
                    AND language_id = \'' . $lang_id . '\'';

		if ($status == TRUE)
		{
			$sql .= ' WHERE p.category_status = \'1\'';
		}

		$sql .= ' ORDER BY n.' . $sort['column'] . ' ' . $sort['order'];

		$query = $this->db->query($sql);

		return $query->result_array();
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = '1', $public = FALSE)
	{

		$sql = 'SELECT p.*, n.category_name,
                        (SELECT ' . $this->id . '
	                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' p
	                        WHERE p.' . $this->id . ' < ' . (int)$id . '
	                        ORDER BY `' . $this->id . '` DESC LIMIT 1)
                        AS prev,
                        (SELECT ' . $this->id . '
	                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' p
	                        WHERE p.' . $this->id . ' > ' . (int)$id . '
	                        ORDER BY `' . $this->id . '` ASC LIMIT 1)
                        AS next,
                        (SELECT g.category_name
	                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' g
	                         WHERE p.parent_id = g.category_id
	                        AND g.language_id = \'' . (int)$lang_id . '\')
	                    AS parent_name
                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' p
						LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' n
                            ON (p.' . $this->id . ' = n.' . $this->id . ')
                            AND language_id = \'' . $lang_id . '\'
                        WHERE p.' . $this->id . ' = \'' . valid_id($id) . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND p.category_status = \'1\' ';
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

				//get language array
				$row['category_path'] = $this->get_cat_path($row, $lang_id);
				$row['lang'] = $this->get_product_category_names($id);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_cat_path($data = array(), $lang_id = 1)
	{
		$sql = 'SELECT
					GROUP_CONCAT(n.category_name ORDER BY p.lft ASC SEPARATOR \' / \') AS path,
					GROUP_CONCAT(p.category_id ORDER BY p.lft ASC SEPARATOR \' / \') AS path_id
                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' p
						LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' n
                            ON (p.' . $this->id . ' = n.' . $this->id . ')
                              AND n.language_id = \'' . (int)$lang_id . '\'
                        WHERE p.lft <  ' . $data['lft'] . ' AND p.rgt > ' . $data['rgt'];


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
	 * @param string $id
	 * @param string $product_id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_product_category($id = '', $product_id = '', $lang_id = 1)
	{
		$sql = 'SELECT *
                  	FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' n
	                    ON (p.' . $this->id . ' = n.' . $this->id . ')
	                    AND n.language_id = \'' . (int)$lang_id . '\'
	                WHERE p.' . $this->id . ' = \'' . valid_id($id) . '\'
	                AND p.product_id = \'' . valid_id($product_id) . '\'';
		
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $id
	 * @return bool|false|string
	 */
	public function get_product_category_names($id)
	{
		$this->db->join(TBL_LANGUAGES,
			$this->db->dbprefix(TBL_LANGUAGES) . '.language_id = ' .
			$this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . '.language_id', 'left');

		if (!$q = $this->db->where('category_id', $id)->get(TBL_PRODUCTS_CATEGORIES_NAME))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = '1')
	{
		$sort = $this->config->item(TBL_PRODUCTS_CATEGORIES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT p.*, n.*,
					GROUP_CONCAT(z.category_name ORDER BY c.lft ASC SEPARATOR \'/\') AS path,
					GROUP_CONCAT(c.category_id ORDER BY c.lft ASC SEPARATOR \'/\') AS path_id';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(product_id)
                                FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . '
                                WHERE ' . 'p.' . $this->id . ' = ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . '.' . $this->id . ')
                                AS total';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' c
						ON p.lft > c.lft
						AND p.rgt < c.rgt
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' z
						ON c.category_id = z.category_id
						AND z.language_id =  \'' . $lang_id . '\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' n
                      ON p.category_id = n.' . $this->id . '
                        AND n.language_id = \'' . $lang_id . '\'';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_PRODUCTS_CATEGORIES, TBL_PRODUCTS_TO_CATEGORIES,
			                                   TBL_PRODUCTS_CATEGORIES_NAME), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' GROUP BY p.category_id
                        ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$query = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			

			if ($query->num_rows() > 0)
			{
				$row = array(
					'values'         => $query->result_array(),
					'total'          => $this->get_table_totals($options),
					'success'        => TRUE,
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool
	 */
	public function get_table_totals($options = '')
	{
		$sql = 'SELECT COUNT(category_id) AS total
                    FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' p';

		if (!empty($options['query']))
		{
			$sql .= $options['where_string'];
		}

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
		{
			$q = $query->row();

			return $q->total;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @return bool
	 */
	public function get_product_categories($id = '', $lang_id = '1')
	{
		//we don't cache this as it is called via the get_details method in Products_model and its cached there

		$sql = 'SELECT *,
					GROUP_CONCAT(z.category_name ORDER BY c.lft ASC SEPARATOR \' / \')
						AS path,
					GROUP_CONCAT(c.category_id ORDER BY c.lft ASC SEPARATOR \' / \')
						AS path_id
                    FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' b
						 ON b.category_id = p.category_id
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' c
						ON b.lft > c.lft
						AND b.rgt < c.rgt
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' z
						ON c.category_id = z.category_id
						AND z.language_id =  \'' . $lang_id . '\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' n
                        ON p.category_id = n.category_id
                    WHERE n.language_id = \'' . $lang_id . '\'
                        AND p.product_id = \'' . $id . '\'
                    GROUP BY n.category_name ASC';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
			foreach ($row as $k => $v)
			{
				//$row[ $k ][ 'sub_categories' ] = $this->get_sub_categories($v[ 'category_id' ], $lang_id);
				//$row[$k]['path'] = format_category_path($row[$k]['sub_categories'], $lang_id);
			}
		}

		return !empty($row) ? $row : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @return bool
	 */
	public function get_sub_categories($id = '', $lang_id = '1')
	{
		//no need to cache as its called from method get_products in Products model

		$sql = 'SELECT *, REPLACE(LOWER(category_name), \' \', \'-\') AS url_name
                    FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' c
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' n
                        ON (c.category_id = n.category_id
                        AND n.language_id = \'' . $lang_id . '\')
                    WHERE c.parent_id = \'' . $id . '\'
                    ORDER BY `category_name` ASC';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return !empty($row) ? $row : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function mass_update($data = array())
	{
		if (!empty($data))
		{
			foreach ($data['cat'] as $k => $v)
			{
				$vars = array('sort_order' => (int)$v['sort_order']);

				if (isset($v['category_status']))
				{
					$vars['category_status'] = $data['change-status'];
				}

				if (!$this->db->where('category_id', $k)->update(TBL_PRODUCTS_CATEGORIES, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			$row = array('success'  => TRUE,
			             'data'     => $data,
			             'msg_text' => lang('mass_update_successful'),
			);

			$this->dbv->db_sort_order(TBL_PRODUCTS_CATEGORIES, 'category_id', 'sort_order');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	public function sub_categories($id = '0', $lang_id = '1', $count = FALSE)
	{
		$cache = __METHOD__;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{

			$sql = 'SELECT *, REPLACE(LOWER(category_name), \' \', \'-\') AS url_name';

			if ($count == TRUE && !$this->config->item('disable_sql_category_count'))
			{
				$sql .= ', (SELECT COUNT(category_id) 
				 	FROM  ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' a WHERE a.category_id = c.category_id) 
				 	AS products';
			}

            $sql .= '  FROM `' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . '` c
                         LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' n
                            ON `c`.`category_id` = `n`.`category_id` WHERE `language_id` = \'' . $lang_id . '\'
                            AND `parent_id` = \'' . $id . '\' AND `c`.`category_status` = \'1\'
                         ORDER BY `sort_order` ASC';

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();

				foreach ($row as $k => $v)
				{
					$row[$k]['url_title'] = url_title($v['category_name']);
				}
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
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
		$vars = $this->dbv->clean($data, TBL_PRODUCTS_CATEGORIES);

		if (!$q = $this->db->where($this->id, valid_id($data['category_id']))->update(TBL_PRODUCTS_CATEGORIES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data['lang'] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_PRODUCTS_CATEGORIES_NAME);

			$this->db->where($this->id, $data['category_id']);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_PRODUCTS_CATEGORIES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		//set the category paths...
		$this->update_category_path(0, 1);

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'data'     => $data,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $parent_id
	 * @param int $left
	 * @return int|mixed
	 */
	public function update_category_path($parent_id = '0', $left = 1)
	{
		$right = $left + 1;

		$this->db->select('category_id');
		$this->db->where('parent_id', $parent_id);
		$this->db->order_by('category_id', 'ASC');
		$q = $this->db->get(TBL_PRODUCTS_CATEGORIES);

		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $row)
			{
				$right = $this->update_category_path($row['category_id'], $right);
			}
		}

		$vars = array('lft' => $left, 'rgt' => $right);
		$this->db->where('category_id', $parent_id);
		$this->db->update(TBL_PRODUCTS_CATEGORIES, $vars);

		return $right + 1;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		$error = '';

		foreach ($data['lang'] as $k => $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);

			//validate the entries...
			$this->form_validation->set_rules('category_name', 'lang:category_name', 'trim|required|strip_tags|xss_clean',
				array('required' => $v['language'] . ' ' . lang('category_name_required')));

			//validate the meta info...
			$vars = array('description', 'meta_title', 'meta_keywords', 'meta_description');

			foreach ($vars as $c)
			{
				$this->form_validation->set_rules($c, 'lang:' . $c, 'trim|strip_tags');
			}

			if (!$this->form_validation->run())
			{

				$error .= validation_errors();
			}
			else
			{
				$data['lang'][ $k ] = $this->dbv->validated($v, FALSE);
			}
		}

		//now validate the rest...
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		$vars = array('category_status', 'sort_order');
		foreach ($vars as $v)
		{
			$this->form_validation->set_rules($v, 'lang:' . $v, 'trim|required|integer');
		}

		$this->form_validation->set_rules('parent_id', 'lang:parent_id', 'trim|integer');

		$vars = array('category_banner', 'category_image', 'page_template');
		foreach ($vars as $v)
		{
			$this->form_validation->set_rules($v, 'lang:' . $v, 'trim|strip_tags|xss_clean');
		}

		if (!$this->form_validation->run())
		{
			$error .= validation_errors();
		}

		if (!empty($error))
		{
			//sorry! got some errors here....
			$row = array('error'    => TRUE,
			             'msg_text' => $error,
			);
		}
		else
		{
			//cool! no errors...
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data),
			);
		}

		return $row;
	}
}

/* End of file Products_categories_model.php */
/* Location: ./application/models/Products_categories_model.php */