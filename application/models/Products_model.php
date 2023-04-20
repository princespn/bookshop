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
class Products_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'product_id';

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $photo_id = 'photo_id';

	// ------------------------------------------------------------------------

	/**
	 * Products_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('products');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $field
	 * @param string $term
	 * @param int $limit
	 * @param int $lang_id
	 * @param bool $price
	 * @param bool $multiple
	 * @return mixed
	 */
	public function ajax_search($field = 'product_name', $term = '', $limit = TPL_AJAX_LIMIT, $lang_id = 1, $price = FALSE, $multiple = FALSE)
	{

		$d[] = $multiple == FALSE ? array() : array('product_id'   => '0',
		                                            'product_name' => 'none');

		$sql = 'SELECT
                  p.product_id,
                  d.product_name,
                  p.product_price,
                  p.product_sale_price
                FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                    ON p.product_id = d.product_id
                    AND d.language_id = \'' . $lang_id . '\'
                WHERE `' . $field . '`
                    LIKE \'%' . $term . '%\' ESCAPE \'!\'';


		if ($this->input->get('product_type'))
		{
			$sql .= ' AND product_type = \'' . url_title($this->input->get('product_type')) . '\'';
		}

		$sql .= ' ORDER BY d.product_name ASC    
                    LIMIT ' . $limit;

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $row)
			{
				$e = $q->result_array();

				foreach ($e as $k => $v)
				{
					//check for price
					$amount = '';
					if ($price == TRUE)
					{
						$amount = $v['product_sale_price'] > 0 ? $v['product_sale_price'] : $v['product_price'];

						$amount = ' - ' . format_amount($amount);
					}

					$e[$k] = array(
						'product_id'   => $v['product_id'],
						'product_name' => $v['product_name'] . $amount,
					);

				}

				//$rows = array_merge($d, $e);
				$rows = $e;
			}
		}
		else
		{
			$rows = $d;
		}

		return $rows;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $product_id
	 * @return bool|false|string
	 */
	public function check_product_cross_sell($id = '', $product_id = '')
	{
		$this->db->where('product_cross_sell_id', $id);

		if (!$q = $this->db->where($this->id, $product_id)->get(TBL_PRODUCTS_CROSS_SELLS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function clone_product($data = array())
	{
		$insert = $this->dbv->clean($data, TBL_PRODUCTS, FALSE, $this->id);

		$insert['product_sku'] = strtoupper(random_string('alnum', DEFAULT_SKU_LENGTH));
		$insert['date_added'] = get_time(now() - 2592000, TRUE);
		$insert['ratings'] = '0';

		if (!$this->db->insert(TBL_PRODUCTS, $insert))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//set the product ID
		$id = $this->db->insert_id();

		$this->dbv->db_sort_order(TBL_PRODUCTS, 'product_id', 'sort_order');

		//now duplicate the other data

		$tables[TBL_PRODUCTS_CROSS_SELLS] = 'id';
		$tables[TBL_PRODUCTS_NAME] = 'product_name_id';
		$tables[TBL_PRODUCTS_PHOTOS] = 'photo_id';
		$tables[TBL_PRODUCTS_TO_AFF_GROUPS] = 'id';
		$tables[TBL_PRODUCTS_TO_ATTRIBUTES] = 'prod_att_id'; //TBL_PRODUCTS_TO_ATTRIBUTES_VALUES
		$tables[TBL_PRODUCTS_TO_CATEGORIES] = 'prod_cat_id';
		$tables[TBL_PRODUCTS_TO_DISC_GROUPS] = 'id';
		$tables[TBL_PRODUCTS_TO_DOWNLOADS] = 'prod_dw_id';
		$tables[TBL_PRODUCTS_TO_PRICING] = 'prod_price_id';
		$tables[TBL_PRODUCTS_TO_SPECIFICATIONS_NAME] = 'prod_spec_id';
		$tables[TBL_PRODUCTS_TO_TAGS] = 'prod_tag_id';
		$tables[TBL_PRODUCTS_TO_VIDEOS] = 'prod_vid_id';


		foreach ($tables as $t => $i)
		{
			if ($q = $this->db->where($this->id, $data['product_id'])->get($t))
			{
				if ($q->num_rows() > 0)
				{
					foreach ($q->result_array() as $row)
					{
						if ($t == TBL_PRODUCTS_TO_ATTRIBUTES)
						{
							$srow = $row;
						}

						$row['product_id'] = $id;
						unset($row[$i]);
						if (!$this->db->insert($t, $row))
						{
							get_error(__FILE__, __METHOD__, __LINE__);
						}

						$new_id = $this->db->insert_id();

						if ($t == TBL_PRODUCTS_TO_ATTRIBUTES)
						{
							$b = $this->db->where('prod_att_id', $srow['prod_att_id'])->get(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES);

							if ($b->num_rows() > 0)
							{
								foreach ($b->result_array() as $c)
								{
									$c['prod_att_id'] = $new_id;
									unset($c['prod_att_value_id']);

									$c['option_sku'] = strtoupper(random_string('alnum', DEFAULT_SKU_LENGTH));

									if (!$this->db->insert(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES, $c))
									{
										get_error(__FILE__, __METHOD__, __LINE__);
									}
								}
							}
						}
					}
				}
			}
		}

		$row = array(
			'id'       => $id,
			'msg_text' => 'record_created_successfully',
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}
	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function create($data = array())
	{
		//check default values and clean the input
		$fields = $this->db->field_data(TBL_PRODUCTS);

		foreach ($fields as $v)
		{
			if ($v->name != 'product_id')
			{
				switch ($v->name)
				{
					case 'product_sku':

						$insert[$v->name] = $this->dbv->clean_field($data, $v, $v->name, strtoupper(random_string('alnum', DEFAULT_SKU_LENGTH)));
						break;

					case 'date_added':
					case 'modified':
					case 'date_available':

						$insert[$v->name] = $this->dbv->clean_field($data, $v, $v->name, get_time(now() - DEFAULT_PRODUCT_DATE_AVAILABILITY, TRUE));

						break;

					case 'date_expires':

						$insert[$v->name] = $this->dbv->clean_field($data, $v, $v->name, get_time(now() + DEFAULT_PRODUCT_DATE_EXPIRATION, TRUE));

						break;

					case 'recommended_product':

						$insert[$v->name] = '1';

						break;

					case 'inventory_amount':

						$insert[$v->name] = DEFAULT_PRODUCT_INVENTORY_AMOUNT;

						break;

					case 'sort_order':

						$insert[$v->name] = '0';

						break;

					case 'min_quantity_required':
					case 'max_quantity_allowed':

						if ($data['product_type'] == 'subscription')
						{
							$insert[$v->name] = '1';
						}

						break;

					case 'tax_class_id':

						$insert[$v->name] = $data['product_type'] == 'subscription' ? '0' : DEFAULT_PRODUCT_TAX_CLASS;

						break;

					case 'enable_inventory':

						$insert[$v->name] = config_enabled('sts_products_enable_inventory') ? '1' : '0';

						break;

					case 'charge_shipping':

						$insert[$v->name] = $data['product_type'] == 'subscription' ? '0' : config_item('sts_shipping_new_products_shipped');
						$data['charge_shipping'] = $insert[$v->name];

						break;

					case 'weight':

						$insert[$v->name] = '1.00000000';

						break;

					default:

						$insert[$v->name] = $this->dbv->clean_field($data, $v, $v->name);

						break;
				}
			}
		}

		if (!$this->db->insert(TBL_PRODUCTS, $insert))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//set the product ID
		$id = $this->db->insert_id();

		$this->dbv->db_sort_order(TBL_PRODUCTS, 'product_id', 'sort_order');

		//clean it then add to product names table
		$fields = $this->db->field_data(TBL_PRODUCTS_NAME);
		$insert = array('product_id' => $id);

		//get languages that are installed first
		$lang = get_languages(FALSE, FALSE);

		foreach ($lang as $a)
		{
			foreach ($fields as $v)
			{
				if ($v->name != 'product_id')
				{
					switch ($v->name)
					{
						case 'language_id':
							$insert[$v->name] = $this->dbv->clean_field($data, $v, $v->name, $a['language_id']);
							break;

						default:

							if ($data['product_type'] == 'subscription')
							{
								$n = 'new_subscription';
							}
							elseif ($data['product_type'] == 'certificate')
							{
								$n = 'new_certificate';
							}
							else
							{
								if ($data['charge_shipping'] == '1')
								{
									$n = 'new_shipped_product';
								}
								else
								{
									$n = 'new_product';
								}
							}

							$insert[$v->name] = $this->dbv->clean_field($data, $v, $v->name, lang($n));
							break;
					}
				}
			}

			if (!$this->db->insert(TBL_PRODUCTS_NAME, $insert))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}


		//add pricing
		$fields = $this->db->field_data(TBL_PRODUCTS_TO_PRICING);
		$insert = array('product_id' => $id);

		foreach ($fields as $v)
		{
			if ($v->name != 'product_id')
			{
				switch ($v->name)
				{
					case 'amount':
						$insert[$v->name] = '20.00';
						break;

					case 'interval_amount':
					case 'enable':
						$insert[$v->name] = '1';
						break;

					case 'name':
						$insert[$v->name] = 'per_month';
						break;

					case 'description':
						$insert[$v->name] = '';
						break;

					default:
						$insert[$v->name] = '0';
						break;
				}
			}
		}


		if (!$this->db->insert(TBL_PRODUCTS_TO_PRICING, $insert))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
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
	 * @param string $lang_id
	 * @return false|string
	 */
	public function delete($id = '', $lang_id = '1')
	{
		$this->dbv->delete(TBL_PRODUCTS, $this->id, $id);

		//delete from promo items
		$q = $this->db->where($this->id, $id)->get(TBL_PROMOTIONAL_ITEMS);

		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $v)
			{
				$this->dbv->delete(TBL_PROMOTIONAL_RULES, 'rule_id', $v['rule_id']);
			}
		}

		//delete cache
		$this->init->reset_cache(__CLASS__ . '::get_details' . $id . $lang_id);

		$row = array('success'  => TRUE,
		             'msg_text' => lang('record_deleted_successfully'));

		return sc($row);
	}
	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param bool $public
	 * @param bool $full
	 * @param bool $get_cache
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = '1', $public = FALSE, $full = TRUE, $get_cache = TRUE)
	{
		$sql = 'SELECT p.*, d.*, h.*,
                    b.brand_name,
                    v.supplier_name,
                    t.class_name,
                    a.aff_group_name,
                    e.video_name AS default_video_name,
                    e.video_code As default_video_code,
                    c.group_name as blog_group_name,
                    g.group_name as disc_group_name,';

		if (sess('discount_group'))
		{
			$sql .= ' f.priority AS disc_priority,
                      f.group_amount AS disc_group_amount,
                      f.quantity AS disc_quantity,
                      f.discount_type AS disc_type,
                      x.amount AS subscription_amount,';
		}

		$sql .= '  p.product_id AS product_id,
                    m.list_name AS add_list_name,
                    n.list_name AS remove_list_name,
                    DATE_FORMAT(date_available,\'' . $this->config->item('sql_date_format') . '\')
                        AS date_available_formatted,
                    DATE_FORMAT(date_added,\'' . $this->config->item('sql_date_format') . '\')
                        AS date_added_formatted,
                    DATE_FORMAT(date_expires,\'' . $this->config->item('sql_date_format') . '\')
                        AS date_expires_formatted';

		if (config_enabled('sts_tax_enable_tax_calculations') && config_enabled('sts_tax_product_display_price_with_tax'))
		{
			$sql .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
		}

		if ($public == TRUE)
		{
			$sql .= ', (SELECT AVG(ratings)
                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' r
						    WHERE r.' . $this->id . ' = \'' . (int)$id . '\'
						    AND r.status = \'1\') AS avg_ratings';
		}
		else
		{
			$sql .= ', (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)$id . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                            AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                            WHERE p.' . $this->id . ' > ' . (int)$id . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                            AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                            LEFT JOIN ' . $this->db->dbprefix(TBL_BRANDS_NAME) . ' b
                                ON p.brand_id = b.brand_id
                            LEFT JOIN ' . $this->db->dbprefix(TBL_SUPPLIERS) . ' v
                                ON p.supplier_id = v.supplier_id
                             LEFT JOIN ' . $this->db->dbprefix(TBL_VIDEOS) . ' e
                                ON p.video_as_default = e.video_id    
                            LEFT JOIN ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . ' a
                                ON p.affiliate_group = a.group_id
                            LEFT JOIN ' . $this->db->dbprefix(TBL_DISCOUNT_GROUPS) . ' g
                                ON p.discount_group = g.group_id
                            LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_GROUPS) . ' c
                                ON p.blog_group = c.group_id
                            LEFT JOIN ' . $this->db->dbprefix(TBL_EMAIL_MAILING_LISTS) . ' m
                                ON p.add_mailing_list = m.list_id
                            LEFT JOIN ' . $this->db->dbprefix(TBL_EMAIL_MAILING_LISTS) . ' n
                                ON p.remove_mailing_list = n.list_id
                            LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                                ON p.tax_class_id = t.tax_class_id
                            LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_PRICING) . ' x
                                ON p.product_id = x.product_id    
                            LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATE_RULES) . ' u
                                ON t.tax_class_id = u.tax_class_id
                            LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATES) . ' w
                                ON w.tax_rate_id = u.tax_rate_id ';

		if ($public == TRUE)
		{
			$sql .= ' LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_PRICING) . '  k
                            ON (p.product_id = k.product_id
                            AND k.enable = \'1\'
                            AND k.default_price = \'1\') ';
		}

		if (sess('discount_group'))
		{
			$sql .= ' LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_DISC_GROUPS) . ' f
                            ON p.product_id = f.product_id
                            AND f.quantity = \'1\'
                            AND f.group_id = \'' . sess('discount_group') . '\'
                            AND f.start_date <= ' . local_time('sql') . '
                            AND f.end_date > ' . local_time('sql');
		}

		$sql .= ' LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                                ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                            LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_PHOTOS) . '  h
                                ON p.' . $this->id . ' = h.' . $this->id . '
                                AND h.product_default = \'1\'
                        WHERE p.' . $this->id . '= \'' . valid_id($id) . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND p.product_status = \'1\'
                            AND p.date_expires >= ' . local_time('sql');
		}

		//check if we have a cache file and return that instead
		$cache = __METHOD__ . md5($sql);
		$cache_type = $public == TRUE ? 'public_db_query' : 'db_query';

		if ($row = $this->init->cache($cache, $cache_type))
		{
			if ($get_cache == TRUE)
			{
				return sc($row);
			}
		}

		//no cache, let's get it from the db
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$a = $q->row_array();

			if (empty($a['product_id']))
			{
				return FALSE;
			}

			//assign default values
			$empty = array('supplier_name', 'brand_name', 'class_name', 'aff_group_name',
			               'disc_group_name', 'add_list_name', 'remove_list_name', 'blog_group_name');

			foreach ($empty as $e)
			{
				if (empty($a[$e]))
				{
					$a[$e] = lang('none');
				}
			}

			//set the data array
			$row = $a;

			$row['discount_groups'] = $this->get_product_discount_groups($id, $public);

			if ($full == TRUE)
			{
				$row['tax_rates'] = $this->get_product_tax_rates($row['tax_class_id']);
				$row['name'] = $this->get_product_languages($id, $public);
				$row['photos'] = $this->get_product_photos($id);
				$row['videos'] = $this->get_product_videos($id);
				$row['categories'] = $this->cat->get_product_categories($id, $lang_id);
				$row['tags'] = $this->get_product_tags($id);
				$row['cross_sell'] = $this->get_product_cross_sell($id, $lang_id);
				$row['downloads'] = $this->get_product_downloads($id);
				$row['pricing_options'] = $this->get_product_pricing($id);
				$row['product_specs'] = $this->specs->get_product_spec_values($id, $public, $lang_id);
				$row['affiliate_groups'] = $this->aff_group->get_product_affiliate_groups($id);
				$row['attributes'] = $this->att->get_product_attributes($id, TRUE, $lang_id, FALSE, $public);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	public function get_product_cross_sell($id = '', $lang_id = '1')
	{
		$sql = 'SELECT p.*, n.product_name
                    FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CROSS_SELLS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' n
                        ON (p.product_cross_sell_id = n. ' . $this->id . '
                        AND n.language_id = \'' . $lang_id . '\')
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
	 * @return bool|false|string
	 */
	public function get_product_cross_sells($id = '', $lang_id = 1)
	{
		$sql = 'SELECT
                      p.*, d.*, c.*, h.*, k.*,';

		if (sess('discount_group'))
		{
			$sql .= ' f.priority AS disc_priority,
                      f.group_amount AS disc_group_amount,
                      f.quantity AS disc_quantity,
                      f.discount_type AS disc_type,';
		}

		$sql .= ' p.product_id AS product_id,
                        (SELECT AVG(ratings)
                            FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' r
						    WHERE s.product_cross_sell_id = r.' . $this->id . '
						    AND status = \'1\') AS avg_ratings';

		if (config_enabled('sts_tax_enable_tax_calculations') && config_enabled('sts_tax_product_display_price_with_tax'))
		{
			$sql .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CROSS_SELLS) . ' s
						LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                            ON s.product_cross_sell_id = p.product_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                            ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                            ON p.product_id = c.product_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                            ON p.tax_class_id = t.tax_class_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATE_RULES) . ' u
                            ON t.tax_class_id = u.tax_class_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATES) . ' w
                             ON w.tax_rate_id = u.tax_rate_id ';

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
                            AND h.product_default = \'1\'
                        WHERE p.product_status = \'1\'
                            AND p.product_featured = \'1\'
                            AND p.hidden_product = \'0\'
                            AND p.date_expires >= ' . local_time('sql') . ' 
                            AND s.product_id = \'' . (int)$id . '\'
                        GROUP BY s.product_cross_sell_id
                        ORDER BY RAND()
                        LIMIT 0,' . config_option('default_total_cross_sells');

		//set the unique cache file
		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($sql))
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

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $public
	 * @return array
	 */
	public function get_product_discount_groups($id = '', $public = FALSE)
	{
		$sql = 'SELECT *,
                    start_date AS sql_start,
                    end_date AS sql_end,
                    DATE_FORMAT(start_date, \'' . $this->config->item('sql_date_format') . '\')
                        AS start_date,
                    DATE_FORMAT(end_date, \'' . $this->config->item('sql_date_format') . '\')
                        AS end_date
                FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_DISC_GROUPS) . '
                    WHERE ' . $this->id . ' = \'' . $id . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND start_date < NOW() AND end_date > NOW()';
		}

		$sql .= '  ORDER BY quantity ASC';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return empty($row) ? array() : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_product_tax_rates($id = '')
	{
		$this->db->where('tax_class_id', $id);

		$this->db->join(TBL_TAX_RATES,
			$this->db->dbprefix(TBL_TAX_RATES) . '.tax_rate_id = ' .
			$this->db->dbprefix(TBL_TAX_RATE_RULES) . '.tax_rate_id', 'left');

		if (!$q = $this->db->get(TBL_TAX_RATE_RULES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $public
	 * @return bool
	 */
	public function get_product_languages($id = '', $public = FALSE)
	{
		$this->db->where($this->id, $id);

		$this->db->join(TBL_LANGUAGES,
			$this->db->dbprefix(TBL_LANGUAGES) . '.language_id = ' .
			$this->db->dbprefix(TBL_PRODUCTS_NAME) . '.language_id', 'left');

		if (!$q = $this->db->get(TBL_PRODUCTS_NAME))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $public == TRUE ? $q->row_array() : $q->result_array();
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return array
	 */
	public function get_product_photos($id = '')
	{
		$cache = __METHOD__ . $id;
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			//get product photos
			if (!$q = $this->db->where($this->id, $id)
				->order_by('product_default', 'DESC')
				->get(TBL_PRODUCTS_PHOTOS)
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

		return empty($row) ? array() : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_product_videos($id = '')
	{
		$cache = __METHOD__ . $id;
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			$this->db->join(TBL_VIDEOS,
				$this->db->dbprefix(TBL_PRODUCTS_TO_VIDEOS) . '.video_id = ' .
				$this->db->dbprefix(TBL_VIDEOS) . '.video_id', 'left');

			if (!$q = $this->db->where($this->id, $id)->get(TBL_PRODUCTS_TO_VIDEOS))
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
	 * @param string $id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_product_downloads($id = '', $lang_id = 1)
	{
		$sql = 'SELECT *
                    FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_DOWNLOADS) . ' p
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
	 * @return bool
	 */
	public function get_product_pricing($id = '')
	{
		if (!$q = $this->db->where($this->id, $id)
			->order_by('default_price', 'DESC')
			->get(TBL_PRODUCTS_TO_PRICING)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_product_tags($id = '')
	{
		$sql = 'SELECT *
                    FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_TAGS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TAGS) . ' n
                        ON p.tag_id = n.tag_id
                    WHERE p.' . $this->id . ' = \'' . $id . '\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_PRODUCTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$select = 'SELECT *, p.product_id AS product_id '; //for the page rows only

		$count = 'SELECT COUNT(*) AS total 
					 FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
				   LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                    ON p.product_id = c.product_id';//for pagination totals

		//sql query
		$sql = ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                        ON p.product_id = c.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_PHOTOS) . '  h
                        ON p.product_id = h.product_id
                        AND h.product_default = \'1\' ';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_PRODUCTS, TBL_PRODUCTS_TO_CATEGORIES), $options['query']);

			$sql .= $options['where_string'];
			$count .= $options['where_string'];
		}

		//set the order and limit clause
		$order = ' GROUP BY p.product_id
		            ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the cache file
		$cache = __METHOD__ . $options['md5'];
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($select . $sql . $order))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values'         => $q->result_array(),
					'debug_db_query' => $this->db->last_query(),
					'total'          => $this->dbv->get_query_total($count),
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
	 * @return bool
	 */
	public function get_video_details($id = '')
	{
		$this->db->where('video_id', $id);

		if (!$q = $this->db->get(TBL_VIDEOS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $id
	 * @return bool
	 */
	public function get_photo_details($id)
	{
		$this->db->where($this->photo_id, $id);
		if (!$q = $this->db->get(TBL_PRODUCTS_PHOTOS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $fields
	 * @return bool
	 */
	public function get_product_names($id = '', $fields = '')
	{
		if (!empty($fields))
		{
			$this->db->select($fields);
		}

		if (!$q = $this->db->where($this->id, $id)
			->get(TBL_PRODUCTS_NAME)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $options
	 * @param int $lang_id
	 * @param string $type
	 * @return bool|false|string
	 */
	public function load_home_products($options, $lang_id = 1, $type = 'latest_products')
	{
		//set the default sort order
		$sort = $this->config->item($type, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$select = 'SELECT
                      p.*, d.*, c.*, h.*, k.*,';

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
						    WHERE p.' . $this->id . ' = r.' . $this->id . '
						    AND status = \'1\') AS avg_ratings';

		if (config_enabled('sts_tax_enable_tax_calculations') && config_enabled('sts_tax_product_display_price_with_tax'))
		{
			$select .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
		}

		$count = 'SELECT COUNT(p.product_id) AS total 
					FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
					 WHERE p.product_status = \'1\'
                            AND p.hidden_product = \'0\'
                            AND p.date_expires >= ' . local_time('sql');  //for pagination totals

		if ($type == 'featured_products')
		{
			$count .= '  AND p.product_featured = \'1\'';
		}

		$sql = ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                            ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                        LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                            ON p.product_id = c.product_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                            ON p.tax_class_id = t.tax_class_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATE_RULES) . ' u
                            ON t.tax_class_id = u.tax_class_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATES) . ' w
                             ON w.tax_rate_id = u.tax_rate_id ';

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
                            AND h.product_default = \'1\'
                        WHERE p.product_status = \'1\'
                            AND p.hidden_product = \'0\'
                            AND p.date_expires >= ' . local_time('sql');

		if ($type == 'featured_products')
		{
			$sql .= '  AND p.product_featured = \'1\'';
		}

		$order = 'GROUP BY p.product_id
                        ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the unique cache file
		$cache = __METHOD__ . md5($select . $sql . $order);
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($select . $sql . $order))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values' => $q->result_array(),
					'total'  => $this->dbv->get_query_total($count),
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function load_store($lang_id = 1)
	{
		$opt = array('offset'           => '0',
		             'session_per_page' => config_option('layout_design_products_per_home_page'),
		);

		if (config_enabled('layout_design_home_page_show_featured_products'))
		{
			$a = $this->load_home_products(query_options($opt), $lang_id, 'featured_products');
		}

		if (config_enabled('layout_design_home_page_show_latest_products'))
		{
			$b = $this->load_home_products(query_options($opt), $lang_id, 'latest_products');
		}

		$rows = array('featured_products' => !empty($a['values']) ? $a['values'] : '',
		              'latest_products'   => !empty($b['values']) ? $b['values'] : '',
		);

		return !empty($rows) ? sc($rows) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $id
	 * @param int $lang_id
	 * @param string $featured
	 * @return bool|false|string
	 */
	public function load_brand_products($options = '', $id = '', $lang_id = 1, $featured = '1')
	{
		//set the default sort order
		$sort = $this->config->item(TBL_PRODUCTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

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
						    WHERE p.' . $this->id . ' = r.' . $this->id . '
                            AND status = \'1\') AS avg_ratings';

		if (config_enabled('sts_tax_enable_tax_calculations'))
		{
			$select .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
		}

		$count = 'SELECT COUNT(p.product_id) AS total 
					 FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
					  WHERE p.product_status = \'1\'
                        AND p.hidden_product = \'0\'
                        AND p.brand_id = \'' . (int)$id . '\'
                        AND p.date_expires >= ' . local_time('sql'); //for pagination totals

		if ($featured == TRUE)
		{
			$count .= '  AND p.product_featured = \'1\'';
		}

		$sql = '  FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                        ON p.product_id = c.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                        ON p.tax_class_id = t.tax_class_id
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
                        AND h.product_default = \'1\'
                    WHERE p.product_status = \'1\'
                        AND p.hidden_product = \'0\'
                        AND p.brand_id = \'' . (int)$id . '\'
                        AND p.date_expires >= ' . local_time('sql');

		if ($featured == TRUE)
		{
			$sql .= '  AND p.product_featured = \'1\'';
		}

		$order = ' GROUP BY p.product_id
                        ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the unique cache file
		$cache = __METHOD__ . md5($select . $sql . $order);

		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			if (!$q = $this->db->query($select . $sql . $order))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$options['id'] = $id;

				$row = array(
					'values'         => $q->result_array(),
					'debug_db_query' => $this->db->last_query(),
					'total'          => $this->dbv->get_query_total($count),
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function load_category_products($options = '', $id = '', $lang_id = 1)
	{
		//set the default sort order
		$sort = $this->config->item(TBL_PRODUCTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

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
						    WHERE p.' . $this->id . ' = r.' . $this->id . '
                            AND status = \'1\') AS avg_ratings';

		if (config_enabled('sts_tax_enable_tax_calculations'))
		{
			$select .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
		}

		$count = 'SELECT COUNT(p.product_id) AS total 
					FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                        ON p.product_id = c.product_id
					WHERE p.product_status = \'1\'
                        AND category_id = \'' . $id . '\'
                        AND p.hidden_product = \'0\'
                        AND p.date_expires >= ' . local_time('sql');  //for pagination totals

		$sql = ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                        ON p.product_id = c.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                        ON p.tax_class_id = t.tax_class_id
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

		$sql .= '  LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_PRICING) . '  k
                        ON (p.product_id = k.product_id
                        AND k.enable = \'1\'
                        AND k.default_price = \'1\')     
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_PHOTOS) . '  h
                        ON p.product_id = h.product_id
                        AND h.product_default = \'1\'
                    WHERE p.product_status = \'1\'
                        AND category_id = \'' . $id . '\'
                        AND p.hidden_product = \'0\'
                        AND p.date_expires >= ' . local_time('sql');

		$order = ' GROUP BY p.product_id
                       ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                       LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the cache file
		$cache = __METHOD__ . md5($select . $sql . $order);

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($select . $sql . $order))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				//set the category id
				$options['id'] = $id;

				$row = array(
					'values'         => $q->result_array(),
					'debug_db_query' => $this->db->last_query(),
					'total'          => $this->dbv->get_query_total($count),
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
	 * @param int $lang_id
	 * @param int $limit
	 * @return bool|false|string
	 */
	public function load_similar_products($id = '', $lang_id = 1, $limit = DEFAULT_TOTAL_SIMILAR_PRODUCTS)
	{
		$tags = $this->get_product_tags($id);

		if (!empty($tags))
		{
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
						    WHERE p.' . $this->id . ' = r.' . $this->id . '
                            AND status = \'1\') AS avg_ratings';

			if (config_enabled('sts_tax_enable_tax_calculations'))
			{
				$select .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
			}

			$sql = '  FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_TAGS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . ' b
                        ON p.product_id = b.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                        ON p.product_id = c.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                        ON b.tax_class_id = t.tax_class_id
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

			$i = 1;
			foreach ($tags as $t)
			{
				$sql .= $i == 1 ? ' WHERE (' : ' OR ';
				$sql .= 'tag_id = ' . (int)$t['tag_id'];

				$i++;
			}

			$sql .= ' ) AND b.product_status = \'1\'
                        AND b.product_featured = \'1\'
                        AND b.hidden_product = \'0\'
                        AND b.date_available <= ' . local_time('sql') . '
                         AND p.product_id != \'' . (int)$id . '\'';

			$order = ' GROUP BY p.product_id
                        ORDER BY RAND() LIMIT ' . $limit;

			//set the unique cache file
			$cache = __METHOD__ . md5($select . $sql . $order);

			if (!$row = $this->init->cache($cache, 'public_db_query'))
			{
				if (!$q = $this->db->query($select . $sql . $order))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = array(
						'values'  => $q->result_array(),
						'success' => TRUE,
					);

					// Save into the cache
					$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function load_tag_products($options = '', $id = '', $lang_id = 1)
	{
		//set the default sort order
		$sort = $this->config->item(TBL_PRODUCTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

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
						    WHERE p.' . $this->id . ' = r.' . $this->id . '
                            AND status = \'1\') AS avg_ratings';

		if (config_enabled('sts_tax_enable_tax_calculations'))
		{
			$select .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
		}

		$count = 'SELECT COUNT(p.product_id) AS total 
					FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_TAGS) . ' c
                        ON p.product_id = c.product_id
					WHERE p.product_status = \'1\'
                        AND tag_id = \'' . $id . '\'
                        AND p.hidden_product = \'0\'
                        AND p.date_expires >= ' . local_time('sql');  //for pagination totals

		$sql = ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_TAGS) . ' c
                        ON p.product_id = c.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                        ON p.tax_class_id = t.tax_class_id
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

		$sql .= '  LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_PRICING) . '  k
                        ON (p.product_id = k.product_id
                        AND k.enable = \'1\'
                        AND k.default_price = \'1\')     
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_PHOTOS) . '  h
                        ON p.product_id = h.product_id
                        AND h.product_default = \'1\'
                    WHERE p.product_status = \'1\'
                        AND tag_id = \'' . $id . '\'
                        AND p.hidden_product = \'0\'
                        AND p.date_expires >= ' . local_time('sql');

		$order = ' GROUP BY p.product_id
                       ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                       LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the cache file
		$cache = __METHOD__ . md5($select . $sql . $order);

		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($select . $sql . $order))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				//set the category id
				$options['id'] = $id;

				$row = array(
					'values'         => $q->result_array(),
					'debug_db_query' => $this->db->last_query(),
					'total'          => $this->dbv->get_query_total($count),
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
			}
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
		if (!empty($data['products']))
		{
			foreach ($data['products'] as $k => $v)
			{
				$v[$this->id] = $k;

				$vars = $this->dbv->clean($v, TBL_PRODUCTS);

				$this->dbv->update(TBL_PRODUCTS, $this->id, $vars);

				//update name
				$vars = $this->dbv->clean($v, TBL_PRODUCTS_NAME);
				$this->db->where('language_id', sess('default_lang_id'));

				if (!$this->db->where($this->id, $v[$this->id])->update(TBL_PRODUCTS_NAME, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			foreach ($data['products'] as $k => $v)
			{
				if (!empty($v['update']))
				{
					switch ($data['change-status'])
					{
						case 'active':
						case 'inactive':

							$status = $data['change-status'] == 'active' ? '1' : '0';

							$this->update_status($k, $status);

							break;

						case 'delete':

							$this->delete($v['update']);

							break;

						case 'add_featured':
						case 'remove_featured':

							$status = $data['change-status'] == 'add_featured' ? '1' : '0';

							$this->update_status($k, $status, 'product_featured');

							break;

						case 'add_brand':
						case 'remove_brand':

							$id =  $data['change-status'] == 'add_brand' ? $data['brand_id'] : '0';

							$this->db->where('product_id', $k);
							if (!$this->db->update(TBL_PRODUCTS, array('brand_id' => $id)))
							{
								get_error(__FILE__, __METHOD__, __LINE__);
							}

							break;

						case 'add_tag':
						case 'remove_tag':

							if (!empty($data['tag_id']))
							{
								//add the product to the category
								if ($data['change-status'] == 'add_tag')
								{
									if (!$this->prod->get_product_tag($data['tag_id'], $k))
									{
										$vars = array('product_id' => $k,
										              'tag_id'     => $data['tag_id']);

										$this->dbv->create(TBL_PRODUCTS_TO_TAGS, $vars);
									}
								}
								else
								{
									$this->db->where('product_id', $k);
									$this->db->where('tag_id', $data['tag_id']);

									if (!$this->db->delete(TBL_PRODUCTS_TO_TAGS))
									{
										get_error(__FILE__, __METHOD__, __LINE__);
									}
								}
							}

							break;

						case 'add_category':
						case 'remove_category':

							if (!empty($data['category_id']))
							{
								//add the product to the category
								if ($data['change-status'] == 'add_category')
								{
									if (!$this->cat->get_product_category($data['category_id'], $k, sess('default_lang_id')))
									{
										$vars = array('product_id'  => $k,
										              'category_id' => $data['category_id']);

										$this->dbv->create(TBL_PRODUCTS_TO_CATEGORIES, $vars);
									}
								}
								else
								{
									$this->db->where('product_id', $k);
									$this->db->where('category_id', $data['category_id']);

									if (!$this->db->delete(TBL_PRODUCTS_TO_CATEGORIES))
									{
										get_error(__FILE__, __METHOD__, __LINE__);
									}
								}
							}

							break;
					}
				}
			}
		}

		$row = array(
			'data'     => $data,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $id
	 * @return array
	 */
	public function set_default_photo($id)
	{
		$data = $this->get_photo_details($id);

		$this->db->where('product_id', $data['product_id']);
		if (!$this->db->update(TBL_PRODUCTS_PHOTOS, array('product_default' => '0')))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$this->db->where('photo_id', $id);
		if (!$this->db->update(TBL_PRODUCTS_PHOTOS, array('product_default' => '1')))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array('msg_text' => lang('system_updated_successfully'),
		             'row'      => $data);

		return $row;
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
		//set the default sort order
		$sort = $this->config->item(TBL_PRODUCTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

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
						    WHERE p.' . $this->id . ' = r.' . $this->id . '
                            AND status = \'1\') AS avg_ratings';

		if (config_enabled('sts_tax_enable_tax_calculations'))
		{
			$select .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
		}

		$count = 'SELECT COUNT(p.product_id) AS total 
					FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'';

		if ($public == TRUE)
		{
			$count .= ' WHERE p.product_status = \'1\'
                        AND p.product_featured = \'1\'
                        AND p.hidden_product = \'0\'
                        AND p.date_expires >= ' . local_time('sql');
		}


		$sql = ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                        ON p.product_id = c.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_BRANDS_NAME) . ' r
                        ON p.brand_id = r.brand_id AND d.language_id = \'' . $lang_id . '\'    
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_TAGS) . ' a
                        ON p.product_id = a.product_id    
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                        ON p.tax_class_id = t.tax_class_id
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
                        AND p.product_featured = \'1\'
                        AND p.hidden_product = \'0\'
                        AND p.date_available <= ' . local_time('sql');
		}

		if (!empty($options['query']))
		{
			foreach ($options['query'] as $k => $v)
			{
				//remove any fields not needed in the query
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				$columns = $this->db->list_fields(TBL_PRODUCTS);

				$i = 1;
				foreach ($columns as $f)
				{
					$v = strip_tags($v);

					if ($i == 1)
					{
						if ($public == TRUE)
						{
							$sql .= ' AND ( p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
							$count .= ' AND ( p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						}
						else
						{
							$sql .= ' WHERE ( p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
							$count .= ' WHERE ( p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						}

					}
					else
					{
						$sql .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						$count .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}

					$i++;
				}

				$columns = $this->db->list_fields(TBL_PRODUCTS_NAME);

				foreach ($columns as $f)
				{
					$sql .= ' OR d.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					$count .= ' OR d.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
				}

				$sql .= ') ';
				$count .= ') ';
			}
		}

		$order = ' GROUP BY p.product_id
                       ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                       LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$cache = __METHOD__ . md5($select . $sql . $order);

		if ($public == TRUE)
		{
			$row = $this->init->cache($cache, 'public_db_query');
		}

		if (empty($row))
		{
			if (!$q = $this->db->query($select . $sql . $order))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values'         => $q->result_array(),
					'debug_db_query' => $this->db->last_query(),
					'total'          => $this->dbv->get_query_total($count),
				);

			}

			if ($public == TRUE)
			{
				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $status
	 * @param string $column
	 * @return bool
	 */
	public function update_status($id = '', $status = '0', $column = 'product_status')
	{
		if (!$this->db->where($this->id, $id)->update(TBL_PRODUCTS, array($column => $status)))
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
		$vars = $this->dbv->clean($data, TBL_PRODUCTS);

		//update products
		$this->dbv->update(TBL_PRODUCTS, 'product_id', $vars);

		//update names
		if (!empty($data['product_name']))
		{
			foreach ($data['product_name'] as $k => $v)
			{
				$v = $this->dbv->clean($v, TBL_PRODUCTS_NAME);

				$this->db->where('language_id', $k);
				$this->db->where('product_id', $vars['product_id']);

				if (!$this->db->update(TBL_PRODUCTS_NAME, $v))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		//update product_tags
		$this->update_product_tags($data);

		//update specs
		if (!empty($data['product_specs']))
		{
			foreach ($data['product_specs'] as $k => $v)
			{
				$this->dbv->update(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME, 'prod_spec_id', $v);
			}
		}

		//updated images
		$this->update_product_photos($data);

		//update affiliate groups
		if (!empty($data['affiliate_groups']))
		{
			foreach ($data['affiliate_groups'] as $k => $v)
			{
				$this->dbv->update(TBL_PRODUCTS_TO_AFF_GROUPS, 'id', $v);
			}
		}

		//update discount groups
		$this->update_product_sub_data($data, TBL_PRODUCTS_TO_DISC_GROUPS, 'id', 'discount_groups');

		//update pricing options
		$this->update_product_sub_data($data, TBL_PRODUCTS_TO_PRICING, 'prod_price_id', 'pricing_options');

		//update videos
		$this->update_product_sub_data($data, TBL_PRODUCTS_TO_VIDEOS, 'prod_vid_id', 'videos');

		//update downloads
		$this->update_product_sub_data($data, TBL_PRODUCTS_TO_DOWNLOADS, 'prod_dw_id', 'product_downloads');

		//update product_categories
		$this->update_product_categories($data);

		//update product_cross_sells
		$this->update_product_cross_sells($data);

		//update attributes
		$this->update_product_attributes($data);

		return array('success'  => TRUE,
		             'data'     => $data,
		             'msg_text' => lang('system_updated_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function update_product_categories($data = array())
	{
		if (!empty($data['product_categories']))
		{
			foreach ($data['product_categories'] as $v)
			{
				if (!$this->cat->get_product_category($v, $data['product_id']))
				{
					$vars = array('product_id'  => $data['product_id'],
					              'category_id' => $v);

					$this->dbv->create(TBL_PRODUCTS_TO_CATEGORIES, $vars);
				}
			}

			//delete the rest
			$a = $this->cat->get_product_categories($data['product_id']);

			if (!empty($a))
			{
				foreach ($a as $v)
				{
					if (!in_array($v['category_id'], $data['product_categories']))
					{
						$this->dbv->delete(TBL_PRODUCTS_TO_CATEGORIES, 'prod_cat_id', $v['prod_cat_id']);
					}
				}
			}
		}
		else
		{
			$this->dbv->delete(TBL_PRODUCTS_TO_CATEGORIES, 'product_id', $data['product_id']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $admins
	 * @return bool|mixed
	 */
	public function update_product_inventories($data = array(), $admins = array())
	{
		//check if there are attributes first
		if (!empty($data['attribute_data']))
		{
			$att = unserialize($data['attribute_data']);

			if (!empty($att))
			{
				foreach ($att as $v)
				{
					if (!empty($v['enable_inventory']) && !empty($v['inventory']))
					{
						$qty = $v['inventory'] - $data['quantity'];

						$this->att->update_attribute_inventory($v['prod_att_value_id'], $qty);

						//check if we are sending out alerts on inventory
						if (config_enabled('sts_products_alert_inventory') && ($qty < config_option('sts_products_alert_inventory_level')))
						{
							if (!empty($admins)) //get active admins to send alerts to.
							{
								foreach ($admins as $a)
								{
									$this->mail->send_template(EMAIL_ADMIN_PRODUCT_ATTRIBUTE_INVENTORY_ALERT, format_product_inventory_alert_email($qty, $data, $v), TRUE, sess('default_lang_id'), $a['primary_email']);
								}
							}
						}
					}
				}
			}
		}

		//now update regular inventories for the product
		if (!empty($data['enable_inventory']))
		{
			$qty = $data['inventory_amount'] - $data['quantity'];

			$this->update_product_inventory($data['product_id'], $qty);

			//check if we are sending out alerts on inventory
			if (config_enabled('sts_products_alert_inventory') && ($qty < config_option('sts_products_alert_inventory_level')))
			{
				if (!empty($admins)) //get active admins to send alerts to.
				{
					foreach ($admins as $a)
					{
						$this->mail->send_template(EMAIL_ADMIN_PRODUCT_INVENTORY_ALERT, format_product_inventory_alert_email($qty, $data), TRUE, sess('default_lang_id'), $a['primary_email']);
					}
				}
			}
		}

		return !empty($qty) ? $qty : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $qty
	 * @return bool
	 */
	public function update_product_inventory($id = '', $qty = '')
	{
		$vars = array('inventory_amount' => $qty);

		if (!$this->db->where($this->id, $id)->update(TBL_PRODUCTS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $func
	 * @param array $data
	 * @return bool|false|string
	 */
	public function validate($func = 'create', $data = array())
	{
		$row = array('msg_text' => '',
		             'data'     => $data,
		);

		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('products_' . $func, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_PRODUCTS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean';

			switch ($f->name)
			{
				case 'product_type':

					$rule .= '|in_list[' . implode(',', config_option('product_types')) . ']';

					break;

				case 'date_expires':

					$rule .= '|end_date_to_sql';

					break;

				default:

					//if this field is a required field, let's set that
					if (in_array($f->name, $required))
					{
						$rule .= '|required';
					}

					//go through each field type first and validate based on it....
					$rule .= generate_db_rule($f->type, $f->max_length);

					break;
			}

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if ($this->form_validation->run())
		{
			$row['data'] = $this->dbv->validated($data);
		}
		else
		{
			$row['error'] = TRUE;
			$row['msg_text'] .= validation_errors();
		}

		//validate the product names
		if (!empty($data['product_name']))
		{
			foreach ($data['product_name'] as $k => $v)
			{
				$a = $this->dbv->validate(TBL_PRODUCTS_NAME, 'products_name', $v, FALSE);

				if (!empty($a['success']))
				{
					$row['data']['product_name'][$k] = $a['data'];
				}
				else
				{
					$row['error'] = TRUE;
					$row['msg_text'] .= $a['msg_text'];
				}
			}
		}

		//validate product_tags
		if (!empty($data['product_tags']))
		{
			foreach ($data['product_tags'] as $k => $v)
			{
				$row['data']['product_tags'][$k] = trim(xss_clean(strip_tags(strtolower($v))));
			}
		}

		//validate product_specs
		if (!empty($data['product_specs']))
		{
			foreach ($data['product_specs'] as $k => $v)
			{
				$a = $this->dbv->validate(TBL_PRODUCTS_TO_SPECIFICATIONS_NAME, 'products_specs', $v);

				if (!empty($a['success']))
				{
					$row['data']['product_specs'][$k] = $a['data'];
				}
				else
				{
					$row['error'] = TRUE;
					$row['msg_text'] .= $a['msg_text'];
				}
			}
		}

		//validate images
		if (!empty($data['images']))
		{
			foreach ($data['images'] as $k => $v)
			{
				$a = $this->dbv->validate(TBL_PRODUCTS_PHOTOS, 'products_images', $v);

				if (!empty($a['success']))
				{
					$row['data']['images'][$k] = $a['data'];
				}
				else
				{
					$row['error'] = TRUE;
					$row['msg_text'] .= $a['msg_text'];
				}
			}
		}

		//validate attributes
		if (!empty($data['attributes']))
		{
			foreach ($data['attributes'] as $k => $v)
			{
				$a = $this->dbv->validate(TBL_PRODUCTS_TO_ATTRIBUTES, 'products_attributes', $v);

				if (!empty($a['success']))
				{
					$row['data']['attributes'][$k] = $a['data'];
				}
				else
				{
					$row['error'] = TRUE;
					$row['msg_text'] .= $a['msg_text'];
				}

				//check if there are attribute options to validate
				if (!empty($v['option_values']))
				{
					foreach ($v['option_values'] as $m => $n)
					{
						if (!empty($n['status'])) //enabled
						{
							$a = $this->dbv->validate(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES, 'products_attributes_values', $n);

							if (empty($a['success']))
							{
								$row['error'] = TRUE;
								$row['msg_text'] .= $a['msg_text'];
							}
						}
					}
				}
			}
		}

		//validate discount_groups
		if (!empty($data['discount_groups']))
		{
			foreach ($data['discount_groups'] as $k => $v)
			{
				$a = $this->dbv->validate(TBL_PRODUCTS_TO_DISC_GROUPS, 'products_discount_groups', $v);

				if (!empty($a['success']))
				{
					$row['data']['discount_groups'][$k] = $a['data'];
				}
				else
				{
					$row['error'] = TRUE;
					$row['msg_text'] .= $a['msg_text'];
				}
			}
		}

		//validate affiliate_groups
		if (!empty($data['affiliate_groups']))
		{
			foreach ($data['affiliate_groups'] as $k => $v)
			{
				$a = $this->dbv->validate(TBL_PRODUCTS_TO_AFF_GROUPS, 'products_affiliate_groups', $v);

				if (!empty($a['success']))
				{
					$row['data']['affiliate_groups'][$k] = $a['data'];
				}
				else
				{
					$row['error'] = TRUE;
					$row['msg_text'] .= $a['msg_text'];
				}
			}
		}

		//validate product pricing
		if (!empty($data['pricing_options']))
		{
			$required = 'products_pricing_subscription';

			foreach ($data['pricing_options'] as $k => $v)
			{
				$a = $this->dbv->validate(TBL_PRODUCTS_TO_PRICING, $required, $v);

				if (!empty($a['success']))
				{
					$row['data']['pricing_options'][$k] = $a['data'];
				}
				else
				{
					$row['error'] = TRUE;
					$row['msg_text'] .= $a['msg_text'];
				}
			}
		}

		//if there's no errors, we're all good...
		if (empty($row['error']))
		{
			$row['success'] = TRUE;
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $tag_id
	 * @param string $product_id
	 * @return bool|false|string
	 */
	protected function get_product_tag($tag_id = '', $product_id = '')
	{
		$this->db->where('tag_id', $tag_id);

		if (!$q = $this->db->where($this->id, $product_id)->get(TBL_PRODUCTS_TO_TAGS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	protected function insert_photo($data = array())
	{
		$data['photo_file_name'] = format_photo_file_name($data['photo_file_name']);

		$row = $this->dbv->create(TBL_PRODUCTS_PHOTOS, $data);

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	protected function update_product_cross_sells($data = array())
	{
		if (!empty($data['product_cross_sell']))
		{
			foreach ($data['product_cross_sell'] as $v)
			{
				if (!$this->check_product_cross_sell($v, $data['product_id']))
				{
					$vars = array('product_id'            => $data['product_id'],
					              'product_cross_sell_id' => $v);

					$this->dbv->create(TBL_PRODUCTS_CROSS_SELLS, $vars);
				}
			}

			//delete the rest
			$a = $this->get_product_cross_sell($data['product_id']);

			if (!empty($a))
			{
				foreach ($a as $v)
				{
					if (!in_array($v['product_cross_sell_id'], $data['product_cross_sell']))
					{
						$this->dbv->delete(TBL_PRODUCTS_CROSS_SELLS, 'id', $v['id']);
					}
				}
			}
		}
		else
		{
			$this->dbv->delete(TBL_PRODUCTS_CROSS_SELLS, 'product_id', $data['product_id']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	protected function update_product_tags($data = array())
	{
		if (!empty($data['product_tags']))
		{
			$c = array();
			foreach ($data['product_tags'] as $v)
			{
				//check if the tag is in the db first...
				if (!$a = $this->dbv->get_record(TBL_PRODUCTS_TAGS, 'tag', url_title($v), TRUE, TRUE))
				{
					$vars = array('tag'   => url_title($v),
					              'count' => '0');

					$a = $this->dbv->create(TBL_PRODUCTS_TAGS, $vars);

					//set the tag id...
					$a['tag_id'] = $a['id'];
				}

				//now check if the tag is in the product tag db
				if (!$b = $this->get_product_tag($a['tag_id'], $data['product_id']))
				{
					$vars = array('product_id' => $data['product_id'],
					              'tag_id'     => $a['tag_id']);

					$this->dbv->create(TBL_PRODUCTS_TO_TAGS, $vars);
				}

				array_push($c, $a['tag_id']);
			}

			$d = $this->get_product_tags($data['product_id']);

			if (!empty($d))
			{
				foreach ($d as $e)
				{
					if (!in_array($e['tag_id'], $c))
					{
						$this->dbv->delete(TBL_PRODUCTS_TO_TAGS, 'prod_tag_id', $e['prod_tag_id']);
					}
				}
			}
		}
		else
		{
			$this->dbv->delete(TBL_PRODUCTS_TO_TAGS, 'product_id', $data['product_id']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	protected function update_product_attributes($data = array())
	{
		if (!empty($data['attributes']))
		{
			foreach ($data['attributes'] as $k => $v)
			{
				$v['product_id'] = $data['product_id'];
				$this->dbv->update(TBL_PRODUCTS_TO_ATTRIBUTES, 'prod_att_id', $v);

				//update select options if any...
				if (!empty($v['option_values']))
				{
					foreach ($v['option_values'] as $a => $b)
					{
						$this->dbv->update(TBL_PRODUCTS_TO_ATTRIBUTES_VALUES, 'prod_att_value_id', $b);
					}
				}
			}
		}
		else
		{
			$this->dbv->delete(TBL_PRODUCTS_TO_ATTRIBUTES, 'product_id', $data['product_id']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $table
	 * @param string $id
	 * @param string $vars
	 */
	protected function update_product_sub_data($data = array(), $table = '', $id = '', $vars = '')
	{
		if (!empty($data[$vars]))
		{
			$c = array();
			foreach ($data[$vars] as $k => $v)
			{
				if (!empty($v[$id]))
				{
					switch ($table)
					{
						case TBL_PRODUCTS_TO_PRICING:

							$v['default_price'] = '0';

							if (!empty($data['default_subscription_price']))
							{
								if ($data['default_subscription_price'] == $v['prod_price_id'])
								{
									$v['default_price'] = '1';
								}
							}

							if (empty($v['enable_initial_amount']))
							{
								$v['enable_initial_amount'] = 0;
							}

							if (empty($v['enable']))
							{
								$v['enable'] = 0;
							}

							if (empty($v['name']))
							{
								$type = $v['interval_amount'] > 1 ? plural($v['interval_type']) : $v['interval_type'];
								$v['name'] = $v['amount'] . ' ' . lang('every') . ' ' . $v['interval_amount'] . ' ' . $type;
							}

							break;
					}

					$a = $this->dbv->update($table, $id, $v);

					array_push($c, $v[$id]);
				}
				else //insert
				{
					switch ($table)
					{
						case TBL_PRODUCTS_TO_PRICING:

							$v['default_price'] = '0';
							$v['product_id'] = $data['product_id'];

							if (empty($v['enable_initial_amount']))
							{
								$v['enable_initial_amount'] = 0;
							}

							if (empty($v['name']))
							{
								$type = $v['interval_amount'] > 1 ? plural($v['interval_type']) : $v['interval_type'];
								$v['name'] = $v['amount'] . ' ' . lang('every') . ' ' . $v['interval_amount'] . ' ' . $type;
							}


							break;

						case TBL_PRODUCTS_TO_DISC_GROUPS:

							$v['product_id'] = $data['product_id'];

							break;

						case TBL_PRODUCTS_TO_VIDEOS:

							$a = $v;
							$v = array('product_id' => $data['product_id'],
							           'video_id'   => $a,
							);

							break;

						case TBL_PRODUCTS_TO_DOWNLOADS:

							$a = $v;
							$v = array('product_id'  => $data['product_id'],
							           'download_id' => $a,
							);

							break;
					}


					$a = $this->dbv->create($table, $v);
					array_push($c, $a['id']);
				}
			}

			//filter out the old ones...
			switch ($table)
			{
				case TBL_PRODUCTS_TO_PRICING:

					$a = $this->get_product_pricing($data['product_id']);

					break;

				case TBL_PRODUCTS_TO_DISC_GROUPS:

					$a = $this->get_product_discount_groups($data['product_id']);

					break;

				case TBL_PRODUCTS_TO_VIDEOS:

					$a = $this->get_product_videos($data['product_id']);

					break;

				case TBL_PRODUCTS_TO_DOWNLOADS:

					$a = $this->get_product_downloads($data['product_id']);

					break;
			}

			//now delete the rest
			if (!empty($a))
			{
				foreach ($a as $v)
				{
					if (!in_array($v[$id], $c))
					{
						$this->dbv->delete($table, $id, $v[$id]);
					}
				}
			}
		}
		else
		{
			$this->dbv->delete($table, 'product_id', $data['product_id']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	protected function update_product_photos($data = array())
	{
		if (!empty($data['images']))
		{
			$c = array();
			foreach ($data['images'] as $k => $v)
			{
				if (!empty($v['photo_id']))
				{
					$a = $this->update_photo($v);

					array_push($c, $v['photo_id']);
				}
				elseif (!empty($v['photo_file_name'])) //insert
				{
					$v['product_id'] = $data['product_id'];
					$a = $this->insert_photo($v);
					array_push($c, $a['id']);
				}
			}

			//now remove all images deleted...
			if ($a = $this->get_product_photos($data['product_id']))
			{
				$default = '';
				foreach ($a as $v)
				{
					if (!in_array($v['photo_id'], $c))
					{
						$this->dbv->delete(TBL_PRODUCTS_PHOTOS, 'photo_id', $v['photo_id']);
					}

					//now check if there is one that is default
					if (!empty($v['product_default']))
					{
						$default = $v['photo_id'];
					}
				}

				if (empty($default))
				{
					$this->set_default_photo($v['photo_id']);
				}
			}
		}
		else
		{
			//delete images for this product
			$this->dbv->delete(TBL_PRODUCTS_PHOTOS, 'product_id', $data['product_id']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	protected function update_photo($data = array())
	{
		$data['photo_file_name'] = format_photo_file_name($data['photo_file_name']);

		$this->dbv->update(TBL_PRODUCTS_PHOTOS, 'photo_id', $data);

		return !empty($row) ? sc($row) : FALSE;
	}
}

/* End of file Products_model.php */
/* Location: ./application/models/Products_model.php */