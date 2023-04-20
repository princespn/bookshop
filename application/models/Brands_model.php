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
class Brands_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'brand_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @param int $lang_id
	 * @return array
	 */
	public function ajax_search($term = '', $lang_id = 1)
	{
		$this->db->like('brand_name', $term);
		$this->db->where('language_id', $lang_id);
		$this->db->select('brand_id, brand_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_BRANDS_NAME))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$d[] = array( 'brand_id'   => '0',
		              'brand_name' => 'none' );

		if ($q->num_rows() > 0)
		{
			$e = $q->result_array();

			$rows = array_merge($d, $e);
		}
		else
		{
			$rows = $d;
		}

		return $rows;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_BRANDS);

		if (!$q = $this->db->insert(TBL_BRANDS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$data['brand_id'] = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data[ 'lang' ] as $k => $v)
		{
			$vars = array(
				$this->id            => $data['brand_id'],
				'language_id'      => $k,
				'brand_name'         => $v[ 'brand_name' ],
				'description'          => $v[ 'description' ],
				'meta_title'       => empty($v[ 'meta_title' ]) ? $v[ 'brand_name' ] : $v[ 'meta_title' ],
				'meta_description' => empty($v[ 'meta_title' ]) ? $v[ 'brand_name' ] : $v[ 'meta_description' ],
				'meta_keywords'    => empty($v[ 'meta_title' ]) ? $v[ 'brand_name' ] : $v[ 'meta_keywords' ],
			);

			if (!$q = $this->db->insert(TBL_BRANDS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array( 'success'  => TRUE,
		                 'data'       => $data,
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
		foreach ( array( TBL_BRANDS_NAME, TBL_BRANDS) as $v)
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
	 * @param $id
	 * @return bool|false|string
	 */
	public function get_brand_names($id)
	{
		$this->db->join(TBL_LANGUAGES,
			$this->db->dbprefix(TBL_LANGUAGES) . '.language_id = ' .
			$this->db->dbprefix(TBL_BRANDS_NAME) . '.language_id', 'left');

		if (!$q = $this->db->where('brand_id', $id)->get(TBL_BRANDS_NAME))
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
	 * @param string $id
	 * @param string $lang_id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = '1', $public = FALSE)
	{
		$cache = __METHOD__ . $id;
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			$sql = 'SELECT *, REPLACE(LOWER(brand_name), \' \', \'-\') AS url_name,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_BRANDS) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                            AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_BRANDS) . ' p
                            WHERE p.' . $this->id . ' > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                            AS next
                        FROM ' . $this->db->dbprefix(TBL_BRANDS) . ' p
                         LEFT JOIN ' . $this->db->dbprefix(TBL_BRANDS_NAME) . ' b
                                ON p.' . $this->id . ' = b. ' . $this->id . '
                                AND b.language_id = \'' . (int)$lang_id . '\'
                        WHERE p.' . $this->id . '= ' . (int)$id . '';

			if ($public == TRUE)
			{
				$sql .= ' AND p.brand_status = \'1\' ';
			}

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}


			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();

				//get language array
				$row[ 'lang' ] = $this->get_brand_names($id);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
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
		$sort = $this->config->item(TBL_BRANDS, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT *, p.brand_id as brand_id ';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(brand_id)
							FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . '
                            WHERE p.brand_id = ' . $this->db->dbprefix(TBL_PRODUCTS) . '.brand_id)
                        AS total ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_BRANDS) . ' p
                LEFT JOIN ' . $this->db->dbprefix(TBL_BRANDS_NAME) . ' c
                    ON (p.' . $this->id . ' = c.' . $this->id . '
                    AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_BRANDS, TBL_BRANDS_NAME ), $options[ 'query' ]);

			$sql .= $options[ 'where_string' ];
		}

		$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . '
					LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
			

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values'         => $q->result_array(),
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
	 * @param bool $public
	 * @return bool
	 */
	public function get_table_totals($options = '', $public = FALSE)
	{
		$sql = 'SELECT COUNT(p.brand_id) AS total
                    FROM ' . $this->db->dbprefix(TBL_BRANDS) . ' p ';

		if ($public == TRUE)
		{
			$sql .= ' WHERE p.brand_status = \'1\'';
		}

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_BRANDS, TBL_BRANDS_NAME ), $options[ 'query' ]);

			$sql .= $public == TRUE ? $options[ 'and_string' ] : $options[ 'where_string' ];
		}

		if (!$query = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($query->num_rows() > 0)
		{
			$q = $query->row();

			return $q->total;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function load_brands($options = '', $lang_id = 1, $all = FALSE, $count = FALSE)
	{
		$sort = $this->config->item(TBL_BRANDS, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT 	*,
						REPLACE(LOWER(brand_name), \' \', \'-\') AS url_name,
                        p.brand_id as brand_id';

		if ($count == TRUE && !$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(product_id) 
				 	FROM  ' . $this->db->dbprefix(TBL_PRODUCTS) . ' a WHERE a.brand_id = p.brand_id) 
				 	AS products';
		}

        $sql .= ' FROM ' . $this->db->dbprefix(TBL_BRANDS) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_BRANDS_NAME) . ' c
                            ON (p.' . $this->id . ' = c.' . $this->id . '
                            AND c.language_id = \'' . $lang_id . '\')
                        WHERE p.brand_status = \'1\' ';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_BRANDS, TBL_BRANDS_NAME ), $options[ 'query' ]);

			$sql .= $options[ 'and_string' ];
		}

		$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ];

		if ($all == FALSE)
		{
      		$sql .= ' LIMIT ' . $options['offset']. ', ' . $options['limit'];
		}

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

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);

	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function mass_update($data = array(), $lang_id = 1)
	{

		if (!empty($data['brands']))
		{
			foreach ($data['brands'] as $k => $v)
			{
				$vars = array( 'sort_order' => $v[ 'sort_order' ]);

				if (isset($v['brand_status']))
				{
					$vars['brand_status'] = $data['change-status'];
				}

				if (!$this->db->where('brand_id', $k)->update(TBL_BRANDS, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				$this->db->where('language_id', $lang_id);
				if (!$this->db->where('brand_id', $k)->update(  TBL_BRANDS_NAME,
																array( 'brand_name'  => valid_id($v[ 'brand_name' ] ,TRUE))))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			$row = array( 'success'  => TRUE,
			              'data'     => $data,
			              'msg_text' => lang('mass_update_successful'),
			);
		}

		//order the tier groups numerically
		$this->dbv->db_sort_order(TBL_BRANDS, 'brand_id', 'sort_order');

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_BRANDS);

		if (!$q = $this->db->where($this->id, valid_id($data['brand_id']))->update(TBL_BRANDS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data[ 'lang' ] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_BRANDS_NAME);

			$this->db->where($this->id, valid_id($data['brand_id']));
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_BRANDS_NAME, $vars))
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

			//validate the kb entries...
			$this->form_validation->set_rules('brand_name', 'lang:brand_name', 'trim|required|xss_clean',
				array( 'required' => $v[ 'language' ] . ' ' . lang('brand_name_required') ));

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
				$data[ 'lang' ][ $k ] = $this->dbv->validated($v, FALSE);
			}
		}

		//now validate the rest...
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		$vars = array( 'brand_status', 'sort_order' );
		foreach ($vars as $v)
		{
			$this->form_validation->set_rules($v, 'lang:' . $v, 'trim|required|integer');
		}

		$vars = array( 'brand_banner', 'brand_image', 'brand_notes');
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

/* End of file Brands_model.php */
/* Location: ./application/models/Brands_model.php */