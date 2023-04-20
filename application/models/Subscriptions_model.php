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
class Subscriptions_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'sub_id';

	// ------------------------------------------------------------------------

	/**
	 * Subscriptions_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('subscriptions');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function cancel($id = '')
	{
		if (!$this->db->where('sub_id', (int)$id)->update(TBL_MEMBERS_SUBSCRIPTIONS, array('status' => '0')))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool|false|string
	 */
	public function cancel_subscriptions()
	{
		$sql = 'UPDATE ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS) .
			' SET status = \'0\'
			WHERE next_due_date < CURDATE() - INTERVAL  ' . config_item('sts_cron_expired_subsriptions_interval') . ' DAY';

		if (!$this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$rows = $this->db->affected_rows();

		if (!empty($rows))
		{
			$row = array(
				'msg_text' => $rows . ' ' . lang('expired_subscriptions_cancelled_successfully'),
				'success'  => TRUE,
			);
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		if (!empty($data['member_id'])) //member is required to create a subscription
		{
			$vars = $this->dbv->clean($data, TBL_MEMBERS_SUBSCRIPTIONS);

			if (!empty($vars))
			{
				if (!$this->db->insert(TBL_MEMBERS_SUBSCRIPTIONS, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			//set the topic id
			$vars['subscription_id'] = $this->db->insert_id();

			return sc(array('id'       => $vars['subscription_id'],
			                'success'  => TRUE,
			                'data'     => $vars,
			                'msg_text' => lang('record_created_successfully'),
			));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function deactivate_subscriptions()
	{
		if (!$q = $this->db->where('intervals_required > ', '0')->get(TBL_MEMBERS_SUBSCRIPTIONS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $v)
			{
				if ($v['intervals_generated'] >= $v['intervals_required'])
				{
					$this->db->where('sub_id', $v['sub_id'])->update(TBL_MEMBERS_SUBSCRIPTIONS, array('status' => '0'));
				}
			}

			$q->free_result();
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $lang_id
	 * @return array|bool
	 */
	public function get_active_subscriptions($lang_id = '1')
	{
		$sql = 'SELECT p.*,
						d.*, s.*,
						p.shipping_data AS shipping_data,  
						t.product_sku
                	FROM ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id
                        AND d.language_id = \'' . (int)$lang_id . '\'
                     LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . ' t
                        ON p.product_id = t.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS) . ' s
                        ON p.order_id = s.order_id 
                    WHERE p.next_due_date < CURRENT_DATE + INTERVAL ' . config_item('sts_cron_generate_subscription_days_before') . ' DAY 
                        AND p.status = \'1\'
                    ORDER BY sub_id DESC';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array();

			foreach ($q->result_array() as $v)
			{
				if (!empty($v['intervals_required']))
				{
					if ($v['intervals_generated'] >= $v['intervals_required'])
					{
						continue;
					}
				}

				array_push($row, $v);
			}

			$q->free_result();
		}

		return !empty($row) ? $row : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = '1')
	{
		$sql = 'SELECT p.*, d.*, m.fname, m.lname, m.username, m.primary_email,
				g.charge_shipping, g.tax_class_id,
				c.order_number, f.invoice_number,
				DATE_FORMAT(p.start_date,\'' . $this->config->item('sql_date_format') . '\')
                    AS start_date_formatted,
                DATE_FORMAT(p.next_due_date,\'' . $this->config->item('sql_date_format') . '\')
                    AS next_due_date_formatted,
                       (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next
                FROM ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS) . ' p
                LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                    ON p.member_id = m.member_id
                LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS) . ' c
                    ON p.order_id = c.order_id
                LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' f
                    ON p.invoice_id = f.invoice_id        
                LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . ' g
                    ON p.product_id = g.product_id
                LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                    ON p.product_id = d.product_id
                    AND d.language_id = \'' . (int)$lang_id . '\'
                 WHERE ' . $this->id . ' = \'' . (int)$id . '\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = sc($q->row_array());

			$row['attributes'] = $this->att->get_product_attributes($row['product_id'], TRUE, $lang_id);

			if (!empty($row['attributes']))
			{
				$att = !empty($row['attribute_data']) ? unserialize($row['attribute_data']) : '';

				foreach ($row['attributes'] as $k => $v)
				{
					$row['attributes'][$k]['option_values'] = $this->att->get_product_attribute_values($v['prod_att_id'], $lang_id);

					switch ($v['attribute_type'])
					{
						case 'text':
						case 'textarea':

							$value = !empty($att[$row['attributes'][$k]['prod_att_id']]['value']) ? $att[$row['attributes'][$k]['prod_att_id']]['value'] : '';

							break;

						default:

							$value = !empty($att[$row['attributes'][$k]['prod_att_id']]['prod_att_value_id']) ? $att[$row['attributes'][$k]['prod_att_id']]['prod_att_value_id'] : '';

							break;
					}

					$class = $v['required'] == 1 ? 'class="form-control required"' : 'class="form-control"';
					$row['attributes'][$k]['form_html'] = format_attribute($v, $value, $class);
				}
			}

			if (!empty($row['charge_shipping']) && !empty($row['shipping_data']))
			{
				$row['shipping_data'] = unserialize($row['shipping_data']);
			}

			//get subscription history
			$row['history'] = $this->get_subscription_history($id);

			return $row;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param int $limit
	 * @return bool|false|string
	 */
	public function get_member_subscriptions($id = '', $lang_id = '1', $limit = ADMIN_MEMBERS_RECENT_DATA)
	{
		$sql = 'SELECT *
                FROM ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS) . ' p
                LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                    ON p.product_id = d.product_id
                    AND d.language_id = \'' . (int)$lang_id . '\'
                 WHERE member_id = \'' . (int)$id . '\'
                ORDER BY sub_id DESC    
                LIMIT ' . $limit;

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = '1')
	{
		//set the sort order for this query
		$sort = $this->config->item(TBL_MEMBERS_SUBSCRIPTIONS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$select = 'SELECT p.*,d.*,
		                    m.fname,
		                    m.lname'; //for the page rows only

		$count = 'SELECT COUNT(p.sub_id) AS total 	
					FROM ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS) . ' p '; //for pagination totals

		//sql query
		$sql = ' FROM ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                        ON p.member_id = m.member_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id
                        AND d.language_id = \'' . (int)$lang_id . '\'';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_MEMBERS_SUBSCRIPTIONS, TBL_PRODUCTS_NAME), $options['query']);

			$sql .= $options['where_string'];
			$count .= $options['where_string'];
		}

		//set the order and limit clause
		$order = ' GROUP BY p.sub_id
                    ORDER BY p.' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		if (!$q = $this->db->query($select . $sql . $order))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->dbv->get_query_total($count),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_subscription_history($id = '')
	{
		$sql = 'SELECT p.*, a.order_number
					FROM ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS_HISTORY) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS) . ' a
					    ON p.order_id = a.order_id
					WHERE p.' . $this->id . ' = \'' . (int)$id . '\'
					    ORDER BY hid DESC';

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param int $limit
	 * @param bool $get_cache
	 * @return bool|false|string
	 */
	public function get_user_subscriptions($id = '', $lang_id = '', $limit = MEMBER_RECORD_LIMIT, $get_cache = TRUE)
	{
		$sort = $this->config->item(TBL_MEMBERS_SUBSCRIPTIONS, 'db_sort_order');

		$sql = 'SELECT p.*, d.*, p.product_id AS product_id
					FROM ' . $this->db->dbprefix(TBL_MEMBERS_SUBSCRIPTIONS) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
	                    ON (p.product_id = d.product_id
                        AND d.language_id = \'' . (int)$lang_id . '\')
				    WHERE p.member_id = \'' . (int)$id . '\'
					        ORDER BY ' . $sort['column'] . ' ' . $sort['order'] . '
					        LIMIT ' . $limit;

		//check if we have a cache file and return that instead
		$cache = __METHOD__ . md5($sql);

		if ($row = $this->init->cache($cache, 'public_db_query'))
		{
			if ($get_cache == TRUE)
			{
				return sc($row);
			}
		}

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'rows'    => $q->result_array(),
				'success' => TRUE,
			);

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function insert_subscription_history($data = array())
	{
		$vars = array('sub_id'   => $data['sub_id'],
		              'order_id' => $data['order_id']);

		if (!$this->db->insert(TBL_MEMBERS_SUBSCRIPTIONS_HISTORY, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return bool|false|string
	 */
	public function update($id = '', $data = array())
	{
		$vars = $this->dbv->clean($data, TBL_MEMBERS_SUBSCRIPTIONS);

		if (!empty($data['carrier']) && !empty($data['service']))
		{
			$vars['shipping_data'] = serialize(array('carrier' => $data['carrier'],
			                                         'service' => $data['service']));
		}

		if (!empty($vars))
		{
			if (!$this->db->where($this->id, $id)->update(TBL_MEMBERS_SUBSCRIPTIONS, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = array('id'       => $vars['subscription_id'],
			             'success'  => TRUE,
			             'data'     => $vars,
			             'msg_text' => lang('record_updated_successfully'),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function update_subscription_history($data = array())
	{
		$this->insert_subscription_history($data);

		$vars = array('order_id'            => $data['order_data']['order']['order_id'],
		              'invoice_id'          => $data['invoice_data']['data']['invoice_id'],
		              'next_due_date'       => get_next_due_date($data),
		              'intervals_generated' => $data['intervals_generated'] + 1,
		);

		if (!$this->db->where('sub_id', $data['sub_id'])->update(TBL_MEMBERS_SUBSCRIPTIONS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $attributes
	 * @return array|string
	 */
	public function validate($data = array(), $attributes = array())
	{
		$error = '';
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('subscriptions', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_MEMBERS_SUBSCRIPTIONS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|strip_tags|xss_clean';

			//if this field is a required field, let's set that
			if (in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			//go through each field type first and validate based on it....
			$rule .= generate_db_rule($f->type, $f->max_length, TRUE, $f->name);

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if (!$this->form_validation->run())
		{
			//sorry! got some errors here....
			$error = validation_errors();
		}
		else
		{
			//cool! no errors...
			$post = $this->dbv->validated($data);

			if (!empty($attributes))
			{
				$post['attribute_data'] = array();

				//check each product attribute if its required
				foreach ($attributes as $v)
				{
					$msg = '';
					//check for valid attribute data
					if (!empty($data['attribute_id'][$v['prod_att_id']]))
					{
						$option = validate_attribute($v, $data['attribute_id'][$v['prod_att_id']], 1);

						$post['attribute_data'][$v['prod_att_id']] = $option;

						if (!empty($option['msg']))
						{
							$msg = $option['msg'];
						}
					}
					else
					{
						if ($v['required'] == 1)
						{
							//attribute is required
							$msg = $v['attribute_name'] . ' ' . lang('required');
						}
					}

					if (!empty($msg))
					{
						$error .= $msg;
					}

					//set the option data
					if (!empty($post['attribute_data']))
					{
						$post['attribute_data'][$v['prod_att_id']]['attribute_type'] = $v['attribute_type'];
						$post['attribute_data'][$v['prod_att_id']]['value'] = empty($option['option_name']) ? $post['attribute_id'][$v['prod_att_id']] : $option['option_name'];
					}
				}
			}

			$post['attribute_data'] = !empty($post['attribute_data']) ? serialize($post['attribute_data']) : '';
			$post['specification_data'] = '';


			$row = array('success' => TRUE,
			             'data'    => $post,
			);
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

/* End of file Subscriptions_model.php */
/* Location: ./application/models/Subscriptions_model.php */
