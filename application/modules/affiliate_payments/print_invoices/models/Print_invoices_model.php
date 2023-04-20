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
class Print_invoices_model extends Affiliate_payments_model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function install($id = '')
	{
		$config = array(
			'settings_key'	=>	'module_affiliate_payments_print_invoices_use_date_range',
			'settings_value'	=>	'0',
			'settings_module'	=>	'affiliate_payments',
			'settings_type'	=>	'dropdown',
			'settings_group'	=>	$id,
			'settings_sort_order'	=>	'1',
			'settings_function'	=>	'yes_no',
		);
		
		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}
		
		$config = array(
			'settings_key'	=>	'module_affiliate_payments_print_invoices_start_date',
			'settings_value'	=>	get_time('', TRUE),
			'settings_module'	=>	'affiliate_payments',
			'settings_type'	=>	'text',
			'settings_group'	=>	$id,
			'settings_sort_order'	=>	'2',
			'settings_function'	=>	'start_date_to_sql',
		);
		
		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}
		
		$config = array(
			'settings_key'	=>	'module_affiliate_payments_print_invoices_end_date',
			'settings_value'	=>	get_time('', TRUE),
			'settings_module'	=>	'affiliate_payments',
			'settings_type'	=>	'text',
			'settings_group'	=>	$id,
			'settings_sort_order'	=>	'3',
			'settings_function'	=>	'end_date_to_sql',
		);
		
		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}
		
		$config = array(
			'settings_key'	=>	'module_affiliate_payments_print_invoices_exclude_minimum',
			'settings_value'	=>	'0',
			'settings_module'	=>	'affiliate_payments',
			'settings_type'	=>	'dropdown',
			'settings_group'	=>	$id,
			'settings_sort_order'	=>	'4',
			'settings_function'	=>	'yes_no',
		);
		
		$this->db->insert('settings', $config);
		
		$config = array(
			'settings_key'	=>	'module_affiliate_payments_print_invoices_total_rows',
			'settings_value'	=>	'100',
			'settings_module'	=>	'affiliate_payments',
			'settings_type'	=>	'text',
			'settings_group'	=>	$id,
			'settings_sort_order'	=>	'5',
			'settings_function'	=>	'',
		);
		
		if (!$this->db->insert('settings', $config))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}
		
		$config = array(
			'settings_key'	=>	'module_affiliate_payments_print_invoices_payment_details',
			'settings_value'	=>	'affiliate payment',
			'settings_module'	=>	'affiliate_payments',
			'settings_type'	=>	'textarea',
			'settings_group'	=>	$id,
			'settings_sort_order'	=>	'6',
			'settings_function'	=>	'',
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
		//remove settings from database
		$this->mod->remove_config($id, 'affiliate_payments');

		return array(
			'success'  => TRUE,
			'msg_text' => lang('module_uninstalled_successfully'),
		);
	}

	public function generate_payments($data = array())
	{
		$invoices = $this->load->view('admin/tpl_admin_print_invoices_header', '', true);

		foreach($data['select'] as $v)
		{
			$invoices .= $this->load->view('admin/tpl_admin_print_invoices', $data['member'][$v], true);

		}

		$invoices .= '</body></html>';

		echo $invoices;

		exit;
	}

	public function run_query()
	{
		$sql = 'SELECT c.fname AS member_fname,
						c.lname AS member_lname,
						c.*,
						r.*,
						a.*,
						s.*,
						p.member_id AS member_id,
						COUNT(p.commission_amount) AS  total_commissions,
						SUM(p.commission_amount) AS total_amount,
						SUM(p.fee) AS fee
					FROM ' . $this->db->dbprefix(TBL_AFFILIATE_COMMISSIONS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_ADDRESSES) . ' r
						ON p.member_id = r.member_id
						AND r.payment_default = \'1\'
		 			LEFT JOIN ' . $this->db->dbprefix(TBL_REGIONS) . ' a ON r.state= a.region_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_COUNTRIES) . ' s ON r.country= s.country_id
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' c
						ON p.member_id = c.member_id
						WHERE approved = \'1\'
						AND comm_status = \'unpaid\' ';

		if (config_enabled('module_affiliate_payments_print_invoices_use_date_range'))
		{
			$sql .=	' AND (p.date > \'' . config_option('module_affiliate_payments_print_invoices_start_date') . '\'
						AND p.date < \'' . config_option('module_affiliate_payments_print_invoices_end_date') . '\')';
		}

		$sql .= ' GROUP by p.member_id
		            ORDER BY '  . config_option('module_view_function_sort_column') . ' '
			. config_option('module_view_function_sort_order') . '
					 LIMIT ' . config_option('module_affiliate_payments_print_invoices_total_rows');

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}



		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : $row;
	}
}

/* End of file Print_invoices_model.php */
/* Location: ./modules/affiliate_payments/print_invoices/models/Print_invoices_model.php */