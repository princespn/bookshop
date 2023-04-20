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
class Forum_categories_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'category_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return bool
	 */
	public function check_url($str = '')
	{
		$str = empty($str) ? $this->input->post('category_url') : $str;

		if (!empty($str))
		{
			$this->db->where('category_url', $str);

			if ($this->input->post($this->id))
			{
				$this->db->where($this->id . ' !=', (int)$this->input->post($this->id));
			}

			if (!$q = $this->db->get(TBL_FORUM_CATEGORIES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_FORUM_CATEGORIES);

		if (!$q = $this->db->insert(TBL_FORUM_CATEGORIES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data[ 'lang' ] as $k => $v)
		{
			$vars = array(
				'category_id'      => $id,
				'language_id'      => $k,
				'category_name'    => $v[ 'category_name' ],
				'description'      => $v[ 'description' ],
				'meta_title'       => empty($v[ 'meta_title' ]) ? $v[ 'category_name' ] : $v[ 'meta_title' ],
				'meta_description' => empty($v[ 'meta_title' ]) ? $v[ 'category_name' ] : $v[ 'meta_description' ],
				'meta_keywords'    => empty($v[ 'meta_title' ]) ? $v[ 'category_name' ] : $v[ 'meta_keywords' ],
			);

			if (!$q = $this->db->insert(TBL_FORUM_CATEGORIES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array( 'success'  => TRUE,
		                 'id'       => $id,
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
		//do not delete the default category...
		if ($id != config_option('default_forum_category_id'))
		{
			//update category IDs for topics
			$this->dbv->reset_id(   TBL_FORUM_TOPICS,
				$this->id,
				$id,
				config_option('default_forum_category_id')
			);

			foreach (array( TBL_FORUM_CATEGORIES_NAME, TBL_FORUM_CATEGORIES ) as $v)
			{
				if (!$this->db->where($this->id, $id)->delete($v))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			return sc(array( 'success'  => TRUE,
			                 'id'       => $id,
			                 'msg_text' => lang('record_deleted_successfully') ));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_FORUM_CATEGORIES, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT p.*, n.*';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(category_id)
		                    FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . '
			                WHERE p.category_id = ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . '.category_id)
			                AS total ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES) . ' p
		    	    LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES_NAME) . ' n
				        ON (p.' . $this->id . ' = n.' . $this->id . '
				        AND n.language_id = \'' . $lang_id . '\')';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_FORUM_CATEGORIES, TBL_FORUM_CATEGORIES_NAME ), $options[ 'query' ]);

			$sql .= $options[ 'where_string' ];
		}

		$sql .= ' GROUP BY p.category_id
					ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . '
                    LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_FORUM_CATEGORIES),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param bool $public
	 * @param string $col
	 * @param bool $str
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = '1', $public = FALSE, $col = 'category_id', $str = FALSE)
	{
		$sql = 'SELECT p.*, b.*';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES) . ' p
                            WHERE p . ' . $this->id . ' < ' . valid_id($id) . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                            AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES) . ' p
                            WHERE p . ' . $this->id . ' > ' . valid_id($id) . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                            AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES_NAME) . ' b
                                ON p.' . $this->id . ' = b. ' . $this->id . '
                                AND b.language_id = \'' . (int)$lang_id . '\'
                        WHERE p.' . $col . '= \'' . valid_id($id, $str) . '\'';

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

				if ($public == FALSE)
				{
					$row['lang'] = $this->dbv->get_names(TBL_FORUM_CATEGORIES_NAME, 'category_id', $id);
				}
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_category($id = '', $lang_id = 1)
	{
		$sql = 'SELECT *
                      FROM ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES) . ' p
                         LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES_NAME) . ' b
                                ON p.' . $this->id . ' = b. ' . $this->id . '
                                AND b.language_id = \'' . (int)$lang_id . '\'
                        WHERE p.' . $this->id . '= ' . valid_id($id) . '';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param int $lang_id
	 * @param bool $form
	 * @return bool|false|string
	 */
	public function get_categories($lang_id = 1, $form = FALSE)
	{
		$sort = $this->config->item(TBL_FORUM_CATEGORIES, 'db_sort_order');

		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES) . ' p
				LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES_NAME) . ' c
				    ON (p.' . $this->id . ' = c.' . $this->id . '
				    AND c.language_id = \'' . (int)$lang_id . '\')
				ORDER BY ' . $sort[ 'column' ] . ' ' . $sort[ 'order' ];

		//set the cache file
		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();

				if ($form == TRUE)
				{
					$row = format_array($row, 'category_id', 'category_name');
				}

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param int $lang_id
	 * @param bool $topics
	 * @return bool|false|string
	 */
	public function load_categories($lang_id = 1, $topics = FALSE)
	{
		$cache = __METHOD__ . $lang_id;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sql = 'SELECT p.*,c.*, ';

			if ($topics == TRUE)
			{
				$sql .= 'm.*, n.username,';
			}

			$sql .= '   (SELECT COUNT(t.topic_id) 
							FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' t 
							WHERE t.category_id = p.category_id) 
								AS topics	
						
					FROM ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES) . ' p
					 LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' f
                        ON (p.' . $this->id . ' = f.' . $this->id . ')	
					LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . (int)$lang_id . '\')';


			if ($topics == TRUE)
			{
				$sql .= 'JOIN ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' s
                            ON p.category_id = s.category_id
                            JOIN ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' m
                            ON m.topic_id = 
                            (SELECT mi.topic_id FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' mi 
                            WHERE mi.category_id = s.category_id ORDER BY topic_id DESC LIMIT 1)
                            LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' n
                                ON (m.member_id = n.member_id)    ';
			}

			$sql .= ' WHERE p.category_status = \'1\'
                        GROUP BY p.category_id    
                        ORDER BY sort_order ASC';

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param int $lang_id
	 * @return false|string
	 */
	public function mass_update($data = array(), $lang_id = 1)
	{
		foreach ($data[ 'cat' ] as $k => $v)
		{
			$vars = array( 'category_name' => $v[ 'category_name' ]);
			$this->db->where('language_id', $lang_id);
			if (!$this->db->where($this->id, $k)->update(TBL_FORUM_CATEGORIES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$vars = array(  'sort_order'    => $v[ 'sort_order' ] );

			if (!$this->db->where($this->id, $k)->update(TBL_FORUM_CATEGORIES, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array( 'success'  => TRUE,
		                 'msg_text' => lang('mass_update_successful'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update($id = '', $data = array())
	{
		$vars = $this->dbv->clean($data, TBL_FORUM_CATEGORIES);

		if (!$q = $this->db->where($this->id, $id)->update(TBL_FORUM_CATEGORIES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data[ 'lang' ] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_FORUM_CATEGORIES_NAME);

			$this->db->where($this->id, $id);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_FORUM_CATEGORIES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$row = array(
			'id'       => $id,
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

			//validate the kb entries...
			$vars = array( 'category_name', 'description' );

			foreach ($vars as $c)
			{
				$this->form_validation->set_rules($c, 'lang:' . $c, 'trim|required|xss_clean',
					array( 'required' => $v[ 'language' ] . ' ' . lang($c . '_required') ));
			}

			//validate the meta info...
			$vars = array( 'meta_title', 'meta_keywords', 'meta_description' );

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
				$data[ 'lang' ][ $k ] = $this->dbv->validated($v, FALSE);
			}
		}

		//now validate the rest...
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		$vars = array( 'category_status', 'sort_order' );
		foreach ($vars as $v)
		{
			$this->form_validation->set_rules($v, 'lang:' . $v, 'trim|required|integer');
		}

		if (CONTROLLER_FUNCTION == 'create')
		{
			$this->form_validation->set_rules(
				'category_url', 'lang:category_url',
				'trim|strtolower|url_title|is_unique[' . TBL_FORUM_CATEGORIES . '.category_url]',
				array(
					'is_unique' => '%s ' . lang('already_in_use'),
				)
			);
		}
		else
		{
			$this->form_validation->set_rules(
				'category_url', 'lang:category_url',
				array(
					'trim', 'required', 'strtolower', 'url_title',
					array( 'check_url', array( $this->cat, 'check_url' ) )
				)
			);

			$this->form_validation->set_message('check_url', '%s ' . lang('already_in_use'));
		}

		if (!$this->form_validation->run())
		{
			$error .= validation_errors();
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
			              'data'    => $this->dbv->validated($data),
			);

			//finally... generate a unique URL if empty
			if (empty($row[ 'data' ][ 'category_url' ]))
			{
				$row[ 'data' ][ 'category_url' ] = $this->dbv->generate_permalink($data[ 'lang' ][ config_item('sts_site_default_language') ][ 'category_name' ], TBL_FORUM_CATEGORIES, 'category_url');
			}
		}

		return $row;
	}
}

/* End of file Forum_categories_model.php */
/* Location: ./application/models/Forum_categories_model.php */