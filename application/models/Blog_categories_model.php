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
class Blog_categories_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'category_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @param string $lang_id
	 * @param bool $multiple
	 * @return bool|false|string
	 */
	public function ajax_search($term = '', $lang_id = '1', $multiple = FALSE)
	{
		$row = array();

		$this->db->like('category_name', $term);
		$this->db->where('language_id', $lang_id);
		$this->db->select('category_id, category_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_BLOG_CATEGORIES_NAME))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		if (empty($multiple))
		{
			array_push($row, array( 'category_id'   => '0',
			                        'category_name' => lang('none') ));
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
		$vars = array( 'status'     => '1',
		               'sort_order' => '0' );

		if (!$q = $this->db->insert(TBL_BLOG_CATEGORIES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data as $v)
		{
			$vars = array(
				$this->id          => $id,
				'language_id'      => $v[ 'language_id' ],
				'category_name'    => lang('new_category_name'),
				'description'      => lang('new_category_name'),
				'meta_title'       => lang('new_category_name'),
				'meta_description' => lang('new_category_name'),
				'meta_keywords'    => lang('new_category_name'),
			);

			if (!$q = $this->db->insert(TBL_BLOG_CATEGORIES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array( 'success'  => TRUE,
		                 'id'     => $id,
		                 'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function delete($id = '')
	{
		if ($id != config_option('default_blog_category_id'))
		{
			if (!$this->db->where($this->id, $id)->delete(TBL_BLOG_CATEGORIES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			//update product group IDs
			$this->dbv->reset_id(TBL_BLOG_POSTS,
				'category_id',
				$id,
				config_option('default_blog_category_id')
			);

			$row = sc(array( 'success'  => TRUE,
			                 'id'       => $id,
			                 'msg_text' => lang('record_deleted_successfully') ));
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_BLOG_CATEGORIES, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT * ';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(' . $this->id . ') FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' b
			    WHERE p.' . $this->id . ' = b.' . $this->id . ') AS total ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES) . ' p
				LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES_NAME) . ' c ON (p.' . $this->id . ' = c.' . $this->id . '
				AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_BLOG_CATEGORIES, TBL_BLOG_CATEGORIES_NAME ), $options[ 'query' ]);

			$sql .= $options[ 'where_string' ];
		}

		$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . ' LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		$query = $this->db->query($sql);
		

		if ($query->num_rows() > 0)
		{
			$row = array(
				'values'         => $query->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_BLOG_CATEGORIES),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = 1, $public = FALSE)
	{
		$cache = __METHOD__ . $id;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sql = 'SELECT *,
						 (SELECT ' . $this->id . '
	                        FROM ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES) . ' p
	                        WHERE p.' . $this->id . ' < ' . (int)$id . '
	                        ORDER BY `' . $this->id . '` DESC LIMIT 1)
                        AS prev,
                        (SELECT ' . $this->id . '
	                        FROM ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES) . ' p
	                        WHERE p.' . $this->id . ' > ' . (int)$id . '
	                        ORDER BY `' . $this->id . '` ASC LIMIT 1)
                        AS next
						FROM ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES_NAME) . ' c
                            ON (p.category_id = c.category_id)
                            AND c.language_id = \'' . $lang_id . '\'
                        WHERE p.category_id = \'' . $id . '\'';

			if ($public == TRUE)
			{
				$sql .= ' AND p.status = \'1\'';
			}

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();
				$row[ 'lang' ] = $this->dbv->get_names(TBL_BLOG_CATEGORIES_NAME, $this->id, $id);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function load_categories($lang_id = 1)
	{
		$sort = $this->config->item(TBL_BLOG_CATEGORIES, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT * ';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(blog_id)
                                FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' 
                                WHERE ' . 'p.' . $this->id . ' = ' . $this->db->dbprefix(TBL_BLOG_POSTS) . '.' . $this->id . ')
                                AS total';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES) . ' p 
					LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_CATEGORIES_NAME) . ' c
					ON (p.' . $this->id . ' = c.' . $this->id . '
					AND c.language_id = \'' . $lang_id . '\')
				WHERE p.status = \'1\'';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_BLOG_CATEGORIES, TBL_BLOG_CATEGORIES_NAME ), $options[ 'query' ]);

			$sql .= $options[ 'and_string' ];
		}

		$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ];

		if (!$q = $this->db->query($sql))
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
	 * @param array $data
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	public function mass_update($data = array(), $lang_id = '1')
	{
		if (!empty($data))
		{
			foreach ($data[ 'sort_order' ] as $k => $v)
			{
				$vars = array( 'sort_order' => (int)$v );

				if (!$this->db->where($this->id, $k)->update(TBL_BLOG_CATEGORIES, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			foreach ($data[ 'category_name' ] as $k => $v)
			{
				$vars = array( 'category_name' => $v );

				$this->db->where('language_id', $lang_id);
				if (!$this->db->where($this->id, $k)->update(TBL_BLOG_CATEGORIES_NAME, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			$row = array( 'success'  => TRUE,
			              'data'     => $data,
			              'msg_text' => lang('mass_update_successful'),
			);

			$this->dbv->db_sort_order(TBL_BLOG_CATEGORIES, $this->id, 'sort_order');
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
		$vars = $this->dbv->clean($data, TBL_BLOG_CATEGORIES);

		$this->db->where($this->id, $data[ 'category_id' ]);

		if (!$this->db->update(TBL_BLOG_CATEGORIES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data[ 'lang' ] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_BLOG_CATEGORIES_NAME);

			$this->db->where($this->id, $data[ 'category_id' ]);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_BLOG_CATEGORIES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
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
	 * @return array
	 */
	public function validate($data = array())
	{
		$error = '';

		foreach ($data[ 'lang' ] as $k => $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);
			$this->form_validation->set_rules('category_name', 'lang:category_name', 'trim|required|strip_tags|xss_clean',
				array( 'required' => $v[ 'language' ] . ' ' . lang('category_name_required') ));

			if (!$this->form_validation->run())
			{
				$error .= validation_errors();
			}
		}

		if (!empty($error))
		{
			//sorry! got some errors here....
			$row = array( 'error'    => TRUE,
			              'msg_text' => $error,
			);
		}
		else
		{
			//cool! no errors...
			$row = array( 'success' => TRUE,
			              'data'    => $this->dbv->validated($data, FALSE),
			);
		}

		return $row;
	}
}

/* End of file Blog_categories_model.php */
/* Location: ./application/models/Blog_categories_model.php */