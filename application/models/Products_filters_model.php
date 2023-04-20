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
class Products_filters_model extends CI_Model
{
	// ------------------------------------------------------------------------

	public function get_details($id = '')
	{
		if (!$q = $this->db->where('filter_id', $id)->get(TBL_PRODUCTS_FILTERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ( $q->num_rows() > 0)
		{
			$row = sc($q->row_array());

			//check for filter values
			$row['values'] =  $this->get_filter_values($id);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	public function check_enabled()
	{
		if (config_enabled('sts_products_filters_enable'))
		{
			return TRUE;
		}

		redirect();
	}

	// ------------------------------------------------------------------------

	public function get_filter_values($id = '')
	{
		if (!$q = $this->db->where('filter_id', $id)->get(TBL_PRODUCTS_FILTERS_VALUES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	public function get_rows($public = FALSE)
	{
		if ($public == TRUE)
		{
			$this->db->where('status', '1');
		}

		if (!$q = $this->db->order_by('sort_order', 'ASC')->get(TBL_PRODUCTS_FILTERS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ( $q->num_rows() > 0)
		{
			$row = $q->result_array();

			if ($public == TRUE)
			{
				foreach ($row as $k => $v)
				{
					switch ($v['filter_id'])
					{
						//price
						case '1':
							//check for filter values
							$row[$k]['values'] = $this->get_filter_values($v['filter_id']);

							break;

						//categories
						case '2':

							$row[$k]['values'] = $this->cat->sub_categories('0', sess('default_lang_id'), TRUE);

							break;

						//brands
						case '3':

							$opt = array('offset' => 0,
							             'session_per_page' => 999,
							             'limit' => '999',
							             'offset' => '0',
							);

							$row[$k]['values'] = $this->brands->load_brands($opt, sess('default_lang_id'), FALSE, TRUE);

							break;

						//ratings
						case '4':

							$a = array();
							for ($i = 5; $i>=1; $i--)
							{
								array_push($a, $i);
							}

							$row[$k]['values'] = $a;

							break;

						//tags
						case '5':

							$row[$k]['values'] = $this->tag->load_tags(array('column' => 'tag', 'order' => 'ASC'));

							break;
					}
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function load_filters($options = '', $lang_id = 1, $public = FALSE)
	{
		//set the default sort order
		$sort = $this->config->item(TBL_PRODUCTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['sort']['order']) ? $options['sort']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['sort']['column']) ? $options['sort']['column'] : $sort['column'];

		$select = 'SELECT *, ';

		if (sess('discount_group'))
		{
			$select .= ' f.priority AS disc_priority,
                      f.group_amount AS disc_group_amount,
                      f.quantity AS disc_quantity,
                      f.discount_type AS disc_type,';
		}

		$select .= ' p.product_id AS product_id,
                        (SELECT AVG(ratings)
                            FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' r
						    WHERE p.product_id' . ' = r.product_id
                            AND status = \'1\') AS avg_ratings';

		if (config_enabled('sts_tax_enable_tax_calculations'))
		{
			$select .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
		}

		$count = 'SELECT COUNT(p.product_id) AS total';


		$sql = ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                        ON p.product_id = c.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME) . ' s
                        ON p.product_id = s.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                        ON p.tax_class_id = t.tax_class_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_TAGS) . ' a
                        ON p.product_id = a.product_id    
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATE_RULES) . ' u
                        ON t.tax_class_id = u.tax_class_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATES) . ' w
                        ON w.tax_rate_id = u.tax_rate_id';

		if (sess('discount_group'))
		{
			$sql .= ' LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_DISC_GROUPS) . ' f
                            ON p.product_id = f.product_id
                            AND f.quantity = \'1\'
                            AND f.group_id = \'' . sess('discount_group') . '\'
                            AND f.start_date <= ' . local_time('sql') . '
                            AND f.end_date > ' . local_time('sql');
		}

		$sql .= ' LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_PRICING) . '  k
                        ON (p.product_id = k.product_id
                        AND k.enable = \'1\'
                        AND k.default_price = \'1\')     
                   LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_PHOTOS) . '  h
                        ON p.product_id = h.product_id
                        AND h.product_default = \'1\'';

		if ($public == TRUE)
		{
			$sql .= ' WHERE p.product_status = \'1\'
                        AND p.hidden_product = \'0\'
                        AND p.date_available <= ' . local_time('sql');
		}

		if (!empty($options['brands']))
		{
			foreach ($options['brands'] as $k => $v)
			{
				$sql .= $k == '0' ? ' AND (' : ' OR ';

				$sql .= ' p.brand_id = \'' . (int)$v . '\'';
			}

			$sql .= ' ) ';
		}

		if (!empty($options['price']))
		{
			$p = $this->get_filter_values('1'); //price

			foreach ($options['price'] as $k => $v)
			{
				foreach ($p as $a)
				{
					if ($a['id'] == $v)
					{
						$min = $a['initial_value'];
						$max = $a['secondary_value'];
					}
				}

				$sql .= $k == '0' ? ' AND ( ' : ' OR ';

				$sql .= ' p.product_price BETWEEN \'' .  $min . '\' AND  \'' . $max . '\'';
			}

			$sql .= ' ) ';
		}

		if (!empty($options['ratings']))
		{
			foreach ($options['ratings'] as $k => $v)
			{
				$sql .= $k == '0' ? ' AND ( ' : ' OR ';
				$r = $v + 1;
				$sql .= ' p.ratings BETWEEN \'' . (int)$v . '\' AND  \'' . (int)$r . '\'';
			}

			$sql .= ' ) ';
		}

		if (!empty($options['categories']))
		{
			foreach ($options['categories'] as $k => $v)
			{
				$sql .= $k == '0' ? ' AND ( ' : ' OR ';

				$sql .= ' c.category_id = \'' . (int)$v . '\'';
			}

			$sql .= ' ) ';
		}

		if (!empty($options['tags']))
		{
			foreach ($options['tags'] as $k => $v)
			{
				$sql .= $k == '0' ? ' AND ( ' : ' OR ';

				$sql .= ' a.tag_id = \'' . (int)$v . '\'';
			}

			$sql .= ' ) ';
		}

		$sql .= ' GROUP BY p.product_id
					ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'];
        $order = ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//check cache
		$cache = __METHOD__ . md5($select . $sql . $order);
		if ($row = $this->init->cache($cache, 'product_filters'))
		{
			return sc($row);
		}

		if (!$q = $this->db->query($select . $sql . $order))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'debug_db_query' => $this->db->last_query(),
				'total'          => $this->dbv->get_query_total($select . $sql, 'total', TRUE),
			);

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'product_filters');

		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	public function set_filters($data = array())
	{
		foreach ($data as $k => $v)
		{
			switch ($k)
			{
				case 'sort':

					$vars = explode('-', base64_decode($v));
					$data[$k] = array('column' => $vars[0], 'order' => $vars[1]);

					break;

				default:

					$data[$k] = explode('-', base64_decode($v));

					break;
			}

		}

		return $data;
	}

	// ------------------------------------------------------------------------

	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_PRODUCTS_FILTERS);

		if (!$q = $this->db->where('filter_id', valid_id($vars['filter_id']))->update(TBL_PRODUCTS_FILTERS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//update values
		$c = $this->db->where('filter_id', $data['filter_id'])->get(TBL_PRODUCTS_FILTERS_VALUES);

		$a = array();
		//check if the list is already in the table
		if (!empty($data['values']))
		{
			foreach ($data['values'] as $v)
			{
				$v['filter_id'] = $data['filter_id'];
				if (!empty($v['id']))
				{
					$row = $this->dbv->update(TBL_PRODUCTS_FILTERS_VALUES, 'id', $v);

					array_push($a, $v['id']);
				}
				else
				{
					$row = $this->dbv->create(TBL_PRODUCTS_FILTERS_VALUES, $v);
				}
			}
		}

		//let's delete all the regions not in the current one
		if (!empty($c))
		{
			foreach ($c->result_array() as $v)
			{
				if (!in_array($v['id'], $a))
				{
					$this->dbv->delete(TBL_PRODUCTS_FILTERS_VALUES, 'id', $v['id']);
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
}

/* End of file Products_filters_model.php */
/* Location: ./application/models/Products_filters_model.php */