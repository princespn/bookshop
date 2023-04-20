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
class Products_attributes_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'attribute_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @param int $lang_id
	 * @return bool
	 */
	public function ajax_search($term = '', $lang_id = 1)
	{
		$this->db->like('attribute_name', $term);
		$this->db->where('language_id', $lang_id);
		$this->db->select('attribute_id, attribute_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_PRODUCTS_ATTRIBUTES_NAME))
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
	 * @return bool|false|string
	 */
	public function create($data = array())
	{
		//check default values and clean the input
		$fields = $this->db->field_data(TBL_PRODUCTS_ATTRIBUTES);

		foreach ($fields as $v)
		{
			if ($v->name != 'attribute_id')
			{
				switch ($v->name)
				{
					case 'attribute_sku':
						$insert[$v->name] = $this->dbv->clean_field($data, $v, $v->name, strtoupper(random_string('alnum', DEFAULT_SKU_LENGTH)));
						break;

					case 'sort_order':
						$insert[$v->name] = '0';
						break;

					default:
						$insert[$v->name] = $this->dbv->clean_field($data, $v, $v->name);
						break;
				}
			}
		}

		if (!$this->db->insert(TBL_PRODUCTS_ATTRIBUTES, $insert))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//set the attribute ID
		$id = $this->db->insert_id();

		$this->dbv->db_sort_order(TBL_PRODUCTS_ATTRIBUTES, 'attribute_id', 'sort_order');

		//clean it then add to product names table
		$fields = $this->db->field_data(TBL_PRODUCTS_ATTRIBUTES_NAME);

		//get languages that are installed first
		$lang = get_languages(FALSE, FALSE);

		foreach ($lang as $a)
		{
			foreach ($fields as $v)
			{
				if ($v->name != 'att_name_id')
				{
					switch ($v->name)
					{
						case 'language_id':
							$b[$v->name] = $this->dbv->clean_field($data, $v, $v->name, $a['language_id']);
							break;

						case 'attribute_name':
						case 'description':
							$b[$v->name] = $this->dbv->clean_field($data, $v, $v->name, lang('new_attribute'));
							break;

						case 'attribute_id':
							$b[$v->name] = $id;
							break;

						default:
							$b[$v->name] = $this->dbv->clean_field($data, $v, $v->name);
							break;
					}
				}
			}

			if (!$this->db->insert(TBL_PRODUCTS_ATTRIBUTES_NAME, $b))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		//add some default options for select type attributes
		//check default values and clean the input
		$fields = $this->db->field_data(TBL_PRODUCTS_ATTRIBUTES_OPTIONS);

		foreach ($fields as $v)
		{
			if ($v->name != 'option_id')
			{
				switch ($v->name)
				{
					case 'attribute_id':
						$option[$v->name] = $id;
						break;

					case 'sort_order':
						$option[$v->name] = '1';
						break;

					default:
						$option[$v->name] = $this->dbv->clean_field($data, $v, $v->name);
						break;
				}
			}
		}

		if (!$this->db->insert(TBL_PRODUCTS_ATTRIBUTES_OPTIONS, $option))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//new option ID...
		$option_id = $this->db->insert_id();

		//clean it then add to product names table
		$fields = $this->db->field_data(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME);

		foreach ($lang as $a)
		{
			foreach ($fields as $v)
			{
				if ($v->name != 'att_option_name_id')
				{
					switch ($v->name)
					{
						case 'language_id':
							$c[$v->name] = $this->dbv->clean_field($data, $v, $v->name, $a['language_id']);
							break;

						case 'option_name':
						case 'option_description':
							$c[$v->name] = $this->dbv->clean_field($data, $v, $v->name, lang('new_option'));
							break;

						case 'option_id':
							$c[$v->name] = $option_id;
							break;
					}
				}
			}

			if (!$this->db->insert(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME, $c))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$row = array(
			'id'       => $id,
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete($id = '')
	{
		foreach (array(TBL_PRODUCTS_TO_ATTRIBUTES, TBL_PRODUCTS_ATTRIBUTES) as $v)
		{
			if (!$this->db->where($this->id, $id)->delete($v))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array('success'  => TRUE,
		                'id'       => $id,
		                'msg_text' => lang('record_deleted_successfully')));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $attribute_id
	 * @param string $prod_att_id
	 * @return bool
	 */
	public function delete_product_attributes($attribute_id = '', $prod_att_id = '')
	{
		//first delete product attribute option values
		if (!$this->db->delete(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES, array('prod_att_id' => $prod_att_id)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//now delete the product attributes
		if (!$this->db->delete(TBL_PRODUCTS_TO_ATTRIBUTES, array('prod_att_id' => $prod_att_id)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @return bool
	 */
	public function get_attribute_option($id = '', $lang_id = '1')
	{
		//get associated attribute values
		$this->db->select('*,' .
			$this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES) . '.status
                AS option_status');
		$this->db->join(TBL_PRODUCTS_ATTRIBUTES_OPTIONS,
			$this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES) . '.option_id = ' .
			$this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_OPTIONS) . '.option_id', 'left');
		$this->db->join(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME,
			$this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES) . '.option_id = ' .
			$this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME) . '.option_id', 'left');
		$this->db->join(TBL_LANGUAGES,
			$this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME) . '.language_id = ' .
			$this->db->dbprefix(TBL_LANGUAGES) . '.language_id', 'left');
		$this->db->where($this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME) . '.language_id', $lang_id);
		if (!$q = $this->db->where('prod_att_value_id', $id)
			->get(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = '1')
	{

		$sql = 'SELECT *,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                            AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES) . ' p
                            WHERE p.' . $this->id . ' > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                            AS next
                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_NAME) . ' v
                                ON p.' . $this->id . ' = v.' . $this->id . '
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

				$row['option_values'] = $this->get_attribute_options($id);
				$row['lang'] = $this->get_attribute_names($id);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_attribute_options($id = '')
	{
		//get associated attribute options
		if (!$q = $this->db->where($this->id, $id)
			->get(TBL_PRODUCTS_ATTRIBUTES_OPTIONS)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$rows = array();
			foreach ($q->result_array() as $row)
			{
				$row['lang'] = $this->get_attribute_option_names($row['option_id']);
				array_push($rows, $row);
			}
		}

		return empty($rows) ? FALSE : sc($rows);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_attribute_option_names($id = '')
	{
		//get associated attribute option names
		$this->db->select(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME . '.*, ' . TBL_LANGUAGES . '.name AS lang_name,' .
			TBL_LANGUAGES . '.code,' . TBL_LANGUAGES . '.image');
		$this->db->join(TBL_LANGUAGES,
			$this->db->dbprefix(TBL_LANGUAGES) . '.language_id = ' .
			$this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME) . '.language_id', 'left');

		if (!$q = $this->db->where('option_id', $id)
			->get(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME)
		)
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
	 * @param $id
	 * @return bool|false|string
	 */
	public function get_attribute_names($id)
	{
		$this->db->join(TBL_LANGUAGES,
			$this->db->dbprefix(TBL_LANGUAGES) . '.language_id = ' .
			$this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_NAME) . '.language_id', 'left');

		if (!$q = $this->db->where($this->id, $id)->get(TBL_PRODUCTS_ATTRIBUTES_NAME))
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
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_PRODUCTS_ATTRIBUTES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT * ';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(attribute_id) FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES) . '
			    WHERE p.attribute_id = ' . $this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES) . '.attribute_id) AS total ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES) . ' p
				LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_NAME) . ' c ON (p.' . $this->id . ' = c.' . $this->id . '
				AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_PRODUCTS_ATTRIBUTES), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$q = $this->db->query($sql);


		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->dbv->get_table_totals($options, TBL_PRODUCTS_ATTRIBUTES),
				'success' => TRUE,
			);

			return sc($row);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $options
	 * @param string $lang_id
	 * @param bool $format
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_product_attributes($id = '', $options = TRUE, $lang_id = '1', $format = FALSE, $public = FALSE)
	{
		$sql = 'SELECT *
					FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES) . ' n
						ON p.attribute_id = n.attribute_id
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_NAME) . ' c
						ON p.attribute_id = c.attribute_id
						AND language_id = ' . valid_id($lang_id) . '
					WHERE product_id = ' . valid_id($id) . '
						ORDER BY `sort_order` ASC';

		$cache = __METHOD__ . md5($sql);
		if (!$rows = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$rows = $q->result_array();

				if ($options == TRUE) //get attribute option values
				{
					foreach ($rows as $k => $v)
					{
						$rows[$k]['option_values'] = $this->get_product_attribute_values($v['prod_att_id'], $lang_id, $public);

						if ($format == TRUE)
						{
							$class = $v['required'] == 1 ? 'class="form-control required"' : 'class="form-control"';
							$rows[$k]['form_html'] = format_attribute($rows[$k], $v['value'], $class);
						}
					}
				}
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $rows, 'db_query');
		}

		return empty($rows) ? FALSE : sc($rows);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_option_value($id = '')
	{
		list($a, $b) = explode('-', $id);

		if (!empty((int)$a) && !empty((int)$b))
		{
			//get the option ID and update an image on the product page with a new one
			$this->db->join(TBL_PRODUCTS_ATTRIBUTES_OPTIONS,
				$this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_OPTIONS) . '.option_id = ' .
				$this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES) . '.option_id', 'left');

			$this->db->where('prod_att_value_id', $b);
			$this->db->where('prod_att_id', $a);

			if (!$q = $this->db->get(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param bool $public
	 * @return bool
	 */
	public function get_product_attribute_values($id = '', $lang_id = '1', $public = FALSE)
	{
		//get associated attribute values
		$this->db->select('*,' .
			$this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES) . '.status
                AS option_status');
		$this->db->join(TBL_PRODUCTS_ATTRIBUTES_OPTIONS,
			$this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES) . '.option_id = ' .
			$this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_OPTIONS) . '.option_id', 'left');
		$this->db->join(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME,
			$this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES) . '.option_id = ' .
			$this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME) . '.option_id', 'left');
		$this->db->join(TBL_LANGUAGES,
			$this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME) . '.language_id = ' .
			$this->db->dbprefix(TBL_LANGUAGES) . '.language_id', 'left');
		$this->db->where($this->db->dbprefix(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME) . '.language_id', $lang_id);

		if ($public == TRUE)
		{
			$this->db->where($this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES) . '.status', '1');
		}

		if (!$q = $this->db->where($this->db->dbprefix(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES) . '.prod_att_id', $id)->get(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $attribute_id
	 * @return bool
	 */
	public function get_product_attribute_options($attribute_id = '')
	{
		if (!$q = $this->db->where('attribute_id', $attribute_id)
			->get(TBL_PRODUCTS_ATTRIBUTES_OPTIONS)
		)
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
	 * @param string $id
	 * @param array $data
	 * @return false|string
	 */
	public function insert_attribute_option($id = '', $data = array())
	{
		$vars = $this->dbv->clean($data, TBL_PRODUCTS_ATTRIBUTES_OPTIONS);

		$vars['attribute_id'] = $id;

		if (!$this->db->insert(TBL_PRODUCTS_ATTRIBUTES_OPTIONS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$option_id = $this->db->insert_id();

		if (!empty($data['lang']))
		{
			//now update the names table
			foreach ($data['lang'] as $k => $v)
			{
				$vars = $this->dbv->clean($v, TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME);

				$vars['option_id'] = $option_id;

				if (!$this->db->insert(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		//update the products that use this attribute with the new option
		$this->update_products_to_attributes($id, $option_id);

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'data'     => $vars,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $attribute_id
	 * @param string $product_id
	 */
	public function insert_product_attribute($attribute_id = '', $product_id = '')
	{
		//check if there's an attribute in the table already
		$this->db->where('product_id', $product_id);
		$this->db->where('attribute_id', $attribute_id);

		if (!$q = $this->db->get(TBL_PRODUCTS_TO_ATTRIBUTES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//if not, let's add it...
		if ($q->num_rows() < 1)
		{
			$vars = array('product_id'   => $product_id,
			              'attribute_id' => $attribute_id,
			);

			if (!$this->db->insert(TBL_PRODUCTS_TO_ATTRIBUTES, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$prod_att_id = $this->db->insert_id();

			//add options to products attributes values table if any
			$a = $this->get_product_attribute_options($attribute_id);

			if (!empty($a))
			{
				foreach ($a as $b)
				{
					$options = array(
						'status'      => '1',
						'prod_att_id' => $prod_att_id,
						'option_id'   => $b['option_id'],
						'option_sku'  => strtoupper(random_string('alnum', DEFAULT_SKU_LENGTH)),
					);

					if (!$this->db->insert(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES, $options))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param int $lang_id
	 * @return false|string
	 */
	public function mass_update($data = array(), $lang_id = 1)
	{
		foreach ($data['att'] as $k => $v)
		{
			$v = $this->dbv->clean($v);
			$vars = array('sort_order' => (int)($v['sort_order']));

			if (!$this->db->where($this->id, $k)->update(TBL_PRODUCTS_ATTRIBUTES, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$vars = array('attribute_name' => valid_id($v['attribute_name'], TRUE));

			$this->db->where('language_id', $lang_id);
			if (!$this->db->where($this->id, $k)->update(TBL_PRODUCTS_ATTRIBUTES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$this->dbv->db_sort_order(TBL_PRODUCTS_ATTRIBUTES, 'attribute_id', 'sort_order');

		return sc(array('success'  => TRUE,
		                'data'     => $data['att'],
		                'msg_text' => lang('mass_update_successful'))
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $qty
	 * @return bool
	 */
	public function update_attribute_inventory($id = '', $qty = '')
	{
		$vars = array('inventory' => $qty);
		if (!$this->db->where('prod_att_value_id', $id)->update(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$id = valid_id($data['attribute_id']);

		$vars = $this->dbv->clean($data, TBL_PRODUCTS_ATTRIBUTES);

		if (!$q = $this->db->where($this->id, $id)->update(TBL_PRODUCTS_ATTRIBUTES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//now update the names table
		foreach ($data['lang'] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_PRODUCTS_ATTRIBUTES_NAME);

			$this->db->where($this->id, $id);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_PRODUCTS_ATTRIBUTES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$select = array('select', 'radio', 'image');

		if (in_array($data['attribute_type'], $select))
		{
			//now update the options table if needed
			if (!empty($data['option_values']))
			{
				//get the options values first
				$options = $this->get_attribute_options($id);

				$update = array();
				foreach ($options as $c)
				{
					foreach ($data['option_values'] as $v)
					{
						if ($c['option_id'] == $v['option_id'])
						{
							$this->update_attribute_option($v);

							array_push($update, $v['option_id']);
						}
					}
				}

				//now delete any unused options
				foreach ($options as $c)
				{
					if (!in_array($c['option_id'], $update))
					{
						$this->dbv->delete(TBL_PRODUCTS_ATTRIBUTES_OPTIONS, 'option_id', $c['option_id']);
					}
				}
			}
			else
			{
				//delete all options
				$this->dbv->delete(TBL_PRODUCTS_ATTRIBUTES_OPTIONS, 'attribute_id', $id);
			}

			//add new options if any
			if (!empty($data['new_option']))
			{
				foreach ($data['new_option'] as $v)
				{
					$this->insert_attribute_option($id, $v);
				}
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
	 * @param string $option_id
	 */
	public function update_products_to_attributes($id = '', $option_id = '')
	{
		//first get the products that have the same attribute ID
		if (!$q = $this->db->where($this->id, $id)->get(TBL_PRODUCTS_TO_ATTRIBUTES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//loop through each
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $v)
			{
				$this->db->where('option_id', $option_id);

				if (!$s = $this->db->where('prod_att_id', $v['prod_att_id'])->get(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($s->num_rows() < 1)
				{
					//insert the new option
					$options = array(
						'status'      => '0',
						'prod_att_id' => $v['prod_att_id'],
						'option_id'   => $option_id,
						'option_sku'  => strtoupper(random_string('alnum', DEFAULT_SKU_LENGTH)),
					);

					if (!$this->db->insert(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES, $options))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool
	 */
	public function update_attribute_option($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_PRODUCTS_ATTRIBUTES_OPTIONS);

		if (!$this->db->where('option_id', $data['option_id'])->update(TBL_PRODUCTS_ATTRIBUTES_OPTIONS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if (!empty($data['lang']))
		{
			//now update the names table
			foreach ($data['lang'] as $k => $v)
			{
				$vars = $this->dbv->clean($v, TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME);

				$this->db->where('att_option_name_id', $v['att_option_name_id']);

				if (!$this->db->update(TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME, $vars))
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
	 * @return array
	 */
	public function update_product_attributes($id = '', $data = array())
	{
		$a = empty($data['product_attributes']) ? array() : $data['product_attributes']; //new attributes
		$b = $this->get_product_attributes($id, FALSE); //attributes in the db

		$c = array();
		if (!empty($b))
		{
			foreach ($b as $v) //let's delete all the attributes not in the current one
			{
				if (!in_array((int)$v['attribute_id'], $a))
				{
					//delete the attribute from db
					$this->delete_product_attributes((int)$v['attribute_id'], (int)$v['prod_att_id']);
				}
				else
				{
					array_push($c, (int)$v['attribute_id']);
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
					//insert the attribute into db
					$this->insert_product_attribute((int)$v, (int)$id);
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
	 * @param string $func
	 * @param array $data
	 * @return array
	 */
	public function validate($func = 'create', $data = array())
	{
		//init the error list....
		$error = '';

		if ($func == 'create')
		{
			$this->form_validation->set_data($data);

			$this->form_validation->set_rules('attribute_type', 'lang:attribute_type', 'trim|required|in_list[' . implode(',', config_option('attribute_types')) . ']');

			if (!$this->form_validation->run())
			{
				$error = validation_errors();
			}
		}
		else
		{
			//let's validate the attribute names first..
			foreach ($data['lang'] as $k => $v)
			{
				$this->form_validation->reset_validation();
				$this->form_validation->set_data($v);

				//validate the entries...
				$this->form_validation->set_rules('attribute_name', 'lang:attribute_name', 'trim|required|strip_tags|xss_clean',
					array('required' => $v['language'] . ' ' . lang('attribute_name_required')));

				$this->form_validation->set_rules('description', 'lang:description', 'trim|strip_tags');
				if (!$this->form_validation->run())
				{
					$error .= validation_errors();
				}
				else
				{
					$data['lang'][$k] = $this->dbv->validated($v);
				}
			}

			//now validate the rest...
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($data);

			$this->form_validation->set_rules('sort_order', 'lang:sort_order', 'trim|required|integer');
			$this->form_validation->set_rules('auto_update_image', 'lang:auto_update_image', 'trim|required|integer');
			$this->form_validation->set_rules('attribute_type', 'lang:attribute_type', 'trim|required|in_list[' . implode(',', config_option('attribute_types')) . ']');

			$this->form_validation->set_rules('attribute_sku', 'lang:attribute_sku', 'trim|required|strip_tags|xss_clean');

			if (!$this->form_validation->run())
			{
				$error .= validation_errors();
			}

			//now validate the option values
			$select = array('select', 'radio', 'image');

			if (in_array($data['attribute_type'], $select))
			{
				//validate options
				if (!empty($data['option_values']))
				{
					foreach ($data['option_values'] as $k => $v)
					{
						switch ($data['attribute_type'])
						{
							case 'image': //for image options

								$this->form_validation->reset_validation();
								$this->form_validation->set_data($v);

								$this->form_validation->set_rules('path', 'lang:image', 'trim|required|xss_clean');
								$this->form_validation->set_rules('option_id', 'lang:option_id', 'trim|required|integer');

								if (!$this->form_validation->run())
								{
									$error = validation_errors();
								}
								else
								{
									$data['option_values'][$k] = $this->dbv->validated($v);
								}

								$this->form_validation->reset_validation();

								break;

							default:

								//validate each of the entries for the options...
								foreach ($v['lang'] as $a => $b)
								{
									$this->form_validation->reset_validation();
									$this->form_validation->set_data($b);

									//validate the entries...
									$this->form_validation->set_rules('option_name', 'lang:option_name', 'trim|required|strip_tags|xss_clean',
										array('required' => $b['language'] . ' - ' . lang('option_name_required')));

									$this->form_validation->set_rules('option_description', 'lang:option_description', 'trim|strip_tags');
									$this->form_validation->set_rules('att_option_name_id', 'lang:att_option_name_id', 'trim|required|integer');
									$this->form_validation->set_rules('language_id', 'lang:language_id', 'trim|integer');

									if (!$this->form_validation->run())
									{
										$error .= validation_errors();
									}
									else
									{
										$data['option_values'][$k]['lang'][$a] = $this->dbv->validated($b);
									}

									$this->form_validation->reset_validation();
								}

								break;
						}
					}
				}

				//check if there are new values
				if (!empty($data['new_option']))
				{
					$new_error = '';

					foreach ($data['new_option'] as $k => $v)
					{
						switch ($data['attribute_type'])
						{
							case 'image': //for image options

								$this->form_validation->reset_validation();
								$this->form_validation->set_data($v);
								$this->form_validation->set_rules('path', 'lang:image', 'trim|required|xss_clean');

								if (!$this->form_validation->run())
								{
									$error .= validation_errors();
								}
								else
								{
									$data['new_option'][$k] = $this->dbv->validated($v);
								}

								break;

							default:

								//validate each of the entries for the options...
								foreach ($v['lang'] as $a => $b)
								{
									$this->form_validation->reset_validation();
									$this->form_validation->set_data($b);

									//validate the entries...
									$this->form_validation->set_rules('option_name', 'lang:option_name', 'trim|required|strip_tags|xss_clean',
										array('required' => lang('all_option_names_required')));

									$this->form_validation->set_rules('option_description', 'lang:option_description', 'trim|strip_tags');
									$this->form_validation->set_rules('language_id', 'lang:language_id', 'trim|integer');

									if (!$this->form_validation->run())
									{
										$new_error = validation_errors();
									}
									else
									{
										$data['new_option'][$k]['lang'][$a] = $this->dbv->validated($b);
									}
								}

								break;
						}
					}

					//reset it again....
					$this->form_validation->reset_validation();

					$error .= $new_error;
				}
			}

			if (!$this->form_validation->run())
			{
				$error .= validation_errors();
			}
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

/* End of file Product_attributes_model.php */
/* Location: ./application/models/Product_attributes_model.php */