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
class Twitter_share_model extends Affiliate_marketing_model
{
	protected $id = 'id';

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Install module
	 *
	 * Install the module and add any config items into the
	 * settings table
	 *
	 * @return array
	 */
	public function install($id = '')
	{
		$delete = 'DROP TABLE IF EXISTS ' . $this->db->dbprefix(config_item('module_table')) . ';';

		if (!$q = $this->db->query($delete))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//install db table
		$sql = "CREATE TABLE IF NOT EXISTS " . $this->db->dbprefix(config_item('module_table')) . " (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
                  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `link_text` text COLLATE utf8_unicode_ci NOT NULL,
                  `enable_redirect` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
                  `redirect_custom_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `affiliate_group` int(11) NOT NULL DEFAULT '0',
                  `sort_order` int(10) NOT NULL DEFAULT '0',
                  `notes` text COLLATE utf8_unicode_ci NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `affiliate_group` (`affiliate_group`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_marketing_twitter_share_show_product_link',
			'settings_value'      => '1',
			'settings_module'     => 'affiliate_marketing',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
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

	/**
	 * Uninstall module
	 *
	 * Uninstall the module and remove any config items from
	 * the settings table
	 *
	 * @param string $id
	 * @return array
	 */
	public function uninstall($id = '')
	{
		$delete = 'DROP TABLE IF EXISTS ' . $this->db->dbprefix(config_item('module_table')) . ';';

		if (!$q = $this->db->query($delete))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
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
		$data['rows'] = $this->run_query($data, $member_id);

		$row = array('title'         => $this->config->item('module_title'), //title of the report
		             'template'      => $this->config->item('module_template'), //template to use
		             'rows'          => $data['rows'], //data array
		             'rendered_html' => render_template($data, 'members'), //rendered html for chart
		);

		return empty($row) ? FALSE : $row;
	}

	protected function run_query($data = array())
	{
		$select = 'SELECT * '; //for the page rows only
		$count = 'SELECT COUNT(id) AS total'; //for pagination totals

		//sql query
		$sql = ' FROM ' . $this->db->dbprefix(config_item('module_table')) . '
                    WHERE status = \'1\'';

		//set the order and limit clause
		$order = '  ORDER BY sort_order ASC
                    LIMIT ' . uri(5, 0) . ', ' . $data['session_per_page'];

		if (!$q = $this->db->query($select . $sql . $order))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $this->aff->check_restrictions($q->result_array()),
				'debug_db_query' => $this->db->last_query(),
				'total'          => $this->dbv->get_query_total($count . $sql),
			);

			$row['page'] = $this->pagination($row['total']);
		}

		return empty($row) ? FALSE : $row;
	}

	public function pagination($total = array())
	{
		if (!empty($total))
		{
			$a = array(
				'uri'        => site_url() . uri(1) . '/'. uri(2) . '/' . uri(3) . '/' . uri(4),
				'total_rows' => $total,
				'per_page'   => $this->config->item('session_per_page'),
				'segment'    => 5,
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

		$sql = 'SELECT * FROM ' . $this->db->dbprefix(config_item('module_table')) . ' 
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
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_query_total($count),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : $row;
	}

    public function get_record_details($id = '')
    {
		$row = $this->dbv->get_record(config_item('module_table'), $this->id, $id);

	    return empty($row) ? FALSE : $row;
    }

	public function delete_record($id = '')
	{
		return $this->dbv->delete(config_item('module_table'), 'id', $id);
	}

	public function create_record($data = array())
	{
		return $this->dbv->create(config_item('module_table'), $data);
	}

	public function update_record($data = array())
	{
		return $this->dbv->update(config_item('module_table'), $this->id, $data);
	}

	public function validate_record($data = array())
	{
		$required = array('status', 'name', 'link_text');

		$row = $this->dbv->validate(config_item('module_table'), $required, $data);

		return $row;
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

/* End of file Twitter_share_model.php */
/* Location: ./modules/affiliate_marketing/banners/models/Twitter_share_model.php */