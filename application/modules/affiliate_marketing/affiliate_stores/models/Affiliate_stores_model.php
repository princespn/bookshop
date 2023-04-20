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
class Affiliate_stores_model extends Affiliate_marketing_model
{
	protected $id = 'id';

	public function __construct()
	{
		parent::__construct();

		$this->load->dbforge();
	}

	public function install($id = '')
	{
		$delete = 'DROP TABLE IF EXISTS ' . $this->db->dbprefix(config_item('module_table')) . ';';

		if (!$q = $this->db->query($delete))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//db table shows current stores created by members
		$sql = "CREATE TABLE `" . $this->db->dbprefix(config_item('module_table')) . "` (
				  `id` int(11) NOT NULL,
				  `status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
				  `member_id` int(11) NOT NULL,
        		  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        		  `welcome_headline` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `welcome_text` text COLLATE utf8_unicode_ci,
				  `header_image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `avatar_image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `permalink` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$sql = "ALTER TABLE `" . $this->db->dbprefix(config_item('module_table')) . "`
				  ADD PRIMARY KEY (`id`),
				   ADD KEY `member_id` (`member_id`),
    			  ADD KEY `permalink` (`permalink`);";

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$sql = "ALTER TABLE `" . $this->db->dbprefix(config_item('module_table')) . "`
			   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$sql = "ALTER TABLE `" . $this->db->dbprefix(config_item('module_table')) . "`
			    ADD CONSTRAINT `" . $this->db->dbprefix(config_item('module_table')) . "_ibfk_1` 
			    FOREIGN KEY (`member_id`) 
			    REFERENCES `" . $this->db->dbprefix(TBL_MEMBERS) . "` (`member_id`) ON DELETE CASCADE;";

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$delete = 'DROP TABLE IF EXISTS ' . $this->db->dbprefix(config_item('module_products_table')) . ';';

		if (!$q = $this->db->query($delete))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$sql = "CREATE TABLE `" . $this->db->dbprefix(config_item('module_products_table')) . "` (
				  `id` int(10) NOT NULL,
				  `member_id` int(10) NOT NULL DEFAULT '0',
				  `product_id` int(10) NOT NULL DEFAULT '0'
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$sql = "ALTER TABLE `" . $this->db->dbprefix(config_item('module_products_table')) . "`
			    ADD CONSTRAINT `" . $this->db->dbprefix(config_item('module_products_table')) . "_ibfk_1` 
			    FOREIGN KEY (`member_id`) 
			    REFERENCES `" . $this->db->dbprefix(TBL_MEMBERS) . "` (`member_id`) ON DELETE CASCADE;";

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$sql = "ALTER TABLE `" . $this->db->dbprefix(config_item('module_products_table')) . "`
			    ADD CONSTRAINT `" . $this->db->dbprefix(config_item('module_products_table')) . "_ibfk_2` 
			    FOREIGN KEY (`product_id`) 
			    REFERENCES `" . $this->db->dbprefix(TBL_PRODUCTS) . "` (`product_id`) ON DELETE CASCADE;";

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$sql = "ALTER TABLE `" . $this->db->dbprefix(config_item('module_products_table')) . "`
			    ADD CONSTRAINT `" . $this->db->dbprefix(config_item('module_products_table')) . "_ibfk_3` 
			    FOREIGN KEY (`member_id`) 
			    REFERENCES `" . $this->db->dbprefix(config_item('module_table')) . "` (`member_id`) ON DELETE CASCADE;";

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//generate settings for this module
		$config = array(
			'settings_key'        => 'module_affiliate_marketing_affiliate_stores_maximum_product_recommendations',
			'settings_value'      => '24',
			'settings_module'     => 'affiliate_marketing',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => 'integer',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_marketing_affiliate_stores_redirect_affiliate_link',
			'settings_value'      => '24',
			'settings_module'     => 'affiliate_marketing',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '2',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_marketing_affiliate_stores_default_welcome_headline',
			'settings_value'      => 'Welcome To My Online Store',
			'settings_module'     => 'affiliate_marketing',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_marketing_affiliate_stores_default_welcome_text',
			'settings_value'      => 'Browse our featured products below',
			'settings_module'     => 'affiliate_marketing',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '4',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_marketing_affiliate_stores_default_background',
			'settings_value'      => '//centos.jrox.com/jem3/images/widgets/plant.jpg',
			'settings_module'     => 'affiliate_marketing',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
			'settings_function'   => 'image_manager',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_marketing_affiliate_stores_allow_affiliate_select_background',
			'settings_value'      => '1',
			'settings_module'     => 'affiliate_marketing',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '6',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_installed_successfully'),
		);
	}

	public function uninstall($id = '')
	{
		$tables = array('module', 'module_products');

		foreach ($tables as $t)
		{
			if (!$this->dbforge->drop_table(config_item($t . '_table'), TRUE))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		//remove settings from database
		$this->mod->remove_config($id, 'affiliate_marketing');

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function generate_module($data = array(), $member_id = '')
	{
		//run the SQL query for the data
		$data['rows'] = $this->run_query($member_id);
		$data['backgrounds'] = get_images('backgrounds');

		$row = array('title'         => $this->config->item('module_title'), //title of the report
		             'template'      => $this->config->item('module_template'), //template to use
		             'rows'          => $data['rows'], //data array
		             'rendered_html' => render_template($data, 'members'), //rendered html for chart
		);

		return empty($row) ? FALSE : $row;
	}

	protected function run_query($member_id = '')
	{
		$sql = 'SELECT * 
					FROM ' . $this->db->dbprefix(config_item('module_table')) . '
					WHERE member_id = \'' . (int)$member_id . '\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	public function member_activate($data = array(), $type = '')
	{
		if (!$this->run_query(sess('member_id')))
		{
			$this->create_record(array('member_id' => sess('member_id'),
			                           'status' => '1',
			                           'name' => sess('username'),
			                           'permalink' => sess('username')));
		}


		$this->session->set_userdata('affiliate_store', '1');
		$this->session->set_userdata('affiliate_store_products', array());

		redirect_page('members/affiliate_marketing/module/' . uri(4));
	}

	public function member_update($data = array(), $type = '')
	{
		switch ($type)
		{
			case 'validate':

				return $this->dbv->validate(config_item('module_table'), config_item('module_table'), $data, FALSE);

				break;

			default: //run update

				$row = $this->dbv->update(config_item('module_table'), 'member_id', $data);

				$row['redirect_url'] = site_url('members/affiliate_marketing');

				return $row;

				break;
		}
	}

	public function add_affiliate_store($type = '', $data = array())
	{
		switch ($type)
		{
			case 'add':

				$this->db->where('product_id', $data['product_id']);
				$this->db->where('member_id', $data['member_id']);

				if (!$q = $this->db->get('module_affiliate_marketing_affiliate_stores_products'))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() < 1)
				{
					$row = $this->dbv->create('module_affiliate_marketing_affiliate_stores_products', $data);

					$_SESSION['affiliate_store_products'][] = $data['product_id'];
				}

				break;

			case 'remove':

				$this->db->where('product_id', $data['product_id']);
				$this->db->where('member_id', $data['member_id']);

				if (!$q = $this->db->delete('module_affiliate_marketing_affiliate_stores_products'))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				break;
		}

		$this->update_store($data);
	}

	public function update_store($data = array())
	{
		$this->db->where('member_id', $data['member_id']);

		if (!$q = $this->db->get('module_affiliate_marketing_affiliate_stores_products'))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $v)
			{
				$_SESSION['affiliate_store_products'][] = $v['product_id'];
			}
		}
	}

	public function check_store_id($id = '')
	{
		$cache = __METHOD__ . $id;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$this->db->where('member_id', $id);

			if (!$q = $this->db->get(config_item('module_table')))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$a['products'] = $q->result_array();

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $a, 'db_query');
			}
		}

		$row = $a;

		return empty($row) ? FALSE : sc($row);


	}

	public function get_affiliate_store($id = '', $lang_id = 1)
	{
		$a = array(
			'settings' => $this->run_query($id)
		);

		if (!$a['settings']) return FALSE;

		//set the default sort order
		$sort = $this->config->item(TBL_PRODUCTS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$select = 'SELECT *, p.product_id AS product_id,
                        (SELECT AVG(ratings)
                            FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' r
						    WHERE p.product_id = r.product_id
                            AND status = \'1\') AS avg_ratings';

		if (config_enabled('sts_tax_enable_tax_calculations'))
		{
			$select .= ', GROUP_CONCAT(DISTINCT CONCAT(w.zone_id, \':\', w.tax_type,\':\', w.amount_type, \':\', w.tax_amount, \':\', u.calculation)
						    ORDER BY u.priority SEPARATOR \'/\' )
                            AS taxes ';
		}

		$sql = ' FROM ' . $this->db->dbprefix($this->config->item('module_products_table')) . ' r
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
                        ON p.product_id = r.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
                        ON p.product_id = d.product_id AND d.language_id = \'' . $lang_id . '\'
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_CATEGORIES) . ' c
                        ON p.product_id = c.product_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_CLASSES) . ' t
                        ON p.tax_class_id = t.tax_class_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATE_RULES) . ' u
                        ON t.tax_class_id = u.tax_class_id
                   LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_PRICING) . '  k
                            ON (p.product_id = k.product_id
                            AND k.enable = \'1\'
                            AND k.default_price = \'1\') 
                    LEFT JOIN ' . $this->db->dbprefix(TBL_TAX_RATES) . ' w
                        ON w.tax_rate_id = u.tax_rate_id
                    LEFT JOIN ' . $this->db->dbprefix('products_photos') . '  h
                        ON p.product_id = h.product_id
                        AND h.product_default = \'1\'
                    WHERE r.member_id = \'' . (int)$id . '\'
                        AND p.product_status = \'1\'
                        AND p.product_featured = \'1\'
                        AND p.hidden_product = \'0\'
                        AND p.date_expires >= ' . local_time('sql');

		$order = ' GROUP BY r.product_id
                       ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                       LIMIT ' . $this->config->item('module_affiliate_marketing_affiliate_stores_maximum_product_recommendations');

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
				$a['products'] = $q->result_array();

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $a, 'db_query');
			}
		}

		$row = $a;

		return empty($row) ? FALSE : sc($row);
	}

	public function pagination($total = array())
	{
		if (!empty($total))
		{
			$a = array(
				'uri'        => site_url() . uri(1),
				'total_rows' => $total,
				'per_page'   => $this->config->item('session_per_page'),
				'segment'    => 2,
			);

			$row['paginate'] = $this->paginate->generate($a, $this->config->item('module_title'));
			$row['next_scroll'] = check_infinite_scroll($a);
		}

		return empty($row) ? FALSE : $row;
	}

	public function get_rows($options = array())
	{
		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $this->config->item('module_view_function_sort_order');
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $this->config->item('module_view_function_sort_column');

		$sql = 'SELECT p.*, c.username 
					FROM ' . $this->db->dbprefix(config_item('module_table')) . ' p 
		            LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' c 
				        ON p.member_id = c.member_id 
					ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' 
				        LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$count = 'SELECT COUNT(*) AS total 
					 FROM ' . $this->db->dbprefix(config_item('module_table')) . ' p';//for pagination totals

			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->dbv->get_query_total($count),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : $row;
	}

	public function get_record_details($id = '', $data = array(), $public = FALSE)
	{
		$sql = 'SELECT p.*, c.username,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(config_item('module_table')) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(config_item('module_table')) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next
				    FROM ' . $this->db->dbprefix(config_item('module_table')) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' c 
				    ON p.member_id = c.member_id 
                    WHERE p.' . $this->id . '= ' . (int)$id . '';

		if ($public == TRUE)
		{
			$sql .= ' AND p.status = \'1\'';
		}
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

	public function delete_record($id = '')
	{
		return $this->dbv->delete(config_item('module_table'), 'id', $id);
	}

	public function create_record($data = array())
	{
		$data = $this->dbv->clean($data, config_item('module_table'));

		if (!$this->db->insert(config_item('module_table'), $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'msg_text' => lang('record_created_successfully'),
			'data'     => $data,
			'success'  => TRUE,
		);

		return $row;
	}

	public function update_record($data = array())
	{
		return $this->dbv->update(config_item('module_table'), $this->id, $data);
	}

	public function validate_record($data = array())
	{
		$required = config_item('module_admin_required_validation_fields');

		$this->form_validation->set_data($data);

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(config_item('module_table'));

		foreach ($fields as $f)
		{
			$rule = 'trim|xss_clean';

			//if this field is a required field, let's set that
			if (in_array($f, $required))
			{
				$rule .= '|required';
			}

			switch ($f->name)
			{
				case 'member_id':

					if (CONTROLLER_FUNCTION == 'create')
					{
						$this->form_validation->set_rules(
							'member_id', 'lang:affiliate',
							array(
								'trim', 'required', 'integer',
								array('member_id', array($this->affiliate_stores, 'check_member_id')),
							)
						);

						$this->form_validation->set_message('member_id', '%s ' . lang('already_has_store'));
					}

					break;

				default:
					//set the default rule
					$rule = 'trim|xss_clean';

					//if this field is a required field, let's set that
					if (is_array($required) && in_array($f->name, $required))
					{
						$rule .= '|required';
					}

					$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

					break;
			}
		}

		$this->form_validation->set_message('slug', '%s ' . lang('already_in_use'));

		if (!$this->form_validation->run())
		{
			//sorry! got some errors here....
			$row = array('error'        => TRUE,
			             'error_fields' => generate_error_fields(),
			             'msg_text'     => validation_errors(),
			);
		}
		else
		{
			//cool! no errors...
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data, FALSE),
			);
		}

		return $row;
	}

	public function check_member_id($str = '')
	{
		if ($this->dbv->check_unique($str, config_item('module_table'), 'member_id'))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function update_affiliate_module($data = array())
	{
		//update module data
		$row = $this->mod->update($data);

		return $row;
	}

	public function validate_affiliate_module($data = array())
	{
		$row = $this->validate_module($data);

		return $row;
	}
}

/* End of file Affiliate_stores_model.php */
/* Location: ./modules/affiliate_marketing/affiliate_stores/models/Affiliate_stores_model.php */