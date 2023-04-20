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
 * @package    eCommerce Suite
 * @author    JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link    https://www.jrox.com
 * @filesource
 */
class Tax_classes_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'tax_class_id';

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $rates_id = 'tax_rate_id';

	// ------------------------------------------------------------------------

	/**
	 * Tax_classes_model constructor.
	 */
	public function __construct()
	{
		//load tax helpers
		$this->load->helper('tax');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @return bool
	 */
	public function ajax_search($term = '')
	{
		$this->db->like('tax_rate_name', $term);
		$this->db->select('tax_rate_id, tax_rate_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_TAX_RATES))
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
	 * @return array|bool
	 */
	public function create_class()
	{
		$vars = array(
			'class_name'        => lang('tax_class'),
			'class_description' => lang('new_tax_description'),
		);

		if (!$this->db->insert(TBL_TAX_CLASSES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return array|bool
	 */
	public function create_rate()
	{
		$vars = array(
			'tax_rate_name' => lang('tax_rate'),
			'zone_id'       => '1',
			'tax_type'      => 'sales',
			'amount_type'   => 'percent',
			'tax_amount'    => '0.00',
		);

		if (!$this->db->insert(TBL_TAX_RATES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $tax_rate_id
	 * @param string $tax_class_id
	 * @return bool
	 */
	public function delete_class_tax_rate($tax_rate_id = '', $tax_class_id = '')
	{
		//first delete product attribute option values
		if (!$this->db->delete(TBL_TAX_RATE_RULES, array('tax_rate_id'  => $tax_rate_id,
		                                                 'tax_class_id' => $tax_class_id))
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete_rate($id = '')
	{
		//first delete rate value
		if (!$this->db->delete(TBL_TAX_RATE_RULES, array('tax_rate_id' => $id)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//now delete the rates
		if (!$this->db->delete(TBL_TAX_RATES, array('tax_rate_id' => $id)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row['msg_text'] = lang('record_deleted_successfully');

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete_class($id = '')
	{
		//delete rate value
		if (!$this->db->delete(TBL_TAX_RATE_RULES, array('tax_class_id' => $id)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//now delete the class
		if (!$this->db->delete(TBL_TAX_CLASSES, array('tax_class_id' => $id)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row['msg_text'] = lang('record_deleted_successfully');

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_tax_rates_details($id = '')
	{
		$sql = 'SELECT *,
				    (SELECT ' . $this->rates_id . '
				        FROM ' . $this->db->dbprefix(TBL_TAX_RATES) . ' p
				        WHERE p.' . $this->rates_id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->rates_id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->rates_id . '
				        FROM ' . $this->db->dbprefix(TBL_TAX_RATES) . ' p
				        WHERE p.' . $this->rates_id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->rates_id . '` ASC LIMIT 1)
				        AS next
				    FROM ' . $this->db->dbprefix(TBL_TAX_RATES) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_ZONES) . ' c ON (p.zone_id= c.zone_id)
                    WHERE p.' . $this->rates_id . '= ' . (int)$id . '';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_tax_class_details($id = '')
	{
		$cache = __METHOD__ . $id;

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			$sql = 'SELECT p.*,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                            AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' p
                            WHERE p.' . $this->id . ' > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                            AS next
                        FROM ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' p
                        WHERE p.' . $this->id . '= ' . (int)$id . '';

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();

				$row['rates'] = $this->get_rate_rules($id);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_rate_rules($id = '')
	{
		$cache = __METHOD__ . $id;

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			$this->db->join(TBL_TAX_RATES,
				$this->db->dbprefix(TBL_TAX_RATES) . '.tax_rate_id = ' .
				$this->db->dbprefix(TBL_TAX_RATE_RULES) . '.tax_rate_id', 'left');

			$this->db->order_by($this->db->dbprefix(TBL_TAX_RATE_RULES) . '.priority', 'ASC');

			$this->db->where('tax_class_id', $id);
			if (!$q = $this->db->get(TBL_TAX_RATE_RULES))
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
	 * @param bool $form
	 * @return array|bool|false
	 */
	public function get_tax_classes($form = FALSE)
	{
		$cache = __METHOD__ . $form;

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->order_by('tax_class_id', 'ASC')->get(TBL_TAX_CLASSES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $form == TRUE ? format_array($q->result_array(), 'tax_class_id', 'class_name', TRUE) : $q->result_array();

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @param string $type
	 * @return array|bool|false
	 */
	public function get_tax_rates($form = FALSE, $type = '')
	{
		$cache = __METHOD__;

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!empty($type))
			{
				$this->db->where('tax_type', $type);
			}

			$this->db->join(TBL_ZONES,
				$this->db->dbprefix(TBL_TAX_RATES) . '.zone_id = ' .
				$this->db->dbprefix(TBL_ZONES) . '.zone_id', 'left');

			if (!$q = $this->db->order_by('tax_rate_id', 'ASC')->get(TBL_TAX_RATES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				if ($form == TRUE)
				{
					$row = format_array($q->result_array(), 'tax_rate_id', 'tax_rate_name', TRUE);
				}
				else
				{
					$row = array(
						'values'         => $q->result_array(),
						'total'          => $q->num_rows(),
						'debug_db_query' => $this->db->last_query(),
					);
				}

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $tax_rate_id
	 * @param string $tax_class_id
	 * @return bool
	 */
	public function insert_class_tax_rate($tax_rate_id = '', $tax_class_id = '')
	{
		$vars = array('tax_rate_id'  => (int)$tax_rate_id,
		              'tax_class_id' => (int)$tax_class_id,
		);

		if (!$this->db->insert(TBL_TAX_RATE_RULES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update_class_tax_rates($id = '', $data = array())
	{
		$a = $data['tax_rates'];
		$b = $this->get_rate_rules($id, FALSE);

		$c = array();
		if (!empty($b))
		{
			foreach ($b as $v)
			{
				if (!in_array($v['tax_rate_id'], $a))
				{
					//delete from db
					$this->delete_class_tax_rate($v['tax_rate_id'], $v['tax_class_id']);
				}
				else
				{
					array_push($c, $v['tax_rate_id']);
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
					//insert the rate into db
					$this->insert_class_tax_rate($v, $id);
				}
			}
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
	public function update_tax_class($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_TAX_CLASSES);

		$this->db->where($this->id, $data[$this->id]);

		if (!$this->db->update(TBL_TAX_CLASSES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if (!empty($data['rate_rules']))
		{
			foreach ($data['rate_rules'] as $k => $v)
			{
				$vars = $this->dbv->clean($v, TBL_TAX_RATE_RULES);

				$this->dbv->update(TBL_TAX_RATE_RULES, $this->rates_id, $vars);
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
	public function validate_tax_class($data = array())
	{
		$error = '';

		if (!empty($data['rate_rules']))
		{
			foreach ($data['rate_rules'] as $k => $v)
			{
				$this->form_validation->reset_validation();
				$this->form_validation->set_data($v);

				$required = $this->config->item(TBL_TAX_RATE_RULES, 'required_input_fields');

				//now get the list of fields directly from the table
				$fields = $this->db->field_data(TBL_TAX_RATE_RULES);

				foreach ($fields as $f)
				{
					//set the default rule
					$rule = 'trim|xss_clean';

					switch ($f->name)
					{
						case 'calculation':

							$rule .= '|in_list[shipping,billing]';

							break;

						case 'priority':
						case 'tax_rate_id':

							$rule .= '|integer';

							break;
					}

					$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
				}

				if (!$this->form_validation->run())
				{

					$error .= validation_errors();
				}
				else
				{
					$data['rate_rules'][ $k ] = $this->dbv->validated($v, FALSE);
				}
			}
		}

		//now validate the rest...
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		$required = $this->config->item(TBL_TAX_CLASSES, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_TAX_CLASSES);

		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean';

			//if this field is a required field, let's set that
			if (is_array($required) && in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
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

/* End of file Tax_classes_model.php */
/* Location: ./application/models/Tax_classes_model.php */