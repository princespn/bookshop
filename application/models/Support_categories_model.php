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
class Support_categories_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'category_id';


	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = array(   'sort_order' => '1');

		if (!$q = $this->db->insert(TBL_SUPPORT_CATEGORIES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data as $v)
		{
			$vars = array(
				'category_id'      => $id,
				'language_id' => $v[ 'language_id' ],
				'category_name'    => lang('new_category'),
				'category_description'      => lang('short_description'),
			);

			if (!$q = $this->db->insert(TBL_SUPPORT_CATEGORIES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array( 'success'  => TRUE,
		                 'id'       => $id,
		                 'msg_text' => lang('record_created_successfully'),
		));
	}

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete($id = '')
	{
		//do not delete the default category...
		if ($id != config_option('default_support_category_id'))
		{
			//update category IDs for records
			$this->dbv->reset_id(TBL_SUPPORT_TICKETS, $this->id, $id, config_option('default_support_category_id'));

			if (!$this->db->where($this->id, $id)->delete(TBL_SUPPORT_CATEGORIES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
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
		$sort = $this->config->item(TBL_SUPPORT_CATEGORIES, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT * ';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(' . $this->id . ') FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS) . ' b
			    WHERE p.' . $this->id . ' = b.' . $this->id . ') AS total ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_SUPPORT_CATEGORIES) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_SUPPORT_CATEGORIES_NAME) . ' c
						ON (p.' . $this->id . ' = c.' . $this->id . '
						AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_SUPPORT_CATEGORIES,
			                                    TBL_SUPPORT_CATEGORIES_NAME ), $options[ 'query' ]);

			$sql .= $options[ 'where_string' ];
		}

		$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . '
					LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}



		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_SUPPORT_CATEGORIES),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	/**
	 * @param int $lang_id
	 * @param bool $form
	 * @return bool|false|string
	 */
	public function get_categories($lang_id = 1, $form = FALSE)
	{
		$sort = $this->config->item(TBL_SUPPORT_CATEGORIES, 'db_sort_order');

		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_SUPPORT_CATEGORIES) . ' p
				LEFT JOIN ' . $this->db->dbprefix(TBL_SUPPORT_CATEGORIES_NAME) . ' c
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
	 * @param string $id
	 * @return bool
	 */
	public function get_details($id = '')
	{
		if ($row = $this->dbv->get_record(TBL_SUPPORT_CATEGORIES, $this->id, $id))
		{
			//get names
			$row[ 'lang' ] = $this->dbv->get_names(TBL_SUPPORT_CATEGORIES_NAME, $this->id, $id);
		}

		return empty($row) ? FALSE : $row;
	}

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update($id = '', $data = array())
	{
		foreach ($data as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_SUPPORT_CATEGORIES_NAME);

			$this->db->where($this->id, $id);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_SUPPORT_CATEGORIES_NAME, $vars))
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

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		$error = '';

		foreach ($data as $k => $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);
			$this->form_validation->set_rules('category_name', 'lang:category_name', 'trim|required|xss_clean',
				array( 'required' => $v[ 'language' ] . ' ' . lang('name_required') ));

			$this->form_validation->set_rules('category_description', 'lang:category_description', 'trim|required|xss_clean',
				array( 'required' => $v[ 'language' ] . ' ' . lang('description_required') ));

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

/* End of file Support_categories_model.php */
/* Location: ./application/models/Support_categories_model.php */