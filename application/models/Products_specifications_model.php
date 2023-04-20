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
class Products_specifications_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'spec_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @param int $lang_id
	 * @return bool
	 */
	public function ajax_search($term = '', $lang_id = 1)
	{
		$this->db->like('specification_name', $term);
		$this->db->where('language_id', $lang_id);
		$this->db->select('spec_id, specification_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_PRODUCTS_SPECIFICATIONS_NAME))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			return $q->result_array();
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = array('sort_order' => '0');

		if (!$q = $this->db->insert(TBL_PRODUCTS_SPECIFICATIONS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$vars[ 'spec_id' ] = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data as $v)
		{
			$vars = array(
				$this->id          => $vars[ 'spec_id' ],
				'language_id'      => $v['language_id'],
				'specification_name'    =>  lang('new_specification'),
			);

			if (!$q = $this->db->insert(TBL_PRODUCTS_SPECIFICATIONS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array( 'success'  => TRUE,
		                 'data'     => $vars,
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
		if (!$this->db->where($this->id, $id)->delete(TBL_PRODUCTS_SPECIFICATIONS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array( 'success'  => TRUE,
		                 'id'       => $id,
		                 'msg_text' => lang('record_deleted_successfully') ));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $product_id
	 * @param string $spec_id
	 * @return bool
	 */
	public function delete_product_specifications($product_id = '', $spec_id = '')
	{
		//now delete the product specifications
		if (!empty($spec_id))
		{
			$this->db->where('spec_id', $spec_id);
		}

		if (!$this->db->where('product_id', $product_id)->delete(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_details($id = '')
	{
		if ($row = $this->dbv->get_record(TBL_PRODUCTS_SPECIFICATIONS, $this->id, $id))
		{
			//get names
			$row[ 'lang' ] = $this->dbv->get_names(TBL_PRODUCTS_SPECIFICATIONS_NAME, $this->id, $id);
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = '1')
	{
		//set the cache file
		$cache = __METHOD__ . $options[ 'md5' ];
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			$sort = $this->config->item(TBL_PRODUCTS_SPECIFICATIONS, 'db_sort_order');

			$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
			$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

			$sql = 'SELECT *
                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_SPECIFICATIONS) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_SPECIFICATIONS_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . $lang_id . '\') ';

			if (!empty($options[ 'query' ]))
			{
				$this->dbv->validate_columns(array( TBL_PRODUCTS_SPECIFICATIONS ), $options[ 'query' ]);

				$sql .= $options[ 'where_string' ];
			}

			$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . '
                        LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

			$query = $this->db->query($sql);
			

			if ($query->num_rows() > 0)
			{
				$row = array(
					'values'         => $query->result_array(),
					'total'          => $this->dbv->get_table_totals($options, TBL_PRODUCTS_SPECIFICATIONS),
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
	 * @param string $id
	 * @param bool $public
	 * @param string $lang_id
	 * @return bool|mixed
	 */
	public function get_product_spec_values($id = '', $public = FALSE, $lang_id = '')
	{
		$cache = __METHOD__ . $id;
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			//get product specifications

			$this->db->join(TBL_PRODUCTS_SPECIFICATIONS_NAME,
				$this->db->dbprefix(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME) . '.spec_name_id = ' .
				$this->db->dbprefix(TBL_PRODUCTS_SPECIFICATIONS_NAME) . '.spec_name_id', 'left');
			$this->db->join(TBL_LANGUAGES,
				$this->db->dbprefix(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME) . '.language_id = ' .
				$this->db->dbprefix(TBL_LANGUAGES) . '.language_id', 'left');
			$this->db->join(TBL_PRODUCTS_SPECIFICATIONS,
				$this->db->dbprefix(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME) . '.spec_id = ' .
				$this->db->dbprefix(TBL_PRODUCTS_SPECIFICATIONS) . '.spec_id', 'left');

			$this->db->group_by('prod_spec_id');
			if (!$q = $this->db->where('product_id', $id)
				->order_by('sort_order', 'ASC')
				->get(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME)
			)
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();

				$row = format_product_specs($row);

				if ($public == TRUE)
				{
					if (!empty($lang_id))
					{
						$row = $row[ $lang_id ];
					}

					// Save into the cache
					$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
				}

			}
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param string $fields
	 * @return bool
	 */
	public function get_product_specs($id = '', $lang_id = '1', $fields = '*')
	{
		//get product specifications
		$this->db->select($fields);

		$this->db->join(TBL_PRODUCTS_SPECIFICATIONS_NAME,
			$this->db->dbprefix(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME) . '.spec_name_id = ' .
			$this->db->dbprefix(TBL_PRODUCTS_SPECIFICATIONS_NAME) . '.spec_name_id', 'left');

		$this->db->where(TBL_PRODUCTS_SPECIFICATIONS_NAME . '.language_id', $lang_id);

		$cache = __METHOD__ . $id . $lang_id . $fields;
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->where('product_id', $id)
				->get(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME)
			)
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $spec_id
	 * @return bool
	 */
	public function get_product_language_specs($spec_id)
	{
		$this->db->where('spec_id', $spec_id);

		if (!$q = $this->db->get(TBL_PRODUCTS_SPECIFICATIONS_NAME))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			return $q->result_array();
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $spec_id
	 * @param string $product_id
	 * @return bool
	 */
	public function insert_product_specification($spec_id = '', $product_id = '')
	{
		//get language data
		$a = $this->get_product_language_specs($spec_id);

		foreach ($a as $v)
		{
			$vars = array( 'product_id'   => $product_id,
			               'spec_id'      => $spec_id,
			               'spec_name_id' => $v[ 'spec_name_id' ],
			               'language_id'  => $v[ 'language_id' ],
			);

			if (!$this->db->insert(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return TRUE;
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
			foreach ($data['sort_order'] as $k => $v)
			{
				$vars = array( 'sort_order'      => (int)$v);

				if (!$this->db->where('spec_id', $k)->update(TBL_PRODUCTS_SPECIFICATIONS, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			foreach ($data['specification_name'] as $k => $v)
			{
				$vars = array( 'specification_name'      => $v);

				$this->db->where('language_id', $lang_id);
				if (!$this->db->where('spec_id', $k)->update(TBL_PRODUCTS_SPECIFICATIONS_NAME, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			$row = array( 'success'  => TRUE,
			              'data'     => $data,
			              'msg_text' => lang('mass_update_successful'),
			);

			$this->dbv->db_sort_order(TBL_PRODUCTS_SPECIFICATIONS, 'spec_id', 'sort_order');
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
		foreach ($data['lang'] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_PRODUCTS_SPECIFICATIONS_NAME);

			$this->db->where($this->id, $data['spec_id']);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_PRODUCTS_SPECIFICATIONS_NAME, $vars))
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
	 * @param string $id
	 * @param string $lang_id
	 * @param array $data
	 * @return array
	 */
	public function update_product_specifications($id = '', $lang_id = '1', $data = array())
	{
		if (!empty($data['product_specifications']))
		{
			$a = $data['product_specifications']; //new specifications
			$b = $this->get_product_specs($id, $lang_id); //specifications in the db

			$c = array();
			if (!empty($b))
			{
				foreach ($b as $v) //let's delete all the specifications not in the current one
				{
					if (!in_array($v['spec_id'], $a))
					{
						//delete the specification from db
						$this->delete_product_specifications($v['product_id'], $v['spec_id']);
					}
					else
					{
						array_push($c, $v['spec_id']);
					}
				}
			}

			//now add the new ones
			if (!empty($a))
			{
				foreach ($a as $v)
				{
					if (!in_array($v, $c))
					{
						//insert the specification into db
						$this->insert_product_specification($v, $id);
					}
				}
			}
		}
		else
		{
			$this->delete_product_specifications($id, $v['spec_id']);
		}

		$row = array(
			'id'       => $id,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
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

		foreach ($data['lang'] as $k => $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);
			$this->form_validation->set_rules('specification_name', 'lang:specification_name', 'trim|required|strip_tags|xss_clean',
				array( 'required' => $v[ 'language' ] . ' ' . lang('specification_name_required') ));

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
			              'data'    => $this->dbv->validated($data),
			);
		}

		return $row;
	}
}

/* End of file Products_specifications_model.php */
/* Location: ./application/models/Products_specifications_model.php */