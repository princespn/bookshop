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
class Suppliers_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'supplier_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @return array
	 */
	public function ajax_search($term = '')
	{
		$this->db->like('supplier_name', $term);
		$this->db->select('supplier_id, supplier_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_SUPPLIERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$d[] = array( 'supplier_id'   => '0',
		              'supplier_name' => 'none' );

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
	 * @return array|bool
	 */
	public function create($data = array())
	{
		$data = $this->dbv->clean($data, TBL_SUPPLIERS);

		if (!$this->db->insert(TBL_SUPPLIERS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$data[ 'supplier_id' ] = $this->db->insert_id();

		$row = array(
			'data'     => $data,
			'msg_text' => lang('record_added_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function delete($id = '')
	{
		if ($id != config_option('default_supplier_id'))
		{
			//update product supplier IDs
			$this->dbv->reset_id(TBL_PRODUCTS,
				'supplier_id',
				$id,
				config_option('default_supplier_id')
			);

			if (!$this->db->where($this->id, $id)->delete(TBL_SUPPLIERS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array( 'success'  => TRUE,
			              'data'     => $id,
			              'msg_text' => lang('record_deleted_successfully') );
		}
		else
		{
			$row = array( 'error'    => TRUE,
			              'msg_text' => lang('could_not_delete_default_record'),
			);
		}

		return empty($row) ? FALSE : sc($row);

	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool|false|string
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_SUPPLIERS, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT * ';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(brand_id) FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' n
			    WHERE p.' . $this->id . ' = n.' . $this->id . ') AS total ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_SUPPLIERS) . ' p ';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_SUPPLIERS ), $options[ 'query' ]);

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
					'total'          => $this->dbv->get_table_totals($options, TBL_SUPPLIERS),
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
	 * @return bool|false|string
	 */
	public function get_details($id = '')
	{
		$sql = 'SELECT *,
                    (SELECT ' . $this->id . '
                        FROM ' . $this->db->dbprefix(TBL_SUPPLIERS) . ' p
                        WHERE p.' . $this->id . ' < ' . (int)$id . '
                        ORDER BY `' . $this->id . '` DESC LIMIT 1)
                        AS prev,
                    (SELECT ' . $this->id . '
                        FROM ' . $this->db->dbprefix(TBL_SUPPLIERS) . ' p
                        WHERE p.' . $this->id . ' > ' . (int)$id . '
                        ORDER BY `' . $this->id . '` ASC LIMIT 1)
                        AS next
                    FROM ' . $this->db->dbprefix(TBL_SUPPLIERS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' v
                            ON p.supplier_country = v.country_id
                    WHERE p.' . $this->id . '= ' . (int)$id . '';

		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'db_query'))
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
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function mass_update($data = array())
	{
		foreach ($data['suppliers'] as $k => $v)
		{
			$v = $this->dbv->clean($v);
			$vars = array('supplier_name' => valid_id($v['supplier_name'], true),
			              'supplier_email' => xss_clean($v['supplier_email']),
			              'supplier_phone' => valid_id($v['supplier_phone'], true)
			);

			if (!$this->db->where($this->id, $k)->update(TBL_SUPPLIERS, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array('success'  => TRUE,
		                'data'     => $data['suppliers'],
		                'msg_text' => lang('mass_update_successful') )
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array|bool
	 */
	public function update($data = array())
	{
		$data = $this->dbv->clean($data, TBL_SUPPLIERS);

		$this->db->where($this->id, valid_id($data[ 'supplier_id' ]));
		if (!$this->db->update(TBL_SUPPLIERS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'data'     => $data,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function validate($data = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('suppliers', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_SUPPLIERS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|strip_tags|xss_clean';

			//if this field is a required field, let's set that
			if (in_array($f, $required))
			{
				$rule .= '|required';
			}

			//check if this is an admin or member response
			switch ($f)
			{
				case 'supplier_email':

					$rule .= '|valid_email';

					break;

				case 'supplier_country':
				case 'suppliery_state':

					$rule .= '|integer';

					break;
			}

			$this->form_validation->set_rules($f, 'lang:' . $f, $rule);
		}

		if ($this->form_validation->run())
		{
			$row = array( 'success' => TRUE,
			              'data'    => $this->dbv->validated($data));
		}
		else
		{
			$row = array( 'error' => TRUE,
			              'msg_text'    => validation_errors());
		}

		return empty($row) ? FALSE : sc($row);
	}

}

/* End of file Suppliers_model.php */
/* Location: ./application/models/Suppliers_model.php */