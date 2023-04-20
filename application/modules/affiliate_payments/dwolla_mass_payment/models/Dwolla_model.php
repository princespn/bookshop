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
class Dwolla_model extends Affiliate_payments_model
{
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
		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_use_date_range',
			'settings_value'      => '0',
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '1',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_start_date',
			'settings_value'      => get_time('', TRUE),
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '2',
			'settings_function'   => 'start_date_to_sql',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_end_date',
			'settings_value'      => get_time('', TRUE),
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '3',
			'settings_function'   => 'end_date_to_sql',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_exclude_minimum',
			'settings_value'      => '0',
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '4',
			'settings_function'   => 'yes_no',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_total_rows',
			'settings_value'      => '100',
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '5',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_api_key',
			'settings_value'      => '',
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '6',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_api_secret',
			'settings_value'      => '',
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '7',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_api_token',
			'settings_value'      => '',
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '8',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_api_pin',
			'settings_value'      => '',
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'text',
			'settings_group'      => $id,
			'settings_sort_order' => '9',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_payment_details',
			'settings_value'      => 'affiliate payment',
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'textarea',
			'settings_group'      => $id,
			'settings_sort_order' => '10',
			'settings_function'   => '',
		);

		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$config = array(
			'settings_key'        => 'module_affiliate_payments_dwolla_mass_payment_environment',
			'settings_value'      => 'production',
			'settings_module'     => 'affiliate_payments',
			'settings_type'       => 'dropdown',
			'settings_group'      => $id,
			'settings_sort_order' => '11',
			'settings_function'   => 'production_sandbox',
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
		//remove settings from database
		$this->mod->remove_config($id, 'affiliate_payments');

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function direct_pay($data = array())
	{
		$key = ($this->config->item('module_affiliate_payments_dwolla_mass_payment_api_key'));
		$secret = ($this->config->item('module_affiliate_payments_dwolla_mass_payment_api_secret'));
		$token = ($this->config->item('module_affiliate_payments_dwolla_mass_payment_api_token'));
		$pin = ($this->config->item('module_affiliate_payments_dwolla_mass_payment_api_pin'));
		$debugMode = FALSE;
		$sandboxMode = config_item('module_affiliate_payments_dwolla_mass_payment_environment') == 'production' ? FALSE : TRUE;

		$note = $this->config->item('module_affiliate_payments_dwolla_mass_payment_payment_details');


		$Dwolla = new DwollaRestClient($key, $secret, false, config_item('module_permissions'), 'live', $debugMode, $sandboxMode);

		$Dwolla->setToken($token);

		if ($data['trans_id']  = $Dwolla->send($pin, $data['id'], $data['amount'], 'email'))
		{
			$data['success'] = TRUE;
			$data['msg_text'] =  lang('affiliate_paid_successfully');

			return $data;
		}
		else
		{
			return array('error' => TRUE,
			             'msg_text' => $Dwolla->getError());
		}
	}

	public function generate_payments($data = array())
	{
		$slash = "\t";

		$file = '';

		foreach ($data['select'] as $v)
		{
			if (!empty($data['member'][$v]['dwolla_id']))
			{
				$file .= $data['member'][$v]['dwolla_id'] . $slash . $data['member'][$v]['total_amount'] . $slash . $data['member'][$v]['member_name'] . $slash . config_option('module_affiliate_payments_dwolla_mass_payment_payment_details') . "\n";
			}
		}

		$name = 'dwolla_mass_payment_' . date('m-d-Y') . '.' . config_option('module_download_file_extension');

		if (!empty($file))
		{
			//download it!
			force_download($name, $file);
		}
		else
		{
			return sc(array('type'     => 'error',
			                'msg_text' => lang('no_file_generated')));
		}
	}

	public function run_query()
	{
		$sql = 'SELECT c.fname AS member_fname,
						c.lname AS member_lname,
						c.*,
						a.*,
						p.member_id AS member_id,
						COUNT(p.commission_amount) AS  total_commissions,
						SUM(p.commission_amount) AS total_amount,
						SUM(p.fee) AS fee
					FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_ADDRESSES) . ' a
						ON p.member_id = a.member_id
						AND a.payment_default = \'1\'
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' c
						ON p.member_id = c.member_id
						WHERE approved = \'1\'
						AND comm_status = \'unpaid\' ';

		if (config_enabled('module_affiliate_payments_dwolla_mass_payment_use_date_range'))
		{
			$sql .= ' AND (p.date > \'' . config_option('module_affiliate_payments_dwolla_mass_payment_start_date') . '\'
						AND p.date < \'' . config_option('module_affiliate_payments_dwolla_mass_payment_end_date') . '\')';
		}

		$sql .= ' GROUP by p.member_id
		            ORDER BY ' . config_option('module_view_function_sort_column') . ' '
			. config_option('module_view_function_sort_order') . '
					 LIMIT ' . config_option('module_affiliate_payments_dwolla_mass_payment_total_rows');

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : $row;
	}
}

/* End of file Dwolla_model.php */
/* Location: ./modules/affiliate_payments/dwolla_mass_payment/models/Dwolla_model.php */