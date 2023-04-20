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
class Kb_articles_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'kb_id';

	// ------------------------------------------------------------------------

	/**
	 * Kb_articles_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('kb');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return bool
	 */
	public function check_url($str = '')
	{
		$str = empty($str) ? $this->input->post('url') : $str;

		if (!empty($str))
		{
			$this->db->where('url', $str);

			if ($this->input->post($this->id))
			{
				$this->db->where($this->id . ' !=', (int)$this->input->post($this->id));
			}

			if (!$q = $this->db->get(TBL_KB_ARTICLES))
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
		$vars = $this->dbv->clean($data, TBL_KB_ARTICLES);

		//finally... generate a unique URL if empty
		if (empty($vars[ 'url' ]))
		{
			$vars[ 'url' ] =  $this->dbv->generate_permalink($data[ 'lang' ][ config_item('sts_site_default_language') ][ 'kb_title' ], TBL_KB_ARTICLES, 'url');
		}

		if (!$q = $this->db->insert(TBL_KB_ARTICLES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data[ 'lang' ] as $k => $v)
		{
			$vars = array(
				$this->id          => $id,
				'language_id'      => $k,
				'kb_title'         => $v[ 'kb_title' ],
				'kb_body'          => $v[ 'kb_body' ],
				'meta_title'       => empty($v[ 'meta_title' ]) ? $v[ 'kb_title' ] : $v[ 'meta_title' ],
				'meta_description' => empty($v[ 'meta_description' ]) ? $v[ 'kb_title' ] : $v[ 'meta_description' ],
				'meta_keywords'    => empty($v[ 'meta_keywords' ]) ? $v[ 'kb_title' ] : $v[ 'meta_keywords' ],
			);

			if (!$q = $this->db->insert(TBL_KB_ARTICLES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		//update any downloads
		$this->update_kb_downloads($id, $data);

		//update kb videos
		$this->update_kb_videos($id, $data);

		return sc(array( 'success'  => TRUE,
		                 'data'       => $vars,
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
		foreach (array( TBL_KB_ARTICLES_NAME, TBL_KB_ARTICLES ) as $v)
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

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_kb_downloads($id = '', $lang_id = 1)
	{
		$sql = 'SELECT *
                    FROM ' . $this->db->dbprefix(TBL_KB_TO_DOWNLOADS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS) . ' n
                        ON p.download_id = n.download_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS_NAME) . ' c
                        ON (c.download_id = n.download_id
                        AND c.language_id = \'' . $lang_id . '\')
                    WHERE p.' . $this->id . ' = \'' . $id . '\'';

		if (!$q = $this->db->query($sql))
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
	public function get_kb_videos($id = '')
	{
		$sql = 'SELECT *
                    FROM ' . $this->db->dbprefix(TBL_KB_TO_VIDEOS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_VIDEOS) . ' n
                        ON p.video_id = n.video_id
                    WHERE p.' . $this->id . ' = \'' . $id . '\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
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
		$cache = __METHOD__ . $id . $lang_id . $public;
		$cache_type = $public == TRUE ? 'public_db_query' : 'db_query';
		if (!$row = $this->init->cache($cache, $cache_type))
		{
			$sql = 'SELECT  p.*,
                            c.*, m.category_name,
                            n.category_url,
                            n.parent_id,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next
                        FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_KB_ARTICLES_NAME) . ' c
                            ON (p.' . $this->id . ' = c.' . $this->id . '
                            AND c.language_id = \'' . $lang_id . '\')
                        LEFT JOIN ' . $this->db->dbprefix(TBL_KB_CATEGORIES) . ' n
                            ON (p.category_id = n.category_id)
                        LEFT JOIN ' . $this->db->dbprefix(TBL_KB_CATEGORIES_NAME) . ' m
                            ON (p.category_id = m.category_id
                            AND c.language_id = \'' . $lang_id . '\')';

			if ($public == TRUE)
			{
				$sql .= ' WHERE p.url = \'' . $id . '\'
                            AND p.status = \'1\'';
			}
			else
			{
				$sql .= ' WHERE p.kb_id = \'' . $id . '\' ';
			}

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();

				$row[ 'lang' ] = $this->dbv->get_names(TBL_KB_ARTICLES_NAME, $this->id, $id);
				$row[ 'kb_downloads' ] = $this->get_kb_downloads($row['kb_id'], $lang_id);
				$row[ 'kb_videos' ] = $this->get_kb_videos($row['kb_id']);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
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
		$sort = $this->config->item(TBL_KB_ARTICLES, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT  p.*, c.*,
                        m.category_id as category_id,
                        m.category_name
                    FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_KB_ARTICLES_NAME) . ' c
				        ON (p.' . $this->id . ' = c.' . $this->id . '
				        AND c.language_id = \'' . $lang_id . '\')
				    LEFT JOIN ' . $this->db->dbprefix(TBL_KB_CATEGORIES_NAME) . ' m
				        ON (p.category_id = m.category_id
				        AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_KB_ARTICLES, TBL_KB_ARTICLES_NAME ), $options[ 'query' ]);

			$sql .= $options[ 'where_string' ];
		}

		$sql .= ' GROUP BY p.kb_id 
					ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . '
                    LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		$query = $this->db->query($sql);
		

		if ($query->num_rows() > 0)
		{
			$row = array(
				'values'         => $query->result_array(),
				'total'          => $this->get_table_totals($options),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $type
	 * @return bool
	 */
	public function get_table_totals($options = '', $type = '')
	{
		switch ($type)
		{
			case 'category':

				$sql = 'SELECT  COUNT(*) as total
                    FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' p
                    WHERE p.status = \'1\'';

				if (!empty($options[ 'category_id' ]))
				{
					$sql .= ' AND p.category_id = \'' . $options[ 'category_id' ] . '\' ';
				}

				break;

			default:

				$sql = 'SELECT COUNT(*) as total FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' m ';

				if (!empty($options['query']))
				{
					$this->dbv->validate_columns(array(TBL_KB_ARTICLES), $options['query']);

					$sql .= $options['where_string'];
				}

				break;
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$q = $q->row();

			return $q->total;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function load_featured_articles($lang_id = 1)
	{
		$cache = __METHOD__ . $lang_id;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sql = 'SELECT *
                        FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_KB_ARTICLES_NAME) . ' c
                            ON (p.' . $this->id . ' = c.' . $this->id . '
                            AND c.language_id = \'' . $lang_id . '\')
                        LEFT JOIN ' . $this->db->dbprefix(TBL_KB_CATEGORIES_NAME) . ' m
                            ON (p.category_id = m.category_id
                            AND m.language_id = \'' . $lang_id . '\')
                        WHERE p.status = \'1\'
                        AND p.featured = \'1\'
                        ORDER BY sort_order ASC';

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array();
				foreach ($q->result_array() as $k => $v)
				{
					$row[ $k ] = $v;
					$row[ $k ][ 'overview' ] = word_limiter(strip_tags($v[ 'kb_body' ]), 50);
				}
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $category_id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function load_kb_articles($options = '', $category_id = '0', $lang_id = 1)
	{
		$cache = __METHOD__ . $options[ 'md5' ] . $lang_id;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sql = 'SELECT 	p.*,c.*,m.*,
							b.category_url
                    FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_KB_ARTICLES_NAME) . ' c
				        ON (p.' . $this->id . ' = c.' . $this->id . '
				        AND c.language_id = \'' . $lang_id . '\')
				    LEFT JOIN ' . $this->db->dbprefix(TBL_KB_CATEGORIES) . ' b
				        ON p.category_id = b.category_id
				     LEFT JOIN ' . $this->db->dbprefix(TBL_KB_CATEGORIES_NAME) . ' m
				        ON (p.category_id = m.category_id
				        AND c.language_id = \'' . $lang_id . '\')
				        WHERE p.status = \'1\'';

			if (!empty($category_id))
			{
				$sql .= ' AND p.category_id = \'' . $category_id . '\' ';
			}

			$sql .= ' GROUP BY p.' . $this->id . '  
						ORDER BY sort_order ASC
                        LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$options[ 'category_id' ] = empty($category_id) ? '0' : $category_id;

				$a = $q->result_array();
				foreach ($a as $k => $v)
				{
					$a[ $k ][ 'overview' ] = word_limiter(strip_tags($v[ 'kb_body' ]), 50);
				}

				$row = array(
					'values'         => $a,
					'total'          => $this->get_table_totals($options, 'category'),
					'success' => TRUE,
				);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function mass_update($data = array())
	{
		if (!empty($data[ 'kb_id' ]))
		{
			foreach ($data[ 'kb_id' ] as $v)
			{
				$vars[ 'status' ] = $data[ 'change-status' ];

				if (!$this->db->where($this->id, $v)->update(TBL_KB_ARTICLES, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		foreach ($data[ 'sort_order' ] as $k => $v)
		{
			$vars[ 'sort_order' ] = $v;

			if (!$this->db->where($this->id, $k)->update(TBL_KB_ARTICLES, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$row = array( 'success'  => TRUE,
		              'data'     => $data,
		              'msg_text' => lang('mass_update_successful'),
		);

		//order the tier groups numerically
		$this->dbv->db_sort_order(TBL_KB_ARTICLES, 'kb_id', 'sort_order');

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function search($options = '', $lang_id = 1, $public = FALSE)
	{
		$sort = $this->config->item(TBL_KB_ARTICLES, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$count = 'SELECT COUNT(*) as total FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' p 
					LEFT JOIN ' . $this->db->dbprefix(TBL_KB_ARTICLES_NAME) . ' c
				        ON (p.' . $this->id . ' = c.' . $this->id . '
				        AND c.language_id = \'' . $lang_id . '\')';

		$sql = 'SELECT  p.*, c.*,
                        m.category_id as category_id,
                        m.category_name
                    FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_KB_ARTICLES_NAME) . ' c
				        ON (p.' . $this->id . ' = c.' . $this->id . '
				        AND c.language_id = \'' . $lang_id . '\')
				    LEFT JOIN ' . $this->db->dbprefix(TBL_KB_CATEGORIES_NAME) . ' m
				        ON (p.category_id = m.category_id
				        AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options['query']))
		{
			foreach ($options['query'] as $k => $v)
			{
				//remove any fields not needed in the query
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				$columns = $this->db->list_fields(TBL_KB_ARTICLES);

				$i = 1;
				foreach ($columns as $f)
				{
					if ($i == 1)
					{
						$sql .= ' WHERE ( p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						$count .= ' WHERE ( p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}
					else
					{
						$sql .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						$count .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}

					$i++;
				}

				$columns = $this->db->list_fields(TBL_KB_ARTICLES_NAME);

				foreach ($columns as $f)
				{
					$sql .= ' OR c.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					$count .= ' OR c.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
				}

				$sql .= ') ';
				$count .= ') ';
			}
		}

		if ($public == TRUE)
		{
			$sql .= '  AND p.status = \'1\'';
			$count .= '  AND p.status = \'1\'';
		}
		$sql .= ' GROUP BY p.kb_id 
					ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . '
                    LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$a = $q->result_array();
			foreach ($a as $k => $v)
			{
				$a[ $k ][ 'overview' ] = word_limiter(strip_tags($v[ 'kb_body' ]), 50);
			}

			$row = array(
				'values'         => $a,
				'total'          =>  $this->dbv->get_query_total($count),
				'success'        => TRUE,
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
		$vars = $this->dbv->clean($data, TBL_KB_ARTICLES);

		if (!$q = $this->db->where($this->id, $data[$this->id])->update(TBL_KB_ARTICLES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data[ 'lang' ] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_KB_ARTICLES_NAME);

			$this->db->where($this->id, $data[$this->id]);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_KB_ARTICLES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		//update any downloads
		$this->update_kb_downloads($data[$this->id], $data);

		//update kb videos
		$this->update_kb_videos($data[$this->id], $data);

		$row = array(
			'id'       => $data[$this->id],
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'data'     => $data,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return bool
	 */
	public function update_kb_downloads($id = '', $data = array())
	{
		$this->db->where($this->id, $id);

		if (!$this->db->delete(TBL_KB_TO_DOWNLOADS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if (!empty($data[ 'kb_downloads' ]))
		{
			foreach ($data[ 'kb_downloads' ] as $v)
			{
				if (!$this->db->insert(TBL_KB_TO_DOWNLOADS, array( $this->id     => $id, 'download_id' => $v )))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return bool
	 */
	public function update_kb_videos($id = '', $data = array())
	{
		$this->db->where($this->id, $id);

		if (!$this->db->delete(TBL_KB_TO_VIDEOS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if (!empty($data[ 'kb_videos' ]))
		{
			foreach ($data[ 'kb_videos' ] as $v)
			{
				if (!$this->db->insert(TBL_KB_TO_VIDEOS, array( $this->id  => $id, 'video_id' => $v )))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		return TRUE;
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
			$vars = array( 'kb_title', 'kb_body' );

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

		$vars = array( 'status', 'featured', 'category_id' );
		foreach ($vars as $v)
		{
			$this->form_validation->set_rules($v, 'lang:' . $v, 'trim|required|integer');
		}

		if (CONTROLLER_FUNCTION == 'create')
		{
			$this->form_validation->set_rules(
				'url', 'lang:permalink',
				'trim|strtolower|url_title|is_unique[' . TBL_KB_ARTICLES . '.url]',
				array(
					'is_unique' => '%s ' . lang('already_in_use'),
				)
			);
		}
		else
		{
			$this->form_validation->set_rules(
				'url', 'lang:permalink',
				array(
					'trim', 'required', 'strtolower', 'url_title',
					array( 'check_url', array( $this->kb, 'check_url' ) )
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
		}

		return $row;
	}

}

/* End of file Kb_articles_model.php */
/* Location: ./application/models/Kb_articles_model.php */